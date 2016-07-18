<?php
/**********************************************************************
 * *
 *
 *  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *  Author                         ->        mariusz@modulesgarden.com
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

if(!class_exists('ModuleInformationClient'))
{
    class ModuleInformationClient
    {
        /**
         *  Cache life time.
         */
        protected $cache = array
        (
            'getLatestModuleVersion'    =>  43200,  /* 12 hours */
            'registerModuleInstance'    =>  43200,  /* 12 hours */
            'getAvailableProducts'      =>  3600,   /* 1 hour */
            'getActivePromotions'       =>  3600,   /* 1 hour */
        );

        //Server Location
        //protected $url          =   'https://www.modulesgarden.com/manage/modules/addons/ModuleInformation/server.php';
        protected $url          =   'https://www.liquidweb.com/manage/modules/addons/ModuleInformation/server.php';
        //protected $url          =   'https://api.github.com/repos/santhoshbsuvarna/test1/releases/latest';


        //This name will be send to modulesgarden.com
        protected $module       =   '';

        //Module Name
        protected $moduleName   =   '';

        //Encryption Key
        protected $accessHash   =   '';

        //Error?
        protected $error        =   '';

        public function __construct($moduleName, $accessHash, $url = '')
        {
            $this->module           =   $moduleName;
            $this->moduleName       =   strtolower(str_replace(' ','',$moduleName));
            $this->accessHash       =   trim($accessHash);
            if($url)
            {
                 $this->url         =   $url;
            }
        }

        public function setModule($moduleName, $accessHash)
        {
            $this->module           =   $moduleName;
            $this->moduleName       =   strtolower(str_replace(' ','',$moduleName));
            $this->accessHash       =   trim($accessHash);
        }

        public function setURL($url)
        {
            $this->url              =   $url;
        }

        /**
         * @param type $currentVersion
         */
        public function getLatestModuleVersion()
        {
            $request = array
            (
                'action'                =>  'getLatestModuleVersion',
            );

            return $this->send($request);
        }

        public function getActivePromotions()
        {
            $request = array
            (
                'action'                =>  'getActivePromotions'
            );

            return $this->send($request);
        }
        /**
         * Register new module instance
         * @param type $moduleVersion
         * @param type $serverIP
         * @param type $serverName
         * @return type
         */
        public function registerModuleInstance($moduleVersion, $serverIP, $serverName)
        {
            $request = array
            (
                'action'                =>  'registerModuleInstance',
                'data'                  =>  array
                (
                    'moduleVersion'     =>  $moduleVersion,
                    'serverIP'          =>  $serverIP,
                    'serverName'        =>  $serverName,
                )
            );

            return $this->send($request);
        }

        /**
         * Get all available products
         * @return type
         */
        public function getAvailableProducts()
        {
            $requst = array
            (
                'action'    =>  'getAvailableProducts',
            );

            return $this->send($requst);
        }


        private function send($data = array())
        {
            if(!$data)
            {
                return false;
            }

            if(empty($data['action']))
            {
                return false;
            }

            //Add module name and access hash
            $data['hash']   =   $this->accessHash;
            $data['module'] =   $this->module;

            //Are we have ane cache?
            $action = $data['action'];
            if(!empty($this->cache[$action]))
            {
                $value = self::getWHMCSconfig($this->moduleName.'_'.$action.'_time');
                if($value && $value + $this->cache[$action] > time())
                {
                    $lastResponse = self::getWHMCSconfig($this->moduleName.'_'.$action.'_response');
                    if($lastResponse)
                    {
                        return unserialize($lastResponse);
                    }
                }
            }

            //Encode data
            $data = json_encode($data);

            //Prepare Curl
            $ch = curl_init($this->url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            //curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Awesome-Octocat-App'));

            $ret = curl_exec($ch);

                $this->error = $ret;
                return false;


            if(!$ret)
            {
                $this->error = 'Did not receive any data. '.  curl_error($ch);
                return false;
            }

            /*
            $json = json_decode($ret);
            if(!$json)
            {
                $this->error = 'Invalid Format';
                return false;
            }

            if(!$json->status)
            {
                $this->error = $json->message;
                return false;
            }
            */

            self::saveWHMCSconfig($this->moduleName.'_'.$action.'_time', time());
            self::saveWHMCSconfig($this->moduleName.'_'.$action.'_response', serialize($ret));

            return $ret;
        }

        public function getError()
        {
            return $this->error;
        }

        protected function setError($error)
        {
            $this->error = $error;
        }

        /********************************************       STATICS     ***************************************/

        /**
         * Get local version of already installed module
         * @param type $module_name
         * @return boolean
         */
        static function getLocalVersion($module_name)
        {
            $row = self::getWHMCSconfig(strtolower(str_replace(' ','',$module_name)).'_version');
            if($row)
            {
                return $row;
            }

            return false;
        }

        /**
         * Save local version to WHMCS database
         * @param type $module_name
         * @param type $module_version
         */
        static function setLocalVersion($module_name, $module_version)
        {
            self::saveWHMCSconfig(strtolower(str_replace(' ','',$module_name)).'_version', $module_version);
        }

        /**
         * Use this function if you want to delete cache
         * @param type $module_name
         * @param type $function_name
         */
        static function clearCache($module_name,$function_name)
        {
            self::saveWHMCSconfig(strtolower(str_replace(' ','',$module_name)).'_'.$function_name, '');
        }

        static function getWHMCSconfig($k)
        {
            $q      =   self::mysql_safequery("SELECT value FROM tblconfiguration WHERE setting = ?", array($k));
            $ret    =   mysql_fetch_array($q);
            unset($q);

            if($ret['value'])
            {
                return $ret['value'];
            }
        }

        static function saveWHMCSconfig($k, $v)
        {
            $q      =   self::mysql_safequery("SELECT `value` FROM tblconfiguration WHERE `setting` = ?",array($k));
            $ret    =   mysql_fetch_array($q);
            unset($q);

            if(isset($ret['value']))
            {
                return self::mysql_safequery("UPDATE tblconfiguration SET value = ? WHERE setting = ?",array( $v, $k));
            }
            else
            {
                return self::mysql_safequery("INSERT INTO tblconfiguration  (setting,value) VALUES (?,?)",array($k, $v));
            }
        }

        static function mysql_safequery($query,$params=false)
        {
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
}