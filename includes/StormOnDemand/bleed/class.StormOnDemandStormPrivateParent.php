<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandStormPrivateParent'))
{
    class StormOnDemandStormPrivateParent extends StormOnDemandConnection
    {
        //API TYPE. Private Parent is only available in bleed API
        protected $api_type = 'bleed';

        public function create($config_id, $domain, $zone = '')
        {
            $data['config_id'] = $config_id;
            $data['domain'] = $domain;

            if($zone)
            {
                $data['zone']   =   $zone;
            }

            return $this->__request('Storm/Private/Parent/create', $data, __METHOD__);
        }

        public function delete($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Storm/Private/Parent/delete', $data, __METHOD__);
        }

        public function details($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Storm/Private/Parent/details', $data, __METHOD__);
        }

        public function lists($page_num = 1, $page_size = 25)
        {
            $data['page_num']   =   $page_num;
            $data['page_size']  =   $page_size;

            return $this->__request('Storm/Private/Parent/list', $data, __METHOD__);
        }

        public function reboot($uniq_id, $force = 0)
        {
            $data['uniq_id'] = $uniq_id;
            $data['force'] = $force;

            return $this->__request('Storm/Private/Parent/list', $data, __METHOD__);
        }

        public function update($uniq_id, $domain)
        {
            $data['uniq_id']    =   $uniq_id;
            $data['domain']     =   $domain;

            return $this->__request('Storm/Private/Parent/update', $data, __METHOD__);
        }
    }
}