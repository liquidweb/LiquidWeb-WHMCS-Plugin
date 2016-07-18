<?php

/**********************************************************************
 *  DirectVPS (2013-09-13)
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
 * @author Grzegorz Draganik <grzegorz@modulesgarden.com>
 * @author Mariusz Miodowski <mariusz@modulesgarden.com>
 */

if(!class_exists('StormOnDemand_Hosting'))
{
    class StormOnDemand_Hosting 
    {
        //Hosting ID
        public $id = 0;
        //Product ID
        protected $product_id = 0;
        
        //product Detaild
        public $product_details = array();
        //custom fields
        public $custom_fields = array();
        //configurable options
        public $configurable_options = array();
        //server configuration
        public $server_details = array();

        public function __construct($id)
        {
            $this->id = $id;
            
            //Load Hosting Params
            $this->load();
        }

        public function updateDetails(array $values)
        {
            $sets = array();
            foreach ($values as $k => $v)
            {
                $v = is_numeric($v) ? $v : '"'.mysql_real_escape_string($v).'"';
                $sets[] = $k.'='.$v;

            }

            return mysql_query('UPDATE tblhosting SET '.implode(',',$sets).' WHERE id = ' . (int)$this->id);
        }

        public function load()
        {
            //Get Hosting Details
            $q  = mysql_get_row("SELECT * FROM tblhosting WHERE id = ?", array($this->id));
            foreach($q as $key => &$val)
            {
                $this->hosting_details[$key]    =   $val;
            }
            $this->hosting_details['password']  =   decrypt($this->hosting_details['password']);

            //Set Product ID 
            $this->product_id = $this->hosting_details['packageid'];
            
            //Get Custom fields
            $q = mysql_get_array('SELECT cf.id, cf.fieldname, cfv.value
                        FROM tblcustomfields AS cf
                        JOIN tblcustomfieldsvalues AS cfv ON cfv.fieldid = cf.id
                        WHERE cf.type = "product" AND cfv.relid = '.(int)$this->id.'');
            
            foreach($q as $key  =>  &$val)
            {
                if(strpos($val['fieldname'], '|'))
                {
                    $this->custom_fields[substr($val['fieldname'], 0, strpos($val['fieldname'], '|'))]  =   $val['value'];
                }
                else
                {
                    $this->custom_fields[$val['fieldname']]  =   $val['value'];
                }
            }

            //Get Server Configuration
            if($this->hosting_details['server'])
            {
                $q = mysql_get_row("SELECT * FROM tblservers WHERE id = ?", array($this->hosting_details['server']));
                foreach($q as $key => &$val)
                {
                    $this->server_details[$key]    =   $val;
                }
            }
        }

        /********************************
         *          GETTERS
         *******************************/

        /**
         * Get Hosting Details
         * @param type $key
         * @return boolean
         */
        public function getDetails($key = null)
        {
            if(isset($this->hosting_details[$key]))
            {
                return $this->hosting_details[$key];
            }

            return false;
        }

        /**
         * Get Custom Field
         * @param type $key
         * @return boolean
         */
        public function getCustomField($key)
        {
            if(isset($this->custom_fields[$key]))
            {
                return $this->custom_fields[$key];
            }   

            return false;
        }
        
        public function createCustomField($field_name, $friendly_name, $type, $value, $more = array())
        {      

            $q  = mysql_safequery("SELECT id
                FROM tblcustomfields
                WHERE
                    relid = ".($this->product_id)." 
                    AND type = 'product'
                    AND (fieldname = '".$field_name."' OR fieldname LIKE '".$field_name."|%') ");
            
            //Our field ID!
            $field_id = 0;
            

            if(!mysql_num_rows($q))
            {
                switch($type)
                {
                    case 'text':
                        mysql_safequery('INSERT INTO tblcustomfields(type,relid,fieldname,fieldtype,description,fieldoptions,regexpr,adminonly,required,showorder,showinvoice,sortorder)
                            VALUES("product", ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                                $this->product_id,
                                $field_name.'|'.$friendly_name,
                                "text",
                                $more['description'] ? $more['description'] : '',
                                $more['fieldoptions'] ? $more['fieldoptions'] : '',
                                $more['regexpr'] ? $more['regexpr'] : '',
                                $more['adminonly'] ? 'on' : '',
                                $more['required']  ? 'on' : '',
                                $more['showorder'] ? 'on' : '',
                                $more['showinvoice'] ? 'on' : '',
                                $more['sortorder']   ?' on' : ''
                            ));
                        break;
                    default:
                        die('Unsupported type!');
                }
                
                $field_id = mysql_insert_id();
            }
            else
            {
                $row        =   mysql_fetch_assoc($q);
                $field_id   =   $row['id'];
            }
  
            $q = mysql_safequery("SELECT fieldid FROM tblcustomfieldsvalues WHERE fieldid = ? AND relid = ?", array(
                $field_id,
                $this->id
            ));
            
            if(!mysql_num_rows($q))
            {
                mysql_safequery("INSERT INTO tblcustomfieldsvalues (`fieldid`, `relid`, `value`) VALUES(?, ?, ?)", array(
                    $field_id,
                    $this->id,
                    $value
                ));
            }
        }


        /*******************************
         *           SETTERS
         ******************************/

        public function setDetails($setting, $value)
        {

        }

        /**
         * Set Custom Field
         * @param type $fieldname
         * @param type $value
         */
        public function setCustomField($fieldname, $value)
        {
            $q = mysql_safequery("UPDATE tblcustomfields f
                LEFT JOIN tblcustomfieldsvalues v ON f.id = v.fieldid
                SET v.value = ?
                WHERE v.relid = ? AND (f.fieldname = ? OR f.fieldname LIKE ?)", array(
                    $value,
                    $this->id,
                    $fieldname,
                    $fieldname.'|%'
                ));
        }

            public static function getFirstAndLastName($str, $first = true){
                    $pos = strpos($str, '|');
                    if ($pos){
                            return $first ? substr($str, 0, $pos) : substr($str, $pos);
                    } else {
                            return $str;
                    }
            }
    }
}