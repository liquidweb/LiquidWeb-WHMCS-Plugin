<?php

/**********************************************************************
 *  StormBilling 1.4 New Idea (2013-10-15)
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


if(!class_exists('StormBillingEventManager'))
{
    class StormBillingEventManager
    {
        static $events = array();

        static public function register($eventName)
        {
            if(!isset(self::$events[$eventName]))
            {
                self::$events[$eventName] = array();
            }
        }

        static public function attach($eventName, $callback)
        {
            if(isset(self::$events[$eventName]))
            {
                if(!in_array($callback, self::$events[$eventName]))
                {
                    self::$events[$eventName][] = $callback;
                    return true;
                }
            }

            return false;
        }

        static public function detach($eventName, $callback)
        {
            if(isset(self::$events[$eventName]))
            {
                foreach(self::$events[$eventName] as $eventKey => $eventCallback)
                {
                    if($eventCallback === $callback)
                    {
                        unset(self::$events[$eventName][$eventKey]);
                    }
                }
            }

            return false;
        }

        static function call($eventName /*$param, $param, $param */)
        {
            $params = func_get_args();
            unset($params[0]);
            
            if(self::$events[$eventName])
            {
                foreach(self::$events[$eventName] as $event)
                {
                    call_user_func_array($event, $params);
                }
            }
        }
    }
}