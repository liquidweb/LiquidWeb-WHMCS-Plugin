<script type="text/javascript">
    jQuery(function(){
        jQuery(".OnDemandBill").click(function(){
            return confirm("Awaiting invoice will be created. Are you sure you want to continue?");
        });
    });
</script>

<?php

echo '<div class="border-box">
        <div class="control-group">
            <strong>Items</strong> list will show you <strong>current usage counted by the StormBilling Module for all accounts</strong>
        </div>
      </div>';

echo '<div style="overflow: auto">';
echo '<table class="table pagiantion" id="mg_items">
    <thead>
        <tr style="white-space:nowrap;">';
        foreach($resources as &$r)
        {
            echo '<th>'.
                    MG_Language::translate($r['FriendlyName']).'
                    <span class="tooltip-box">
                        <a data-original-title="'.$r['Description'].'" rel="tooltip" data-placement="top" href="#">
                            <i class="icon-question-sign"></i>
                        </a>
                    </span>
                </th>';
        }
        echo '<th style="width: 90px">'.MG_Language::translate('Total').'</th>';
        echo '<th style="width: 90px">'.MG_Language::translate('Last Update').'</th>';
echo '  </tr>
        </thead>';

echo '    <tbody>';
foreach($items as &$item)
{
    echo '<tr>';
    foreach($resources as $r_key => &$r)
    {
        echo '<td><span class="tooltip-box"><a class="nocolor" href="#" data-placement="top" rel="tooltip" data-original-title="'.$currency['prefix'].(number_format($item['p.'.$r_key], 3)).$currency['suffix'].'">'.number_format($item['r.'.$r_key], 3).' '.($r['unit']).'</a></span></td>';
    }

    echo '<td style="white-space: nowrap;">'.$currency['prefix'].(number_format($item['total'], 2)).$currency['suffix'].'</td>';
    echo '<td style="white-space: nowrap;">'.$item['date'].'</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '<tfoot class="blue-foot">
        <tr class="level1">
            <td style="text-align: center" colspan="'.(count($resources)+2).'">'.MG_Language::translate('Total for <b>all</b> usage records').'</td>
        </tr>
        <tr class="level2">';
        foreach($resources as $u => &$r)
        {
            echo '<td>';
            if($r['type'] == 'disabled')
            {
                echo MG_Language::translate('Disabled');
            }
            else
            {
                if(!empty($summary_usage[$u]['AdminAreaDescription']))
                {
                    echo '<a href="#">'.$summary_usage[$u]['AdminAreaDescription'].'</a></span>';
                }
                else
                {
                    if($r['type'] == 'average')
                    {
                        echo '<span class="tooltip-box"><a href="#" data-placement="top" rel="tooltip" data-original-title="Total Average Usage Of '.$r['FriendlyName'].' Usage Record">'.number_format($summary_usage[$u]['usage'], 3).' '.$summary_usage[$u]['unit'].'</a></span>';
                    } 
                    elseif($r['type'] == 'summary')
                    {
                        echo '<span class="tooltip-box"><a href="#" data-placement="top" rel="tooltip" data-original-title="Total Summary Usage Of '.$r['FriendlyName'].' Usage Record">'.number_format($summary_usage[$u]['usage'], 3).' '.$summary_usage[$u]['unit'].'</a></span>';
                    }
                }
            } 
            echo '</td>';
        }
        echo '<td></td>
              <td></td>';
echo '</tr>
        <tr class="level3">';
        foreach($resources as $u => &$r)
        {
            echo '<td>';
            if($r['type'] == 'disabled')
            {
                echo '-';
            }
            else
            {
                echo '<span class="tooltip-box"><a href="#" data-placement="top" rel="tooltip" data-original-title="Total Cost Of '.$r['FriendlyName'].' Usage Record">'.$currency['prefix'].(number_format($summary_usage[$u]['total'], 2)).$currency['suffix'].'</a></span>';
            }
            echo '</td>';
        }
        echo '<td></td>
              <td></td>
      </tfoot>';
echo '
    
    </table>';
echo '</div>';
echo '<div style="clear: both; overflow: hidden; margin-top: 20px;">';
if(!$settings['credit_billing']['enable'])
{
    echo '
                <form action="" method="post" class="form form-inline" style="float: left">
                    <input type="hidden" name="modaction" value="onDemandBill" />
                 <input type="submit" class="btn btn-inverse OnDemandBill" value="On Demand Bill"/>
             </form>';
}
echo '
            <div class="pagination pagination-right" style="float: right; margin-top: -20px!important">'.$pagination->getPagination().'</div>
      </div>';