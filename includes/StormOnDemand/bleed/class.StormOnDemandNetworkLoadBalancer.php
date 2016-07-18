<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandNetworkLoadBalancer'))
{
    class StormOnDemandNetworkLoadBalancer extends StormOnDemandConnection
    {
        public function addNode($uniq_id, $node)
        {
            $data['uniq_id'] = $uniq_id;
            $data['node'] = $node;

            return $this->__request('Network/LoadBalancer/addNode', $data, __METHOD__);
        }

        public function addService($uniq_id, $src_port, $dest_port)
        {
            $data['uniq_id'] = $uniq_id;
            $data['src_port'] = $dest_port;

            return $this->__request('Network/LoadBalancer/addService', $data, __METHOD__);
        }

        public function available($name)
        {
            $data['name'] = $name;

            return $this->__request('Network/LoadBalancer/available', $data, __METHOD__);
        }

        public function create($name, $services, $strategy, $session_persistence, $region = 1, $nodes = array())
        {
            $data['name'] = $name;
            $data['services'] = $services;
            $data['strategy'] = $strategy;
            $data['session_persistence'] = $session_persistence;
            $data['region'] = $region;
            $data['nodes'] = $nodes;

            return $this->__request('Network/LoadBalancer/create', $data, __METHOD__);
        }

        public function delete($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;;

            return $this->__request('Network/LoadBalancer/delete', $data, __METHOD__);
        }

        public function details($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Network/LoadBalancer/details', $data, __METHOD__);
        }

        public function lists($region, $page_size = 10, $page_num = 1)
        {
            $data['region'] = $region;
            $data['page_size'] = $page_size;
            $data['page_num'] = $page_num;

            return $this->__request('Network/LoadBalancer/addNode', $data, __METHOD__);
        }

        public function possibleNodes($region)
        {
            $data['region'] = $region;

            return $this->__request('Network/LoadBalancer/possibleNodes', $data, __METHOD__);
        }

        public function removeNode($uniq_id, $node)
        {
            $data['uniq_id'] = $uniq_id;
            $data['node'] = $node;

            return $this->__request('Network/LoadBalancer/removeNode', $data, __METHOD__);
        }

        public function removeService($uniq_id, $src_port)
        {
            $data['uniq_id'] = $uniq_id;
            $data['src_port'] = $src_port;

            return $this->__request('Network/LoadBalancer/removeService', $data, __METHOD__);
        }

        public function strategies()
        {
            return $this->__request('Network/LoadBalancer/strategies', $data, __METHOD__);
        }

        public function update($uniq_id, $name, $services, $strategy, $session_persistence, $nodes = array(), $additionals = array(/*ssl_termination, ssl_key, ssl_cert, ssl_includes, ssl_int*/))
        {
            $data['uniq_id'] = $uniq_id;
            $data['name'] = $name;
            $data['services'] = $services;
            $data['strategy'] = $strategy;
            $data['session_persistence'] = $session_persistence;
            $data['nodes'] = $nodes ? $nodes : $nodes[] = array();
            $data += $additionals;

            return $this->__request('Network/LoadBalancer/update', $data, __METHOD__);
        }
    }
}