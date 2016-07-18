<?php

global $CONFIG;

$template = $CONFIG['Template'];
$hosting_tpl = MG_Language::translate('your_whmcs').DS.'templates'.DS.$template.DS.'clientareaproductdetails.tpl';

$order_template = $CONFIG['OrderFormTemplate'];
$order_tpl = MG_Language::translate('your_whmcs').DS.'templates'.DS.'orderforms'.DS.$order_template.DS.'configureproduct.tpl';

$PAGE_HEADING = 'Integration Code';
?>
