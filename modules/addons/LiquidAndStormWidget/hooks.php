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



if(isset($_REQUEST['action']) && $_REQUEST['action'] == "save" && isset($_REQUEST['msave_LiquidAndStormWidget'])){


    if(isset($_POST['rolescheckliquid']) && is_array($_POST['rolescheckliquid']) ){

      $roles = "";
      $i=0;
      foreach($_POST['rolescheckliquid'] as $rolescheckliquid){
        $i++;
        if($i == 1 ){
          $roles .= $rolescheckliquid;
        }else{
          $roles .= ",".$rolescheckliquid;
        }

        $role_widgets = mysql_get_row("SELECT `widgets` FROM `tbladminroles` WHERE id = ?", array($rolescheckliquid));
        if(strpos($role_widgets['widgets'], 'oneAvailability') === false){
          $new_role_widgets = $role_widgets['widgets'].',oneAvailability';
          mysql_safequery("UPDATE `tbladminroles` SET widgets = ? WHERE id = ?", array($new_role_widgets,$rolescheckliquid));
        }
      }

      $module_custom_param = mysql_get_row("SELECT * FROM `tbladdonmodules` WHERE module ='LiquidAndStormWidget' AND setting ='liquidroles'");
      if($module_custom_param == false){
        mysql_safequery("INSERT INTO `tbladdonmodules` (`module`,`setting`,`value`) VALUES (?,?,?)", array("LiquidAndStormWidget","liquidroles",$roles));
      }else{
        mysql_safequery("UPDATE `tbladdonmodules` SET `value` = ? WHERE `module` = ? AND `setting`= ?", array($roles,"LiquidAndStormWidget","liquidroles" ));
      }
    }



    if(isset($_POST['rolescheckdemond']) && is_array($_POST['rolescheckdemond']) ){
      $roles = "";
      $i=0;
      foreach($_POST['rolescheckdemond'] as $rolescheckdemond){
        $i++;
        if($i == 1 ){
          $roles .= $rolescheckdemond;
        }else{
          $roles .= ",".$rolescheckdemond;
        }

        $role_widgets = mysql_get_row("SELECT `widgets` FROM `tbladminroles` WHERE id = ?", array($rolescheckdemond));
        if(strpos($role_widgets['widgets'], 'DemandZoneAvailability') === false){
          $new_role_widgets = $role_widgets['widgets'].',DemandZoneAvailability';
          mysql_safequery("UPDATE `tbladminroles` SET widgets = ? WHERE id = ?", array($new_role_widgets,$rolescheckdemond));
        }
      }

      $module_custom_param = mysql_get_row("SELECT * FROM `tbladdonmodules` WHERE module ='LiquidAndStormWidget' AND setting ='stormroles'");
      if($module_custom_param == false){
        mysql_safequery("INSERT INTO `tbladdonmodules` (`module`,`setting`,`value`) VALUES (?,?,?)", array("LiquidAndStormWidget","stormroles",$roles));
      }else{
        mysql_safequery("UPDATE `tbladdonmodules` SET `value` = ? WHERE `module` = ? AND `setting`= ?", array($roles,"LiquidAndStormWidget","stormroles" ));
      }

    }
    if(isset($_POST['cronroles']) && is_array($_POST['cronroles']) ){
      $roles = "";
      $i=0;
      foreach($_POST['cronroles'] as $cronroles){
        $i++;
        if($i == 1 ){
          $roles .= $cronroles;
        }else{
          $roles .= ",".$cronroles;
        }
      }

      $module_custom_param = mysql_get_row("SELECT * FROM `tbladdonmodules` WHERE module ='LiquidAndStormWidget' AND setting ='cronroles'");

      if($module_custom_param == false){
        mysql_safequery("INSERT INTO `tbladdonmodules` (`module`,`setting`,`value`) VALUES (?,?,?)", array("LiquidAndStormWidget","cronroles",$roles));
      }else{

        mysql_safequery("UPDATE `tbladdonmodules` SET `value` = ? WHERE `module` = ? AND `setting`= ?", array($roles,"LiquidAndStormWidget","cronroles" ));
      }

    }
}


