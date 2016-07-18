<?php


if(isset($_SERVER['HTTP_USER_AGENT']))
    die('Cannot run directly');

ob_start();
sleep(3); // SECURITY AGAINST MULTIPLE CALLS


$start = time();
/*************************************
 * 
 *  LOAD ENVIRONMENT
 * 
 *************************************/
define('DS', DIRECTORY_SEPARATOR); 
define('WHMCS_MAIN_DIR', substr(dirname(__FILE__),0, strpos(dirname(__FILE__),'modules'.DS.'addons')));  
define('StormBillingDIR', substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), DS.'cron')));

/**********************
 * BEGIN
 **********************/
//WHMCS
if(file_exists(WHMCS_MAIN_DIR.DS.'init.php')) // 
{
    require_once WHMCS_MAIN_DIR.DS.'init.php';
}
else // Older than 5.2.2
{
    require_once WHMCS_MAIN_DIR.DS."configuration.php";
    require_once WHMCS_MAIN_DIR.DS."dbconnect.php";
    require_once WHMCS_MAIN_DIR.DS."includes".DS."functions.php";
}

//ADDON FUNCTIONS
require_once StormBillingDIR.DS.'core'.DS.'functions.php';
require_once StormBillingDIR.DS.'core'.DS."class.ModulesGarden.php";
if(!class_exists('MG_Language'))
{
    require_once StormBillingDIR.DS.'core'.DS.'class.MG_Language.php';
}
//StormBilling FUNCTIONS
require_once StormBillingDIR.DS.'class.SBProduct.php';
require_once StormBillingDIR.DS.'core.php';


/********************************
 * 
 * ERROR REPORTING
 * 
 ******************************/
set_exception_handler('exceptionHandler');
set_error_handler('errorHandler');
register_shutdown_function('importShutDown');


function exceptionHandler($error)
{
    StormBillingLogger::error($error);
}

function errorHandler($errno, $errstr, $errfile, $errline)
{
    if ($errno == 1 || $errno == 64)
    {
        StormBillingLogger::error("ERROR: [$errno] $errstr <br />on line $errline in file $errfile");
    }
}

function importShutDown() 
{
    $error = error_get_last();
    if ($error['type'] == 1 || $error['type'] == 64) 
    {
        StormBillingLogger::error("ERROR: {$error['message']} <br />on line {$error['line']} in file {$error['file']}");
    }
}

//Do not log warnings
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR); 


$hostings = mysql_get_array("SELECT * FROM StormBilling_autosuspend WHERE suspended = 0");

foreach($hostings as $hosting)
{
    $res = localAPI('modulesuspend', array(
        'accountid'         =>  $hosting['hosting_id'],
        'suspendreason'     =>  'Low Credits Amount'
    ), ModulesGarden::getAdmin());
    
    if($res['result'] == 'success')
    {
        mysql_safequery("UPDATE StormBilling_autosuspend SET suspended = 1 WHERE hosting_id = ?", array($hosting['hosting_id']));
    }
}