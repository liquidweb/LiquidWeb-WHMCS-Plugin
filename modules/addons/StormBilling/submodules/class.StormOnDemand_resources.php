<?php
require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormServer.php';
require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandMonitoringBandwidth.php';
require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandMonitoringLoad.php';

if(!class_exists('StormOnDemand_resources'))
{
    class StormOnDemand_resources extends SBResource
    {
        const name = 'Liquid Web'; 
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
                'FriendlyName'          =>  "IPs Number",
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
            $accounts = array();
            $q = mysql_safequery("SELECT DISTINCT d.uniq_id, h.id as id, h.userid, p.configoption1 as Username, p.configoption2 as Password, h.domainstatus
                                  FROM `tblhosting` h 
                                  JOIN `tblproducts` p ON(h.packageid = p.id) 
                                  LEFT JOIN mg_storm_on_demand d ON h.id = d.hosting_id
                                  WHERE p.id = ? AND domainstatus <> 'Terminated'", array($this->product_id));

            while($row = mysql_fetch_assoc($q))
            {
                $accounts[] = $row;
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