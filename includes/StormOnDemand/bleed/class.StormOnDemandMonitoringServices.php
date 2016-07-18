<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandMonitoringServices'))
{
    class StormOnDemandMonitoringServices extends StormOnDemandConnection
    {
        public function get($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Monitoring/Services/get', $data, __METHOD__);
        }

        public function monitoringIps()
        {
            return $this->__request('Monitoring/Services/monitoringIps','', __METHOD__);
        }

        public function status($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Monitoring/Services/status', $data, __METHOD__);
        }

        public function update($uniq_id, $services, $enabled)
        {
            $data['uniq_id'] = $uniq_id;
            $data['services'] = $services;
            $data['enabled'] = $enabled;

            return $this->__request('Monitoring/Services/update', $data, __METHOD__);
        }
    }
}