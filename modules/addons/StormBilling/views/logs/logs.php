<?php

if($check_configuration)
{
    echo '<script type="text/javascript">';
    echo 'jQuery(function(){
            jQuery(".show-log").click(function(){
                val = jQuery("select[name=\'logfile\']").val();
                if(val != "---")
                {
                    window.location = "addonmodules.php?module=StormBilling&modpage=logs&log="+val+"&modaction=show";
                }
            });

            jQuery(".delete-log").click(function(){
                val = jQuery("select[name=\'logfile\']").val();
                if(val != "---")
                {
                    window.location = "addonmodules.php?module=StormBilling&modpage=logs&log="+val+"&modaction=delete";
                }
            });
        });';
    echo '</script>';

    echo '
        <div class="border-box">
            <form action="" method="post">
                <div class="control-group">
                    <label class="control-label">'.MG_Language::translate('File').'</label>
                    <div class="controls controls-row">
                        <select name="logfile" class="span2" style="max-height:18px;">
                            <option value="---">---</option>';
                            foreach($logs_files as $filename)
                            {
                                if($filename == $_REQUEST['log'])
                                    echo '<option selected value="'.$filename.'">'.$filename.'</option>';
                                else 
                                    echo '<option value="'.$filename.'">'.$filename.'</option>';
                            }
                            echo '
                         </select>
                         <button class="btn btn-primary span1 show-log" type="button">'.MG_Language::translate('Show').'</button>
                         <button class="btn btn-danger span1 delete-log" type="button">'.MG_Language::translate('Delete').'</button>
                    </div>
                </div>
            </form>
     </div>';

    if($log)
    {
        echo '<pre>'.$log.'</pre>';
    }
}
?>
