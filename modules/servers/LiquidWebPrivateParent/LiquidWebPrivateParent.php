<?php

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);


define('LiquidWebPPCacheLive',1);
define('LiquidWebPPCacheName','liquidforwhmcs_quota_cache');
define('LiquidWebPPLiquidWebServerType', 'LiquidPrivateParent');
define('LiquidWebPPFirewallSlashEscapeChar','_');
define('LiquidWebPPSBSServerType', 'LiquidWebSBS');

if(file_exists(dirname(__FILE__).DS.'moduleVersion.php')){
    require_once dirname(__FILE__).DS.'moduleVersion.php';
     define('LIQUID_WEB_PRIVATE_PARENT_VERSION', $moduleVersion);
}else{
     define('LIQUID_WEB_PRIVATE_PARENT_VERSION', 'Development Version');
}

//Database functions
require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'database.php';
//Hosting Class
require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Hosting.php';
//Product Class
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.LiquidWebPrivateParentProduct.php';

function LiquidWebPP_checkConnection()
{
    //load server helper class
    require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'modulesgarden' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'class.StormOnDemand_Helper.php';

    if(strpos($_SERVER['SCRIPT_FILENAME'], 'configproducts.php') !== false)
    {
        $q = mysql_query("SELECT * FROM tblproducts WHERE id = " . (int)$_REQUEST['id'] . " LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $username = $row['configoption1'];
        $password = $row['configoption2'];
        //$password = StormOnDemand_Helper::encrypt_decrypt($row['configoption2']);

        if(! empty($username) && ! empty($password))
        {
            require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormConfig.php';

            $config = new StormOnDemandStormConfig($username, $password);

            $res = $config->ping();

            if(isset($res['ping']) && $res['ping'] = 'success')
            {
                return true;
            }
            /* if($_GET['action'] != 'save'){
                echo '<p style="text-align: center;" class="errorbox">
                    <span style="font-weight: bold">Authorization error. Please check username and password.</span>
                 </p>';
            } */
            return false;
        }
        /* if($_GET['action'] != 'save'){
            echo '<p style="text-align: center;" class="infobox">
                    <span style="font-weight: bold">Please enter your API User username in "Username" field and your API User password in "Password".</span>
                 </p>';
        } */
    }elseif(strpos($_SERVER['SCRIPT_FILENAME'], 'orders.php') !== false){

        $q = mysql_query("SELECT tblproducts.* FROM tblproducts LEFT JOIN tblhosting ON tblproducts.id = tblhosting.packageid LEFT JOIN tblorders ON tblhosting.orderid = tblorders.id WHERE tblorders.id = " . (int)$_REQUEST['id'] . " LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $username = $row['configoption1'];
        $password = $row['configoption2'];
        //$password = StormOnDemand_Helper::encrypt_decrypt($row['configoption2']);


        if(! empty($username) && ! empty($password))
          {
              require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormConfig.php';

              $config = new StormOnDemandStormConfig($username, $password);

              $res = $config->ping();

              if(isset($res['ping']) && $res['ping'] = 'success')
              {
                  return true;
              }
              return false;
          }
    }elseif(strpos($_SERVER['SCRIPT_FILENAME'], 'clientsservices.php') !== false){

        $q = mysql_query("SELECT tblproducts.* FROM tblproducts LEFT JOIN tblhosting ON tblproducts.id = tblhosting.packageid WHERE tblhosting.id = " . (int)$_REQUEST['id'] . " LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $username = $row['configoption1'];
        $password = $row['configoption2'];
        //$password = StormOnDemand_Helper::encrypt_decrypt($row['configoption2']);


        if(! empty($username) && ! empty($password))
          {
              require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormConfig.php';

              $config = new StormOnDemandStormConfig($username, $password);

              $res = $config->ping();

              if(isset($res['ping']) && $res['ping'] = 'success')
              {
                  return true;
              }
              return false;
          }
    }elseif(strpos($_SERVER['SCRIPT_FILENAME'], 'clientarea.php') !== false){
          return true;
    }
}

function LiquidWebPrivateParent_ConfigOptions()
{
	require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'class.ModuleInformationClient.php';
    $idProduct    = (int) intval($_REQUEST['id']);
    $productQuery = ModuleInformationClient::mysql_safequery('SELECT * FROM tblproducts WHERE id = ? LIMIT 1', array($idProduct));
    $productRow   = mysql_fetch_assoc($productQuery);

    $username     = $productRow['configoption1'];
    $password     = $productRow['configoption2'];


    $config	=	array(
     'Username' => array
     (
    		'Type' => 'text',
    		'Size' => '25',
             'Description' => '<div id="custom-dialog" style="display:none;" title=""></div>'
     ),
     'Password' => array
     (
    		'Type' => 'password',
    		'Size' => '25'
     ),
     'Default Configurable Options' => array
     (
    		'Type' => '',
    		'Description' => '<a id="generate-storm-confoption" href="modaction=generate_configurable_options" class="gen-config-options">Generate Default Configurable Options</a>'
     ),
     'Custom Fields' => array
     (
    		'Type' => '',
    		'Description' => '<a id="generate-storm-customfields" href="modaction=generate_custom_fields" class="gen-config-options">Generate Custom Fields</a>'
     ),
     'Parent Server' => array
     (
    		'Type' => 'dropdown',
    		'Options' => array()
     ),
     'Available Parents' => array
     (
    		'Type' => 'dropdown',
            //'Multiple' => TRUE,
    		'Options' => array()
     ),
     'Select Parent Automatically' => array
     (
    		'Type' => 'yesno',
    		'Description' => 'Tick to automatically choose private cloud from "Available Parent"'
     ),
     'Template' => array
     (
    		'Type' => 'text',
    		'Size' => '25',
    		'Description' => '<a id="load-storm-template" href="stormajax=load-template" class="load-configuration">Load Template</a>'
     ),
     'Image' => array
     (
    		'Type' => 'text',
    		'Size' => '25',
    		'Description' => '<a id="load-storm-image" href="stormajax=load-image" class="load-configuration">Load Image</a>'
     ),
     'Memory (MB)' => array
     (
    		'Type' => 'text',
    		'Size' => '25'
     ),
     'Disk Space (GB)' => array
     (
    		'Type' => 'text',
    		'Size' => '25'
     ),
     'Virtual CPU' => array
     (
    		'Type' => 'text',
    		'Size' => '25'
     ),
     'Backup Plan' => array
     (
    		'Type' => 'dropdown',
            'Options' => array
    		(
    			'0' => 'Disabled',
    			'quota' => 'Quota',
    			'daily' => 'Daily'
    		)
     ),
     'Backup Quota' => array
     (
    		'Type' => 'text',
    		'Size' => '25'
     ),
     'Daily Backup Quota'=> array
     (
    		'Type' => 'text',
    		'Size' => '25'
     ),
     'Number of IPs' => array
     (
    		'Type' => 'text',
    		'Size' => '25'
     ),
     'Maximum IP Addresses' => array
     (
    		'Type' => 'text',
    		'Size' => '25'
     ),
     'Bandwidth Quota' => array
     (
    		'Type' => 'dropdown',
    		'Options' => array
    		(
    			'5000' => '5000',
    			'6000' => '6000',
    			'8000' => '8000',
    			'10000' => '10000',
    			'15000' => '15000',
    			'20000' => '20000'
    		)
     ),
     'Monitoring' => array
     (
    		'Type' => 'yesno',
    		'Description' => 'Tick to give possibility to monitoring server from Client Area'
     ),
     'Firewall' => array
     (
    		'Type' => 'yesno',
    		'Description' => 'Tick to give possibility to manage firewall from Client Area'
     ),
     'IPs Management' => array
     (
    		'Type' => 'yesno',
    		'Description' => 'Tick to give possibility to manage IPs addresses from Client Area'
     ),
     "Error" => array
     (
    		'Type' => '',
    		'Description' => '<p style="text-align: center;" class="errorbox"><span style="font-weight: bold">Authorization error. Please check username and password.</span></p>'
      )
    );


    if(basename($_SERVER["SCRIPT_NAME"]) == 'configproducts.php' && $_GET['action'] != 'save')
    {
        $config['Username']['Description'] .= '
        	<script type="text/javascript">
                jQuery(function(){
                  jQuery( document ).ready(function() {
                    jQuery(".load-configuration").click(function(event){
                        event.preventDefault();
                        if($("#custom-dialog").is(":data(dialog)"))
                        {
                            $("#custom-dialog").dialog("destroy");
                        }
                        $("#ui-id-3").html($(this).html());
                        $("#custom-dialog").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWebSBS/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
                        $("#custom-dialog").dialog({
                        	title: $(this).html(),
                        	minWidth: 650,
                        	close: function(event, ui) {
                        		$(this).dialog(\'close\');
    						}
    					});

                        val = $(this).parent().find("input").val();
                        jQuery.post("../modules/servers/LiquidWebPrivateParent/stormajax.php?action=edit&id='.$_REQUEST['id'].'&conf_id="+val,jQuery(this).attr("href"), function(data){
                        	$("#custom-dialog").html(data);
    					});
                    });

                    jQuery(".gen-config-options").click(function(event){
                        event.preventDefault();
                        if($("#custom-dialog").is(":data(dialog)")) {
                            $("#custom-dialog").dialog("destroy");
                        }
                        $("#ui-id-3").html($(this).html());
                        $("#custom-dialog").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWebSBS/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
                        $("#custom-dialog").dialog({
                        	title: $(this).html(),
                        	minWidth: 650,
                        	close: function(event, ui) {
                        		$(this).dialog(\'close\');
    						}
    					});
                        jQuery.post("../modules/servers/LiquidWebPrivateParent/stormajax.php?action=edit&id='.$_REQUEST['id'].'",jQuery(this).attr("href"), function(data){
								$("#custom-dialog").html("<p style=\'text-align: center\'>"+data.message+"<p>");
								if(data.status == 1) {
									//window.location.href = "configproducts.php?action=edit&id='.$_REQUEST['id'].'&tab=5";
								}
							},"json");
                    });
                  });
                });
              </script>';
    }


    $lError = FALSE;

    //Is set?
    if((!$username || !$password) && ($_GET['action'] != 'save') && !$lError)
    {
      $config["Error"]["Description"] = '<p style="text-align: center;" class="errorbox"><span style="font-weight: bold">Please enter your API User username in "Username" field and your API User password in "Password".</span></p>';
      foreach ($config as $key => $value) {
          if($key != 'Username' && $key != 'Password' && $key != 'Error') {
              unset($config[$key]);
          }
      }
      $lError = TRUE;
    }

    //Get Parent Servers
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormPrivateParent.php';
    $private = new StormOnDemandStormPrivateParent($username, $password, 'bleed');

    $page = 1;
    $items_per_page = 250;

    $lists = $private->lists($page, $items_per_page);


    if(!$lists && ($_GET['action'] != 'save') && !$lError)
    {
     $config["Error"]["Description"] = '<p style="text-align: center;" class="errorbox"><span style="font-weight: bold">'.$private->getError().'</span></p>';
     foreach ($config as $key => $value) {
          if($key != 'Username' && $key != 'Password' && $key != 'Error') {
              unset($config[$key]);
          }
      }
      $lError = TRUE;
    }

    if (!$lError) {
        $items = $lists['items'];
        while($lists['item_total'] > $page * $items_per_page)
        {
            $page++;
            $lists = $private->lists($page, $items_per_page);
            $items = array_merge($items, $lists['items']);
        }

        if(!$items && ($_GET['action'] != 'save') && !$lError)
        {
            $config["Error"]["Description"] = '<p style="text-align: center;" class="errorbox"><span style="font-weight: bold">You do not have any <b>Private Cloud servers</b>. Please create it before continue.</span></p>';
             foreach ($config as $key => $value) {
              if($key != 'Username' && $key != 'Password' && $key != 'Error') {
                  unset($config[$key]);
              }
          }
          $lError = TRUE;
        }

        if (!$lError) {
            foreach($items as $item)
            {
                $product->defaultConfig['Parent']['options'][$item['uniq_id']] = $item['domain'];
                $product->defaultConfig['AvailableParents']['options'][$item['uniq_id']] = $item['domain'];
            }

            $config['Parent Server']['Options'] = $product->defaultConfig['Parent']['options'];
            $config['Available Parents']['Options'] = $product->defaultConfig['AvailableParents']['options'];


            if (($_GET['action'] != 'save') && !$lError) {
                foreach ($config as $key => $value) {
                    if($key == 'Error') {
                        unset($config[$key]);
                    }
                }
            }
        }
    }

    return $config;
}


function LiquidWebPrivateParent_CreateAccount($params)
{
    //Product Configuration
    $product = new LiquidWebPrivateParentProduct($params['pid']);

    //Hosting Configuration
    $hosting = new StormOnDemand_Hosting($params['serviceid']);

    if(!isset($params['customfields']['uniq_id']))
    {
        $hosting->createCustomField('uniq_id', 'Uniq ID', 'text', '', array(
            'adminonly' =>  1
        ));
    }

    if(isset($params['customfields']['uniq_id']) && $params['customfields']['uniq_id'])
    {
        return "Cannot create server. Please remove value from field Uniq ID before creating server";
    }

    //get configuration
    $username           =   LiquidWebPrivateParent_getOption('Username', $params);
    $password           =   LiquidWebPrivateParent_getOption('Password', $params);
    $template           =   LiquidWebPrivateParent_getOption('Template', $params);
    $bandwidth_quota    =   LiquidWebPrivateParent_getOption('Bandwidth Quota', $params);
    $hostname           =   $params['customfields']['hostname'] ? $params['customfields']['hostname'] : $params['domain'];

    if ($hostname == '') {
        $hostname = $params['customfields']['Create my VPS with following host name'];
    }

    $auto_parent        =   LiquidWebPrivateParent_getOption('AutoParent', $params);
    $parent             =   LiquidWebPrivateParent_getOption('Parent', $params);
    $selected_parents   =   $product->getConfig('AvailableParents');
    $memory             =   LiquidWebPrivateParent_getOption('Memory', $params);
    $diskspace          =   LiquidWebPrivateParent_getOption('Diskspace', $params);
    $vcpu               =   LiquidWebPrivateParent_getOption('VCPU', $params);

    if(isset($params['customfields']['Clone From Server']) && $params['customfields']['Clone From Server']){

        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
        $allHosting = StormOnDemand_Helper::getAllLiquidWebUniqIds($params['clientsdetails']['userid']);

        if(empty($allHosting)){
            return "Wrong uniq_id to clone";
        }

        $fme = false;
        foreach($allHosting as $cUniq){
            if(strcmp($cUniq, $params['customfields']['Clone From Server']) === 0){
                $fme = true;
                break;
            }
        }

        if(!$fme){
            return "Wrong uniq_id to clone";
        }

        //load server class
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
        $server = new StormOnDemandStormServer($username, $password);

        $ret = $server->cloneServer($params['customfields']['Clone From Server'],$hostname,$params['password'], $parent);

        //has error?
        if($error = $server->getError()){
            return $error;
	    } else {
	        for ($i = 0; $i < 5; $i++) {
                $dtl = $server->details($ret['uniq_id']);
                if ($dtl['ip'] == '127.0.0.1') {
                    usleep(30000000);// 30 seconds
                } else {
                    $command = 'UpdateClientProduct';
                    $postData = array(
                        'serviceid' => $params['serviceid'],
                        'dedicatedip' => $dtl['ip'],
                    );
                    $results = localAPI($command, $postData, StormOnDemand_Helper::getAdmin());
                    break;
                }
            }
        }

        $txt = StormOnDemand_Helper::addUniqIdToCustomFields($params['serviceid'],$ret['uniq_id']);
        return "success";
    }

    if($auto_parent && $selected_parents)
    {
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormPrivateParent.php';
        $private = new StormOnDemandStormPrivateParent($username, $password, 'bleed');

        $page_num = 1;
        $page_size = 250;

        $response = $private->lists($page_num, $page_size);
        $parents = $response['items'];
        while($response['item_total'] > $page_num * $page_size)
        {
            $page_num++;
            $response = $private->lists($page_size, $page_num);
            $parents = array_merge($parents, $response['items']);
        }

        $choose = array();
        $details = array();
        foreach($parents as $p)
        {
            if(!in_array($p['uniq_id'], $selected_parents))
            {
                continue;
            }

            $parent_memory =   ($p['resources']['memory']['free'] / $p['resources']['memory']['total']) * 100;
            $parent_disk   =   ($p['resources']['diskspace']['free'] / $p['resources']['diskspace']['total']) * 100;

            $choose[$p['uniq_id']] = ($parent_memory + $parent_disk) / 2;
            $details[$p['uniq_id']] = array
            (
                'memory'    =>  $p['resources']['memory']['free'],
                'diskspace' =>  $p['resources']['diskspace']['free']
            );
        }

        asort($choose, SORT_NUMERIC);
        $choose = array_reverse($choose);

        //Get Config ID Details
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormConfig.php';
        $api_config = new StormOnDemandStormConfig($username, $password, 'bleed');

        $found = false;
        foreach($choose as $parent_id => $percentages)
        {
            if($details[$parent_id]['memory'] >= $memory && $details[$parent_id]['diskspace'] >= $diskspace)
            {
                $parent = $parent_id;
                $found = true;
            }
        }

        if(!$found)
        {
            return "Cannot find enough space to create VM";
        }
    }
    else
    {
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormPrivateParent.php';
        $private = new StormOnDemandStormPrivateParent($username, $password, 'bleed');

        $details = $private->details($parent);
        if($details['resources']['memory']['free'] < $memory || $details['resources']['diskspace']['free'] < $diskspace)
        {
            return "Cannot find enough space to create VM";
        }
    }
    //check bandwidth quota
    if(!in_array($bandwidth_quota, $product->defaultConfig['Bandwidth Quota']['options']))
    {
        return "The value you selected for Bandwidth is incorrect, please review the configuration of the product";
    }

    $configuration = array
    (
        'new_ips'           =>  LiquidWebPrivateParent_getOption("Number of IPs", $params),
        'image_id'          =>  LiquidWebPrivateParent_getOption('Image', $params),
        'bandwidth_quota'   =>  $bandwidth_quota,
        'parent'            =>  $parent,
        'memory'            =>  $memory,
        'diskspace'         =>  $diskspace,
        'vcpu'              =>  $vcpu
    );

    if(LiquidWebPrivateParent_getOption('Backup Plan', $params))
    {
        $configuration['backup_enabled']    =  1;
        $configuration['backup_quota']      =  LiquidWebPrivateParent_getOption('Backup Plan', $params) == 'quota' ? LiquidWebPrivateParent_getOption('Backup Quota', $params) : LiquidWebPrivateParent_getOption('Daily Backup Quota', $params);
        $configuration['backup_plan']       =  LiquidWebPrivateParent_getOption('Backup Plan', $params);
    }


    //load server class
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password, 'bleed');

    //create server with base configuration
    $ret = $server->create($hostname, $params['password'], 0, $template, $configuration);

    //has error?
    $error = $server->getError();
    if($error)
    {
        return $error;
	} else {
		for ($i = 0; $i < 5; $i++) {
			$dtl = $server->details($ret['uniq_id']);
			if ($dtl['ip'] == '127.0.0.1') {
				usleep(30000000);// 30 seconds
			} else {
				$command = 'UpdateClientProduct';
				$postData = array(
					'serviceid' => $params['serviceid'],
					'dedicatedip' => $dtl['ip'],
				);
				$results = localAPI($command, $postData, StormOnDemand_Helper::getAdmin());
				break;
			}
		}
    }

    //Save Uniq ID
    $hosting->setCustomField('uniq_id', $ret['uniq_id']);

    //return successful message
    return "success";
}

function LiquidWebPrivateParent_TerminateAccount($params)
{
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);

    //we need uniq_id to terminate server
    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password, 'bleed');
    //create server with base configuration
    $ret = $server->destroy($uniq_id);

    if($errro = $server->getError())
    {
        return $error;
    }

    //Hosting Configuration
    $hosting = new StormOnDemand_Hosting($params['serviceid']);
    //Clear UNIQ ID
    $hosting->setCustomField('uniq_id', '');

    return "success";
}

/**
 * Admin Area. Custom Buttons
 * @return type
 */
function LiquidWebPrivateParent_AdminCustomButtonArray()
{
    return array(
        'Reboot'    => 'reboot',
        'Shutdown'  => 'shutdown',
        'Start'    =>  'start',
    );
}

function LiquidWebPrivateParent_ClientAreaCustomButtonArray()
{
    $id = (int)$_REQUEST['id'];
    $q = mysql_query("SELECT tblproducts.*, tblproducts.id as pid
        FROM tblhosting
        LEFT JOIN tblproducts ON tblhosting.packageid = tblproducts.id
        WHERE tblhosting.id = ".(int)$id);
    $params = mysql_fetch_assoc($q);

    $q = mysql_query("SELECT tblhostingconfigoptions.*,  tblproductconfigoptions.optionname, tblproductconfigoptions.optiontype, tblproductconfigoptionssub.optionname as optname
        FROM tblhostingconfigoptions
        LEFT JOIN tblproductconfigoptions ON tblhostingconfigoptions.configid = tblproductconfigoptions.id
        LEFT JOIN tblproductconfigoptionssub ON tblhostingconfigoptions.optionid = tblproductconfigoptionssub.id
        WHERE tblhostingconfigoptions.relid=".(int)$id);

    while($row = mysql_fetch_assoc($q))
    {
        $optionname = $row['optionname'];
        if(strpos($row['optionname'], '|'))
        {
            $exp = explode('|', $row['optionname']);
            $optionname = $exp[0];
        }

        switch($row['optiontype'])
        {
            case 1:
                if(strpos($row['optname'],'|'))
                {
                    $ex = explode('|', $row['optname']);
                    $params['configoptions'][$optionname] = $ex[0];
                }
                else
                {
                    $params['configoptions'][$optionname] = $row['optname'];
                }
                break;

            case 3:
            case 4:
                $params['configoptions'][$optionname] = $row['qty'];
                break;
        }
    }

    $return = array
    (
        'Start'         =>  'clientStart',
        'Shutdown'      =>  'clientShutdown',
        'Reboot'        =>  'clientReboot',
        'Restore'       =>  'restore',
        'History'       =>  'history',
        'Block Storage' =>  'blockStorage',
    );

    if(LiquidWebPrivateParent_getOption("IPs Management", $params))
    {
        $return['IP Management'] = 'ipmanagement';
    }


    if(LiquidWebPrivateParent_getOption("Firewall", $params))
    {
        $return['Firewall'] = 'firewall';
    }

    if(LiquidWebPrivateParent_getOption("Backup Plan", $params))
    {
        $return['Backups'] = 'backups';
    }

    return $return;
}

function LiquidWebPrivateParent_Reboot($params)
{
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);

    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password, 'bleed');
    //create server with base configuration
    $ret = $server->reboot($uniq_id);

    if($error = $server->getError())
    {
        return $error;
    }

    return "success";
}


function LiquidWebPrivateParent_clientReboot($params)
{
    $vars = array();
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);

    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }

    switch($_REQUEST['modaction'])
    {
        case 'reboot':
            $row = mysql_fetch_assoc($q);

            require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
            $server = new StormOnDemandStormServer($username, $password, 'bleed');
            //create server with base configuration
            $ret = $server->reboot($uniq_id);

            if($error = $server->getError())
            {
                $vars['error'] = $error;
            }
            else
            {
                $vars['info'] = 'Rebooting machine';
                header("Location: clientarea.php?action=productdetails&id=".$params['serviceid']."&success=".$vars['info']);
            }
            break;
    }

    //getting custom configurations
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
    $customConfig = StormOnDemand_Helper::getCustomConfigValues();

    $vars['custom_template'] = $customConfig['custom_template'];
    $vars['storm_links']    =  LiquidWebPrivateParent_ClientAreaCustomButtonArray();
    $vars['loadBootstrap']  =  LiquidWebPrivateParent_lookForBootstrap();
    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'reboot.tpl';
    $vars['params'] = $params;
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">Reboot</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}


function LiquidWebPrivateParent_Shutdown($params)
{
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);

    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password, 'bleed');
    //create server with base configuration
    $ret = $server->shutdown($uniq_id);

    if($error = $server->getError())
    {
        return $error;
    }

    return "success";
}

