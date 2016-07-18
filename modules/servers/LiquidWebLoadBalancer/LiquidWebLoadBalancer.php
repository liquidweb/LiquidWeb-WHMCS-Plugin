<?php

require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'database.php';

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

if(file_exists(dirname(__FILE__).DS.'moduleVersion.php')){
    require_once dirname(__FILE__).DS.'moduleVersion.php';
     define('LIQUID_WEB_LOAD_BALACER_VERSION', $moduleVersion);
}else{
     define('LIQUID_WEB_LOAD_BALACER_VERSION', 'Development Version');
}

function LiquidWebLoadBalancer_checkConnection()
{
    if(strpos($_SERVER['SCRIPT_FILENAME'], 'configproducts.php') !== false)
    {
        $q = mysql_query("SELECT * FROM tblproducts WHERE id = " . (int)$_REQUEST['id'] . " LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $username = $row['configoption1'];
        $password = $row['configoption2'];

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
    }elseif(strpos($_SERVER['SCRIPT_FILENAME'], 'orders.php') !== false){

        $q = mysql_query("SELECT tblproducts.* FROM tblproducts LEFT JOIN tblhosting ON tblproducts.id = tblhosting.packageid LEFT JOIN tblorders ON tblhosting.orderid = tblorders.id WHERE tblorders.id = " . (int)$_REQUEST['id'] . " LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $username = $row['configoption1'];
        $password = $row['configoption2'];


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
    elseif(strpos($_SERVER['SCRIPT_FILENAME'], 'clientsservices.php') !== false){

        $q = mysql_query("SELECT tblproducts.* FROM tblproducts LEFT JOIN tblhosting ON tblproducts.id = tblhosting.packageid WHERE tblhosting.id = " . (int)$_REQUEST['id'] . " LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $username = $row['configoption1'];
        $password = $row['configoption2'];


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

function LiquidWebLoadBalancer_ConfigOptions()
{
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

        $regions = array();
        foreach($ret['items'] as $item)
        {
            $regions[$item['region']['id']] = $item['region']['name'];
        }

        echo '<table class="datatable" style="width: 100%">
                <tr>
                    <th>Region</th>
                </tr>
              ';
        foreach($regions as $region_key => $region_val)
        {
            echo '<tr>
                    <td><input class="storm-zone" type="radio" name="zone-id" value="'.$region_key.'" '.($region_key == $conf_id ? 'checked="checked"' : '').' />'.$region_val.'</td>
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
            'Name'      =>  'Region|Region',
            'Type'      =>  'select',
            'Values'    =>  array()
        );

        $ret = $zone->lists();

        foreach($ret['items'] as $item)
        {
            $configurable_options[3]['Values'][$item['region']['id']] = $item['region']['name'];
        }

        //Number Of Nodes
        $configurable_options[] = array
        (
            'Name'      =>  'Number Of Nodes|Number Of Nodes',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                '1'     =>  '1',
                '2'     =>  '2',
                '3'     =>  '3'
            )
        );

        //Number Of Services
        $configurable_options[] = array
        (
            'Name'      =>  'Number Of Services|Number Of Services',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                '1'     =>  '1',
                '2'     =>  '2',
                '3'     =>  '3'
            )
        );

        //Strategy
        $configurable_options[] = array
        (
            'Name'      =>  'Strategy|Strategy',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                'roundrobin'    =>  'roundrobin',
                'connections'   =>  'connections',
                'cells'         =>  'cells'
            )
        );

        //Session Persistence
        $configurable_options[] = array
        (
            'Name'      =>  'Session Persistence|Session Persistence',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                1       =>  'Yes',
                0       =>  'No'
            )
        );

        //SSL
        $configurable_options[] = array
        (
            'Name'      =>  'SSL|SSL',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                1       =>  'Yes',
                0       =>  'No'
            )
        );

        //Default Destination Port
        $configurable_options[] = array
        (
            'Name'      =>  'Default Destination Port|Default Destination Port',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                80      =>  '80',
            )
        );

        //Default Source Port
        $configurable_options[] = array
        (
            'Name'      =>  'Default Source Port|Default Source Port',
            'Type'      =>  'select',
            'Values'    =>  array
            (
                80      =>  '80',
            )
        );


        //Default Destination Port
        $groups =   array();
        $groups[] = array
        (
            'Name'          =>  'Configurable Options For LiquidWeb Load Balancer',
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
                    window.location = "configproductoptions.php?action=managegroup&id='.(int)$group_id.'";
                }, 1000);
              </script>';
        die();
    }

    //Create table. We need it!
    mysql_query("CREATE TABLE IF NOT EXISTS `mg_liquid_web_load_balancer`
        (
            `hosting_id` INT NOT NULL,
            `uniq_id`    CHAR(6),
            UNIQUE KEY(`hosting_id`)
        ) ENGINE = MyISAM") or die(mysql_error());

    //Base config
    $config                     =   array
    (
        'Username'              =>  array
        (
            'Type'              =>  'text',
            'Size'              =>  '25',
        ),
        'Password'              =>  array
        (
            'Type'              =>  'password',
            'Size'              =>  '25'
        ),
        'Default Configurable Options'       =>  array
        (
            'Type'          =>  '',
            'Description'   =>  '<a id="generate-storm-confoption" href="stormajax=generate-confoption" class="load-configuration">Generate Default Configurable Options</a>'
        ),
        'Region'              =>  array
        (
            'Type'          =>  'text',
            'Size'          =>  '25',
            'Description'   =>  '<a id="load-storm-zone" href="stormajax=load-zone" class="load-configuration">Load Region</a>'
        ),
        'Number Of Nodes'       =>  array
        (
            'Type'              =>  'text',
            'Size'              =>  '25',
            'Default'           =>  1
        ),
        'Number Of Services'    =>  array
        (
            'Type'              =>  'text',
            'Size'              =>  '25',
            'Default'           =>  1
        ),
        'Strategy'              =>  array
        (
          'Type'                =>  'dropdown',
          'Options'             =>  'roundrobin,connections,cells'
        ),
        'Session Persistence'   =>  array
        (
            'Type'              =>  'yesno'
        ),
        'SSL'                   =>  array
        (
            'Type'              =>  'yesno'
        ),
        'Default Source Port'   =>  array
        (
            'Type'              =>  'text',
            'Size'              =>  '25',
            'Default'           =>  80
        ),
        'Default Destination Port'=>  array
        (
            'Type'              =>  'text',
            'Size'              =>  '25',
            'Default'           =>  80
        ),
    );

    $newVersion = LiquidWebLoadBalancer_getLatestVersion();
    $script = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR) + 1);

    if($newVersion && $script == 'configproducts.php' && $_GET['action'] != 'save')
    {
        echo '<p style="text-align: center;" class="infobox op_version">
            <span style="font-weight: bold">New version of Liquid Web Load Balancer module is available!</span>
            <span style="font-weight: bold"><br />Check this address to find out more <a target="_blank" href="'.$newVersion['site'].'">'.$newVersion['site'].'</a></span>
         </p>
         ';
    }

    if($script == 'configproducts.php'){
        $testConnection = LiquidWebLoadBalancer_checkConnection();
    }else{
        $testConnection = true;
    }

    if($testConnection)
    {
      return $config;
    }else
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

