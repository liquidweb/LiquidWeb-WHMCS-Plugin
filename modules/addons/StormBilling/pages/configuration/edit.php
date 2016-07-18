<?php

global $CONFIG;

include_once StormBillingDIR.DS.'class.SBProduct.php';

if(!$_REQUEST['id'])
{
    addError(MG_Language::translate('Product doest not exists'));
    header('Location: addonmodules.php?module=StormBilling');
    die();
}

if($_REQUEST['savesettings'])
{
    if(!$CONFIG['NoAutoApplyCredit'] && $_REQUEST['bill_on_invoice_generate'])
    {
        addError('You have disabled option \'Disable Auto Credit Applying\' in WHMCS configuration. \'Bill on Invoice Generate\' will not be working with your current configuration.');
    }
    elseif(!$CONFIG['NoAutoApplyCredit'] && $_REQUEST['credit_billing']['enable'])
    {
        addError('You have disabled option \'Disable Auto Credit Applying\' in WHMCS configuration. \'Credit Billing\' will not be working with your current configuration.');
    }
    else
    {
        $p = new SBProduct($_REQUEST['id']);
        $settings = $p->getSettings();
        $p->saveSettings(array
            (
                'enable'                        =>  isset($_REQUEST['enable_billing']) ? 1 : 0,
                'billing_methods'               =>  array
                (
                    'bill_on_terminate'         =>  isset($_REQUEST['bill_on_terminate']) ? 1 : 0,
                    'bill_on_invoice_generate'  =>  isset($_REQUEST['autogenerate_invoice']) ? 1 : 0,
                    'bill_per_month'            =>  isset($_REQUEST['bill_per_month']) ? 1 : 0,
                    'bill_per_days'             =>  isset($_REQUEST['bill_per_days']) && $_REQUEST['bill_per_days'] > 0 ? 0 : (int)$_REQUEST['bill_per_days'],
                ),
                'invoice_settings'              =>  array
                (
                    'duedate'                   =>  isset($_REQUEST['bill_duedate']) && $_REQUEST['bill_duedate'] >= 0 ? (int)$_REQUEST['bill_duedate'] : 7,
                    'autogenerate'              =>  isset($_REQUEST['autogenerate_invoice']) ? 1 : 0,
                    'autoapplycredit'           =>  isset($_REQUEST['autoapplycredit']) ? 1 : 0,
                ),
                'billing_settings'              =>  array
                (
                    'bill_per_month'            =>  isset($_REQUEST['bill_per_month']) ? 1 : 0,
                    'billing_period'            =>  isset($_REQUEST['bill_on_invoice_generate']) ? 30 : (isset($_REQUEST['bill_per_month']) ? 'month' : (isset($_REQUEST['bill_per_days']) ? $_REQUEST['bill_per_days'] : 30)),
                    'billing_duedate'           =>  isset($_REQUEST['bill_duedate']) && (int)$_REQUEST['bill_duedate'] >= 0 ? (int)$_REQUEST['bill_duedate'] : 7,
                    'bill_on_terminate'         =>  isset($_REQUEST['bill_on_terminate']) ? 1 : 0,
                    'bill_on_invoice_generate'  =>  isset($_REQUEST['bill_on_invoice_generate']) ? 1 : 0,
                    'autogenerate_invoice'      =>  isset($_REQUEST['autogenerate_invoice']) ? 1 : 0,
                    'autoapplycredit'           =>  isset($_REQUEST['autoapplycredit']) ? 1 : 0,
                    //Automation
                    'automation'                    =>  array
                    (
                        //Auto suspend
                        'autosuspend'               =>  array
                        (
                            'enable'                =>  isset($_REQUEST['automation']['autosuspend']['enable']) ? 1 :0,
                            'interval'              =>  intval($_REQUEST['automation']['autosuspend']['interval']) > 0 ? $_REQUEST['automation']['autosuspend']['interval'] : 7,
                            'message'               =>  $_REQUEST['automation']['autosuspend']['message'],
                        )
                    ),
                ),
                'credit_billing'                =>  array
                (
                    'enable'                    =>  isset($_REQUEST['credit_billing']['enable']) ? 1 :0,
                    'billing_period'            =>  intval($_REQUEST['credit_billing']['billing_period']) >= 1 ? intval($_REQUEST['credit_billing']['billing_period']) : 1,
                    'minimal_credit'            =>  floatval($_REQUEST['credit_billing']['minimal_credit']) > 1 ? floatval($_REQUEST['credit_billing']['minimal_credit']) : 1,
                    'low_credit_notify'         =>  floatval($_REQUEST['credit_billing']['low_credit_notify']) > 1 ? floatval($_REQUEST['credit_billing']['low_credit_notify']) : 1,
                    'autosuspend'               =>  isset($_REQUEST['credit_billing']['autosuspend']) ? 1 : 0,
                    'email_interval'            =>  intval($_REQUEST['email_interval']) >= 1 ? intval($_REQUEST['email_interval']) : 1,
                ),
                'resource_settings'             =>  $_REQUEST['resources'],
                'module_configuration'          =>  $_REQUEST['configuration'],
                'module'                        =>  $_REQUEST['settings']['module'] ? $_REQUEST['settings']['module'] : ''
            )
        );
        addInfo(MG_Language::translate('Configuration saved'));
    }
}

//Get Available modules and sort it
$modules = StormBilling_getModules();
$available_modules = array_fill_keys($modules, 0);

foreach($available_modules as $m_key => &$m_val)
{
    $m_val = StormBilling_getSubmoduleName($m_key);
}

array_multisort(array_map('strtolower', $available_modules), $available_modules);


//Create New Instance
$p = new SBProduct($_REQUEST['id']);
//Is supported?
if(!$p->isSupported())
{
    addError(MG_Language::translate('Product is not supported'));
    header('Location: addonmodules.php?module=StormBilling');
    die();
}

//Get Product Settings
$settings = $p->getSettings();
//Get Module Resources
$resources = $p->getResources();
$configuration = $p->getConfiguration();
$html_area = $p->module()->getConfigurationArea();

$details = mysql_get_row('SELECT p.name as product_name, g.name as group_name
    FROM tblproducts p
    LEFT JOIN tblproductgroups g ON (p.gid = g.id)
    WHERE p.id = ?', array($_REQUEST['id']));

$currency = mysql_get_row("SELECT prefix, suffix FROM tblcurrencies ORDER BY id ASC LIMIT 1" );

$PAGE_SUBMODULE_HEADING = $details['group_name'].' - '.$details['product_name'];

if(!$CONFIG['NoAutoApplyCredit'] && $settings['billing_settings']['bill_on_invoice_generate'])
{
    addError('You have disabled option \'Disable Auto Credit Applying\' in WHMCS configuration. \'Bill on Invoice Generate\' will not be working with your current configuration.');
}

if(!$CONFIG['NoAutoApplyCredit'] && $settings['billing_settings']['billing_settings']['enable'])
{
    addError('You have disabled option \'Disable Auto Credit Applying\' in WHMCS configuration. \'Credit Billing\' will not be working with your current configuration.');
}
