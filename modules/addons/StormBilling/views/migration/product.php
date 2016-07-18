<?php

    echo '<h2 style="margin-bottom: 20px">'.MG_Language::translate('Migration Product ').$product['product_name'].'</h2>';
    
    echo '<form id="migrationForm" class="form-horizontal form-low" action="" method="post" >';
    echo '<input type="hidden" name="savesettings" value="true" />';
    
   echo '
       <div class="border-box">
        <ul class="nav nav-tabs">
            <li class="active">
                <a data-toggle="tab" href="#general-settings">'.MG_Language::translate('General Settings').'</a>
            </li>
            <li class="">
                <a data-toggle="tab" href="#invoice-settings">'.MG_Language::translate('Invoice Settings').'</a>
            </li>
            <li class="">
                <a data-toggle="tab" href="#billing-settings">'.MG_Language::translate('Billing Settings').'</a>
            </li>
            <li class="">
                <a data-toggle="tab" href="#credit-billing-settings">'.MG_Language::translate('Credit Billing').'</a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="general-settings" class="tab-pane active">
                <div class="control-group">
                    <label class="control-label"><h3><strong>'.MG_Language::translate('Enable').'</strong></h3></label>
                    <div class="controls">
                        <input type="checkbox" name="enable_billing" id="enable_billing_check" '.($product['enable'] == 1 || isset($_REQUEST['auto_enable']) ? 'checked' : '').' />
                        <span class="help-inline">'.MG_Language::translate('Uncheck This Box to DISABLE StormBilling For This Product').'</span>
                    </div>
                </div>
                ';
            echo'<div class="control-group">
                        <label class="control-label"><b>'.MG_Language::translate('Used Module').'</b></label>
                        <div class="controls">';

                    echo '<input type="hidden" name="settings[module]" value="'.$product['module'].'" />';
                    echo '<strong>'.$product['module'].'</strong>';
 
                echo '           </div>
                    </div>';
                echo '
            </div>
            <div id="invoice-settings" class="tab-pane">      
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Due Date').'</label>
                    <div class="controls">
                        <input class="span1" type="text" name="bill_duedate" id="bill_duedate_value" value="'.$product['billing_settings']['billing_duedate'].'" />
                        <span class="help-inline">'.MG_Language::translate('days (7 by default)').'</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Auto Apply Credits').'</label>
                    <div class="controls">
                        <input type="checkbox" name="autoapplycredit" '.($product['billing_settings']['autoapplycredit'] == 1 ? 'checked' : '').' />
                        <span class="help-inline">'.MG_Language::translate('Tick to Auto Apply Any Available Credit From The Clients Credit Balance').'</span>
                    </div>
                </div><!--- invoice settings -->
            </div>
            <div id="billing-settings" class="tab-pane">
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Bill on Terminate').'</label>
                    <div class="controls">
                        <input type="checkbox" value="1" name="bill_on_terminate" id="bill_on_terminate" '.($product['billing_settings']['bill_on_terminate'] == 1 ? 'checked' : '').' />
                        <span class="help-inline">'.MG_Language::translate('Bill Your Client After Account is Terminated').'</span>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Bill on Invoice Generate').'</label>
                    <div class="controls">
                        <input type="checkbox" value="1" name="bill_on_invoice_generate" id="bill_on_invoice_generate" '.($product['billing_settings']['bill_on_invoice_generate'] == 1 ? 'checked' : '').' />
                        <span class="help-inline">'.MG_Language::translate('Bill Your Client When Invoice is Generated For Hosting').'</span>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Bill Each 1st Day of Month').'</label>
                    <div class="controls">
                        <input type="checkbox" value="1" name="bill_per_month" id="bill_per_month_check" '.($product['billing_settings']['billing_period'] == 'month' ? 'checked' : '').' />
                        <span class="help-inline">'.MG_Language::translate('Create Invoices Each 1st Day of Month').'</span>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Autogenerate Invoice').'</label>
                    <div class="controls">
                        <input type="checkbox" name="autogenerate_invoice" '.($product['billing_settings']['autogenerate_invoice'] == 1 ? 'checked' : '').' />
                        <span class="help-inline">'.MG_Language::translate('Check if You Want to Enable Auto Generating Invoice').'</span>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Create Invoices Each').'</label>
                    <div class="controls">
                        <input class="span1" type="text" name="bill_per_days" size="5" id="bill_per_days_value" value="'.($product['billing_settings']['billing_period'] == 'month' ? '' : $product['billing_settings']['billing_period']).'" '.($product['billing_settings']['billing_period'] == 'month' || $product['billing_settings']['bill_on_invoice_generate'] ? 'disabled="disabled"' : '').' />
                        <span class="help-inline">'.MG_Language::translate('Days (30 by default)').'</span>
                    </div>
                </div>
            </div>
            <div id="credit-billing-settings" class="tab-pane">
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Enable Credit Pay').'</label>
                    <div class="controls">
                        <input type="checkbox" name="credit_billing[enable]" '.($product['billing_settings']['credit_billing']['enable'] == 1 ? 'checked' : '').' />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Create Invoices Each').'</label>
                    <div class="controls">
                        <input class="span1" type="text" name="credit_billing[billing_period]" size="5"  value="'.$product['billing_settings']['credit_billing']['billing_period'].'" />
                        <span class="help-inline">'.MG_Language::translate('Days (30 by default)').'</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Minimal Credit').'</label>
                    <div class="controls">
                        <input class="span1" type="text" name="credit_billing[minimal_credit]" size="5"  value="'.$product['billing_settings']['credit_billing']['minimal_credit'].'" />
                        <span class="help-inline">'.MG_Language::translate('').'</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Low Credit Notify').'</label>
                    <div class="controls">
                        <input class="span1" type="text" name="credit_billing[low_credit_notify]" size="5"  value="'.$product['billing_settings']['credit_billing']['low_credit_notify'].'" />
                        <span class="help-inline">'.MG_Language::translate('Low Credit Notification').'</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Autosuspend').'</label>
                    <div class="controls">
                        <input type="checkbox" name="credit_billing[autosuspend]" '.($product['billing_settings']['credit_billing']['autosuspend'] == 1 ? 'checked' : '').' />
                        <span class="help-inline">'.MG_Language::translate('Autosusped Account When User Do Not Has Enough Funds').'</span>
                    </div>
                </div>
            </div>
        </div>';
                
    echo '<legend>Pricing</legend>';
           
    echo ' <div class="control-group">
                <label class="control-label" for="divprice">Divider Prices</label>
                <div class="controls">
                    <div class="input-append">
                        <input type="text" style="width:130px;" id="divprice" value="720" />
                        <button type="button" style="padding:1px 6px" class="btn"><i class="icon-refresh"></i></button>
                    </div>
                    <span class="help-inline">'.MG_Language::translate('By changing this value you can recalculate all prices counted hourly. For example 30 days * 24 hours = 720.').'</span>
                </div>
            </div>
            '; 
                
    echo '<table class="table tableprices table-condensed">';
    echo '<thead>';
    echo '</thead>';
    echo '<tr>
            <th>'.MG_Language::translate('Resource').'</th>
            <th>'.MG_Language::translate('Old Free Limit').'</th>
            <th>'.MG_Language::translate('Free Limit').'</th>
            <th>'.MG_Language::translate('Old Price').'</th>
            <th>'.MG_Language::translate('Price').'</th>
            <th>'.MG_Language::translate('Display Unit').'</th>
            <th>'.MG_Language::translate('Type').'</th> 
        </tr>';
    echo '<tbody>';
    
    foreach($resources as $name => $resource)
    {
        $oldResource = $product['resources'][$name];
        echo '<tr>
                <td>'.$resources[$name]['FriendlyName'].'</td>
                <td>';
        if($oldResource['unit'])
        {
            echo '<strong style="float:left;margin:6px;">'.$oldResource['free_limit'].' '.$oldResource['unit'].'  =></strong>';
        }
        echo '
                </td>
                <td>
                    <input class="calc" data-noCalc="'.($oldResource['type']=='summary'?'true':'false').'" data-oldVal="'.$oldResource['newFree'].'" style="float:left;width:60px;" type="text" name="resources['.$name.'][free_limit]" value="'.$oldResource['newFree'].'" />
                    <b style="float:left;margin:6px;">'.$resource['Unit'].'</b>
                </td>
                <td>';
        if($oldResource['unit'])
        {
            echo '
                    <strong style="display:inline-block;margin:6px;">
                        '.$currency['prefix'].' <span>'.$oldResource['price'].'</span>'.$currency['suffix'].'
                        per '.$oldResource['unit'].' => 
                    </strong>';
        }
        echo '
                </td>
                <td>
                    '.$currency['prefix'].' <input class="calc" style="width:70px;" type="text" name="resources['.$name.'][price]" data-noCalc="'.($oldResource['type']=='summary'?'true':'false').'" data-oldVal="'.$oldResource['newPrize'].'"  value="'.$oldResource['newPrize'].'" />'.$currency['suffix'].'
                    <b style="margin:6px;">per '.$resource['Unit'].'</b>
                </td>
                <td>';
                if($resources[$name]['AvailableUnits'])
                {
                    if($oldResource['unit'])
                    {
                        echo '<strong style="float:left;margin:6px;">'.$oldResource['unit'].' =></strong>';
                    }
                    echo '<select style="max-width: 75px" name="resources['.$name.'][unit]">';
                    foreach($resources[$name]['AvailableUnits'] as $key => $val)
                    {
                        echo '<option '.($oldResource['unit'] == $key ? 'selected="selected"' : '').' value="'.$key.'">'.$key.'</option>';
                    }
                    echo '</select>';
                }
                else
                {
                    echo $resource['Unit'] ? $resource['Unit'] : '-';
                    echo '<input type="hidden" name="resources['.$name.'][unit]" value="'.$resource['Unit'].'"/>';
                }
                echo '<td>';
                     echo '<select style="width: 150px!important" name="resources['.$name.'][type]">';
                            echo '<option value="'.$resources[$name]['AvailableTypes'][0].'">'.MG_Language::translate('Enabled').'</option>';
                            echo '<option '.($oldResource['type'] == 'disabled' || !$oldResource['type'] ? ' selected="selected" ' : '').'value="disabled">'.MG_Language::translate('Disabled').'</option>';
                        echo '</select>
                    </td>
              </tr>';
    }
    echo '</tbody>';
    echo '</table>';
    
