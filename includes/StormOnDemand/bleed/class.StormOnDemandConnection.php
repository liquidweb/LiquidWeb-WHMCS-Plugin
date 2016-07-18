<?php

/**
 * StormOnDemand. Simple class for connecting to API
 */
if (!class_exists('StormOnDemandConnection')) {

    class StormOnDemandConnection {

        protected $api_url = array
            (
            'v1' => 'https://api.stormondemand.com/v1/',
            'bleed' => 'https://api.stormondemand.com/bleed/'
        );
        //Default API Type
        protected $api_version = 'v1';
        private $username = null;
        private $password = null;
        private $ch = null;
        //errors queue
        private $errors = array();
        private $last_request;
        private $last_request_header;
        private $last_response;
        private $last_response_header;

        private function logLastCall($request, $response, $action='CurlCall')
        {
            if (is_callable('logmodulecall')) {
                logModuleCall('LiquidWebStormServers', $action, $request, $response);
            }
        }

        //enabled bleed API
        public function setAPIVersion($version)
        {
            if (isset($this->api_url[$version])) {
                $this->api_version = $version;
            }
        }

        public function __construct($username, $password, $api = 'v1')
        {
            //Set API Version
            $this->setAPIVersion($api);

            //Prepare data
            $this->username = $username;
            $this->password = $password;

            //Create curl with basic settings
            $this->ch = curl_init();
            curl_setopt($this->ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->ch, CURLOPT_HEADER, true);
            curl_setopt($this->ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json'
            ));
        }

        public function __destruct()
        {
            curl_close($this->ch);
        }

        private function resetLastCall()
        {
            $this->last_request = false;
            $this->last_request_header = false;
            $this->last_response = false;
            $this->last_response_header = false;
        }

        public function getLastRequest()
        {
            return array(
                'header' => $this->last_request_header,
                'content' => $this->last_request,
            );
        }

        public function getLastResponse()
        {
            return array(
                'header' => $this->last_response_header,
                'content' => $this->last_response,
            );
        }

        public function __request($url, $data = array(), $action="CurlCall")
        {
            $this->resetLastCall();
            if (!empty($data)) {
                //preapre json
                $data = json_encode(array('params' => $data));
                //set post data
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
            }

            //set url
            curl_setopt($this->ch, CURLOPT_URL, $this->api_url[$this->api_version] . $url);
            //header
            //run curl
            $ret = curl_exec($this->ch);

            $header = curl_getinfo($this->ch);

            $this->last_request = $data;
            $this->last_request_header = $header['request_header'];

            //check curl error
            if ($ret == false) {
                require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
	            $customConfig = StormOnDemand_Helper::getCustomConfigValues();
	            if ($customConfig['log_api_errors'] == 'YES') {
                    $this->logLastCall($this->last_request_header . $this->last_request, 'CURL ERROR: ' . curl_error($this->ch), $action);
	            }
                $this->addError(curl_error($this->ch));
                return false;
            }

            if ($ret !== false) {
                $headerend = strrpos($ret, "\r\n\r\n");
                $oheader = substr($ret, 0, $headerend);
                $ret = trim(substr($ret, $headerend));

                $this->last_response = $ret;
                $this->last_response_header = $oheader."\r\n\r\n";
            }

            require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
            $customConfig = StormOnDemand_Helper::getCustomConfigValues();
            if ($customConfig['log_api_calls'] == 'YES') {
                $this->logLastCall($this->last_request_header . $this->last_request, $this->last_response_header . $this->last_response, $action);
            }

            if ($header['http_code'] == 401) {
                $this->addError('Authorization required');
                return false;
            }
            //decode json
            $json = json_decode($ret, true);

            // is valid json?
            if (!$json) {
                $this->addError('JSON error');
                return false;
            }
            //any error returned by API?
            if (isset($json['error_class'])) {
                $this->addError($json['full_message']);
                return false;
            }
            //return json
            return $json;
        }

        //ERROR HANDLING

        /**
         * Add error
         * @param type $error
         */
        private function addError($error)
        {
            $this->errors[] = $error;
        }

        /**
         * Get LAST error. If no error return false
         * @return type
         */
        public function getError()
        {
            if ($this->errors) {
                return array_pop($this->errors);
            }

            return false;
        }

    }

}