<?php

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

define('LiquidWebCacheLive',1);
define('LiquidWebCacheName','liquidforwhmcs_quota_cache');
define('LiquidWebConfigName','liquidforwhmcs_config_cache');
define('LiquidWebDefaultZone',27);
define('LiquidWebFirewallSlashEscapeChar','_');
define('LiquidWebSBSServerType', 'LiquidWebSBS');
define('LiquidWebLiquidWebServerType', 'LiquidWeb');

if(file_exists(dirname(__FILE__).DS.'moduleVersion.php')){
    require_once dirname(__FILE__).DS.'moduleVersion.php';
     define('LIQUID_WEB_VERSION', $moduleVersion);
}else{
     define('LIQUID_WEB_VERSION', 'Development Version');
}


function LiquidWeb_checkConnection()
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

function LiquidWeb_ConfigOptions()
{
    //load server helper class
    require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'modulesgarden' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'class.StormOnDemand_Helper.php';

	if(isset($_REQUEST['id']) && $_REQUEST['id'] && preg_match('/^[0-9]{1,}$/D', $_REQUEST['id'])){

		$type 	     = 'product';
		$fieldType 	 = 'dropdown';
		$fieldName 	 = 'Clone From Server';
		$description = 'Select server to clone(optional)';
		$showOrder   = 'on';

		$idRel 			= (int) $_REQUEST['id'];
		$cfServerCloneQ = ModuleInformationClient::mysql_safequery('SELECT * FROM tblcustomfields WHERE relid = ? AND fieldname = ? LIMIT 1', array($idRel,$fieldName));
		$cfServerClone  = mysql_fetch_assoc($cfServerCloneQ);
		if(!$cfServerClone){
			$cfServerCloneQC = ModuleInformationClient::mysql_safequery('INSERT INTO tblcustomfields(type,relid,fieldname,fieldtype, showorder, description) VALUES(?,?,?,?,?,?)',
																		array($type,
																			  $idRel,
																			  $fieldName,
																			  $fieldType,
																			  $showOrder,
																			  $description));
		}
	}

    if($_REQUEST['stormajax'] == 'load-config')
    {
        ob_clean();
        $conf_id = $_REQUEST['conf_id'];

        $q = mysql_query("SELECT * FROM tblproducts WHERE id = ".(int)$_REQUEST['id']." LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $username   =   $row['configoption1'];
        $password   =   $row['configoption2'];
        //$password = StormOnDemand_Helper::encrypt_decrypt($row['configoption2']);

		$zoneSelected = (int) $row['configoption4'];
		if(!$zoneSelected){
			$zoneSelected = (int) LiquidWebDefaultZone;
		}

		if(isset($_REQUEST['zone']) && preg_match('/^[0-9]{1,}$/D', $_REQUEST['zone'])){
			$zoneSelected = (int) intval($_REQUEST['zone']);
		}

	    $confs   = array();
		$baCache = ModuleInformationClient::getWHMCSconfig(LiquidWebConfigName);
		if($baCache){
			$baCache = json_decode($baCache,true);
		}

		if(!$baCache || !isset($baCache['cache_time']) || (((int)time() - (int)$baCache['cache_time']) > LiquidWebCacheLive)){

			$baCache = array(
				'cache_time' => time(),
				'data'	     => array(),
			);

	        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormConfig.php';
	        $config = new StormOnDemandStormConfig($username, $password);

	        $page = 1;
	        $count = $config->lists('all', $page, 1);

	        if (isset($count['item_total']) && $count['item_total'] > 0)
                {
                    $ret = $config->lists('all', $page, $count['item_total']);

                    if ($error = $config->getError()) {
                        echo '<p style="color: red">' . $error . '</p>';
                        die();
                    }

                    $configs = $ret['items'];

                    foreach ($configs as &$c) {
                        if (!$c['available']) {
                            continue;
                        }

                        $confs[$c['category']][] = $c;
                    }

                }

		$baCache['data'] = $confs;
		}
                else
                {
			$confs = $baCache['data'];
		}



        echo '<div id="storm-config-tabs">';
        echo '<ul>
                <!-- <li><a href="#storm-config-tab-storm">Cloud Servers</a></li> -->
                <li><a href="#storm-config-tab-ssd">SSD Servers</a></li>
                <li><a href="#storm-config-tab-bare-metal">Bare Metal Servers</a></li>
              </ul>';

/*              
        //Storm Servers
        echo '<div id="storm-config-tab-storm">';
        echo '<table class="datatable" style="width: 100%">
                <tr>
                    <th>Server Type</th>
                    <th style="width: 80px">VCPU</th>
                    <th style="width: 80px">Disk</th>
                    <th style="width: 80px">Memory</th>
                </tr>
              ';
        foreach($confs['storm'] as &$item)
        {

			if(!isset($item['zone_availability']) 				 ||
			   empty($item['zone_availability']) 				 ||
			   !isset($item['zone_availability'][$zoneSelected]) ||
			   !$item['zone_availability'][$zoneSelected]){
			    	continue;
			    }

            echo '<tr style="border-top: 1px solid  #efefef">
                    <td><input class="storm-config" type="radio" name="config-id" value="'.$item['id'].'" '.($item['id'] == $conf_id ? 'checked="checked"' : '').'/> '.$item['description'].'</td>
                    <td>'.$item['vcpu'].' CPUs</td>
                    <td>'.$item['disk'].'</td>
                    <td>'.$item['memory'].'</td>
                  </tr>';
        }
        echo '</table>';
        echo '</div>';
*/
        //SSD Servers
        echo '<div id="storm-config-tab-ssd">';
        echo '<table class="datatable" style="width: 100%">
                <tr>
                    <th>Server Type</th>
                    <th style="width: 80px">VCPU</th>
                    <th style="width: 80px">Disk</th>
                    <th style="width: 80px">Memory</th>
                </tr>
              ';
        foreach($confs['ssd'] as &$item)
        {
			if(!isset($item['zone_availability']) 				 ||
			   empty($item['zone_availability']) 				 ||
			   !isset($item['zone_availability'][$zoneSelected]) ||
			   !$item['zone_availability'][$zoneSelected]){
			    	continue;
			    }

            echo '<tr style="border-top: 1px solid  #efefef">
                    <td><input class="storm-config" type="radio" name="config-id" value="'.$item['id'].'" '.($item['id'] == $conf_id ? 'checked="checked"' : '').'/> '.$item['description'].'</td>
                    <td>'.$item['vcpu'].' CPUs</td>
                    <td>'.$item['disk'].'</td>
                    <td>'.$item['memory'].'</td>
                  </tr>';
        }
        echo '</table>';
        echo '</div>';

        //Bare-Metal
        echo '<div id="storm-config-tab-bare-metal">';
        echo '<table class="datatable" style="width: 100%">
                <tr>
                    <th>Server Type</th>
                    <th style="width: 50px">Speed</th>
                    <th style="width: 40px">CPUs</th>
                    <th style="width: 45px">Cores</th>
                    <th style="width: 40px">RAM</th>
                    <th style="width: 45px">Disks</th>
                    <th style="width: 40px">Size</th>
                    <th style="width: 40px">Type</th>
                    <th style="width: 50px">RAID</th>
                </tr>
              ';
        foreach($confs['bare-metal'] as &$item)
        {

			if(!isset($item['zone_availability']) 				 ||
			   empty($item['zone_availability']) 				 ||
			   !isset($item['zone_availability'][$zoneSelected]) ||
			   !$item['zone_availability'][$zoneSelected]){
			    	continue;
			    }

            echo '<tr style="border-top: 1px solid  #efefef">
                    <td><input class="storm-config" type="radio" name="config-id" value="'.$item['id'].'" '.($item['id'] == $conf_id ? 'checked="checked"' : '').'/> '.$item['description'].'</td>
                    <td>'.$item['cpu_speed'].'</td>
                    <td>'.$item['cpu_count'].'</td>
                    <td>'.$item['cpu_cores'].'</td>
                    <td>'.$item['ram_total'].'</td>
                    <td>'.$item['disk_count'].'</td>
                    <td>'.$item['disk_total'].'</td>
                    <td>'.$item['disk_type'].'</td>
                    <td>'.($item['raid_level'] == -1 ? '(none)' : 'RAID'.$item['raid_level']).'</td>
                  </tr>';
        }
        echo '</table>';
        echo '</div>';

        echo '</div>'; //close tabs

        echo '<script type="text/javascript">
                $(function(){
                    $("#storm-config-tabs").tabs();
                    $(".storm-config").click(function(event){
                        event.preventDefault();

                        val = $(this).parent().find("input[name=\'config-id\']").val();
                        $("#load-storm-config").parent().find("input").val(val).change();
                        $("#conf-dialog").dialog("destroy");
                        //$("#conf-dialog").hide();
                    });
                });
              </script>';
        die();
    }
    elseif($_REQUEST['stormajax'] == 'load-template')
    {
        ob_clean();
        $conf_id = $_REQUEST['conf_id'];

        $q = mysql_query("SELECT * FROM tblproducts WHERE id = ".(int)$_REQUEST['id']." LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $username   =   $row['configoption1'];
        $password   =   $row['configoption2'];
        //$password = StormOnDemand_Helper::encrypt_decrypt($row['configoption2']);

		$zoneSelected = (int) $row['configoption4'];
		if(!$zoneSelected){
			$zoneSelected = (int) LiquidWebDefaultZone;
		}

		if(isset($_REQUEST['zone']) && preg_match('/^[0-9]{1,}$/D', $_REQUEST['zone'])){
			$zoneSelected = (int) intval($_REQUEST['zone']);
		}

        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormTemplate.php';
        $template = new StormOnDemandStormTemplate($username, $password);
        $ret = $template->lists();

        if($error = $template->getError())
        {
            echo '<p style="color: red">'.$error.'</p>';
            die();
        }

        echo '<table class="datatable" style="width: 100%">
                <tr>
                    <th>Template</th>
                </tr>
              ';
        foreach($ret['items'] as $item)
        {
            if($item['deprecated'] == 1)
            {
                continue;
            }

			if(!isset($item['zone_availability']) 				 ||
			   empty($item['zone_availability']) 				 ||
			   !isset($item['zone_availability'][$zoneSelected]) ||
			   !$item['zone_availability'][$zoneSelected]){
			    	continue;
			    }

            echo '<tr>
                    <td><input class="storm-template" type="radio" name="template-id" value="'.$item['name'].'" '.($item['name'] == $conf_id ? 'checked="checked"' : '').'/>'.$item['description'].'</td>
                  </tr>';
        }
        echo '</table>';
        echo '<script type="text/javascript">
                $(function(){
                    $(".storm-template").click(function(event){
                        event.preventDefault();

                        val = $(this).parent().find("input[name=\'template-id\']").val();
                        $("#load-storm-template").parent().find("input").val(val).change();
                        $("#conf-dialog").dialog("destroy");
                        $("#load-storm-image").prev().val("");
                    });
                });
              </script>';
        die();
    }
    elseif($_REQUEST['stormajax'] == 'load-image')
    {
        ob_clean();
        $conf_id = $_REQUEST['conf_id'];
        $q = mysql_query("SELECT * FROM tblproducts WHERE id = ".(int)$_REQUEST['id']." LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $username   =   $row['configoption1'];
        $password   =   $row['configoption2'];
        //$password = StormOnDemand_Helper::encrypt_decrypt($row['configoption2']);

        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormImage.php';
        $image = new StormOnDemandStormImage($username, $password);
        $ret = $image->lists();

        if($error = $image->getError())
        {
            echo '<p style="color: red">'.$error.'</p>';
            die();
        }

        echo '<table class="datatable" style="width: 100%">
                <tr>
                    <th>Template</th>
                    <th>Source Hostname</th>
                    <th>Time take</th>
                </tr>
              ';
        foreach($ret['items'] as $item)
        {
            if($item['deprecated'] == 1)
            {
                continue;
            }
            echo '<tr>
                    <td><input class="storm-image" type="radio" name="image-id" value="'.$item['id'].'" '.($item['name'] == $conf_id ? 'checked="checked"' : '').' />'.$item['template_description'].'</td>
                    <td>'.$item['source_hostname'].'</td>
                    <td>'.$item['time_taken'].'</td>
                  </tr>';
        }
        echo '</table>';
        echo '<script type="text/javascript">
                $(function(){
                    $(".storm-image").click(function(event){
                        event.preventDefault();

                        val = $(this).parent().find("input[name=\'image-id\']").val();
                        $("#load-storm-image").parent().find("input").val(val);
                        $("#conf-dialog").dialog("destroy");
                        $("#load-storm-template").prev().val("");
                    });
                });
              </script>';
        die();
    }
    elseif($_REQUEST['stormajax'] == 'load-zone')
    {
        ob_clean();
        $conf_id = $_REQUEST['conf_id'];

        $q = mysql_query("SELECT * FROM tblproducts WHERE id = ".(int)$_REQUEST['id']." LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $username   =   $row['configoption1'];
        $password   =   $row['configoption2'];
        //$password = StormOnDemand_Helper::encrypt_decrypt($row['configoption2']);

        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkZone.php';
        $zone = new StormOnDemandNetworkZone($username, $password);

        $ret = $zone->lists();

        if($error = $zone->getError())
        {
            echo '<p style="color: red">'.$error.'</p>';
            die();
        }

        echo '<table class="datatable" style="width: 100%">
                <tr>
                    <th>Name</th>
                    <th>Region</th>
                </tr>
              ';
        if($conf_id == null){
            $conf_id = 27; //Zone C
        }
        foreach($ret['items'] as $item)
        {

			$zoneName = $item['name'];
			if(isset($item['region']['name'])){
				$zoneName .=' ('.$item['region']['name'].')';
			}

            echo '<tr>
                    <td><input class="storm-zone" type="radio" name="zone-id" data-zone-name="'.$zoneName.'" value="'.$item['id'].'" '.($item['id'] == $conf_id ? 'checked="checked"' : '').' />'.$item['name'].'</td>
                    <td>'.$item['region']['name'].'</td>
                  </tr>';
        }
        echo '</table>';
        echo '<script type="text/javascript">
                $(function(){
                    $(".storm-zone").click(function(event){
                        event.preventDefault();

                        val = $(this).parent().find("input[name=\'zone-id\']").val();
						name = $(this).parent().find("input[name=\'zone-id\']").attr("data-zone-name");

                        $("#load-storm-zone").parent().find("input").val(val).change();
						$("#load-storm-zone").parent().find("#zone_name").html(name);
                        $("#conf-dialog").dialog("destroy");
                    });
                });
              </script>';
        die();

    }
    elseif($_REQUEST['stormajax'] == 'generate-confoption')
    {
        $configurable_options = array();

        ob_clean();
        $q = mysql_query('SELECT * FROM tblproductconfiglinks WHERE pid = '. (int)$_REQUEST['id']);
        if(mysql_num_rows($q))
        {
            echo '<p style="color: red">Configurable options for this product already exists. Cannot generate</p>';
            die();
        }

        $q = mysql_query("SELECT * FROM tblproducts WHERE id = ".(int)$_REQUEST['id']." LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $username   =   $row['configoption1'];
        $password   =   $row['configoption2'];
        //$password = StormOnDemand_Helper::encrypt_decrypt($row['configoption2']);

        //Templates
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormTemplate.php';
        $template = new StormOnDemandStormTemplate($username, $password);

        //Configs
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormConfig.php';
        $config = new StormOnDemandStormConfig($username, $password);

        //Images
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormImage.php';
        $image = new StormOnDemandStormImage($username, $password);

        //Server
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
        $server = new StormOnDemandStormServer($username, $password);

        //Zones
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkZone.php';
        $zone = new StormOnDemandNetworkZone($username, $password);

        //Templates
        $configurable_options[0] = array
        (
            'Name'      =>  'Template|VM Template',
            'Type'      =>  'select',
            'Values'    =>  array()
        );

        $ret = $template->lists();
        if($error = $template->getError())
        {
            echo '<p style="color: red">'.$error.'</p>';
            die();
        }

        foreach($ret['items'] as $item)
        {
            if($item['deprecated'] == 1)
            {
                continue;
            }
            $configurable_options[0]['Values'][$item['name']] = $item['description'];
        }

        //Configs
        $configurable_options[1] = array
        (
            'Name'      =>  'Config|VM Config',
            'Type'      =>  'select',
            'Values'    =>  array()
        );

        $page = 1;
        $count = $config->lists('all', $page, 1);

        if (isset($count['item_total']) && $count['item_total'] > 0)
        {
            $ret = $config->lists('all', $page, $count['item_total']);

            $configs = $ret['items'];

            foreach ($configs as $config) {
                if (!$config['available']) {
                    continue;
                }

                $configurable_options[1]['Values'][$config['id']] = $config['description'];
            }
        }

        //Images
        $configurable_options[2] = array
        (
            'Name'      =>  'Images|VM image',
            'Type'      =>  'select',
            'Values'    =>  array()
        );
        $ret = $image->lists();
        foreach($ret['items'] as $item)
        {
            if($item['deprecated'] == 1)
            {
                continue;
            }

            $configurable_options[2]['Values'][$item['id']] = $item['template_description'];
        }

        //Zones
        $configurable_options[3] = array
        (
            'Name'      =>  'Zone|Zone',
            'Type'      =>  'select',
            'Values'    =>  array()
        );

        $ret = $zone->lists();
        foreach($ret['items'] as $item)
        {
            $configurable_options[3]['Values'][$item['id']] = $item['region']['name'].' - '.$item['name'];
        }

        //Backup
        $configurable_options[] = array
        (
            'Name'      =>  'Backup Enabled|Backup Enabled',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                1       =>  'Yes',
                0       =>  'No'
            )
        );

        //Backup Plan
        $configurable_options[] = array
        (
            'Name'      =>  'Backup Plan|Backup Plan',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                'quota' =>  'Quota',
                'daily' =>  'Daily'
            )
        );

        //Backup Quota
        $configurable_options[] = array
        (
            'Name'      =>  'Backup Quota|Backup Quota',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                '100'    =>  '100GB',
                '200'    =>  '200GB',
                '500'    =>  '500GB'
            )
        );

        //Daily Backup Quota
        $configurable_options[] = array
        (
            'Name'      =>  'Daily Backup Quota|Daily Backup Quota',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                '1'    =>  '1',
                '2'    =>  '2',
                '3'    =>  '3',
                '4'    =>  '4',
                '5'    =>  '5'
            )
        );

        //Number of IPs
        $configurable_options[] = array
        (
            'Name'      =>  'Number of IP Addresses|Number of IP Addresses',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                '1'     =>  '1',
                '2'     =>  '2',
                '3'     =>  '3'
            )
        );

        //Maximum IP Addresses
        $configurable_options[] = array
        (
            'Name'      =>  'Maximum IP Addresses|Maximum IP Addresses',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                '8'     =>  '8',
                '9'     =>  '9',
                '10'    =>  '10'
            )
        );

        //Bandwidth Quota
        $configurable_options[] = array
        (
            'Name'      =>  'Bandwidth Quota|Bandwidth Quota',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                5000    =>  5000,
                6000    =>  6000,
                8000    =>  8000,
                10000   =>  10000,
                15000   =>  15000,
                20000   =>  20000,
            )
        );

        //Monitoring
        $configurable_options[] = array
        (
            'Name'      =>  'Monitoring|Monitoring',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                1       =>  'Yes',
                0       =>  'No'
            )
        );

        //Firewall
        $configurable_options[] = array
        (
            'Name'      =>  'Firewall|Firewall',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                1       =>  'Yes',
                0       =>  'No'
            )
        );

        //IPs Management
        $configurable_options[] = array
        (
            'Name'      =>  'IPs Management|IPs Management',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                1       =>  'Yes',
                0       =>  'No'
            )
        );


        //Create groups
        $groups =   array();
        $groups[] = array
        (
            'Name'          =>  'Configurable Options For Liquid Web Servers',
            'Description'   =>  'Auto Generated by Module',
            'Fields'        =>  $configurable_options
        );

        $group_id = '';
        foreach($groups as $group)
        {
            //Add Group
            mysql_query('INSERT INTO tblproductconfiggroups(name,description) VALUES("'.$group['Name'].'","'.$group['Description'].'")');

            $group_id = mysql_insert_id();
            //Connect to product
            mysql_query('INSERT INTO tblproductconfiglinks(gid,pid) VALUES('.(int)$group_id.', '.(int)$_REQUEST['id'].')');

            //Add fields
            foreach($group['Fields'] as $field)
            {
                $type    = 0;
                switch($field['Type'])
                {
                    case 'select':
                        $type    =   1;
                        break;
                }

                mysql_query("INSERT INTO tblproductconfigoptions(gid,optionname,optiontype,qtyminimum,qtymaximum,`order`,hidden) VALUES(".(int)$group_id.", '".$field['Name']."', ".$type.",0,0,0,0)");
                $option_id = mysql_insert_id();

                foreach($field['Values'] as $option_value   =>  $option_name)
                {
                    mysql_query("INSERT INTO tblproductconfigoptionssub(configid,optionname,sortorder,hidden) VALUES(".(int)$option_id.", '".$option_value.'|'.$option_name."',0,0)");
                    mysql_query("INSERT INTO `tblpricing` ( `type` , `currency` , `relid` , `msetupfee` , `qsetupfee` , `ssetupfee` , `asetupfee` , `bsetupfee` , `tsetupfee` , `monthly` , `quarterly` , `semiannually` , `annually` , `biennially` , `triennially`)
                                VALUES ('configoptions',1,".mysql_insert_id().",'0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00')");
                }
            }
        }

        echo '<p style="color: green">Default Configurable options generated!</p>
              <script type="text/javascript">
                setTimeout(function(){
                    window.location = "configproductoptions.php?action=managegroup&id='.$group_id.'";
                }, 1000);
              </script>';
        die();
    }
    elseif($_REQUEST['stormajax'] == 'load-quota')
    {
    	/*
		 * get bandwidth and backup quota
		 */
    	ob_clean();

		$response = array(
			'type' => 'success',
			'data' => array(),
		);

		if(empty($_REQUEST) || !isset($_REQUEST['id']) || !preg_match('/^[0-9]{1,}$/D', $_REQUEST['id'])){
			$response['type'] = 'error';
		}else{

	        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'class.ModuleInformationClient.php';

			$idProduct    = (int) intval($_REQUEST['id']);
	        $productQuery = ModuleInformationClient::mysql_safequery('SELECT * FROM tblproducts WHERE id = ? LIMIT 1', array($idProduct));
	        $productRow   = mysql_fetch_assoc($productQuery);

	        $username     = $productRow['configoption1'];
	        $password     = $productRow['configoption2'];

			$baCache = ModuleInformationClient::getWHMCSconfig(LiquidWebCacheName);

			if($baCache){
				$baCache = json_decode($baCache,true);
			}

			if(!$baCache || !isset($baCache['cache_time']) || (((int)time() - (int)$baCache['cache_time']) > LiquidWebCacheLive)){
				//Load data from api
				//Save cache in database
				$baCache = array(
					'cache_time' => time(),
					'data'	     => array(
						'bandwidth_quota' => array(),
						'backup_quota'	  => array(),
					),
				);

				require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandProduct.php';

				$product 	  = new StormOnDemandProduct($username,$password);
				$productLists = $product->details(null,'SS.VPS');
				/* Get backup quota */
				if($productLists && !empty($productLists['options'])){
					foreach($productLists['options'] as $kOpt => $vOpt){
						if(strcmp($vOpt['key'],'LiquidWebBackupPlan') === 0){

							if(!empty($vOpt['values'])){
								foreach($vOpt['values'] as $kVal => $vVal){

									if(strcmp($vVal['description'],'Quota-based Backups') === 0){
										if(!empty($vVal['options'])){
											foreach($vVal['options'] as $vkOpt => $vkVal){
												 if(strcmp($vkVal['key'], 'BackupQuota') === 0){
													if(!empty($vkVal['values'])){
												 	  foreach($vkVal['values'] as $kBack => $vBack){
															$baCache['data']['backup_quota'][(int)$vBack['display_order']] = array(
																'description' => $vBack['description'],
																'value'		  => $vBack['value'],
															);
												 	  }
													}
													break;
												 }
											}
										}
										break;
									}
								}
							}
							break;
						}
					}
				}

				/* Get bandwidth options */
				if($productLists && !empty($productLists['options'])){
					foreach($productLists['options'] as $kOpt => $vOpt){
						if(strcmp($vOpt['key'],'Bandwidth') === 0){

							if(!empty($vOpt['values'])){
								foreach($vOpt['values'] as $kVal => $vVal){

									$limit = (int) str_replace('SS.','', $vVal['value']);

									$baCache['data']['bandwidth_quota'][$limit] = array(
										'description' => $limit.' GB',
										'value'		  => $limit,
									);
								}
							}

							break;
						}
					}
				}

				ModuleInformationClient::saveWHMCSconfig(LiquidWebCacheName,json_encode($baCache));
			}
			$response['data'] = $baCache;
		}

    	echo json_encode($response);
    	die();
    }

    //Create table. We need it!
    mysql_query("CREATE TABLE IF NOT EXISTS `mg_liquid_web`
        (
            `hosting_id` INT NOT NULL,
            `uniq_id`    CHAR(6),
            UNIQUE KEY(`hosting_id`)
        ) ENGINE = MyISAM") or die(mysql_error());

    //Base config
    $config = getConfigFields();

	//input keys
	//id name form input =  packageconfigoption[array_search($inputName,$configFormKeys) + 1]
	$configFormKeys = array_keys($config);

	$productId    = (isset($_REQUEST['id']))?(int) intval($_REQUEST['id']):0;
	$jsTplParams  = array(  'id'                        => $productId,
                                'backup_quota'              => 0,
				'bandwidth_quota'           => 0,
				'zone_name'                 => 'Zone C (US Central)',
				'templates'                 => array(),
                                'inpt_bandwidth_quota_name' => 'packageconfigoption['.(array_search('Bandwidth Quota',$configFormKeys)+1).']',
                                'inpt_backup_quota_name'    => 'packageconfigoption['.(array_search('Backup Quota',$configFormKeys)+1).']',
                                'inpt_zone_name'            => 'packageconfigoption['.(array_search('Zone',$configFormKeys)+1).']',
                                'input_zone_default_id'     => LiquidWebDefaultZone,
                              );

	$productQuery = ModuleInformationClient::mysql_safequery('SELECT * FROM tblproducts WHERE id = ? LIMIT 1', array($productId));

	$productRow   = mysql_fetch_assoc($productQuery);

	$bqName   = 'configoption'.(array_search('Backup Quota',$configFormKeys)+1);
	$bndqName = 'configoption'.(array_search('Bandwidth Quota',$configFormKeys)+1);
	$zoneName = 'configoption'.(array_search('Zone',$configFormKeys)+1);

	$jsTplParams['backup_quota'] 	= ((int) $productRow[$bqName])?$productRow[$bqName]:100;
	$jsTplParams['bandwidth_quota'] = ($productRow[$bndqName])?$productRow[$bndqName]:5000;

	$zoneResults = array();

	//get full zone name
	if($productRow[$zoneName]){
    	//if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'edit')) {
    	    //only call from products edit page

    		require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkZone.php';

    		$zone 		 = new StormOnDemandNetworkZone($productRow['configoption1'], $productRow['configoption2']);
    		$zoneResults = $zone->lists();

    		if($zoneResults){
    			$productRow[$zoneName] = (int) $productRow[$zoneName];
    			if(isset($zoneResults['items']) && !empty($zoneResults['items'])){
    				foreach($zoneResults['items'] as $k => $v){
    					if((int)$v['id'] === $productRow[$zoneName]){

    						$zoneName = $v['name'];
    						if(isset($v['region']['name'])){
    							$zoneName .=' ('.$v['region']['name'].')';
    						}

    						$jsTplParams['zone_name'] = $zoneName;
    						break;
    					}
    				}
    			}
    		}
    	//}
    }

   $config['Zone']['Description'] = '<style type="text/css">input[name="packageconfigoption[4]"]{display:none;}</style><span id="zone_name">'.$jsTplParams['zone_name'].'</span>&nbsp;&nbsp;<a id="load-storm-zone" href="stormajax=load-zone" class="load-configuration">Load Zone</a>';

   $templates = LiquidWeb_loadTemplates();

	$xmlTemplate =  array(

			'zone'	=> array(
				'type' => 'text',
				'name' => 'packageconfigoption['.(array_search('Zone',$configFormKeys)+1).']',
			),

			'zone_name' => array(
				'type' => 'html',
				'id'   => '#zone_name',
			),

			'backup_enabled' => array(
				'type' => 'checkbox',
				'name' => 'packageconfigoption['.(array_search('Backup Enabled',$configFormKeys)+1).']',
			),

			'backup_plan' => array(
				'type' => 'select',
				'name' => 'packageconfigoption['.(array_search('Backup Plan',$configFormKeys)+1).']',
			),

			'backup_quota' => array(
				'type' => 'select',
				'name' => 'packageconfigoption['.(array_search('Backup Quota',$configFormKeys)+1).']',
			),

			'bandwidth_quota' => array(
				'type'	=> 'select',
				'name'	=> 'packageconfigoption['.(array_search('Bandwidth Quota',$configFormKeys)+1).']',
			),

			'ips_number' => array(
				'type'	=> 'text',
				'name'  => 'packageconfigoption['.(array_search('Number of IPs',$configFormKeys)+1).']',
			),

			'maximum_ips_number' => array(
				'type'  => 'text',
				'name'	=> 'packageconfigoption['.(array_search('Maximum IP Addresses',$configFormKeys)+1).']',
			),

			'monitoring' => array(
				'type'  => 'checkbox',
				'name'	=> 'packageconfigoption['.(array_search('Monitoring',$configFormKeys)+1).']',
			),

			'ips_management' => array(
				'type'	=> 'checkbox',
				'name'	=> 'packageconfigoption['.(array_search('IPs Management',$configFormKeys)+1).']',
			),

			'firewall' => array(
				'type'  => 'checkbox',
				'name'	=> 'packageconfigoption['.(array_search('Firewall',$configFormKeys)+1).']',
			),

			'config' => array(
				'type'  => 'text',
				'name'	=> 'packageconfigoption['.(array_search('Config',$configFormKeys)+1).']',
			),

			'template' => array(
				'type'  => 'text',
				'name'	=> 'packageconfigoption['.(array_search('Template',$configFormKeys)+1).']',
			),

			'username' => array(
				'type'  => 'text',
				'name'	=> 'packageconfigoption['.(array_search('Username',$configFormKeys)+1).']',
				'reset' => false,
			),

			'password' => array(
				'type'  => 'text',
				'name'	=> 'packageconfigoption['.(array_search('Password',$configFormKeys)+1).']',
				'reset' => false,
			),

			'image' => array(
				'type'  => 'text',
				'name'	=> 'packageconfigoption['.(array_search('Image',$configFormKeys)+1).']',
			),
	);

	if(!empty($templates)){

		if(empty($zoneResults)){

        	//if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'edit')) {
        	    //only call from products edit page

    			require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkZone.php';
    			$zone 		 = new StormOnDemandNetworkZone($productRow['configoption1'], $productRow['configoption2']);
    			$zoneResults = $zone->lists();
        	//}
		}

		if(isset($zoneResults['items']) && !empty($zoneResults['items'])){
			foreach($templates as $k => $v){
				if(isset($v['zone']) && $v['zone']){

					$v['zone'] = (int) $v['zone'];

					//search zone
					foreach($zoneResults['items'] as $kZone => $vZone){
						if((int)$vZone['id'] === $v['zone']){

							$templates[$k]['zone_name'] = $vZone['name'];
							if(isset($vZone['region']['name']) && $vZone['region']['name']){
								$templates[$k]['zone_name'] .= ' ('.$vZone['region']['name'].')';
							}
							break;
						}
					}
				}
			}
		}
	}

	$jsTplParams['templates'] = array(
		'templates_info' => $xmlTemplate,
		'templates'		 => $templates,
	);
	//script for product configure fields
	$config['Backup Quota']['Description'] = "<script type='text/javascript'>".LiquidWeb_loadAsset('js/ProductConfigure.tpl.js', $jsTplParams)."</script>";

    if(basename($_SERVER["SCRIPT_NAME"]) == 'configproducts.php')
    {
        $lcConfig = array(
	   		'id' 				=> $_REQUEST['id'],
	   		'config_to_send' 	=> array(),
	    );

		if(!empty($xmlTemplate)){
			foreach($xmlTemplate as $k => $v){
				$lcConfig['config_to_send'] []= array(
					'name' 		=> $v['name'],
					'var_name'  => $k,
				);
			}
		}

		$config['Backup Quota']['Description'] .= "<script type='text/javascript'>".LiquidWeb_loadAsset('js/LoadConfiguration.tpl.js', $lcConfig).'</script><div id="conf-dialog" style="display:none;" title=""></div>';
    }

    /*$newVersion = LiquidWeb_getLatestVersion();
    $script = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR) + 1);

    if($newVersion && $script == 'configproducts.php' && $_GET['action'] != 'save')
    {
        echo '<p style="text-align: center;" class="infobox op_version">
            <span style="font-weight: bold">New version of Liquid Web module is available!</span>
            <span style="font-weight: bold"><br />Check this address to find out more <a target="_blank" href="'.$newVersion['site'].'">'.$newVersion['site'].'</a></span>
         </p>';
    }*/

    if(basename($_SERVER["SCRIPT_NAME"]) == 'configproducts.php'){
        $testConnection = LiquidWeb_checkConnection();
    }else{
        $testConnection = true;
    }

    if($testConnection) {
      foreach ($config as $key => $value) {
          if($key == 'Error') {
              unset($config[$key]);
          }
      }
      return $config;
    } else {
      foreach ($config as $key => $value) {
          if($key != 'Username' && $key != 'Password' && $key != 'Error') {
              unset($config[$key]);
          }
      }
      return $config;
    }
}

