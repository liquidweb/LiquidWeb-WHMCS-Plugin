<?php

switch($_REQUEST['modsubpage'])
{
    case 'show':
    {
        //Get Hosting ID
        $hosting_id = (int)$_REQUEST['id'];

        require_once StormBillingDIR.DS.'class.SBProduct.php';

        $row = mysql_get_row("SELECT packageid FROM StormBilling_billed_hostings u
            LEFT JOIN tblhosting h ON u.hosting_id = h.id
            WHERE hosting_id = ?", array($hosting_id));
        //product ID
        $product_id = $row['packageid'];
        
        $p = new SBProduct($product_id);
        $type = $p->getServerType();
        //Get Records Count
        $row = mysql_get_row("SELECT count(record_id) as `count` FROM StormBilling_".$type."_prices WHERE hosting_id = ? AND product_id = ?", array($hosting_id, $product_id));
        $records_count = $row['count'];

        $pagination = new MG_Pagination("mg_items");
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
        foreach($resources as $res_key => $res)
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
    }
    break;
    
    default:
    {
        $pagination = new MG_Pagination("mg_accounts");

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

    }
    break;
}

$currency = mysql_get_row("SELECT prefix, suffix FROM tblcurrencies ORDER BY id ASC LIMIT 1" );