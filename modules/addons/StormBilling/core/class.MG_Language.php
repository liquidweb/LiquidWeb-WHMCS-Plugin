<?php

/**
 * Simple class to translataing languages
 */
 
if(!class_exists('MG_Language'))
{
    class MG_Language
    {
        private static $lang = null;

        public static function translate($key)
        {
            if(self::$lang == null)
            {
                $language = '';
                if(isset($_SESSION['Language'])) // GET LANG FROM SESSION
                { 
                    $language = strtolower($_SESSION['Language']);
                }
                else
                {
                    $q = mysql_query("SELECT language FROM tblclients WHERE id = ".$_SESSION['uid']);
                    $row = mysql_fetch_assoc($q); 
                    if($row['language'])
                        $language = $row['language'];
                }

                if(!$language)
                {
                    $q = mysql_query("SELECT value FROM tblconfiguration WHERE setting = 'Language' LIMIT 1");
                    $row = mysql_fetch_assoc($q);
                    $language = $row['language'];
                }

                if(!$language)
                {
                    $language = 'english';
                }

                if(file_exists(StormBillingDIR.'/lang/'.$language.'.php'))
                {
                    include StormBillingDIR.'/lang/'.$language.'.php';
                }

                if(isset($LANG))
                {
                    self::$lang = $LANG;
                }
            }

            if(isset(self::$lang[$key]))
            {
                return self::$lang[$key];
            }

            return $key;
        }

        public static function getLang()
        {
            if(self::$lang == null)
            {
                $LANG = null;

                $language = '';
                if(isset($_SESSION['Language'])) // GET LANG FROM SESSION
                { 
                    $language = strtolower($_SESSION['Language']);
                }
                else
                {
                    $q = mysql_query("SELECT language FROM tblclients WHERE id = ".$_SESSION['uid']);
                    $row = mysql_fetch_assoc($q); 
                    if($row['language'])
                        $language = $row['language'];
                }

                if(!$language)
                {
                    $q = mysql_query("SELECT value FROM tblconfiguration WHERE setting = 'Language' LIMIT 1");
                    $row = mysql_fetch_assoc($q);
                    $language = $row['language'];
                }

                if(!$language)
                {
                    $language = 'english';
                }

                if(file_exists(StormBillingDIR.'/lang/'.$language.'.php'))
                {
                    include StormBillingDIR.'/lang/'.$language.'.php';
                }

                if(isset($LANG))
                {
                    self::$lang = $LANG;
                }
            }

            return self::$lang;
        }
    }
}