function LiquidWebLoadBalancer_CreateAccount($params)
{
    //get configuration
    $username   =   LiquidWebLoadBalancer_getOption('Username', $params);
    $password   =   LiquidWebLoadBalancer_getOption('Password', $params);
    $strategy   =   LiquidWebLoadBalancer_getOption('Strategy', $params);
    $sess_per   =   LiquidWebLoadBalancer_getOption('Session Persistence', $params) ? 1 : 0;
    $hostname   =   $params['customfields']['hostname'] ? $params['customfields']['hostname'] : $params['domain'];
    $region     =   LiquidWebLoadBalancer_getOption('Region', $params);

    //load server class
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkLoadBalancer.php';
    $balancer = new StormOnDemandNetworkLoadBalancer($username, $password);

    $ret = $balancer->create($hostname, array(array('src_port' =>  LiquidWebLoadBalancer_getOption('Default Source Port', $params), 'dest_port' => LiquidWebLoadBalancer_getOption('Default Destination Port', $params))), $strategy, $sess_per, $region);

    //has error?
    if($error = $balancer->getError())
    {
        return $error;
    }
    //save uniq_id to database. We need it!
    mysql_query("REPLACE INTO mg_liquid_web_load_balancer (`hosting_id`, `uniq_id`) VALUES ('".$params['serviceid']."', '".$ret['uniq_id']."')") or die(mysql_error());
    //return successful message
    return "success";
}