function LiquidWebPrivateParent_ClientShutdown($params)
{
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);

    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }

    switch($_REQUEST['modaction'])
    {
        case 'shutdown':
            $row = mysql_fetch_assoc($q);
            require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
            $server = new StormOnDemandStormServer($username, $password, 'bleed');
            //create server with base configuration
            $ret = $server->shutdown($uniq_id);

            if($error = $server->getError())
            {
                $vars['error'] = $error;
            }
            else
            {
                $vars['info'] = 'Shutting down machine';
                header("Location: clientarea.php?action=productdetails&id=".$params['serviceid']."&success=".$vars['info']);
            }
            break;
    }

    //getting custom configurations
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
    $customConfig = StormOnDemand_Helper::getCustomConfigValues();

    $vars['custom_template'] = $customConfig['custom_template'];
    $vars['storm_links'] =  LiquidWebPrivateParent_ClientAreaCustomButtonArray();
    $vars['loadBootstrap'] =  LiquidWebPrivateParent_lookForBootstrap();
    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'shutdown.tpl';
    $vars['params'] = $params;
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">Shutdown</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}

function LiquidWebPrivateParent_Start($params)
{
    $vars = array();
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);

    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password, 'bleed');
    //staart sever
    $ret = $server->start($uniq_id);

    if($error = $server->getError())
    {
        return $error;
    }

    return "success";
}

