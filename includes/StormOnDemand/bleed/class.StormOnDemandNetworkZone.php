<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandNetworkZone'))
{
    class StormOnDemandNetworkZone extends StormOnDemandConnection
    {
        public function lists($region = '', $page_size = 20, $page_num = 1)
        {
            if($region)
            {
                $data['region']     =   $region;
            }
            $data['page_size']  =   $page_size;
            $data['page_num']   =   $page_num;

            return $this->__request('Network/Zone/list', $data, __METHOD__);
        }

        public function details($id)
        {
          $data['id']   =   $id; //Zone Id
          return $this->__request('Network/Zone/details', $data, __METHOD__);
        }
    }
}
