<?php


if(isset($_REQUEST['modaction']))
{
    switch($_REQUEST['modaction'])
    {
        case 'delete':
            $client_id  =   $_REQUEST['client_id'];
            $hosting_id =   $_REQUEST['hosting_id'];
            $product_id =   $_REQUEST['product_id'];
            
            //Check credits
            $row = mysql_get_row("SELECT * FROM StormBilling_user_credits WHERE hosting_id = ? AND user_id = ?", array(
                $hosting_id,
                $client_id
            ));
            
            if($row)
            {
                addError('Cannot delete records. You need to refund credits before');
                break;
            }
        
            //Delete records
            StormBillingDeleteUsageRecord($hosting_id, $product_id);
            addInfo("Usage records has beed deleted");
            header('Location: addonmodules.php?module=StormBilling&modpage=items');
            exit;
            
            break;
    }
}

$pagination = new MG_Pagination("mg_accounts");
$pagination->resetFilter();


$row = mysql_get_row("SELECT COUNT(StormBilling_billed_hostings.hosting_id) as `count`  FROM StormBilling_billed_hostings");
$pagination->setAmount($row['count']);

$items = mysql_get_array("SELECT u.*, c.id as user_id, c.firstname, c.lastname, h.domain, p.name, p.id as product_id, s.module
    FROM StormBilling_billed_hostings u
    LEFT JOIN tblhosting h ON u.hosting_id = h.id
    LEFT JOIN tblproducts p ON h.packageid = p.id
    LEFT JOIN tblclients c ON h.userid = c.id
    LEFT JOIN StormBilling_settings s ON p.id = s.product_id
    ORDER BY h.id ASC 
    ".$pagination->getLimitAndOffset());
 

foreach($items as &$item)
{
    $amount         =   mysql_get_row("SELECT SUM(total) as `amount` FROM StormBilling_".strtolower($item['module'])."_prices WHERE hosting_id = ? AND product_id = ?", array($item['hosting_id'], $item['product_id']));
    $item['amount'] =   $amount['amount'];
}
unset($item);

$currency = mysql_get_row("SELECT prefix, suffix FROM tblcurrencies ORDER BY id ASC LIMIT 1" );