<?php

/* * ********************************************************************
 *
 *
 *  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *  AUTHOR                         ->       dariusz.bi@modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
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
 * *********************************************************************/

/**
 * User Predefined Report
 *
 * @author Dariusz Bijos <dariusz.bi@modulesgarden.com>
 * @link http://modulesgarden.com ModulesGarden - Top Quality Custom Software Development
 */

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");


if(function_exists('mysql_safequery') == false) {
    function mysql_safequery($query,$params=false) {
        if ($params) {
            foreach ($params as &$v) { $v = mysql_real_escape_string($v); }
            $sql_query = vsprintf( str_replace("?","'%s'",$query), $params );
            $sql_query = mysql_query($sql_query);
        } else {
            $sql_query = mysql_query($query);
        }

        return ($sql_query);
    }
}

if(!function_exists('mysql_get_array'))
{
    function mysql_get_array($query, $params = false)
    {
        $q = mysql_safequery($query, $params);
        $arr = array();
        while($row = mysql_fetch_assoc($q))
        {
            $arr[] = $row;
        }

        return $arr;
    }
}

if(!function_exists('mysql_get_row'))
{
    function mysql_get_row($query, $params = false)
    {
        $q = mysql_safequery($query, $params);
        $row = mysql_fetch_assoc($q);
        return $row;
    }
}

//Directory Separator
defined('DS') ? null : define('DS',DIRECTORY_SEPARATOR);
//Init (Autoload and Other stuff)



if(file_exists(dirname(__FILE__).DS.'moduleVersion.php')){
    require_once dirname(__FILE__).DS.'moduleVersion.php';
     define('STORM_SERVERS_WIDGET_VERSION', $moduleVersion);
}else{
     define('STORM_SERVERS_WIDGET_VERSION', 'Development Version');
}



function LiquidAndStormWidget_config() {

    $roles = array();
    $result = mysql_safequery("SELECT id,name FROM tbladminroles");
    while($row = mysql_fetch_assoc($result)){
        $roles[$row['id']] = $row['name'];
    }

    $rolescheckliquid = "";
    $rolescheckdemond = "";
    $rolescron        = "";
    $module_liquidroles = mysql_get_row(" SELECT `value` FROM `tbladdonmodules` WHERE setting = 'liquidroles' ");
    $module_stormroles  = mysql_get_row(" SELECT `value` FROM `tbladdonmodules` WHERE setting = 'stormroles' ");
    $module_cronroles   = mysql_get_row(" SELECT `value` FROM `tbladdonmodules` WHERE setting = 'cronroles' ");
    $module_emailtpl    = mysql_get_row(" SELECT `value` FROM `tbladdonmodules` WHERE setting = 'emailtplid' ");

    foreach($roles as $id => $name){

      if(strpos($module_liquidroles['value'], (string)$id) !== false){
        $rolescheckliquid .= "<label><input type='checkbox' name='rolescheckliquid[]' value='{$id}' checked='checked' /> {$name}</label>";
      }else{
        $rolescheckliquid .= "<label><input type='checkbox' name='rolescheckliquid[]' value='{$id}' /> {$name}</label>";
      }

      if(strpos($module_stormroles['value'], (string)$id) !== false){
        $rolescheckdemond .= "<label><input type='checkbox' name='rolescheckdemond[]' value='{$id}' checked='checked' /> {$name}</label>";
      }else{
        $rolescheckdemond .= "<label><input type='checkbox' name='rolescheckdemond[]' value='{$id}' /> {$name}</label>";
      }

      if(strpos($module_cronroles['value'], (string)$id) !== false){
        $rolescron .= "<label><input type='checkbox' name='cronroles[]' value='{$id}' checked='checked' /> {$name}</label>";
      }else{
        $rolescron .= "<label><input type='checkbox' name='cronroles[]' value='{$id}' /> {$name}</label>";
      }
    }

    $jsscript = <<<EOT
        <script type="text/javascript">
            $(document).ready(function(){
              $("input[name='access[LiquidAndStormWidget][1]']").parent().parent().parent().hide();
            });
        </script>
EOT;

    $emailurl = 'configemailtemplates.php?action=edit&id='.$module_emailtpl['value'];

    $newVersion = LiquidWebStormServersForWHMCS_getLatestVersion();
    $script = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR) + 1);
    $description = "This addon allows you to configure widgets as well as define email notifications.";

    if($newVersion && $script == 'configaddonmods.php')
    {
        $description .=  '<p><span class="label closed">New version</span> of  Liquid Web Cloud Servers Billing is available! <span><br>Check this address to find out more <a target="_blank" href="'.$newVersion['site'].'">'.$newVersion['site'].'</a></span></p>';
    }

    $configarray = array(
    "name"        => "Liquid Web Widget For WHMCS",
    "description" => $description,
    "version"     => STORM_SERVERS_WIDGET_VERSION,
    "author"      => '<a href="http://www.liquidweb.com" targer="_blank">Liquid Web</a>',
    "language"    => "english",
    "fields"      => array(
        //"showliquidwidget"  => array ("FriendlyName" => "Enable Display Liquid Web Widget",           "Type" => "",         "Size"    => "25", "Description" => "{$rolescheckliquid}", "Default" => "checked" ),
        //"showstormwidget"   => array ("FriendlyName" => "Enable Display Storm On Demond Widget",      "Type" => "",         "Size"    => "25", "Description" => "{$rolescheckdemond}", "Default" => "checked" ),
        "alert"             => array ("FriendlyName" => "Set A Low Inventory Threshold",              "Type" => "text",     "Size"    => "1",  "Description" => "Alert specific group if zone availability go below this value.", "Default" => "3" ),
        "cronmail"          => array ("FriendlyName" => "Cron Mail",                                  "Type" => "yesno",    "Size"    => "25", "Description" => "Run cron that will send an email to admins once a day with the list of products that are out of stock or at low availability level.<br /><a href='".$emailurl."' style='color:blue;'>Email Template</a>".$jsscript, "Default" => "checked" ),
        "cronrolesids"      => array ("FriendlyName" => "Send Cron Alert Mail To",                    "Type" => "",         "Size"    => "25", "Description" => "{$rolescron}", "Default" => "checked" ),
    ));
    return $configarray;
}

