<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandStormPrivateParent'))
{
    class StormOnDemandServer extends StormOnDemandConnection
    {
        //API TYPE. Private Parent is only available in bleed API
        //protected $api_type = 'v1';

        public function create($domain, $password, $data)
        {
            //$data['features']['ConfigId'] = $configid;
            $data['password'] = $password;
            //$data['type'] = $additonal['type'];
            $data['domain'] = $domain;
            //$data += $additonal;

            return $this->__request('Server/create', $data, __METHOD__);
        }

        public function lists($page_num = 1, $page_size = 25)
        {
            $data['page_num']   =   $page_num;
            $data['page_size']  =   $page_size;

            return $this->__request('Server/list', $data, __METHOD__);
        }

        public function destroy($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Server/destroy', $data, __METHOD__);
        }

        public function details($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Server/details', $data, __METHOD__);
        }

        public function serverList()
        {

        }

        public function reboot($uniq_id, $force = 0)
        {
            $data['uniq_id'] = $uniq_id;
            $data['force'] = $force;

            return $this->__request('Server/reboot', $data, __METHOD__);
        }

        public function resize($uniq_id, $config_id, $skip_fs_resize = 0, $additonal = array())
        {
            $data['uniq_id'] = $uniq_id;
            $data['config_id'] = $config_id;
            $data['skip_fs_resize'] = $skip_fs_resize;
            $data += $additonal;

            return $this->__request('Server/resize', $data, __METHOD__);
        }

        public function start($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Server/start', $data, __METHOD__);
        }

        public function update($uniq_id, $additional = array())
        {
            $data['uniq_id'] = $uniq_id;
            $data += $additional;

            return $this->__request('Server/update', $data, __METHOD__);
        }

        public function shutdown($uniq_id, $force = 0)
        {
            $data['uniq_id'] = $uniq_id;
            $data['force'] = $force;

            return $this->__request('Server/shutdown', $data, __METHOD__);
        }

        public function history($uniq_id, $page_size = 20, $page_num = 1)
        {
            $data['uniq_id'] = $uniq_id;
            $data['page_size'] = $page_size;
            $data['page_num'] = $page_num;

            return $this->__request('Server/history', $data, __METHOD__);
        }

        public function status($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Server/status', $data, __METHOD__);
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

            return $this->__request('Server/clone',$data, __METHOD__);
		}

    }
}