function LiquidWebPrivateParent_ClientStart($params)
{
    $vars = array();
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);

    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }

    switch($_REQUEST['modaction'])
    {
        case 'start':
            $row = mysql_fetch_assoc($q);

            require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
            $server = new StormOnDemandStormServer($username, $password, 'bleed');
            //staart sever
            $ret = $server->start($uniq_id);

            if($error = $server->getError())
            {
                $vars['error'] = $error;
            }
            else
            {
                $vars['info'] = 'Starting machine';
                header("Location: clientarea.php?action=productdetails&id=".$params['serviceid']."&success=".$vars['info']);
            }
            break;
    }

    //getting custom configurations
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
    $customConfig = StormOnDemand_Helper::getCustomConfigValues();

    $vars['custom_template'] = $customConfig['custom_template'];
    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'start.tpl';
    $vars['params'] = $params;
    $vars['storm_links']    =  LiquidWebPrivateParent_ClientAreaCustomButtonArray();
    $vars['loadBootstrap']  =  LiquidWebPrivateParent_lookForBootstrap();
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">Start</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}


function LiquidWebPrivateParent_ChangePackage($params)
{
    //Product Configuration
    $product = new LiquidWebPrivateParentProduct($params['pid']);

    //get configuration
    $username           =   LiquidWebPrivateParent_getOption('Username', $params);
    $password           =   LiquidWebPrivateParent_getOption('Password', $params);
    $ipcount            =   LiquidWebPrivateParent_getOption("Number of IPs", $params);
    $bandwidth_quota    =   LiquidWebPrivateParent_getOption('Bandwidth Quota', $params);
    $hostname           =   $params['customfields']['hostname'] ? $params['customfields']['hostname'] : $params['domain'];
    if ($hostname == '') {
        $hostname = $params['customfields']['Create my VPS with following host name'];
    }
    $memory             =   LiquidWebPrivateParent_getOption('Memory', $params);
    $diskspace          =   LiquidWebPrivateParent_getOption('Diskspace', $params);
    $vcpu               =   LiquidWebPrivateParent_getOption('VCPU', $params);

    //check bandwidth quota
    if(!in_array($bandwidth_quota, $product->defaultConfig['Bandwidth Quota']['options']))
    {
        return "The value you selected for Bandwidth is incorrect, please review the configuration of the product";
    }

    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password, 'bleed');


    //Get Info
    $info = $server->details($uniq_id);
    if(!$info)
    {
        return $server->getError();
    }

    $parent = $info['parent'];
    if(!$parent)
    {
        return 'Cannot update this server. Parent ID is not set!';
    }

    //details
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkIP.php';
    $ip = new StormOnDemandNetworkIP($username, $password, 'bleed');

    $lists = $ip->lists($uniq_id);

    if($ipcount && $ipcount < count($lists['items']))
    {
        return "Cannot make downgrade. User have too many IP's";
    }

    $configuration = array
    (
        'bandwidth_quota'   =>  $bandwidth_quota,
        'domain'            =>  $hostname
    );

    if(LiquidWebPrivateParent_getOption('Backup Plan', $params))
    {
        $configuration['backup_enabled']    =  1;
        $configuration['backup_quota']      =  LiquidWebPrivateParent_getOption('Backup Plan', $params) == 'quota' ? LiquidWebPrivateParent_getOption('Backup Quota', $params) : LiquidWebPrivateParent_getOption('Daily Backup Quota', $params);
        $configuration['backup_plan']       =  LiquidWebPrivateParent_getOption('Backup Plan', $params);
    }

    $Firewall = LiquidWebPrivateParent_getOption("Firewall", $params);
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkFirewall.php';
    $firewall = new StormOnDemandNetworkFirewall($username, $password, 'bleed');

    if($Firewall == '0'){
        $firewall->update($uniq_id);
    }else{
        $firewall->update($uniq_id,'basic',array());
    }



    $ret = $server->update($uniq_id, $configuration);

    if($error = $server->getError())
    {
        return 'Nothing upgraded! '.$error;
    }

    $server->resize($uniq_id, 0, 0, array(
        'memory'    =>  $memory,
        'vcpu'      =>  $vcpu,
        'diskspace' =>  $diskspace,
        'parent'    =>  $parent
    ));

    if($error = $server->getError())
    {
        return $error;
    }

    return "success";
}

