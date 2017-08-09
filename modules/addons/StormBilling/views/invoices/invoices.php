<?php


echo '<div class="border-box">
        <div class="control-group">
            <strong>Awaiting Invoice</strong> means, that it will be not generated until you will confirm that manually.<br />
            This is the best way to avoid any unwanted invoices for your clients and test the possibilities of <strong>Cloud Billing</strong> module. <br /><br />
            To enable this feature, please edit your Products in <a href="addonmodules.php?module=StormBilling&modpage=configuration">Configuration</a> page and disable "Autogenerate Invoice" option.
        </div>
      </div>';

if(!$invoices)
{
    addInfo(MG_Language::translate('Nothing to display'));
}
else
{
    echo '<table class="table pagination" id="mg_invoices">';
    echo '<thead>
            <tr>
                <th class="span1">'.MG_Language::translate('Show').'</th>
                <th>'.MG_Language::translate('User').'</th>
                <th>'.MG_Language::translate('Account').'</th>
                <th>'.MG_Language::translate('Products').'</th>
                <th class="span2">'.MG_Language::translate('Total').'</th>
                <th class="span2">'.MG_Language::translate('Invoice Date').'</th>
                <th class="span2">'.MG_Language::translate('Invoice Duedate').'</th>
                <th class="span1">'.MG_Language::translate('Delete').'</th>
            </tr>
          </thead>
          <tbody>';
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
    echo '</tbody>
        </table>';
    echo '<div class="pagination pagination-right">'.$pagination->getPagination().'</div>';
}
?>
