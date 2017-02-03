<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright (c) ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2013-06-18, 11:19:35)
 * 
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
 * ******************************************************************** */

/**
 * @author Grzegorz Draganik <grzegorz@modulesgarden.com>
 * @modified Mariusz Miodowski <mariusz@modulesgarden.com>
 */

if(!class_exists('StormOnDemand_Product'))
{
    class StormOnDemand_Product
    {	
        //Product ID
        public $id;
        //Product Configuration. Should be created in child
        public $defaultConfig   = array();
        //Default table
        protected $_tableName   = '';

        protected $_config      = null;

        public function __construct($id, array $params = array())
        {
            //Get child name
            $child              =   get_class($this);

            //Set up default table name
            $this->_tableName   =   'mg_'.$child;

            //Copy Params
            foreach ($params as $k => $v)
            {
                    $this->$k = $v;
            }
            $this->id = (int)$id;
        }

        public function runAutoConfiguration()
        {
            
            //Generate Custom Fields
            if($_REQUEST['modaction']  ==  'generate_custom_fields')
            {
                ob_clean();
                $ret = $this->generateDefaultCustomField();
                $json = array();
                if($ret)
                {
                    $json['status']     =   1;
                    $json['message']    =   'Custom Fields Generated<br/><br/>Click <b><a href="configproducts.php?action=edit&id='.$_REQUEST['id'].'&tab=4">here</a></b> to check Custom Fields';
                }
                else
                {
                    $json['status']     =   0;
                    $json['message']    =   'Custom Fields Already Generated<br/><br/>Click <b><a href="configproducts.php?action=edit&id='.$_REQUEST['id'].'&tab=4">here</a></b> to check Custom Fields';
                }

                echo json_encode($json);
                die();
            }
            //Generate Configurable Options
            elseif($_REQUEST['modaction']  ==  'generate_configurable_options')
            {
                ob_clean();
                $ret = $this->generateDefaultConfigurableOptions();

                $q = mysql_safequery('SELECT * FROM tblproductconfiglinks WHERE pid = ?', array($_REQUEST['id']));
                $row = mysql_fetch_assoc($q);

                $json = array();
                if($ret)
                {
                    $json['status']     =   1;
                    //$json['message']    =   'Configurable Options Generated<br/><br/>Click <b><a href="configproducts.php?action=edit&id='.$_REQUEST['id'].'&tab=5">here</a></b> to check Configurable options';
                    $json['message']    =   'Configurable Options Generated<br/><br/>Click <b><a href="configproductoptions.php?action=managegroup&id='.$row['gid'].'">here</a></b> to check Configurable options';


                }
                else
                {
                    $json['status']     =   0;
                    //$json['message']    =   'Configurable Options Already Generated<br/><br/>Click <b><a href="configproducts.php?action=edit&id='.$_REQUEST['id'].'&tab=5">here</a></b> to check Configurable options';
                    $json['message']    =   'Configurable Options Already Generated<br/><br/>Click <b><a href="configproductoptions.php?action=managegroup&id='.$row['gid'].'">here</a></b> to check Configurable options';
                }

                echo json_encode($json);
                die();
            }
        }
        /**
         *  Save Product Configuration
         * @param type $customconfigoption
         */
        public function saveConfigOptions($customconfigoption)
        {
            $this->clearConfig();

            foreach ($customconfigoption as $k => $v)
            {
                $this->saveConfig($k, $v);
            }
        }

        /**
         *  Generate Default Custom Fieds
         * @return boolean
         */
        public function generateDefaultCustomField()
        {
            $count_added = 0;
            foreach($this->defaultCustomField as $key => $field)
            {
                $q  = mysql_safequery("SELECT id, relid, fieldname
                            FROM tblcustomfields
                            WHERE
                                    relid = ".$this->id."
                                    AND type = 'product'
                                    AND (fieldname = '".$key."' OR fieldname LIKE '".$key."|%') ");

                if(mysql_num_rows($q))
                {
                    continue;
                }

                switch($field['type'])
                {
                    case 'text':
                        mysql_safequery('INSERT INTO tblcustomfields(type,relid,fieldname,fieldtype,description,fieldoptions,regexpr,adminonly,required,showorder,showinvoice,sortorder)
                            VALUES("product", ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                                $this->id,
                                $key.'|'.$field['title'],
                                "text",
                                $field['description'] ? $field['description'] : '',
                                $field['fieldoptions'] ? $field['fieldoptions'] : '',
                                $field['regexpr'] ? $field['regexpr'] : '',
                                $field['adminonly'] ? 'on' : '',
                                $field['required']  ? 'on' : '',
                                $field['showorder'] ? 'on' : '',
                                $field['showinvoice'] ? 'on' : '',
                                $field['sortorder']   ?' on' : ''
                            ));
                            $count_added++;
                        break;
                    default:
                        die('Unsupported type!');
                }
            }
            if($count_added == 0){
                return false;
            }else{
                return true;
            }

        }

        /**
         * Generate Default Configurable Options
         */
        public function generateDefaultConfigurableOptions() {
            if ($this->hasConfigurableOptions()) {
                    return false;
            }

            foreach ($this->defaultConfigurableOptions as $group) {
                //Create Group

                //$q = mysql_safequery('SELECT name FROM tblproducts WHERE id = ?', array($this->id));
                //$row = mysql_fetch_assoc($q);

                mysql_safequery('INSERT INTO tblproductconfiggroups(name,description) VALUES(?, ?)', array(
                        $group['title'], // . ' for ' . $row['name'],
                        $group['description']
                ));
                $group_id = mysql_insert_id();

                //Assign to product
                mysql_safequery('INSERT INTO tblproductconfiglinks(gid,pid) VALUES(?,?)', array($group_id, $this->id));

                foreach ($group['fields'] as $field_key => $field) {
                    switch ($field['type']){
                        case 'select': case 1: case 'dropdown':
                            $field_type = 1; break;
                        case 'radio': case 2:
                            $field_type = 2; break;
                        case 'yesno': case 3:
                            $field_type = 3; break;
                        case 'quantity': case 4:
                            $field_type = 4; break;
                        default:
                            continue;
                    }

                    mysql_safequery('INSERT INTO tblproductconfigoptions(gid,optionname,optiontype,qtyminimum,qtymaximum,`order`,hidden) VALUES(?,?,?,?,?,0,0)', array(
                            $group_id,
                            $field_key . '|' . $field['title'],
                            $field_type,
                            isset($field['qtyminimum']) ? (int)$field['qtyminimum'] : 0,
                            isset($field['qtymaximum']) ? (int)$field['qtymaximum'] : 0,
                    ));

                    $config_id = mysql_insert_id();

                    //Insert options
                    foreach ($field['options'] as $option) {
                        mysql_safequery("INSERT INTO tblproductconfigoptionssub(configid,optionname,sortorder,hidden) VALUES(?,?,0,0)", array(
                            $config_id,
                            $option['value'] . '|' . $option['title'],
                            isset($field['sortorder']) ? (int)$field['sortorder'] : 0,
                            isset($field['hidden']) ? 'on' : '',
                        ));
                        $suboption_id = mysql_insert_id();

                        if (isset($field['options']['pricing'])) {
                            foreach ($field['options']['pricing'] as $currency_id => $values){
                                mysql_safequery('INSERT INTO `tblpricing` (`type`,`currency`,`relid`,`msetupfee`,`qsetupfee`,`ssetupfee`,`asetupfee`,`bsetupfee`,`tsetupfee`,`monthly`,`quarterly`,`semiannually`,`annually`,`biennially`,`triennially`) VALUES("configoptions",?,?,?,?,?,?,?,?,?,?,?,?,?,?)', array(
                                    $currency_id,
                                    $suboption_id,
                                    isset($values['msetupfee'])		? (float)$values['msetupfee']	: 0,
                                    isset($values['qsetupfee'])		? (float)$values['qsetupfee']	: 0,
                                    isset($values['ssetupfee'])		? (float)$values['ssetupfee']	: 0,
                                    isset($values['asetupfee'])		? (float)$values['asetupfee']	: 0,
                                    isset($values['bsetupfee'])		? (float)$values['bsetupfee']	: 0,
                                    isset($values['tsetupfee'])		? (float)$values['tsetupfee']	: 0,
                                    isset($values['monthly'])		? (float)$values['monthly']		: 0,
                                    isset($values['quarterly'])		? (float)$values['quarterly']	: 0,
                                    isset($values['semiannually'])	? (float)$values['semiannually']: 0,
                                    isset($values['annually'])		? (float)$values['annually']	: 0,
                                    isset($values['biennially'])	? (float)$values['biennially']	: 0,
                                    isset($values['triennially'])	? (float)$values['triennially'] : 0,
                                ));
                            }
                        } else {
                            $currencies = mysql_safequery("SELECT id FROM tblcurrencies");
                            while($currency = mysql_fetch_assoc($currencies))
                            {
                                mysql_safequery("INSERT INTO `tblpricing`
                                ( `type` , `currency` , `relid` , `msetupfee` , `qsetupfee` , `ssetupfee` , `asetupfee` , `bsetupfee` , `tsetupfee` , `monthly` , `quarterly` , `semiannually` , `annually` , `biennially` , `triennially`)
                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array("configoptions", $currency['id'], $suboption_id,"0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00"));
                            }
                        }
                    }
                }
            }
            return true;
        }

        /**
         * Load Product Configuration
         * @throws Exception
         */
        public function load()
        {
            $q = mysql_safequery('SELECT * FROM tblproducts WHERE id = ' . (int)$this->id);
            $row = mysql_fetch_assoc($q);
            if (!empty($row))
            {
                foreach ($row as $k => $v)
                {
                    $this->$k = $v;
                }
            }
            else
            {
                throw new Exception('No product to load');
            }
        }

        /**
         *  Load product configuration by service it
         * @param type $serviceid
         */
        public function setIdByServiceId($serviceid)
        {
            $q = mysql_safequery('SELECT packageid FROM tblhosting WHERE id = ' . (int)$serviceid);
            $row = mysql_fetch_assoc($q);
            $this->id = (int)$row['packageid'];
        }

        /**
         * Update product details
         * @param array $values
         * @return type
         */
        public function update(array $values)
        {
            $sets = array();
            foreach ($values as $k => $v)
            {
                    $v = is_numeric($v) ? $v : '"'.mysql_real_escape_string($v).'"';
                    $sets[] = $k.'='.$v;
            }
            return mysql_safequery('UPDATE tblproducts SET '.implode(',',$sets).' WHERE id = ' . (int)$this->id);
        }

        /**
         * Has Configurable Options?
         * @return type
         */
        public function hasConfigurableOptions()
        {
                $q = mysql_safequery('SELECT * FROM tblproductconfiglinks WHERE pid = ?', array($this->id));
                return (bool)mysql_num_rows($q);
        }

        /**
         *
         * @return type
         */
        public function hasAssignedServerGroup()
        {
                $q = mysql_safequery('SELECT servergroup FROM tblproducts WHERE id = ?', array($this->id));
                $row = mysql_fetch_assoc($q);
                return isset($row['servergroup']) ? (int)$row['servergroup'] : false;
        }


        /**
         *
         * @return type
         */
        public function getParams()
        {
                $result = mysql_safequery("
                        SELECT
                                s.ipaddress AS serverip, s.hostname AS serverhostname, s.username AS serverusername, s.password AS serverpassword, s.secure AS serversecure,
                                configoption1,configoption2,configoption3,configoption4,configoption5,configoption6,configoption7,configoption8,configoption9
                        FROM tblservers AS s
                        JOIN tblservergroupsrel AS sgr ON sgr.serverid = s.id
                        JOIN tblservergroups AS sg ON sgr.groupid = sg.id
                        JOIN tblproducts AS p ON p.servergroup = sg.id
                        WHERE p.id = ?
                        ORDER BY s.active DESC
                        LIMIT 1",
                        array($this->id)
                );
                $row = mysql_fetch_assoc($result);
                // old whmcs
                if (!function_exists('decrypt') && file_exists(ROOTDIR . DS . 'includes' . DS . 'functions.php'))
                        include_once ROOTDIR . DS . 'includes' . DS . 'functions.php';
                        if(!empty($row['serverpassword']))
                            $row['serverpassword'] = decrypt($row['serverpassword']);
                return $row;
        }

                    // ========================================
                    // ============ CUSTOM CONFIG =============
                    // ========================================

                    public function getConfig($name)
                    {
                            $this->loadConfig();
                            return isset($this->_config[$name]) ? $this->_config[$name] : null;
                    }

                    public function issetConfig($name){
                            $this->loadConfig();
                            return isset($this->_config[$name]);
                    }

                    public function loadConfig(){
                            if ($this->_config !== null)
                                    return $this->_config;

                            $this->setupDbTable();
                            $q = mysql_safequery('SELECT * FROM '.$this->_tableName.' WHERE product_id = ' . (int)$this->id);
                            while ($row = mysql_fetch_assoc($q)){
                                    if(json_decode($row['value'])!== NULL)
                                        $row['value'] = json_decode ($row['value']);

                                    $this->_config[$row['setting']] = $row['value'];
                            }
                            return $this->_config;
                    }

                    public function saveConfig($name, $value){
                            $this->setupDbTable();
                            if(is_array($value))
                                $value = json_encode($value);
                            return mysql_safequery('INSERT INTO '.$this->_tableName.'(setting,product_id,value) VALUES(?,?,?) ON DUPLICATE KEY UPDATE value = ?', array(
                                    $name,
                                    (int)$this->id,
                                    ($value=='-- not specified --' ? '' : $value),
                                    $value
                            ));
                    }

                    public function clearConfig(){
                            return mysql_safequery('DELETE FROM '.$this->_tableName.' WHERE product_id = ' . (int)$this->id);
                    }

                    public function renderConfigOptions($scripts = ''){
                            $scripts .= '
                                    <style type="text/css">
                                    td.configoption_group {background-color:silver;font-weight:bold;text-align:left;}
                                    .fieldlabel.mg, .fieldarea.mg {width:25%;}
                                    .fielddescription {font-size: 10px;color: gray;display: inline;}
                                    </style>
                            ';

                            $str = '<div id="custom-dialog" title=""></div>';
                            $options = array();
                            $groups = array();
                            $i = 0;
                            //ob_clean();

                            foreach ($this->defaultConfig as $k => $config)
                            {
                                // group html
                                if (is_string($config))
                                {
                                        $groups[$i] = $config;
                                        continue;
                                }

                                if($k == 'generate_configurable_options')
                                {
                                    $options[] = '
                                            <td class="fieldlabel mg">Configurable Options</td>
                                            <td class="fieldarea mg">
                                                <a href="#" class="generate_configurable_options">Generate</a>
                                                <div class="fielddescription">These fields are used to configure the product. Your clients will be able to choose specific configuration options according to how to setup these option fields after they are generated. </div>
                                            </td>';

                                    $scripts    .=  '<script type="text/javascript">
                                                        jQuery(function(){
                                                           jQuery(".generate_configurable_options").click(function(event){
                                                                    event.preventDefault();
                                                                    $("#custom-dialog").attr("title", "Generating");
                                                                    $("#custom-dialog").html("<p style=\'text-align: center\'><img src=\'images/loading.gif\'></img><p>");
                                                                    $("#custom-dialog").dialog();
                                                                        jQuery.post(window.location.href, {"modaction":"generate_configurable_options", "productid":'.$this->id.'}, function(res){
                                                                        $("#custom-dialog").html("<p style=\'text-align: center\'>"+res.message+"<p>");
                                                                        if(res.status)
                                                                        {
                                                                            window.location.href = "configproducts.php?action=edit&id='.$this->id.'&tab=4";
                                                                        }
                                                                }, "json");
                                                            });
                                                        });
                                                     </script>';


                                }
                                elseif($k == 'genereate_custom_field')
                                {
                                    $options[] = '
                                            <td class="fieldlabel mg">Custom Field</td>
                                            <td class="fieldarea mg">
                                                <a href="#" class="generate_custom_fields">Generate</a>
                                                <div class="fielddescription">These fields are used by the module, please generate this before you offer this product to your clients.</div>
                                            </td>';

                                    $scripts    .=  '<script type="text/javascript">
                                                        jQuery(function(){
                                                           jQuery(".generate_custom_fields").click(function(event){
                                                                    event.preventDefault();
                                                                    $("#custom-dialog").attr("title", "Generating");
                                                                    $("#custom-dialog").html("<p style=\'text-align: center\'><img src=\'images/loading.gif\'></img><p>");
                                                                    $("#custom-dialog").dialog();
                                                                        jQuery.post(window.location.href, {"modaction":"generate_custom_fields", "productid":'.$this->id.'}, function(res){
                                                                        $("#custom-dialog").html("<p style=\'text-align: center\'>"+res.message+"<p>");
                                                                        if(res.status)
                                                                        {
                                                                            window.location.href = "configproducts.php?action=edit&id='.$this->id.'&tab=3";
                                                                        }
                                                                }, "json");
                                                            });
                                                        });
                                                     </script>';

                                }
                                else
                                {
                                    $options[] = '
                                            <td class="fieldlabel mg">'.$config['title'].'</td>
                                            <td class="fieldarea mg">
                                                    '.$this->renderConfigOptionInput(
                                                            $k,
                                                            $config['type'],
                                                            isset($config['default']) ? $config['default'] : '',
                                                            isset($config['options']) ? $config['options'] : array(),
                                                            isset($config['useOptionsKeys']) && $config['useOptionsKeys']
                                                    ). '
                                                    '.(isset($config['description']) ? '<div class="fielddescription">'.$config['description'].'</div>' : '').'
                                            </td>';
                                }
                                $i++;
                            }

                            $countFields = 0;
                            foreach ($options as $k => $option)
                            {
                                    if ($countFields == 0 && $k != 0)
                                            $str .= '<tr>';

                                    if (isset($groups[$k])){
                                            if ($countFields == 1)
                                                    $str .= '<td></td><td></td>';
                                            $str .= '</tr><tr><td colspan="4" class="configoption_group">'.$groups[$k].'</td></tr><tr>';
                                            $countFields = 0;
                                    }
                                    $str .= $option;

                                    $countFields++;
                                    if ($countFields == 2)
                                            $str .= '</tr>';
                                    if ($countFields > 1)
                                            $countFields = 0;
                            }
                            if ($countFields != 0)
                                    $str .= '</tr>';
                            return $scripts.$str;
                    }

                    public function renderConfigOptionInput($name, $type, $default, array $options = array(), $optionsValuesFromKeys = false){
                            $value = $this->getConfig($name) ? $this->getConfig($name) : ($this->issetConfig($name) ? '' : $default);
                            switch ($type){
                                    case 'multiselect':
                                            $str = '<select name="customconfigoption['.$name.'][]" multiple style="width:160px;">';
                                            foreach ($options as $k => $option){
                                                    $str .= '<option value="'.($optionsValuesFromKeys ? $k : $option).'" '.(is_array($value) && in_array(($optionsValuesFromKeys ? $k : $option),$value) ? 'selected' : '').'>'.$option.'</option>';
                                            }
                                            $str .= '</select>';
                                            return $str;

                                    case 'select':
                                            $str = '<select name="customconfigoption['.$name.']" style="width:160px;">';
                                            foreach ($options as $k => $option){
                                                    $str .= '<option value="'.($optionsValuesFromKeys ? $k : $option).'" '.($value == ($optionsValuesFromKeys ? $k : $option) ? 'selected' : '').'>'.$option.'</option>';
                                            }
                                            $str .= '</select>';
                                            return $str;

                                    case 'text':
                                            return '<input type="text" name="customconfigoption['.$name.']" style="width:150px;" value="'.$value.'" />';

                                    case 'password':
                                            return '<input type="password" name="customconfigoption['.$name.']" style="width:150px;" value="'.$value.'" />';

                                    case 'textarea':
                                            return '<textarea name="customconfigoption['.$name.']" style="width:100%">'.$value.'</textarea>';

                                    case 'radio':
                                            $str = '';
                                            foreach ($options as $option)
                                                    $str .= '<input type="radio" name="customconfigoption['.$name.']" value="'.$option.'" /> ' . $option;
                                            return $str;

                                    case 'checkbox':
                                        return '<input type="checkbox"  name="customconfigoption['.$name.']" value="1"  '.($value ? ' checked="checked" ' : '').' /> '.$option;

                                    case 'empty':
                                        return '';

                            }
                            // NO CHECKBOX
                            throw new Exception('Config Option type not supported');
                    }

                    public function setupDbTable()
                    {
                            return mysql_safequery('CREATE TABLE IF NOT EXISTS `'.$this->_tableName.'` (
                                    `setting` varchar(100) NOT NULL,
                                    `product_id` int(10) unsigned NOT NULL,
                                    `value` varchar(250) NOT NULL,
                                    PRIMARY KEY (`setting`,`product_id`)
                                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
                    }




    }
}

/**
@EXAMPLE HOOKS.php

add_hook("ProductEdit", 1, "directVPS_ProductEdit");

function directVPS_ProductEdit($params)
{
    if(strtolower($params['servertype']) == 'yourservertype' && $_REQUEST['customconfigoption'])
    {
        //Load StormOnDemand_Product
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'tools'.DS.'class.StormOnDemand_Product.php';
        //Load Product
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'core'.DS.'class.yourservertype.php';
        //Create VPS Product
        $conf   =   new DirectVPSProduct($params['pid']);
        $conf   ->  saveConfigOptions($_REQUEST['customconfigoption']);
    }
}
**/

