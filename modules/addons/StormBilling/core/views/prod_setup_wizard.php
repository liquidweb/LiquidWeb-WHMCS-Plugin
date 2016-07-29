<?php
//ajax call
if (isset($_REQUEST['ajaxload'])) {
    if ($_REQUEST['ajaxload'] == 'zone') {
        $conf_id = $_REQUEST['conf_id'];

        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkZone.php';
        $zone = new StormOnDemandNetworkZone($_SESSION['api_username'], $_SESSION['api_password'], 'bleed');

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

        foreach($ret['items'] as $item) {

			$zoneName = $item['name'];
			if(isset($item['region']['name'])){
				$zoneName .=' ('.$item['region']['name'].')';
			}

            echo '<tr>
                    <td><input class="storm-zone" type="radio" name="zone-id" id="zone-id" data-zone-name="'.$zoneName.'" value="'.$item['id'].'" '.($item['id'] == $conf_id ? 'checked="checked"' : '').' />'.$item['name'].'</td>
                    <td>'.$item['region']['name'].'</td>
                  </tr>';
        }
        echo '</table>';
    }

    if ($_REQUEST['ajaxload'] == 'template') {
        $conf_id = $_REQUEST['conf_id'];
        $zoneSelected = $_REQUEST['zone'];

        $template_id = array();
        $q = mysql_query("SELECT * FROM `StormBilling_customconfig` where `config_name` = 'wiz_pg_4_hide_from_tmplt_list'");
        if(($res = mysql_fetch_assoc($q))) {
            $template_id = @explode(",", $res['config_value']);
        }

        require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormTemplate.php';

        $template = new StormOnDemandStormTemplate($_SESSION['api_username'], $_SESSION['api_password'], 'bleed');
        $ret = $template->lists();

        if ($error = $template->getError()) {
            logModuleCall('StormOnDemand','ConfigOptions',$template->getLastRequest(),'',$template->getLastResponse(),array());

            echo '<p style="color: red">'.$error.'</p>';
            die();
        }

        echo '<table class="datatable" style="width: 100%">
                <tr>
                    <th>Template</th>
                </tr>
              ';

        foreach ($ret['items'] as $item) {

            foreach ($template_id as $tempid) {

                if ($tempid != $item['id']) {
                    if ($item['deprecated'] == 1) {
                        continue;
                    }

                    if (!isset($item['zone_availability']) ||
                         empty($item['zone_availability']) ||
                        !isset($item['zone_availability'][$zoneSelected]) ||
                        !$item['zone_availability'][$zoneSelected]) {
                        continue;
                    }

                    echo '<tr>
                            <td><input class="storm-template" type="radio" name="template-id" data-template-name="'.$item['description'].'" value="'.$item['name'].'" '.($item['name'] == $conf_id ? 'checked="checked"' : '').'/>'.$item['description'].'</td>
                          </tr>';
                }
            }
        }
        echo '</table>';
    }

    if ($_REQUEST['ajaxload'] == 'pp_template') {
        $conf_id = $_REQUEST['conf_id'];

        $template_id = array();
        $q = mysql_query("SELECT * FROM `StormBilling_customconfig` where `config_name` = 'wiz_pg_4_hide_from_tmplt_list'");
        if(($res = mysql_fetch_assoc($q))) {
            $template_id = @explode(",", $res['config_value']);
        }

        require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormTemplate.php';

        $template = new StormOnDemandStormTemplate($_SESSION['api_username'], $_SESSION['api_password'], 'bleed');
        $ret = $template->lists();

        if ($error = $template->getError()) {
            logModuleCall('StormOnDemand','ConfigOptions',$template->getLastRequest(),'',$template->getLastResponse(),array());

            echo '<p style="color: red">'.$error.'</p>';
            die();
        }

        echo '<table class="datatable" style="width: 100%">
                <tr>
                    <th>Template</th>
                </tr>
              ';

        foreach ($ret['items'] as $item) {
            foreach ($template_id as $tempid) {

                if ($tempid != $item['id']) {
                    if ($item['deprecated'] == 1) {
                        continue;
                    }

                    echo '<tr>
                            <td><input class="storm-template" type="radio" name="template-id" data-template-name="'.$item['description'].'" value="'.$item['name'].'" '.($item['name'] == $conf_id ? 'checked="checked"' : '').'/>'.$item['description'].'</td>
                          </tr>';
                }
            }
        }
        echo '</table>';
    }

    if ($_REQUEST['ajaxload'] == 'vpstypes') {
        $conf_id = $_REQUEST['conf_id'];
        $zoneSelected = $_REQUEST['zone'];


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
	        $config = new StormOnDemandStormConfig($_SESSION['api_username'], $_SESSION['api_password'], 'bleed');

	        $page = 1;
	        $count = $config->lists('all', $page, 1);

	        if (isset($count['item_total']) && $count['item_total'] > 0) {
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
		} else {
			$confs = $baCache['data'];
		}

 echo '<script>
  $(function() {
    $( "#storm-config-tabs" ).tabs();
  });
  </script>';


        echo '<div id="storm-config-tabs">';
        echo '<ul id="menus">
                <li><a href="#storm-config-tab-storm">Storm Servers</a></li>
                <li><a href="#storm-config-tab-ssd">SSD Servers</a></li>
                <li><a href="#storm-config-tab-bare-metal">Bare Metal Servers</a></li>
              </ul>';

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
                    <td><input class="storm-config" type="radio" name="config-id" data-VPSType-name="'.$item['description'].'" value="'.$item['id'].'" '.($item['id'] == $conf_id ? 'checked="checked"' : '').'/> '.$item['description'].'</td>
                    <td>'.$item['vcpu'].' CPUs</td>
                    <td>'.$item['disk'].'</td>
                    <td>'.$item['memory'].'</td>
                  </tr>';
        }
        echo '</table>';
        echo '</div>';

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
                    <td><input class="storm-config" type="radio" name="config-id" data-VPSType-name="'.$item['description'].'" value="'.$item['id'].'" '.($item['id'] == $conf_id ? 'checked="checked"' : '').'/> '.$item['description'].'</td>
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
                    <td><input class="storm-config" type="radio" name="config-id" data-VPSType-name="'.$item['description'].'" value="'.$item['id'].'" '.($item['id'] == $conf_id ? 'checked="checked"' : '').'/> '.$item['description'].'</td>
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
    }

    die;
}
?>

