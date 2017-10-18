<?php
/**********************************************************************
 *  StormBilling (11.12.12)
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
class StormBilling
{
    //Module Name
    public $name = 'Liquid Web Cloud Servers Billing';

    //System Name
    public $system_name = 'Liquid Web Cloud Servers Billing';

    //Module Description
    public $description = 'This module gives you possibility to bill your clients for usage of specific resources in the accounts.<br />';

    //Module Version
    public $version = STORM_SERVERS_BILLING_VERSION;

    //Module Author
    public $author = '<a href="http://www.liquidweb.com/partner-programs/reseller-hosting/" targer="_blank">Liquid Web</a>';

    //Default Page
    public $default_page = 'configuration';

    //LiquidWeb report ink
    public $liquidwebReportApi = "https://whmcspluginstats.liquidweb.com:443/liquidwebwhmcsplugin/Webservice";

    //Top Menu
    public $top_menu =  array
    (
        'configuration'         =>  array
        (
            'title'             =>  'Configuration',
            'icon'              =>  'magic',
            'submenu'           =>  array
            (
                'edit'          =>  array
                (
                    'title'     =>  'Edit Product',
                    'show'      =>  false
                ),
            )
        ),
        'items'                 =>  array
        (
            'icon'              =>  'th-list',
            'title'             =>  'Items'
        ),
        'logs'                  =>  array
        (
            'title'             =>  'Logs',
            'icon'              =>  'adjust',
        ),
        'invoices'              =>  array
        (
            'title'             =>  'Awaiting Invoices',
            'icon'              =>  'book',
            'submenu'           =>  array
            (
                'show'          =>  array
                (
                    'title'     =>  'Show Invoice',
                    'show'      =>  false
                )
            )
        ),
        'credits'           =>  array
        (
            'title'             =>  'User Credits',
            'icon'              =>  'money',
        ),
        'integration'           =>  array
        (
            'title'             =>  'Integration Code',
            'icon'              =>  'edit',
        ),
        /*
        'migration'           =>  array
        (
            'title'             =>  'Migration Tool',
            'icon'              =>  'share-alt',
            'submenu'           =>  array
            (
                'product'          =>  array
                (
                    'title'     =>  'Product Migration',
                    'show'      =>  false
                )
            )
        ),
        */
    );

    //Side Menu
    public $side_menu = array
    (
        'configuration'         =>  array
        (
            'title'             =>  'Configuration',
            'icon'              =>  'magic',
        ),
        'items'                 =>  array
        (
            'icon'              =>  'th-list',
            'title'             =>  'Items'
        ),
        'logs'                  =>  array
        (
            'title'             =>  'Logs',
            'icon'              =>  'adjust',
        ),
        'invoices'              =>  array
        (
            'title'             =>  'Awaiting Invoices',
            'icon'              =>  'book'
        ),
        'integration'           =>  array
        (
            'title'             =>  'Integration Code',
            'icon'              =>  'edit',
        )
    );

    //Enable PHP Debug Info
    public $debug = 0;

    //Enable Logger
    public $logger = 1;

    /**
     * This function is call when administrator will activate your module
     */
    public function activate()
    {
        mysql_safequery("CREATE TABLE IF NOT EXISTS `StormBilling_settings`
            (
                `product_id`            INT(11) NOT NULL,
                `enable`                INT(1) NOT NULL,
                `module`                VARCHAR(255),
                `billing_settings`      TEXT NOT NULL,
                `resource_settings`     TEXT NOT NULL,
                `module_configuration`  TEXT NOT NULL,
                UNIQUE KEY(`product_id`)
            ) ENGINE = MyISAM") or die(mysql_error());

        mysql_safequery("CREATE TABLE IF NOT EXISTS `StormBilling_resources_settings`
            (
                `product_id` varchar(255) NOT NULL,
                `resources` blob NOT NULL,
                PRIMARY KEY (`product_id`)
            ) ENGINE=MyISAM") or die(mysql_error());

        mysql_safequery("CREATE TABLE IF NOT EXISTS `StormBilling_updates`
            (
                `hosting_id`    INT(11) NOT NULL,
                `rel_id`        VARCHAR(128) DEFAULT '0',
                `timestamp`     DATETIME NOT NULL,
                 KEY(`hosting_id`),
                 KEY(`rel_id`)
            ) ENGINE = MyISAM") or die(mysql_error());

        mysql_safequery("CREATE TABLE IF NOT EXISTS `StormBilling_submodules_data`
            (
                `hosting_id`    INT (11) NOT NULL,
                `data`          BLOB,
                UNIQUE KEY(`hosting_id`)
            ) ENGINE = MyISAM") or die(mysql_error());

        mysql_safequery("CREATE TABLE IF NOT EXISTS `StormBilling_user_credits`
            (
                `hosting_id`    INT (11) NOT NULL,
                `user_id`       INT (11) NOT NULL,
                `credit`        DECIMAL (20,10),
                `paid`          DECIMAL (20,10),
                `warned`        INT (1),
                KEY(`user_id`),
                UNIQUE KEY(`hosting_id`)
            )ENGINE = MyISAM");

        //Faktury oczekujace na potwierdzenie
        mysql_safequery("CREATE TABLE IF NOT EXISTS `StormBilling_awaiting_invoices`
            (
                `id`            INT(11) NOT NULL AUTO_INCREMENT,
                `userid`        INT(11) NOT NULL,
                `hostingid`     INT(11) NOT NULL,
                `date`          DATE NOT NULL,
                `duedate`       DATE NOT NULL,
                `items`         BLOB NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE = MyISAM") or die(mysql_error());

        mysql_safequery("CREATE TABLE IF NOT EXISTS `StormBilling_autosuspend`
            (
                `hosting_id` INT(11) NOT NULL,
                `invoice_id` INT(11) NOT NULL,
                `suspended`  INT(1),
                UNIQUE KEY(`hosting_id`),
                KEY(`invoice_id`)
            ) ENGINE = MyISAM") or die(mysql_error());

        mysql_safequery("CREATE TABLE IF NOT EXISTS `StormBilling_hosting_details`
            (
                `hosting_id`    INT(11) NOT NULL,
                `invoice_date`  DATETIME,
                UNIQUE KEY(`hosting_id`),
                KEY(`invoice_date`)
            ) ENGINE = MyISAM") or die(mysql_error());

        mysql_safequery("CREATE TABLE IF NOT EXISTS `StormBilling_billed_hostings`
        (
            `hosting_id`    INT(11) NOT NULL,
            `date`          DATETIME NOT NULL,
             UNIQUE KEY(`hosting_id`)
        ) ENGINE = MyISAM") or die(mysql_error());

        //Add Email Template
        $row = mysql_get_row("SELECT id FROM tblemailtemplates WHERE name = ?", array('Credit Warning'));
        if(!$row)
        {
            mysql_safequery("INSERT INTO tblemailtemplates (type, name, subject, message) VALUES (?, ?, ?,?)", array(
                'general',
                'Credit Warning',
                'Credit Warning',
                'Hello {$client_name}
                 <br />
                 <br />
                 Your credit balance has gone below {$minimal_credit} USD.
                 <br />
                 Please add some credits or your account will be suspended.
                 '
            )) or die(mysql_error());
        }


        //ver 1.2.0
        mysql_safequery("CREATE TABLE `StormBilling_customconfig` (
    						`id` INT(11) NOT NULL AUTO_INCREMENT,
                        	`config_name` VARCHAR(50) NULL DEFAULT NULL,
                        	`config_value` VARCHAR(50) NULL DEFAULT NULL,
                        	PRIMARY KEY (`id`),
                        	UNIQUE INDEX `config_name` (`config_name`)
                        );");

        mysql_safequery("DELETE FROM `StormBilling_customconfig` WHERE `config_name`='log_api_calls'");
        mysql_safequery("insert into StormBilling_customconfig (config_name,config_value)
        						values ('log_api_calls', 'YES')

								ON DUPLICATE KEY UPDATE config_value='YES'");
        mysql_safequery("DELETE FROM `StormBilling_customconfig` WHERE `config_name`='log_api_errors'");
        mysql_safequery("insert into StormBilling_customconfig (config_name,config_value)
        						values ('log_api_errors', 'YES')
								ON DUPLICATE KEY UPDATE config_value='YES'");

        mysql_safequery("DELETE FROM `StormBilling_customconfig` WHERE `config_name`='wiz_pg_4_hide_from_tmplt_list'");
        mysql_safequery("insert into StormBilling_customconfig (config_name,config_value)
        						values ('wiz_pg_4_hide_from_tmplt_list', '152')
								ON DUPLICATE KEY UPDATE config_value='152'");

        //default config options
        mysql_safequery("CREATE TABLE IF NOT EXISTS `mg_LiquidWeb_def_config_options` (
                        	`id` INT(11) NOT NULL AUTO_INCREMENT,
                        	`prod_type` VARCHAR(50) NULL DEFAULT NULL,
                        	`field_name` VARCHAR(200) NOT NULL,
                        	`field_value` VARCHAR(200) NOT NULL,
							PRIMARY KEY (`id`)
						)
						ENGINE=MyISAM");

        mysql_safequery("DELETE FROM `mg_LiquidWeb_def_config_options` WHERE `prod_type` in ('LiquidWeb','LiquidWebPrivatePare')");
        mysql_safequery("INSERT INTO `mg_LiquidWeb_def_config_options` (`prod_type`, `field_name`, `field_value`)
                            VALUES ('LiquidWeb', 'name', 'Liquid Web VPS'),
                             ('LiquidWeb', 'description', 'High Performance Fully Managed VPS'),
                             ('LiquidWeb', 'os_template', 'UBUNTU_1404_COREMANAGED'),
                             ('LiquidWeb', 'zone', '27'),
                             ('LiquidWeb', 'vps_type', '527'),
                             ('LiquidWeb', 'backup_enabled', 'on'),
                             ('LiquidWeb', 'backup_plan', 'quota'),
                             ('LiquidWeb', 'backup_quota', '1000'),
                             ('LiquidWeb', 'ips', '1'),
                             ('LiquidWeb', 'monitoring', 'on'),
                             ('LiquidWeb', 'firewall', 'on'),
                             ('LiquidWeb', 'ips_management', 'on'),
                             ('LiquidWeb', 'max_ips_number', '20'),
                             ('LiquidWeb', 'bandwidth_quota', '5000'),
                             ('LiquidWebPrivatePare', 'name', 'Liquid Web PP'),
                             ('LiquidWebPrivatePare', 'description', ''),
                             ('LiquidWebPrivatePare', 'os_template', 'UBUNTU_1404_COREMANAGED'),
                             ('LiquidWebPrivatePare', 'memory', '1024'),
                             ('LiquidWebPrivatePare', 'disk_space', '15'),
                             ('LiquidWebPrivatePare', 'virtual_cpu', '1'),
                             ('LiquidWebPrivatePare', 'backup_plan', '0'),
                             ('LiquidWebPrivatePare', 'backup_quota', '0'),
                             ('LiquidWebPrivatePare', 'bandwidth_quota', '5000'),
                             ('LiquidWebPrivatePare', 'monitoring', 'off'),
                             ('LiquidWebPrivatePare', 'firewall', 'off'),
                             ('LiquidWebPrivatePare', 'ips_management', 'off')");

        mysql_safequery("CREATE TABLE `mg_LiquidWebSSDVPSProduct` (
							`setting` VARCHAR(100) NOT NULL,
							`product_id` INT(10) UNSIGNED NOT NULL,
							`value` VARCHAR(250) NOT NULL,
								PRIMARY KEY (`setting`, `product_id`)
                         )
                         ENGINE=InnoDB;");
        //ver 1.2.0 //


        //teoretycznie tworzy katalog z logami dla crona
        $dir = dirname(__FILE__);
        if(!is_dir($dir.DS.'cron'.DS.'logs'))
        {
            //try to create dir
            mkdir($dir.DS.'cron'.DS.'logs', 0644);
        }

        //update liquidweb report
        global $CONFIG;
        $data = array(
                        "server_ip" => $_SERVER['SERVER_ADDR'],
                        "server_name" => $_SERVER['SERVER_NAME'],
                        "activation_date" => date("Y-m-d"),
                        "module_version" => $this->version,
                        "whmcs_version" => $CONFIG['Version'],
                        "template" => $CONFIG['Template']
                    );
        $this->execLWCurl($this->liquidwebReportApi, $data, 'POST');
    }

    /**
     * Functions is called when administrator will deactivate your module
     */
    public function deactivate()
    {
        mysql_safequery("DROP TABLE StormBilling_settings");
        mysql_safequery("DROP TABLE StormBilling_resources_settings");
        mysql_safequery("DROP TABLE StormBilling_updates");
        mysql_safequery("DROP TABLE StormBilling_submodules_data");
        mysql_safequery("DROP TABLE StormBilling_awaiting_invoices");
        mysql_safequery("DROP TABLE StormBilling_user_credits");
        mysql_safequery("DROP TABLE StormBilling_autosuspend");
        mysql_safequery("DROP TABLE StormBilling_hosting_details");
        mysql_safequery("DROP TABLE StormBilling_billed_hostings");

        mysql_safequery("DROP TABLE `mg_LiquidWeb_def_config_options`");
        mysql_safequery("DROP TABLE `StormBilling_customconfig`");


        //Uninstall submodules databases
        $this->uninstallSubmodules();

        //update liquidweb report
        $data = array(
                        "update" => "deactivation",
                        "server_ip" => $_SERVER['SERVER_ADDR'],
                        "server_name" => $_SERVER['SERVER_NAME'],
                        "deactivation_date" => date("Y-m-d")
                    );
        $this->execLWCurl($this->liquidwebReportApi, $data, 'PUT');
    }

    public function upgrade($version)
    {
        $version = (int)str_replace('.','', $version);
        if($version < 100)
        {
            $version *= 10;
        }

        //From Version 2.0.0
        if($version < 120)
        {
            mysql_safequery("ALTER TABLE `StormBilling_updates` ADD COLUMN `rel_id` VARCHAR(128) DEFAULT '0'");
            mysql_safequery("ALTER TABLE `StormBilling_updates` DROP INDEX hosting_id");
            mysql_safequery("ALTER TABLE `StormBilling_updates` ADD INDEX `hosting_id`(`hosting_id`)");
            mysql_safequery("ALTER TABLE `StormBilling_updates` ADD INDEX `rel_id`(`rel_id`)");
        }

        if($version < 120)
        {
            $this->installSubmodules();
            $this->upgradeSubmodules();
        }

        if($version < 120)
        {
            mysql_safequery("ALTER TABLE `StormBilling_autosuspend` ADD COLUMN `email_sent` DATETIME");
        }

        if($version < 121)
        {
            mysql_safequery("CREATE TABLE `StormBilling_customconfig` (
        						`id` INT(11) NOT NULL AUTO_INCREMENT,
                            	`config_name` VARCHAR(50) NULL DEFAULT NULL,
                            	`config_value` VARCHAR(50) NULL DEFAULT NULL,
                            	PRIMARY KEY (`id`),
                            	UNIQUE INDEX `config_name` (`config_name`)
                            );");

            mysql_safequery("DELETE FROM `StormBilling_customconfig` WHERE `config_name`='log_api_calls'");
            mysql_safequery("insert into StormBilling_customconfig (config_name,config_value)
            						values ('log_api_calls', 'YES')

									ON DUPLICATE KEY UPDATE config_value='YES'");
            mysql_safequery("DELETE FROM `StormBilling_customconfig` WHERE `config_name`='log_api_errors'");
            mysql_safequery("insert into StormBilling_customconfig (config_name,config_value)
            						values ('log_api_errors', 'YES')
									ON DUPLICATE KEY UPDATE config_value='YES'");

            mysql_safequery("DELETE FROM `StormBilling_customconfig` WHERE `config_name`='wiz_pg_4_hide_from_tmplt_list'");
            mysql_safequery("insert into StormBilling_customconfig (config_name,config_value)
            						values ('wiz_pg_4_hide_from_tmplt_list', '152')
									ON DUPLICATE KEY UPDATE config_value='152'");

            //default config options
            mysql_safequery("CREATE TABLE IF NOT EXISTS `mg_LiquidWeb_def_config_options` (
                            	`id` INT(11) NOT NULL AUTO_INCREMENT,
                            	`prod_type` VARCHAR(50) NULL DEFAULT NULL,
                            	`field_name` VARCHAR(200) NOT NULL,
                            	`field_value` VARCHAR(200) NOT NULL,
								PRIMARY KEY (`id`)
							)
							ENGINE=MyISAM");

            mysql_safequery("DELETE FROM `mg_LiquidWeb_def_config_options` WHERE `prod_type` in ('LiquidWeb','LiquidWebPrivatePare')");
            mysql_safequery("INSERT INTO `mg_LiquidWeb_def_config_options` (`prod_type`, `field_name`, `field_value`)
                            VALUES ('LiquidWeb', 'name', 'Liquid Web VPS'),
                             ('LiquidWeb', 'description', 'High Performance Fully Managed VPS'),
                             ('LiquidWeb', 'os_template', 'UBUNTU_1404_COREMANAGED'),
                             ('LiquidWeb', 'zone', '27'),
                             ('LiquidWeb', 'vps_type', '527'),
                             ('LiquidWeb', 'backup_enabled', 'on'),
                             ('LiquidWeb', 'backup_plan', 'quota'),
                             ('LiquidWeb', 'backup_quota', '1000'),
                             ('LiquidWeb', 'ips', '1'),
                             ('LiquidWeb', 'monitoring', 'on'),
                             ('LiquidWeb', 'firewall', 'on'),
                             ('LiquidWeb', 'ips_management', 'on'),
                             ('LiquidWeb', 'max_ips_number', '20'),
                             ('LiquidWeb', 'bandwidth_quota', '5000'),
                             ('LiquidWebPrivatePare', 'name', 'Liquid Web PP'),
                             ('LiquidWebPrivatePare', 'description', ''),
                             ('LiquidWebPrivatePare', 'os_template', 'UBUNTU_1404_COREMANAGED'),
                             ('LiquidWebPrivatePare', 'memory', '1024'),
                             ('LiquidWebPrivatePare', 'disk_space', '15'),
                             ('LiquidWebPrivatePare', 'virtual_cpu', '1'),
                             ('LiquidWebPrivatePare', 'backup_plan', '0'),
                             ('LiquidWebPrivatePare', 'backup_quota', '0'),
                             ('LiquidWebPrivatePare', 'bandwidth_quota', '5000'),
                             ('LiquidWebPrivatePare', 'monitoring', 'off'),
                             ('LiquidWebPrivatePare', 'firewall', 'off'),
                             ('LiquidWebPrivatePare', 'ips_management', 'off')");

            mysql_safequery("CREATE TABLE `mg_LiquidWebSSDVPSProduct` (
								`setting` VARCHAR(100) NOT NULL,
								`product_id` INT(10) UNSIGNED NOT NULL,
								`value` VARCHAR(250) NOT NULL,
									PRIMARY KEY (`setting`, `product_id`)
                             )
                             ENGINE=InnoDB;");
        }
        if($version < 122)
        {
            $rows = mysql_get_array("SELECT product_id, setting, value FROM mg_LiquidWebPrivateParentProduct WHERE product_id in (SELECT id FROM tblproducts where servertype='LiquidWebPrivateParent')");
            foreach($rows  as $set) {
                $settings[$set['product_id']][$set['setting']] = $set['value'];
            }

            foreach($settings  as $id=>$data) {
                 $qry = "UPDATE tblproducts SET ";
                    $qry .= " configoption5='".$data['Parent']."'";
                    $qry .= ", configoption6='".$data['AvailableParents']."'";
                    $qry .= ", configoption8='".$data['Template']."'";
				    $qry .= ", configoption10='".$data['Memory']."'";
					$qry .= ", configoption11='".$data['Diskspace']."'";
					$qry .= ", configoption12='".$data['VCPU']."'";
					$qry .= ", configoption13='".$data['Backup Plan']."'";
					$qry .= ", configoption14='".$data['Backup Quota']."'";
					$qry .= ", configoption17='".$data['Maximum Number of IPs']."'";
					$qry .= ", configoption18='".$data['Bandwidth Quota']."'";
					$qry .= ", configoption19='".($data['Monitoring'] == '1' ? 'on' : '')."'";
					$qry .= ", configoption20='".($data['Firewall'] == '1' ? 'on' : '')."'";
					$qry .= ", configoption21='".($data['IPs Management'] == '1' ? 'on' : '')."'";
                    $qry .= " WHERE id=".$id;
                mysql_safequery($qry);
            }
        }


        /**
         * @warning This function will add or REMOVE columns from database.
         */
        //$this->upgradeSubmodules();


        //update liquidweb report
        global $CONFIG;
        $data = array(
                        "update" => "upgrade",
                        "server_ip" => $_SERVER['SERVER_ADDR'],
                        "server_name" => $_SERVER['SERVER_NAME'],
                        "last_update" => date("Y-m-d"),
                        "module_version" => $this->version,
                        "whmcs_version" => $CONFIG['Version'],
                        "template" => $CONFIG['Template']
        );
        $this->execLWCurl($this->liquidwebReportApi, $data, 'PUT');
    }

    private function installSubmodules()
    {
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'core.php';
        $submodules = StormBilling_getModules();
        foreach($submodules as $name)
        {
            $n = $name.'_resources';
            require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'submodules'.DIRECTORY_SEPARATOR.'class.'.$n.'.php';
            $m = new $n();
            $m->install();
        }
    }

    private function uninstallSubmodules()
    {
        require_once dirname(__FILE__).DS.'core.php';
        $submodules = StormBilling_getModules();
        foreach($submodules as $name)
        {
            $n = $name.'_resources';
            require_once dirname(__FILE__).DS.'submodules'.DS.'class.'.$n.'.php';
            $m = new $n(0);
            $m->uninstall();
        }
    }

    private function upgradeSubmodules()
    {
        require_once dirname(__FILE__).DS.'core.php';
        $submodules = StormBilling_getModules();
        foreach($submodules as $name)
        {
            $n = $name.'_resources';
            require_once dirname(__FILE__).DS.'submodules'.DS.'class.'.$n.'.php';
            $m = new $n(0);
            $m->upgrade();
        }
    }

    private function execLWCurl($url, $data, $method) {
        $data_json = json_encode($data);
        $ch = curl_init();
        $options = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                //CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                //CURLOPT_USERPWD => @$username . ":" . @$password,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array('Content-Type: application/json','Content-Length: ' . strlen($data_json)),
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => $data_json,
                CURLOPT_HEADER => true,
            );
        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        $responseBody = substr($result, $header_size);
        curl_close($ch);
    }
}
