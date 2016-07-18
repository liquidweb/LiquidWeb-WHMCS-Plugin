<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandStormServer'))
{
    class StormOnDemandStormServer extends StormOnDemandConnection
    {
        public function create($domain, $password, $config_id, $template, $additonal = array(/*antivirus, backup_enabled, backup_id, backup_plan, backup_quota, bandwidth_quota, image_id, ip_count, ms_sql, public_ssh_key, zone*/))
        {
            $data['domain'] = $domain;
            $data['password'] = $password;
            $data['template'] = $template;
            $data['config_id'] = $config_id;
            $data += $additonal;

            if($data['image_id'])
            {
                unset($data['template']);
            }

            if($data['template'])
            {
                unset($data['image_id']);
            }

            return $this->__request('Storm/Server/create', $data, __METHOD__);
        }

        public function destroy($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Storm/Server/destroy', $data, __METHOD__);
        }

        public function details($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Storm/Server/details', $data, __METHOD__);
        }

        public function serverList()
        {

        }

        public function reboot($uniq_id, $force = 0)
        {
            $data['uniq_id'] = $uniq_id;
            $data['force'] = $force;

            return $this->__request('Storm/Server/reboot', $data, __METHOD__);
        }

        public function resize($uniq_id, $config_id, $skip_fs_resize = 0, $additonal = array())
        {
            $data['uniq_id'] = $uniq_id;
            $data['config_id'] = $config_id;
            $data['skip_fs_resize'] = $skip_fs_resize;
            $data += $additonal;

            return $this->__request('Storm/Server/resize', $data, __METHOD__);
        }

        public function start($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Storm/Server/start', $data, __METHOD__);
        }

        public function update($uniq_id, $additional = array())
        {
            $data['uniq_id'] = $uniq_id;
            $data += $additional;

            return $this->__request('Storm/Server/update', $data, __METHOD__);
        }

        public function shutdown($uniq_id, $force = 0)
        {
            $data['uniq_id'] = $uniq_id;
            $data['force'] = $force;

            return $this->__request('Storm/Server/shutdown', $data, __METHOD__);
        }

        public function history($uniq_id, $page_size = 20, $page_num = 1)
        {
            $data['uniq_id'] = $uniq_id;
            $data['page_size'] = $page_size;
            $data['page_num'] = $page_num;

            return $this->__request('Storm/Server/history', $data, __METHOD__);
        }

        public function status($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Storm/Server/status', $data, __METHOD__);
        }

		public function cloneServer($idUniq, $hostname, $password, $parent = null){

			$data = array(
				'uniq_id'  => $idUniq,
				'domain'   => $hostname,
				'password' => $password,
			);

			if($parent){
				$data['parent'] = $parent;
				$this->api_version = 'bleed';
			}

            return $this->__request('Storm/Server/clone',$data, __METHOD__);
		}
    }
}