function LiquidWeb_CreateAccount($params)
{

    //get configuration
    $username           =   LiquidWeb_getOption('Username', $params);
    $password           =   LiquidWeb_getOption('Password', $params);
    $config             =   LiquidWeb_getOption('Config', $params);
    $template           =   LiquidWeb_getOption('Template', $params);
    $bandwidth_quota    =   LiquidWeb_getOption('Bandwidth Quota', $params);
    $hostname           =   $params['customfields']['hostname'] ? $params['customfields']['hostname'] : $params['domain'];

    if ($hostname == '') {
        $hostname = $params['customfields']['Create my VPS with following host name'];
    }

    $q = mysql_query("SELECT tblproducts.* FROM tblhosting LEFT JOIN tblproducts ON tblhosting.packageid = tblproducts.id WHERE tblhosting.id = " . (int)$params['serviceid'] . " LIMIT 1");
    $row = mysql_fetch_assoc($q);
    if($config == null){
     $config = $row['configoption7'];
    }

    if($template == null){
      $template = $row['configoption5'];
    }

    if($bandwidth_quota == null){
      $bandwidth_quota = $row['configoption13'];
    }


	if(isset($params['customfields']['Clone From Server']) && $params['customfields']['Clone From Server']){

	    //load server class
	    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
	    $server = new StormOnDemandStormServer($username, $password);

		//getting parent hosting
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

		$ret = $server->cloneServer($params['customfields']['Clone From Server'],$hostname,$params['password'] );

	    //has error?
	    if($error = $server->getError()){
	        return $error;
	    }

	    //save uniq_id to database. We need it!
	    mysql_query("REPLACE INTO mg_liquid_web (`hosting_id`, `uniq_id`) VALUES ('".$params['serviceid']."', '".$ret['uniq_id']."')") or die(mysql_error());
	}else{
	    //check bandwidth quota
	    $configuration = LiquidWeb_ConfigOptions();

	    //load server class
	    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
	    $server = new StormOnDemandStormServer($username, $password);

	    $configuration = array
	    (
	        'ip_count'          =>  (LiquidWeb_getOption("Number of IPs", $params) == null)? $row['configoption11']:LiquidWeb_getOption("Number of IPs", $params),
	        'image_id'          =>  (LiquidWeb_getOption('Image', $params) == null)? $row['configoption6']:LiquidWeb_getOption('Image', $params),
	        'bandwidth_quota'   =>  $bandwidth_quota ,
	        'zone'              =>  (LiquidWeb_getOption('Zone', $params) == null)? $row['configoption4']:LiquidWeb_getOption('Zone', $params)
	    );

	    if(LiquidWeb_getOption('Backup Enabled', $params) != null)
	    {
	        $configuration['backup_enabled']    =  1;
	        $configuration['backup_quota']      =  LiquidWeb_getOption('Backup Plan', $params) == 'quota' ? LiquidWeb_getOption('Backup Quota', $params) : LiquidWeb_getOption('Daily Backup Quota', $params);
	        $configuration['backup_plan']       =  LiquidWeb_getOption('Backup Plan', $params);
	    }else{
          if($row['configoption8'] == 'on' ){
            $configuration['backup_enabled'] = 1;
            /*NTODO:było: StormOnDemand_getOption('Backup Quota', $params);*/
            $configuration['backup_quota']   = $row['configoption10'];
            $configuration['backup_plan']    = $row['configoption9'];
          }

      }

	    //create server with base configuration
	    $ret = $server->create($hostname, $params['password'], $config, $template, $configuration);

	    //has error?
	    if($error = $server->getError())
	    {
	        return $error;
	    } else {
	        for ($i = 0; $i < 5; $i++) {
                $dtl = $server->details($ret['uniq_id']);
                if ($dtl['ip'] == '127.0.0.1') {
                    usleep(30000000);// 30 seconds
                } else {
                    require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'modulesgarden' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'class.StormOnDemand_Helper.php';
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
	    //save uniq_id to database. We need it!
	    mysql_query("REPLACE INTO mg_liquid_web (`hosting_id`, `uniq_id`) VALUES ('".$params['serviceid']."', '".$ret['uniq_id']."')") or die(mysql_error());
	    //return successful message
	}

    return "success";
}

function LiquidWeb_TerminateAccount($params)
{
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);

    //we need uniq_id to terminate server
    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password);
    //create server with base configuration
    $ret = $server->destroy($row['uniq_id']);

    if($error = $server->getError())
    {
        return $error;
    }

    mysql_query("DELETE FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    return "success";
}

/**
 * Admin Area. Custom Buttons
 * @return type
 */
function LiquidWeb_AdminCustomButtonArray()
{
    return array(
        'Reboot'    => 'reboot',
        'Shutdown'  => 'shutdown',
        'Start'    =>  'start',
    );
}

function LiquidWeb_ClientAreaCustomButtonArray()
{
    $id = (int)$_REQUEST['id'];
    $q = mysql_query("SELECT tblproducts.*
        FROM tblhosting
        LEFT JOIN tblproducts ON tblhosting.packageid = tblproducts.id
        WHERE tblhosting.id = ".$id);
    $params = mysql_fetch_assoc($q);

    $q = mysql_query("SELECT tblhostingconfigoptions.*,  tblproductconfigoptions.optionname, tblproductconfigoptions.optiontype, tblproductconfigoptionssub.optionname as optname
        FROM tblhostingconfigoptions
        LEFT JOIN tblproductconfigoptions ON tblhostingconfigoptions.configid = tblproductconfigoptions.id
        LEFT JOIN tblproductconfigoptionssub ON tblhostingconfigoptions.optionid = tblproductconfigoptionssub.id
        WHERE tblhostingconfigoptions.relid=".$id);

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
        }
    }

    $return = array
    (
        'Reboot'        =>  'clientReboot',
        'Shutdown'      =>  'clientShutdown',
        'Start'         =>  'clientStart',
        'Restore'       =>  'restore',
        'History'       =>  'history',
        'Block Storage' =>  'blockStorage',
    );
    $op_q = mysql_query("SELECT tblproducts.* FROM tblhosting LEFT JOIN tblproducts ON tblhosting.packageid = tblproducts.id WHERE tblhosting.id = " . (int)$id . " LIMIT 1");
    $op_row = mysql_fetch_assoc($op_q);

    if(LiquidWeb_getOption("IPs Management", $params))
    {
      $return['IP Management'] = 'ipmanagement';
    }elseif($op_row['configoption16'] == 'on' && LiquidWeb_getOption("IPs Management", $params) == null){
      $return['IP Management'] = 'ipmanagement';
    }


    if(LiquidWeb_getOption("Firewall", $params))
    {
        $return['Firewall'] = 'firewall';
    }elseif($op_row['configoption15'] == 'on' && LiquidWeb_getOption("Firewall", $params) == null){
      $return['Firewall'] = 'firewall';
    }

    if(LiquidWeb_getOption("Backup Enabled", $params))
    {
        $return['Backups'] = 'backups';
    }elseif($op_row['configoption8'] == 'on'  && LiquidWeb_getOption("Backup Enabled", $params) == null){
      $return['Backups'] = 'backups';
    }

    return $return;
}

function LiquidWeb_Reboot($params)
{
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);

    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password);
    //create server with base configuration
    $ret = $server->reboot($row['uniq_id']);

    if($error = $server->getError())
    {
        return $error;
    }

    return "success";
}


