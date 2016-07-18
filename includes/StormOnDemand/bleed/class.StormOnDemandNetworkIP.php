<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandNetworkIP'))
{
    class StormOnDemandNetworkIP extends StormOnDemandConnection
    {
        public function add($uniq_id, $ip_count, $reboot = 0)
        {
            $data['uniq_id'] = $uniq_id;
            $data['ip_count'] = $ip_count;
            $data['reboot'] = $reboot;

            return $this->__request('Network/IP/add', $data, __METHOD__);
        }

        public function details($uniq_id, $ip)
        {
            $data['uniq_id'] = $uniq_id;
            $data['ip'] = $ip;

            return $this->__request('Network/IP/details', $data, __METHOD__);
        }

        public function lists($uniq_id, $page_size = 20, $page_num = 1)
        {
            $data['uniq_id'] = $uniq_id;
            $data['page_size'] = $page_size;
            $data['page_num'] = $page_num;

            return $this->__request('Network/IP/list', $data, __METHOD__);
        }

        public function listAccntPublic($uniq_id, $page_size = 20, $page_num = 1)
        {
            $data['uniq_id'] = $uniq_id;
            $data['page_size'] = $page_size;
            $data['page_num'] = $page_num;

            return $this->__request('Network/IP/listAccntPublic', $data, __METHOD__);
        }

        public function listPublic($uniq_id, $page_size = 20, $page_num = 1)
        {
            $data['uniq_id'] = $uniq_id;
            $data['page_size'] = $page_size;
            $data['page_num'] = $page_num;

            return $this->__request('Network/IP//listPublic', $data, __METHOD__);
        }

        public function remove($uniq_id, $ip, $reboot = 0)
        {
            $data['uniq_id'] = $uniq_id;
            $data['ip'] = $ip;
            $data['reboot'] = $reboot;

            return $this->__request('Network/IP/remove', $data, __METHOD__);
        }
    }
}
