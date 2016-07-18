<?php
 
if(!class_exists('SBProduct'))
{
    class SBProduct
    {
        protected $class_name   =   '';
        
        protected $used_module = false;
        //Seted to true when product has dedicated module
        protected $dedicated_module = false;

        protected $_module = null;

        protected $server_type = null;


        protected $settings = array
        (
            'enable'                =>  '',
            'billing_settings'      =>  array(),
            'resource_settings'     =>  array(),
            'module_configuration'  =>  array(),
            'credit_billing'        =>  array(),
            'module'                =>  '',
        );

        /**
         * ID of our products
         * @var type 
         */
        protected $product_id = null;
        /**
         * This variable is true when StormBilling is supporting module.
         * @var type 
         */
        protected $isSupported  =   false;


        public function loadConfiguration($settings = array())
        {
            if($settings)
            {
                $this->settings = $settings;
            }
            else
            {
                $db_settings = array();
                $q = mysql_safequery('SELECT * FROM StormBilling_settings WHERE product_id = ?', array($this->product_id));
                $row = mysql_fetch_assoc($q);

                if($row)
                {
                    foreach($this->settings as $key => &$setting)
                    {
                        if(isset($row[$key]))
                        {
                            $setting = $row[$key];
                        }
                    }
                    $this->settings['billing_settings'] = unserialize($this->settings['billing_settings']);
                    $this->settings['resource_settings'] = unserialize($this->settings['resource_settings']);
                    $this->settings['module_configuration'] = unserialize($this->settings['module_configuration']);
                }
            }
                
            $module_settings = array();
            $q = mysql_safequery('SELECT servertype FROM tblproducts WHERE id = ?', array($this->product_id));
            $row = mysql_fetch_assoc($q);

            $servertype = $row['servertype'];

            //Dedicated module
            if(file_exists(StormBillingDIR.DS.'submodules'.DS.'class.'.$servertype.'_resources.php'))
            {
                $this->dedicated_module = true;
            }
            else
            {
                if($this->settings['module'])
                {
                    $servertype = $this->settings['module'];
                }
                else
                {
                    $servertype = 'default';
                }
            }
            
            //Load Module
            $load = $this->loadModule($servertype);
            if(!$load)
            {
                return false;
            }
            
            //Is getResource exists?
            if(!method_exists($this->_module, 'getResources'))
            {
                StormBillingLogger::critical("Method getResources for ".$servertype." not exists");
                $this->isSupported  =   false;
                return false;
            }
            //Get Resrouce

            if(basename($_SERVER['PHP_SELF']) == 'addonmodules.php' && method_exists($this->_module, 'loadExtendedPricingConfiguration') && $_REQUEST['modpage'] == 'configuration' && $_REQUEST['modsubpage'] == 'edit')
            {
                $this->_module->loadExtendedPricingConfiguration();
            }

            $module_settings = $this->_module->getResources();

            //Set Server Type
            $this->server_type = strtolower($servertype);

            if($servertype == 'default')
            {
                $this->server_type = $servertype;
                $this->dedicated_module = false;
            } 
            //Set Product ID
            $this->_module->setProductId($this->product_id);

            //copy settings
            foreach($this->settings['resource_settings'] as $key => $s)
            {
                if(isset($module_settings[$key]))
                {
                    $module_settings[$key]['free_limit']        =   $s['free_limit'];
                    $module_settings[$key]['price']             =   isset($s['price']) ? $s['price'] : 0;
                    $module_settings[$key]['type']              =   $s['type'];
                    $module_settings[$key]['unit']              =   $s['unit'];
                    if(!empty($s['ExtendedPricing']))
                    {
                        if(basename($_SERVER['PHP_SELF']) == 'addonmodules.php')
                        {
                            foreach($module_settings[$key]['ExtendedPricing'] as &$ep)
                            {     
                                $ep['Price']        =   $s['ExtendedPricing'][$ep['Relid']]['Price'];
                                $ep['FreeLimit']    =   $s['ExtendedPricing'][$ep['Relid']]['FreeLimit'];
                            }
                        }
                        else
                        {
                            foreach($s['ExtendedPricing'] as $ep_key => $ep_val)
                            {
                                $module_settings[$key]['ExtendedPricing'][] = array
                                (
                                    'FreeLimit' =>  $ep_val['FreeLimit'],
                                    'Price'     =>  $ep_val['Price'],
                                    'Relid'     =>  $ep_key
                                );
                            }
                        }
                    }
                }
            }

            $this->settings['resource_settings'] = $module_settings;
            //$this->_module->setBillingOptions($this->getBillingSettings());

            if($this->settings['module_configuration'])
            {
                $this->_module->setConfiguration($this->settings['module_configuration']);
            }

            //Set Submodule Pricing
            if($this->settings['resource_settings'])
            {
                $pricing = array();
                foreach($this->settings['resource_settings'] as $record_name => $record_values)
                {
                    $pricing[$record_name]  =   array
                    (
                        'type'          =>  $record_values['type'],
                        'price'         =>  $record_values['price'],
                        'free_limit'    =>  $record_values['free_limit']
                    );

                    if(!empty($this->settings['resource_settings'][$record_name]['ExtendedPricing']))
                    {
                        foreach($this->settings['resource_settings'][$record_name]['ExtendedPricing'] as &$ep)
                        {
                            $pricing[$record_name]['ExtendedPricing'][$ep['Relid']]['Price'] = $ep['Price'];
                            $pricing[$record_name]['ExtendedPricing'][$ep['Relid']]['FreeLimit'] = $ep['FreeLimit'];
                        }
                    }
                }
 
                $this->_module->setPricing($pricing);
            }
            
            $this->isSupported  =   true;
        }
        /**
         * Load product configuration and check that product is supported by StormBilling
         * @param type $product_id
         * @return boolean
         */
        public function __construct($product_id,$settings = array(), $auto_load = 1)
        {
            $this->product_id = $product_id;
            
            if($auto_load)
            {
                $this->loadConfiguration($settings);
            }
        }

        public function getSettings()
        {
            return $this->settings;
        }

        public function saveSettings($settings)
        {
            //Install submodule
            foreach($settings['resource_settings'] as &$r)
            { 
                $r['price']         =   floatval($r['price']);
                $r['free_limit']    =   floatval($r['free_limit']);
                if(isset($r['ExtendedPricing']))
                {
                    foreach($r['ExtendedPricing'] as &$ep)
                    {
                        $ep['Price']        =   isset($ep['Price']) && $ep['Price'] != '' ? floatval($ep['Price']) : $r['price'];
                        $ep['FreeLimit']    =   isset($ep['FreeLimit']) && $ep['FreeLimit'] != '' ? floatval($ep['FreeLimit']) : $r['free_limit'];
                    }
                } 
            }

            if($settings['billing_settings']['billing_period'] != 'month')
            {
                $settings['billing_settings']['billing_period'] = intval($settings['billing_settings']['billing_period']) > 0 ? intval($settings['billing_settings']['billing_period']) : 1;
            }

            $settings['billing_settings']['billing_duedate'] = intval($settings['billing_settings']['billing_duedate']) >= 0 ? intval($settings['billing_settings']['billing_duedate']) : 7;

            $settings['billing_settings']['credit_billing'] = $settings['credit_billing']; 

            $q = mysql_safequery('REPLACE INTO StormBilling_settings SET 
                product_id = ?,
                enable = ?,
                module = ?,
                billing_settings = ?,
                resource_settings = ?,
                module_configuration = ?', 
                array
                (
                    $this->product_id,
                    (int)$settings['enable'],
                    $settings['module'] ? $settings['module'] : $this->settings['module'],
                    serialize($settings['billing_settings']),
                    serialize($settings['resource_settings']),
                    serialize($settings['module_configuration']),
                    serialize($settings['credit_billing'])
                )
                );
            
            if($this->loadModule($settings['module']))
            {
                $this->_module->install();
                $this->_module->upgrade();
            }
        }

        public function getModuleConfiguration()
        {

        }

        public function saveModuleConfiguration()
        {

        }

        public function getResources()
        {
            return $this->settings['resource_settings'];
        }

        public function getServerType()
        {
            return $this->server_type;
        }

        public function module()
        {
            return $this->_module;
        }

        public function getBillingSettings()
        {
            return $this->settings['billing_settings'];
        }

        public function hasDedicatedModule()
        {
            return $this->dedicated_module;
        }

        public function getModuleDescription()
        {
            if($this->_module)
            { 
                $name = $this->class_name;
                return constant($name.'::description');
            }

            return false;
        }

        public function getUsedModule()
        {
            return $this->used_module;
        }

        public function getConfiguration()
        {
            if($this->_module)
            {
                return $this->_module->getConfiguration();
            }

            return false;
        }

        /**
         * Is supported by StormBilling?
         * @return type
         */
        public function isSupported()
        {
            return $this->isSupported;
        }
         
        private function loadModule($module_name)
        {
            //Is module file exists?
            $file = ROOTDIR.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'addons'.DIRECTORY_SEPARATOR.'StormBilling'.DIRECTORY_SEPARATOR.'submodules'.DS.'class.'.$module_name.'_resources.php';
            if(!file_exists($file))
            {
                $this->isSupported  =   false;
                return false;
            }
            //Load file
            require_once $file;
            //Is class exists?
            $classname = $module_name.'_resources';
            if(!class_exists($classname))
            {
                StormBillingLogger::critical("Class ".$classname." not exists");
                $this->isSupported  =   false;
                return false;
            }
            //Create Class
            $this->_module = new $classname($this->product_id);
            
            //Set Class name
            $this->class_name = $classname;
            
            return true;
        }
    }
}