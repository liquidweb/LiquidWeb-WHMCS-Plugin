<?php
 
if (!function_exists('mysql_safequery')) 
{
    function mysql_safequery($query, $params = array())
    {
        if (!empty($params)) 
        {
            // there is possibility to use % sign in query - this line escapes it!
            $query = str_replace('%', '%%', $query);

            foreach ($params as $k => $p) 
            {
                if ($p === null) {
                    $query = preg_replace('/\?/', 'NULL', $query, 1);
                    unset($params[$k]);
                } elseif (is_int($p) || is_float($p)) {
                    $query = preg_replace('/\?/', $p, $query, 1);
                    unset($params[$k]);
                } else {
                    $query = preg_replace('/\?/', "'%s'", $query, 1);
                }
            }
            foreach ($params as &$v)
            {
                $v = mysql_real_escape_string($v);
            }
            $sql_query = vsprintf(str_replace("?", "'%s'", $query), $params);
            
            $sql_query = mysql_query($sql_query);
        } 
        else 
        {
            $sql_query = mysql_query($query);
        }

        $err = mysql_error();
        if (!$sql_query && $err) 
        {
            throw new Exception($err);
        }
        return ($sql_query);
    }
}

if (!function_exists('mysql_get_array')) 
{
    function mysql_get_array($query, $params = false) 
    {
        $q = mysql_safequery($query, $params);
        $arr = array();
        while ($row = mysql_fetch_assoc($q)) {
            $arr[] = $row;
        }

        return $arr;
    }

}

if (!function_exists('mysql_get_row')) 
{
    function mysql_get_row($query, $params = false) 
    {
        $q = mysql_safequery($query, $params);
        $row = mysql_fetch_assoc($q);
        return $row;
    }
}
