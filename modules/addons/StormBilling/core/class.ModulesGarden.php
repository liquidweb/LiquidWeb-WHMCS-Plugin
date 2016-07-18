<?php

if(!class_exists('ModulesGarden'))
{
    class ModulesGarden
    {
        /**
         * Get Module Class Name
         * @param type $file
         * @return type 
         */
        public static function getModuleClass($file)
        {
            $dirname = dirname($file);
            $basename = basename($dirname);
            return $basename;
        }
        
        static public function getAdmin()
        {
            $q = mysql_safequery('SELECT username FROM tbladmins WHERE roleid = 1 LIMIT 1');
            $row = mysql_fetch_assoc($q);

            return $row['username'];
        }
    }
}
