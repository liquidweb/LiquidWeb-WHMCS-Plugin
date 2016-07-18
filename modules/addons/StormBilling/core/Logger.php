<?php

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
require_once dirname(__FILE__).DS.'logger'.DS.'FileLogger.php';

/**
 * Simple wrapper for loging
 */
class Logger
{
    private static $engine = null;
    
    //default settings for file logger
    private static $engine_name = 'FileLogger';
    private static $settings = array
    (
        'info_file'     =>  'info_file.log',
        'error_file'    =>  'error_file.log'
    );
    
    public static function addInfo($info)
    {
        self::loadEngine();
        self::$engine->addInfo($info);
    }
    
    public static function addError($error)
    {
        self::loadEngine();
        self::$engine->addError($error);
    }
    
    public static function setEngine($name, $settings = null)
    {
        if($settings)
        {
            self::$settings = $settings;
        }
        self::$engine_name = $name;
    }
    
    private static function loadEngine()
    {
        if(!self::$engine)
        {
            self::$engine = new $engine_name(self::$settings);
        }
    }
}
?>