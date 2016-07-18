<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandStormConfig'))
{
    class StormOnDemandStormConfig extends StormOnDemandConnection
    {
        public function details($id)
        {
            $data['id'] = $id;
            return $this->__request('Storm/Config/details', $data, __METHOD__);
        }

        public function lists($category = 'storm', $page_num = 1, $page_size = 20)
        {
            $data['category']   =   $category;
            $data['page_num']   =   $page_num;
            $data['page_size']  =   $page_size;

            $results = $this->__request('Storm/Config/list', $data, __METHOD__);

            return $results;
        }

        public function ping()
        {
            $results = $this->__request('Utilities/Info/ping','', __METHOD__);

            return $results;
        }

        public function userDetails($username) {
            $data['username']   =   $username;
            $results = $this->__request('Account/User/details', $data, __METHOD__);
            return $results;
        }

        public function userCreate($username, $password) {
            $data['firstname']   =   'WHMCS';
            $data['lastname']   =   'USER';
            $data['password']   =   $password;
            $data['username']   =   'whmcsuser-'.$username;
            $data['roles']   =   Array('AccountLogin','Console','Firewall','LoadBalance','Monitoring','Network','Reboot','Resize','RestoreReimage','ServerServices','Users','Support','Destroy','Profile','Invoices');

            $results = $this->__request('Account/User/create', $data, __METHOD__);

            return $results;
        }

    }
}