function LiquidWeb_clientReboot($params)
{
    $vars = array();
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);

    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    switch($_REQUEST['modaction'])
    {
        case 'reboot':
            $row = mysql_fetch_assoc($q);

            require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
            $server = new StormOnDemandStormServer($username, $password);
            //create server with base configuration
            $ret = $server->reboot($row['uniq_id']);

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

    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'reboot.tpl';
    $vars['params'] = $params;
    $links = LiquidWeb_ClientAreaCustomButtonArray();
    $vars['storm_links'] = $links;
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">Reboot</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}


function LiquidWeb_Shutdown($params)
{
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);

    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password);
    //create server with base configuration
    $ret = $server->shutdown($row['uniq_id']);

    if($error = $server->getError())
    {
        return $error;
    }

    return "success";
}

function LiquidWeb_ClientShutdown($params)
{
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);

    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    switch($_REQUEST['modaction'])
    {
        case 'shutdown':
            $row = mysql_fetch_assoc($q);
            require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
            $server = new StormOnDemandStormServer($username, $password);
            //create server with base configuration
            $ret = $server->shutdown($row['uniq_id']);

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

    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'shutdown.tpl';
    $vars['params'] = $params;
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">Shutdown</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}

function LiquidWeb_Start($params)
{
    $vars = array();
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);

    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password);
    //staart sever
    $ret = $server->start($row['uniq_id']);

    if($error = $server->getError())
    {
        return $error;
    }

    return "success";
}