function LiquidWebPrivateParent_ClientArea($params)
{
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);
    $monitoring =   LiquidWebPrivateParent_getOption('Monitoring', $params);

    //we need uniq_id to terminate server
    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }
    ////////////////////////////
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password, 'bleed');

    $q2 = mysql_query("SELECT tblproducts.* FROM tblhosting LEFT JOIN tblproducts ON tblhosting.packageid = tblproducts.id WHERE tblhosting.id = " . (int)$params['serviceid'] . " LIMIT 1");
    $row2 = mysql_fetch_assoc($q2);

    if($monitoring == null){
        $monitoring = $row2['configoption14'];
    }

    if(isset($_REQUEST['stormajax']))
    {
        //ob_clean();
        switch($_REQUEST['stormajax'])
        {
            case 'status':
                $status = $server->status($uniq_id);
                if($server->getError())
                {
                    return;
                }
                echo $status['status'];
                break;

            case 'bandwidth_graph':
                require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandMonitoringBandwidth.php';
                $bandwidth = new StormOnDemandMonitoringBandwidth($username, $password, 'bleed');

                $graph = $bandwidth->graph($uniq_id, $width = 510, $height = 100, $_REQUEST['frequency'], 1);
                header('Content-type: '.$graph['content_type']);
                echo base64_decode($graph['content']);
                break;

            case 'bandwidth_stats':
                require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandMonitoringBandwidth.php';
                $bandwidth = new StormOnDemandMonitoringBandwidth($username, $password, 'bleed');

                $stats = $bandwidth->stats($uniq_id);
                global $smarty;
                $smarty->assign('stats', $stats);
                $tpl = $smarty->fetch(dirname(__FILE__).DS.'clientarea'.DS.'subviews'.DS.'bandwidth.tpl');
                echo $tpl;
                break;

            case 'load_graph':
                require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandMonitoringLoad.php';
                $load = new StormOnDemandMonitoringLoad($username, $password, 'bleed');

                $graph = $load->graph($uniq_id, $width = 510, $height = 100, $_REQUEST['stat'] = 'load5', $_REQUEST['duration'], 1);
                header('Content-type: '.$graph['content_type']);
                echo base64_decode($graph['content']);
                break;

            case 'load_stats':
                require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandMonitoringLoad.php';
                $load = new StormOnDemandMonitoringLoad($username, $password, 'bleed');

                $stats = $load->stats($uniq_id);

                global $smarty;
                $smarty->assign('stats', $stats);
                $tpl = $smarty->fetch(dirname(__FILE__).DS.'clientarea'.DS.'subviews'.DS.'load.tpl');
                echo $tpl;
                break;

            case 'history':
                $history = $server->history($uniq_id, 5, 1);
                global $smarty;
                $smarty->assign('history', $history['items']);
                $smarty->assign('params', $params);
                $tpl = $smarty->fetch(dirname(__FILE__).DS.'clientarea'.DS.'subviews'.DS.'history.tpl');
                echo $tpl;
                break;
        }
        die();
    }

    $details = $server->details($uniq_id);
    if($server->getError())
    {
        return;
    }

    global $smarty;
    $smarty->assign('buttons', LiquidWebPrivateParent_ClientAreaCustomButtonArray());
    $smarty->assign('details', $details);
    $smarty->assign('params', $params);
    $smarty->assign('monitoring', $monitoring);
    $smarty->assign('loadBootstrap', LiquidWebPrivateParent_lookForBootstrap());

    $code = $smarty->fetch(dirname(__FILE__).DS.'clientarea'.DS.'subviews'.DS.'clientarea.tpl');

    return $code;
}

