<?php

if($avaible)
{
    echo '<h2 style="margin-bottom: 20px">'.MG_Language::translate('Available Products').'</h2>';
    echo '<table class="table">';
    echo '<thead>';
    echo '<tr>
            <th>'.MG_Language::translate('Product').'</th>
            <th>'.MG_Language::translate('Used Module').'
                <span class="tooltip-box">
                    <a data-original-title="'.MG_Language::translate('Currently Used Module').'" rel="tooltip" data-placement="top" href="#"><i class="icon-question-sign"></i>
                    </a>
                </span>
            </th>
            <th>
                '.MG_Language::translate('Product Migration').'
            </th>
        </tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach($avaible as $id => $product)
    {

        echo '<tr>
                <td>
                    '.$product['group'].' - '.$product['product_name'].'
                </td>
                <td>'.$product['module'].'</td>
                <td>
                    <a href="addonmodules.php?module=StormBilling&modpage=migration&modsubpage=product&id='.$id.'" class="btn btn-success">'.MG_Language::translate('Migrate Product').'</a>
                </td>
              </tr>';
    }
    echo '</tbody>';
    echo '</table>';
}