function LiquidWeb_ClientStart($params)
{
    $vars = array();
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);

    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    switch($_REQUEST['modaction'])
    {
        case 'start':
            $row = mysql_fetch_assoc($q);

            require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
            $server = new StormOnDemandStormServer($username, $password);
            //staart sever
            $ret = $server->start($row['uniq_id']);

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


    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'start.tpl';
    $vars['params'] = $params;
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">Start</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}


function LiquidWeb_ChangePackage($params)
{
    //get configuration
    $username           =   LiquidWeb_getOption('Username', $params);
    $password           =   LiquidWeb_getOption('Password', $params);
    $ipcount            =   LiquidWeb_getOption("Number of IPs", $params);
    $bandwidth_quota    =   LiquidWeb_getOption('Bandwidth Quota', $params);
    $hostname           =   $params['customfields']['hostname'] ? $params['customfields']['hostname'] : $params['domain'];

    if ($hostname == '') {
        $hostname = $params['customfields']['Create my VPS with following host name'];
    }

    //check bandwidth quota
    $configuration = LiquidWeb_ConfigOptions();

    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);
    $uniq_id = $row['uniq_id'];

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password);

    //details
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkIP.php';
    $ip = new StormOnDemandNetworkIP($username, $password);
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

    if(LiquidWeb_getOption('Backup Enabled', $params))
    {
        $configuration['backup_enabled']    =  1;
        $configuration['backup_quota']      =  LiquidWeb_getOption('Backup Plan', $params) == 'quota' ? LiquidWeb_getOption('Backup Quota', $params) : LiquidWeb_getOption('Daily Backup Quota', $params);
        $configuration['backup_plan']       =  LiquidWeb_getOption('Backup Plan', $params);
    }


    $Firewall = LiquidWeb_getOption("Firewall", $params);
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

    $server->resize($uniq_id, LiquidWeb_getOption('Config', $params), 0);
    if($error = $server->getError())
    {
        return $error;
    }

    return "success";
}

