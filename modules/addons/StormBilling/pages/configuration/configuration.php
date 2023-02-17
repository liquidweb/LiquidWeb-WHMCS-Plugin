<?php
include_once StormBillingDIR.DS.'class.SBProduct.php';

//LIST ALL AVAILABLE PRODUCTS
$products = StormBilling_getModulesProducts();
$enabled = array();
$disabled = array();

foreach($products as $id => $p)
{
    if($p['enable'])
    {
        $enabled[$id] = $p;
    }
    else
    {
        $disabled[$id] = $p;
    }
}
function sortx($a, $b)
{
    return strcmp($a['group'], $b['group']);
}
uasort($disabled, 'sortx');
uasort($enabled, 'sortx');
        