function LiquidWebPrivateParent_AdminServicesTabFields($params)
{
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);

    $uniq_id = $params['customfields']['uniq_id'];

    if(!$uniq_id)
    {
        return false;
    }

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password, 'bleed');


    if(isset($_REQUEST['stormajax']) && $_REQUEST['stormajax'] == 'storm-history')
    {
        ob_clean();
        $history = $server->history($uniq_id, 20, $_REQUEST['page'] ? $_REQUEST['page'] : 1);

        $items = $history['items'];
        if(!$error = $server->getError())
        {
            echo '<style type="text/css">
                    .storm-notification td
                    {
                        background-color: #FCF8E3!important;
                    }

                    .storm-error td
                    {
                        background-color: #F2DEDE!important;
                    }
                 </style>';

            echo '<table style="width: 100%" class="datatable">
                    <tr>
                        <th>Description</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Severity</th>
                    </tr>';
            foreach($items as $item)
            {
                echo '<tr class="storm-'.strtolower($item['severity']).'">
                        <td>'.$item['description'].'</td>
                        <td>'.$item['startdate'].'</td>
                        <td>'.$item['enddate'].'</td>
                        <td>'.$item['severity'].'</td>
                      </tr>';
            }
            echo '<tfoot>
                    <tr>
                        <td colspan="4" style="padding-top: 5px">';
            if($history['page_num'] > 1)
            {
                echo '<a class="history-change-page btn btn-info" style="float: left" href="page='.($history['page_num']-1).'&stormajax=storm-history">Prev</a>';
            }

            if($history['page_num'] < $history['page_total'])
            {
                echo '<a class="history-change-page btn btn-info" style="float: right" href="page='.($history['page_num']+1).'&stormajax=storm-history">Next</a>';
            }
            echo '      </td>
                      </tr>
                    </tfoot>';
            echo '</table>';
            echo '<script type="text/javascript">
                    $(function(){
                        $(".history-change-page").click(function(event){
                            event.preventDefault();

                            $("#storm-history").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWebPrivateParent/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
                            $.post(document.location.toString()+"&"+$(this).attr("href"), function(data){
                                $("#storm-history").html(data);
                            });
                        });
                    });
                  </script>';

        }
        else
        {
            echo '<p style="color: red">'.$error.'</div>';
        }
        die();
    }
    elseif(isset($_REQUEST['stormajax']) && $_REQUEST['stormajax'] == 'storm-image')
    {
        ob_clean();
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormImage.php';
        $image = new StormOnDemandStormImage($username, $password, 'bleed');

        $images = $image->lists(20, $_REQUEST['page'] ? $_REQUEST['page'] : 1);


        $items = $images['items'];
        if(!$error = $image->getError())
        {
            echo '<table style="width: 100%" class="datatable">
                    <tr>
                        <th>Template Description</th>
                        <th>Source Hostname</th>
                        <th>Time taken</th>
                        <th style="width: 100px"></th>
                    </tr>';
            foreach($items as $item)
            {
                echo '<tr>
                        <td>'.$item['template_description'].'</td>
                        <td>'.$item['source_hostname'].'</td>
                        <td>'.$item['time_taken'].'</td>
                        <td><a style="float: right" href="stormajax=storm-image-restore&image_id='.$item['id'].'" class="btn btn-danger storm-restore">Restore</a></td>
                      </tr>';
            }
            echo '<tfoot>
                    <tr>
                        <td colspan="4" style="padding-top: 5px">';
            if($images['page_num'] > 1)
            {
                echo '<a class="image-change-page btn btn-info" style="float: left" href="page='.($images['page_num']-1).'&stormajax=storm-image">Prev</a>';
            }

            if($images['page_num'] < $images['page_total'])
            {
                echo '<a class="image-change-page btn btn-info" style="float: right" href="page='.($images['page_num']+1).'&stormajax=storm-image">Next</a>';
            }
            echo '      </td>
                      </tr>
                    </tfoot>';
            echo '</table>';
            echo '<script type="text/javascript">
                    $(function(){
                        $(".image-change-page").click(function(event){
                            event.preventDefault();

                            $("#storm-images").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWebPrivateParent/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
                            $.post(document.location.toString()+"&"+$(this).attr("href"), function(data){
                                $("#storm-images").html(data);
                            });
                        });

                        $(".storm-restore").click(function(event){
                            event.preventDefault();
                            link = $(this);

                            /*$("#restore-dialog").dialog("destroy");*/
                            $("#restore-dialog").html("Are you sure you want to restore?");
                            $("#restore-dialog").dialog({
                            resizable: false,
                            height:140,
                            modal: true,
                            buttons:
                            {
                                "Yes": function() {
                                    $( this ).dialog( "close" );
                                    /*$("#restore-dialog").dialog("destroy"); */
                                    $("#restore-dialog").dialog();
                                    $("#restore-dialog").html("Loading...");

                                    $.post(document.location.toString()+"&"+$(link).attr("href"), function(data){
                                        $("#restore-dialog").html(data);
                                    });
                                },
                                Cancel: function() {
                                $( this ).dialog( "close" );
                            }
                            }
                            });
                        });
                    });
                  </script>
                  <div id="restore-dialog" title="Restore"></div>';
        }
        else
        {
            echo '<p style="color: red">'.$error.'</div>';
        }
        die();
    }
    elseif(isset($_REQUEST['stormajax']) && $_REQUEST['stormajax'] == 'storm-image-restore')
    {
        ob_clean();
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormImage.php';
        $image = new StormOnDemandStormImage($username, $password, 'bleed');

        $image->restore($uniq_id, $_REQUEST['image_id']);
        if($error = $image->getError())
        {
            echo '<p style="color:red">'.$error.'</p>';
            die();
        }

        echo '<p>Restoring in progress...</p>';
        die();
    }
    elseif(isset($_REQUEST['stormajax']) && $_REQUEST['stormajax'] == 'storm-template')
    {
        ob_clean();

        $hid_template = array('0');
        $q = mysql_query("SELECT * FROM `StormBilling_customconfig` where `config_name` = 'wiz_pg_4_hide_from_tmplt_list'");
        if(($res = mysql_fetch_assoc($q))) {
            $hid_template = @explode(",", $res['config_value']);
        }

        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormTemplate.php';
        $template = new StormOnDemandStormTemplate($username, $password, 'bleed');

        $templates = $template->lists(40, $_REQUEST['page'] ? $_REQUEST['page'] : 1);

        $items = $templates['items'];
        if(!$error = $template->getError())
        {
            echo '<table style="width: 100%" class="datatable">
                    <tr>
                        <th>Description</th>
                        <th style="width: 100px"></th>
                    </tr>';
            foreach($items as $item)
            {
                foreach ($hid_template as $tempid) {
                    if ($tempid != $item['id']) {
                        if($item['deprecated'] == 1)
                            continue;
                        echo '<tr>
                                <td>'.$item['description'].'</td>
                                <td><a style="float: right" href="stormajax=storm-template-restore&template_id='.$item['id'].'" class="btn btn-danger storm-restore">Restore</a></td>
                            </tr>';
                    }
                }
            }
            echo '<tfoot>
                    <tr>
                        <td colspan="4" style="padding-top: 5px">';
            if($templates['page_num'] > 1)
            {
                echo '<a class="template-change-page btn btn-info" style="float: left" href="page='.($templates['page_num']-1).'&stormajax=storm-template">Prev</a>';
            }

            if($templates['page_num'] < $templates['page_total'])
            {
                echo '<a class="template-change-page btn btn-info" style="float: right" href="page='.($templates['page_num']+1).'&stormajax=storm-template">Next</a>';
            }
            echo '      </td>
                      </tr>
                    </tfoot>';
            echo '</table>';
            echo '<script type="text/javascript">
                    $(function(){
                        $(".template-change-page").click(function(event){
                            event.preventDefault();

                            $("#storm-templates").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWebPrivateParent/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>.");
                            $.post(document.location.toString()+"&"+$(this).attr("href"), function(data){
                                $("#storm-templates").html(data);
                            });
                        });

                        $(".storm-restore").click(function(event){
                            event.preventDefault();
                            link = $(this);

                           /* $("#restore-dialog").dialog("destroy");*/
                            $("#restore-dialog").html("Are you sure you want to restore?");
                            $("#restore-dialog").dialog({
                            resizable: false,
                            height:140,
                            modal: true,
                            buttons:
                            {
                                "Yes": function() {
                                    $( this ).dialog( "close" );
                                    /*$("#restore-dialog").dialog("destroy");     */
                                    $("#restore-dialog").dialog();
                                    $("#restore-dialog").html("Loading...");

                                    $.post(document.location.toString()+"&"+$(link).attr("href"), function(data){
                                        $("#restore-dialog").html(data);
                                    });
                                },
                                Cancel: function() {
                                $( this ).dialog( "close" );
                            }
                            }
                            });
                        });
                    });
                  </script>
                  <div id="restore-dialog" title="Restore"></div>';
        }
        else
        {
            echo '<p style="color: red">'.$error.'</div>';
        }
        die();
    }
    elseif(isset($_REQUEST['stormajax']) && $_REQUEST['stormajax'] == 'storm-template-restore')
    {
        ob_clean();
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormTemplate.php';
        $template = new StormOnDemandStormTemplate($username, $password, 'bleed');


        $template->restore($uniq_id, $_REQUEST['template_id']);
        if($error = $template->getError())
        {
            echo '<p style="color:red">'.$error.'</p>';
            die();
        }

        echo '<p>Restoring in progress...</p>';
        die();
    }

    $details = $server->details($uniq_id);
    $status = $server->status($uniq_id);

    if($error = $server->getError())
    {
        return array
        (
            'Error'             =>  '<p style="color: red">'.$error.'</p>',
            'Server Uniq ID'    =>  '<input type="text" name="uniq_id" value="'.$uniq_id.'" />'
        );
    }

    $script = '<script type="text/javascript">
                    $(function(){
                        $("#load-storm-history").click(function(event){
                            event.preventDefault();
                            $("#storm-history").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWebPrivateParent/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
                            $.post(document.location.toString(), {"stormajax" : "storm-history"}, function(data){
                                $("#storm-history").html(data);
                            });
                        });

                        $("#load-storm-images").click(function(event){
                            event.preventDefault();
                            $("#storm-images").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWebPrivateParent/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
                            $.post(document.location.toString(), {"stormajax" : "storm-image"}, function(data){
                                $("#storm-images").html(data);
                            });
                        });

                        $("#load-storm-templates").click(function(event){
                            event.preventDefault();
                            $("#storm-templates").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWebPrivateParent/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
                            $.post(document.location.toString(), {"stormajax" : "storm-template"}, function(data){
                                $("#storm-templates").html(data);
                            });
                        });
                    });
                  </script>';

    $arr['Server Uniq ID'] = '<input type="text" name="uniq_id" value="'.$uniq_id.'" />';
    $arr['Server Status'] = $status['status'].$script;
    $arr['Create Date'] = $details['create_date'];
    $arr['Template Description'] = $details['template_description'];
    $arr['Bandwidth Quota'] = $details['bandwidth_quota'];
    $arr['IP'] = $details['ip'];
    $arr['History'] = '<div id="storm-history"><a class="btn" id="load-storm-history">Load History</a></div>';
    $arr['Restore From Image'] = '<div id="storm-images"><a class="btn" id="load-storm-images">Load Images</a></div>';
    $arr['Restore From Template'] = '<div id="storm-templates"><a class="btn" id="load-storm-templates">Load Templates</a></div>';

    return $arr;
}

