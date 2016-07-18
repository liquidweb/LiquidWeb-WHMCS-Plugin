<?php

/**********************************************************************
 * Product developed. (2014-12-18)
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
 **********************************************************************/

/**
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 */
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
include_once dirname(__FILE__) .DIRECTORY_SEPARATOR.'LiquidWebSBS_Loader.php';
include_once dirname(__FILE__) .DIRECTORY_SEPARATOR.'functions.php';

if(file_exists(dirname(__FILE__).DS.'moduleVersion.php')){
    require_once dirname(__FILE__).DS.'moduleVersion.php';
     define('LIQUID_WEB_SBS_VERSION', $moduleVersion);
}else{
     define('LIQUID_WEB_SBS_VERSION', 'Development Version');
}

function LiquidWebSBS_checkConnection()
{
    $q = mysql_query("SELECT * FROM tblproducts WHERE id = " . (int)$_REQUEST['id'] . " LIMIT 1");
    $row = mysql_fetch_assoc($q);

    $username = $row['configoption1'];
    $password = $row['configoption2'];

    if(strpos($_SERVER['SCRIPT_FILENAME'], 'configproducts.php') !== false)
    {
        if(! empty($username) && ! empty($password))
        {
            require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormConfig.php';

            $config = new StormOnDemandStormConfig($username, $password);

            $res = $config->ping();

            if(isset($res['ping']) && $res['ping'] = 'success')
            {
                return true;
            }
            if($_GET['action'] != 'save'){
                echo '<p style="text-align: center;" class="errorbox">
                    <span style="font-weight: bold">Authorization error. Please check username and password.</span>
                 </p>';
            }

            return false;
        }
        if($_GET['action'] != 'save'){
            echo '<p style="text-align: center;" class="infobox">
                    <span style="font-weight: bold">Please enter your API User username in "Username" field and your API User password in "Password".</span>
                 </p>';
        }
    }elseif(strpos($_SERVER['SCRIPT_FILENAME'], 'clientsservices.php') !== false){
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
    }
}

/**
 * Config options are the module settings defined on a per product basis.
 *
 * @return array
 */