if(isset($_SESSION['adminid']) && $_SESSION['adminid'] && isset($_REQUEST['ZoneAvailabilityProducts']))
{
    ob_clean();
    ob_start();

    $table = '<table width="100%" bgcolor="#cccccc" cellspacing="1" align="center"><tbody><tr bgcolor="#efefef" style="text-align:center;font-weight:bold;"><td>Product Name</td><td>Config ID</td><td>Available Zone</td></tr>';

    require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormConfig.php';
    //load server class
    require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormServer.php';

    require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandNetworkZone.php';

    //load server helper class
    require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'modulesgarden' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'class.StormOnDemand_Helper.php';

    //Load API Class
    require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'modulesgarden' . DIRECTORY_SEPARATOR . 'class.ModuleInformationClient.php';

    $query_mg_storm_on_demand_products = mysql_get_array("SELECT mg_storm_on_demand.hosting_id , mg_storm_on_demand.uniq_id ,tblhosting.id ,tblhosting.packageid , tblhosting.domain, tblhosting.domainstatus,tblproducts.id,tblproducts.name FROM mg_storm_on_demand
                                              LEFT JOIN tblhosting ON  mg_storm_on_demand.hosting_id = tblhosting.id
                                              LEFT JOIN tblproducts ON  tblhosting.packageid = tblproducts.id
                                              ORDER BY tblproducts.name;
                                             "
                                            );

    $query_liquid_products = mysql_get_array("SELECT mg_liquid_web.hosting_id , mg_liquid_web.uniq_id ,tblhosting.id ,tblhosting.packageid , tblhosting.domain, tblhosting.domainstatus,tblproducts.id,tblproducts.name FROM mg_liquid_web
                                              LEFT JOIN tblhosting ON  mg_liquid_web.hosting_id = tblhosting.id
                                              LEFT JOIN tblproducts ON  tblhosting.packageid = tblproducts.id
                                              ORDER BY tblproducts.name;
                                             "
                                            );

    $products = array_merge($query_mg_storm_on_demand_products, $query_liquid_products);

    $existone = false;
    foreach($products as $productDetails){
      if($productDetails['domainstatus'] == 'Active'){
        $existone = true;
        $table .= '<tr bgcolor="#ffffff" style="text-align:center;"><td>'.$productDetails['name'].' - '.$productDetails['domain'].'</td>';

        $q = mysql_query("SELECT * FROM tblproducts WHERE id = " . (int)$productDetails['packageid'] . " LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $get_alert = mysql_query("SELECT * FROM tbladdonmodules WHERE module = 'LiquidAndStormWidget' and setting='alert' LIMIT 1");
        $alert = mysql_fetch_assoc($get_alert);

        $username = $row['configoption1'];
        $password = $row['configoption2'];
        //$password = StormOnDemand_Helper::encrypt_decrypt($row['configoption2']);

        $zoneSelected   = (int) $row['configoption4'];
        $configSelected = (int) $row['configoption7']; // Id config

        $table .= '<td>'.$configSelected.'</td>';

        $config = new StormOnDemandStormConfig($username, $password);
        $zone   = new StormOnDemandNetworkZone($username, $password);

        $res = $config->ping();

        if(isset($res['ping']) && $res['ping'] != 'success')
        {
          $table .= '<td>Connect Error</td></tr>';
        }else{
            $avilablezones = $config->details($configSelected);

          $avhtml ='';
          if(count($avilablezones['zone_availability']) > 0){
            foreach($avilablezones['zone_availability'] as $zoneid => $avilablezoneid){
              $ret = $zone->details($zoneid);
              $avhtml .= $ret['name'].' - '.$ret['region']['name'].'<br>';
            }
          }else{
              $avhtml .= 'Out of stock';
          }

          if(count($avilablezones['zone_availability']) < (int)$alert['value']){
            $table .= '<td style="color:red;">'.$avhtml.'</td></tr>';
          }else{
            $table .= '<td>'.$avhtml.'</td></tr>';
          }
        }
      }
    }
    if($existone == false){
      $table .= '<tr bgcolor="#ffffff" style="text-align:center;"><td>No Products Exist</td></tr>';
    }
    $table .= '</tbody></table>';

    $tables = array
    (
        'mg_LiquidWebPrivateParentProduct',
        'mg_StormOnDemandPrivateParentProduct'
    );

    $fields = array();
    foreach($tables as $op_table)
    {
        $q = mysql_query(
            "SELECT ".$op_table.".* "
            . "FROM ".$op_table." "
            . "LEFT JOIN "
                . "tblproducts "
            . "ON "
                .$op_table.".product_id = tblproducts.id "
            . "WHERE "
                . "(".$op_table.".setting = 'Username' ". "OR ".$op_table.".setting = 'Password') "
            . "AND "
                . "tblproducts.id IS NOT NULL "
            . "AND "
                . "(tblproducts.servertype = 'LiquidWebPrivateParent' OR tblproducts.servertype = 'StormOnDemandPrivateParent')"
        );

        if($q && mysql_num_rows($q))
        {
            while($row = mysql_fetch_assoc($q))
            {
                $fields[$row['product_id']][$row['setting']] = $row['value'];
            }
        }
    }

    $details = array();

    foreach($fields as $product_id => $field)
    {
        $found = false;
        foreach($details as $d)
        {
            if($d['Username'] == $field['Username'] && $field['Password'] == $d['Password'])
            {
                $found = true;
                break;
            }
        }

        if(!$found)
        {
            $details[$product_id] = $field;
        }
    }

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormPrivateParent.php';

    $items = array();
    $content = '';

    foreach($details as $d)
    {
        $private = new StormOnDemandStormPrivateParent($d['Username'], $d['Password'], 'bleed');

        $page = 1;
        $items_per_page = 250;

        $lists = $private->lists($page, $items_per_page);

        if(!$lists)
        {
            $content .= '<p style="color: red">Cannot Connect! '.$private->getError().'</p>';
        }
        else
        {
            $items = array_merge($items, $lists['items']);
            while($lists['item_total'] > $page * $items_per_page)
            {
                $page++;
                $lists = $private->lists($page, $items_per_page);
                $items = array_merge($items, $lists['items']);
            }
        }
    }

    //Delete Doubled Serves
    $uniq = array();
    foreach($items as $item)
    {
        $found = false;
        foreach($uniq as $u)
        {
            if($item['accnt'] == $u['accnt'] && $item['uniq_id'] == $u['uniq_id'])
            {
                $found = true;
                break;
            }
        }

        if(!$found)
        {
            $uniq[] = $item;
        }
    }

    if($uniq)
    {
        $content .= '<table style="width: 100%">';
        foreach($uniq as $item)
        {
            $content .= '<tr>
                            <td><b>'.$item['domain'].'</b></td>
                            <td>'.$item['resources']['memory']['total'].' MB RAM</td>
                            <td>'.$item['resources']['diskspace']['total'].' GB Disk</td>
                         </tr>
                         <tr>
                            <td rowspan="2">Used</td>
                            <td>'.$item['resources']['memory']['used'].' MB - '.number_format(($item['resources']['memory']['used'] / $item['resources']['memory']['total']) * 100, 2).'%</td>
                            <td>'.$item['resources']['diskspace']['used'].' GB - '.number_format(($item['resources']['diskspace']['used'] / $item['resources']['diskspace']['total']) * 100, 2).'%</td>
                         </tr>
                         <tr>
                            <td>
                                <div style="width: 95%; height: 10px; border: 1px #000 solid; border-radius: 2px">
                                    <div style="background-color: '.((($item['resources']['memory']['used'] / $item['resources']['memory']['total']) * 100)> 90 ? '#FF1111' : '#178DBE').'; height: 10px; width: '.number_format(($item['resources']['memory']['used'] / $item['resources']['memory']['total']) * 100).'%"></div>
                                </div>
                            </td>
                            <td>
                                <div style="width: 95%; height: 10px; border: 1px #000 solid; border-radius: 2px">
                                    <div style="background-color: '.((($item['resources']['diskspace']['used'] / $item['resources']['diskspace']['total']) * 100)> 90 ? '#FF1111' : '#178DBE').'; height: 10px; width: '.number_format(($item['resources']['diskspace']['used'] / $item['resources']['diskspace']['total']) * 100).'%"></div>
                                </div>
                            </td>
                         </tr>
                         <tr><td colspan="3" style="height: 10px"></td></tr>';
        }

        $content .= '</table>

        <div align="right"; class="widget-footer">';
        if (($CONFIG['Template'] == 'six') || (LW_CUSTOM_TEMPLATE_SIX == 'YES')){
            $content = $content.'<a href="addonmodules.php?module=StormBilling&action=setup" class="btn btn-sm" style="color:#fff; background-color:#333333">';
        } else {
            $content = $content.'<a href="addonmodules.php?module=StormBilling&action=setup" class="btn btn-sm">';
        }
        $content = $content.'Goto Liquid Web Product Setup Wizard</a></div>';
    }
    else
    {
        $content .= '<p style="text-align: center; font-weight: bold">You do not have any private servers in Storm On Demand or Liquid Web</p>


                <div align="right"; class="widget-footer">';
                if (($CONFIG['Template'] == 'six') || (LW_CUSTOM_TEMPLATE_SIX == 'YES')){
                    $content = $content.'<a href="addonmodules.php?module=StormBilling&action=setup" class="btn btn-sm" style="color:#fff; background-color:#333333">';
                } else {
                    $content = $content.'<a href="addonmodules.php?module=StormBilling&action=setup" class="btn btn-sm">';
                }
                $content = $content.'Goto Liquid Web Product Setup Wizard</a></div>';
    }
    echo $table.'<hr />'.$content;

    require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'modulesgarden' . DIRECTORY_SEPARATOR . 'class.ModuleInformationClient.php';

    if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'moduleVersion.php')){
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'moduleVersion.php';
         define('STORM_SERVERS_WIDGET_VERSION', $moduleVersion);
    }else{
         define('STORM_SERVERS_WIDGET_VERSION', 'Development Version');
    }

    //Set up name for your module.
    $moduleName         =   'Liquid Web Storm Servers For WHMCS';
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   STORM_SERVERS_WIDGET_VERSION;
    //Encryption key
    $moduleKey          =   'geBrbObvFPJHzHtTq9dRvl60DFnOyN5oUdZsVROMWps7bnhvUg7KFSDl65I23euI';

    //Create Client Class
    $client = new ModuleInformationClient($moduleName, $moduleKey);

    //Try to register current instance
    $ret = $client->registerModuleInstance($moduleVersion,  $_SERVER['SERVER_ADDR'], $_SERVER['SERVER_NAME']);
    $ret = json_decode($ret);


    //Save current module version in database
    if(isset($ret->tag_name)) {
        ModuleInformationClient::setLocalVersion($moduleName, $moduleVersion);
    }

    /*$hasModulesGardenWidget = mysql_query("SELECT a.id FROM tbladmins a
    WHERE a.id = ".(int)$_SESSION['adminid']." AND a.homewidgets LIKE '%GardenProductsWidget:true%'");
    //Check already existing modules
    if(!mysql_num_rows($hasModulesGardenWidget))
    {*/
        //Get Available products
        /*
        $ret = $client->getAvailableProducts();

        //Any errors?
        if($ret === false)
        {
            echo '<p style="text-align: center; color: #f00"><b>'.$client->getError().'</b></p>';
            exit;
        }


        $clientModules = array();

        foreach($ret->data->modules as $module)
        {
            if(!in_array($module->name, array('Liquid Web Storm Servers For WHMCS')))
            {
                continue;
            }

            $localVersion = ModuleInformationClient::getLocalVersion($module->name);

            if($localVersion)
            {
                $module->local_version  =   $localVersion;
                $clientModules[]        =   $module;
            }
        }
        */

        //if($clientModules && !empty($clientModules))

        $localVersion = ModuleInformationClient::getLocalVersion('Liquid Web Storm Servers For WHMCS');
        if ($ret->tag_name > $localVersion)
        {
            $out .= '<table style="width: 100%; margin-top: 10px">
                <thead>
                    <tr bgcolor="#efefef" style="text-align:center;font-weight:bold;">
                        <th>Module Name</th>
                        <th>Current Version</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';

                $out .= '<tr>
                            <td>Liquid Web and Storm On Demand Package</td>
                            <td>'.$localVersion.'</td>';

                if($ret->tag_name == $localVersion)
                {
                    $out .= '<td>Up to date</td>';
                }
                else
                {
                    $out .= '<td>New version is available. <a style="color: #0A0" href="'.$ret->tarball_url.'">Check it!</a></td>';

                    $out .= '<script type="text/javascript">
                    $(function() {
                        $("#dialog").dialog({
                    		width: 600,
                            modal: true,
                            position: { my: "top", at: "top+150" }
                        });
                      });

                    $( "#dialog" ).dialog( "open" );

                      </script>


                    <div id="dialog" title="Liquidweb">
                    	<img style="position:absolute; right:8px; width:230px; height:70px;" src="../modules/addons/LiquidAndStormWidget/lw-logo.png" alt="Liquid Web">
                    	<br/>
                      	<p>New version of Liquid Web addon available. <br/><a href="'.$ret->tarball_url.'">Click here to download</a></p>
                    </div>';

                }

                $out .=  '</tr>';

                $out .= '   </tbody>
                 </table>

                <div align="right"; class="widget-footer">';
                if (($CONFIG['Template'] == 'six') || (LW_CUSTOM_TEMPLATE_SIX == 'YES')){
                    $out = $out.'<a href="addonmodules.php?module=StormBilling&action=setup" class="btn btn-sm" style="color:#fff; background-color:#333333">';
                } else {
                    $out = $out.'<a href="addonmodules.php?module=StormBilling&action=setup" class="btn btn-sm">';
                }
                $out = $out.'Goto Liquid Web Product Setup Wizard</a></div>';

            echo $out;
        }
    //}

    ob_end_flush();
    exit;
}


