<?php

/**********************************************************************
 *  StormBilling Trunk (2014-02-26)
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

if(isset($_REQUEST['modaction']))
{
    switch($_REQUEST['modaction'])
    {
        case 'refund':
            $client_id  =   $_REQUEST['client_id'];
            $hosting_id =   $_REQUEST['hosting_id'];
            
            $row = mysql_get_row("SELECT * FROM StormBilling_user_credits WHERE hosting_id = ? AND user_id = ?", array(
                $hosting_id,
                $client_id
            ));
            
            if(!$row)
            {
                addError('Nothing to do!');
                ob_clean();
                header('Location: addonmodules.php?module=StormBilling&modpage=credits');
                exit;
                break;
            }
            
            $total = round($row['credit'] + $row['paid'], 2);
            if($total < 0.01)
            {
                addError('Cannot refund money! Refunded amount cannot be lower that 0.01');
                ob_clean();
                header('Location: addonmodules.php?module=StormBilling&modpage=credits');
                exit;
                break;
            }
            
            //Made refund
            $res = localAPI('addcredit',array(
                'clientid'     =>  $client_id,
                'description'  =>  'Refund For Hosting #'.$hosting_id.' ('.date('Y-m-d H:i:s').')',
                'amount'       =>  $total
            ),  ModulesGarden::getAdmin());
        
            if($res['result'] == 'success')
            {
                mysql_safequery("DELETE FROM StormBilling_user_credits  WHERE hosting_id = ? AND user_id = ?", array(
                    $hosting_id,
                    $client_id
                ));
                addInfo('Money has been returned to the client credit');
            }
            
            ob_clean();
            header('Location: addonmodules.php?module=StormBilling&modpage=credits');
            exit;
            
            break;
    }
}

$pagination = new MG_Pagination("StormBilling_Credits");
$pagination->resetFilter();

$row = mysql_get_row("SELECT COUNT(StormBilling_user_credits.hosting_id) as `count`  FROM StormBilling_user_credits");
$pagination->setAmount($row['count']);

$clients = mysql_get_array("SELECT uc.*, c.firstname, c.lastname, h.domain 
    FROM StormBilling_user_credits uc
    LEFT JOIN tblclients c ON uc.user_id = c.id
    LEFT JOIN tblhosting h ON uc.hosting_id = h.id
".$pagination->getLimitAndOffset());

