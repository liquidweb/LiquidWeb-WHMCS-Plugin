<?php

if(isset($_REQUEST['modaction']))
{
    switch($_REQUEST['modaction'])
    {
        case 'generate':
        {
            /**
             * Rozdzielone na 2 etapy, pierw update danych z bazy, pozniej generowanie i usuniecie. Moze w przyszłosci bedziemy chciecli zostawic wygenerowane itemy
             */
            foreach($_REQUEST['item'] as &$item)
            {
                $item['taxed'] = $item['taxed'] == 'on' ? 1 : 0;
                $item['amount'] = floatval($item['amount']);
            }
            mysql_safequery("UPDATE StormBilling_awaiting_invoices 
                SET date = ?, duedate = ?, items = ? WHERE id = ?", array(
                    $_REQUEST['date'],
                    $_REQUEST['duedate'],
                    serialize($_REQUEST['item']),
                    $_REQUEST['id']
                ));
            
            
            $invoice = StormBilling_getAwaitingInvoice($_REQUEST['id']); 
            if($invoice) 
            {
                //hosting
                $hosting = mysql_get_row("SELECT paymentmethod FROM tblhosting WHERE id = ?", array($invoice['hostingid']));
        
                $postfields['action']           = 'createinvoice';
                $postfields['userid']           = $invoice['userid'];
                $postfields['date']             = $invoice['date'];
                $postfields['duedate']          = $invoice['duedate'];
                $postfields['paymentmethod']    = $hosting['paymentmethod'];
                $i = 1;
                foreach($invoice['items'] as $item)
                {
                    $postfields['itemdescription'.$i]   = $item['description'];
                    $postfields['itemamount'.$i]        = $item['amount'];
                    $postfields['itemtaxed'.$i]         = $item['taxed'];
                    $i++;
                }

                $result = localAPI('CreateInvoice', $postfields, ModulesGarden::getAdmin()); 
                if($result['result'] == 'success')
                {
                    addInfo(MG_Language::translate('Invoice Generated'));;
                    StormBilling_deleteAwaitingInvoices($invoice['id']);
                    header('Location: addonmodules.php?module=StormBilling&modpage=invoices');
                    exit;
                }
                else
                {
                    addError(MG_Language::translate('Canno generate invoice!'));
                }
            }
        }
    }
}

$invoice = mysql_get_row("SELECT a.*, tblclients.firstname, tblclients.lastname 
        FROM StormBilling_awaiting_invoices a
        LEFT JOIN tblclients ON a.userid = tblclients.id
        WHERE a.id = ?", array($_REQUEST['id']));
$invoice['items'] = unserialize($invoice['items']);

$currency = mysql_get_row("SELECT prefix, suffix FROM tblcurrencies ORDER BY id ASC LIMIT 1" );
?>
