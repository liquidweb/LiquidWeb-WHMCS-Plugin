<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandMonitoringBandwidth'))
{
    class StormOnDemandMonitoringBandwidth extends StormOnDemandConnection
    {
        public function graph($uniq_id, $width, $height, $frequency, $small = 0)
        {
            $data['uniq_id'] = $uniq_id;
            $data['width'] = $width;
            $data['height'] = $height;
            $data['frequency'] = $frequency;
            $data['small'] = $small;

            return $this->__request('Monitoring/Bandwidth/graph', $data, __METHOD__);
        }

        public function stats($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Monitoring/Bandwidth/stats', $data, __METHOD__);
        }
    }
}