<div id="mg-content" class="right">
    <div id="top-bar">
        <div id="module-name">
        	<h2>Liquid Web Product Setup</h2>
        </div>

        <!--<div class="clear"></div>-->
        <a class="slogan" href="http://www.liquidweb.com" target="_blank" alt="Liquid Web">
            <span class="lw-logo"></span>
        </a>
    </div><!-- end of TOP BAR -->

<?php

if(!function_exists('print_error'))
{
    function print_error($err_msg)
    {
        echo '<div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">&times;</button>'.$err_msg.'</div>';
    }
}

if ((isset($_REQUEST['module']) && $_REQUEST['module']=='StormBilling') && (isset($_REQUEST['action']) && $_REQUEST['action']=='setup')) {
?>
    <div class="inner">
    <?php

    //validate page 1
    if (isset($_POST['wiz_page']) && ($_POST['wiz_page'] == '1')){
        unset($_SESSION['setup_lw_ssd_vps']); unset($_SESSION['setup_lw_pri_cld']);
        if ((isset($_POST['setup_lw_ssd_vps'])) || (isset($_POST['setup_lw_pri_cld']))) {
            $_SESSION['setup_lw_ssd_vps'] = $_POST['setup_lw_ssd_vps'];
            $_SESSION['setup_lw_pri_cld'] = $_POST['setup_lw_pri_cld'];

            //check for api_username from database
            require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
            $customConfig = StormOnDemand_Helper::getCustomConfigValues();

            unset($_SESSION['api_username']); unset($_SESSION['api_password']);
            if (isset($customConfig['api_username'])) {
                $_SESSION['api_username'] = $customConfig['api_username'];
                $_SESSION['api_password'] = $customConfig['api_password'];
                //$_SESSION['api_password'] = StormOnDemand_Helper::encrypt_decrypt($customConfig['api_password']);

                if (isset($_POST['setup_lw_ssd_vps'])) {
                    header('Location: '.'addonmodules.php?module=StormBilling&action=setup&pg=4');
                } elseif (isset($_POST['setup_lw_pri_cld'])) {
                    header('Location: '.'addonmodules.php?module=StormBilling&action=setup&pg=5');
                }
            }
        } else {
            $_REQUEST['pg'] = $_POST['wiz_page'];
            print_error('Please select any one option.');
        }
    }

    //validate page 2
    if (isset($_POST['wiz_page']) && ($_POST['wiz_page'] == '2')){
        if (((isset($_POST['wiz_page2_usertype'])) && ($_POST['wiz_page2_usertype'] == 'ACCOUNT'))){
            //validate
            $username = $_POST['setup_lw_username'];
            $password = $_POST['setup_lw_password'];
            require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormConfig.php';
            $config = new StormOnDemandStormConfig($username, $password, 'bleed');

            $res = $config->ping();
            if (isset($res['ping']) && $res['ping'] = 'success') {
                $username = StormOnDemand_Helper::random_password(6);
                $password = StormOnDemand_Helper::random_password(8);

                $res = $config->userCreate($username,$password, 'bleed');
                if (isset($res['active']) && $res['active'] = '1') {
                    $username = 'whmcsuser-'.$username; //prefix whmcsuser

                    $_SESSION['api_username'] = $username;
                    $_SESSION['api_password'] = $password;
                    //$password = StormOnDemand_Helper::encrypt_decrypt($password, 'encrypt');

                    //save to database
                    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
                    $api_user = array('api_username'=>$username, 'api_password'=>$password);
                    StormOnDemand_Helper::saveConfigs($api_user);
                } else {
                    $_REQUEST['pg'] = $_POST['wiz_page'];
                    print_error('Unable to create new API user account.');
                }
            } else {
                $_REQUEST['pg'] = $_POST['wiz_page'];
                print_error('Invalid account credentials.');
            }
        } elseif (((isset($_POST['wiz_page2_usertype'])) && ($_POST['wiz_page2_usertype'] == 'API'))) {
            //validate
            $username = $_POST['setup_lw_username'];
            $password = $_POST['setup_lw_password'];

            require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormConfig.php';
            $config = new StormOnDemandStormConfig($username, $password, 'bleed');

            $res = $config->ping();
            if (isset($res['ping']) && $res['ping'] = 'success') {
                $res = $config->userDetails($username);
                if (isset($res['active']) && $res['active'] = '1') {
                    $_SESSION['api_username'] = $username;
                    $_SESSION['api_password'] = $password;

                    //save to database
                    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
                    $api_user = array('api_username'=>$username, 'api_password'=>$password);
                    StormOnDemand_Helper::saveConfigs($api_user);
                } else {
                    $_REQUEST['pg'] = $_POST['wiz_page'];
                    print_error('Invalid API credentials.XXXX');
                }
            } else {
                $_REQUEST['pg'] = $_POST['wiz_page'];
                print_error('Invalid API credentials.');
            }
        } else {
            $_REQUEST['pg'] = $_POST['wiz_page'];
            print_error('Please enter your Liquid Web API / ACCOUNT username and password.');
        }
    }

    if (isset($_POST['wiz_page']) && ($_POST['wiz_page'] == '4')){
        //save data
        $data = Array();
        $data['configoption4'] = $_POST['setup_lw_zonecode'];
        $data['configoption5'] = $_POST['setup_lw_ostemplates'];
        $data['configoption7'] = $_POST['setup_lw_vpstype'];
        $data['configoption8'] = $_POST['setup_lw_backup'];
        $data['configoption9'] = $_POST['setup_lw_backupplan'];
        $data['configoption10'] = $_POST['setup_lw_backupquota'];
        $data['configoption11'] = $_POST['setup_lw_ips'];
        $data['configoption12'] = '20';
        $data['configoption13'] = '5000';
        $data['configoption14'] = $_POST['setup_lw_backup'];
        $data['configoption15'] = $_POST['setup_lw_firewall'];
        $data['configoption16'] = $_POST['setup_lw_ipsmgt'];
        //$data['configoption17'] = $_POST['setup_lw_price'];

        $data['price_type'] = $_POST['setup_lw_price_type'];
        $data['price'] = $_POST['setup_lw_price'];
        $price = $data['price'];
        $data['gid'] = $_POST['setup_lw_productgroup'];
        $data['name'] = $_POST['setup_lw_productname'];
        $data['description'] = $_POST['setup_lw_description'];

        if ($_POST['setup_lw_price_type'] == 'perentage') {
    		require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandProduct.php';

    		$product 	  = new StormOnDemandProduct($_SESSION['api_username'],$_SESSION['api_password'],'bleed');
    		$product_ret = $product->details(null,'SS.VPS');
            $arr_price = Array();

            foreach ($product_ret['options'] as $optn) {
            	if ($optn['key'] == 'LiquidWebBackupPlan') {
        			foreach ($optn['values'] as $values) {
    					if ($values['value'] == 'Quota') { //only quota is considered in wizard page
    						foreach ($values['options'][0]['values'] as $vals) {
    							foreach ($vals['prices'] as $prKey=>$prVal) {
    								$zoneprice[$prKey] = $prVal['month'];
    							}
    							$arr_price[$optn['key']][$vals['value']] = $zoneprice;
    						}
    					}
        			}
            	} else {
                	foreach ($optn as $values) {
                		if (is_array($values)) {
                			foreach ($values as $vals) {
                				foreach ($vals['prices'] as $prKey=>$prVal) {
            						$zoneprice[$prKey] = $prVal['month'];
                				}
                				$arr_price[$optn['key']][$vals['value']] = $zoneprice;
                			}
                		}
                	}
            	}
            }

            //get zone list from api
            require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkZone.php';
            $zonedtl = new StormOnDemandNetworkZone($_SESSION['api_username'],$_SESSION['api_password'],'bleed');
            $zone = $zonedtl->details($_POST['setup_lw_zonecode']);

            $price = 0;
            $price = $price + $arr_price['Template'][$_POST['setup_lw_ostemplates']][$zone['region']['id']];
            $price = $price + $arr_price['ConfigId'][$_POST['setup_lw_vpstype']][$zone['region']['id']];
            if ($_POST['setup_lw_backup'] == 'on'){
                $price = $price + $arr_price['LiquidWebBackupPlan'][$_POST['setup_lw_backupquota']][$zone['region']['id']];
            }
            if ($_POST['setup_lw_ips'] > 1){
                $xtraips = $_POST['setup_lw_ips']-1;
                $price = $price + ($arr_price['ExtraIp']['1'][$zone['region']['id']] * $xtraips);
            }
            $price = $price + ($price*$_POST['setup_lw_price']*0.01);
        }

        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
		$new_pid = StormOnDemand_Helper::saveSSDVPSConfigurations($data);

        $q = mysql_query("SELECT * FROM `tblpricing` WHERE `type` = 'product' and `relid`=".$new_pid);
        if(mysql_fetch_array($q) !== false) {
            mysql_query("UPDATE `tblpricing` SET `monthly`='".$price."' WHERE `type` = 'product' and `relid`=".$new_pid);
        } else {
            mysql_query("INSERT INTO `tblpricing`(`type`, `currency`, `relid`, `msetupfee`, `qsetupfee`, `ssetupfee`, `asetupfee`, `bsetupfee`, `tsetupfee`, `monthly`, `quarterly`, `semiannually`, `annually`, `biennially`, `triennially`)
            VALUES ('product',1,".$new_pid.",'0.00','0.00','0.00','0.00','0.00','0.00','".$price."','-1.00','-1.00','-1.00','-1.00','-1.00')");
        }

        //create hostname custom field
        mysql_query("INSERT INTO `tblcustomfields`(`type`, `relid`, `fieldname`, `fieldtype`, `required`, `showorder`)
        VALUES ('product',".$new_pid.",'Create my VPS with following host name','text','on','on')");

        //redirect
        if (isset($_POST['wiz_page4_action']))  {
    		if ($_POST['wiz_page4_action'] == 'save') {
                if (! isset($_SESSION['setup_lw_pri_cld'])){
                    header('Location: '.'addonmodules.php?module=StormBilling&action=setup');
                }
            } else {
                header('Location: '.'configproducts.php?action=edit&id='.$new_pid.'&tab=3');
            }
        }
    }

    if (isset($_POST['wiz_page']) && ($_POST['wiz_page'] == '5')){
        if($_POST['wiz_page5_action'] == "Add Server") {
            //echo $_POST['setup_lw_add_server_name']." -- ".$_POST['setup_lw_add_server_password']." -- ".$_POST['setup_lw_add_server_configid'];

            $configid = $_POST['setup_lw_add_server_configid'];
            $domain = $_POST['setup_lw_add_server_name'];

            //Get Servers
            require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormPrivateParent.php';
            $server = new StormOnDemandStormPrivateParent($_SESSION['api_username'], $_SESSION['api_password'],'bleed');

            $response = $server->create($configid, $domain);
        } else {
            require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';

            //save data
            $monitoring = $firewall = $ipsmgmnt = '';
            if($_POST['setup_lw_spp_monitoring'] == "on") {
               $monitoring =  '1';
            }

            if($_POST['setup_lw_spp_firewall'] == "on") {
               $firewall = '1';
            }

            if($_POST['setup_lw_spp_ipsmgmnt'] == "on") {
                $ipsmgmnt = '1';
            }

            $data = array();
            $data['Parent'] = $_POST['setup_lw_spp_parent_unique_id'];
            $data['Template'] = $_POST['setup_lw_ostemplates'];
            $data['Memory'] = $_POST['setup_lw_spp_memory'];
            $data['Diskspace'] = $_POST['setup_lw_spp_diskspace'];
            $data['VCPU'] = $_POST['setup_lw_spp_virtcpu'];

            $data['Backup Plan'] = $_POST['setup_lw_spp_backupplan'];
            if ($_POST['setup_lw_spp_ipsbackupquota'] == '0') {
                $data['Backup Plan'] = '0';
            }

            $data['Backup Quota'] = $_POST['setup_lw_spp_ipsbackupquota'];
            $data['Maximal IPs Number'] = $_POST['setup_lw_ips'];
            $data['Bandwidth Quota'] = $_POST['setup_lw_spp_bandwidthquota'];
            $data['Monitoring'] = $monitoring;
            $data['Firewall'] = $firewall;
            $data['IPs Management'] = $ipsmgmnt;
            $data['Username'] = $_SESSION['api_username'];
            $data['Password'] = $_SESSION['api_password'];
            $data['gid'] = $_POST['setup_lw_spp_productgroup'];
            $data['name'] = $_POST['setup_lw_spp_productname'];
            $data['description'] = $_POST['setup_lw_spp_description'];
            //$data['Password'] = StormOnDemand_Helper::encrypt_decrypt($_SESSION['api_password'], 'encrypt');

            $new_pid = StormOnDemand_Helper::savePrivateCloudConfigurations($data);

            //create hostname custom field
            mysql_query("INSERT INTO `tblcustomfields`(`type`, `relid`, `fieldname`, `fieldtype`, `required`, `showorder`)
            VALUES ('product',".$new_pid.",'Create my VPS with following host name','text','on','on')");

            //redirect
            if (isset($_POST['wiz_page5_action']))  {
                if ($_POST['wiz_page5_action'] == 'save') {
                    header('Location: '.'addonmodules.php?module=StormBilling&action=setup');
                } else {
                    header('Location: '.'configproducts.php?action=edit&id='.$new_pid.'&tab=3');
                }
            }
        }
    }

    if ((isset($_REQUEST['pg']) && $_REQUEST['pg']=='2')) {
        require_once CORE_DIR.DS.'views'.DS.'wizard'.DS.'page2.php';
    } elseif ((isset($_REQUEST['pg']) && $_REQUEST['pg']=='3')) {
        require_once CORE_DIR.DS.'views'.DS.'wizard'.DS.'page3.php';
    } elseif ((isset($_REQUEST['pg']) && $_REQUEST['pg']=='4')) {
        //load from template (db)
        $q = mysql_query('SELECT * FROM `mg_LiquidWeb_def_config_options` WHERE prod_type = "LiquidWeb"');
        while(($res = mysql_fetch_assoc($q))){
            if ($res['field_name'] == 'zone') { $row['configoption4'] = $res['field_value']; }
            else if ($res['field_name'] == 'os_template') { $row['configoption5'] = $res['field_value']; }
            else if ($res['field_name'] == 'vps_type') { $row['configoption7'] = $res['field_value']; }
            else if ($res['field_name'] == 'backup_enabled') { $row['configoption8'] = $res['field_value']; }
            else if ($res['field_name'] == 'backup_plan') { $row['configoption9'] = $res['field_value']; }
            else if ($res['field_name'] == 'backup_quota') { $row['configoption10'] = $res['field_value']; }
            else if ($res['field_name'] == 'ips') { $row['configoption11'] = $res['field_value']; }
            else if ($res['field_name'] == 'max_ips_number') { $row['configoption12'] = $res['field_value']; }
            else if ($res['field_name'] == 'bandwidth_quota') { $row['configoption13'] = $res['field_value']; }
            else if ($res['field_name'] == 'monitoring') { $row['configoption14'] = $res['field_value']; }
            else if ($res['field_name'] == 'firewall') { $row['configoption15'] = $res['field_value']; }
            else if ($res['field_name'] == 'ips_management') { $row['configoption16'] = $res['field_value']; }
            else if ($res['field_name'] == 'name') { $row['name'] = $res['field_value']; }
            else if ($res['field_name'] == 'description') { $row['description'] = $res['field_value']; }
            $row['price_type'] = 'fixed';
            $row['price'] = '0.00';
        }

        $productgroup = Array();
        $q = mysql_query('SELECT `id` , `name` FROM `tblproductgroups`');
        while(($res = mysql_fetch_assoc($q))){
            $productgroup[$res['id']] = $res['name'];
        }
        if (count($productgroup) == 0) {
            $productgroup['0'] = 'Liquid Web Products';
        }

        //get zone list from api
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkZone.php';
        $zonedtl = new StormOnDemandNetworkZone($_SESSION['api_username'],$_SESSION['api_password'],'bleed');

        $ret = $zonedtl->details($row['configoption4']);
        if($error = $zonedtl->getError()) {
            $zone[27] = 'ERROR';
            die();
        }

		$zoneName = $ret['name'];
		if(isset($ret['region']['name'])){
			$zoneName .=' ('.$ret['region']['name'].')';
		}
        $zone[$ret['id']] = $zoneName;

        require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormTemplate.php';

        $template = new StormOnDemandStormTemplate($_SESSION['api_username'], $_SESSION['api_password'], 'bleed');
        $ret = $template->lists();

        if ($error = $template->getError()) {
            $ostemplate[0] = 'ERROR';
            die();
        }

        foreach ($ret['items'] as $item)
        {
            if ($item['name'] == $row['configoption5']) {
                $ostemplate[$row['configoption5']] = $item['description'];
                break;
            }
        }

        //fetch vps type name
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormConfig.php';
        $config = new StormOnDemandStormConfig($_SESSION['api_username'], $_SESSION['api_password'], 'bleed');

        $ret = $config->details($row['configoption7']);

        if($error = $config->getError()) {
            $vpstype[0] = 'ERROR';
            die();
        }
        $vpstype[$ret['id']] = $ret['description'];


		require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandProduct.php';

		$product 	  = new StormOnDemandProduct($_SESSION['api_username'],$_SESSION['api_password'],'bleed');
		$product_ret = $product->details(null,'SS.VPS');

        // Get backup quota
        if ($product_ret && !empty($product_ret['options'])) {
            foreach ($product_ret['options'] as $kOpt => $vOpt) {
                if (strcmp($vOpt['key'], 'LiquidWebBackupPlan') === 0) {

                    if (!empty($vOpt['values'])) {
                        foreach ($vOpt['values'] as $kVal => $vVal) {

                            if (strcmp($vVal['description'], 'Quota-based Backups') === 0) {
                                if (!empty($vVal['options'])) {
                                    foreach ($vVal['options'] as $vkOpt => $vkVal) {
                                        if (strcmp($vkVal['key'], 'BackupQuota') === 0) {
                                            if (!empty($vkVal['values'])) {
                                                foreach ($vkVal['values'] as $kBack => $vBack) {
                                                    $backupQuota[$vBack['value']] = $vBack['description'];
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

        require_once CORE_DIR.DS.'views'.DS.'wizard'.DS.'page4.php';
    } elseif ((isset($_REQUEST['pg']) && $_REQUEST['pg']=='5')) {

        $private_parent = array();
        $q = mysql_query("SELECT * FROM `mg_LiquidWeb_def_config_options` WHERE `prod_type`='LiquidWebPrivatePare'");
        while(($res = mysql_fetch_assoc($q))) {
            if (trim($res['field_name']) == 'os_template') { $private_parent['template'] = $res['field_value']; }
            else if (trim($res['field_name']) == 'memory') { $private_parent['memory'] = $res['field_value']; }
            else if (trim($res['field_name']) == 'disk_space') { $private_parent['diskspace'] = $res['field_value']; }
            else if (trim($res['field_name']) == 'virtual_cpu') { $private_parent['vcpu'] = $res['field_value']; }
            else if (trim($res['field_name']) == 'backup_plan') { $private_parent['backup_plan'] = $res['field_value']; }
            else if (trim($res['field_name']) == 'backup_quota') { $private_parent['backup_quota'] = $res['field_value']; }
            else if (trim($res['field_name']) == 'bandwidth_quota') { $private_parent['bandwidth_quota'] = $res['field_value']; }
            else if (trim($res['field_name']) == 'monitoring') { $private_parent['monitoring'] = $res['field_value']; }
            else if (trim($res['field_name']) == 'firewall') { $private_parent['firewall'] = $res['field_value']; }
            else if (trim($res['field_name']) == 'ips_management') { $private_parent['ips_management'] = $res['field_value']; }
            //else if (trim($res['field_name']) == 'gid') { $private_parent['gid'] = $res['field_value']; }
            else if (trim($res['field_name']) == 'name') { $private_parent['name'] = $res['field_value']; }
            else if (trim($res['field_name']) == 'description') { $private_parent['description'] = $res['field_value']; }
/*

        else if (trim($res['field_name']) == 'Parent') { $private_parent['parent'] = $res['field_value']; }
            else if (trim($res['field_name']) == 'Maximal IPs Number') { $private_parent['maximal_ips_number'] = $res['field_value']; }
            */
        }
        $private_parent['page5_action'] = '';

        $q = mysql_query('SELECT `id` , `name` FROM `tblproductgroups`');
        while(($res = mysql_fetch_assoc($q))){
            $productgroup[$res['id']] = $res['name'];
        }
        if (count($productgroup) == 0) {
            $productgroup['0'] = 'Liquid Web Products';
        }

        //Get Servers
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormPrivateParent.php';
        $server = new StormOnDemandStormPrivateParent($_SESSION['api_username'], $_SESSION['api_password'], 'bleed');

        $page_num = 1;
        $page_size = 250;

        $response = $server->lists($page_num, $page_size);

        if (((int)$response['item_count'] = 0) || ((int)$response['item_total'] == 0)) {
            $private_parent['page5_action'] = "Add Server";

            //Get Network List
            require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkZone.php';
            $zones = new StormOnDemandNetworkZone($_SESSION['api_username'], $_SESSION['api_password'], 'bleed');

            $zone = $zones->lists();

            $dropdown = "<select id='ddl_zone'>";
            $is_default = '';
            foreach($zone['items'] as $z) {
                if ($z['is_default'] == 1) {
                    $dropdown .= "<option value='".$z['id']."' selected>".$z['name']." - ".$z['region']['name']."</option>";
                    $is_default = $z['id'];
                } else {
                    $dropdown .= "<option value='".$z['id']."'>".$z['name']." - ".$z['region']['name']."</option>";
                }
            }
            $dropdown .= "</select>";

            //Get Product Details
            require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandProduct.php';
            $product = new StormOnDemandProduct($_SESSION['api_username'],$_SESSION['api_password'],'bleed');

            $result = $product->details(null,'SS.PP');

            $serverdetails = $result['options'][0]['values']; //For code = 'SS.PP';

            $servertypetable = "<div style='width: 700px; height: 25px; padding-bottom: 10;'>CHOOSE SERVER TYPE: ".$dropdown."</div>";
            foreach($zone['items'] as $z) {
                $divid = "div_".$z['id'];
                if($is_default == $z['id']) {
                    $servertable .= "<div class='showdiv' id='".$divid."'><table class='datatable' border='1' style='border-collapse: collapse;'>";
                } else {
                    $servertable .= "<div class='hidediv' id='".$divid."'><table class='datatable' border='1' style='border-collapse: collapse;'>";
                }

                $servertable .= "<tr><th></th><th>Server Type</th><th>Speed</th><th>CPUs</th><th>Cores</th><th>RAM</th><th>Disks</th><th>Size</th><th>Type</th><th>RAID</th><th>PRICE</th></tr>";
                foreach($serverdetails as $sd) {

                    foreach ($sd['zone_availability'] as $key => $value) {
                        if ($key == $z['id'] && $value != 0) {
                            $price = $sd['prices'][$z['region']['id']]['month'];
                            $servertable .= "<tr>";
                                $servertable .= "<td><input type='radio' name='server' id=". $sd['value']." onclick='setconfigid(this.id)'/></td><td>".$sd['cpu_description']."</td><td>".$sd['cpu_speed']."</td><td>".$sd['cpu_sockets']."</td><td>".$sd['cpu_cores']."</td><td>".$sd['memory']."</td><td>".$sd['disk_count']."</td><td>".$sd['disk_total']."</td><td>".$sd['disk_type']."</td><td>".'RAID'.$sd['raid_level']."</td><td>$".$price." / mo</td>";
                            $servertable .= "</tr>";
                        }
                    }
                }
                $servertable .=  "</table></div>";
            }

            require_once CORE_DIR.DS.'views'.DS.'wizard'.DS.'page5.php';
        }

        //Get Parent Servers
        //require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormPrivateParent.php';
        //$private = new StormOnDemandStormPrivateParent($_SESSION['api_username'], $_SESSION['api_password'], 'bleed');

        //$page_num = 1;
        //$page_size = 25;

        //$response = $private->lists($page_num, $page_size);

        if(!$response) {
            ob_clean();
            json_encode(array(
                'status'    =>  0,
                'message'   =>  $private->getError()
            ));
            die();
        }

        $parents = $response['items'];
        while($response['item_total'] > $page_num * $page_size) {
            $page_num++;
            $response = $private->lists($page_size, $page_num);
            $parents = array_merge($parents, $response['items']);
        }

        $priParents = array();
        foreach($parents as $parent) {
            //$priParents[$parent['uniq_id']] = $parent['domain'];
            $uniq_id = $parent['uniq_id'];
            $freemem = $parent['resources']['memory']['free'];
            $freedsk = $parent['resources']['diskspace']['free'];

            $priParents['{"unique_id":"'.$uniq_id.'","free_mem":"'.$freemem.'","free_disk":"'.$freedsk.'"}'] = $parent['domain'];
        }

        //Fetch Template
        require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormTemplate.php';

        $template = new StormOnDemandStormTemplate($_SESSION['api_username'], $_SESSION['api_password'], 'bleed');
        $get_template = $template->lists();

        $ostemplate = array();

        if ($error = $template->getError()) {
            $ostemplate[0] = 'ERROR';
            die();
        }

        foreach ($get_template['items'] as $item) {
            if ($item['name'] == $private_parent['template']) {
                $ostemplate[$private_parent['template']] = $item['description'];
                break;
            }
        }

        /*//Backup Plans
        $backupPlans[0] = Array('name'=>'0','value'=>'Disabled');
        $backupPlans[1] = Array('name'=>'quota','value'=>'Quota');
        $backupPlans[2] = Array('name'=>'daily','value'=>'Daily');*/

        //Fetch Backup Plan
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandProduct.php';
		$product 	  = new StormOnDemandProduct($_SESSION['api_username'], $_SESSION['api_password'], 'bleed');
		//$productLists = $product->details(null,'SS.PP');
        $productLists = $product->details(null,'SS.VPS');
		$backupQuota[0] = array(
			'description' => 'NO BACKUP',
			'value'		  => '0'
		);
		// Get backup quota
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
													$backupQuota[(int)$vBack['display_order']] = array(
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

		// Get bandwidth options
		$bandwidthQuota = array();

		if($productLists && !empty($productLists['options'])){
			foreach($productLists['options'] as $kOpt => $vOpt){
				if(strcmp($vOpt['key'],'Bandwidth') === 0){

					if(!empty($vOpt['values'])){
						foreach($vOpt['values'] as $kVal => $vVal){

							$limit = (int) str_replace('SS.','', $vVal['value']);
							$bandwidthQuota[] = array(
								'description' => $limit.' GB',
								'value'		  => $limit,
							);
						}
					}

					break;
				}
			}
		}
        require_once CORE_DIR.DS.'views'.DS.'wizard'.DS.'page5.php';
    } else {
        require_once CORE_DIR.DS.'views'.DS.'wizard'.DS.'page1.php';
    }
}

?>
</div>

	</div><!-- end of INNER -->
	<div class="overlay hide"></div>
</div><!-- end of CONTENT -->

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->

<script src="'.$ASSETS_DIR.'/js/jquery.js"></script>
<script src="'.$ASSETS_DIR.'/js/jquery-ui-1.9.1.custom.min.js"></script>
<script src="'.$ASSETS_DIR.'/js/bootstrap.js"></script>

<script src="'.$ASSETS_DIR.'/js/application.js"></script>
<script src="'.$ASSETS_DIR.'/js/modulesgarden.js"></script>
