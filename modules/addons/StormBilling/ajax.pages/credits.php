<?php

/**********************************************************************
 *  StormBilling Trunk (2014-02-26)
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
 **********************************************************************/


/**
 * @author Mariusz Miodowski <mariusz@modulesgarden.com>
 */

$pagination = new MG_Pagination("StormBilling_Credits");

$row = mysql_get_row("SELECT COUNT(StormBilling_user_credits.hosting_id) as `count`  FROM StormBilling_user_credits");
$pagination->setAmount($row['count']);

$clients = mysql_get_array("SELECT uc.*, c.firstname, c.lastname, h.domain 
    FROM StormBilling_user_credits uc
    LEFT JOIN tblclients c ON uc.user_id = c.id
    LEFT JOIN tblhosting h ON uc.hosting_id = h.id
".$pagination->getLimitAndOffset());