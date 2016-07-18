<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandStormTemplate'))
{
    class StormOnDemandStormTemplate extends StormOnDemandConnection
    {
        public function details($id)
        {
            $data['id'] = $id;

            return $this->__request('Storm/Template/details ', $data, __METHOD__);
        }

        public function lists($page_size = 9001, $page_num = 1)
        {
            $data['page_size'] = $page_size;
            $data['page_num'] = $page_num;

            return $this->__request('Storm/Template/list ', $data, __METHOD__);
        }

        public function restore($uniq_id, $id, $force = 0)
        {
            $data['uniq_id'] = $uniq_id;
            $data['id'] = $id;
            $data['force'] = $force;

            return $this->__request('Storm/Template/restore', $data, __METHOD__);
        }
    }
}
