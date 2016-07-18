<?php

/**********************************************************************
 *  StormBilling - 1.3 (2013-07-29)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 **********************************************************************/


/**
 * @author Mariusz Miodowski <mariusz@modulesgarden.com>
 */


$row = mysql_get_row("SELECT count(a.userid) as `count`
        FROM StormBilling_awaiting_invoices a
        LEFT JOIN tblclients ON a.userid = tblclients.id 
        LEFT JOIN tblhosting h ON a.hostingid = h.id
        LEFT JOIN tblproducts p ON h.packageid = p.id
        ORDER BY a.id ASC");

$pagination = new MG_Pagination("mg_invoices");
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
        $invoice['total'] += $i['amount'];
    }
    $invoice['total'] = number_format($invoice['total'], 2);
}

$currency = mysql_get_row("SELECT prefix, suffix FROM tblcurrencies ORDER BY id ASC LIMIT 1" );