function LiquidAndStormWidget_activate() {

  $name = 'Liquid Web Cloud Servers For WHMCS';
  $template = mysql_get_row("SELECT * FROM `tblemailtemplates` WHERE `name` = ?", array( $name ));
  $template_module = mysql_get_row("SELECT * FROM `tbladdonmodules` WHERE `setting` = ?", array( "emailtplid" ));
  if($template == false){
    $type = 'admin';
    $subject = '{if $attention eq "1"} Attention !!! {/if}';
    $message = '<p>{if $liquid_product_exist eq "1"}</p>
                <p><strong>Liquid Web Products</strong></p>
                <ul>{foreach from=$liquid_product_array item=liquid_product}
                <li>  {$liquid_product}</li>
                {/foreach}</ul>
                {/if} <br /><br />
                <div><br />{if $storm_product_exist eq "1"}
                <p><strong>Storm On Demand Products</strong></p>
                <ul>{foreach from=$storm_product_array item=storm_product }
                <li>  {$storm_product}</li>
                {/foreach}</ul>
                {/if}</div>
                <p><a href="{$whmcs_admin_url}">{$whmcs_admin_url}</a></p>';

    mysql_safequery("INSERT INTO `tblemailtemplates` (`type`,`name`,`subject`,`message`) VALUES(?,?,?,?)", array($type, $name, $subject, $message));
    $new_template = mysql_get_row("SELECT * FROM `tblemailtemplates` WHERE `name` = ?", array( $name ));


    if($template_module == false){
      mysql_safequery("INSERT INTO `tbladdonmodules` (`module`,`setting`,`value`) VALUES (?,?,?)", array("LiquidAndStormWidget","emailtplid",$new_template['id']));
    }else{
      mysql_safequery("UPDATE `tbladdonmodules` SET value = ? WHERE setting = ?", array($new_template['id'],"emailtplid"));
    }
  }else{
    if($template_module == false){
      mysql_safequery("INSERT INTO `tbladdonmodules` (`module`,`setting`,`value`) VALUES (?,?,?)", array("LiquidAndStormWidget","emailtplid",$template['id']));
    }else{
      mysql_safequery("UPDATE `tbladdonmodules` SET value = ? WHERE setting = ?", array($template['id'],"emailtplid"));
    }
  }
    $sql =   "CREATE TABLE IF NOT EXISTS `tblwidgetvpsdetails` (
                  `id` int(10) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),
                  `product_id` int(10) NOT NULL,
                  `product_name` text NOT NULL,
        		  `domain` text NOT NULL,
        		  `package_id` int(10) NOT NULL,
        		  `hosting_id` int(11) NOT NULL,
        		  `configoption1` text NOT NULL,
        		  `configoption2` text NOT NULL,
        		  `configoption7` text NOT NULL,
        		  `uniq_id` varchar(11) NOT NULL,
        		  `is_zone_available` int(1) NOT NULL DEFAULT '0',
        	   	  `zone_available` text NOT NULL
    		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    mysql_safequery($sql);

    $sql =   "CREATE TABLE IF NOT EXISTS `tblwidgetppdetails` (
            	`id` INT(10) NOT NULL AUTO_INCREMENT,
            	`domain` TEXT NOT NULL,
            	`total_memory` DOUBLE NOT NULL,
            	`total_diskspace` DOUBLE NOT NULL,
            	`used_memory` DOUBLE NOT NULL,
            	`used_diskspace` DOUBLE NOT NULL,
            	PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    mysql_safequery($sql);
}

