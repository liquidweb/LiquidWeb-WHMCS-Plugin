<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandStormBackup'))
{
    class StormOnDemandStormBackup extends StormOnDemandConnection
    {
        public function details($uniq_id, $backup_id)
        {
            $data['uniq_id'] = $uniq_id;
            $data['id'] = $backup_id;

            return $this->__request('Storm/Backup/details', $data, __METHOD__);
        }

        public function lists($uniq_id, $page_size = 20, $page_num = 1)
        {
            $data['uniq_id'] = $uniq_id;
            $data['page_size'] = $page_size;
            $data['page_num'] = $page_num;

            return $this->__request('Storm/Backup/list', $data, __METHOD__);
        }



        public function restore($uniq_id, $backup_id, $force = 0)
        {
            $data['uniq_id'] = $uniq_id;
            $data['id'] = $backup_id;
            $data['force'] = $force;

            return $this->__request('Storm/Backup/restore', $data, __METHOD__);
        }
    }
}
