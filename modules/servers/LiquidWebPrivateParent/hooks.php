<?php

/* * ********************************************************************
 *  Liquid Web - Private Parent (2013-11-22)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 * ******************************************************************** */

/**
 * @author Mariusz Miodowski <mariusz@modulesgarden.com>
 */
function LiquidWebPrivateParent_ProductEdit($params)
{
    if (($_REQUEST['servertype'] == 'LiquidWebPrivateParent') &&  ($_REQUEST['action'] == 'save')) {

        $q = mysql_query("SELECT * FROM tblproducts WHERE id = " . (int)$params['pid'] . " LIMIT 1");
        $row = mysql_fetch_assoc($q);

        $_REQUEST['customconfigoption'] = Array (
											'Username' => $params[configoption1],
											'Password' => $row[configoption2],
											'Parent' => $row[configoption5],
											'AvailableParents' => $row[configoption6],
        									'Template' => $row[configoption8],
											'Image' => $row[configoption9],
											'Memory' => $row[configoption10],
											'Diskspace' => $row[configoption11],
											'VCPU' => $row[configoption12],
											'Backup Plan' => $row[configoption13],
											'Backup Quota' => $row[configoption14],
											'Daily Backup Quota' => $row[configoption15],
											'Number of IPs' => $row[configoption16],
											'Maximum IP Addresses' => $row[configoption17],
											'Bandwidth Quota' => $row[configoption18],
											'Monitoring' => ($row[configoption19] == 'on' ? '1':''),
											'Firewall' => ($row[configoption20] == 'on' ? '1':''),
											'IPs Management' => ($row[configoption21] == 'on' ? '1':''));
        //DB
        require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'modulesgarden' . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'database.php';
        //Product Configuration
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'class.LiquidWebPrivateParentProduct.php';
        //Save Configuration
        $product = new LiquidWebPrivateParentProduct($params['pid']);
        $product->saveConfigOptions($_REQUEST['customconfigoption']);
    }
}

add_hook("ProductEdit", 1, "LiquidWebPrivateParent_ProductEdit");
