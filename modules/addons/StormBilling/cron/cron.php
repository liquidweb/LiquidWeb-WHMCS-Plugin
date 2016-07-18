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

/************************************* CREDIT BILLING *************************************************/
StormBillingEventManager::attach('StormBillingResourceAdded', 'StormBillingResourceAdded');
StormBillingEventManager::attach('StormBillingCronLoop', 'StormBillingCreditBilling');

function StormBillingResourceAdded($client_id, $product_id, $hosting_id, $record_id)
{
    if(empty($client_id) || empty($product_id) || empty($hosting_id) || empty($record_id))
    {
        throw new Exception('Invalid parameters');
    }

    //Get Billing Settings For Product
    $row = mysql_get_row("SELECT billing_settings, module FROM StormBilling_settings WHERE product_id = ? AND enable = 1", array($product_id));
    $billing_settings = unserialize($row['billing_settings']);
    if($billing_settings['credit_billing']['enable'] != 1)
    {
        return false;
    }
    
    //get hosting
    $hosting    =   mysql_get_row("SELECT * FROM tblhosting WHERE id = ?", array($hosting_id));
    if($hosting['domainstatus'] != 'Active')
    {
        return; 
    }
    
    //Should be suspended?
    $suspend    =   mysql_get_row("SELECT * FROM StormBilling_autosuspend WHERE hosting_id = ?", array($hosting_id));
    if($billing_settings['credit_billing']['autosuspend'] && $suspend)
    {
        return true;
    }
    
    $record     = mysql_get_row("SELECT total FROM StormBilling_".strtolower($row['module'])."_prices WHERE record_id = ?", array($record_id));
    $credit     = mysql_get_row("SELECT credit, paid, warned FROM StormBilling_user_credits WHERE user_id = ? AND hosting_id = ?", array($client_id, $hosting_id));
    $client     = mysql_get_row("SELECT credit FROM tblclients WHERE id = ?", array($client_id)); echo mysql_error();
    
    
    if(!$client)
    {
        throw new Exception("Client not found!");
    }
	
    //Send warning emails
    if($credit['warned'] == 0 && $credit['credit'] + $client['credit'] < $billing_settings['credit_billing']['low_credit_notify'] && strtotime($suspend['email_sent']) + ($billing_settings['credit_billing']['email_interval'] * 60 * 60 * 3600) < time())
    {
        $values['customtype']   =   'general';
        $values["messagename"]  =   "Credit Warning";
        $values["id"]           =   $client_id;
        $values["customvars"]   =   base64_encode(serialize(array(
            "minimal_credit"    =>  $billing_settings['credit_billing']['low_credit_notify'],
        ))); 
        
        $results = localAPI("sendemail",$values,ModulesGarden::getAdmin());
        
        if($results['result'] == 'success')
        {
            mysql_safequery("UPDATE StormBilling_autosuspend SET email_sent = ? WHERE hosting_id = ?", array(
                time(),
                $hosting_id
            ));
            $credit['warned'] = 1;
        } 
    }
    elseif($credit['credit'] + $client['credit'] > $billing_settings['credit_billing']['low_credit_notify'])
    {
        $credit['warned'] = 0;
    }
      
    //Try to get credit from user account
    if($record['total'] >= $credit['credit'] && $client['credit'] > 0.01)
    {
        //We should get this amount
        $missingValue   =   ceil($record['total'] - $credit['credit']);
        if($missingValue > $billing_settings['credit_billing']['minimal_credit'])
        {
            $get    =   $missingValue;
        }
        else
        {
            $get    =   ceil($billing_settings['credit_billing']['minimal_credit']);
        }
        
        //Oups! User don't have money!
        if($get > $client['credit'])
        {
            $get = $client['credit'];
        }
        
        //Get Credit From User Account
        $res = localAPI('addcredit',array(
            'clientid'     =>  $client_id,
            'description'  =>  'Credit Pay For Hosting #'.$hosting_id.' ('.date('Y-m-d H:i:s').')',
            'amount'       =>  $get * (-1)
        ),  ModulesGarden::getAdmin());

        if($res['result'] == 'success')
        {
            //Calculate Value For Credit
            $credit['credit'] = $get + $credit['credit'];
            //Update User Credit
            mysql_safequery("REPLACE INTO StormBilling_user_credits (`hosting_id`, `user_id`, `credit`, `paid`, `warned`) VALUES(?, ?, ?, ?, ?)", array(
                $hosting_id,
                $client_id,
                $credit['credit'],
                $credit['paid'],
                $credit['warned']
            ));
        }
    }
    
    //User don't have enough funds. It is working only if autosuspend is enabled
    if($record['total'] > $credit['credit'] && $billing_settings['credit_billing']['autosuspend'] == 1)
    {
        $invoice_id = StormBillingGenerateInvoice($hosting_id, $product_id); 
        if($invoice_id !== false)
        {
            //Delete Usage Records
            StormBillingDeleteUsageRecord($hosting_id, $product_id); 
            
            //Suspend Account
            if($billing_settings['credit_billing']['autosuspend'])
            {
                mysql_safequery("REPLACE INTO StormBilling_autosuspend (`hosting_id`, `invoice_id`, `suspended`) VALUES(?, ?, ?)", array(
                    $hosting_id,
                    $invoice_id,
                    0
                ));
            }
            
            //Is it possible to pay some part of invoice?
            if($credit['credit'] > 0.01)
            {
                //Round amount
                $transaction = round($credit['credit'], 2, PHP_ROUND_HALF_UP);
                //Add transaction
                $res = localAPI('addinvoicepayment',array(
                    'invoiceid'     =>  $invoice_id,
                    'transid'       =>  'Credit Pay For Hosting #'.$hosting_id.' - '.date('Y-m-d H:i:s'),
                    'amount'        =>  $credit['paid'] + $transaction,
                    'paymentmethod' => StormBilling_getHostingPaymentMethod($hosting_id),
                    'noemail'       =>  true
                ),  ModulesGarden::getAdmin());
                 
     
                if($res['result'] == 'success')
                {
                    mysql_safequery("REPLACE INTO StormBilling_user_credits (`hosting_id`, `user_id`, `credit`, `paid`, `warned`) VALUES(?, ?, ?, ?, ?)", array(
                        $hosting_id,
                        $client_id,
                        $credit['credit'] - $transaction,
                        0,
                        $credit['warned']
                    ));
                }
            }
        }
        //Delete Usage Record
        StormBillingDeleteUsageRecord($hosting_id, $product_id);
    }
	
    if($record['total'] <= $credit['credit'])
    {
        //Update Hosting Credit
        mysql_safequery("REPLACE INTO StormBilling_user_credits (`hosting_id`, `user_id`, `credit`, `paid`, `warned`) VALUES(?, ?, ?, ?, ?)", array(
            $hosting_id,
            $client_id,
            $credit['credit'] - $record['total'],
            $credit['paid'] + $record['total'],
            $credit['warned']
        ));
    }
}

