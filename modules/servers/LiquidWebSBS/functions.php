<?php
/**********************************************************************
 * CloudLinux Licenses Product developed. (2014-06-04)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->       http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
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
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 */


if (!function_exists('mysql_safequery')) {

	/**
	 * FUNCTION mysql_safequery
	 * Connect mysql safety
	 * @param string $query
	 * @param array $params
	 * @return mysql_query $sql_query
	 */
	function mysql_safequery($query, $params = false) {
		if ($params) {
			foreach ($params as &$v) {
				$v = mysql_real_escape_string($v);
			}
			$sql_query = vsprintf(str_replace("?", "'%s'", $query), $params);
			$sql_query = mysql_query($sql_query);
		} else {
			$sql_query = mysql_query($query);
		}
		return ($sql_query);
	}

}

if (!function_exists('mysql_query_safe')) {

	/**
	 * FUNCTION mysql_query_safe
	 * Better version of mysql_safequery
	 * @param string $query
	 * @param array $params
	 * @return mysql_query $sql_query
	 * @throws Exception
	 */
	function mysql_query_safe($query, array $params = array()) {
		if (!empty($params)) {
			// there is possibility to use % sign in query - this line escapes it!
			$query = str_replace('%', '%%', $query);

			foreach ($params as $k => $p) {
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
				$v = mysql_real_escape_string($v);

			$sql_query = vsprintf(str_replace("?", "'%s'", $query), $params);
			$sql_query = mysql_query($sql_query);
		} else {
			$sql_query = mysql_query($query);
		}

		$err = mysql_error();
		if (!$sql_query && $err) {
			throw new Exception($err);
		}
		return ($sql_query);
	}

}

if(!function_exists('mysql_get_array'))
{
    /**
     * FUNCTION mysql_get_array
     * mysql get array 
     * @param string $query
     * @param array $params
     * @return array
     */
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


if(function_exists('dumpx') == false){
    function dumpx(){
        echo "<pre>";
        $arg = func_get_args();
        foreach( $arg as $a){
              if(empty($a))
                    var_dump($a);
              else{
                    print_r($a); echo "\n";
              }
        }
        echo "</pre>";
    }    
}
if(function_exists('dump_xml') == false){
    function dump_xml($row='#############################################'){
        echo "<pre>";
        echo htmlentities(print_r($row, true));
        echo "</pre>";
    }    
}

if (!function_exists('getFullAdmin')){
     /**
     * FUNCTION getFullAdmin
     * Returns array with admin data (for api calls)
     * @return string $row
     */
    function getFullAdmin(){
            $query_admin = mysql_query('SELECT id, username, password, email FROM tbladmins WHERE roleid = 1 LIMIT 1');
            while ($row = mysql_fetch_assoc($query_admin))
                    return $row;
            return false;
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

if (!function_exists('MG_formatBytes')) {
      /**
       * FUNCTION MG_formatBytes
       * format Bytes
       * @param int $bytes
       * @param int $precision
       * @return string
       */
      function MG_formatBytes($bytes, $precision = 2) {
            $units = array('B', 'KB', 'MB', 'GB', 'TB');
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= (1 << (10 * $pow));
            return round($bytes, $precision) . ' ' . $units[$pow];
      }
}