function LiquidWeb_ClientArea($params)
{
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);
    $monitoring =   LiquidWeb_getOption('Monitoring', $params);

    //we need uniq_id to terminate server
    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);
    $uniq_id = $row['uniq_id'];
    ////////////////////////////
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password);

    $q2 = mysql_query("SELECT tblproducts.* FROM tblhosting LEFT JOIN tblproducts ON tblhosting.packageid = tblproducts.id WHERE tblhosting.id = " . (int)$params['serviceid'] . " LIMIT 1");
    $row2 = mysql_fetch_assoc($q2);

    if($monitoring == null){
      $monitoring = $row2['configoption14'];
    }

    if(isset($_REQUEST['stormajax']))
    {
        ob_clean();
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
                $bandwidth = new StormOnDemandMonitoringBandwidth($username, $password);
                $graph = $bandwidth->graph($uniq_id, $width = 510, $height = 100, $_REQUEST['frequency'], 1);
                header('Content-type: '.$graph['content_type']);
                echo base64_decode($graph['content']);
            break;

            case 'bandwidth_stats':
                require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandMonitoringBandwidth.php';
                $bandwidth = new StormOnDemandMonitoringBandwidth($username, $password);
                $stats = $bandwidth->stats($uniq_id);

                global $smarty;
                $smarty->assign('stats', $stats);
                $tpl = $smarty->fetch(dirname(__FILE__).DS.'clientarea'.DS.'subviews'.DS.'bandwidth.tpl');
                echo $tpl;
            break;

            case 'load_graph':
                require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandMonitoringLoad.php';
                $load = new StormOnDemandMonitoringLoad($username, $password);
                $graph = $load->graph($uniq_id, $width = 510, $height = 100, $_REQUEST['stat'] = 'load5', $_REQUEST['duration'], 1);
                header('Content-type: '.$graph['content_type']);
                echo base64_decode($graph['content']);
            break;

            case 'load_stats':
                require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandMonitoringLoad.php';
                $load = new StormOnDemandMonitoringLoad($username, $password);
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


	//getting custom configurations
	require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
	$customConfig = StormOnDemand_Helper::getCustomConfigValues();

    global $smarty;
    $smarty->assign('buttons', LiquidWeb_ClientAreaCustomButtonArray());
    $smarty->assign('details', $details);
    $smarty->assign('params', $params);
    $smarty->assign('monitoring', $monitoring);
    $smarty->assign('custom_template', $customConfig['custom_template']);

    try{
        return $smarty->fetch( dirname(__FILE__) . DS . 'clientarea' . DS . 'subviews' . DS . 'clientarea.tpl');
    }
    catch(Exception $e) {
        return $e;
    }
}

