<?php

if(!class_exists('LWCustomConfig'))
{
    class LWCustomConfig
    {
        protected $class_name   =   '';

        protected $configs = array();

        public function loadCustomConfig()
        {
            $db_settings = array();
            $q = mysql_safequery('SELECT config_name,config_value FROM StormBilling_customconfig');
            while(($row = mysql_fetch_assoc($q))){
                $this->configs[$row['config_name']] = $row['config_value'];
            }
        }

        public function __construct()
        {
            $this->loadCustomConfig();
        }

        public function getConfigs()
        {
            return $this->configs;
        }

        public function saveConfigs($conf)
        {
            //Save custom configuration settings
            foreach($conf as $key=>$value)
            {
                $q = mysql_safequery("insert into StormBilling_customconfig (config_name,config_value)
                						values ('$key', '$value')
										ON DUPLICATE KEY UPDATE config_value='$value'");
            }
            $this->loadCustomConfig();
            return $this->configs;
        }
    }
}