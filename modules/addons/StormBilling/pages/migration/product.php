<?php

include_once SB_DIR.DS.'class.SBProduct.php';

$q = mysql_safequery("
    SELECT
        BS.product_id
        ,BS.enable
        ,BS.module 
        ,P.name AS product_name
        ,G.name AS `group`
        ,P.servertype
        ,BS.billing_settings 	
        ,BS.resource_settings
    FROM 
        StormServersBilling_settings BS
    JOIN
        tblproducts P
        ON
            P.id = BS.product_id
    JOIN
        tblproductgroups G
        ON 
            G.id = P.gid
    WHERE 
        BS.enable = 1
        AND BS.product_id = ?
        ",array($_REQUEST['id']));

    $product = mysql_fetch_assoc($q);
    $product['resource_settings'] = unserialize($product['resource_settings']);
    $resources_prices = $product;

if($_REQUEST['savesettings'])
{
    $p = new SBProduct($_REQUEST['id'],$product);
    $settings = $p->getSettings();
    $resources_prices = $settings;
    $p->saveSettings(array 
        (
            'enable'                        =>  isset($_REQUEST['enable_billing']) ? 1 : 0,
            'billing_methods'               =>  array
            (
                'bill_on_terminate'         =>  isset($_REQUEST['bill_on_terminate']) ? 1 : 0,
                'bill_on_invoice_generate'  =>  isset($_REQUEST['autogenerate_invoice']) ? 1 : 0,
                'bill_per_month'            =>  isset($_REQUEST['bill_per_month']) ? 1 : 0,
                'bill_per_days'             =>  isset($_REQUEST['bill_per_days']) && $_REQUEST['bill_per_days'] > 0 ? 0 : (int)$_REQUEST['bill_per_days'],
            ),
            'invoice_settings'              =>  array
            (
                'duedate'                   =>  isset($_REQUEST['bill_duedate']) && $_REQUEST['bill_duedate'] >= 0 ? (int)$_REQUEST['bill_duedate'] : 7,
                'autogenerate'              =>  isset($_REQUEST['autogenerate_invoice']) ? 1 : 0,
                'autoapplycredit'           =>  isset($_REQUEST['autoapplycredit']) ? 1 : 0,
            ),
            'billing_settings'              =>  array
            (
                'bill_per_month'            =>  isset($_REQUEST['bill_per_month']) ? 1 : 0,
                'billing_period'            =>  isset($_REQUEST['bill_on_invoice_generate']) ? 30 : (isset($_REQUEST['bill_per_month']) ? 'month' : (isset($_REQUEST['bill_per_days']) ? $_REQUEST['bill_per_days'] : 30)),
                'billing_duedate'           =>  isset($_REQUEST['bill_duedate']) && (int)$_REQUEST['bill_duedate'] >= 0 ? (int)$_REQUEST['bill_duedate'] : 7,
                'bill_on_terminate'         =>  isset($_REQUEST['bill_on_terminate']) ? 1 : 0,
                'bill_on_invoice_generate'  =>  isset($_REQUEST['bill_on_invoice_generate']) ? 1 : 0,
                'autogenerate_invoice'      =>  isset($_REQUEST['autogenerate_invoice']) ? 1 : 0,
                'autoapplycredit'           =>  isset($_REQUEST['autoapplycredit']) ? 1 : 0,
            ),
            'credit_billing'                =>  array
            (
                'enable'                    =>  isset($_REQUEST['credit_billing']['enable']) ? 1 :0,
                'billing_period'            =>  intval($_REQUEST['credit_billing']['billing_period']) >= 1 ? intval($_REQUEST['credit_billing']['billing_period']) : 1,
                'minimal_credit'            =>  floatval($_REQUEST['credit_billing']['minimal_credit']) > 1 ? floatval($_REQUEST['credit_billing']['minimal_credit']) : 1,
                'low_credit_notify'         =>  floatval($_REQUEST['credit_billing']['low_credit_notify']) > 1 ? floatval($_REQUEST['credit_billing']['low_credit_notify']) : 1,
                'autosuspend'               =>  isset($_REQUEST['credit_billing']['autosuspend']) ? 1 : 0,
            ),
            'resource_settings'             =>  $_REQUEST['resources'],
            'module_configuration'          =>  $_REQUEST['configuration'],
            'module'                        =>  $_REQUEST['settings']['module'] ? $_REQUEST['settings']['module'] : ''
        )
    );
    
    $p = new SBProduct($_REQUEST['id']);
            
    if($_REQUEST['generateAwaitingInvoiceNow'] == 'on')
    {
        
        // CHUJ WIE CZY TO DZIAŁA 
            $product_id = $_REQUEST['id'];

            $res = $p->getResources();
            $server_type = $p->getServerType();

            $period = $settings['billing_period'];

            if($settings['bill_on_invoice_generate'])
            {
                return false;
            }

            if($settings['bill_per_month'])
            {
                $period = 'month';
            }

            $accounts = mysql_get_array("SELECT DISTINCT hosting_id FROM StormServersBilling_resources WHERE product_id = ? AND type = ?", array($product_id, $server_type));

            $records = array();
            $account_usages = array();

            foreach($accounts as $acc)
            {
                $hosting_id = $acc['hosting_id'];

                $records = mysql_get_array("SELECT user_id, resources
                  FROM `StormServersBilling_resources` 
                  WHERE product_id = ? AND type = ? AND hosting_id = ? ", array($product_id, $server_type, $hosting_id)) or die(mysql_error());
                
                //Any records?
                if(!$records)
                {
                    continue; 
                }
                
                //Prepare accounts usage
                foreach($records as &$r)
                {
                    //unserialize data
                    $r['resources'] = unserialize(base64_decode($r['resources']));

                    //prepare records
                    $account_usages[$hosting_id]['product_id']  =   $product_id;
                    $account_usages[$hosting_id]['user_id']     =   $r['user_id'];
                    foreach($r['resources'] as $key => &$val)
                    {
                        if($res[$key]['unit'] && $val && isset($res[$key]['AvailableUnits'][$res[$key]['unit']]))
                        {
                            $val *= $res[$key]['AvailableUnits'][$res[$key]['unit']];
                        }

                        //Count quantity
                        if(!isset($account_usages[$hosting_id]['resources'][$key]['quantity']))
                        {
                            $account_usages[$hosting_id]['resources'][$key]['quantity'] = 0;
                        }
                        $account_usages[$hosting_id]['resources'][$key]['quantity']++;

                        if($val)
                        {
                            switch($res[$key]['type'])
                            {
                                case 'highest':
                                    if($val > $account_usages[$hosting_id]['resources'][$key]['highest'])
                                    {
                                        $account_usages[$hosting_id]['resources'][$key]['highest']         =   $val;
                                        $account_usages[$hosting_id]['resources'][$key]['computed_value']  =   $account_usages[$hosting_id]['resources'][$key]['highest'];
                                    }
                                    break;

                                case 'average':
                                    $account_usages[$hosting_id]['resources'][$key]['summary']         +=  $val;// round($val, 4); Rounding is disabled
                                    break;

                                case 'summary':
                                    $account_usages[$hosting_id]['resources'][$key]['summary']         +=  $val;// round($val, 4); Rounding is disabled
                                    $account_usages[$hosting_id]['resources'][$key]['computed_value']  =   $account_usages[$hosting_id]['resources'][$key]['summary'];
                                    break;
                            }
                        }
                    }

                    $r  =   null;
                }

                //Free Memory
                $records = null;

                //Compute average
                foreach($account_usages as &$account)
                {
                    foreach($account['resources'] as $key => &$resource)
                    {
                        if($res[$key]['type'] == 'average')
                        {
                            $resource['average']         =   $resource['summary'] / $resource['quantity'];
                            $resource['computed_value']  =   $resource['average'];
                        }
                    }
                }
            }            


            //Free Memory
            $accounts = null;

            //Compute Prices
            $accounts_computed = array();

            foreach($account_usages as $hosting_id => &$account_data)
            {
                foreach($account_data['resources'] as $u_name => &$resource)
                {
                    if(!in_array($res[$u_name]['type'], array('highest', 'summary', 'average')))
                    {
                        continue;
                    }

                    $computed_value =   $resource['computed_value'];
                    
                    if($computed_value < $resources_prices['resource_settings'][$u_name]['free_limit'])
                    {
                        continue;
                    }
                    
                    $computed_price                                                                             =   ($computed_value - $resources_prices['resource_settings'][$u_name]['free_limit']) * $resources_prices['resource_settings'][$u_name]['price']; // compute price
                    $accounts_computed[$hosting_id]['resources'][$res[$u_name]['FriendlyName']]['amount']       =   $computed_value - $resources_prices['resource_settings'][$u_name]['free_limit'];
                    $accounts_computed[$hosting_id]['resources'][$res[$u_name]['FriendlyName']]['unit_cost']    =   $resources_prices['resource_settings'][$u_name]['price'];
                    $accounts_computed[$hosting_id]['resources'][$res[$u_name]['FriendlyName']]['price']        =   $computed_price;
                    $accounts_computed[$hosting_id]['resources'][$res[$u_name]['FriendlyName']]['unit']         =   $res[$u_name]['unit'];

                    $accounts_computed[$hosting_id]['product_id']                                               =   $product_id;
                    $accounts_computed[$hosting_id]['user_id']                                                  =   $account_data['user_id'];
                }
            }
                
            foreach($accounts_computed as $hosting_id => &$values)
            {           
                //products
                $product = mysql_get_row("SELECT name, tax FROM tblproducts WHERE id = ?", array($product_id));
                //hosting
                $hosting = mysql_get_row("SELECT id, domain, paymentmethod FROM tblhosting WHERE id = ?", array($hosting_id));
                //items array
                $items = array();
                // invoice for domain 
                $postfields["action"]           =   "createinvoice";
                $postfields['paymentmethod']    = StormBilling_getHostingPaymentMethod($hosting_id);
                $postfields["userid"]           =   $values['user_id'];
                $postfields['autoapplycredit']  =   $settings['autoapplycredit'] ? 1 : 0;  

                $d = getdate(); 
                $postfields["date"] = $d['year'].($d['mon'] <= 9 ? '0'.$d['mon'] : $d['mon']).($d['mday'] <= 9 ? '0'.$d['mday'] : $d['mday']);
                $d = getdate(time()+(($settings['billing_duedate'] >=0 ? $settings['billing_duedate'] : 7)*24*60*60));
                $postfields["duedate"] = $d['year'].($d['mon'] <= 9 ? '0'.$d['mon'] : $d['mon']).($d['mday'] <= 9 ? '0'.$d['mday'] : $d['mday']);

                $product_line = $product['name'].' - ';

                if($hosting['domain'])
                {
                    $product_line .= $hosting['domain'].' - ';
                }
                else
                {
                    $product_line .= 'no domain - ';
                }

                if($settings['bill_per_month'])
                {
                    $product_line .='('.date('Y-m-01', strtotime("-1 month")).' - '.date('Y-m-d').')';
                }
                else
                {
                    $product_line .= '('.date('Y-m-d', strtotime("-".$settings['billing_period']." day")).' - '.date('Y-m-d').')';
                }

                $items[] = array
                (
                    'description'               =>  $product_line,
                    'amount'                    =>  0.00,
                    'taxed'                     =>  $product['tax']
                );

                $postfields["itemdescription1"] = $product_line;
                $postfields["itemamount1"]      = 0.00;
                $postfields["itemtaxed1"]       = $product['tax'];


                $period = $settings['billing_period'];
                if($settings['bill_per_month'])
                {
                    $period = '31';
                }

                $i = 2;
                foreach($values['resources'] as $resource => &$record)
                {            
                    if(round($record['price'], 3) <= 0)
                    {
                        continue;
                    }

                    $tmp = array(
                        'amount'                        =>  round($record['price'], 3),
                        'taxed'                         =>  $product['tax']
                    );

                    if($record['type'] == 'average')
                    {
                        $tmp['description'] = MG_Language::translate($resource).': '.$record['num'].' x '.number_format($record['amount'], 3).$record['unit'].' x '.$record['unit_cost'];
                    }
                    else
                    {
                        $tmp['description'] = MG_Language::translate($resource).': '.number_format($record['amount'], 3).$record['unit'].' x '.$record['unit_cost'];
                    }

                    $items[] = $tmp;
                    $postfields["itemdescription1".$i]  =   $tmp['description'];
                    $postfields["itemamount1".$i]       =   $tmp['amount'];
                    $postfields["itemtaxed1".$i]        =   $tmp['taxed'];
                    $i++;
                }  
                
                mysql_safequery('INSERT INTO StormServersBilling_awaiting_invoices (`userid`, `hostingid`, `date`, `duedate`, `items`) VALUES (?, ?, ?, ?, ?)', array(
                    $postfields['userid'],
                    $hosting_id,
                    $postfields['date'],
                    $postfields['duedate'],
                    serialize($items)
                )) or die(mysql_error());
                                
            }
    }
    
    mysql_safequery("UPDATE StormServersBilling_settings SET enable = 0 WHERE product_id = ?",array($_REQUEST['id']));

    addInfo(MG_Language::translate('Product was migrated successfully'));
    
    ob_clean();
    header('Location: addonmodules.php?module=StormBilling&modpage=migration');
    die();
}

$q = mysql_safequery("
            SELECT 
                H.id
                ,H.regdate 	
                ,H.domain
                ,U.id as clientid
                ,U.firstname 	
                ,U.lastname
            FROM 
                tblhosting H
            JOIN
                tblclients U
                ON 
                    U.id = H.userid
            WHERE
                H.packageid = ?
            ",array($_REQUEST['id']));

$accounts = array();

while($row = mysql_fetch_assoc($q))
{
    $accounts[$row['id']] = $row;
}

$q = mysql_safequery("
    SELECT
        BS.product_id
        ,BS.enable
        ,BS.module 
        ,P.name AS product_name
        ,G.name AS `group`
        ,P.servertype
        ,BS.billing_settings 	
        ,BS.resource_settings
    FROM 
        StormServersBilling_settings BS
    JOIN
        tblproducts P
        ON
            P.id = BS.product_id
    JOIN
        tblproductgroups G
        ON 
            G.id = P.gid
    WHERE 
        BS.enable = 1
        AND BS.product_id = ?
        ",array($_REQUEST['id']));

$product = mysql_fetch_assoc($q);

$product['billing_settings'] = unserialize($product['billing_settings']);
$product['resources'] = unserialize($product['resource_settings']);

$p = new SBProduct($_REQUEST['id'],$product);
$resources = $p->getResources();

$unitValues = array(
    'b'         => 1
    ,'kb'       => 1024
    ,'mb'       => 1048576
    ,'gb'       => 1073741824
    ,'tb'       => 1099511627776
    ,'bps'      => 1
    ,'kbps'     => 1024
    ,'mbps'     => 1048576
    ,'gbps'     => 1073741824
    ,'mhz'      => 1
    ,'ghz'      => 1000
);

foreach($product['resources'] as $name => &$tmpResource)
{
    if(isset($unitValues[strtolower($tmpResource['unit'])]))
    {
        $unitFound = 1;
        foreach($unitValues as $unitName => $unitValue)
        {
            if(strpos(strtolower($resources[$name]['Unit']),$unitName) === 0)
            {
                $unitFound = $unitValue;
                break;
            }
        }

        $tmpResource['newFree'] = (doubleval($tmpResource['free_limit'])*$unitValues[strtolower($tmpResource['unit'])])/$unitFound;
        $tmpResource['newPrize']= number_format((doubleval($tmpResource['price']*$unitValues[strtolower($tmpResource['unit'])])/$unitFound),6);
    }
    else
    {
        $tmpResource['newFree'] = $tmpResource['free_limit'];
        $tmpResource['newPrize']= $tmpResource['price'];
    }
}

$currency = mysql_get_row("SELECT prefix, suffix FROM tblcurrencies ORDER BY id ASC LIMIT 1" );