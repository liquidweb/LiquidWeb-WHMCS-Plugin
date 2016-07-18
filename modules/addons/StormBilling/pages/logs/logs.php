<?php

//chech log directoty
$check_configuration = true;
if(!is_dir(StormBillingDIR.DS.'cron'.DS.'logs'))
{
    //try to create dir
    if(!mkdir(StormBillingDIR.DS.'cron'.DS.'logs', 0744)) 
    {
        addError(MG_Language::translate('Cannot create directory for log files. Check permission for '.StormBillingDIR.DS.'cron'.DS));
        $check_configuration = false;
    }
}
else
{
    if(!is_writable(StormBillingDIR.DS.'cron'.DS.'logs'.DS))
    {
        addError(MG_Language::translate('Check permissions to log directory. File : ').StormBillingDIR.DS.'cron'.DS.'logs must be writeable');
        $check_configuration = false;
    }
}


if($check_configuration)
{
    $log = null;
    switch($_REQUEST['modaction'])
    {
        case 'delete':
        {
            if(isset($_REQUEST['log']) && $_REQUEST['log'] != '' && $_REQUEST['log'] != '---')
            {
                if(StormBilling_deleteLogFile($_REQUEST['log']))
                {
                    addInfo(MG_Language::translate('Log file deleted successfully.'));
                }
                else 
                {
                    addError(MG_Language::translate('Cannot delete log file. Try again!'));
                }
            }
            header("Location: addonmodules.php?module=StormBilling&modpage=logs");
            exit;
        }
        break;

        case 'show':
        {
            $log = StormBilling_getLogFileContent($_GET['log']);
            if(!$log)
            {
                addInfo(MG_Language::translate('Nothing to display'));
            }
        }
    }

    $logs_files = StormBilling_getLogsFiles();
    sort($logs_files);
}