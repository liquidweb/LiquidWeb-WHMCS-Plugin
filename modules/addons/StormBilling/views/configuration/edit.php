<?php

echo '<style type="text/css">
        .autosuspend
        {
            display: none;
        }
      </style>';

echo '<script type="text/javascript">
        jQuery(function(){
            jQuery(".btn-extended-pricing").click(function(event){
                event.preventDefault();

                parent = jQuery(this).attr("data-parent");
                children = jQuery(this).closest("table").find("tr[data-parent="+parent+"]");

                if(jQuery(this).hasClass("closed"))
                {
                    jQuery(this).removeClass("closed");
                    jQuery(this).removeClass("btn-info").addClass("btn-success");
                    jQuery(children).removeClass("hidden");
                }
                else
                {
                    jQuery(this).addClass("closed");
                    jQuery(this).addClass("btn-info").removeClass("btn-success");
                    jQuery(children).addClass("hidden");
                }

                /*next = jQuery(this).closest(".usage-records-line").next(".extended-pricing-line");
                if(jQuery(next).hasClass("hidden"))
                {
                    jQuery(this).removeClass("btn-info").addClass("btn-success");
                    jQuery(next).removeClass("hidden");
                }
                else
                {
                    jQuery(this).removeClass("btn-info").addClass("btn-success");
                    jQuery(next).addClass("hidden");
                }*/
            });

            jQuery("#get_module_description").change(function(){
                val = $(this).val();
                jQuery.post(window.location.href, "ajax=1&modaction=description&submodule="+val, function(data){
                    $("#module_description").html(data);
                });
                jQuery.post(window.location.href, "ajax=1&modaction=resources&submodule="+val, function(data){
                    $("#module_resources").html(data);
                    reloadScripts();
                });

                jQuery.post(window.location.href, "ajax=1&modaction=module_area&submodule="+val, function(data){
                    $("#module_area").html(data);
                });
            });

            jQuery("#bill_per_month_check").change(function(){
                if(jQuery(this).is(":checked"))
                {
                    jQuery("#bill_per_days_value").attr("disabled", "disabled");
                    jQuery("#bill_on_invoice_generate").removeAttr("checked");
                }
                else
                {
                    jQuery("#bill_per_days_value").removeAttr("disabled");
                }
            });

            jQuery("#bill_on_invoice_generate").change(function(){
                if(jQuery(this).is(":checked"))
                {
                    jQuery("#bill_per_days_value").attr("disabled", "disabled");
                    jQuery("#bill_per_month_check").removeAttr("checked");
                    jQuery("input[name=autogenerate_invoice]").attr("checked", "checked");
                    jQuery("input[name=autogenerate_invoice]").attr("disabled", "disabled");
                }
                else
                {
                    jQuery("#bill_per_days_value").removeAttr("disabled");
                    jQuery("#bill_per_month_check").removeAttr("disabled");
                    jQuery("input[name=autogenerate_invoice]").removeAttr("disabled");
                }
            });

            jQuery("input[name=\'credit_billing[enable]\']").change(function(){
                if(jQuery(this).is(":checked"))
                {
                    $("#billing-settings input[type=checkbox]").removeAttr("checked");
                    $("#billing-settings input[type=checkbox]").attr("disabled", "disabled");
                }
                else
                {
                    $("#billing-settings input[type=checkbox]").removeAttr("disabled");
                }
            });

            jQuery("input[name=\'automation[autosuspend][enable]\']").change(function(){

                if(jQuery(this).is(":checked"))
                {
                    $(".autosuspend").show();
                }
                else
                {
                    $(".autosuspend").hide();
                }
            });

            jQuery("input[name=bill_duedate]").change(function(){
                $("input[name=bill_duedate]").val($(this).val());
            });
        });
      </script>';

echo '<div class="border-box">
        <div class="control-group">
            You can simply enable CloudBilling for this product by  configuring price for the following items.<br />
            <strong>Setting the following up will also bill existing accounts!</strong>
        </div>
      </div>';


