<?php

/**********************************************************************
 *  StormBilling Trunk (2014-01-17)
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

class StormBillingLogger
{
    static function error($error)
    {
        if(!trim($error))
        {
            return;
        }
        
        $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'cron'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'errorlog-'.gmdate('Y-m').'.log';
        @file_put_contents($file, date("Y-m-d G:i:s").': '.$error."\n", FILE_APPEND);
    }
     
    static function info($info)
    {
        if(!trim($info))
        {
            return;
        }
        
        $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'cron'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'infolog-'.gmdate('Y-m').'.log';
        @file_put_contents($file, date("Y-m-d G:i:s").': '.$info."\n", FILE_APPEND);
    }
    
    static function crtitical($crtitical)
    {
        if(!trim($crtitical))
        {
            return;
        }
        
        $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'cron'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'criticallog-'.gmdate('Y-m').'.log';
        @file_put_contents($file, date("Y-m-d G:i:s").': '.$crtitical."\n", FILE_APPEND);
    }
}