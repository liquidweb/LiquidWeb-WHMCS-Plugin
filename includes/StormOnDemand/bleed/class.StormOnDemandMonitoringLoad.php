<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandMonitoringLoad'))
{
    class StormOnDemandMonitoringLoad extends StormOnDemandConnection
    {
        public function graph($uniq_id, $width, $height, $stat, $duration, $compact = 0)
        {
            $data['uniq_id'] = $uniq_id;
            $data['width'] = $width;
            $data['height'] = $height;
            $data['stat'] = $stat;
            $data['duration'] = $duration;
            $data['compact'] = $compact;

            return $this->__request('Monitoring/Load/graph', $data, __METHOD__);
        }

        public function stats($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Monitoring/Load/stats', $data, __METHOD__);
        }
    }
}