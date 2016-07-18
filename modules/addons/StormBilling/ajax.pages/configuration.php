<?php

switch($_REQUEST['modaction'])
{
    case 'description':
        echo StormBilling_getSubmoduleDescription($_REQUEST['submodule']);
        break; 
    case 'resources':
        $currency = mysql_get_row("SELECT prefix, suffix FROM tblcurrencies ORDER BY id ASC LIMIT 1" );
        $resources = StormBilling_getSubmoduleResources($_REQUEST['submodule']);
        break;
    case 'module_area':
        $html_area      =   StormBilling_getSubmoduleHTMLArea($_REQUEST['submodule']);
        $configuration  =   StormBilling_getSubmoduleConfiguration($_REQUEST['submodule']);
        break;
}