echo '
    <form action="" method="post" class="form-horizontal form-low">
    <input type="hidden" value="1" name="savesettings" />
    <div class="border-box">
        <ul class="nav nav-tabs">
            <li class="active">
                <a data-toggle="tab" href="#general-settings">'.MG_Language::translate('General Settings').'</a>
            </li>
            <li class="">
                <a data-toggle="tab" href="#billing-settings">'.MG_Language::translate('Billing Settings').'</a>
            </li>
            <li class="">
                <a data-toggle="tab" href="#credit-billing-settings">'.MG_Language::translate('Credit Billing').'</a>
            </li>
            <!--<li class="">
                <a data-toggle="tab" href="#automation-settings">'.MG_Language::translate('Automation').'</a>
            </li>-->
            <li class="'.(!$configuration && !$html_area ? 'hidden' : '').'">
                <a data-toggle="tab" href="#module-settings">'.MG_Language::translate('Module Settings').'</a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="general-settings" class="tab-pane active">
                <div class="control-group">
                    <label class="control-label"><h3><strong>'.MG_Language::translate('Enable').'</strong></h3></label>
                    <div class="controls">
                        <input type="checkbox" name="enable_billing" id="enable_billing_check" '.($settings['enable'] == 1 || isset($_REQUEST['auto_enable']) ? 'checked' : '').' />
                        <span class="help-inline">'.MG_Language::translate('Uncheck This Box to DISABLE CloudBilling For This Product').'</span>
                    </div>
                </div>
                ';
            echo'<div class="control-group">
                        <label class="control-label"><b>'.MG_Language::translate('Used Module').'</b></label>
                        <div class="controls">';
                if($p->hasDedicatedModule())
                {
                    foreach($available_modules as $key => $u)
                    {
                        if($p->getServerType() == strtolower($key))
                        {
                            echo '<input type="hidden" name="settings[module]" value="'.$key.'" />';
                            echo '<strong>'.$u.'</strong>';
                            break;
                        }
                    }
                }
                else
                {
                    echo '<select name="settings[module]" id="get_module_description">';
                    foreach($available_modules as $key => $u)
                    {
                        echo '<option '.($p->getServerType() == strtolower($key) ? 'selected="selected"' : '').' value="'.$key.'">'.MG_Language::translate($u).'</option>';
                    }
                    echo '</select>';
                }

                echo '           </div>
                    </div>';
                if($p->getModuleDescription())
                {
                    echo '<div class="control-group">
                            <label class="control-label"></label>
                            <div class="controls" id="module_description">'.$p->getModuleDescription().'</div>
                        </div>';
                }
                echo '
            </div>
            <div id="billing-settings" class="tab-pane">
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Bill on Terminate').'</label>
                    <div class="controls">
                        <input type="checkbox" value="1" name="bill_on_terminate" id="bill_on_terminate" '.(isset($settings['billing_settings']['bill_on_terminate']) && $settings['billing_settings']['bill_on_terminate'] == 1 ? 'checked' : '').' '.(isset($settings['billing_settings']['credit_billing']['enable']) && $settings['billing_settings']['credit_billing']['enable'] == 1 ? ' disabled="disabled"' : '').'/>
                        <span class="help-inline">'.MG_Language::translate('Bill Your Client After Account is Terminated').'</span>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Bill on Invoice Generate').'</label>
                    <div class="controls">
                        <input type="checkbox" value="1" name="bill_on_invoice_generate" id="bill_on_invoice_generate" '.(isset($settings['billing_settings']['bill_on_invoice_generate']) && $settings['billing_settings']['bill_on_invoice_generate'] == 1 ? 'checked' : '').' '.($settings['billing_settings']['credit_billing']['enable'] == 1 ? ' disabled="disabled"' : '').' />
                        <span class="help-inline">'.MG_Language::translate('Bill Your Client When Invoice is Generated For Hosting').'</span>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Bill Each 1st Day of Month').'</label>
                    <div class="controls">
                        <input type="checkbox" value="1" name="bill_per_month" id="bill_per_month_check" '.($settings['billing_settings']['billing_period'] == 'month' ? 'checked' : '').' '.($settings['billing_settings']['credit_billing']['enable'] == 1 ? ' disabled="disabled"' : '').'/>
                        <span class="help-inline">'.MG_Language::translate('Create Invoices Each 1st Day of Month').'</span>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Autogenerate Invoice').'</label>
                    <div class="controls">
                        <input type="checkbox" name="autogenerate_invoice" '.($settings['billing_settings']['autogenerate_invoice'] == 1 || isset($settings['billing_settings']['bill_on_invoice_generate']) && $settings['billing_settings']['bill_on_invoice_generate'] == 1 ? 'checked' : '').' '.($settings['billing_settings']['credit_billing']['enable'] == 1 ? ' disabled="disabled"' : '').'/>
                        <span class="help-inline">'.MG_Language::translate('Check if You Want to Enable Auto Generating Invoice').'</span>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Create Invoices Each').'</label>
                    <div class="controls">
                        <input class="span1" type="text" name="bill_per_days" size="5" id="bill_per_days_value" value="'.($settings['billing_settings']['billing_period'] == 'month' ? '' : $settings['billing_settings']['billing_period']).'" '.($settings['billing_settings']['billing_period'] == 'month' || $settings['billing_settings']['bill_on_invoice_generate'] ? 'disabled="disabled"' : '').'/>
                        <span class="help-inline">'.MG_Language::translate('Days (30 by default)').'</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Due Date').'</label>
                    <div class="controls">
                        <input class="span1" type="text" name="bill_duedate" id="bill_duedate_value" value="'.$settings['billing_settings']['billing_duedate'].'" />
                        <span class="help-inline">'.MG_Language::translate('Days (7 by default)').'</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Auto Apply Credits').'</label>
                    <div class="controls">
                        <input type="checkbox" name="autoapplycredit" '.($settings['billing_settings']['autoapplycredit'] == 1 ? 'checked' : '').' '.($settings['billing_settings']['credit_billing']['enable'] == 1 ? ' disabled="disabled"' : '').'/>
                        <span class="help-inline">'.MG_Language::translate('Tick to Auto Apply Any Available Credit From The Clients Credit Balance').'</span>
                    </div>
                </div>
            </div>
            <div id="credit-billing-settings" class="tab-pane">
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Enable Credit Pay').'</label>
                    <div class="controls">
                        <input type="checkbox" name="credit_billing[enable]" '.($settings['billing_settings']['credit_billing']['enable'] == 1 ? 'checked' : '').' />
                        <span class="help-inline">'.MG_Language::translate('Check This Option If You Want To Enable Credit Billing For This Product').'</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Create Invoices Each').'</label>
                    <div class="controls">
                        <input class="span1" type="text" name="credit_billing[billing_period]" size="5"  value="'.$settings['billing_settings']['credit_billing']['billing_period'].'" />
                        <span class="help-inline">'.MG_Language::translate('Days (30 by default)').'</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Minimal Credit').'</label>
                    <div class="controls">
                        <input class="span1" type="text" name="credit_billing[minimal_credit]" size="5"  value="'.$settings['billing_settings']['credit_billing']['minimal_credit'].'" />
                        <span class="help-inline">'.MG_Language::translate('Minimal Amount That Will Be Charged From Client Account').'</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Low Credit Notification').'</label>
                    <div class="controls">
                        <input class="span1" type="text" name="credit_billing[low_credit_notify]" size="5"  value="'.$settings['billing_settings']['credit_billing']['low_credit_notify'].'" />
                        <span class="help-inline">'.MG_Language::translate('Send Email To Your Client About Low Credit Amount On Account').'</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Email Interval').'</label>
                    <div class="controls">
                        <input class="span1" type="text" name="credit_billing[email_interval]" size="5"  value="'.$settings['billing_settings']['credit_billing']['email_interval'].'" />
                        <span class="help-inline">'.MG_Language::translate('Set email interval for email notifications').'</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Autosuspend').'</label>
                    <div class="controls">
                        <input type="checkbox" name="credit_billing[autosuspend]" '.($settings['billing_settings']['credit_billing']['autosuspend'] == 1 ? 'checked' : '').' />
                        <span class="help-inline">'.MG_Language::translate('Autosusped Account When User Do Not Have Enough Funds').'</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Due Date').'</label>
                    <div class="controls">
                        <input class="span1" type="text" name="bill_duedate" id="bill_duedate_value" value="'.$settings['billing_settings']['billing_duedate'].'" />
                        <span class="help-inline">'.MG_Language::translate('Days (7 by default)').'</span>
                    </div>
                </div>
            </div>
            <!--<div id="automation-settings" class="tab-pane">
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('Enable Autosuspend').'</label>
                    <div class="controls">
                        <input type="checkbox" name="automation[autosuspend][enable]" '.($settings['billing_settings']['automation']['autosuspend']['enable'] == 1 ? 'checked' : '').' />
                        <span class="help-inline">'.MG_Language::translate('Check This Option If You Want To Enable Auto-Suspend').'</span>
                    </div>
                </div>
                <div class="control-group autosuspend" '.($settings['billing_settings']['automation']['autosuspend']['enable'] == 1 ? ' style="display: block" ' : '').'>
                    <label class="control-label">'.MG_Language::translate('Autosuspend Over').'</label>
                    <div class="controls">
                        <input class="span1" type="text" name="automation[autosuspend][interval]" size="5"  value="'.$settings['billing_settings']['automation']['autosuspend']['interval'].'" />
                        <span class="help-inline">'.MG_Language::translate('Days After Service Date Creation (7 by default)').'</span>
                    </div>
                </div>
                <div class="control-group autosuspend" '.($settings['billing_settings']['automation']['autosuspend']['enable'] == 1 ? ' style="display: block" ' : '').'>
                    <label class="control-label">'.MG_Language::translate('Suspend Reason').'</label>
                    <div class="controls">
                        <input class="span6" type="text" name="automation[autosuspend][message]" size="5"  value="'.$settings['billing_settings']['automation']['autosuspend']['message'].'" />
                        <span class="help-inline">'.MG_Language::translate('Set up reason for suspend action').'</span>
                    </div>
                </div>

            </div>-->
            <div id="module-settings" class="tab-pane '.(!$configuration && !$html_area ? 'hidden' : '').'">';
            echo '<div id="module_area">';
                if($html_area)
                {
                    echo $html_area;
                }

                if($configuration)
                {
                    foreach($configuration as $name => $conf)
                    {
                        echo '<div class="control-group">
                            <label class="control-label">'.MG_Language::translate($conf['FriendlyName']).'</label>
                            <div class="controls">';
                        switch($conf['Type'])
                        {
                            case 'Select':
                                echo '<select name="configuration['.$name.']">';
                                foreach($conf['Select'] as $key => $value)
                                {
                                    echo '<option value="'.$key.'" '.($conf['Value'] == $key ? ' selected="selected" ' : '').'>'.$value.'</option>';
                                }
                                echo '<select>';
                                break;
                            case 'Text':
                                echo '<input type="text" name="configuration['.$name.']" value="'.$conf['Value'].'" />';
                                break;
                        }
                        echo '</div>
                        </div>';
                    }
                }
            echo '</div>';
        echo '
            </div>
        </div>';

