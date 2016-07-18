<?php

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

/**
 * Call functions when modules is terminated. This function should genererate invoice for termianted account.
 * It is working only if credit billing is disabled!
 * @param type $params 
 */


function StormBilling_ClientAreaPage($params){
    
    global $smarty;
    
    require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'core.php');
    
    $order_template         = dirname(__FILE__).DIRECTORY_SEPARATOR.'views/order/order_template.tpl';
    $template_clientarea    = dirname(__FILE__).DIRECTORY_SEPARATOR.'views/clientarea/template.tpl';  
    $hosting_id             = $smarty->get_template_vars('id');
    $product_id             = $smarty->get_template_vars('pid');
    $product_info           = $smarty->get_template_vars('productinfo');

        if($product_info['pid'])
        {
            $q = mysql_safequery('SELECT product_id FROM StormBilling_settings WHERE product_id = ? AND enable=1', array($product_info['pid'])); 
            if(mysql_num_rows($q))
            {
                $currency = mysql_get_row("SELECT prefix, suffix FROM tblcurrencies ORDER BY id ASC LIMIT 1" );
                $product_id = $product_info['pid'];
                 
                $product = new SBProduct($product_id);
                
                $conf = $product->getSettings();
                if($conf['enable'])
                {
                    $resources = $product->getResources();

                    foreach($resources as $key => &$res)
                    {
                        if($res['type'] == 'disabled')
                        {
                            unset($resources[$key]);
                            continue;
                        }

                        $res['price'] = $currency['prefix'].$res['price'].$currency['suffix'];
                    }

                    if($resources)
                    {  
                        $smarty->assign('resources', $resources);
                        $smarty->assign( 'mg_lang', MG_Language::getLang() );

                        $tpl_order = $smarty->fetch($order_template);
                        return array('order_pricing' => $tpl_order);
                    }
                }
            }
        }
        if($hosting_id && $hosting_id != null){
            $q = mysql_safequery('SELECT product_id FROM StormBilling_settings WHERE product_id = ? AND enable=1', array($product_id)); 
            if(mysql_num_rows($q))
            { 
                $currency   = mysql_get_row("SELECT prefix, suffix FROM tblcurrencies ORDER BY id ASC LIMIT 1" );
                $product    = new SBProduct($product_id);
                $conf       = $product->getSettings();
                if($conf['enable'])
                {
                    $account    = new StormBillingAccount($hosting_id);
                    //Get Summay usage
                    $summary    = $account->getSummary($product_id);
                    $resources  = $product->getResources();


                    foreach($resources as $key_r => &$r)
                    {
                        $r['total']                     =   $currency['prefix'].number_format($summary[$key_r]['total'], 2).$currency['suffix'];
                        $r['usage']                     =   number_format($summary[$key_r]['usage'], 2);
                        $r['price']                     =   $currency['prefix'].$summary[$key_r]['price'].$currency['suffix'];
                        $r['FriendlyName']              =   MG_Language::translate($r['FriendlyName']);
                        $r['ClientAreaDescription']     =   $summary[$key_r]['ClientAreaDescription'];
                    }

                    if($conf['billing_settings']['credit_billing']['enable'] == 1)
                    {
                        $row2 = mysql_get_row("SELECT * FROM StormBilling_user_credits WHERE hosting_id = ? AND user_id = ?", array($hosting_id, $_SESSION['uid'])); 
                        $row2['total']      =   $row2['credit'] + $row2['paid'];
                        $row2['credit']     =   $currency['prefix'].number_format($row2['credit'], 2).$currency['suffix'];
                        $row2['paid']       =   $currency['prefix'].number_format($row2['paid'], 2).$currency['suffix']; 
                        $row2['total']      =   $currency['prefix'].number_format($row2['total'], 2).$currency['suffix'];

                        $smarty->assign('credit_billing', $row2); 
                    }
                    $smarty->assign('mg_lang', MG_Language::getLang()); 
                    $smarty->assign('resources', $resources);
                    $tpl_clientarea = $smarty->fetch($template_clientarea);
                    return array('clientarea_pricing' => $tpl_clientarea);
                }
            }
        }
        
        
        
        
        
}
 
 
add_hook('ClientAreaPage', 1, 'StormBilling_ClientAreaPage');


