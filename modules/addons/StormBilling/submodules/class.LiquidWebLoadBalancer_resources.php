<?php


require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandNetworkLoadBalancer.php';

if(!class_exists('LiquidWebLoadBalancer_resources'))
{
    class LiquidWebLoadBalancer_resources extends SBResource 
    {

        const name = 'Liquid Web Load Balancer';

        //module description
        const description = '';

        protected $resources = array
        (
            'services'                 =>  array
            (
                'FriendlyName'          =>  'Services',
                'Description'           =>  'Billing for amount of services',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  '/hr',
                'AvailableTypes'        =>  array('average')
            ),
            'nodes'                 =>  array
            (
                'FriendlyName'          =>  'Nodes',
                'Description'           =>  'Billing for amount of nodes',
                'InvoiceDescription'    =>  '',
                'Unit'                  =>  '/hr',
                'AvailableTypes'        =>  array('average')
            ),
        );


        public function getProductAccounts()
        {
            $accounts = array();
            $q = mysql_safequery("SELECT DISTINCT d.uniq_id, h.id as hosting_id, p.*, c.id as userid, h.domainstatus
                                  FROM `tblhosting` h 
                                  JOIN `tblproducts` p ON(h.packageid = p.id) 
                                  LEFT JOIN `tblservers` s ON(h.server = s.id)
                                  LEFT JOIN mg_liquid_web_load_balancer d ON h.id = d.hosting_id
                                  JOIN `tblclients` c ON(c.id = h.userid)
                                  WHERE p.servertype = ? AND p.id = ? AND domainstatus <> 'Terminated'", array($this->type, $this->product_id));

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

                $loadbalancer = new StormOnDemandNetworkLoadBalancer($acc['configoption1'], $acc['configoption2']);
                $details = $loadbalancer->details($acc['uniq_id']);

                $services = count($details['services']);
                $nodes = count($details['nodes']);

                $account_resources_usages = array();
                $account_resources_usages['services'] = $services;
                $account_resources_usages['nodes'] = $nodes;

                $this->insertResource($acc['userid'], $acc['hosting_id'], $account_resources_usages);
            }
        }
    }
}