function LiquidWeb_AdminServicesTabFields($params)
{
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);

    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);

    $row = mysql_fetch_assoc($q);
    $uniq_id = $row['uniq_id'];

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password);

    if($_REQUEST['stormajax'] == 'storm-history')
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

                            $("#storm-history").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWeb/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
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
    elseif($_REQUEST['stormajax'] == 'storm-image')
    {
        ob_clean();
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormImage.php';
        $image = new StormOnDemandStormImage($username, $password);
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

                            $("#storm-images").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWeb/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
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
    elseif($_REQUEST['stormajax'] == 'storm-image-restore')
    {
        ob_clean();
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormImage.php';
        $image = new StormOnDemandStormImage($username, $password);
        $image->restore($uniq_id, $_REQUEST['image_id']);
        if($error = $image->getError())
        {
            echo '<p style="color:red">'.$error.'</p>';
            die();
        }

        echo '<p>Restoring in progress...</p>';
        die();
    }
    elseif($_REQUEST['stormajax'] == 'storm-template')
    {
        ob_clean();
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormTemplate.php';
        $template = new StormOnDemandStormTemplate($username, $password);
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
                if($item['deprecated'] == 1)
                    continue;

                echo '<tr>
                        <td>'.$item['description'].'</td>
                        <td><a style="float: right" href="stormajax=storm-template-restore&template_id='.$item['id'].'" class="btn btn-danger storm-restore">Restore</a></td>
                      </tr>';
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

                            $("#storm-templates").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWeb/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>.");
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
    elseif($_REQUEST['stormajax'] == 'storm-template-restore')
    {
        ob_clean();
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormTemplate.php';
        $template = new StormOnDemandStormTemplate($username, $password);
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
                            $("#storm-history").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWeb/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
                            $.post(document.location.toString(), {"stormajax" : "storm-history"}, function(data){
                                $("#storm-history").html(data);
                            });
                        });

                        $("#load-storm-images").click(function(event){
                            event.preventDefault();
                            $("#storm-images").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWeb/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
                            $.post(document.location.toString(), {"stormajax" : "storm-image"}, function(data){
                                $("#storm-images").html(data);
                            });
                        });

                        $("#load-storm-templates").click(function(event){
                            event.preventDefault();
                            $("#storm-templates").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWeb/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
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


function LiquidWeb_AdminServicesTabFieldsSave($params)
{
    mysql_query("REPLACE INTO mg_liquid_web SET uniq_id = '".$_REQUEST['uniq_id']."', hosting_id=".(int)$params['serviceid']);
}



//FIREWALL
function LiquidWeb_Firewall($params)
{
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);

    //we need uniq_id to terminate server
    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);
    $uniq_id = $row['uniq_id'];

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkFirewall.php';
    $firewall = new StormOnDemandNetworkFirewall($username, $password);
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
            		$value = str_replace(LiquidWebFirewallSlashEscapeChar,'/',$value);
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
		     $vars['options'][$k] = str_replace('/',LiquidWebFirewallSlashEscapeChar, $v);
		}
	}

    if($vars['type'] == 'none')
    {

    }

    if($vars['type'] == 'basic')
    {
        $ret = $firewall->rules($uniq_id);
        //$vars['advanced_rules'] = $ret['rules'];
        foreach($ret['rules'] as $rule)
        {
            $vars['rules'][str_replace('/',LiquidWebFirewallSlashEscapeChar, $rule['label'])] = 1;
        }
    }

    if($vars['type'] == 'advanced')
    {
        $ret = $firewall->rules($uniq_id);
        $vars['advanced_rules'] = $ret['rules'];
    }


    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'firewall.tpl';
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">Firewall</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}


