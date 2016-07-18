<?php

/**********************************************************/
//AB Logger
require_once dirname(__FILE__).DS.'classes'.DS.'class.StormBillingLogger.php';
//AB Resource 
require_once dirname(__FILE__).DS.'classes'.DS.'class.StormBillingResource.php';
//AB Account
require_once dirname(__FILE__).DS.'classes'.DS.'class.StormBillingAccount.php';
//AB Event Manager
require_once dirname(__FILE__).DS.'classes'.DS.'class.StormBillingEventManager.php';

//some functions
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'functions.php';
//Resource Class
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'class.SBResource.php';
//Product Class
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'class.SBProduct.php';
//Modules Garden Class
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'class.ModulesGarden.php';
//Language
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'class.MG_Language.php';

/**********************************************************/

//Load Event Manager And Register Events
StormBillingEventManager::register('StormBillingResourceAdded');
StormBillingEventManager::register('StormBillingInvoiceGenerated');
StormBillingEventManager::register('StormBillingLowUserCredit');
StormBillingEventManager::register('StormBillingCronLoop');

/****************** AWAITING INVOICES *********************/

function StormBilling_addAwaitingInvoice($userid, $hostingid, $date, $duedate, $items)
{
    mysql_safequery('INSERT INTO StormBilling_awaiting_invoices (`userid`, `hostingid`, `date`, `duedate`, `items`) VALUES (?, ?, ?, ?, ?)', array(
        $userid,
        $hostingid,
        $date,
        $duedate,
        serialize($items)
    )) or die(mysql_error());
} 

function StormBilling_deleteAwaitingInvoices($id)
{
    mysql_safequery("DELETE FROM StormBilling_awaiting_invoices WHERE id = ?", array($id));
}

function StormBilling_getAwaitingInvoice($id)
{
    $invoice = mysql_get_row("SELECT * FROM StormBilling_awaiting_invoices WHERE id = ?", array($id));
    if($invoice)
    {
        $invoice['items'] = unserialize($invoice['items']);
        return $invoice;
    }
    
    return false;
}
/*************************************************************/

function StormBilling_getWhmcsAccountDetails($hosting_id)
{ 
    $q = mysql_safequery("SELECT DISTINCT c.id, c.firstname, c.lastname, c.language FROM tblhosting h JOIN tblclients c ON(h.userid = c.id) WHERE h.id = ?", array($hosting_id));
    return  mysql_fetch_assoc($q);
}


/*************************** MODULES AND PRODUCTS ***********************/
function StormBilling_getSubmoduleInformation($name)
{
    //kiedyś
}

function StormBilling_getSubmoduleDescription($name)
{
    $file = 'class.'.$name.'_resources.php';
    if(!file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'submodules'.DIRECTORY_SEPARATOR.$file))
    {
        return;
    }
    
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'class.SBResource.php';
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'submodules'.DIRECTORY_SEPARATOR.$file;
    $name .= '_resources';
    return constant($name.'::description');
}

function StormBilling_getSubmoduleName($name)
{
    $file = 'class.'.$name.'_resources.php';
    if(!file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'submodules'.DIRECTORY_SEPARATOR.$file))
    {
        return;
    }

    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'class.SBResource.php';
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'submodules'.DIRECTORY_SEPARATOR.$file;
    $name .= '_resources';
    return constant($name.'::name');
}

function StormBilling_getSubmoduleResources($name)
{
    $file = 'class.'.$name.'_resources.php';
    if(!file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'submodules'.DIRECTORY_SEPARATOR.$file))
    {
        return;
    }
    
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'class.SBResource.php';
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'submodules'.DIRECTORY_SEPARATOR.$file;
    $name .= '_resources';
    $m = new $name();
    return $m->getResources();
}

function StormBilling_getSubmoduleHTMLArea($name)
{
    $file = 'class.'.$name.'_resources.php';
    if(!file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'submodules'.DIRECTORY_SEPARATOR.$file))
    {
        return;
    }
    
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'class.SBResource.php';
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'submodules'.DIRECTORY_SEPARATOR.$file;
    $name .= '_resources';
    $m = new $name();
    return $m->getConfigurationArea();
}

