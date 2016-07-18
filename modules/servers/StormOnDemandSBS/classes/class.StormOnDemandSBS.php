<?php
/* * ********************************************************************
 * CloudLinux Licenses Product developed. (2014-06-04)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->       http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 * ******************************************************************** */

/**
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 */

class StormOnDemandSBS extends MG_Clientarea {
       /**
        * API
        * @var StormOnDemand_Storage
        */
       private $storage;
       /**
        *
        * @var StormOnDemandStormServer
        */
       private $server;
       /**
        *
        * @var string
        */
       private $uniq_id;
       
	public function init($params) {
             $username           =   StormOnDemandSBS_getOption('Username', $params);
             $password           =   StormOnDemandSBS_getOption('Password', $params);
             $this->storage = new StormOnDemand_Storage($username, $password, 'bleed');  
             $this->server = new StormOnDemandStormServer($username, $password, 'bleed');
             $this->uniq_id = $params['customfields']['uniq_id'];
       }

	public function indexAction($params) {
             
             try{
                   if (!$params['customfields']['uniq_id'])
                         throw new Exception('Custom Field /Uniq ID/ is empty');
                   $this->storage->setDebug(true);
                   $datails = $this->storage->details($this->uniq_id);
                   $hostings = mysql_get_array("SELECT h.domain, h.id, d.uniq_id, p.name FROM `tblhosting` h
                                                left join tblproducts p ON (p.id=h.packageid)
                                                left join `mg_storm_on_demand` d ON (d.hosting_id =h.id)
                                                where h.userid=? and  h.domainstatus=? and (p.servertype=? or p.servertype=?)", array($params['clientsdetails']['userid'],'Active', 'StormOnDemand','StormOnDemandPrivateParent'));
                   $attached = array();
                   $zoneAvailability = $datails['zoneAvailability'];

                   foreach($datails['attachedTo'] as $k => $v){
                         $attached[] =$v['resource'];
                         $info = $this->server->details($v['resource']);
                         $datails['attachedTo'][$k]['domain'] = $info['domain'];
                   }
                   foreach($hostings as $k => $h){
                     
                        if($h['uniq_id'] == null){
                          //Uniq_id Parent
                          /*
                          $q = mysql_query("SELECT mg_LiquidWebPrivateParentProduct.value
                                          FROM tblhosting
                                          LEFT JOIN mg_LiquidWebPrivateParentProduct ON tblhosting.packageid = mg_LiquidWebPrivateParentProduct.product_id
                                          WHERE mg_LiquidWebPrivateParentProduct.setting =  'Parent'"
                                        );
                          */
                          //Uniq_id child
                          $q = mysql_query("SELECT tblcustomfieldsvalues.value
                                          FROM tblhosting
                                          LEFT JOIN tblcustomfields ON tblhosting.packageid = tblcustomfields.relid
                                          LEFT JOIN tblcustomfieldsvalues ON tblcustomfields.id = tblcustomfieldsvalues.fieldid
                                          WHERE (tblcustomfields.fieldname = 'uniq_id' OR tblcustomfields.fieldname = 'uniq_id|Uniq ID')
                                          AND tblhosting.id =".(int)$h['id']."
                                          AND tblcustomfieldsvalues.relid =".(int)$h['id']
                                        );
                                          
                          $row = mysql_fetch_assoc($q);

                          if($row != null && $row!= false){
                            $h['uniq_id'] = $row['value'];
                            $hostings[$k]['uniq_id'] = $row['value'];
                          }
                        }
                     
                     
                         if(in_array($h['uniq_id'], $attached)){
                               unset($hostings[$k]);
                               continue;
                         }
                         $info = $this->server->details($h['uniq_id']);
                         if($info && !in_array($info['zone']['id'], $zoneAvailability)){
                              unset($hostings[$k]);
                              continue; 
                         }
                   }
                   
                   if(isset($_POST['attach']['to'])){
                         $res = $this->storage->attach($_POST['attach']['to'], $this->uniq_id);
                         if($e = $this->storage->getError()){
                               throw new Exception($e);
                         }
                         sleep(3);
                         $this->addInfo($this->_lang['index']['attach_info']);
                         $this->redToMainPage();
                         die();
                   }
                   if($_GET['detach']){
                         $res = $this->storage->detach($_GET['detach'] , $this->uniq_id);
                         
                         if($e = $this->storage->getError()){
                               throw new Exception($e);
                         }
                         sleep(3);
                         $this->addInfo($this->_lang['index']['detach_info']);
                         $this->redToMainPage();
                         die();
                   }
                   
                   
             } catch (Exception $ex) {
                   $this->addError($ex->getMessage());
             }
             
            if($datails == null){
               $datails['notset'] = true;
            }
            
             return array(
                           "hostings" => $hostings,
                           "datails" => $datails,
                           "errors" => $this->getErrors(),
                           "infos" => $this->getInfos(),
                  );

	}
       
       public function domainsManagementAction($params) {
             
             try{
             } catch (Exception $ex) {
                   $this->addError($ex->getMessage());
             }
             return array(
                           "errors" => $this->getErrors(),
                           "infos" => $this->getInfos(),
                  );

	}
       
      
        public function ajaxAction($params){
		try {

			switch ($_POST['subaction']){					
				case 'details':
					$res = array(
						'result' => '1',
						'data' => 'test',
					);
					break;
                             break;
				default: throw new Exception('Action not supported');
			}
			
			if (!isset($res)){
				$res = array(
					'result' => '1',
					'msg' => $msg,
				);
			}
		} catch (Exception $e){
			$res = array(
				'result'=> '0',
				'msg'	=> $e->getMessage()
			);
		}
		echo json_encode($res);
		die();
	}
       

}
