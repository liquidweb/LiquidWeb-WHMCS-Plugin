<?php

switch($_REQUEST['modaction'])
{
    case 'resources':
        foreach($resources as $k => $v)
        {
            echo '<tr>
                    <td>'.MG_Language::translate($v['FriendlyName']).'&nbsp';

                    if($v['Description'])
                    echo '
                        <span class="tooltip-box">
                            <a data-original-title="'.$v['Description'].'" rel="tooltip" data-placement="top" href="#">
                                <i class="icon-question-sign"></i>
                            </a>
                        </span>';
                    echo '</td>';
                        if($v['ExtendedPricing'])
                        {
                            echo '<td>';
                            foreach($v['ExtendedPricing'] as $extendedPricing)
                            {
                                echo '<div class="extended-pricing"><label>'.$extendedPricing['FriendlyName'].'</label><input style="width: 100px!important" type="text" name="resources['.$k.'][ExtendedPricing]['.$extendedPricing['Relid'].'][FreeLimit]" size="7" value="'.$extendedPricing['FreeLimit'].'" placeholder="0" />'.$v['Unit'].'</div>';
                            }
                            echo '</td>';
                            echo '<td>';
                            foreach($v['ExtendedPricing'] as $extendedPricing)
                            {
                                echo '<div class="extended-pricing">'
                                        .$currency['prefix'].'<input style="width: 100px!important" type="text" name="resources['.$k.'][ExtendedPricing]['.$extendedPricing['Relid'].'][Price]" size="7" value="'.$extendedPricing['Price'].'" placeholder="0"/>'.$currency['suffix'].'
                                        '.$v['Unit'].'
                                      </div>';
                            }
                            echo '</td>';
                        }
                        else
                        {
                            echo '<td>
                                    <input style="width: 100px!important" type="text" name="resources['.$k.'][free_limit]" size="7" value="'.$v['free_limit'].'" placeholder="0"/>
                                        '.$v['Unit'].'
                                  </td>
                                  <td>
                                    '.$currency['prefix'].'<input style="width: 100px!important" type="text" name="resources['.$k.'][price]" size="7" placeholder="0" value="'.(isset($v['LockPrice']) && $v['LockPrice'] ? '1' : $v['price']).'" '.($v['LockPrice'] ? 'disabled="disabled"' : '').'/>'.$currency['suffix'].'
                                     '.$v['Unit'].'
                                  </td>';
                        }
                    echo '
                    <td style="text-align: center">';
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
                        <select class="span3" name="resources['.$k.'][type]">';
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
            echo '</tr>';

            if($v['Html'])
            {
                echo '<tr '.($v['HtmlVisible'] ? '' : 'style="display: none"').'><td colspan="5">'.$v['Html'].'</td></tr>';
            }
        }
        break;
        
    case 'module_area':
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
        
        if($html_area || $configuration)
        {
            echo '<script type="text/javascript">
                    $(function(){
                        $("a[href=#module-settings]").parent().removeClass("hidden");
                        $("div#module-settings").removeClass("hidden");
                    });
                  </script>';
        }
        else
        {
            echo '<script type="text/javascript">
                $(function(){
                    $("a[href=#module-settings]").parent().addClass("hidden");
                    $("div#module-settings").addClass("hidden");
                });
              </script>';
        }
        break;
}