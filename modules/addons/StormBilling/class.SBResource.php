<?php
/**********************************************************************
 *  Base class for resource modules. StormBilling
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
 * Base class for all resource class. You MUST implements this class!
 * @author Mariusz Miodowski
 */
if(!class_exists('SBResource'))
{
    class SBResource extends StormBillingResource
    {
        /**
         * This fields name will NOT be created in database
         */
        private $reservedFieldNames = array
        (
            'id',
            'hosting_id',
            'client_id',
            'product_id',
            'rel_id',
            'date',
            'total',
            'record_id'
        );
        
        /**
         * Module name
         */
        const name = 'MyCustomName';
        
        /**
         * Module description
         * @var type 
         */
        const description = 'Sample description';
        
        /**
         * Submodule version 
         */
        const version = 1.0;
        
        /**
         * You can set up supported server modules. For this products this module will be automatically loaded
         * @var type 
         */
        protected $supported_modules = array();
        /**
         *  Add custom HTML to configuration area
         * @var type 
         */
        protected $configuration_area = '';
        /**
         * Billing options 
         * @var type 
         */
        protected $billing_options = array();

        protected $compute_type = array();
        /**
         * Module resources
         * Ex: array('my_resource' => 'My SBResource')
         * @var type 
         */
        protected $resources = array();

        /**
         * Current product id
         * @var type 
         */
        protected $product_id = null;

        /**
         * Automatically loaded configuratiom
         * @var type 
         */
        protected $servers_configuration = null;

        /**
         * Product type
         * @var type 
         */
        protected $type = null;

        /**
         * Display custom configuration for your module
         * @var type 
         */
        protected $module_settings = array();


        /**
         * Module settins array
         * @var type 
         */
        protected $configuration = array();

        
        /**
         * Usage Records Pricing
         * @var type 
         */
        protected $pricing = array();

        /**
         * Interval for updating usage records. Use it only if you know what you are doing ;]
         * @var type 
         */
        protected $interval = 0;

        /**
         * Set this value as true when your module is supporting extendedPricing
         * @var type 
         */
        protected $extendedPricing = false;
        
        /**
         * Set up this array if you want to use configurable options as free limit
         * @var type 
         */
        protected $freeLimitFromConfigurableOptions = array();
        
        /**
         * Initialize class
         */
        public function __construct($product_id = 0)
        {
            //Set type
            $this->type = strtolower(substr(get_class($this), 0, strpos(get_class($this), '_resource')));
            //Set Product Type
            $this->product_id = $product_id;
            
            parent::__construct($product_id);
        }

        /**
         * Create submodules tables 
         */
        final function install()
        {
            $keys = array_keys($this->resources); 
            $sql = '';
            foreach($keys as $key)
            {
                $sql .= '`'.$key.'` DECIMAL(16, 6), ';
            } 

            mysql_safequery("CREATE TABLE IF NOT EXISTS `StormBilling_".$this->type."_records`
                (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `hosting_id`    INT NOT NULL,
                    `client_id`     INT NOT NULL,
                    `product_id`    INT NOT NULL,
                    `rel_id`        VARCHAR(128) DEFAULT '0',
                    ".$sql."
                    `date` DATETIME,
                    PRIMARY KEY(`id`),
                    KEY(`hosting_id`),
                    KEY(`client_id`),
                    KEY(`date`),
                    KEY(`rel_id`)
                )ENGINE=MyISAM;") or die(mysql_error());

            mysql_safequery("CREATE TABLE IF NOT EXISTS `StormBilling_".$this->type."_prices`
                (
                    `record_id`     INT NOT NULL,
                    `hosting_id`    INT NOT NULL,
                    `client_id`     INT NOT NULL,
                    `product_id`    INT NOT NULL,
                    `rel_id`        VARCHAR(128) DEFAULT '0',
                    ".$sql."
                    `date` DATETIME,
                    `total` DECIMAL(16,6),
                    KEY(`record_id`),
                    KEY(`hosting_id`),
                    KEY(`client_id`),
                    KEY(`date`),
                    KEY(`rel_id`)
                )ENGINE=MyISAM;") or die(mysql_error());
                    
            mysql_safequery("CREATE TABLE IF NOT EXISTS `StormBilling_".$this->type."_extendedPricing`
                (
                    `hosting_id`    INT NOT NULL,
                    `rel_id`        VARCHAR(128),
                    `resource`      VARCHAR(128),
                    `extended_id`   VARCHAR(128),
                    `value`         DECIMAL(16,6),
                    KEY(`hosting_id`),
                    KEY(`resource`),
                    KEY(`extended_id`)
                )ENGINE=MyISAM;") or die(mysql_error()); 
        }
        
        
        /**
         * This function should ran after module upgrade.
         * It should add or delete columns from database
         */
        final function upgrade()
        {
            $records = mysql_get_array("SHOW COLUMNS IN `StormBilling_".$this->type."_records`");
            if($records)
            {
                //Get Old Fields
                $old_fields = array();
                foreach($records as &$record)
                {
                    if(in_array($record['Field'], $this->reservedFieldNames))
                    { 
                        continue;
                    }
                    $old_fields[] = $record['Field'];
                }
                //Get New Fields
                $new_fields = array_keys($this->resources);
                
                //Fields to create
                $create = array_diff($new_fields, $old_fields);
                //Field to delete
                $delete = array_diff($old_fields, $new_fields);
                
                foreach($create as &$field)
                {
                    mysql_safequery("ALTER TABLE `StormBilling_".$this->type."_records` ADD COLUMN `".$field.'` DECIMAL(16, 6)') or die(mysql_error());
                }
                
                foreach($delete as &$field)
                {
                    mysql_safequery("ALTER TABLE `StormBilling_".$this->type."_records` DROP COLUMN `".$field."`") or die(mysql_error());
                }
            }
            
            $records = mysql_get_array("SHOW COLUMNS IN `StormBilling_".$this->type."_prices`");
            if($records)
            {
                //Get Old Fields
                $old_fields = array();
                foreach($records as &$record)
                {
                    if(in_array($record['Field'], $this->reservedFieldNames))
                    { 
                        continue;
                    }
                    $old_fields[] = $record['Field'];
                }
                //Get New Fields
                $new_fields = array_keys($this->resources);
                
                //Fields to create
                $create = array_diff($new_fields, $old_fields);
                //Field to delete
                $delete = array_diff($old_fields, $new_fields);
                
                foreach($create as &$field)
                {
                    mysql_safequery("ALTER TABLE `StormBilling_".$this->type."_prices` ADD COLUMN `".$field.'` DECIMAL(16, 6)') or die(mysql_error());
                }
                
                foreach($delete as &$field)
                {
                    mysql_safequery("ALTER TABLE `StormBilling_".$this->type."_prices` DROP COLUMN `".$field."`") or die(mysql_error());
                }
            }
        }

        /**
         * Uninstall submodule table
         */
        final function uninstall()
        {
            mysql_safequery("DROP TABLE IF EXISTS `StormBilling_".$this->type."_prices`");
            mysql_safequery("DROP TABLE IF EXISTS `StormBilling_".$this->type."_records`");
            mysql_safequery("DROP TABLE IF EXISTS `StormBilling_".$this->type."_extendedPricing`");
        }
        /**
         * Set product ID. Function is called automatically by cron
         * @param type $product_id 
         */
        final public function setProductId($product_id)
        {
            $this->product_id = $product_id;
        }

        /**
         * Function is called by main configuration and cron. Should return available resources
         * @return type 
         * @Mariusz Miodowski
         */
        final public function getResources()
        {
            return $this->resources;
        }

        final public function getConfiguration()
        {
            return $this->configuration;
        }

        final public function setConfiguration($conf)
        {
            foreach($conf as $key => $value)
            {
                if(isset($this->configuration[$key]))
                {
                    $this->configuration[$key]['Value'] = $value;
                }
            }
        }

        /**
         * Add resources to database
         * @param type $client_id
         * @param type $hosting_id
         * @param type $domain
         * @param type $resources
         * @return type 
         * @author Mariusz Miodowski
         */
        protected function insertResource($client_id, $hosting_id, $resources = array(), $rel_id = 0)
        {
            //Is type setup?
            if(!$this->type)
            {
                $this->logError('Cannot add usage records because module type is not setup');
                return;
            }
            
            //Is pricing setup?
            if(!$this->pricing)
            {
                $this->logError('Cannot add resource. Pricing doest not exists for this module ('.$this->type.')');
                return;
            }     
            
            //Re-Format Resources
            foreach($resources as $res_key => &$res_val)
            {
                //If module is not supporting multipricing
                if(!is_array($res_val))
                {
                    $val = $res_val ? $res_val : 0;
                    $res_val = array
                    (
                        0   =>  $val
                    );
                }
                //If module is supporting One varaible with Value and Relid
                elseif(is_array($res_val) && isset($res_val['Value']) && isset($res_val['Relid']))
                {
                    $relid  =   strlen($res_val['Relid']) ? $res_val['Relid'] : 0;
                    $val    =   $res_val['Value'] ? $res_val['Value'] : 0;
                    
                    $res_val = array
                    (
                        $relid    =>  $val
                    );
                }
                elseif(is_array($res_val) && empty($res_val))
                {
                    $res_val[0] =   0;
                }
            }
            unset($res_key, $res_val);
            
            //Current Time
            $curr_time = time();
            
            //MySQL Date
            if(is_null($this->recordStartDate))
            {
                $date = date('Y-m-d H:i:s', $curr_time);
            }
            else
            {
                $date = date('Y-m-d H:i:s', $this->recordStartDate);
            }
            
            //Get Current Usage
            $current_usage = array();
            $rows = mysql_get_array("SELECT resource, extended_id, value, rel_id FROM `StormBilling_".$this->type."_extendedPricing` WHERE hosting_id = ?", array($hosting_id));
            foreach($rows as $row)
            {
                $current_usage[$row['rel_id']][$row['resource']][$row['extended_id']] = $row['value'];
            }
            unset($rows, $row);
 
            //Update Current Usage
            foreach($resources as $res_key => $res_val)
            {
                foreach($res_val as $ext_key => $ext_val)
                {
                    $q = mysql_safequery("SELECT hosting_id FROM StormBilling_".$this->type."_extendedPricing WHERE hosting_id = ? AND rel_id = ? AND resource = ? AND extended_id = ?", array(
                        $hosting_id,
                        $rel_id,
                        $res_key,
                        $ext_key
                    ));
                    
                    if(!mysql_num_rows($q))
                    {
                        mysql_safequery("INSERT INTO `StormBilling_".$this->type."_extendedPricing` (`hosting_id`, `rel_id`, `resource`, `extended_id`, `value`) VALUES(?, ?, ?, ?, ?)", array(
                            $hosting_id,
                            $rel_id,
                            $res_key,
                            $ext_key,
                            $ext_val,
                        )) or die(mysql_error());
                    }
                    else
                    {
                        mysql_safequery("UPDATE `StormBilling_".$this->type."_extendedPricing` SET `value` = `value` + ? WHERE `hosting_id` = ? AND `resource` = ? AND `extended_id` = ? AND `rel_id` = ?", array(
                            $ext_val,
                            $hosting_id,
                            $res_key,
                            $ext_key,
                            $rel_id
                        )) or die(mysql_error());
                    }
                }
            }
            unset($res_key, $res_val, $ext_key, $ext_val);
            
            //Count Summary
            foreach($resources as $res_key => $res_val)
            {
                $records[$res_key] = array_sum($res_val);
            }
            unset($res_key, $res_val);
            
            $records['date']        =   $date;
            $records['hosting_id']  =   $hosting_id;
            $records['product_id']  =   $this->product_id;
            $records['client_id']   =   $client_id;
            $records['rel_id']      =   $rel_id;
            //Get Resource Keys
            $keys = array_keys($records);
            //Add

            foreach($keys as &$key)
            {
                $key = '`'.$key.'`';
            }
            unset($key);

            
            //Add Records To Database
            mysql_safequery("INSERT INTO `StormBilling_".$this->type."_records` (".implode(',', $keys).") VALUES(".implode(',', array_fill(0, count($keys), '?')).")", $records) or die(mysql_error());
            $record_id = mysql_insert_id();
            
            //Count difference between last cron run
            $time_diff = 0.0001;
            if($this->interval)
            {
                $time_diff = $this->interval;
            }
            else
            {
                $last_update = strtotime($this->getLastUpdate($hosting_id, $rel_id));
                if(!$last_update)
                {
                    $last_update = doubleval($curr_time) - doubleval(0.0001);
                }
                
                $time_diff = doubleval($curr_time) - doubleval($last_update);
            }
                        
            //Load Free Limits From Configurable Options
            $freeLimits = array();
            if($this->freeLimitFromConfigurableOptions)
            {
                $rows = mysql_get_array("SELECT copt.optionname as `option`, hopt.qty, sub.optionname as suboption, copt.optiontype as `type`
                    FROM tblhostingconfigoptions hopt
                    LEFT JOIN tblproductconfigoptions copt ON hopt.configid = copt.id
                    LEFT JOIN tblproductconfigoptionssub sub ON hopt.optionid = sub.id
                    WHERE hopt.relid = ?", array($hosting_id));
                
  
                foreach($rows as $row)
                {
                    $option     =   $row['option'];
                    $suboption  =   $row['suboption'];
                    
                    if(strpos($row['option'], '|') !== false)
                    {
                        $option = substr($option, 0, strpos($option, '|')); 
                    }
                    
                    if(strpos($row['suboption'], '|') !== false)
                    {
                        $suboption = substr($suboption, 0, strpos($suboption, '|')); 
                    }
                    
                    if($row['type'] == '4')
                    {
                        $freeLimits[$option] = $row['qty'];
                    }
                    else
                    {
                        $freeLimits[$option] = $suboption;
                    }
                }
                unset($row);
            }
            
            //Compute Prices
            $prices =   array();
            foreach($resources as $resource_key => $resource_val)
            {
                //
                $prices[$resource_key] = 0;
                
                foreach($resource_val as $ext_id => $val)
                {
                    $value      = doubleval($val);
                    //Are we have extended pricing for this ext_id ?
                    if(isset($this->pricing[$resource_key]['ExtendedPricing'][$ext_id]['Price']) && $this->pricing[$resource_key]['ExtendedPricing'][$ext_id]['Price'] >= 0)
                    {
                        $price      = doubleval($this->pricing[$resource_key]['ExtendedPricing'][$ext_id]['Price']);
                        $free_limit = doubleval($this->pricing[$resource_key]['ExtendedPricing'][$ext_id]['FreeLimit']);
                    }
                    //Nope? Use default
                    else
                    {
                        $price      = doubleval($this->pricing[$resource_key]['price']);
                        $free_limit = doubleval($this->pricing[$resource_key]['free_limit']);
                    }
                    
                    //Is Free Limit Set Up In Configurable Options? Overwrite main configuration
                    if($freeLimits && isset($freeLimits[$resource_key]))
                    {
                        $free_limit = doubleval($freeLimits[$resource_key]);
                    }
                    
                    //count price for specified usage record
                    switch($this->pricing[$resource_key]['type'])
                    { 
                        case 'average': 
                            $diff = $value - $free_limit;
                            if($diff > 0)
                            {
                                $value = (doubleval($time_diff) / (double)3600) * $price * $diff;
                            }
                            else
                            {
                                $value = 0;
                            }
                            break;
                        case 'summary':
                            if($free_limit > 0)
                            {
                                $sum = isset($current_usage[$rel_id][$resource_key][$ext_id]) ? $current_usage[$rel_id][$resource_key][$ext_id] : 0;
                                $diff = doubleval($sum) - $free_limit;

                                if($diff > 0)
                                {
                                    $value = $value * $price;
                                }
                                else
                                { 
                                    if($diff + $value > 0)
                                    {
                                        $value = ($diff + $value) * $price; 
                                    }
                                    else
                                    {
                                        $value = 0;
                                    }
                                }
                            }
                            else
                            {
                                $value = $value * $price;
                            }
                            break;
                        
                        default:
                            $value = 0;
                            break;
                    }
                }
                
                $prices[$resource_key] += $value;
            }
            
            $prices['total']        =   array_sum($prices);
            $prices['date']         =   $date;
            $prices['hosting_id']   =   $hosting_id;
            $prices['product_id']   =   $this->product_id;
            $prices['client_id']    =   $client_id;
            $prices['record_id']    =   $record_id;
            $prices['rel_id']       =   $rel_id;
            
            //Get Resource Keys
            $keys = array_keys($prices);
            
            foreach($keys as &$key)
            {
                $key = '`'.$key.'`';
            }

            //Insert Prices
            mysql_safequery("INSERT INTO `StormBilling_".$this->type."_prices` (".implode(',', $keys).") VALUES(".implode(',', array_fill(0, count($keys), '?')).")", $prices) or die(mysql_error());
                    
            //Insert Last Update
            mysql_safequery("DELETE FROM StormBilling_updates WHERE hosting_id = ? AND rel_id = ?", array($hosting_id, $rel_id));
            mysql_safequery("INSERT INTO StormBilling_updates(`hosting_id`, `rel_id`, `timestamp`) VALUES(?, ?, ?)", array($hosting_id, $rel_id, $date));
            
            //Insert Billed Account
            mysql_safequery("REPLACE INTO StormBilling_billed_hostings SET `date` = ?, hosting_id = ?", array($date, $hosting_id));
            
            StormBillingEventManager::call('StormBillingResourceAdded', $client_id, $this->product_id, $hosting_id, $record_id);
        }
        
        protected function insertMultiResources($client_id, $hosting_id, $multi_resources = array())
        {
            //Is type setup?
            if(!$this->type)
            {
                $this->logError('Cannot add usage records because module type is not setup');
                return;
            }

            //Is pricing setup?
            if(!$this->pricing)
            {
                $this->logError('Cannot add resource. Pricing doest not exists for this module ('.$this->type.')');
                return;
            }
            
            //Current Time
            $curr_time = time();
            
            //MySQL Date
            if(is_null($this->recordStartDate))
            {
                $date = date('Y-m-d H:i:s', $curr_time);
            }
            else
            {
                $date = date('Y-m-d H:i:s', $this->recordStartDate);
            }
            
            //Re-Format Resources
            $newFormat = array();
            foreach($multi_resources as $rel_id => $resources)
            {
                if(isset($resources['Resources']))
                {
                    foreach($resources['Resources'] as $res_key => $res_val)
                    {
                        if(!is_array($res_val))
                        {
                            $val = $res_val;
                            $res_val = array
                            (
                                'type'      =>  'SinglePricing',
                                'resources' =>  array
                                (
                                    $val
                                )
                            );
                        }
                        elseif(is_array($res_val) && !isset($res_val['type']))
                        {
                            $key = key($res_val);
                            $val = current($res_val);

                            $res_val = array
                            (
                                'type'      =>  'ExtendedPricing',
                                'resources' =>  array
                                (
                                    $key    =>  $val
                                )
                            );
                        }
                        //elseif()
                    }
                }
                else
                {
                    foreach($resources as $res_key => $res_val)
                    {
                        if(is_array($res_val) && isset($res_val['Value']) && isset($res_val['Relid']))
                        {
                            $newFormat[$rel_id][$res_key][$res_val['Relid']] = $res_val['Value'];
                        }
                        elseif(is_array($res_val))
                        {
                            if(!empty($res_val))
                            {
                                foreach($res_val as $ext_key => $ext_val)
                                {
                                    if($ext_key == '')
                                    {
                                        $ext_key = 0;
                                    }
                                    
                                    if($ext_val == '' || $ext_val < 0)
                                    {
                                        $ext_val = 0;
                                    }
                                    
                                    $newFormat[$rel_id][$res_key][$ext_key] = $ext_val;
                                }
                            }
                            else
                            {
                                $newFormat[$rel_id][$res_key][0] = 0;
                            }
                        }
                        else
                        {
                            if($res_val == '' || $res_val < 0)
                            {
                                $res_val = 0;
                            }
                            
                            $newFormat[$rel_id][$res_key][0] = $res_val;
                        } 
                    }
                }
            }
            unset($rel_id, $resources, $res_key, $res_val);

            $multi_resources = $newFormat;
            unset($newFormat);
            
            
            //Get Current Usage. In multiresouces we have free limits per whole accoun so we no need to divind it per vm
            $current_usage = array();
            $rows = mysql_get_array("SELECT resource, extended_id, value, rel_id FROM `StormBilling_".$this->type."_extendedPricing` WHERE hosting_id = ?", array($hosting_id));
            foreach($rows as $row)
            {
                if(!isset($current_usage[$row['resource']][$row['extended_id']]))
                {
                    $current_usage[$row['resource']][$row['extended_id']] = 0;
                }
                $current_usage[$row['resource']][$row['extended_id']] += $row['value'];
            }
            
            //Update Current Usage
            foreach($multi_resources as $rel_id => $resources)
            {
                foreach($resources as $res_key => $res_val)
                {
                    foreach($res_val as $ext_key => $ext_val)
                    {
                        $q = mysql_safequery("SELECT hosting_id FROM StormBilling_".$this->type."_extendedPricing WHERE hosting_id = ? AND rel_id = ? AND resource = ? AND extended_id = ?", array(
                            $hosting_id,
                            $rel_id,
                            $res_key,
                            $ext_key
                        )) or die(mysql_error);

                        if(!mysql_num_rows($q))
                        {
                            mysql_safequery("INSERT INTO `StormBilling_".$this->type."_extendedPricing` (`hosting_id`, `rel_id`, `resource`, `extended_id`, `value`) VALUES(?, ?, ?, ?, ?)", array(
                                $hosting_id,
                                $rel_id,
                                $res_key,
                                $ext_key,
                                $ext_val,
                            )) or die(mysql_error());
                        }
                        else
                        {
                            mysql_safequery("UPDATE `StormBilling_".$this->type."_extendedPricing` SET `value` = `value` + ? WHERE `hosting_id` = ? AND `resource` = ? AND `extended_id` = ? AND `rel_id` = ?", array(
                                $ext_val,
                                $hosting_id,
                                $res_key,
                                $ext_key,
                                $rel_id
                            )) or die(mysql_error());
                        }
                    }
                }
            }
            unset($resources, $res_key, $res_val, $ext_key, $ext_val);
            
            //Records IDs
            $records_ids = array();
            
            //Insert Usage Records
            foreach($multi_resources as $resource_key => $resource)
            {
                $records                =   $resource;
                //Summarize extended pricing records. 
                foreach($records as $r_key => $val)
                {
                    $records[$r_key]    =   array_sum($val);
                }
                unset($r_key, $val);
                
                $records['date']        =   $date;
                $records['hosting_id']  =   $hosting_id;
                $records['product_id']  =   $this->product_id;
                $records['client_id']   =   $client_id;
                $records['rel_id']      =   $resource_key;
                //Get Resource Keys
                $keys = array_keys($records);
                

                foreach($keys as &$key)
                {
                    $key = '`'.$key.'`';
                } 

                //Add Records To Database
                mysql_safequery("INSERT INTO `StormBilling_".$this->type."_records` (".implode(',', $keys).") VALUES(".implode(',', array_fill(0, count($keys), '?')).")", $records) or die(mysql_error());
                //Save Record ID. We need it!
                $records_ids[$resource_key] = mysql_insert_id();
            }
            unset($resource_key, $resource);
            
                    
            //Set the time diff
            $time_diff = 0.0001;
            if($this->interval)
            {
                $time_diff = $this->interval;
            }
            else
            {
                $last_update = strtotime($this->getLastUpdate($hosting_id));
                if(!$last_update)
                {
                    $last_update = doubleval($curr_time) - doubleval(0.0001);
                }
                
                $time_diff = doubleval($curr_time) - doubleval($last_update);
            }
            
            
            $summarized_resources = array();
            foreach($multi_resources as $resources)
            {
                foreach($resources as $res_key => $res_val)
                {
                    foreach($res_val as $ext_key => $ext_val)
                    {
                        if(!isset($summarized_resources[$res_key][$ext_key]))
                        {
                            $summarized_resources[$res_key][$ext_key] = 0;
                        }
                        
                        $summarized_resources[$res_key][$ext_key] += $ext_val;
                    }
                }
                
            }
            unset($resources, $res_key, $res_val, $ext_key, $ext_val);
 
            
            //Load Free Limits From Configurable Options
            $freeLimits = array();
            if($this->freeLimitFromConfigurableOptions)
            {
                $rows = mysql_get_array("SELECT copt.optionname as `option`, hopt.qty, sub.optionname as suboption, copt.optiontype as `type`
                    FROM tblhostingconfigoptions hopt
                    LEFT JOIN tblproductconfigoptions copt ON hopt.configid = copt.id
                    LEFT JOIN tblproductconfigoptionssub sub ON hopt.optionid = sub.id
                    WHERE hopt.relid = ?", array($hosting_id));
                
  
                foreach($rows as $row)
                {
                    $option     =   $row['option'];
                    $suboption  =   $row['suboption'];
                    
                    if(strpos($row['option'], '|') !== false)
                    {
                        $option = substr($option, 0, strpos($option, '|')); 
                    }
                    
                    if(strpos($row['suboption'], '|') !== false)
                    {
                        $suboption = substr($suboption, 0, strpos($suboption, '|')); 
                    }
                    
                    if($row['type'] == '4')
                    {
                        $freeLimits[$option] = $row['qty'];
                    }
                    else
                    {
                        $freeLimits[$option] = $suboption;
                    }
                }
                unset($row);
            }
            
            
            //Compute Summarized Prices
            $summarized_prices = array();
            foreach($summarized_resources as $resource_key => $res_val)
            {
                foreach($res_val as $ext_id => $val)
                {
                    $value      = doubleval($val);
                    //Are we have extended pricing for this ext_id ?
                    if(isset($this->pricing[$resource_key]['ExtendedPricing'][$ext_id]['Price']) && $this->pricing[$resource_key]['ExtendedPricing'][$ext_id]['Price'] >= 0)
                    {
                        $price      = doubleval($this->pricing[$resource_key]['ExtendedPricing'][$ext_id]['Price']);
                        $free_limit = doubleval($this->pricing[$resource_key]['ExtendedPricing'][$ext_id]['FreeLimit']);
                    }
                    //Nope? Use default
                    else
                    {
                        $price      = doubleval($this->pricing[$resource_key]['price']);
                        $free_limit = doubleval($this->pricing[$resource_key]['free_limit']);
                    }
                    
                    //Is Free Limit Set Up In Configurable Options? Overwrite main configuration
                    if($freeLimits && isset($freeLimits[$resource_key]))
                    {
                        $free_limit = doubleval($freeLimits[$resource_key]);
                    }     
                                    
                    //count price for specified usage record
                    switch($this->pricing[$resource_key]['type'])
                    { 
                        case 'average': 
                            $diff = $value - $free_limit;
                            if($diff > 0)
                            {
                                $value = ($time_diff / (double)3600) * $price * $diff;
                            }
                            else
                            {
                                $value = 0;
                            }
                            break;
                        case 'summary':
                            if($free_limit > 0)
                            {
                                $sum = isset($current_usage[$resource_key][$ext_id]) ? $current_usage[$resource_key][$ext_id] : 0;
                                $diff = doubleval($sum) - $free_limit;

                                if($diff > 0)
                                {
                                    $value = $value * $price;
                                }
                                else
                                { 
                                    if($diff + $value > 0)
                                    {
                                        $value = ($diff + $value) * $price; 
                                    }
                                    else
                                    {
                                        $value = 0;
                                    }
                                }
                            }
                            else
                            {
                                $value = $value * $price;
                            }
                            break;

                        default:
                            $value = 0;
                            break;
                    }
                    
                    $summarized_prices[$resource_key][$ext_id] += $value;
                }   
            }
            
            foreach($multi_resources as $resource_key => $resource)
            {
                $prices = array();
                foreach($resource as $res_key => $res_val)
                {
                    foreach($res_val as $ext_key => $ext_val)
                    {
                        if(!isset($prices[$res_key]))
                        {
                            $prices[$res_key] = 0;
                        }
                        
                        $prices[$res_key] += ($ext_val / $summarized_resources[$res_key][$ext_key]) * $summarized_prices[$res_key][$ext_key];
                    }
                }
                unset($res_key, $res_val, $ext_key, $ext_val);

                
                $prices['total']        =   array_sum($prices);
                $prices['date']         =   $date;
                $prices['hosting_id']   =   $hosting_id;
                $prices['product_id']   =   $this->product_id;
                $prices['client_id']    =   $client_id;
                $prices['record_id']    =   $records_ids[$resource_key];
                $prices['rel_id']       =   $resource_key;
                //Get Resource Keys
                $keys = array_keys($prices);
                
                foreach($keys as &$key)
                {
                    $key = '`'.$key.'`';
                }

                //Insert Prices
                mysql_safequery("INSERT INTO `StormBilling_".$this->type."_prices` (".implode(',', $keys).") VALUES(".implode(',', array_fill(0, count($keys), '?')).")", $prices) or die(mysql_error());
            }
            
            //Insert Last Update
            mysql_safequery("DELETE FROM StormBilling_updates WHERE hosting_id = ? AND rel_id = 0", array($hosting_id, 0));
            mysql_safequery("INSERT INTO StormBilling_updates(`hosting_id`, `rel_id`, `timestamp`) VALUES(?, ?, ?)", array($hosting_id, 0, $date));

            //Insert Billed Account
            mysql_safequery("REPLACE INTO StormBilling_billed_hostings SET `date` = ?, hosting_id = ?", array($date, $hosting_id));
            
            foreach($records_ids as $id)
            {
                StormBillingEventManager::call('StormBillingResourceAdded', $client_id, $this->product_id, $hosting_id, $id);
            }
        }

        /**
         * Simple helper to get product accounts. For standard products it should works
         * @return type 
         * @author Mariusz Miodowski
         */
        protected function getProductAccounts()
        {
            $accounts = array();
            $q = mysql_safequery("SELECT DISTINCT h.id as hosting_id, c.email, h.domain, h.username as customerid, h.password as customerpass, s.ipaddress, s.hostname, s.username, s.password, s.accesshash, s.secure
                                  FROM `tblhosting` h 
                                  JOIN `tblproducts` p ON(h.packageid = p.id) 
                                  LEFT JOIN `tblservers` s ON(h.server = s.id)
                                  JOIN `tblclients` c ON(c.id = h.userid)
                                  WHERE p.servertype = ? AND p.id = ?", array($this->type, $this->product_id));

            while($row = mysql_fetch_assoc($q))
            {
                $accounts[] = array
                (
                    'username'          =>  $row['customerid'],
                    'client_id'         =>  $row['customerid'],
                    'customerpass'      =>  $row['customerpass'],
                    'hosting_id'        =>  $row['hosting_id'],
                    'serverip'          =>  $row['ipaddress'],
                    'serverhostname'    =>  $row['hostname'],
                    'serverusername'    =>  $row['username'],
                    'serverpassword'    =>  decrypt($row['password']),
                    'serveraccesshash'  =>  $row['accesshash'],
                    'serversecure'      =>  $row['secure'],
                    'domain'            =>  $row['domain'],
                    'email'             =>  $row['email']
                );
            }

            return $accounts;
        }

        public function setBillingOptions($billing_options)
        {
            $this->billing_options = $billing_options;
        }

        public function getDescription()
        {
            return $this->description;
        }
   
        public function getSupportedModules()
        {
            return $this->getSupportedModules();
        }
        /**
         * Zwraca czas ostatniego uzycia getSample. Czasem może sie przydac ;] 
         * @param type $hosting_id
         * @return type 
         * @author Mariusz Miodowski
         */
        protected function getLastUpdate($hosting_id, $rel_id = 0)
        {
            $q = mysql_safequery('SELECT `timestamp` FROM StormBilling_updates WHERE hosting_id = ? AND rel_id = ?', array($hosting_id, $rel_id));
            $row = mysql_fetch_assoc($q);
            
            if($row)
            {
                return $row['timestamp'];
            }
            
            return false;
        }

        /**
         * Simple function to storing data per account. 
         * @param type $hosting_id
         * @param type $data 
         * @author Mariusz Miodowski
         */
        protected function saveSettings($hosting_id, $data = array())
        {
            $q = mysql_safequery("REPLACE INTO StormBilling_submodules_data SET hosting_id = ?, data = ?", array($hosting_id, serialize($data)));
        }

        /**
         * Read module data for account
         * @param type $hosting_id
         * @return type 
         * @author Mariusz Miodowski
         */
        protected function getSettings($hosting_id)
        {
            $q = mysql_safequery("SELECT data FROM StormBilling_submodules_data WHERE hosting_id = ? LIMIT 1", array($hosting_id));
            if(mysql_num_rows($q))
            {
                $row = mysql_fetch_assoc($q);
                return unserialize($row['data']);
            }

            return array();
        }

        protected function hasResourceRecords($hosting_id)
        {
            $type = strtolower($this->type);
            $row = mysql_get_row("SELECT * FROM StormBilling_".$type."_prices WHERE hosting_id = ? AND product_id = ? LIMIT 1", array($hosting_id, $this->product_id));

            return $row ? true : false;
        }

        public function getConfigurationArea()
        {
            return $this->configuration_area;
        }

        public function setPricing($pricing = array())
        {
            $this->pricing = $pricing;
        }

        protected function setInterval($interval)
        {
            $this->interval = $interval;
        }

        protected function logError($error)
        {
            StormBillingLogger::error($error);
        }

        protected function logInfo($info)
        {
            StormBillingLogger::error($info);
        }


        /****************** 1.4 *******************/
        public function getRelIDName($rel_id){return $rel_id;}
        
        /****************** 2.0.2 ****************/
        
        /** 
         * If you cannot run insertResources you should run this function!
         * @param type $hosting_id
         * @param type $rel_id
         */
        protected function forceLastUpdate($hosting_id, $rel_id = 0)
        {
            StormBillingLogger::info('Force last update fot hosting ID '.$hosting_id);
            //Current Time
            $curr_time = time();
            //MySQL Date
            $date = date('Y-m-d H:i:s', $curr_time);
            
            //Insert Last Update
            mysql_safequery("DELETE FROM StormBilling_updates WHERE hosting_id = ? AND rel_id = ?", array($hosting_id, $rel_id));
            mysql_safequery("INSERT INTO StormBilling_updates(`hosting_id`, `rel_id`, `timestamp`) VALUES(?, ?, ?)", array($hosting_id, $rel_id, $date));
        }
        
        protected function inactiveAccount($hosting_id, $rel_id)
        {
            $this->forceLastUpdate($hosting_id, $rel_id);
        }
    }
}
