<?php
if(isset($_REQUEST['modaction']))
{
    switch($_REQUEST['modaction'])
    {
        case 'delete':
        {
            mysql_safequery("DELETE FROM StormBilling_awaiting_invoices WHERE id = ?", array($_REQUEST['itemid']));
            addInfo(MG_Language::translate('Awaiting Invoice Deleted'));
        }
        break;
    }
}

$row = mysql_get_row("SELECT count(a.userid) as `count`
        FROM StormBilling_awaiting_invoices a
        LEFT JOIN tblclients ON a.userid = tblclients.id 
        LEFT JOIN tblhosting h ON a.hostingid = h.id
        LEFT JOIN tblproducts p ON h.packageid = p.id
        ORDER BY a.id ASC");

$pagination = new MG_Pagination("mg_invoices");
$pagination->setPage(0);
$pagination->setLimit(25);
$pagination->setAmount($row['count']);

$invoices = mysql_get_array("SELECT a.*, tblclients.firstname, tblclients.lastname, tblclients.id as client_id, h.domain, h.id as hosting_id, p.name as product, p.id as product_id
        FROM StormBilling_awaiting_invoices a
        LEFT JOIN tblclients ON a.userid = tblclients.id 
        LEFT JOIN tblhosting h ON a.hostingid = h.id
        LEFT JOIN tblproducts p ON h.packageid = p.id
        ORDER BY id ASC ".$pagination->query());

foreach($invoices as &$invoice)
{
    $invoice['items'] = unserialize($invoice['items']);
    $invoice['total'] = 0;
    foreach($invoice['items'] as $i)
    {
        $invoice['total'] += doubleval($i['amount']);
    }
    $invoice['total'] = number_format($invoice['total'], 2);
}

$currency = mysql_get_row("SELECT prefix, suffix FROM tblcurrencies ORDER BY id ASC LIMIT 1" );