function LiquidWebLoadBalancer_TerminateAccount($params)
{
    //get configuration
    $username   =   LiquidWebLoadBalancer_getOption('Username', $params);
    $password   =   LiquidWebLoadBalancer_getOption('Password', $params);

    //we need uniq_id to terminate server
    $q = mysql_query("SELECT * FROM mg_liquid_web_load_balancer WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_fields($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkLoadBalancer.php';
    $balancer = new StormOnDemandNetworkLoadBalancer($username, $password);
    //create server with base configuration
    $ret = $balancer->delete($row['uniq_id']);

    if($error = $balancer->getError())
    {
        return $error;
    }

    mysql_query("DELETE FROM mg_liquid_web_load_balancer WHERE hosting_id = ".(int)$params['serviceid']);
    return "success";
}

function LiquidWebLoadBalancer_ClientAreaCustomButtonArray()
{
    $return = array
    (
        'Settings'      =>  'settings',
    );

    return $return;
}

function LiquidWebLoadBalancer_Settings($params)
{
    //get configuration
    $username       =   LiquidWebLoadBalancer_getOption('Username', $params);
    $password       =   LiquidWebLoadBalancer_getOption('Password', $params);
    $max_nodes      =   LiquidWebLoadBalancer_getOption('Number Of Nodes', $params) ? LiquidWebLoadBalancer_getOption('Number Of Nodes', $params) : 0;
    $max_services   =   LiquidWebLoadBalancer_getOption('Number Of Services', $params) ? LiquidWebLoadBalancer_getOption('Number Of Services', $params) : 0;

    $ssl            =   LiquidWebLoadBalancer_getOption('SSL', $params);
    $hostname       =   $params['customfields']['hostname'] ? $params['customfields']['hostname'] : $params['domain'];

    $q = mysql_query("SELECT * FROM mg_liquid_web_load_balancer WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_fields($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);
    $uniq_id = $row['uniq_id'];

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkLoadBalancer.php';
    $balancer = new StormOnDemandNetworkLoadBalancer($username, $password);

    if($_REQUEST['modaction'])
    {
        switch($_REQUEST['modaction'])
        {
            case 'save':
                //update base settings
                if(count($_REQUEST['services']) > $max_services && $max_services)
                {
                    $vars['error'] = 'Too many servies';
                    break;
                }

                if(count($_REQUEST['nodes']) > $max_nodes && $max_nodes)
                {
                    $vars['error'] = 'Too many nodes';
                    break;
                }

                if(count($_REQUEST['nodes']))
                {
                    //nodes
                    $nodes = $balancer->possibleNodes($vars['details']['region_id']);

                    //delete nodes
                    foreach($nodes['items'] as $key => $n)
                    {
                        foreach($vars['details']['nodes'] as $n2)
                        {
                            if($n['ip'] == $n2['ip'])
                            {
                                unset($nodes['items'][$key]);
                            }
                        }
                    }

                    if($nodes['items'])
                    {
                        //Get Storm Servers
                        $q = mysql_query("SELECT uniq_id
                            FROM tblhosting h
                            LEFT JOIN mg_liquid_web d ON d.hosting_id = h.id
                            WHERE uniq_id <> '' AND h.userid=".$_SESSION['uid']);

                        $servers = array();
                        while($row = mysql_fetch_assoc($q))
                        {
                            $servers[] = $row['uniq_id'];
                        }

                        //Get Storm Server From Private Nodes
                        $private_servers = mysql_get_array("SELECT h.id
                            FROM tblhosting h
                            LEFT JOIN tblproducts p ON h.packageid = p.id
                            WHERE h.userid = ? AND p.servertype = ?", array($_SESSION['uid'], 'LiquidWebPrivateParent'));

                        if($private_servers)
                        {
                            foreach($private_servers as &$server)
                            {
                                $custom_fields = mysql_get_array("SELECT v.value
                                    FROM tblcustomfieldsvalues v
                                    LEFT JOIN tblcustomfields f ON v.fieldid = f.id
                                    WHERE v.relid = ? AND (f.fieldname = 'uniq_id' OR f.fieldname LIKE 'uniq_id|%')", array($server['id']));

                                foreach($custom_fields as $f)
                                {
                                    $servers[] = $f['value'];
                                }
                            }
                        }

                        foreach($nodes['items'] as $key => $n)
                        {
                            if(!in_array($n['uniq_id'], $servers))
                            {
                                unset($nodes['items'][$key]);
                            }
                        }

                        foreach($_REQUEST['nodes'] as $new_node)
                        {
                            $found = false;
                            foreach($nodes['items'] as $user_node)
                            {
                                if($new_node == $user_node['ip'])
                                {
                                    $found = true;
                                    break;
                                }
                            }

                            if(!$found)
                            {
                                $vars['error'] = 'Cannot add node!';
                                break 2;
                            }
                        }
                    }
                }

                $additionals = array();
                if($_REQUEST['ssl_termination'] && $ssl)
                {
                    if(!strpos($_REQUEST['ssl_certificate'], 'hidden'))
                    {
                        $additionals['ssl_termination'] = 1;
                        $additionals['ssl_cert'] = $_REQUEST['ssl_certificate'];
                        $additionals['ssl_key'] = $_REQUEST['ssl_private_key'];
                    }

                    if($_REQUEST['ssl_includes'])
                    {
                        $additionals['ssl_includes'] = 1;
                        $additionals['ssl_int'] = $_REQUEST['ssl_int'];
                    }
                }
                else
                {
                    $additionals['ssl_termination'] = 0;
                }


                if( $_REQUEST['services'] == null ){
                  $vars['error'] = 'You must have set at last one service';
                  break;
                }else{
                    $i = -1;
                    foreach($_REQUEST['services'] as $service){
                        $i++;
                        $services[$i] = $service;
                    }
                }

                $ret = $balancer->update($uniq_id, $hostname, $services, $_REQUEST['strategy'], $_REQUEST['session_persistence'] ? 1 : 0, $_REQUEST['nodes'], $additionals);

                if($error = $balancer->getError())
                {
                    $vars['error'] = $error;
                }
                else
                {
                    $vars['info'] = 'Load Balancer Updated';
                }
            break;
        }
    }

    $vars['details'] = $balancer->details($uniq_id);

    $strategies = $balancer->strategies();

    $vars['strategies'] = $strategies['strategies'];

    if(empty($vars['strategies'])){
      $vars['strategies'][0] = array("description" =>"" , "name" => "roundrobin", "strategy" => "roundrobin");
      $vars['strategies'][1] = array("description" =>"" , "name" => "connections", "strategy" => "connections");
      $vars['strategies'][2]=  array("description" =>"" , "name" => "cells", "strategy" => "cells");
    }

    //nodes
    $nodes = $balancer->possibleNodes($vars['details']['region_id']);

    //delete nodes
    foreach($nodes['items'] as $key => $n)
    {
        foreach($vars['details']['nodes'] as $n2)
        {
            if($n['ip'] == $n2['ip'])
            {
                unset($nodes['items'][$key]);
            }
        }
    }

    if($nodes['items'])
    {
        //Get Storm Servers
        $q = mysql_query("SELECT uniq_id
            FROM tblhosting h
            LEFT JOIN mg_liquid_web d ON d.hosting_id = h.id
            WHERE uniq_id <> '' AND h.userid=".(int)$_SESSION['uid']);

        $servers = array();
        while($row = mysql_fetch_assoc($q))
        {
            $servers[] = $row['uniq_id'];
        }

        //Get Storm Server From Private Nodes
        $private_servers = mysql_get_array("SELECT h.id
            FROM tblhosting h
            LEFT JOIN tblproducts p ON h.packageid = p.id
            WHERE h.userid = ? AND p.servertype = ?", array($_SESSION['uid'], 'LiquidWebPrivateParent'));

        if($private_servers)
        {
            foreach($private_servers as &$server)
            {
                $custom_fields = mysql_get_array("SELECT v.value
                    FROM tblcustomfieldsvalues v
                    LEFT JOIN tblcustomfields f ON v.fieldid = f.id
                    WHERE v.relid = ? AND (f.fieldname = 'uniq_id' OR f.fieldname LIKE 'uniq_id|%')", array($server['id']));

                foreach($custom_fields as $f)
                {
                    $servers[] = $f['value'];
                }
            }
        }

        foreach($nodes['items'] as $key => $n)
        {
            if(!in_array($n['uniq_id'], $servers))
            {
                unset($nodes['items'][$key]);
            }
        }
    }

	//getting custom configurations
	require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
	$customConfig = StormOnDemand_Helper::getCustomConfigValues();

    $vars['custom_template'] = $customConfig['custom_template'];
    $vars['nodes'] = $nodes;
    $vars['max_nodes'] = $max_nodes;
    $vars['max_services'] = $max_services;
    $vars['ssl'] = $ssl;
    $vars['subpage'] = dirname(__FILE__).DS.'clientarea'.DS.'settings.tpl';

    $pagearray = array(
        'templatefile'  =>  'clientarea'.DS.'clientarea',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">Settings</a>',
        'vars'          =>  $vars
    );

    return $pagearray;
}

function LiquidWebLoadBalancer_ChangePackage($params)
{
    //get configuration
    $username       =   LiquidWebLoadBalancer_getOption('Username', $params);
    $password       =   LiquidWebLoadBalancer_getOption('Password', $params);
    $max_nodes      =   LiquidWebLoadBalancer_getOption('Number Of Nodes', $params) ? LiquidWebLoadBalancer_getOption('Number Of Nodes', $params) : 0;
    $max_services   =   LiquidWebLoadBalancer_getOption('Number Of Services', $params) ? LiquidWebLoadBalancer_getOption('Number Of Services', $params) : 0;

    $q = mysql_query("SELECT * FROM mg_liquid_web_load_balancer WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_fields($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);
    $uniq_id = $row['uniq_id'];

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkLoadBalancer.php';
    $balancer = new StormOnDemandNetworkLoadBalancer($username, $password);
    $details = $balancer->details($uniq_id);

    if($max_services && count($details['services']) > $max_services)
    {
        return 'Cannot make downgrade. User have to many services';
    }

    if($max_nodes && count($details['nodes']) > $max_nodes)
    {
        return 'Cannot make downgrade. User have to many nodes';
    }

    return "success";
}

function LiquidWebLoadBalancer_ClientArea($params)
{
    //get configuration
    $username   =   LiquidWebLoadBalancer_getOption('Username', $params);
    $password   =   LiquidWebLoadBalancer_getOption('Password', $params);

    $q = mysql_query("SELECT * FROM mg_liquid_web_load_balancer WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_fields($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);
    $uniq_id = $row['uniq_id'];
    ////////////////////////////
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkLoadBalancer.php';
    $loadbalancer = new StormOnDemandNetworkLoadBalancer($username, $password);

    $details = $loadbalancer->details($uniq_id);
    if($loadbalancer->getError())
    {
        return;
    }

	//getting custom configurations
	require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
	$customConfig = StormOnDemand_Helper::getCustomConfigValues();

    global $smarty;
    $smarty->assign('buttons', LiquidWebLoadBalancer_ClientAreaCustomButtonArray());
    $smarty->assign('details', $details);
    $smarty->assign('params', $params);
    $smarty->assign('custom_template', $customConfig['custom_template']);

    $code = $smarty->fetch(dirname(__FILE__).DS.'clientarea'.DS.'subviews'.DS.'clientarea.tpl');

    return $code;
}

function LiquidWebLoadBalancer_AdminServicesTabFields($params)
{
    //get configuration
    $username   =   LiquidWebLoadBalancer_getOption('Username', $params);
    $password   =   LiquidWebLoadBalancer_getOption('Password', $params);

    //we need uniq_id to terminate server
    $q = mysql_query("SELECT * FROM mg_liquid_web_load_balancer WHERE hosting_id = ".(int)$params['serviceid']);
    if(!mysql_num_fields($q))
    {
        return "Cannot find uniq_id for this service";
    }

    $row = mysql_fetch_assoc($q);
    $uniq_id = $row['uniq_id'];
        ////////////////////////////
    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkLoadBalancer.php';
    $loadbalancer = new StormOnDemandNetworkLoadBalancer($username, $password);

    $details = $loadbalancer->details($uniq_id);
    if($loadbalancer->getError())
    {
        return;
    }

    $fields['VIP'] = $details['vip'];
    $fields['Session Persistence'] = $details['session_persistence'] ? 'Enabled' : 'Disabled';
    $fields['SSL Termination'] = $details['ssl_termination'] ? 'Enabled' : 'Disabled';
    $fields['Intermediate Certificate'] = $details['ssl_includes'] ? 'Enabled' : 'Disabled';
    $fields['Strategy'] = $details['strategy'];

    if($details['services'])
    {
        $fields['Services'] = '<table style="width: 100%" class="datatable">
            <tr>
                <th style="width:50%">Source Port</th>
                <th>Destination Port</th>
            </tr>';
        foreach($details['services'] as $s)
        {
            $fields['Services'] .= '<tr><td>'.$s['src_port'].'</td><td>'.$s['dest_port'].'</td></tr>';
        }
        $fields['Services'] .= '</table>';
    }

    if($details['nodes'])
    {
        $fields['Nodes'] = '<table style="width: 100%" class="datatable">
            <tr>
                <th style="width:50%">Domain</th>
                <th>IP</th>
            </tr>';
        foreach($details['nodes'] as $n)
        {
            $fields['Nodes'] .= '<tr><td>'.$n['domain'].'</td><td>'.$n['ip'].'</td></tr>';
        }
        $fields['Nodes'] .= '</table>';
    }

    return $fields;
}



function LiquidWebLoadBalancer_getOption($option, $params)
{
    $config = LiquidWebLoadBalancer_ConfigOptions();

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
//Register instance

LiquidWebLoadBalancer_registerInstance();
function LiquidWebLoadBalancer_registerInstance()
{
    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   'Liquid Web Load Balancer For WHMCS';
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   LIQUID_WEB_LOAD_BALACER_VERSION;
    //Encryption key
    $moduleKey          =   'IGr5ovdyfePBSl4AG7tzOQ1FUaVbdLARq1LNXz8BSdtctX7mvTRW1Gde0DstL5Vz';
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

function LiquidWebLoadBalancer_getLatestVersion()
{
    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   'Liquid Web Load Balancer For WHMCS';
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   LIQUID_WEB_LOAD_BALACER_VERSION;
    //Encryption key
    $moduleKey          =   'IGr5ovdyfePBSl4AG7tzOQ1FUaVbdLARq1LNXz8BSdtctX7mvTRW1Gde0DstL5Vz';
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