function StormBillingCreditBilling($product_id, $billing_settings)
{
    if($billing_settings['credit_billing']['enable'] != 1)
    {
        return false;
    }

    //Get Invoice Interval
    $interval = intval($billing_settings['credit_billing']) > 0 ? intval($billing_settings['credit_billing']): 1;
    
    //Get accounts
    $accounts = mysql_get_array("SELECT h.id, h.userid, d.*
        FROM tblhosting h
        LEFT JOIN StormBilling_hosting_details d ON d.hosting_id = h.id
        WHERE h.packageid = ? AND (invoice_date IS NULL OR DATE(`invoice_date`) <= DATE_SUB(NOW(), INTERVAL ".$interval." DAY)) ", array($product_id));
    
    //Nothing to do
    if(!$accounts)
    {
        return false;
    }
    
    //Create Product Class
    $p = new SBProduct($product_id);
    //Get Server Type
    $type = $p->getServerType();
    
    foreach($accounts as $account)
    {
        //This will be first invoice for this hosting so we don't have invoice date
        if(!$account['invoice_date'])
        {
            $has_records = mysql_get_row("SELECT record_id FROM StormBilling_".$type."_prices 
                    WHERE DATE(`date`) <= DATE_SUB(NOW(), INTERVAL ".$interval." DAY) AND product_id = ? AND hosting_id = ? LIMIT 1", array($product_id, $account['id']));
            //Nope?
            if(!$has_records)
            {
                continue;
            }
        }
        
        //Calulate start date and end date
        $start_date =   date('Y-m-d', strtotime('-'.$interval.' DAY'));
        $end_date   =   date('Y-m-d');
        
        //get total price from records
        $total = mysql_get_row("SELECT SUM(`total`) as `total` FROM StormBilling_".$type."_prices WHERE product_id = ? AND hosting_id = ?", array(
            $product_id,
            $account['id'],
        ));
            
        //Generate Invoice
        $invoice_id = StormBillingGenerateInvoice($account['id'], $product_id);
        if($invoice_id > 0)
        {
            //Already paid
            $paid = 0;
            
            //Get User credit
            $credit = mysql_get_row("SELECT * FROM StormBilling_user_credits WHERE hosting_id = ? AND user_id = ?", array($account['id'], $account['userid']));
            
            //Get absolute value
            $abs = abs($total['total'] - $credit['paid']);
            if($abs <= 0.01)
            {
                $invoice    =   mysql_get_row("SELECT total FROM tblinvoices WHERE id = ?", array($invoice_id));
                //Just to be sure.
                $abs2       =   abs($invoice['total'] - $total['total']);
                if($abs2 < 1)
                {
                    $paid   =   $invoice['total'];
                }
                else
                {
                    $paid = $credit['paid'];
                }
            }
            else
            {
                $paid = $credit['paid'];
            }
             
            
            $res = localAPI('addinvoicepayment',array(
                'invoiceid'     =>  $invoice_id,
                'transid'       =>  'Credit Pay For Hosting #'.$account['id'].' - '.date('Y-m-d H:i:s'),
                'amount'        =>  round($paid, 2),
                'paymentmethod' =>  StormBilling_getHostingPaymentMethod($account['id']),
                'noemail'       =>  true
            ),  ModulesGarden::getAdmin());
                            
            if($res['result'] == 'success')
            {
                //Everything fine so update hosting details and remove invoice 
                mysql_safequery("REPLACE INTO StormBilling_hosting_details(`hosting_id`, `invoice_date`) VALUES(?, NOW())", array(
                    $account['id'],
                ));

                mysql_safequery("UPDATE StormBilling_user_credits SET paid = ? WHERE hosting_id = ? AND user_id = ?", array(
                    0,
                    $account['id'],
                    $account['userid']
                ));
            }
        }
        
        StormBillingDeleteUsageRecord($account['id'], $product_id);
    }
}
/************************************* END OF CREDIT BILLING *****************************************/

/************************************* MONTHLY BILLING **********************************************/
StormBillingEventManager::attach('StormBillingCronLoop', 'StormBillingMonthlyBilling');

function StormBillingMonthlyBilling($product_id, $billing_settings)
{
    if($billing_settings['credit_billing']['enable'])
    {
        return false;
    }
    //IF bill on invoice generate is enabled we cannot run this function
    if($billing_settings['bill_on_invoice_generate'])
    {
        return false;
    }
    //Monthly billing is enabled?
    if($billing_settings['bill_per_month'] != 1)
    {
        return false;
    }
    //Get current day
    $c_date = getdate(); 
    if(isset($_REQUEST['mday']))
    {
        $c_date['mday'] =   $_REQUEST['mday'];
    }
    
    if($c_date['mday'] != '1')
    {
        return false;
    }
    
    //Create Product Class
    $p = new SBProduct($product_id);
    //Get Server Type
    $type = $p->getServerType();

    $start_date     =   date('Y-m-01 00:00:00', strtotime('-1 month'));
    $end_date       =   date('Y-m-t 23:59:59', strtotime('-1 month'));
    
    if(isset($_REQUEST['mday']))
    {
        $start_date = date('Y-m-01 00:00:00');
        $end_date = date('Y-m-d H:i:s');
    }
    
    //Get accounts
    $accounts = mysql_get_array("SELECT h.id, h.userid
        FROM tblhosting h
        WHERE h.packageid = ?", array($product_id));
    
    foreach($accounts as $account)
    {
        $has_records = mysql_get_row("SELECT record_id FROM StormBilling_".$type."_prices 
                WHERE DATE(`date`) BETWEEN ? AND ? AND product_id = ? AND hosting_id = ? LIMIT 1", array($start_date, $end_date, $product_id, $account['id']));
        
        //Nope?
        if(!$has_records)
        {
            continue;
        }
            
        if($billing_settings['autogenerate_invoice'] == 1)
        {
            $invoice_id = StormBillingGenerateInvoice($account['id'], $product_id, $start_date, $end_date, array(
                'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
                'duedate'           =>  $billing_settings['billing_duedate']
            ));
            
            if($invoice_id !== false)
            {
                StormBillingDeleteUsageRecord($account['id'], $product_id, $start_date, $end_date);
            }
        }
        else
        {
            $invoice_id = StormBillingGenerateAwaitingInvoice($account['id'], $product_id, $start_date, $end_date, array(
                'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
                'duedate'           =>  $billing_settings['billing_duedate']
            ));
            
            if($invoice_id !== false)
            {
                StormBillingDeleteUsageRecord($account['id'], $product_id, $start_date, $end_date);
            }
        }  
    }
}
/************************************** END OF MONTHLY BILLING ***************************************/
 

/************************************** BILL EACH DAY ******************************************/
StormBillingEventManager::attach('StormBillingCronLoop', 'StormBillingEachDayBilling');

function StormBillingEachDayBilling($product_id, $billing_settings)
{
    if($billing_settings['credit_billing']['enable'])
    {
        return false;
    }
    
    //IF bill on invoice generate is enabled we cannot run this function
    if($billing_settings['bill_on_invoice_generate'])
    {
        return false;
    }
    //Monthly billing is enabled?
    if($billing_settings['bill_per_month'] == 1)
    {
        return false;
    }
     
    if(intval($billing_settings['billing_period']) == 0)
    {
        return false;
    }
    
    //Create Product Class
    $p = new SBProduct($product_id);
    //Get Server Type
    $type = $p->getServerType();
    //interval
    $interval = intval($billing_settings['billing_period']);
    
    $start_date = date('Y-m-d', strtotime('-'.$interval.' days'));
    $end_date = date('Y-m-d');
    
        //Get accounts
    $accounts = mysql_get_array("SELECT h.id, h.userid
        FROM tblhosting h
        WHERE h.packageid = ?", array($product_id));
    
    foreach($accounts as &$account)
    {
        $has_records = mysql_get_row("SELECT record_id, record_id, DATE(`date`) as `date` FROM StormBilling_".$type."_prices 
                WHERE DATE(`date`) <= ? AND product_id = ? AND hosting_id = ? LIMIT 1", array($start_date, $product_id, $account['id']));
        //Nope?
        if(!$has_records)
        {
            continue;
        } 
        
        //Change start date to first record
        $start_date = $has_records['date'];
        
        if($billing_settings['autogenerate_invoice'] == 1)
        {
            $invoice_id = StormBillingGenerateInvoice($account['id'], $product_id, $start_date, $end_date, array(
                'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
                'duedate'           =>  $billing_settings['billing_duedate']
            ));
            
            if($invoice_id !== false)
            {
                StormBillingDeleteUsageRecord($account['id'], $product_id, $start_date, $end_date);
            }
        }
        else
        {
            $invoice_id = StormBillingGenerateAwaitingInvoice($account['id'], $product_id, $start_date, $end_date, array(
                'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
                'duedate'           =>  $billing_settings['billing_duedate']
            ));
            
            if($invoice_id !== false)
            {
                StormBillingDeleteUsageRecord($account['id'], $product_id, $start_date, $end_date);
            }
        }
    }
}

/************************************** BILL EACH DAY ******************************************/
StormBillingEventManager::attach('StormBillingCronLoop', 'StormBillingAutomationAutoSuspend');

function StormBillingAutomationAutoSuspend($product_id, $billing_settings)
{
    if(!$billing_settings['automation']['autosuspend']['enable'])
    {

        
        return false;
    }
        
    //Get accounts
    $accounts = mysql_get_array("SELECT h.id, h.userid, h.regdate
        FROM tblhosting h
        WHERE 
        h.packageid = ? 
        AND DATE_SUB(regdate, INTERVAL ".((int)$billing_settings['automation']['autosuspend']['interval'])." DAY) < NOW()
        AND domainstatus = 'Active'
        ", array($product_id));
    
    if($accounts)
    {
        return false;
    }
    
    foreach($accounts as $account)
    {
        StormBillingLogger::info('Suspending account with ID: '.$account['id']);
        $results = localAPI("modulesuspend", array
        (
            'accountid'     =>  $account['id'],
            'suspendreason' =>  $billing_settings['automation']['autosuspend']['message'],
        ), ModulesGarden::getAdmin());
    }
}
/*************************************** END OF BILL EACH DAY **********************************/


$modules_products = mysql_get_array("SELECT product_id FROM StormBilling_settings
    WHERE enable = 1");

if($modules_products)
{
    StormBillingLogger::info('Starting...');
    
    foreach($modules_products as $product)
    {
        //products settings
        $p = new SBProduct($product['product_id']);
        $settings = $p->getSettings();
        //Billing Settings
        $billing_settings = $settings['billing_settings'];
        
        //get resource settings
        $resources_settings = $p->getResources(); 
        if($resources_settings === false)
        {
            StormBillingLogger::error('Cannot get resource settings for: '.$p->getServerType());
            continue;
        }

        //Module Exists?
        if(!$p->module())
        {
            StormBillingLogger::error('Cannot get module!');
            continue;
        }
        
        $m_start = time();
        $p->module()->getSample();
        StormBillingLogger::info('Getting data from server... Module: '.$p->getServerType().' Product ID '.$product['product_id'].' Time: '.(time() - $m_start));
        
        //Run 
        StormBillingEventManager::call('StormBillingCronLoop', $product['product_id'], $billing_settings);
    }

    //DONE!
    StormBillingLogger::info('Done. Running time: '.(time() - $start));
}
        
echo 'done';