//FIREWALL
function LiquidWebPrivateParent_Firewall($params)
{
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);

    //we need uniq_id to terminate server
    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkFirewall.php';
    $firewall = new StormOnDemandNetworkFirewall($username, $password, 'bleed');
    $vars = array();

    //update
    if(isset($_POST['firewall']))
    {
        switch($_POST['type'])
        {
            case 'none':
                $firewall->update($uniq_id);
                break;

            case 'basic':
                $options = array_keys($_POST['basic_opt']);
                $basic = $firewall->getBasicOptions($uniq_id);

            	foreach($options as &$value){
            		$value = str_replace(LiquidWebPPFirewallSlashEscapeChar,'/',$value);
            	}

                $firewall->update($uniq_id, 'basic', $options);
                break;

            case 'advanced':
                $advanced =  $_POST['advanced'];
                foreach($advanced as &$a)
                {
                    foreach($a as &$v)
                    {
                        if(!$v)
                        {
                            $v = '*';
                        }
                    }
                }

                $firewall->update($uniq_id, 'advanced', $advanced);
                break;
        }

        $error = $firewall->getError();
        if($error)
        {
            $vars['error'] =  $error;
        }
        else
        {
            $vars['info'] = 'Firewall updated';
        }
    }


    //get firewall configuration
    $ret = $firewall->details($uniq_id);

    $error = $firewall->getError();
    if($error)
    {
        $vars['error'] =  $error;
    }
    $vars['type'] = $ret['type'];

    //get basic settings
    $ret = $firewall->getBasicOptions($uniq_id);
    $vars['options'] = $ret['options'];

	if(!empty($vars['options'])){
		foreach($vars['options'] as $k => $v){
		     $vars['options'][$k] = str_replace('/',LiquidWebPPFirewallSlashEscapeChar, $v);
		}
	}

    if($vars['type'] == 'none')
    {

    }

    if($vars['type'] == 'basic')
    {
        $ret = $firewall->rules($uniq_id);
        //$vars['advanced_rules'] = $ret['rules'];
        foreach($ret['rules'] as $rule){
            $vars['rules'][str_replace('/',LiquidWebPPFirewallSlashEscapeChar, $rule['label'])] = 1;
        }
    }

    if($vars['type'] == 'advanced')
    {
        $ret = $firewall->rules($uniq_id);
        $vars['advanced_rules'] = $ret['rules'];
    }

	//getting custom configurations
	require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
	$customConfig = StormOnDemand_Helper::getCustomConfigValues();

    $vars['custom_template'] = $customConfig['custom_template'];
    $vars['storm_links']    =  LiquidWebPrivateParent_ClientAreaCustomButtonArray();
    $vars['loadBootstrap']  =  LiquidWebPrivateParent_lookForBootstrap();
    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'firewall.tpl';
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">Firewall</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}


//IP Management
function LiquidWebPrivateParent_IPManagement($params)
{
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);
    $ipcount    =   LiquidWebPrivateParent_getOption("Maximum IP Addresses", $params);

    //we need uniq_id to terminate server
    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkIP.php';
    $ipmanagement = new StormOnDemandNetworkIP($username, $password, 'bleed');
    $vars = array();

    //update
    if(isset($_POST['modaction']))
    {
        switch($_REQUEST['modaction'])
        {
            case 'add':
                if((int)$_REQUEST['ip_amount'] <= 0)
                {
                    $vars['error'] = "Invalid amount";
                    break;
                }

                if($ipcount)
                {
                    $ret = $ipmanagement->lists($uniq_id);
                    $ips = count($ret['items']);
                    if($ips + $_REQUEST['ip_amount'] > $ipcount)
                    {
                        $vars['error'] = "Too many IP's. Cannon add more";
                        break;
                    }
                }

                require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
                $server = new StormOnDemandStormServer($username, $password, 'bleed');


                $status = $server->status($uniq_id);
                if($status['running'][0]['status'] == 'Adding IPs')
                {
                    $vars['error'] = 'Adding new IP in progress. Cannot add more';
                    break;
                }

                $ipmanagement->add($uniq_id, $_REQUEST['ip_amount']);
                if($error = $ipmanagement->getError())
                {
                    $vars['error'] = $error;
                }
                else
                {
                    $vars['info'] = 'Adding new IP in progress';
                }
                break;

            case 'remove':
                require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
                $server = new StormOnDemandStormServer($username, $password, 'bleed');
                $status = $server->status($uniq_id);
                if($status['running'][0]['status'] == 'Removing IP')
                {
                    $vars['error'] = 'Deleting IP in progress. Cannot delete now';
                    break;
                }

                $ipmanagement->remove($uniq_id, $_REQUEST['ip']);
                if($error = $ipmanagement->getError())
                {
                    $vars['error'] = $error;
                }
                else
                {
                    $vars['info'] = 'Deleting IP in progress.';
                }
                break;
        }
    }

    //getting custom configurations
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
    $customConfig = StormOnDemand_Helper::getCustomConfigValues();

    $vars['custom_template'] = $customConfig['custom_template'];

    //get lists op IPs
    $ret = $ipmanagement->lists($uniq_id);
    $vars['list'] = $ret['items'];
    $vars['ip_count'] = $ipcount;
    $vars['storm_links']    =  LiquidWebPrivateParent_ClientAreaCustomButtonArray();
    $vars['loadBootstrap']  =  LiquidWebPrivateParent_lookForBootstrap();
    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'ipmanagement.tpl';
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">IP Management</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}


//Backups
function LiquidWebPrivateParent_Backups($params)
{
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);

    //we need uniq_id to terminate server
    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormBackup.php';
    $backup = new StormOnDemandStormBackup($username, $password, 'bleed');
    $vars = array();

    //update
    if(isset($_POST['modaction']))
    {
        switch($_REQUEST['modaction'])
        {
            case 'restore':
                $backup->restore($uniq_id, $_REQUEST['backup_id']);
                if($error = $backup->getError())
                {
                    $vars['error'] = $error;
                }
                else
                {
                    $vars['info'] = 'IP';
                }
                break;
        }
    }

    //getting custom configurations
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
    $customConfig = StormOnDemand_Helper::getCustomConfigValues();

    $vars['custom_template'] = $customConfig['custom_template'];

    $ret = $backup->lists($uniq_id);
    $vars['list'] = $ret['items'];
    $vars['storm_links']    =  LiquidWebPrivateParent_ClientAreaCustomButtonArray();
    $vars['loadBootstrap']  =  LiquidWebPrivateParent_lookForBootstrap();
    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'backups.tpl';
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">IP Management</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}