function LiquidAndStormWidget_deactivate() {
  $name = 'Liquid Web Alert';
  mysql_safequery("DELETE FROM `tblemailtemplates` WHERE `name` = ?", array($name));
  mysql_safequery("DROP TABLE `tblwidgetdetails`");
  mysql_safequery("DROP TABLE `tblwidgetppdetails`");
}

function LiquidAndStormWidget_output($params) {

}

function LiquidAndStormWidget_upgrade($vars) {

    logActivity(print_r($vars, true));

    $version = (int)str_replace('.','', $vars['version']);
    if($version < 100) {
        $version *= 10;
    }

    if($version < 124) {
        $sql =   "CREATE TABLE IF NOT EXISTS `tblwidgetvpsdetails` (
                  `id` int(10) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),
                  `product_id` int(10) NOT NULL,
                  `product_name` text NOT NULL,
    			  `domain` text NOT NULL,
    			  `package_id` int(10) NOT NULL,
    			  `hosting_id` int(11) NOT NULL,
    			  `configoption1` text NOT NULL,
    			  `configoption2` text NOT NULL,
    			  `configoption7` text NOT NULL,
    			  `uniq_id` varchar(11) NOT NULL,
    			  `is_zone_available` int(1) NOT NULL DEFAULT '0',
    		   	  `zone_available` text NOT NULL
    			  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        mysql_safequery($sql);

        $sql =   "CREATE TABLE IF NOT EXISTS `tblwidgetppdetails` (
                	`id` INT(10) NOT NULL AUTO_INCREMENT,
                	`domain` TEXT NOT NULL,
                	`total_memory` DOUBLE NOT NULL,
                	`total_diskspace` DOUBLE NOT NULL,
                	`used_memory` DOUBLE NOT NULL,
                	`used_diskspace` DOUBLE NOT NULL,
                	PRIMARY KEY (`id`)
                )
                ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        mysql_safequery($sql);
    }
}


/****************** MODULE INFORMATION ************************/

//Register instance
LiquidWebStormServersForWHMCS_registerInstance();
function LiquidWebStormServersForWHMCS_registerInstance()
{
    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   "Liquid Web Cloud Servers For WHMCS";
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   STORM_SERVERS_WIDGET_VERSION;
    //Encryption key
    $moduleKey          =   "geBrbObvFPJHzHtTq9dRvl60DFnOyN5oUdZsVROMWps7bnhvUg7KFSDl65I23euI";
    //Server URL
    //$serverUrl          =   "https://www.liquidweb.com/manage/modules/addons/ModuleInformation/server.php";



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
    $client = new ModuleInformationClient($moduleName, $moduleKey);

    //Register current instance
    $ret = $client->registerModuleInstance($moduleVersion, $_SERVER["SERVER_ADDR"], $_SERVER["SERVER_NAME"]);

    if($ret->status == 1)
    {
        ModuleInformationClient::clearCache($moduleName, "getLatestModuleVersion");
        ModuleInformationClient::setLocalVersion($moduleName, $moduleVersion);
    }
}

function LiquidWebStormServersForWHMCS_getLatestVersion()
{
    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   "Liquid Web Cloud Servers For WHMCS";
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   STORM_SERVERS_WIDGET_VERSION;
    //Encryption key
    $moduleKey          =   "geBrbObvFPJHzHtTq9dRvl60DFnOyN5oUdZsVROMWps7bnhvUg7KFSDl65I23euI";
    //Server URL
    //$serverUrl          =   "https://www.liquidweb.com/manage/modules/addons/ModuleInformation/server.php";


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