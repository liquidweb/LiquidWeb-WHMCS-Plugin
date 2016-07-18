<?php

echo '<div class="border-box">
        <div class="control-group">
        <strong>Awaiting Invoice</strong> means, that it will be not generated until you will confirm that manually.<br/>
            This is the best way to avoid any unwanted invoices for your clients and test the possibilities of <strong>StormBilling</strong> module. <br/><br/>
            To enable this feature, please edit your Products in <a href="addonmodules.php?module=StormBilling&modpage=configuration">Configuration</a> page and disable "Autogenerate Invoice" option.
        </div>
      </div>';
 
echo '<script type="text/javascript">
        jQuery(function(){
            jQuery("#datepicker-date").datepicker({ dateFormat: "yy-mm-dd" });
            jQuery("#datepicker-duedate").datepicker({ dateFormat: "yy-mm-dd" });
            jQuery("#datepicker-generatedate").datepicker({ dateFormat: "yy-mm-dd" });
        });
      </script>';

echo '<form class="form-low" action="addonmodules.php?module=StormBilling&modpage=invoices&modsubpage=show&id='.$_REQUEST['id'].'" method="post">';
echo '<input type="hidden" name="modaction" value="generate" />';
echo '<div class="border-box form-horizontal form-low">
    
        <div class="control-group">
            <label class="control-label">'.MG_Language::translate('Client').'</label>
            <div class="controls">
                <span><a href="clientssummary.php?userid='.$invoice['userid'].'">'.$invoice['firstname'].' '.$invoice['lastname'].'</a></span>
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label">'.MG_Language::translate('Invoice date').'</label>
            <div class="controls">
                <input type="text" name="date" value="'.$invoice['date'].'" id="datepicker-date" />
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label">'.MG_Language::translate('Invoice duedate').'</label>
            <div class="controls">
                <input type="text" name="duedate" value="'.$invoice['duedate'].'" id="datepicker-duedate"/>
            </div>
        </div>
        
      </div>';

      echo '<table class="table">';
      echo '<thead>
                <tr>
                    <th>'.MG_Language::translate('Description').'</th>
                    <th style="width: 170px;">'.MG_Language::translate('Amount').'</th>
                    <th class="span1">'.MG_Language::translate('Taxed').'</th>
                </tr>
            </thead>
            <tbody>';
      $counter = 0;
      foreach($invoice['items'] as $i)
      {
          echo '<tr>
                    <td><input type="text" value="'.$i['description'].'" name="item['.$counter.'][description]" /></td>
                    <td>
                        '.$currency['prefix'].'<input style="width: 120px" type="text" value="'.$i['amount'].'" name="item['.$counter.'][amount]"/>'.$currency['suffix'].'
                    </td>
                    <td><input type="checkbox" name="item['.$counter.'][taxed]" '.($i['taxed'] ? 'checked="checked"' : '').'/></td>
                </tr>';
          $counter++;
      }
      echo '<tr>
        <td colspan="3">
            <button class="btn btn-success">'.MG_Language::translate('Generate Invoice').'</button>
        </td>
      </tr>';
      echo '<tbody>
          </table>';
      
echo '</form>';
?>