function LiquidWebPrivateParent_Restore($params)
{
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);

    //we need uniq_id to terminate server
    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }

    //Templates!
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormTemplate.php';
    $template = new StormOnDemandStormTemplate($username, $password, 'bleed');

    $page = 1;
    $templates = array();
    $ret = $template->lists(20, $page);
    $templates = $ret['items'];
    while(isset($ret['page_num']) && $ret['page_num'] < $ret['page_total'])
    {
        $ret = $template->lists(20, ++$page);
        $templates = array_merge($templates, $ret['items']);
    }
    foreach($templates as $key => $t)
    {
        if($t['deprecated'])
        {
            unset($templates[$key]);
        }
    }
    $vars['templates'] = $templates;

    //Images
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormImage.php';
    $image = new StormOnDemandStormImage($username, $password, 'bleed');

    // Get all user uniq_id
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
    $rAllUniqsId  = StormOnDemand_Helper::getAllLiquidWebUniqIds($params['clientsdetails']['userid']);

    $page = 1;
    $images = array();
    $ret = $image->lists(20, $page);
    if(isset($ret['items']) && !empty($ret['items'])){
        foreach($ret['items'] as $item){
            if(in_array($item['source_uniq_id'], $rAllUniqsId) !== false){
                $images []= $item;
            }
        }
    }

    while(isset($ret['page_num']) && $ret['page_num'] < $ret['page_total'])
    {
        $ret = $image->lists(20, ++$page);
        if(!empty($ret['items'])){
            foreach($ret['items'] as $item){
                if(in_array($item['source_uniq_id'], $rAllUniqsId) !== false){
                    $images []= $item;
                }
            }
        }
    }
    $vars['images'] = $images;

    //Servers
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $apiServer = new StormOnDemandStormServer($username, $password);

    $vars['servers'] 		= StormOnDemand_Helper::getAllLiquidWebHosting($params['clientsdetails']['userid']);
    if(!empty($vars['servers'])){
        foreach($vars['servers'] as $k=> $server){
            if(strcmp($uniq_id, $server['uniq_id']) === 0){
                unset($vars['servers'][$k]);
            }
        }
    }

    //update
    if(isset($_POST['modaction']))
    {
        switch($_REQUEST['modaction'])
        {
            case 'restore':
                switch ($_REQUEST['type'])
                {
                    case 'template':
                        $template->restore($uniq_id, $_REQUEST['template_id']);
                        if($error = $template->getError())
                        {
                            $vars['error'] = $error;
                        }
                        else
                        {
                            $vars['info'] = 'Restoring from template. Please wait';
                            header("Location: clientarea.php?action=productdetails&id=".$params['serviceid']."&success=".$vars['info']);
                        }
                        break;

                    case 'image':
                        $image->restore($uniq_id, $_REQUEST['image_id']);
                        if($error = $image->getError())
                        {
                            $vars['error'] = $error;
                        }
                        else
                        {
                            $vars['info'] = 'Restoring from image. Please wait';
                        }
                        break;

                    case 'server':

                        if(!isset($_REQUEST['server_id']) || !preg_match('/^[a-zA-Z0-9]{1,}$/D', $_REQUEST['server_id'])){
                            $vars['error'] = "Please select server id";
                        }

                        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
                        $rAllUniqsId  = StormOnDemand_Helper::getAllLiquidWebUniqIds($params['clientsdetails']['userid']);

                        if(empty($rAllUniqsId)){
                            $vars['error'] = "Wrong uniq_id";
                        }

                        $ckeck = false;
                        foreach($rAllUniqsId as $uniq){
                            if(strcmp($uniq, $_REQUEST['server_id']) === 0 && strcmp($uniq_id, $_REQUEST['server_id']) !== 0){
                                $check = true;
                                break;
                            }
                        }

                        if(!$check){
                            $vars['error'] =  "Wrong uniq_id";
                        }

                        $apiServer->update($uniq_id, array(
							'domain' => 'whmcsdel-'.$params['domain']
                        ));

                        if($error = $apiServer->getError()){
                            $vars['error'] =  'Cannot restore: '.$error;
                        }else{
                            $parent = StormOnDemandPrivateParent_getOption('Parent', $params);
                            $result = $apiServer->cloneServer($_REQUEST['server_id'],$params['domain'],$params['password'],$parent);

                            if($error = $apiServer->getError()){
                                $vars['error'] =  $error;
                                $apiServer->update($uniq_id, array(
									'domain' => $params['domain']
                                ));
                            }else{
                                StormOnDemand_Helper::addUniqIdToCustomFields($params['serviceid'], $result['uniq_id']);

                                $apiServer->destroy($uniq_id);
                                if($error = $apiServer->getError()){
                                    $vars['error'] =  $error;
                                }else{
                                    $vars['info'] = 'Restoring from server. Please wait';
                                }
                            }
                        }
                        break;
                }
                break;
        }
    }


    //getting custom configurations
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
    $customConfig = StormOnDemand_Helper::getCustomConfigValues();

    $vars['custom_template'] = $customConfig['custom_template'];
    $vars['storm_links']    =  LiquidWebPrivateParent_ClientAreaCustomButtonArray();
    $vars['loadBootstrap']  =  LiquidWebPrivateParent_lookForBootstrap();
    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'restore.tpl';
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">IP Management</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}

function LiquidWebPrivateParent_History($params)
{
    //get configuration
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);

    //we need uniq_id to terminate server
    $uniq_id = $params['customfields']['uniq_id'];
    if(!$uniq_id)
    {
        return "Cannot find uniq_id for this service";
    }

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password, 'bleed');

    if(isset($_REQUEST['page']))
    $page = $_REQUEST['page'];
    else
    $page = 1;

    $ret = $server->history($uniq_id, 20, $page);

    $history = $ret['items'];
    $vars['history'] = $history;
    $vars['page'] = $page;
    $vars['page_total'] = $ret['page_total'];


    //getting custom configurations
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
    $customConfig = StormOnDemand_Helper::getCustomConfigValues();

    $vars['custom_template'] = $customConfig['custom_template'];
    $vars['storm_links']    =  LiquidWebPrivateParent_ClientAreaCustomButtonArray();
    $vars['loadBootstrap']  =  LiquidWebPrivateParent_lookForBootstrap();
    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'history.tpl';
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">IP Management</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}