function StormBilling_getSubmoduleConfiguration($name)
{
    $file = 'class.'.$name.'_resources.php';
    if(!file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'submodules'.DIRECTORY_SEPARATOR.$file))
    {
        return;
    }
    
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'class.SBResource.php';
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'submodules'.DIRECTORY_SEPARATOR.$file;
    $name .= '_resources';
    $m = new $name();
    return $m->getConfiguration();
}

function StormBilling_getModules()
{
    $submodules = StormBilling_getSubmodulesList();
    $modules = array();
    foreach($submodules as $m)
    {
        $modules[] = substr(substr($m, 0, -14), 6);
    }
            
    return $modules; 
}

function StormBilling_getUnusedModules($name)
{
    $submodules = StormBilling_getSubmodulesList();  
    $q = mysql_get_array("SELECT DISTINCT servertype FROM tblproducts");
    
    $types = array();
    foreach($q as $r)
    {
        $types[] = $r['servertype'];
    }
    
    $modules = array();
    foreach($submodules as $m)
    {
        $modules[] = substr(substr($m, 0, -14), 6);
    } 
    
    $ret = array_diff($modules, $types);
    
    return $ret;
}


function StormBilling_getModulesProducts() 
{
    $products = array();
    $submodules = StormBilling_getSubmodulesList(); 

    //GET PRODUCT WITH SUPPORTED MODULES
    if(count($submodules) > 0)
    {
        foreach($submodules as $m){
            $sql_part.= '?,';
            $modules[] = substr(substr($m, 0, -14), 6);
        } 
        $q = mysql_safequery("SELECT tblproducts.id, tblproducts.name, servertype, tblproductgroups.name as `group`, s.enable, s.billing_settings
            FROM `tblproducts` 
            LEFT JOIN tblproductgroups ON (tblproducts.gid = tblproductgroups.id)
            LEFT JOIN StormBilling_settings s ON (tblproducts.id = s.product_id)
            WHERE servertype IN (".trim($sql_part, ',').")", $modules);
        while($row = mysql_fetch_assoc($q))
        {
            if(in_array($row['servertype'], $modules))
            {
                $products[$row['id']] = array(
                    'module'            =>  $row['servertype'],
                    'product_name'      =>  $row['name'],
                    'group'             =>  $row['group'],
                    'enable'            =>  $row['enable'],
                    'billing_settings'  =>  unserialize($row['billing_settings'])
                );
            }
        }   
    }
    
    return $products;
}

function StormBilling_getSubmodulesList() 
{ 
    $modulesdir = dirname(__FILE__).DIRECTORY_SEPARATOR.'submodules';
    if(!is_dir($modulesdir)){
        return false;
    } 
    $dir = dir($modulesdir);
    $i=0;
    while(($file = $dir->read()) !== false)
        if($file != '.' && $file != '..' && is_file($modulesdir.'/'.$file) && strpos($file,'.php') !== FALSE){
            $listfiles[$i] = $file;
            $i++;
        }
    $dir->close();
    if($i > 0)
        return $listfiles;
    else
        return array();
}
/*****************************************************************************/

/************************   HOSTING ********************************************/
function StormBilling_getHostingPaymentMethod($hosting_id)
{
    $hosting = mysql_get_row("SELECT paymentmethod FROM tblhosting WHERE id = ?", array($hosting_id));
    if($hosting['paymentmethod'])
    {
        return $hosting['paymentmethod'];
    }
    
    $results = localAPI("getpaymentmethods", array(),  ModulesGarden::getAdmin());
    if(!$results['totalresults'])
    {
        return false;
    }
    
    return $results['paymentmethods']['paymentmethod'][1]['module'];
}

/************************ RESOURCE SETTINGS ********************************/
function StormBilling_getResourcesSettings($product_id) {
    $q = mysql_safequery("SELECT resources FROM `StormBilling_resources_settings` WHERE product_id = ?", array($product_id));
    $row = mysql_fetch_assoc($q);
    if($row)
        return unserialize($row['resources']);
    else
        return array();
}

function StormBilling_saveResourcesSettings($product_id, $resources) 
{
    return mysql_safequery("REPLACE INTO `StormBilling_resources_settings` (product_id, resources) VALUES(?, ?)", array($product_id, serialize($resources)));
}

function StormBilling_deleteRecordsAndPrices($hosting_id, $product_id, $start_date = null, $end_date = null)
{
    $sql_filters = '';
    $params = array
    (
        'hosting_id'    =>  $hosting_id,
        'product_id'    =>  $product_id
    );
    
    if($start_date && $end_date)
    {
        $params['start_date']   =   $start_date;
        $params['end_date']     =   $end_date;
        $sql_filters .= ' AND `date` BETWEEN ? AND ?';
    }
    
    //Load Product Class
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'class.SBProduct.php';
    $p = new SBProduct($product_id);
    $type       =   $p->getServerType();
    
    mysql_safequery("DELETE FROM StormBilling_".$type."_records WHERE hosting_id = ? AND product_id = ? ".$sql_filters, $params);
    mysql_safequery("DELETE FROM StormBilling_".$type."_prices WHERE hosting_id = ? AND product_id = ? ".$sql_filters, $params);
}


function StormBilling_columnPrefix($field_names, $alias)
{
    $prefixed = array();
    foreach ($field_names as $field_name)
    {
        $prefixed[] = "`{$alias}`.`{$field_name}` AS `{$alias}.{$field_name}`";
    }

    return implode(", ", $prefixed);
}

function StormBilling_convertToUserCurrency($number, $user_id = null)
{
    if(!$user_id)
    {
        $user_id = $_SESSION['uid'];
    }
    
    $currency = getCurrency($user_id);;
    
    if($user_currency)
    {
        return convertCurrency($number, 1, $user_currency['id']);
    }
    
    return $number;
}

function StormBilling_formatToUserCurrency($number, $user_id = null)
{
    global $currency;
    $old_currency = $currency;
    
    $number = StormBilling_convertToUserCurrency($number, $user_id);
    //Stupid WHMCS oO
    $currency   =   getCurrency($user_id);
    $formated   =   formatCurrency($number);
    
    $currency = $old_currency;
    return $formated;
}

/************************************************************************/


/******************* LOGS FILES ****************************/
function StormBilling_getLogsFiles()
{
    $crondir = dirname(__FILE__).DIRECTORY_SEPARATOR.'cron'.DIRECTORY_SEPARATOR.'logs';
    
    if(!is_dir($crondir)){
        return false; 
    }
    $dir = dir($crondir);
    $i=0;
    while(($file = $dir->read()) !== false)
        if($file != '.' && $file != '..' && is_file($crondir.'/'.$file) && strpos($file,'.log') !== FALSE){
            $listfiles[$i] = $file;
            $i++;
        }
    $dir->close();
    if($i > 0)
        return $listfiles;
    else
        return false;
}

function StormBilling_deleteLogFile($logfile)
{
    return unlink(substr(dirname(__FILE__), 0 , strpos(dirname(__FILE__), 'modules'.DS)).'modules'.DS.'addons'.DS.'StormBilling'.DS.'cron'.DS.'logs'.DS.$logfile);
}

function StormBilling_getLogFileContent($logfile)
{
    return file_get_contents(substr(dirname(__FILE__), 0 , strpos(dirname(__FILE__), 'modules'.DS)).'modules'.DS.'addons'.DS.'StormBilling'.DS.'cron'.DS.'logs'.DS.$logfile);
}


/*************************** 1.4 ****************************/

/**
 * Generate invoice from selected period.
 * @param type $hosting_id
 * @param type $product_id
 * @param type $start_date
 * @param type $end_date
 * @param type $invoiceSettings
 * @return boolean
 */
function StormBillingGenerateInvoice($hosting_id, $product_id, $start_date = null, $end_date = null, $invoiceSettings = array())
{
    $account    =   new StormBillingAccount($hosting_id);
    //Get Summay usage
    $summary    =   $account->getSummaryLines($product_id, $start_date, $end_date);
    
    //Do not create invoice if amout is lower that 0.01
    if($summary['amount'] < 0.01)
    {
        return true;
    }

    //products
    $product    =   mysql_get_row("SELECT name, tax FROM tblproducts WHERE id = ?", array($product_id));
    //hosting
    $hosting    =   mysql_get_row("SELECT id, domain, paymentmethod, userid FROM tblhosting WHERE id = ?", array($hosting_id));
    //user id
    $user_id    =   $hosting['userid'];
    
    $postfields["action"]           =   "createinvoice";
    $postfields['paymentmethod']    =   StormBilling_getHostingPaymentMethod($hosting_id);
    $postfields["userid"]           =   $user_id;
    $postfields['autoapplycredit']  =   $invoiceSettings['autoapplycredit'] ? 1 : 0;
    
    //Calculate Invoice Date And Duedate
    $d = getdate(); 
    $postfields["date"] = $d['year'].($d['mon'] <= 9 ? '0'.$d['mon'] : $d['mon']).($d['mday'] <= 9 ? '0'.$d['mday'] : $d['mday']);
    $d = getdate(time()+(($invoiceSettings['duedate'] >=0 ? $invoiceSettings['duedate'] : 7)*24*60*60));
    $postfields["duedate"] = $d['year'].($d['mon'] <= 9 ? '0'.$d['mon'] : $d['mon']).($d['mday'] <= 9 ? '0'.$d['mday'] : $d['mday']);
    
    //First Line In Invoice
    $product_line = $product['name'].' - ';
    if($hosting['domain'])
    {
        $product_line .= $hosting['domain'];
    }
    else
    { 
        $product_line .= 'no domain';
    }
    
    if($start_date && $end_date)
    {
        $product_line .= ' ('.$start_date.' - '.$end_date.')';
    }
      
    $postfields["itemdescription1"] =   $product_line;
    $postfields["itemamount1"]      =   0.00;
    $postfields["itemtaxed1"]       =   $product['tax'];
    
    $i = 2;
    foreach($summary['lines'] as $record)
    {
        $postfields["itemdescription".$i]       =   $record['invoiceDescription'];
        $postfields["itemamount".$i]            =   $record['amount'];
        $postfields["itemtaxed".$i]             =   $product['tax'];
        $i++;
    }
    
    $result = localAPI('CreateInvoice', $postfields, ModulesGarden::getAdmin()); 
    
    if($result['result'] == 'success')
    {
        StormBillingLogger::info("Invoice generated. Invoice ID ".$result['invoiceid']);
        return $result['invoiceid'];
    }
    
    StormBillingLogger::error("Cannot generate invoice. ".print_r($result, true));
    return false;
}
            
function StormBillingUpdateInvoice($invoice_id, $hosting_id, $product_id, $start_date = null, $end_date = null)
{
    $account    =   new StormBillingAccount($hosting_id);
    //Get Summay usage
    $summary    =   $account->getSummaryLines($product_id, $start_date, $end_date);
    
    //Do not update invoice if amout is lower that 0.01
    if($summary['amount'] < 0.01)
    {
        return true;
    }
    
    //products
    $product    =   mysql_get_row("SELECT name, tax FROM tblproducts WHERE id = ?", array($product_id));
    
    $postfields                     =   array();
    $postfields['invoiceid']        =   $invoice_id;

    foreach($summary['lines'] as $record)
    {
        $postfields["newitemdescription"][]         =   $record['invoiceDescription'];
        $postfields['newitemamount'][]              =   $record['amount'];
        $postfields['newitemtaxed'][]               =   $product['tax'];
    }
    
    $result = localAPI('updateinvoice', $postfields, ModulesGarden::getAdmin()); 
    
    if($result['result'] == 'success')
    {
        StormBillingLogger::info("Invoice Updated. Invoice ID ".$invoice_id);
        return $invoice_id;
    }
    
    StormBillingLogger::error("Cannot update invoice with ID ".$invoice_id.". ".print_r($result, true));
    return false;
}

function StormBillingGenerateAwaitingInvoice($hosting_id, $product_id, $start_date = null, $end_date = null, $invoiceSettings = array())
{
    $account    =   new StormBillingAccount($hosting_id);
    //Get Summay usage
    $summary    =   $account->getSummaryLines($product_id, $start_date, $end_date);
    //products
    $product = mysql_get_row("SELECT name, tax FROM tblproducts WHERE id = ?", array($product_id));
    //hosting
    $hosting = mysql_get_row("SELECT id, domain, paymentmethod, userid FROM tblhosting WHERE id = ?", array($hosting_id));
    //user id
    $user_id = $hosting['userid'];
    
    //items array
    $items = array();
    
    $postfields["action"]           =   "createinvoice";
    $postfields['paymentmethod']    =   StormBilling_getHostingPaymentMethod($hosting_id);
    $postfields["userid"]           =   $user_id;
    $postfields['autoapplycredit']  =   $invoiceSettings['autoapplycredit'] ? 1 : 0;
    
    //Calculate Invoice Date And Duedate
    $d = getdate(); 
    $postfields["date"] = $d['year'].($d['mon'] <= 9 ? '0'.$d['mon'] : $d['mon']).($d['mday'] <= 9 ? '0'.$d['mday'] : $d['mday']);
    $d = getdate(time()+(($invoiceSettings['duedate'] >=0 ? $invoiceSettings['duedate'] : 7)*24*60*60));
    $postfields["duedate"] = $d['year'].($d['mon'] <= 9 ? '0'.$d['mon'] : $d['mon']).($d['mday'] <= 9 ? '0'.$d['mday'] : $d['mday']);
    
    //First Line In Invoice
    $product_line = $product['name'].' - ';
    if($hosting['domain'])
    {
        $product_line .= $hosting['domain'];
    }
    else
    {
        $product_line .= 'no domain';
    }
    
    if($start_date && $end_date)
    {
        $product_line .= ' ('.$start_date.' - '.$end_date.')';
    }

    $items[] = array
    (
        'description'               =>  $product_line,
        'amount'                    =>  0.00,
        'taxed'                     =>  $product['tax']
    );

    foreach($summary['lines'] as $record)
    {        
        $items[] = array
        (
            "description"   =>   $record['invoiceDescription'],
            "amount"        =>   $record['amount'],
            "taxed"         =>   $product['tax']
        );
    }

    //Add items to database
    mysql_safequery('INSERT INTO StormBilling_awaiting_invoices (`userid`, `hostingid`, `date`, `duedate`, `items`) VALUES (?, ?, ?, ?, ?)', array(
        $user_id,
        $hosting_id,
        $postfields['date'],
        $postfields['duedate'],
        serialize($items)
    ));
    
    $invoice_id = mysql_insert_id();
    if($invoice_id)
    {
        return $invoice_id;
    }
    
    return false;
}
/**
 * Delete usage records from selected period
 * @param type $hosting_id
 * @param type $product_id
 * @param type $start_date
 * @param type $end_date
 */
function StormBillingDeleteUsageRecord($hosting_id, $product_id, $start_date = 0, $end_date = 0)
{
    //Load Product Class
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'class.SBProduct.php';
    $p = new SBProduct($product_id);
    $type       =   $p->getServerType();
    
    //Delete records
    mysql_safequery("DELETE FROM StormBilling_".$type."_records WHERE hosting_id = ?", array($hosting_id));
    mysql_safequery("DELETE FROM StormBilling_".$type."_prices WHERE hosting_id = ?", array($hosting_id));
    mysql_safequery("DELETE FROM StormBilling_".$type."_extendedPricing WHERE hosting_id = ?", array($hosting_id));
    mysql_safequery("DELETE FROM StormBilling_billed_hostings WHERE hosting_id = ?", array($hosting_id));
}
