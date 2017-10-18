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

            //remove these servers from config list
            $excludeCodes = array("1078","1079","1048","1065","1064","1073","1067","1066","1072","1074","1077","1076","1061","1075","1264","1265","1266","1109");

            foreach ($results['items'] as $key => $value) {
                if (in_array($value['id'], $excludeCodes)) {
                    unset($results['items'][$key]);
                };
            }
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

