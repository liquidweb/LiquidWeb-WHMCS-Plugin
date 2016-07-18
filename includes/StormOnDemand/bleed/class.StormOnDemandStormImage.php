<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandStormImage'))
{
    class StormOnDemandStormImage extends StormOnDemandConnection
    {
        public function create($uniq_id, $name)
        {
            $data['uniq_id']    =   $uniq_id;
            $data['page_num']   =   $name;

            return $this->__request('Storm/Image/create', $data, __METHOD__);
        }

        public function delete($id)
        {
            $data['id']         =   $id;

            return $this->__request('Storm/Image/create', $data, __METHOD__);
        }

        public function details($id)
        {
            $data['id']         =   $id;

            return $this->__request('Storm/Image/details', $data, __METHOD__);
        }

        public function lists($page_size = 20, $page_num = 1)
        {
            $data['page_size']  =   $page_size;
            $data['page_num']   =   $page_num;

            return $this->__request('Storm/Image/list', $data, __METHOD__);
        }

        public function restore($uniq_id, $id, $force = 0)
        {
            $data['uniq_id']    =   $uniq_id;
            $data['id']         =   $id;
            $data['force']      =   $force;

            return $this->__request('Storm/Image/restore', $data, __METHOD__);
        }

        public function update($id, $name)
        {
            $data['id']         =   $id;
            $data['name']       =   $name;

            return $this->__request('Storm/Image/update', $data, __METHOD__);
        }

    }
}
