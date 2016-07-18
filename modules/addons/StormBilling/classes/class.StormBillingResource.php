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

if(!class_exists('StormBillingResource'))
{
    class StormBillingResource
    {   
        /**
         * Display types
         */
        //Usage records will be summarized
        const DISPLAY_SUMMARY           =   1;
        //Usage records will be summarized and divided by amount 
        const DISPLAY_AVERAGE           =   2;
        //Usage records will be summarized and divided per amount hours between first and last record
        const DISPLAY_HOURLY_SUM    =   3;
        
        /**
         * Calculation types
         */
        const CALCULATION_SUMMARY   =   1;
        const CALCULATION_HOURLY    =   2;
        
        //Current product ID
        protected $productId        =   null;
        
        //Custom start date for resource. This it timestamp!
        protected $recordStartDate  =   null;
        
        //Set to true if you want to show mulit resource on invoice
        protected   $showMulitResourcesOnInvoice    =   false;
        
        
        public function __construct($productId) 
        {
            $this->productId = $productId;
            
            $this->loadModuleConfiguration();
            $this->saveModuleConfiguration();
        }
        
        /**
         * Load  module configuration
         * @author Mariusz Miodowski <mariusz@modulesgarden.com>
         * @return boolean
         */
        protected function loadModuleConfiguration()
        {
            if(!$this->productId)
            {
                return false;
            }
            
            //Get configuration
            $settings = mysql_get_row('SELECT module_configuration FROM StormBilling_settings WHERE product_id = ?', array($this->product_id));
            if(!$settings)
            {
                return false;
            }
            
            //Set custom module configuration
            $moduleConfiguration = unserialize($settings['module_configuration']);
            if(!empty($moduleConfiguration))
            {
                foreach($moduleConfiguration as $confKey => $confVal)
                {
                    if(isset($this->configuration[$confKey]))
                    {
                        $this->configuration[$confKey]['Value']  =   $confVal;
                    }
                }
            }
            
            return true;
        }
        
        /**
         * Save module configuration. This function is using "Values" keys from "$this->configuration"
         * @author Mariusz Miodowski <mariusz@modulesgarden.com>
         * @return boolean
         */
        protected function saveModuleConfiguration()
        {
            if(!$this->productId)
            {
                return false;
            }
            
            if(!$this->configuration)
            {
                return false;
            }
            
            $configuration = array();
            if(!empty($this->configuration))
            {
                foreach($this->configuration as $confKey => $confVal)
                {
                    $configuration[$confKey]    =   $confVal['Value'];
                }
            }
            
            if(!$configuration)
            {
                return false;
            }
            
            mysql_safequery("UPDATE StormBilling_settings SET module_configuration = ? WHERE product_id = ?", array(
                serialize($configuration),
                $this->productId
            ));
            
            return (bool)mysql_affected_rows();
        }
        
        /**
         * This function is called in administrator area before displaying page. In thin function you can load extended pricing for your module
         */
        public function loadExtendedPricingConfiguration()
        {
            
        }
        
        //
        public function showMulitResourcesOnInvoice()
        {
            return $this->showMulitResourcesOnInvoice;
        }
    }
}
