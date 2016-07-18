<?php

switch($_REQUEST['modsubpage'])
{
    case 'show':
    {
        foreach($items as &$item)
        {
            echo '<tr>';
            foreach($resources as $r_key => &$r)
            {
                echo '<td><span class="tooltip-box"><a class="nocolor" href="#" data-placement="top" rel="tooltip" data-original-title="'.$currency['prefix'].(number_format($item['p.'.$r_key], 2)).$currency['suffix'].'">'.number_format($item['r.'.$r_key], 3).' '.($r['unit']).'</a></span></td>';
            }

            echo '<td style="white-space: nowrap;">'.$currency['prefix'].(number_format($item['total'], 2)).$currency['suffix'].'</td>';
            echo '<td style="white-space: nowrap;">'.$item['date'].'</td>';
            echo '</tr>';
        }
    }
    break;
    
    default:
    {
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
    }
    break;
}
?>
