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
function StormOnDemandPrivateParent_ProductEdit($params)
{
    if ($_REQUEST['servertype'] == 'StormOnDemandPrivateParent' && $_REQUEST['customconfigoption']) {
        //DB
        require_once ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'StormOnDemand' . DIRECTORY_SEPARATOR . 'modulesgarden' . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'database.php';
        //Product Configuration
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'class.StormOnDemandPrivateParentProduct.php';
        //Save Configuration
        $product = new StormOnDemandPrivateParentProduct($params['pid']);
        $product->saveConfigOptions($_REQUEST['customconfigoption']);
    }
}

add_hook("ProductEdit", 1, "StormOnDemandPrivateParent_ProductEdit");
