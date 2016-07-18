<?php

echo '<div class="border-box">
        <div class="control-group">
            <strong>Items</strong> list will show you <strong>current usage counted by the StormBilling module for all accounts.</strong>
        </div>
      </div>';

if(!$items)
{
    addInfo(MG_Language::translate('Nothing to display'));
}
else
{
    echo '<table class="table pagination" id="mg_accounts" style="display:table !important;">
            <thead>
                <tr>
                    <th>'.MG_Language::translate('Account').'</th>
                    <th>'.MG_Language::translate('Product').'</th>
                    <th>'.MG_Language::translate('Client').'</th>
                    <th>'.MG_Language::translate('Total Amount').'</th>
                    <th>'.MG_Language::translate('Last Update').'</th>
                    <th class="span1">'.MG_Language::translate('Delete').'</th>
                </tr>
            </thead>';
    
    foreach($items as $item)
    {
        echo '<tr>
                <td><a href="clientshosting.php?userid='.$item['user_id'].'&id='.$item['hosting_id'].'">#'.$item['hosting_id'].' ('.($item['domain'] ? $item['domain'] : MG_Language::translate('no hostname')).')</a>  <a class="text-error" href="?module=StormBilling&modpage=items&modsubpage=show&id='.$item['hosting_id'].'">'.MG_Language::translate('Show usage records').'</a></td>
                <td><a href="configproducts.php?action=edit&id='.$item['product_id'].'">'.$item['name'].'</a></td>
                <td><a href="clientssummary.php?userid='.$item['user_id'].'">'.$item['firstname'].' '.$item['lastname'].'</a></td>
                <td>'.$currency['prefix'].(number_format($item['amount'], 2)).$currency['suffix'].'</td>
                <td>'.$item['date'].'</td> 
                <td>
                    <form action="" method="post" style="margin: 0; text-align: center">
                        <input type="hidden" name="modaction" value="delete" />
                        <input type="hidden" name="hosting_id" value="'.$item['hosting_id'].'" />
                        <input type="hidden" name="product_id" value="'.$item['product_id'].'" />
                        <input type="hidden" name="client_id" value="'.$item['user_id'].'" />
                        <button class="btn-link btn-delete"><i class="icon-remove"></i></button>
                    </form>
                </td>
            </tr>';
    }
    echo '</table>';
    echo '<div class="pagination pagination-right">'.$pagination->getPagination().'</div>';
}
?>
