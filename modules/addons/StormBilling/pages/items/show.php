<?php
//Get Hosting ID
$hosting_id = (int)$_REQUEST['id'];
//Load Product Class
require_once StormBillingDIR.DS.'class.SBProduct.php';
//IS ID setup?
if(!$hosting_id)
{
    addError(MG_Language::translate('Hosting ID Not Set'));
    header('Location: ?module=StormBilling&modpage=items');
    exit;
}

//Is exists in hosting id?
$row = mysql_get_row("SELECT hosting_id FROM StormBilling_billed_hostings WHERE hosting_id = ?", array($hosting_id));
if(!$row)
{
    addError(MG_Language::translate('Wrong Hosting ID'));
    header('Location: ?module=StormBilling&modpage=items');
    exit;
}

//Have connected product?
$row = mysql_get_row("SELECT packageid FROM StormBilling_billed_hostings u
    LEFT JOIN tblhosting h ON u.hosting_id = h.id
    WHERE hosting_id = ?", array($hosting_id));
if(!$row)
{
    addError(MG_Language::translate('Cannot Found Product'));
    header('Location: ?module=StormBilling&modpage=items');
    exit;
}
//product ID
$product_id = $row['packageid'];

//Create New Product
$p = new SBProduct($product_id);
if(!$p->isSupported())
{
    addError(MG_Language::translate('Product is not supported'));
    header('Location: ?module=StormBilling&modpage=items');
    exit;
}

//Get Type
$type = $p->getServerType();

//Get settings
$settings = $p->getBillingSettings();

if(isset($_REQUEST['modaction']))
{
    switch($_REQUEST['modaction'])
    {
        case 'onDemandBill':
            if($settings['credit_billing']['enable'])
            {
                addError('Cannot run this options when credit billing is enabled');
                break;
            }
            
            $invoiceId = StormBillingGenerateAwaitingInvoice($hosting_id, $product_id);
            if($invoiceId === false)
            {
                addError('Cannot generate awaiting invoice');
            }
            else
            {
                //clean old resultes
                StormBillingDeleteUsageRecord($hosting_id, $product_id);
                
                //show info
                addInfo('Awaiting invoice has been generated');
                
                //redirect!
                ob_clean();
                header('Location: addonmodules.php?module=StormBilling&modpage=invoices&modsubpage=show&id='.$invoiceId);
                exit;
            }
            
            break;
    }
}


$row = mysql_get_row("SELECT count(record_id) as `count` FROM StormBilling_".$type."_prices WHERE hosting_id = ? AND product_id = ?", array($hosting_id, $product_id));
$records_count = $row['count'];

if(!$records_count)
{
    mysql_safequery("DELETE FROM StormBilling_billed_hostings WHERE hosting_id = ?", array($hosting_id));
    addError(MG_Language::translate('Account have not any usage records'));
    header('Location: ?module=StormBilling&modpage=items');
    exit;
}

//Enable pagination. We don't want all records!
$pagination = new MG_Pagination("mg_items");
$pagination->resetFilter();
$pagination->setAmount($records_count);
$pagination->setLimit(20);

//Get Available Resources
$resources = $p->getResources();

$usage_table = StormBilling_columnPrefix(array_keys($resources) ,'r');
$price_table = StormBilling_columnPrefix(array_keys($resources) ,'p');
$items = mysql_get_array("SELECT p.total, r.id, r.date, ".$usage_table.", ".$price_table." FROM StormBilling_".$type."_prices as p
        LEFT JOIN StormBilling_".$type."_records r ON p.record_id = r.id 
        WHERE p.hosting_id = ? AND p.product_id = ? ORDER BY `date` DESC ".$pagination->getLimitAndOffset(), array($hosting_id, $product_id));

//Change Unit
foreach($resources as $res_key => &$res)
{
    if(isset($res['AvailableUnits']) && array_key_exists($res['unit'], $res['AvailableUnits']))
    {
        $factor = $res['AvailableUnits'][$res['unit']];
        foreach($items as &$item)
        { 
            if($item['r.'.$res_key] > 0)
            {
                $item['r.'.$res_key] *= $factor;
            }
        }
    }
}

//Get Summary Usage
$account = new StormBillingAccount($hosting_id);
$summary_usage = $account->getSummary($product_id);


//Get Curreny
$currency = mysql_get_row("SELECT prefix, suffix FROM tblcurrencies ORDER BY id ASC LIMIT 1" );
//Get Hosting and Client Details
$details = mysql_get_row("SELECT tblhosting.id, domain, firstname, lastname
        FROM tblhosting 
        LEFT JOIN tblclients ON tblhosting.userid = tblclients.id
        WHERE tblhosting.id = ?", array($_REQUEST['id']));

//Set header
$PAGE_SUBMODULE_HEADING = '#'.$details['id'].' ('.($details['domain'] ? $details['domain'] : MG_Language::translate('no hostname')).') '/*.$details['firstname'].' '.$details['lastname']*/;
