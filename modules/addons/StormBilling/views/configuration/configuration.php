<?php

$crondir =  substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), 'views'.DIRECTORY_SEPARATOR.'configuration')).'cron'.DIRECTORY_SEPARATOR;
echo '<div class="border-box">
        <div class="control-group">
        <strong style="color: #cc0000">Please note:</strong><br />
            <strong>To enable automatic synchronization, please setup a following command in the cron (each 5 minutes suggested):</strong> <br />
            <em>*/5 * * * *    php -q '.$crondir.'cron.php'.'</em>
            <br /><br />
            <strong>To enable automatic account suspension for credit billing, please setup a following command in the cron (each 3 minutes suggested):</strong> <br />
            <em>*/3 * * * *    php -q '.$crondir.'autosuspend.php'.'</em>
        </div>
      </div>';


if($disabled)
{
    echo '<h2>'.MG_Language::translate('Enable Billing').'</h2>';
    echo '<script type="text/javascript">
            jQuery(function(){
                jQuery("#enable_module").change(function(){
                    if($(this).val() == "---")
                    {
                        return false;
                    }
                    document.location = "addonmodules.php?module=StormBilling&modpage=configuration&modsubpage=edit&auto_enable=1&id="+$(this).val();
                });
            });
          </script>

        <form action="" method="post" class="form-horizontal form-low">
        <input type="hidden" value="enable" name="modaction" />
        <div class="border-box">

            <div class="control-group">
                <label class="control-label">'.MG_Language::translate('Enable StormBilling for: ').'</label>
                <div class="controls">
                    <select name="module" id="enable_module">
                        <option value="---">---</option>';
                    foreach($disabled as $id =>$p)
                    {
                        echo '<option value="'.$id.'">'.$p['group'].' - '.$p['product_name'].'</option>';
                    }
               echo'</select>
                </div>
            </div>
         </div>
      </form>';
}


if($enabled)
{
    echo '<h2 style="margin-bottom: 20px">'.MG_Language::translate('Enabled Products').'</h2>';
    echo '<table class="table">';
    echo '<thead>';
    echo '</thead>';
    echo '<tr>
            <th>'.MG_Language::translate('Product').'</th>
            <th style="width: 180px">'.
                MG_Language::translate('Used Module').'
                <span class="tooltip-box">
                    <a data-original-title="'.MG_Language::translate('Currently Used Module').'" rel="tooltip" data-placement="top" href="#">
                        <i class="icon-question-sign"></i>
                    </a>
                </span>
            </th>
            <th style="width: 180px">'.
                MG_Language::translate('Auto Generate Invoice').'
                <span class="tooltip-box">
                    <a data-original-title="'.MG_Language::translate('Create Automatically Invoices').'" rel="tooltip" data-placement="top" href="#">
                        <i class="icon-question-sign"></i>
                    </a>
                </span>
            </th>
            <th style="width: 150px">'.
                MG_Language::translate('Bill on Terminate').'
                <span class="tooltip-box">
                    <a data-original-title="'.MG_Language::translate('Bill Clients When Product is Terminated').'" rel="tooltip" data-placement="top" href="#">
                        <i class="icon-question-sign"></i>
                    </a>
                </span>
            </th>
            <th style="width: 150px">'.
                MG_Language::translate('Credit Billing').'
                <span class="tooltip-box">
                    <a data-original-title="'.MG_Language::translate('Bill Your Client For Current Usage').'" rel="tooltip" data-placement="top" href="#">
                        <i class="icon-question-sign"></i>
                    </a>
                </span>
            </th>
        </tr>';

    echo '<tbody>';
    foreach($enabled as $id => $product)
    {
        $p = new SBProduct($id);
        $billing = $p->getBillingSettings();
        $class_name = get_class($p->module());

        echo '<tr>
                <td>
                    <a href="addonmodules.php?module=StormBilling&modpage=configuration&modsubpage=edit&id='.$id.'">'.$product['group'].' - '.$product['product_name'].'</a>
                </td>
                <td>'.MG_Language::translate(constant($class_name.'::name')).'</td>
                <td>'.($billing['autogenerate_invoice'] ? '<span class="text-success">'.MG_Language::translate('enabled').'</span>' : '<span class="text-warning">'.MG_Language::translate('disabled').'</span>').'</td>
                <td>'.($billing['bill_on_terminate'] ? '<span class="text-success">'.MG_Language::translate('enabled').'</span>' : '<span class="text-warning">'.MG_Language::translate('disabled').'</span>').'</td>
                <td>'.($billing['credit_billing']['enable'] ? '<span class="text-success">'.MG_Language::translate('enabled').'</span>' : '<span class="text-warning">'.MG_Language::translate('disabled').'</span>').'</td>
              </tr>';
    }
    echo '</tbody>';
    echo '</table>';
}
?>