//IP Management
function LiquidWeb_IPManagement($params)
{
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);
    $ipcount    =   LiquidWeb_getOption("Maximum IP Addresses", $params);

    //we need uniq_id to terminate server
    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);
    $uniq_id = $row['uniq_id'];

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkIP.php';
    $ipmanagement = new StormOnDemandNetworkIP($username, $password);
    $vars = array();

    //update
    if(isset($_POST['modaction']))
    {
        switch($_REQUEST['modaction'])
        {
            case 'add':
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
                    $server = new StormOnDemandStormServer($username, $password);
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
                    $server = new StormOnDemandStormServer($username, $password);
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


    //get lists op IPs
    $ret = $ipmanagement->lists($uniq_id);
    $vars['list'] = $ret['items'];
    $vars['ip_count'] = $ipcount;

    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'ipmanagement.tpl';
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">IP Management</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}


//Backups
function LiquidWeb_Backups($params)
{
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);

    //we need uniq_id to terminate server
    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);
    $uniq_id = $row['uniq_id'];

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormBackup.php';
    $backup = new StormOnDemandStormBackup($username, $password);
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
                        $vars['info'] = 'Restore Backup Start Successfully.';
                    }
                break;
        }
    }

    $ret = $backup->lists($uniq_id);
    $vars['list'] = $ret['items'];

    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'backups.tpl';
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">IP Management</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}