function LiquidWebSBS_ConfigOptions($loadValuesFromServer = true) {

	if(!isset($_REQUEST['stormajax'])){
		$_REQUEST['stormajax'] = 'empty';
	}

    if($_REQUEST['stormajax'] == 'load-zone')
    {
        ob_clean();
        $conf_id = $_REQUEST['conf_id'];

        $q = mysql_query("SELECT * FROM tblproducts WHERE id = ".(int)$_REQUEST['id']." LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $username   =   $row['configoption1'];
        $password   =   $row['configoption2'];

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
        foreach($ret['items'] as $item)
        {
            echo '<tr>
                    <td><input class="storm-zone" type="radio" name="zone-id" value="'.$item['id'].'" '.($item['id'] == $conf_id ? 'checked="checked"' : '').' />'.$item['name'].'</td>
                    <td>'.$item['region']['name'].'</td>
                  </tr>';
        }
        echo '</table>';
        echo '<script type="text/javascript">
                $(function(){
                    $(".storm-zone").click(function(event){
                        event.preventDefault();

                        val = $(this).parent().find("input[name=\'zone-id\']").val();
                        $("#load-storm-zone").parent().find("input").val(val);
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

        //Zones
        require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkZone.php';
        $zone = new StormOnDemandNetworkZone($username, $password);

        //Zones
        $configurable_options[3] = array
        (
            'Name'      =>  'zone|Zone',
            'Type'      =>  'select',
            'Values'    =>  array()
        );

        $ret = $zone->lists();
        foreach($ret['items'] as $item)
        {
            $configurable_options[3]['Values'][$item['id']] = $item['region']['name'].' - '.$item['name'];
        }


        // Storage Size
        $configurable_options[] = array
        (
            'Name'      =>  'size|Size',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                '150'    =>  '150GB',
                '200'    =>  '200GB',
                '300'    =>  '300GB'
            )
        );

        $configurable_options[] = array
        (
            'Name'      =>  'crossAttaching|Cross Attaching',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                '1'    =>  'Yes',
                '0'    =>  'No',
            )
        );



        //Create groups
        $groups =   array();
        $groups[] = array
        (
            'Name'          =>  'Configurable Options For Liquid Web SBS Module',
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
            'Description'   =>  '<a id="load-storm-zone" href="stormajax=load-zone" class="load-configuration">Load Zone</a>'
        ),
        "Size"=>  array
        (
            'Type'          =>  'text',
            'Size'          =>  '25',
            'Default'       =>  '150',
            'Description'   =>  'Range 150 GB - 15000 GB'
        ),
         "Cross Attaching"   =>  array
        (
            'Type'          =>  'yesno',
            'Description'   =>  'Cross attaching allows a volume to be attached to multiple servers.'
        ),
    );


    if(basename($_SERVER["SCRIPT_NAME"]) == 'configproducts.php' && $_GET['action'] != 'save')
    {
        $config['Zone']['Description'] .= '<script type="text/javascript">
                jQuery(function(){
                  jQuery( document ).ready(function() {
                    jQuery(".load-configuration").click(function(event){
                        event.preventDefault();
                        if($("#conf-dialog").is(":data(dialog)"))
                        {
                            $("#conf-dialog").dialog("destroy");
                        }
                        $("#conf-dialog").attr("title", $(this).html());
                        $("#ui-id-3").html($(this).html());
                        $("#conf-dialog").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWebSBS/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
                        $("#conf-dialog").dialog({minWidth: 650});

                        val = $(this).parent().find("input").val();
                        jQuery.post("configproducts.php?action=edit&id='.$_REQUEST['id'].'&conf_id="+val,jQuery(this).attr("href"), function(data){
                            $("#conf-dialog").html(data);
                        });
                    });
                  });
                });
              </script>
              <div id="conf-dialog" style="display:none;" title="">
              </div>';
    }


    $newVersion = LiquidWebSBSForWHMCS_getLatestVersion();

    $script = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR) + 1);

    if($newVersion && $script == 'configproducts.php' && $_GET['action'] != 'save')
    {
        echo '<p style="text-align: center;" class="infobox op_version">
            <span style="font-weight: bold">New version of Storm On Demand module is available!</span>
            <span style="font-weight: bold"><br />Check this address to find out more <a target="_blank" href="'.$newVersion['site'].'">'.$newVersion['site'].'</a></span>
         </p>';
    }

    if($script == 'configproducts.php'){
        $testConnection = LiquidWebSBS_checkConnection();
    }else{
        $testConnection = true;
    }

    if($testConnection)
    {
        return $config;
    }
    else
    {
        foreach ($config as $key => $value)
        {
            if($key != 'Username' && $key != 'Password')
            {
                unset($config[$key]);
            }
        }

        return $config;
    }
}

/**
 * This function is called when a new product is due to be provisioned.
 *
 * @param array $params
 * @return string
 */
function LiquidWebSBS_CreateAccount($params) {



    try{
          $username           =   LiquidWebSBS_getOption('Username', $params);
          $password           =   LiquidWebSBS_getOption('Password', $params);
          $zone =   $params['configoptions']['zone'] ? $params['configoptions']['zone'] : LiquidWebSBS_getOption('Zone', $params);
          $size =   $params['configoptions']['size'] ? $params['configoptions']['size'] : LiquidWebSBS_getOption('Size', $params);



          $q = mysql_query("SELECT tblproducts.* FROM tblhosting LEFT JOIN tblproducts ON tblhosting.packageid = tblproducts.id WHERE tblhosting.id = " . (int)$_REQUEST['id'] . " LIMIT 1");
          $row = mysql_fetch_assoc($q);

          if($zone == null){
            $zone = $row['configoption4'];
          }

          if($size == null ){
            $size = $row['configoption5'];
          }

          $crossAttaching = (boolean) (isset($params['configoptions']['crossAttaching']) ? $params['configoptions']['crossAttaching'] : LiquidWebSBS_getOption('Cross Attaching', $params));
          //Custom Field
          if(LiquidWebSBS_getOption('Cross Attaching', $params) == null && !isset($params['configoptions']['crossAttaching'])){
            if($row['configoption6'] == 'on'){
              $crossAttaching = true;
            }
          }

          if(!isset($params['customfields']['uniq_id']) && !MG_CustomField::isField('uniq_id', 'product', $params['packageid'])){
                MG_CustomField::create('product', $params['packageid'], 'uniq_id|Uniq ID', 'text', 'Uniq ID','','', 'on');
          }

          $uniq_id = new MG_CustomField(null, "uniq_id",  'product', $params['packageid'], $params['serviceid']);
          $storage = new StormOnDemand_Storage($username, $password, 'bleed');
          $storage->setDebug(true);
          $domain           =   $params['customfields']['hostname'] ? $params['customfields']['hostname'] : $params['domain'];

          $res  = $storage->create($domain, $size, null, $crossAttaching, null, $zone);

          if($error = $storage->getError()){
          		$crossAttaching = (bool) $crossAttaching;
                  throw new Exception ($error);
          }
          $uniq_id->setValue($res['uniq_id']);

          return "success";
    } catch (Exception $ex) {
          return $ex->getMessage();
    }

}

/**
 * FUNCTION proxmoxVPS_ChangePackage
 * This function is used for upgrading and downgrading of products.
 * @param array $params
 * @return string
 */
function LiquidWebSBS_ChangePackage($params) {
      if (!$params['customfields']['uniq_id'])
		return 'Custom Field /Uniq ID/ is empty';
      try{
            $username           =   LiquidWebSBS_getOption('Username', $params);
            $password           =   LiquidWebSBS_getOption('Password', $params);
            $storage = new StormOnDemand_Storage($username, $password, 'bleed');
            $storage->setDebug(true);
            $newSize = $params['configoptions']['size'] ? $params['configoptions']['size'] : LiquidWebSBS_getOption('Size', $params);

            $res = $storage->resize($params['customfields']['uniq_id'], $newSize);
            if($error = $storage->getError())
                  throw new Exception ($error);
            $details = $storage->details($params['customfields']['uniq_id']);
            $crossAttaching = (boolean) isset($params['configoptions']['crossAttaching']) ? $params['configoptions']['crossAttaching'] : LiquidWebSBS_getOption('Cross Attaching', $params);
            if($crossAttaching != (boolean) $details['cross_attach']){
                 $storage->update($crossAttaching, null,$params['customfields']['uniq_id']);
            }
            if($error = $storage->getError())
                  throw new Exception ($error);
            return "success";

      } catch (Exception $ex) {
            return $ex->getMessage();
      }
}

/**
 * This function is called when a termination is requested.
 *
 * @param array $params
 * @return string
 */
function LiquidWebSBS_TerminateAccount($params) {
      if (!$params['customfields']['uniq_id'])
		return 'Custom Field /Uniq ID/ is empty';
      try{
            $username           =   LiquidWebSBS_getOption('Username', $params);
            $password           =   LiquidWebSBS_getOption('Password', $params);
            $storage = new StormOnDemand_Storage($username, $password, 'bleed');
            $storage->delete($params['customfields']['uniq_id']);
            if($error = $storage->getError())
                  throw new Exception ($error);
            $uniq_id = new MG_CustomField(null, "uniq_id",  'product', $params['packageid'], $params['serviceid']);
            $uniq_id->setValue("");
            return "success";

      } catch (Exception $ex) {
            return $ex->getMessage();
      }
}

function LiquidWebSBS_getOption($option, $params)
{
    $config = LiquidWebSBS_ConfigOptions();

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

function LiquidWebSBS_ClientArea($params) {
      $module =  basename(dirname(__FILE__)); //Eg. module name is LiquidWebSBS
      try {

             $clientArea = new LiquidWebSBS($params['serviceid']);
             $clientArea->init($params);
             return $clientArea->run('index', $params);
      } catch (Exception $ex) {
            return array(
               'templatefile' => 'templates/index',
               'breadcrumb'   => ' > <a href="#">Management</a>',
               'vars'         => array("errors" => array( $ex->getMessage()))
            );
      }

}

/****************** MODULE INFORMATION ************************/

//Register instance
LiquidWebSBSForWHMCS_registerInstance();
function LiquidWebSBSForWHMCS_registerInstance()
{
    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   "Liquid Web SBS For WHMCS";
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   LIQUID_WEB_SBS_VERSION;
    //Encryption key
    $moduleKey          =   "hqao9lV2xxXUyTQmwRPEVnk8TPdMNGOKznFuRgTdyE24fK8muWVxz2DDuUDVKzCg";
    //Server URL
    $serverUrl          =   "https://www.liquidweb.com/manage/modules/addons/ModuleInformation/server.php";



    /***************************************************
     *                      DO NOT TOUCH!
     ***************************************************/

    //Load API Class
    require_once ROOTDIR.DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."modulesgarden".DIRECTORY_SEPARATOR."class.ModuleInformationClient.php";

    //Is Already Registered?
    $currentVersion = ModuleInformationClient::getLocalVersion($moduleName);
    if($currentVersion == $moduleVersion)
    {
        return false;
    }

    //Create Client Class
    $client = new ModuleInformationClient( $moduleName, $moduleKey);

    //Register current instance
    $ret = $client->registerModuleInstance($moduleVersion, $_SERVER["SERVER_ADDR"], $_SERVER["SERVER_NAME"]);

    if($ret->status == 1)
    {
        ModuleInformationClient::clearCache($moduleName, "getLatestModuleVersion");
        ModuleInformationClient::setLocalVersion($moduleName, $moduleVersion);
    }
}

function LiquidWebSBSForWHMCS_getLatestVersion()
{
    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   "Liquid Web SBS For WHMCS";
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   LIQUID_WEB_SBS_VERSION;
    //Encryption key
    $moduleKey          =   "hqao9lV2xxXUyTQmwRPEVnk8TPdMNGOKznFuRgTdyE24fK8muWVxz2DDuUDVKzCg";
    //Server URL
    $serverUrl          =   "https://www.liquidweb.com/manage/modules/addons/ModuleInformation/server.php";


    /***************************************************
     *                      DO NOT TOUCH!
     ***************************************************/

    //Load API Class
    require_once ROOTDIR.DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."modulesgarden".DIRECTORY_SEPARATOR."class.ModuleInformationClient.php";

    //Is Already Registered?
    $currentVersion = ModuleInformationClient::getLocalVersion($moduleName);
    if(!$currentVersion)
    {
        return false;
    }

    //Is current instance registered?
    if($currentVersion != $moduleVersion)
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
        "version"   =>  $res->data->version,
        "site"      =>  $res->data->site,
    );
}