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


foreach($invoices as $i)
{
    echo '<tr>
            <td><a href="addonmodules.php?module=StormBilling&modpage=invoices&modsubpage=show&id='.$i['id'].'">'.MG_Language::translate('Show').'</a></td>
            <td><a href="clientssummary.php?userid='.$i['client_id'].'">'.$i['firstname'].' '.$i['lastname'].'</a></td>
            <td><a href="clientsservices.php?userid='.$i['client_id'].'&id='.$i['hosting_id'].'">'.($i['domain'] ? $i['domain'] : '(no domain)').'</a></td>
            <td><a href="configproducts.php?action=edit&id='.$i['product_id'].'">'.$i['product'].'</a></td>
            <td>'.$currency['prefix'].$i['total'].$currency['suffix'].'</td>
            <td>'.$i['date'].'</td>
            <td>'.$i['duedate'].'</td>
            <td>
                <form action="" method="post" style="margin: 0; text-align: center">
                    <input type="hidden" name="modaction" value="delete" />
                    <input type="hidden" name="itemid" value="'.$i['id'].'" />
                    <button class="btn-link btn-delete"><i class="icon-remove"></i></button>
                </form>
            </td>
          </tr>';
} 