echo '</div>';


echo '<table class="table">
        <thead>
                    <tr>
                        <th>
                            '.MG_Language::translate('Usage Record').'
                            <span class="tooltip-box">
                                <a data-original-title="'.MG_Language::translate('Type of the usage that client can be billed for').'" rel="tooltip" data-placement="top" href="#">
                                    <i class="icon-question-sign"></i>
                                </a>
                            </span>
                        </th>
                <th  style="width: 200px">
                    '.MG_Language::translate('Free Limit').'
                            <span class="tooltip-box">
                                <a data-original-title="'.MG_Language::translate('Limit of the usage that client will be NOT billed for. Client will be billed for any overage of this value (default 0)').'" rel="tooltip" data-placement="top" href="#">
                                    <i class="icon-question-sign"></i>
                                </a>
                            </span>
                        </th>
                <th  style="width: 200px">
                    '.MG_Language::translate('Price').'
                            <span class="tooltip-box">
                                <a data-original-title="'.MG_Language::translate('Price for each usage unit').'" rel="tooltip" data-placement="top" href="#">
                                    <i class="icon-question-sign"></i>
                                </a>
                            </span>
                        </th>
                <th style="width: 100px">
                    '.MG_Language::translate('Display Unit').'
                    <a data-original-title="'.MG_Language::translate('This unit will be used on invoices and in account summary.').'" rel="tooltip" data-placement="top" href="#">
                                <i class="icon-question-sign"></i>
                            </a>
                        </th>
                <th style="width: 100px">
                    '.MG_Language::translate('Status').'
                        </th>
                <!--<th style="width: 70px">
                    '.MG_Language::translate('Configure').'
                </th>-->
                    </tr>
                    <tbody id="module_resources">';
                    foreach($resources as $k => $v)
                    {
                        echo '<tr>
                    <td>'.MG_Language::translate($v['FriendlyName']).'&nbsp';
                                if($v['Description'])
                        {
                                echo '
                                    <span class="tooltip-box">
                                        <a data-original-title="'.$v['Description'].'" rel="tooltip" data-placement="top" href="#">
                                            <i class="icon-question-sign"></i>
                                        </a>
                                    </span>';
                                            }
            echo '  </td>
                    <td style="white-space: nowrap;">';
            echo '      <input style="width: 100px!important" type="text" name="resources['.$k.'][free_limit]" size="7" value="'.$v['free_limit'].'" placeholder="0"/>
                                                    '.$v['Unit'].'
                                              </td>
                                              <td style="white-space: nowrap;">
                                                '.$currency['prefix'].'<input style="width: 100px!important" type="text" name="resources['.$k.'][price]" size="7" placeholder="0" value="'.(isset($v['LockPrice']) && $v['LockPrice'] ? '1' : $v['price']).'" '.($v['LockPrice'] ? 'disabled="disabled"' : '').'/>'.$currency['suffix'].'
                                                 '.$v['Unit'].'
                    </td>
                    <td>';
                                if($v['AvailableUnits'])
                                {
                                    echo '<select style="max-width: 75px" name="resources['.$k.'][unit]">';
                                    foreach($v['AvailableUnits'] as $key => $val)
                                    {
                                        echo '<option '.($v['unit'] == $key ? 'selected="selected"' : '').' value="'.$key.'">'.$key.'</option>';
                                    }
                                    echo '</select>';
                                }
                                else
                                {
                                    echo $v['Unit'] ? $v['Unit'] : '-';
                                    echo '<input type="hidden" name="resources['.$k.'][unit]" value="'.$v['Unit'].'"/>';
                                }
                                echo '</td>
                                <td>
                        <select style="width: 100px!important;" name="resources['.$k.'][type]">';
                                        if(count($v['AvailableTypes']) == 1)
                                        {
                                echo '<option value="'.$v['AvailableTypes'][0].'">'.MG_Language::translate('Enabled').'</option>';
                                        }
                                        else
                                        {
                                            if(in_array('average', $v['AvailableTypes']))
                                            {
                                                echo '<option '.($v['type'] == 'average' ? ' selected="selected" ' : '').'value="average">Average</option>';
                                            }
                                            if(in_array('summary', $v['AvailableTypes']))
                                            {
                                                echo '<option '.($v['type'] == 'summary' ? ' selected="selected" ' : '').'value="summary">Summary</option>';
                                            }
                                        }
                            echo '<option '.($v['type'] == 'disabled' || !$v['type'] ? ' selected="selected" ' : '').'value="disabled">'.MG_Language::translate('Disabled').'</option>';
                                    echo '</select>
                                    </td>';
              /*echo '<td>';
                        if($v['ExtendedPricing'])
                        {
                            echo '<a href="#" data-parent="'.$k.'" onclick="return false;" class="btn btn-info btn-extended-pricing btn-small closed">'.MG_Language::translate('Configure').'</a>';
                        }
             echo '</td>';*/

                        echo '</tr>';

           /* if($v['ExtendedPricing'])
            {
                $max = count($v['ExtendedPricing']);
                $i = 1;
                foreach($v['ExtendedPricing'] as $extendedPricing)
                {
                    echo '<tr class="extended-pricing-line hidden '.($i == $max ? 'last-line' : '').'" data-parent="'.$k.'">
                            <td>'.$extendedPricing['FriendlyName'].'</td>
                            <td><input style="width: 100px!important" type="text" name="resources['.$k.'][ExtendedPricing]['.$extendedPricing['Relid'].'][FreeLimit]" size="7" value="'.$extendedPricing['FreeLimit'].'" placeholder="'.MG_Language::translate('Use Default').'" />'.$v['Unit'].'</td>
                            <td>'.$currency['prefix'].'<input style="width: 100px!important" type="text" name="resources['.$k.'][ExtendedPricing]['.$extendedPricing['Relid'].'][Price]" size="7" value="'.$extendedPricing['Price'].'" placeholder="'.MG_Language::translate('Use Default').'"/>'.$currency['suffix'].' '.$v['Unit'].'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>';
                    $i++;
                }
            }*/

            if($v['Html'])
            {

                echo '<tr '.($v['HtmlVisible'] ? '' : 'style="display: none"').'>
                        <td colspan="6">'.$v['Html'].'</td>
                      </tr>';
            }
        }

                    echo '
            </tbody>
      </table>';

echo '<button class="btn btn-success clearfix">'.MG_Language::translate('Save Changes').'</button>';

echo '</form>';