function LiquidWeb_Restore($params)
{
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);

    //we need uniq_id to terminate server
    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);
    $uniq_id = $row['uniq_id'];


    //Templates!
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormTemplate.php';
    $template = new StormOnDemandStormTemplate($username, $password);

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
    $image = new StormOnDemandStormImage($username, $password);

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
	$idUniqs  = StormOnDemand_Helper::getAllVpsUserUniqIds($params['clientsdetails']['userid'],StormOnDemand_Helper::LiquidWebLiquidWebServerType);

    $page = 1;
    $images = array();
    $ret = $image->lists(20, $page);
	if(isset($ret['items']) && !empty($ret['items'])){
		foreach($ret['items'] as $item){
			if(in_array($item['source_uniq_id'], $idUniqs) !== false){
				$images []= $item;
			}
		}
	}

    while(isset($ret['page_num']) && $ret['page_num'] < $ret['page_total'])
    {
        $ret = $image->lists(20, ++$page);

		if(!empty($ret['items'])){
			foreach($ret['items'] as $item){
				if(strcmp($item['source_uniq_id'], $uniq_id) === 0){
					$images []= $item;
				}
			}
		}

    }
    $vars['images'] = $images;


	//VPS
	require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $apiServer = new StormOnDemandStormServer($username, $password);

	$vars['servers'] 		= StormOnDemand_Helper::getAllVpsUserUniqIds($params['clientsdetails']['userid'],StormOnDemand_Helper::LiquidWebLiquidWebServerType,false);
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
						$rAllUniqsId  = StormOnDemand_Helper::getAllVpsUserUniqIds($params['clientsdetails']['userid'],StormOnDemand_Helper::StormOnDemandStormOnDemanderverType);

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

						if($uniq_id){
							$apiServer->update($uniq_id, array(
								'domain' => 'whmcsdel-'.$params['domain']
							));
						}

                        if($uniq_id && $error = $apiServer->getError()){
                        	$vars['error'] =  'Cannot restore: '.$error;
                        }else{
							$result = $apiServer->cloneServer($_REQUEST['server_id'],$params['domain'],$params['password']);
	                        if($error = $apiServer->getError()){
	                        	$vars['error'] = $error;
							}else{

								ModuleInformationClient::mysql_safequery("REPLACE INTO mg_liquid_web (`hosting_id`, `uniq_id`) VALUES (?,?)", array(
									$params['serviceid'],
									$result['uniq_id'],
								));

								if($uniq_id){
									$apiServer->destroy($uniq_id);
			                        if($error = $apiServer->getError()){
			                        	$vars['error'] =  $error;
									}else{
		                        		$vars['info'] = 'Restoring from server. Please wait';
									}
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



    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'restore.tpl';
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">IP Management</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}

function LiquidWeb_History($params)
{
    //get configuration
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);

    //we need uniq_id to terminate server
    $q = mysql_query("SELECT * FROM mg_liquid_web WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_rows($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);
    $uniq_id = $row['uniq_id'];

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
    $server = new StormOnDemandStormServer($username, $password);

    if(isset($_REQUEST['page']))
        $page = $_REQUEST['page'];
    else
        $page = 1;

    $ret = $server->history($uniq_id, 20, $page);

    $history = $ret['items'];
    $vars['history'] = $history;
    $vars['page'] = $page;
    $vars['page_total'] = $ret['page_total'];

    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'history.tpl';
    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">IP Management</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}



function LiquidWeb_BlockStorage($params){

	$vars		=   array(); //vars for template
    $username   =   LiquidWeb_getOption('Username', $params);
    $password   =   LiquidWeb_getOption('Password', $params);

	require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'class.ModuleInformationClient.php';
	require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Storage.php';
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
		$hosting_id = $params['serviceid'];
		if(!$hosting_id){
			throw new Exception;
		}
	}catch(Exception $e){
		return 'Hosting identificator is empty';
	}

	if(LiquidWeb_validateArray($_GET, array(
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

		$soadUserProductQuery = ModuleInformationClient::mysql_safequery('SELECT hosting.id AS hosting_id,hosting.orderid as hosting_order_id, hosting.domain as hosting_domain, sod.uniq_id
																	   	  FROM tblhosting AS hosting
																	   	  JOIN mg_liquid_web as sod ON hosting.id = sod.hosting_id
																	  	  WHERE hosting.userid = ? AND hosting.id = ? LIMIT 1',array($user_id,$_GET['id']));
		$soadUserProduct = mysql_fetch_assoc($soadUserProductQuery);

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
	$hostingUniqKeyQuery   = ModuleInformationClient::mysql_safequery('SELECT * FROM mg_liquid_web WHERE hosting_id = ? LIMIT 1', array($hosting_id));
	$hostingUniqKey		   = mysql_fetch_assoc($hostingUniqKeyQuery);

	if(!$hostingUniqKey){
		return 'Hosting uniq_key doesn\'t exists';
	}

	//get all SBSModule products
	$sbsProductsIds = array();
	$sbsQuery       = ModuleInformationClient::mysql_safequery('SELECT id FROM tblproducts WHERE servertype = ?', array(LiquidWebSBSServerType));
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
																	 WHERE 	   hosting.packageid IN('.$op_pid.')
																	 	   AND hosting.userid = '.$user_id.'
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

	$vars['sbs']		 = $avSbs;
	$vars['subpage']	 = dirname(__FILE__).DS.'clientarea'.DS.'blockstorage.tpl';
	$vars['request_uri'] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	if($aPos = strpos($vars['request_uri'], 'ajaxaction')){
		$vars['request_uri'] = substr($vars['request_uri'],0, $aPos - 6);
	}

    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">Block Storage</a>',
        'vars'          =>  $vars,
    );
	return $pagearray;
}


function LiquidWeb_getOption($option, $params)
{
    //$config = LiquidWeb_ConfigOptions();
    $config = getConfigFields();

    if(isset($params['configoptions'][$option]))
    {
        return $params['configoptions'][$option];
    }

    $i = 1;
    foreach($config as $key => $value)
    {
        if($key == $option)
        {
            return $params['configoption'.$i];
        }
        $i++;
    }
}


/****************** MODULE INFORMATION ************************/

LiquidWeb_registerInstance();

function LiquidWeb_registerInstance()
{

    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   'Liquid Web For WHMCS';
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   LIQUID_WEB_VERSION;
    //Encryption key
    $moduleKey          =   'kxgpToMGObXOiUUCcJeQWpuNC9AZHvowsiOr6QFwi1VFUnQCnd0NNJDfoTi9UP7h';
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

function LiquidWeb_getLatestVersion()
{
    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   'Liquid Web For WHMCS';
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   LIQUID_WEB_VERSION;
    //Encryption key
    $moduleKey          =   'kxgpToMGObXOiUUCcJeQWpuNC9AZHvowsiOr6QFwi1VFUnQCnd0NNJDfoTi9UP7h';
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


function LiquidWeb_loadAsset($assetPath, $vars = array())
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

function LiquidWeb_loadTemplates(){

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


function LiquidWeb_validateArray($array, $pregs){

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


function getConfigFields()
{
    //Base config
   $config                 =   array
    (
        'Username'          =>  array
        (
            'Type'          =>  'text',
            'Size'          =>  '25',
        ),
        'Password'          =>  array
        (
            'Type'          =>  'password',
            'Size'          =>  '25'
        ),

        'Default Configurable Options'       =>  array
        (
            'Type'          =>  '',
            'Description'   =>  '<a id="generate-storm-confoption" href="stormajax=generate-confoption" class="load-configuration">Generate Default Configurable Options</a>'
        ),
        'Zone'              =>  array
        (
            'Type'          =>  'text',
            'Size'          =>  '25',
            'Default'	    =>  27
        ),
        //Just for defaults
        'Template'          =>  array
        (
            'Type'          =>  'text',
            'Size'          =>  '25',
            'Description'   =>  '<a id="load-storm-template" href="stormajax=load-template" class="load-configuration">Load Template</a>'
        ),
        'Image'             =>  array
        (
            'Type'          =>  'text',
            'Size'          =>  '25',
            'Description'   =>  '<a id="load-storm-image" href="stormajax=load-image" class="load-configuration">Load Image</a>'
        ),
        'Config'            =>  array
        (
          'Type'            =>  'text',
          'Size'            =>  '25',
          'Description'     =>  '<a id="load-storm-config" href="stormajax=load-config" class="load-configuration">Load Config</a>'
        ),
        'Backup Enabled'    =>  array
        (
            'Type'          =>  'yesno'
        ),
        'Backup Plan'       =>  array
        (
            'Type'          =>  'dropdown',
            'Options'       =>  'quota,daily',
        ),
        'Backup Quota'      =>  array
        (/*
            'Type'          =>  'text',
            'Size'          =>  '25',*/
            'Type'          =>  'dropdown',
        ),
        "Number of IP Addresses"       =>  array
        (
            'Type'          =>  'text',
            'Size'          =>  '25',
            'Default'       =>  1
        ),
        "Maximum IP Addresses"=>  array
        (
            'Type'          =>  'text',
            'Size'          =>  '25',
            'Default'       =>  8
        ),
        'Bandwidth Quota'   =>  array
        (
            'Type'          =>  'dropdown',
            'Options'       =>  'Pay as You Go,250,500,1000,2000,4000',
        ),
        'Monitoring'        =>  array
        (
            'Type'          =>  'yesno',
            'Description'   =>  'Check if you want to display monitoring in the clientarea'
        ),
        'Firewall'          =>  array
        (
            'Type'          =>  'yesno',
            'Description'   =>  'Check if you want to enable firewall managing in the clientarea'
        ),
        "IPs Management"   =>  array
        (
            'Type'          =>  'yesno',
            'Description'   =>  'Check if you want to enable IP managing in the clientarea'
        ),
        "Error"   =>  array
        (
            'Type'          =>  '',
            'Description'   =>  '<p style="text-align: center;" class="errorbox"><span style="font-weight: bold">Authorization error. Please check username and password.</span></p>'
        )
    );
    return $config;
}