if($accounts)
{
    echo '<legend>Current Accounts</legend>';
    
    echo '<div class="control-group">
                <label class="control-label">Generate Invoices</label>
                <div class="controls">
                    <label class="checkbox">
                        <input id="generateAwaitingInvoiceNow" type="checkbox" name="generateAwaitingInvoiceNow" value="on" checked> Generate Awaiting Invoices For Accounts Below
                    </label>
               </div>
           </div>';
    echo '<table class="table">';
    echo '<thead>';
    echo '</thead>';
    echo '<tr>
            <th>'.MG_Language::translate('Client').'</th>
            <th>'.MG_Language::translate('Domain').'</th>
            <th style="width:120px;">'.MG_Language::translate('Registration Date').'</th>
        </tr>';

    echo '<tbody>';
    foreach($accounts as $id => $account)
    {

        echo '<tr>
                <td><a href="clientssummary.php?userid='.$account['clientid'].'">'.$account['firstname'].' '.$account['lastname'].'</a></td>
                <td><a href="clientsservices.php?userid='.$account['clientid'].'&id='.$account['id'].'">'.(empty($account['domain'])?'No Domain':$account['domain']).'</a></td>
                <td>'.$account['regdate'].'</td>
              </tr>';
    }
    echo '</tbody>';
    echo '</table>';
}

    echo '<button type="button" class="btn btn-success" id="migrateProduct">Migrate Product</button>';
    echo '</form>';
    
    echo '<div id="popupMigrate" style="display:none;">
                <div class="content">
                    <h1>Are you sure you want to perform migration of product '.$product['product_name'].'</h1>
                    <br/>
                    <h3>The following actions will be executed</h3>
                    <ul>
                        <li>Product will be configurated in Storm Servers Billing 2.0</li>
                        <li id="advancebillinggenerateinvoicepopup">For all accounts in Storm Server Billing 1.x will be generated awaiting invoices</li>
                        <li>Product will be disabled in Storm Server Billing 1.x</li>
                    </ul>
                    <button type="button" class="allow btn btn-danger">Migrate</button>
                    <button type="button" class="cancel btn btn-inverse">Cancel</button>
                </div>
            </div>';
    
    echo "<script type=\"text/javascript\">
            jQuery(document).ready(function(){
                jQuery('#divprice').change(function(){
                    var rate = parseFloat(jQuery(this).val());
                    jQuery('.calc').each(function(){
                        if(jQuery(this).attr('data-noCalc') !== 'true')
                        {
                            var old = parseFloat(jQuery(this).attr('data-oldVal'));
                            if(isNaN(old))
                            {
                                jQuery(this).val(0);
                            }
                            else
                            {
                                jQuery(this).val((old/rate).toFixed(6));
                            }
                        }
                    });
                });
                jQuery('#divprice').change();
                
                jQuery('#migrateProduct').click(function(){
                    jQuery('#popupMigrate').show();
                    if(jQuery('#generateAwaitingInvoiceNow').is(':checked'))
                    {
                        jQuery('#advancebillinggenerateinvoicepopup').show();
                    }
                    else
                    {
                        jQuery('#advancebillinggenerateinvoicepopup').hide();
                    }
                    jQuery('#popupMigrate .content').css('margin-top',jQuery(document).scrollTop());
                });
                
                jQuery('#popupMigrate .cancel').click(function(){
                    jQuery('#popupMigrate').hide();
                });
                
                jQuery('#popupMigrate .allow').click(function(){
                    jQuery('#migrationForm').submit();
                });
            });
        </script>
        <style>
            #popupMigrate{
                width:100%;
                height:100%;
                position:absolute;
                top:0;
                left:0;
                background: rgba(0, 0, 0, 0.6);
            }
            #popupMigrate .content{
                background-color: #F2DEDE;
                border-color: #EED3D7;
                color: #B94A48;
                border-radius: 4px 4px 4px 4px;
                padding: 8px 35px 8px 14px;
                text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
                width:500px;
                margin:0 auto;
                min-height:100px;
            }

        </style>

";
    