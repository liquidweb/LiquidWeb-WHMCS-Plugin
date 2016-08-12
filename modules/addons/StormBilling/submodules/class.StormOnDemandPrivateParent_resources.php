<?php

/**********************************************************************
 *  StormBilling Trunk (2013-12-05)
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


require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandMonitoringBandwidth.php';
require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandMonitoringLoad.php';

if(!class_exists('StormOnDemandPrivateParent_resources'))
{
    class StormOnDemandPrivateParent_resources extends SBResource
    {
        //module name
        const name = 'Storm On Demand Private Cloud';

        //module description
        const description = '';

        protected $resources = array
        (
            'bandwidth_in'                 =>  array
            (
                'FriendlyName'          =>  'Bandwidth In',
                'Description'           =>  '',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  'MB',
                'AvailableTypes'        =>  array('summary'),
                'AvailableUnits'        =>  array
                (
                    'MB'                =>  1,
                    'GB'                =>  0.0009765625
                )
            ),
            'bandwidth_out'             =>  array
            (
                'FriendlyName'          =>  'Bandwidth Out',
                'Description'           =>  '',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  'MB',
                'AvailableTypes'        =>  array('summary'),
                'AvailableUnits'        =>  array
                (
                    'MB'                =>  1,
                    'GB'                =>  0.0009765625
                )
            ),
            'bandwidth_total'             =>  array
            (
                'FriendlyName'          =>  'Bandwidth Total',
                'Description'           =>  '',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  'MB',
                'AvailableTypes'        =>  array('summary'),
                'AvailableUnits'        =>  array
                (
                    'MB'                =>  1,
                    'GB'                =>  0.0009765625
                )
            ),
            'ipsnumber'                 =>  array
            (
                'FriendlyName'          =>  'Number of IPs',
                'Description'           =>  '',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  '/hr',
                'AvailableTypes'        =>  array('average')
            ),
            'disk_space'                 =>  array
            (
                'FriendlyName'          =>  'Disk Space',
                'Description'           =>  '',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  'GB/hr',
                'AvailableTypes'        =>  array('average')
            ),
            'disk_used'                 =>  array
            (
                'FriendlyName'          =>  'Disk Used',
                'Description'           =>  '',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  'GB/hr',
                'AvailableTypes'        =>  array('average')
            ),
            'backup_size'               =>  array
            (
                'FriendlyName'          =>  'Backup Size',
                'Description'           =>  '',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  'GB/hr',
                'AvailableTypes'        =>  array('average')
            ),
            'memory'                    =>  array
            (
                'FriendlyName'          =>  'Memory',
                'Description'           =>  '',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  'MB/hr',
                'AvailableTypes'        =>  array('average')
            ),
            'virtual_memory_usage'      =>  array
            (
                'FriendlyName'          =>  'Virtual Memory Usage',
                'Description'           =>  '',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  'MB/hr',
                'AvailableTypes'        =>  array('average')
            ),
            'physical_memory_usage'      =>  array
            (
                'FriendlyName'          =>  'Physical Memory Usage',
                'Description'           =>  '',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  'MB/hr',
                'AvailableTypes'        =>  array('average')
            ),
            'vcpu'                      =>  array
            (
                'FriendlyName'          =>  'VCPU',
                'Description'           =>  '',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  '/hr',
                'AvailableTypes'        =>  array('average')
            ),
            'load_avg'                  =>  array
            (
                'FriendlyName'          =>  'Average Load',
                'Description'           =>  '',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  '/hr',
                'AvailableTypes'        =>  array('average')
            )
        );


        public function getProductAccounts()
        {
            $accounts = mysql_get_array("SELECT h.id, h.packageid, h.userid, h.domainstatus
                FROM tblhosting h
                LEFT JOIN tblproducts p ON h.packageid = p.id
                WHERE h.packageid = ? AND domainstatus <> 'Terminated'", array($this->product_id));

            if(!$accounts)
            {
                return array();
            }

            //Get Product Settings
            $settings = array();
            $rows = mysql_get_array("SELECT setting, value FROM mg_StormOnDemandPrivateParentProduct WHERE product_id = ? AND (setting = 'Password' OR setting='Username')", array($this->product_id));
            foreach($rows  as $set)
            {
                $settings[$set['setting']] = $set['value'];
            }

            foreach($accounts as &$account)
            {
                //Get Uniq ID
                $custom_fields = mysql_get_array("SELECT v.value, f.fieldname
                    FROM tblcustomfieldsvalues v
                    LEFT JOIN tblcustomfields f ON v.fieldid = f.id
                    WHERE v.relid = ? ", array($account['id']));

                foreach($custom_fields as $f)
                {
                    if(strpos($f, '|') !== false)
                    {
                        $account[substr($f['fieldname'], 0, strpos($f['fieldname'], '|'))] = $f['value'];
                    }
                    else
                    {
                        $account[$f['fieldname']] = $f['value'];
                    }
                }

                //Add settings
                $account += $settings;
            }

            return $accounts;
        }

        public function getSample()
        {
            $accounts = $this->getProductAccounts();

            foreach($accounts as $acc)
            {
                if($acc['domainstatus'] != 'Active')
                {
                    $this->forceLastUpdate($acc['id']);
                    continue;
                }

                //Uniq ID not set?
                if(!$acc['uniq_id'])
                {
                    $this->logError("Uniq ID is not set for account with ID #".$acc['id']);
                    continue;
                }

                //Clear usage records
                $usage_records = array_fill_keys(array_keys($this->resources), 0);

                //load settings from database
                $settings = $this->getSettings($acc['id']);

                //Create Objects
                $server = new StormOnDemandStormServer($acc['Username'], $acc['Password'], 'bleed');
                $bandwidth = new StormOnDemandMonitoringBandwidth($acc['Username'], $acc['Password'], 'bleed');
                $load = new StormOnDemandMonitoringLoad($acc['Username'], $acc['Password'], 'bleed');

                //Get Details
                $details = $server->details($acc['uniq_id']);

                $error = $server->getError();
                if($error)
                {
                    $this->logError($error);
                    continue;
                }

                //Get Bandwidth
                $ret = $bandwidth->stats($acc['uniq_id']);
                $error = $bandwidth->getError();
                if($error)
                {
                    $this->logError($error);
                    continue;
                }

                /********************************************************************************
                 *                              BANDWIDTH
                 ********************************************************************************/
                //Get Bandwidth
                $ret = $bandwidth->stats($acc['uniq_id']);

                //Bandwidth out
                $bandwidth_out      =   isset($ret['actual']['out']['MB']) ? $ret['actual']['out']['MB'] : 0;
                //Bandwidth in
                $bandwidth_in       =   isset($ret['actual']['in']['MB']) ? $ret['actual']['in']['MB'] : 0;

                //IF this is firs cron run we need to make initial data
                if(!$settings)
                {
                    $settings = array
                    (
                        'bandwidth_in'		=>  $bandwidth_in,
                        'bandwidth_out'		=>  $bandwidth_out,
                        'bandwidth_in_sum'          =>  0,
                        'bandwidth_out_sum'         =>  0,
                    );
                }

                //Create new settings
                $new_settings = array
                (
                    'bandwidth_in'  =>  $bandwidth_in,
                    'bandwidth_out' =>  $bandwidth_out
                );

                if($bandwidth_out < $settings['bandwidth_out']) // somebody reset bandwidth
                {
                    $bandwidth_out                      =   ($bandwidth_out + $settings['bandwidth_out_sum']);
                    $new_settings['bandwidth_out_sum']  =   0;
                }
                else
                {
                    $diff   =   $bandwidth_out - $settings['bandwidth_out'];
                    $sum    =   $diff + $settings['bandwidth_out_sum'];
                    //If we have more than 1MB of bandiwdth we can create usage records
                    if($sum > 1)
                    {
                        $bandwidth_out                      =   $sum;
                        $new_settings['bandwidth_out_sum']  =   0;
                    }
                    else
                    {
                        $bandwidth_out                      =   0;
                        $new_settings['bandwidth_out_sum']  =   $sum;
                    }
                }

                if($bandwidth_in < $settings['bandwidth_in']) // somebody reset bandwidth
                {
                    $bandwidth_in                       =   ($bandwidth_in + $settings['bandwidth_in_sum']);
                    $new_settings['bandwidth_in_sum']   =   0;
                }
                else
                {
                    $diff   =   $bandwidth_in - $settings['bandwidth_in'];
                    $sum    =   $diff + $settings['bandwidth_in_sum'];
                    //If we have more than 1MB of bandiwdth we can create usage records
                    if($sum > 1)
                    {
                        $bandwidth_in                      =   $sum;
                        $new_settings['bandwidth_in_sum']  =   0;
                    }
                    else
                    {
                        $bandwidth_in                      =   0;
                        $new_settings['bandwidth_in_sum']  =   $sum;
                    }
                }

                //Bandwidth total
                $bandwidth_total    =   $bandwidth_in + $bandwidth_out;

                /******************************************************************
                 *                      STATISTICS
                 ******************************************************************/
                $load_stats             =   $load->stats($acc['uniq_id']);
                $disk_used              =   isset($load_stats['disk']['used']) ? $load_stats['disk']['used'] : 0;
                $virtual_memory_usage   =   isset($load_stats['memory']['virtual']['used']) ? $load_stats['memory']['virtual']['used'] : 0;
                $physical_memory_usage  =   isset($load_stats['memory']['physical']['used']) ? $load_stats['memory']['physical']['used'] : 0;
                $load_avg               =   isset($load_stats['loadavg']['fifteen']) ? $load_stats['loadavg']['fifteen'] : 0;

                //Collect Usage Records
                $usage_records['bandwidth_in']          =   $bandwidth_in;
                $usage_records['bandwidth_out']         =   $bandwidth_out;
                $usage_records['bandwidth_total']       =   $bandwidth_total;
                $usage_records['ipsnumber']             =   $details['ip_count'];
                $usage_records['disk_space']            =   $details['diskspace'];
                $usage_records['disk_used']             =   $disk_used;
                $usage_records['backup_size']           =   $details['backup_size'];
                $usage_records['memory']                =   $details['memory'];
                $usage_records['virtual_memory_usage']  =   $virtual_memory_usage;
                $usage_records['physical_memory_usage'] =   $physical_memory_usage;
                $usage_records['vcpu']                  =   $details['vcpu'];
                $usage_records['load_avg']              =   $load_avg;

                $this->insertResource($acc['userid'], $acc['id'], $usage_records);
                $this->saveSettings($acc['id'], $new_settings);
            }
        }
    }
}