function LiquidWebPrivateParent_BlockStorage($params){

    $vars		=   array(); //vars for template
    $username   =   LiquidWebPrivateParent_getOption('Username', $params);
    $password   =   LiquidWebPrivateParent_getOption('Password', $params);

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'class.ModuleInformationClient.php';
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandConnection.php';
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Storage.php';
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';

    $storageService = new StormOnDemand_Storage($username,$password);

    $user_id = null;
    try{
        $user_id = $params['clientsdetails']['userid'];
        if(!$user_id){
            throw new Exception;
        }
    }catch(Exception $e){
        return "User identificator doesn\'t exists";
    }

    $hosting_id = null;
    try{
        $hosting_id = (int) $params['serviceid'];
        if(!$hosting_id){
            throw new Exception;
        }
    }catch(Exception $e){
        return 'Hosting identificator is empty';
    }

    if(LiquidWebPrivateParent_validateArray($_GET, array(
    array(
			'field' => 'ajaxaction',
			'preg'	=> '/^[a-z]{1,10}$/D',
    ),
    array(
			'field' => 'uid',
			'preg'	=> '/^[a-zA-Z0-9]{1,10}$/D',
    ),
    array(
			'field' => 'id',
			'preg'  => '/^[0-9]{1,}$/D',
    ),
    ))){

        $response = array(
			'type' => 'success',
			'data' => array(),
        );

        $userSBSProductQuery = ModuleInformationClient::mysql_safequery('SELECT  hosting.id AS hosting_id,hosting.orderid as hosting_order_id, hosting.domain as hosting_domain,customvalues.value AS custom_uniq_id, hosting.domain as hosting_domain
																		 FROM tblhosting AS hosting
																		 JOIN tblcustomfields AS customfields 		ON 	hosting.packageid 	= customfields.relid
																		 JOIN tblcustomfieldsvalues as customvalues ON 	customfields.id 	= customvalues.fieldid
																		 											AND hosting.id  		= customvalues.relid
																		 WHERE hosting.userid = ? AND (customfields.fieldname = "uniq_id" OR customfields.fieldname = "uniq_id|Uniq ID") AND customvalues.value = ? LIMIT 1', array(
        $user_id,
        $_GET['uid'],
        ));
        $userSBSProduct = mysql_fetch_assoc($userSBSProductQuery);
        if(!$userSBSProduct){
            $response['type'] = 'error';
            $response['data'] = array(
				   	'error' => 'Wrong uniq_id',
            );
        }

        $soadUserProduct = array();
        $hostings = StormOnDemand_Helper::getAllLiquidWebHosting($user_id);
        if(!empty($hostings)){
            foreach($hostings as $hosting){
                if($hosting['hosting_id'] === $hosting_id){
                    $soadUserProduct = $hosting;
                }
            }
        }else{
            return 'Hosting uniq_key doesn\'t exists';
        }

        if(strcmp($_GET['ajaxaction'], 'attach') === 0){

            if($soadUserProduct){
                $apiRet = $storageService->attach($soadUserProduct['uniq_id'], $_GET['uid']);
                if($error = $storageService->getError()){
                    $response['type'] = 'error';
                    $response['data'] = array(
				   	'error' => 'Cannot attach volume',
                    );
                }else{
                    $response['data'] = array(
				   	'msg' => 'Volume attached',
                    );
                }
            }else{
                $response['type'] = 'error';
                $response['data'] = array(
				   	'error' => "Cannot find hosting",
                );
            }
        }else if(strcmp($_GET['ajaxaction'], 'detach') === 0){
            $apiRet = $storageService->detach($soadUserProduct['uniq_id'], $_GET['uid']);
            if($error = $storageService->getError()){
                $response['type'] = 'error';
                $response['data'] = array(
				   	'error' => "Cannot detach volume",
                );
            }else{
                $response['data'] = array(
				   	'msg' => 'Volume detached',
                );
            }
        }


        ob_clean();
        echo json_encode($response);
        die();
    }


    //get hosting uniq_id key
    $hostings = StormOnDemand_Helper::getAllLiquidWebHosting($user_id);
    if(!empty($hostings)){
        foreach($hostings as $hosting){
            if($hosting['hosting_id'] === $hosting_id){
                $hostingUniqKey = $hosting;
            }
        }
    }else{
        return 'Hosting uniq_key doesn\'t exists';
    }

    if(!$hostingUniqKey){
        return 'Hosting uniq_key doesn\'t exists';
    }

    //get all SBSModule products
    $sbsProductsIds = array();
    $sbsQuery       = ModuleInformationClient::mysql_safequery('SELECT id FROM tblproducts WHERE servertype = ?', array(StormOnDemand_Helper::LiquidWebSBSLiquidWebServerType));

    $sbsProducts    = array();

    while($row = mysql_fetch_assoc($sbsQuery)){
        $sbsProducts []= $row;
    }

    if($sbsProducts){
        foreach($sbsProducts as $val){
            $sbsProductsIds []= $val['id'];
        }
    }
    $op_pid = join(',',$sbsProductsIds);
    //get all user SBS products with uniq_id from order
    $sbsUserProducts 	 = array();
    $sbsUQuery           = ModuleInformationClient::mysql_safequery('SELECT hosting.id AS hosting_id,hosting.orderid as hosting_order_id, hosting.domain as hosting_domain,customvalues.value AS uniq_id, hosting.domain as hosting_domain, productinfo.name as productinfo_name
																	 FROM tblhosting AS hosting
																	 JOIN tblcustomfields AS customfields ON hosting.packageid = customfields.relid
																	 JOIN tblcustomfieldsvalues as customvalues ON 	customfields.id 	= customvalues.fieldid
																	 											AND hosting.id  		= customvalues.relid
																	 JOIN tblproducts AS productinfo ON hosting.packageid = productinfo.id
																	 WHERE 	   hosting.packageid IN('.(int)$op_pid.')
																	 	   AND hosting.userid = '.(int)$user_id.'
																	 	   AND (customfields.fieldname = "uniq_id" OR customfields.fieldname = "uniq_id|Uniq ID")');

    while($row = mysql_fetch_assoc($sbsUQuery)){
        $sbsUserProducts []= $row;
    }



    $sbsProductsDetails = array();
    if(!empty($sbsUserProducts)){
        foreach($sbsUserProducts as $sbs){

            if(!$sbs['uniq_id']){
                continue;
            }

            $_det = $storageService->details($sbs['uniq_id']);

            if($_det){
                $_det['system_config'] = $sbs;
                $sbsProductsDetails []= $_det;
            }
        }
    }


    $avSbs = array();
    if(!empty($sbsProductsDetails)){
        foreach($sbsProductsDetails as $sbs){
            if((int)$sbs['cross_attach']){
                if(!empty($sbs['attachedTo'])){

                    $assigned = false;
                    foreach($sbs['attachedTo'] as $sbsHosting){
                        if(strcmp($sbsHosting['resource'], $hostingUniqKey['uniq_id']) === 0){
                            $avSbs []= array(
								'status' => 'Assigned',
								'is_assigned' => 1,
								'sbs'	 => $sbs,
                            );

                            $assigned = true;
                            break;
                        }
                    }

                    if($assigned){
                        continue;
                    }else{
                        $avSbs []= array(
							'status' => 'Not Assigned',
							'is_assigned' => 0,
							'sbs'	 => $sbs,
                        );
                    }
                }else{
                    $avSbs []= array(
						'status' => 'Not Assigned',
						'is_assigned' => 0,
						'sbs'	 => $sbs,
                    );
                }
            }else{
                if(!empty($sbs['attachedTo'])){

                    $_hosting = array_shift($sbs['attachedTo']);
                    if(strcmp($_hosting['resource'], $hostingUniqKey['uniq_id']) === 0){
                        $sbs['attachedTo'] []= $_hosting;
                        $avSbs []= array(
							'status' => 'Assigned',
							'is_assigned' => 1,
							'sbs'	 => $sbs,
                        );
                    }

                }else{
                    $avSbs []= array(
						'status' => 'Not Assigned',
						'is_assigned' => 0,
						'sbs'	 => $sbs,
                    );
                }
            }
        }
    }

    //getting custom configurations
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
    $customConfig = StormOnDemand_Helper::getCustomConfigValues();

    $vars['custom_template']		 = $customConfig['custom_template'];
    $vars['sbs']		 = $avSbs;
    $vars['subpage']	 = dirname(__FILE__).DS.'clientarea'.DS.'blockstorage.tpl';
    $vars['request_uri'] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    if($aPos = strpos($vars['request_uri'], 'ajaxaction')){
        $vars['request_uri'] = substr($vars['request_uri'],0, $aPos - 6);
    }
    $vars['storm_links']    =  LiquidWebPrivateParent_ClientAreaCustomButtonArray();
    $vars['loadBootstrap']  =  LiquidWebPrivateParent_lookForBootstrap();
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">Block Storage</a>',
        'vars'          =>  $vars,
    );
    return $pagearray;
}


function LiquidWebPrivateParent_getOption($option, $params)
{
    if(isset($params['configoptions'][$option]))
    {
        return $params['configoptions'][$option];
    }

    //Product Configuration
    $product = new LiquidWebPrivateParentProduct($params['pid']);

    return $product->getConfig($option);
}

function LiquidWebPrivateParent_lookForBootstrap()
{
    if(! class_exists('RecursiveDirectoryIterator') or ! class_exists('RecursiveIteratorIterator'))
    {
        return false;
    }

    global $CONFIG;

    $template = $CONFIG['Template'];

    $directory = new RecursiveDirectoryIterator(ROOTDIR.DS.'templates'.DS.$template.DS);

    foreach(new RecursiveIteratorIterator($directory) as $file)
    {
        if(in_array($file->getFilename(), array('bootstrap.css', 'bootstrap.min.css')))
        {
            return false;
        }
    }

    return true;
}

/****************** MODULE INFORMATION ************************/
//Register instance
LiquidWebPrivateParent_registerInstance();

function LiquidWebPrivateParent_registerInstance()
{
    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   'Liquid Web Private Cloud For WHMCS';
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   LIQUID_WEB_PRIVATE_PARENT_VERSION;
    //Encryption key
    $moduleKey          =   'GxnatiRsnusw1OlOgac8F7hlW6ZTe6qQ8CxeLJrBZ0btcHeQG2Aq9sNdNtBrOzpR';

    /***************************************************
     *                      DO NOT TOUCH!
     ***************************************************/

    //Load API Class
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'class.ModuleInformationClient.php';

    //Create Client Class
    $client = new ModuleInformationClient($moduleName, $moduleKey);

    //Register current instance
    $ret = $client->registerModuleInstance($moduleVersion, $_SERVER['SERVER_ADDR'], $_SERVER['SERVER_NAME']);

    if($ret->status == 1)
    {
        ModuleInformationClient::setLocalVersion($moduleName, $moduleVersion);
    }
}

function LiquidWebPrivateParent_getLatestVersion()
{
    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   'Liquid Web Private Parent For WHMCS';
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   LIQUID_WEB_PRIVATE_PARENT_VERSION;
    //Encryption key
    $moduleKey          =   'GxnatiRsnusw1OlOgac8F7hlW6ZTe6qQ8CxeLJrBZ0btcHeQG2Aq9sNdNtBrOzpR';


    /***************************************************
     *                      DO NOT TOUCH!
     ***************************************************/

    //Load API Class
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'class.ModuleInformationClient.php';

    //Is Already Registered?
    $currentVersion = ModuleInformationClient::getLocalVersion($moduleName);
    if(!$currentVersion)
    {
        return false;
    }

    //Create Client Class
    $client = new ModuleInformationClient($moduleName, $moduleKey);

    //Get Information about latest version
    $res = $client->getLatestModuleVersion();

    if(!$res)
    {
        return false;
    }

    if($res->data->version == $moduleVersion)
    {
        return false;
    }

    return array
    (
        'version'   =>  $res->data->version,
        'site'      =>  $res->data->site,
    );
}



function LiquidWebPrivateParent_loadAsset($assetPath, $vars = array())
{
    $str = '';
    if(file_exists(dirname(__FILE__).DS.'assets'.DS.$assetPath)){
        $str = file_get_contents(dirname(__FILE__).DS.'assets'.DS.$assetPath);

        if(!empty($vars)){
            foreach($vars as $k => $v){

                if(is_array($v)){
                    $v = json_encode($v);
                }

                $str = str_replace('{$'.$k.'}', $v,$str);
            }
        }

    }
    return $str;
}

function LiquidWebPrivateParent_loadTemplates(){

    $dirToTemplates = dirname(__FILE__).DS.'assets'.DS.'templates';
    $templates 	    = array();

    if(is_dir($dirToTemplates)){
        if ($handle = opendir($dirToTemplates)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $xml = simplexml_load_file($dirToTemplates.DS.$entry);
                    $tpl = json_decode(json_encode($xml), TRUE);

                    if(isset($tpl['visible']) && (bool)$tpl['visible'] === true){
                        $templates []= $tpl;
                    }
                }
            }
            closedir($handle);
        }
        return $templates;
    }
    return array();
}


function LiquidWebPrivateParent_validateArray($array, $pregs){

    foreach($pregs as $k => $v){

        if(!isset($array[$v['field']])){
            return false;
        }

        if(!preg_match($v['preg'],$array[$v['field']])){
            return false;
        }
    }
    return true;
}
