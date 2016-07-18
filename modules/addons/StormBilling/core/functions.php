<?php
//define addons main dir
defined('StormBillingDIR') ? null: define('StormBillingDIR', dirname(dirname(__FILE__)));

if(function_exists('mysql_safequery') == false) {
    function mysql_safequery($query,$params=false) {
        if ($params) {
            foreach ($params as &$v) { $v = mysql_real_escape_string($v); }
            $sql_query = vsprintf( str_replace("?","'%s'",$query), $params );
            $sql_query = mysql_query($sql_query);
        } else {
            $sql_query = mysql_query($query);
        }

        return ($sql_query);
    }
}

if(!function_exists('mysql_get_array'))
{
    function mysql_get_array($query, $params = false)
    {
        $q = mysql_safequery($query, $params);
        $arr = array();
        while($row = mysql_fetch_assoc($q))
        {
            $arr[] = $row;
        }

        return $arr;
    }
}

if(!function_exists('mysql_get_row'))
{
    function mysql_get_row($query, $params = false)
    {
        $q = mysql_safequery($query, $params);
        $row = mysql_fetch_assoc($q);
        return $row;
    }
}

if(!function_exists('saveWHMCSconfig'))
{
    function saveWHMCSconfig($k, $v)
    {
        $q = mysql_safequery("SELECT `value` FROM tblconfiguration WHERE `setting` = ?",array($k));
        $ret=mysql_fetch_array($q);
        unset($q);

        if(isset($ret['value']))
        {
            return mysql_safequery("UPDATE tblconfiguration SET value = ? WHERE setting = ?",array( $v, $k));
        }
        else
        {
            return mysql_safequery("INSERT INTO tblconfiguration  (setting,value) VALUES (?,?)",array($k, $v));
        }
    }
}

if(!function_exists('getWHMCSconfig'))
{
    function getWHMCSconfig($k)
    {
        $q = mysql_safequery("SELECT value FROM tblconfiguration WHERE setting = ?", array($k));
        $ret=mysql_fetch_array($q);
        unset($q);

        if($ret['value'])
        {
            return $ret['value'];
        }
    }
}

if(!function_exists('addError'))
{
    function addError($error)
    {
        $_SESSION[ADDON_NAME]['errors'][] = $error;
    }
}


if(!function_exists('addInfo'))
{
    function addInfo($info)
    {
        $_SESSION[ADDON_NAME]['infos'][] = $info;
    }
}

if(!function_exists('getErrors'))
{
    function getErrors()
    {
        $errors = $_SESSION[ADDON_NAME]['errors'];
        $_SESSION[ADDON_NAME]['errors'] = null;
        return $errors;
    }
}

if(!function_exists('getInfos'))
{
    function getInfos()
    {
        $infos = $_SESSION[ADDON_NAME]['infos'];
        $_SESSION[ADDON_NAME]['infos'] = null;
        return $infos;
    }
}

if(!function_exists('getCustomConfigOptions'))
{
    function getCustomConfigOptions()
    {
        $configs = array();
        $q = mysql_safequery("SELECT config_name,config_value FROM StormBilling_customconfig");
        while(($row = mysql_fetch_assoc($q))){
            $configs[$row['config_name']] = $row['config_value'];
        }
        unset($q);
        return $configs;

    }
}