function StormBilling_AfterModuleTerminate($params)
{
    //include required files
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'core.php';
    //get account details
    $hosting_id =   $params['params']['accountid'];
    $product_id =   $params['params']['pid'];
    $user_id    =   $params['params']['clientsdetails']['id'];
    
    $row = mysql_get_row("SELECT billing_settings, module FROM StormBilling_settings WHERE product_id = ? AND enable = 1", array($product_id));
    if(!$row)
    {
        return false;
    }
        
    $billing_settings = unserialize($row['billing_settings']);
    
    if($billing_settings['bill_on_terminate'] != 1)
    {
        return false;
    }
    
    if($billing_settings['credit_billing']['enable'] == 1)
    {
        return true;
    }
    
    //Create Product Class
    $p = new SBProduct($product_id);
    //Get Server Type
    $type = $p->getServerType();
    //Get First Record
    $first_record = mysql_get_row("SELECT DATE(`date`) as `date` FROM StormBilling_".$type."_prices 
                WHERE product_id = ? AND hosting_id = ? ORDER BY record_id ASC LIMIT 1", array($product_id, $hosting_id));
    if(!$first_record)
    {
        return false;
    }
    
    $start_date =   $first_record['date'];
    $end_date   =   date('Y-m-d');
    
    if($billing_settings['autogenerate_invoice'] == 1)
    {
        $invoice_id = StormBillingGenerateInvoice($hosting_id, $product_id, $start_date, $end_date, array(
            'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
            'duedate'           =>  $billing_settings['billing_duedate']
        ));
        
        if($invoice_id !== false)
        {
            StormBillingDeleteUsageRecord($hosting_id, $product_id, $start_date, $end_date);
        } 
    }
    else
    {
        $invoice_id = StormBillingGenerateAwaitingInvoice($hosting_id, $product_id, $start_date, $end_date, array(
            'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
            'duedate'           =>  $billing_settings['billing_duedate']
        ));
        
        if($invoice_id !== false)
        {
            StormBillingDeleteUsageRecord($hosting_id, $product_id, $start_date, $end_date);
        }
    }
}
add_hook('AfterModuleTerminate', 100, 'StormBilling_AfterModuleTerminate');
 
function StormBilling_InvoiceCreationPreEmail($params)
{
    global $CONFIG;
    if($CONFIG['NoInvoiceEmailOnOrder'])
    {
        return;
    }
    
    //get invoice id 
    $invoice_id = $params['invoiceid'];
    
    //include required files
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'core.php';
    
    $invoice = mysql_get_row("SELECT status, userid FROM tblinvoices WHERE id = ?", array($invoice_id));
    $items = mysql_get_array("SELECT i.relid, h.packageid
        FROM tblinvoiceitems i
        LEFT JOIN tblhosting h ON i.relid = h.id
        WHERE i.invoiceid = ? AND i.type = 'Hosting'", array($invoice_id));
      
    foreach($items as &$item)
    {
        $hosting_id =   $item['relid'];
        $product_id =   $item['packageid'];
        
        $row = mysql_get_row("SELECT enable, billing_settings, module FROM StormBilling_settings WHERE product_id = ? AND enable = 1", array($product_id));
        if(!$row)
        {
            continue;
        }
        
        $billing_settings = unserialize($row['billing_settings']);

        //Should we bill this account?
        if(!$billing_settings['bill_on_invoice_generate'])
        {
            continue;
        }
        
        if($billing_settings['credit_billing']['enable'] == 1)
        {
            continue;
        }

        //Create Product Class
        $p = new SBProduct($product_id);
        //Get Server Type
        $type = $p->getServerType();
        //Get First Record
        $first_record = mysql_get_row("SELECT DATE(`date`) as `date` FROM StormBilling_".$type."_prices 
                    WHERE product_id = ? AND hosting_id = ? ORDER BY record_id ASC LIMIT 1", array($product_id, $hosting_id));
        if(!$first_record)
        {
            continue;
        }

        $start_date =   $first_record['date'];
        $end_date   =   date('Y-m-d');

        if(strtolower($invoice['status']) == 'unpaid')
        {
            $invoice_id = StormBillingUpdateInvoice($invoice_id, $hosting_id, $product_id, $start_date, $end_date, array(
                'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
                'duedate'           =>  $billing_settings['billing_duedate']
            ));

            if($invoice_id !== false)
            {
                StormBillingDeleteUsageRecord($hosting_id, $product_id, $start_date, $end_date);
            } 
        }
        else
        {
            $invoice_id = StormBillingGenerateInvoice($hosting_id, $product_id, $start_date, $end_date, array(
                'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
                'duedate'           =>  $billing_settings['billing_duedate']
            ));

            if($invoice_id !== false)
            {
                StormBillingDeleteUsageRecord($hosting_id, $product_id, $start_date, $end_date);
            } 
        }
    }
}
add_hook('InvoiceCreationPreEmail', 100, 'StormBilling_InvoiceCreationPreEmail');


/**
 * 
 * @global type $CONFIG
 * @param type $params
 * @return type
 */
function StormBilling_InvoiceCreated($params)
{
    global $CONFIG;
    if(!$CONFIG['NoInvoiceEmailOnOrder'])
    {
        return;
    }

    //get invoice id 
    $invoice_id = $params['invoiceid'];
    
    //include required files
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'core.php';
    
    $invoice = mysql_get_row("SELECT status, userid FROM tblinvoices WHERE id = ?", array($invoice_id));
    $items = mysql_get_array("SELECT i.relid, h.packageid
        FROM tblinvoiceitems i
        LEFT JOIN tblhosting h ON i.relid = h.id
        WHERE i.invoiceid = ? AND i.type = 'Hosting'", array($invoice_id));
    
    foreach($items as &$item)
    {
        $hosting_id =   $item['relid'];
        $product_id =   $item['packageid'];
        
        $row = mysql_get_row("SELECT enable, billing_settings, module FROM StormBilling_settings WHERE product_id = ? AND enable = 1", array($product_id));
        if(!$row)
        {
            continue;
        }
        
        $billing_settings = unserialize($row['billing_settings']);
        
        //Should we bill this account?
        if(!$billing_settings['bill_on_invoice_generate'])
        {
            continue;
        }
        
        if($billing_settings['credit_billing']['enable'] == 1)
        {
            continue;
        }

        //Create Product Class
        $p = new SBProduct($product_id);
        //Get Server Type
        $type = $p->getServerType();
        //Get First Record
        $first_record = mysql_get_row("SELECT DATE(`date`) as `date` FROM StormBilling_".$type."_prices 
                    WHERE product_id = ? AND hosting_id = ? ORDER BY record_id ASC LIMIT 1", array($product_id, $hosting_id));
        if(!$first_record)
        {
            continue;
        }

        $start_date =   $first_record['date'];
        $end_date   =   date('Y-m-d');

        if(strtolower($invoice['status']) == 'unpaid')
        {
            $invoice_id = StormBillingUpdateInvoice($invoice_id, $hosting_id, $product_id, $start_date, $end_date, array(
                'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
                'duedate'           =>  $billing_settings['billing_duedate']
            ));

            if($invoice_id !== false)
            {
                StormBillingDeleteUsageRecord($hosting_id, $product_id, $start_date, $end_date);
            } 
        }
        else
        {
            $invoice_id = StormBillingGenerateInvoice($hosting_id, $product_id, $start_date, $end_date, array(
                'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
                'duedate'           =>  $billing_settings['billing_duedate']
            ));

            if($invoice_id !== false)
            {
                StormBillingDeleteUsageRecord($hosting_id, $product_id, $start_date, $end_date);
            } 
        }
    }
}
add_hook('InvoiceCreated', 100, 'StormBilling_InvoiceCreated');

/**
 * Create invoice when product package is changed
 * @param type $params
 * @return type
 */
function StormBilling_AfterProductUpgrade($params)
{
    //include required files
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'core.php';
    
    $upgrade_id     =   $params['upgradeid'];
    $upgrade        =   mysql_get_row("SELECT u.*, c.id as client_id
                                        FROM tblupgrades u
                                        LEFT JOIN tblhosting h ON u.relid = h.id
                                        LEFT JOIN tblclients c ON h.userid = c.id
                                        WHERE u.id = ? AND u.type = 'package'", array($upgrade_id));
    $hosting_id     =   $upgrade['relid'];
    $product_id     =   $upgrade['originalvalue'];
    $user_id        =   $upgrade['client_id'];

    $row = mysql_get_row("SELECT billing_settings, module FROM StormBilling_settings WHERE product_id = ? AND enable = 1", array($product_id));
    if(!$row)
    {
        return false;
    }
        
    $billing_settings = unserialize($row['billing_settings']);
    
    //Create Product Class
    $p = new SBProduct($product_id);
    //Get Server Type
    $type = $p->getServerType();
    //Get First Record
    $first_record = mysql_get_row("SELECT DATE(`date`) as `date` FROM StormBilling_".$type."_prices 
                WHERE product_id = ? AND hosting_id = ? ORDER BY record_id ASC LIMIT 1", array($product_id, $hosting_id));
    if(!$first_record)
    {
        return false;
    }
    
    $start_date =   $first_record['date'];
    $end_date   =   date('Y-m-d');
    
    if($billing_settings['credit_billing']['enable'] == 1)
    {
        //Generate Invoice
        $invoice_id = StormBillingGenerateInvoice($hosting_id, $product_id, $start_date, $end_date);
        //Invoice generated? Try to pay it!
        if($invoice_id > 0)
        {
            //Get User Credit
            $credit     =   mysql_get_row("SELECT * FROM StormBilling_user_credits WHERE hosting_id = ? AND user_id = ?", array($hosting_id, $user_id));
            //Should we refund something?
            $refund = 0;
            //Get Invoice details
            $invoice    =   mysql_get_row("SELECT * FROM tblinvoices WHERE id = ?", array($invoice_id));
            //Count amount
            $amount     =   $credit['credit'] + $credit['paid'];
            if($amount > $invoice)
            {
                $amount = $invoice;
            }

            if($amount)
            {
                $res = localAPI('addtransaction',array(
                    'userid'        =>  $user_id,
                    'invoiceid'     =>  $invoice_id,
                    'description'   =>  'Refund For Hosting #'.$hosting_id,
                    'amountin'      =>  $amount,
                    'paymentmethod' => StormBilling_getHostingPaymentMethod($hosting_id)
                ),  ModulesGarden::getAdmin());
            }

            $refund = $amount < $credit['credit'] + $credit['paid'];
        }
        else
        {
            $refund = $credit['credit'];
        }

        if($refund > 0)
        {
            $res = localAPI('addcredit',array(
                'clientid'     =>  $user_id,
                'description'  =>  'Refund For Hosting #'.$hosting_id,
                'amount'       =>  $refund
            ),  ModulesGarden::getAdmin());
        }
        
        if($invoice_id !== false)
        {
            StormBillingDeleteUsageRecord($hosting_id, $product_id, $start_date, $end_date);
        }
        mysql_safequery("DELETE FROM StormBilling_user_credits WHERE hosting_id = ? AND user_id = ?", array($hosting_id, $user_id));
    }
    else
    {
        if($billing_settings['autogenerate_invoice'] == 1)
        {
            $invoice_id = StormBillingGenerateInvoice($hosting_id, $product_id, $start_date, $end_date, array(
                'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
                'duedate'           =>  $billing_settings['billing_duedate']
            ));

            if($invoice_id !== false)
            {
                StormBillingDeleteUsageRecord($hosting_id, $product_id, $start_date, $end_date);
            } 
        }
        else
        {
            $invoice_id = StormBillingGenerateAwaitingInvoice($hosting_id, $product_id, $start_date, $end_date, array(
                'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
                'duedate'           =>  $billing_settings['billing_duedate']
            ));

            if($invoice_id !== false)
            {
                StormBillingDeleteUsageRecord($hosting_id, $product_id, $start_date, $end_date);
            }
        }
    }
}
add_hook('AfterProductUpgrade', 100,  'StormBilling_AfterProductUpgrade'); 


function StormBilling_AfterModuleChangePackage($params)
{
    if(!$_SESSION['adminid'])
    {
        return;
    }
        //include required files
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'core.php';

    
    $current_pid    =   $params['params']['pid'];
    $hosting_id     =   $params['params']['serviceid'];
    $user_id        =   $params['clientsdetails']['id'];
 
    $products = mysql_get_array("SELECT product_id FROM StormBilling_settings WHERE enable = 1");
     
    foreach($products as $product)
    {
        $product_id = $product['product_id'];
        
        if($current_pid == $product_id)
        {
            continue;
        }
        //Create Product Class
        $p = new SBProduct($product_id);
        //Get Server Type
        $type = $p->getServerType();
        //Get First Record
        $first_record = mysql_get_row("SELECT DATE(`date`) as `date` FROM StormBilling_".$type."_prices 
                    WHERE product_id = ? AND hosting_id = ? ORDER BY record_id ASC LIMIT 1", array($product_id, $hosting_id));
        if(!$first_record)
        {
            continue;
        }
        
        //Get Billing Settings
        $row = mysql_get_row("SELECT billing_settings, module FROM StormBilling_settings WHERE product_id = ? AND enable = 1", array($product_id));
        $billing_settings = unserialize($row['billing_settings']);
        

        $start_date =   $first_record['date'];
        $end_date   =   date('Y-m-d');

        if($billing_settings['credit_billing']['enable'] == 1)
        {
            //Generate Invoice
            $invoice_id = StormBillingGenerateInvoice($hosting_id, $product_id, $start_date, $end_date);
            //Invoice generated? Try to pay it!
            if($invoice_id > 0)
            {
                //Get User Credit
                $credit     =   mysql_get_row("SELECT * FROM StormBilling_user_credits WHERE hosting_id = ? AND user_id = ?", array($hosting_id, $user_id));
                //Should we refund something?
                $refund = 0;
                //Get Invoice details
                $invoice    =   mysql_get_row("SELECT * FROM tblinvoices WHERE id = ?", array($invoice_id));
                //Count amount
                $amount     =   $credit['credit'] + $credit['paid'];
                if($amount > $invoice)
                {
                    $amount = $invoice;
                } 

                if($amount)
                {
                    $res = localAPI('addtransaction',array(
                        'userid'        =>  $user_id,
                        'invoiceid'     =>  $invoice_id,
                        'description'   =>  'Credit Pay For Hosting #'.$hosting_id,
                        'amountin'      =>  $amount,
                        'paymentmethod' => StormBilling_getHostingPaymentMethod($hosting_id)
                    ),  ModulesGarden::getAdmin());
                }

                $refund = $amount < $credit['credit'] + $credit['paid'];
            }
            else
            {
                $refund = $credit['credit'];
            }

            if($refund > 0)
            {
                $res = localAPI('addcredit',array(
                    'clientid'     =>  $user_id,
                    'description'  =>  'Refund For Hosting #'.$hosting_id,
                    'amount'       =>  $refund
                ),  ModulesGarden::getAdmin());
            }

            if($invoice_id !== false)
            {
                StormBillingDeleteUsageRecord($hosting_id, $product_id, $start_date, $end_date);
            }
            mysql_safequery("DELETE FROM StormBilling_user_credits WHERE hosting_id = ? AND user_id = ?", array($hosting_id, $user_id));
        }
        else
        {
            if($billing_settings['autogenerate_invoice'] == 1)
            {
                $invoice_id = StormBillingGenerateInvoice($hosting_id, $product_id, $start_date, $end_date, array(
                    'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
                    'duedate'           =>  $billing_settings['billing_duedate']
                ));

                if($invoice_id !== false)
                {
                    StormBillingDeleteUsageRecord($hosting_id, $product_id, $start_date, $end_date);
                } 
            }
            else
            {
                $invoice_id = StormBillingGenerateAwaitingInvoice($hosting_id, $product_id, $start_date, $end_date, array(
                    'autoapplycredit'   =>  $billing_settings['autoapplycredit'],
                    'duedate'           =>  $billing_settings['billing_duedate']
                ));

                if($invoice_id !== false)
                {
                    StormBillingDeleteUsageRecord($hosting_id, $product_id, $start_date, $end_date);
                }
            }
        }
    }
}
add_hook('AfterModuleChangePackage', 100, 'StormBilling_AfterModuleChangePackage');




/**
 * This function is called when invoice is paid. 
 * @param type $params
 */
function StormBilling_CreditPay_InvoicePaid($params)
{
    //include required files
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'core.php';

    $row = mysql_get_row("SELECT * FROM StormBilling_autosuspend WHERE invoice_id = ?", array($params['invoiceid']));
    if($row)
    { 
        $adminuser = ModulesGarden::getAdmin();
        $res = localAPI("moduleunsuspend", array(
            'accountid'    =>  $row['hosting_id']
        ),$adminuser);

        mysql_safequery("DELETE FROM StormBilling_autosuspend WHERE invoice_id = ? AND hosting_id = ?", array(
            $row['invoice_id'],
            $row['hosting_id']
        ));

        mysql_safequery("UPDATE StormBilling_user_credits SET warned = ? WHERE hosting_id = ?", array(
            0,
            $row['hosting_id']
        ));
    }
}
add_hook('InvoicePaid', 100, 'StormBilling_CreditPay_InvoicePaid');

/**
 * Generate invoice when credit pay is enabled. This function should also refund some credit to user account
 * @param type $params
 */
function StormBilling_CreditPay_AfterModuleTerminate($params)
{
    //include required files
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'core.php';
    //get account details
    $hosting_id =   $params['params']['accountid'];
    $product_id =   $params['params']['pid'];
    $user_id    =   $params['params']['clientsdetails']['id'];
    
    $row = mysql_get_row("SELECT billing_settings, module FROM StormBilling_settings WHERE product_id = ? AND enable = 1", array($product_id));
    $billing_settings = unserialize($row['billing_settings']);
    if($billing_settings['credit_billing']['enable'] != 1)
    {
        return false;
    }
    //Get User Credit
    $credit     =   mysql_get_row("SELECT * FROM StormBilling_user_credits WHERE hosting_id = ? AND user_id = ?", array($hosting_id, $user_id));
    //Should we refund something?
    $refund = 0;
    //Generate Invoice
    $invoice_id = StormBillingGenerateInvoice($hosting_id, $product_id);
    //Invoice generated? Try to pay it!
    if($invoice_id > 0)
    {
        //Get Invoice details
        $invoice    =   mysql_get_row("SELECT * FROM tblinvoices WHERE id = ?", array($invoice_id));
        
        $amount     =   $credit['credit'] + $credit['paid'];
        if($amount > $invoice)
        {
            $amount = $invoice;
        }
        
        if($amount)
        {
            $res = localAPI('addtransaction',array(
                'userid'        =>  $user_id,
                'invoiceid'     =>  $invoice_id,
                'description'   =>  'Credit Pay For Hosting #'.$hosting_id,
                'amountin'      =>  $amount,
                'paymentmethod' => StormBilling_getHostingPaymentMethod($hosting_id)
            ),  ModulesGarden::getAdmin());
        }
        
        $refund = $amount < $credit['credit'] + $credit['paid'];
    }
    else
    {
        $refund = $credit['credit'];
    }
    
    if($refund > 0)
    {
        $res = localAPI('addcredit',array(
            'clientid'     =>  $user_id,
            'description'  =>  'Refund For Hosting #'.$hosting_id,
            'amount'       =>  $refund
        ),  ModulesGarden::getAdmin());
    }
    
    StormBillingDeleteUsageRecord($hosting_id, $product_id);
    mysql_safequery("DELETE FROM StormBilling_user_credits WHERE hosting_id = ? AND user_id = ?", array($hosting_id, $user_id));
}
add_hook('PreModuleTerminate', 100, 'StormBilling_CreditPay_AfterModuleTerminate');


/**
 * Delete Account Data When Hosting Was Deleted
 * @param type $params
 */
function StormBilling_ServiceDelete($params)
{
    //include required files
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'core.php';
    
    $products = mysql_get_array("SELECT product_id FROM StormBilling_settings");
    
    foreach($products as $product)
    {
        $product_id = $product['product_id'];
        //Delete Usage Records
        StormBillingDeleteUsageRecord($params['serviceid'], $product_id);
        //Delete User Credit
        mysql_safequery("DELETE FROM StormBilling_user_credits WHERE hosting_id = ?", array($params['serviceid'])); 
    }
}

 
add_hook('ServiceDelete', 100, 'StormBilling_ServiceDelete');