function ZoneAvailability($vars) {
    $content = '<script type="text/javascript">
                    function ZoneAvailability()
                    {
                        $("#ZoneAvailability").html("<p style=\"text-align: center\"><img src=\"images/loading.gif\"/></p>");
                        jQuery.post(document.location.toString(), "ZoneAvailabilityProducts=1&ajax=1", function(data){
                            $("#ZoneAvailability").html(data);
                        });
                    }

                    jQuery(function(){
                        ZoneAvailability();
                        //setInterval(StormOnDemandZoneAvailability, 150000);
                    })
               </script>
               <div id="ZoneAvailability"></div>';

    return array( 'title' => 'Liquid Web Storm Servers', 'content' => $content );

}


function LiquidAndStormWidget_hook_DailyCronJobPreEmail($vars){

  $should_send = mysql_get_row("SELECT `value` FROM `tbladdonmodules` WHERE `setting` = ?", array("cronmail"));

  $zone_limit  = mysql_get_row("SELECT `value` FROM `tbladdonmodules` WHERE `setting` = ?", array("alert"));

  if($should_send != false && $should_send['value'] == 'on'){

    require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormConfig.php';
    //load server class
    require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandStormServer.php';

    require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'bleed' . DIRECTORY_SEPARATOR . 'class.StormOnDemandNetworkZone.php';

    //load server helper class
    require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'modulesgarden' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'class.StormOnDemand_Helper.php';

    $outofstock      =  mysql_get_array("SELECT `name` FROM `tblproducts` WHERE `stockcontrol` = 'on' AND `qty` <= 0");

    /////////////////////////-------- LIQUID ---------/////////////////////
    $liquid_products =  mysql_get_array("SELECT mg_liquid_web.hosting_id , mg_liquid_web.uniq_id ,tblhosting.id ,tblhosting.packageid , tblhosting.domain, tblhosting.domainstatus,tblproducts.id as productid,tblproducts.name FROM mg_liquid_web
                                              LEFT JOIN tblhosting ON  mg_liquid_web.hosting_id = tblhosting.id
                                              LEFT JOIN tblproducts ON  tblhosting.packageid = tblproducts.id
                                              ORDER BY tblproducts.name;
                                             "
                                        );

    $attention = 0;
    $liquid_product_zones = array();
    $liquid_products_activity = array();

    foreach($liquid_products as $liquid_product){
      if($liquid_product['domainstatus'] == 'Active'){

        $q = mysql_query("SELECT * FROM tblproducts WHERE id = " . (int)$liquid_product['packageid'] . " LIMIT 1");

        $row = mysql_fetch_assoc($q);

        $username = $row['configoption1'];
        $password = $row['configoption2'];
        //$password = StormOnDemand_Helper::encrypt_decrypt($row['configoption2']);

        $liquid_configSelected = (int) $row['configoption7']; // Id config

        $config = new StormOnDemandStormConfig($username, $password);
        $zone   = new StormOnDemandNetworkZone($username, $password);

        $res = $config->ping();

        if(!array_key_exists($liquid_configSelected,$liquid_product_zones)){
          if(isset($res['ping']) && $res['ping'] == 'success')
          {
            $liquid_avilablezones = $config->details($liquid_configSelected);
            $liquid_zones = array();

            foreach($liquid_avilablezones['zone_availability'] as $liquid_zoneid => $liquid_avilablezoneid){
              $ret = $zone->details($liquid_zoneid);
              $liquid_zones[] = $ret['name'].' - '.$ret['region']['name'];
            }

            $liquid_product_zones[$liquid_configSelected] = $liquid_zones;
            $liquid_products_activity[$liquid_product['id']] = $liquid_product_zones;
          }
        }else{
          $liquid_products_activity[$liquid_product['id']][$liquid_configSelected] = $liquid_product_zones[$liquid_configSelected];
        }
      }
    }

    $liquid_product_array = array();

    foreach($liquid_products_activity as $liquid_pid => $liquid_configid){
      foreach($liquid_configid as $count_zones){
        $liquid_availability = count($count_zones);
      }

      $liquid_produc_details =  mysql_get_row("SELECT tblhosting.id ,tblhosting.packageid , tblhosting.domain, tblhosting.domainstatus,tblproducts.id,tblproducts.name FROM tblhosting
                                              LEFT JOIN tblproducts ON  tblhosting.packageid = tblproducts.id
                                              WHERE tblhosting.id = ?
                                             ",array($liquid_pid));

      if($liquid_availability <= $zone_limit){
        $liquid_product_array[] = '<span style="color: #ff0000;">'.$liquid_produc_details['name'].' - '.$liquid_produc_details['domain']. '. Available Zones:'.$liquid_availability.'</span>';
        $attention = 1;
      }else{
         $liquid_product_array[] = $liquid_produc_details['name'].' - '.$liquid_produc_details['domain']. '. Available Zones:'.$liquid_availability;
      }
    }
    /////////////////////////-------- STORM DAIMOND ---------/////////////////////
    $storm_products =  mysql_get_array("SELECT mg_storm_on_demand.hosting_id , mg_storm_on_demand.uniq_id ,tblhosting.id ,tblhosting.packageid , tblhosting.domain, tblhosting.domainstatus,tblproducts.id as productid ,tblproducts.name FROM mg_storm_on_demand
                                              LEFT JOIN tblhosting ON  mg_storm_on_demand.hosting_id = tblhosting.id
                                              LEFT JOIN tblproducts ON  tblhosting.packageid = tblproducts.id
                                              ORDER BY tblproducts.name;
                                             "
                                        );


    $storm_product_zones = array();
    $storm_products_activity = array();

    foreach($storm_products as $storm_product){
      if($storm_product['domainstatus'] == 'Active'){
        $q = mysql_query("SELECT * FROM tblproducts WHERE id = " . (int)$storm_product['packageid'] . " LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $username = $row['configoption1'];
        $password = $row['configoption2'];
        //$password = StormOnDemand_Helper::encrypt_decrypt($row['configoption2']);

        $storm_configSelected = (int) $row['configoption7']; // Id config

        $config = new StormOnDemandStormConfig($username, $password);
        $zone   = new StormOnDemandNetworkZone($username, $password);

        $res = $config->ping();

        if(!array_key_exists($storm_configSelected,$storm_product_zones)){
          if(isset($res['ping']) && $res['ping'] == 'success')
          {
            $storm_avilablezones = $config->details($storm_configSelected);
            $storm_zones = array();

            foreach($storm_avilablezones['zone_availability'] as $storm_zoneid => $storm_avilablezoneid){
              $ret = $zone->details($storm_zoneid);
              $storm_zones[] = $ret['name'].' - '.$ret['region']['name'];
            }

            $storm_product_zones[$storm_configSelected] = $storm_zones;
            $storm_products_activity[$storm_product['id']] = $storm_product_zones;
          }
        }else{
          $storm_products_activity[$storm_product['id']][$storm_configSelected] = $storm_product_zones[$storm_configSelected];
        }
      }
    }

    $storm_product_array = array();
    foreach($storm_products_activity as $storm_pid => $storm_configid){
      foreach($storm_configid as $count_zones){
        $storm_availability = count($count_zones);
      }
      $storm_produc_details =  mysql_get_row("SELECT tblhosting.id ,tblhosting.packageid , tblhosting.domain, tblhosting.domainstatus,tblproducts.id,tblproducts.name FROM tblhosting
                                              LEFT JOIN tblproducts ON  tblhosting.packageid = tblproducts.id
                                              WHERE tblhosting.id = ?
                                             ",array($storm_pid));

      if($storm_availability <= $zone_limit){
        $storm_product_array[] = '<span style="color: #ff0000;">'.$storm_produc_details['name'].' - '.$storm_produc_details['domain']. '. Available Zones:'.$storm_availability.'<span>';
        $attention = 1;
      }else{
        $storm_product_array[] = $storm_produc_details['name'].' - '.$storm_produc_details['domain']. '. Available Zones:'.$storm_availability;
      }
    }


    if(!empty($liquid_product_array) || !empty ($storm_product_array)){

      if(empty($liquid_product_array)){
        $liquid_product_exist = '0';
      }else{
        $liquid_product_exist = '1';
      }

      if(empty($storm_product_array)){
        $storm_product_exist = '0';
      }else{
        $storm_product_exist = '1';
      }

      $cronroles = mysql_get_row("SELECT `value` FROM `tbladdonmodules` WHERE `setting` = ?", array("cronroles"));

      $roles_array = explode(',',$cronroles['value'] );

      foreach($roles_array as $roleid){
        $admins = mysql_get_array("SELECT `id` FROM `tbladmins` WHERE `roleid` = ?", array($roleid));

        foreach($admins as $admin){
          sendAdminMessage("Storm On Demand Widget For WHMCS",
                            array("liquid_product_exist"  =>$liquid_product_exist,
                                  "storm_product_exist"   =>$storm_product_exist,
                                  "liquid_product_array"  =>$liquid_product_array,
                                  "storm_product_array"   =>$storm_product_array,
                                  "attention"      =>$attention,
                                  ),
                            $to = "system",
                            $deptid = "",
                            $admin['id'],
                            $ticketnotify = ""
                          );
        }
      }
    }
  }
}


add_hook("DailyCronJobPreEmail", 1, "LiquidAndStormWidget_hook_DailyCronJobPreEmail");
add_hook("AdminHomeWidgets", 1, "ZoneAvailability");
