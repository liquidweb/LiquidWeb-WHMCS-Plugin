<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandStormPrivateParent'))
{
    class StormOnDemandServer extends StormOnDemandConnection
    {
        //API TYPE. Private Parent is only available in bleed API
        protected $api_type = 'v1';

        public function create($configid, $domain, $password, $type)
        {
            $data['features']['ConfigId'] = $configid;
            $data['password'] = $password;
            $data['type'] = $type;
            $data['domain'] = $domain;

            return $this->__request('Server/create', $data, __METHOD__);
        }

        public function lists($page_num = 1, $page_size = 25)
        {
            $data['page_num']   =   $page_num;
            $data['page_size']  =   $page_size;

            return $this->__request('Server/list', $data, __METHOD__);
        }
    }
}