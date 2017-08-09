<?php

$q = mysql_safequery("
    SELECT
        BS.product_id
        ,BS.enable
        ,BS.module
        ,P.name AS product_name
        ,G.name AS `group`
    FROM
        StormServersBilling_settings BS
    JOIN
        tblproducts P
        ON
            P.id = BS.product_id
    JOIN
        tblproductgroups G
        ON
            G.id = P.gid
    WHERE
        BS.enable = 1
        ");

$avaible = array();
while($row = mysql_fetch_assoc($q))
{
    $avaible[$row['product_id']] = $row;
}

if(!$avaible)
{
    addError(MG_Language::translate('You currently have no active products in Cloud Servers Billing 1.x'));
}