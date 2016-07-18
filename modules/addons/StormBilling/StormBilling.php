<?php

//SOME DEFINES
defined('DS') ? null : define('DS',DIRECTORY_SEPARATOR);
defined('WHMCS_MAIN_DIR') ? null : define('WHMCS_MAIN_DIR', substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), 'modules'.DS.'addons')));


if(file_exists(dirname(__FILE__).DS.'moduleVersion.php')){
    require_once dirname(__FILE__).DS.'moduleVersion.php';
     define('STORM_SERVERS_BILLING_VERSION', $moduleVersion);
}else{
     define('STORM_SERVERS_BILLING_VERSION', 'Development Version');
}

/**
 * Just change function name. Do not edit anything more.
 */


function StormBilling_config()
{
    //SOME USEFUL STUFF
    require_once dirname(__FILE__).DS.'core'.DS.'functions.php';

    require_once dirname(__FILE__).DS.'config.php';

    $module = StormBilling_getModuleClass(__FILE__);
    $MGC = new $module();
    $description = $MGC->description;
    $newVersion = StormBilling_getLatestVersion();
    $script = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR) + 1);

    if($newVersion && $script == 'configaddonmods.php')
    {
        $description .=  '<p><span class="label closed">New version</span> of  Liquid Web Storm Servers Billing is available! <span><br>Check this address to find out more <a target="_blank" href="'.$newVersion['site'].'">'.$newVersion['site'].'</a></span></p>';
    }

    return array
    (
        'name'          =>  $MGC->system_name,
        'description'   =>  $description,
        'version'       =>  $MGC->version,
        'author'        =>  $MGC->author
    );
}

function StormBilling_activate()
{
    //SOME USEFUL STUFF
    require_once dirname(__FILE__).DS.'core'.DS.'functions.php';

    require_once dirname(__FILE__).DS.'config.php';

    $module = StormBilling_getModuleClass(__FILE__);
    $MGC = new $module();
    $MGC->activate();
}

function StormBilling_deactivate()
{
    //SOME USEFUL STUFF
    require_once dirname(__FILE__).DS.'core'.DS.'functions.php';

    require_once dirname(__FILE__).DS.'config.php';

    $module = StormBilling_getModuleClass(__FILE__);
    $MGC = new $module();
    $MGC->deactivate();
}


function StormBilling_upgrade($vars)
{
    //SOME USEFUL STUFF
    require_once dirname(__FILE__).DS.'core'.DS.'functions.php';

    require_once dirname(__FILE__).DS.'config.php';

    $module = StormBilling_getModuleClass(__FILE__);
    $MGC = new $module($vars['version']);
    $MGC->upgrade($vars['version']);
}

/**
 * Admin area output
 * @param type $vars
 */
function StormBilling_output($vars)
{
    require_once dirname(__FILE__).DS.'config.php';
    require_once dirname(__FILE__).DS.'core'.DS.'output.php';
}

function StormBilling_sidebar($vars)
{
    $modulelink = $vars['modulelink'];
    $version = $vars['version'];
    $LANG = $vars['_lang'];

    $sidebar = '<span class="header"><img src="images/icons/addonmodules.png" class="absmiddle" width="16" height="16" />Liquid Web Storm Servers</span>
                <ul class="menu">
                    <li><a href="addonmodules.php?module=StormBilling&action=setup">Product Setup Wizard</a></li>
                    <li><a href="addonmodules.php?module=StormBilling&action=billing">Storm Servers Billing</a></li>
                    <li><a href="addonmodules.php?module=StormBilling&action=config">Configuration</a></li>
                    <li></li>
                    <li></li>
                    <li>Version: '.$version.'</li>
                </ul>';

    return $sidebar;
}

function StormBilling_getModuleClass($file)
{
    $dirname = dirname($file);
    $basename = basename($dirname);
    return $basename;
}


/****************** MODULE INFORMATION ************************/
//Register instance
StormBilling_registerInstance();

function StormBilling_registerInstance()
{
    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   'Storm Servers Billing For WHMCS';
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   STORM_SERVERS_BILLING_VERSION;
    //Encryption key
    $moduleKey          =   'WPtcK1Qbsof8dIau6yC1Ku1JwI1vMwTsT5UfzpVoPXlwSCzDBgnNzAAzDOamGJdM';
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

function StormBilling_getLatestVersion()
{
    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   'Storm Servers Billing For WHMCS';
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   STORM_SERVERS_BILLING_VERSION;
    //Encryption key
    $moduleKey          =   'WPtcK1Qbsof8dIau6yC1Ku1JwI1vMwTsT5UfzpVoPXlwSCzDBgnNzAAzDOamGJdM';;
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

