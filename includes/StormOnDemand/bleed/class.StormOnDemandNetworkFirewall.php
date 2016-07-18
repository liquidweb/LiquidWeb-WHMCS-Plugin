<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandNetworkFirewall'))
{
    class StormOnDemandNetworkFirewall extends StormOnDemandConnection
    {
        public function details($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Network/Firewall/details', $data, __METHOD__);
        }

        public function getBasicOptions($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Network/Firewall/getBasicOptions', $data, __METHOD__);
        }

        public function rules($uniq_id)
        {
            $data['uniq_id'] = $uniq_id;

            return $this->__request('Network/Firewall/rules', $data, __METHOD__);
        }

        public function update($uniq_id, $type = 'none', $rules = array())
        {
            $data['uniq_id'] = $uniq_id;
            $data['type'] = $type;

            switch($type)
            {
                case 'basic':
                    $data['allow'] = $rules;
                    break;
                case 'saved':
                    $data['ruleset'] = $rules;
                    break;
                case 'advanced':
                    //We need to send array starting from 0.
                    sort($rules);
                    $data['rules'] = $rules;
                    break;
            }

            return $this->__request('Network/Firewall/update', $data, __METHOD__);
        }
    }
}
