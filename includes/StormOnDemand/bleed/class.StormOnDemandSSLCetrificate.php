<?php

/**********************************************************************
 * Custom developed. (2014-12-18)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->       http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 **********************************************************************/

/**
 * @author Kamil Szela <kamil@modulesgarden.com>
 */

if(!class_exists('StormOnDemandSSLCetrificate'))
{
	require_once 'class.StormOnDemandConnection.php';

	class StormOnDemandSSLCetrificateException extends Exception{

		private $_errors = array();

		public function __construct($msg,$errors = array()){
			parent::__construct($msg);
			$this->_errors = $errors;
		}

		public function getErrors(){
			return $this->_errors;
		}
	}

    class StormOnDemandSSLCetrificate extends StormOnDemandConnection
    {

		private $_durations 			= array('1','2','3');
		private $_verification_methods 	= array('auto', 'dns', 'email', 'metatag');

		const DOMAIN_WITH_WILDCARD_REGX = '/^(http:\/\/|https:\/\/)?(\*\.)?[a-z0-9\_\-\.]{1,100}\.[a-z0-9]{1,100}$/D';
		const DOMAIN_REGX 				= '/^(http:\/\/|https:\/\/)?[a-z0-9\_\-\.]{1,100}\.[a-z0-9]{1,100}$/D';
		const DOMAIN_WITH_HTTP_OR_HTTPS = '/^(http:\/\/|https:\/\/)[a-z0-9\_\-\.]{1,100}\.[a-z0-9]{1,100}$/D';

		public function __construct($u, $p, $api = 'bleed'){
			parent::__construct($u,$p,$api);
		}

		public function order($csr, $domain, $duration, $verificationMethod){

			$errors = array();
			$data   = array();

			if(!openssl_csr_get_subject($csr)){
				$errors []= 'csr';
			}else{
				$data['csr'] = $csr;
			}

			/*
			 * Either: a fully-qualified domain name (i.e. liquidweb.com, www.liquidweb.com, etc);
			 * or a wildcard, represented by '*.domain'.
			 */
			if(!preg_match(StormOnDemandSSLCetrificate::DOMAIN_WITH_WILDCARD_REGX, $domain)){
				$errors []= 'domain: '.$domain;
			}else{
				$data['domain'] = $domain;
			}
			if(!in_array((string)$duration, $this->_durations)){
				$errors []= 'duration: '.$duration;
			}else{
				$data['duration'] = (string) $duration;
			}
			if(!in_array($verificationMethod, $this->_verification_methods)){
				$errors []= 'verification_method: '.$verificationMethod;
			}else{
				$data['verification_method'] = $verificationMethod;
			}

			if(!empty($errors)){
				throw new StormOnDemandSSLCetrificateException('Not valid', $errors);
			}

            return $this->__request('SSL/Certificate/order', $data, __METHOD__);
		}

		public function verfiy($email, $name, $subaccnt = null, $uniq_id = null, $url){

			$errors = array();
			$data   = array();

			if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
				$errors []= 'email';
			}else{
				$data['email'] = $email;
			}

			$uniq_id = trim($uniq_id);

			/*
			 * Either: a fully-qualified domain name (i.e. liquidweb.com, www.liquidweb.com, etc);
			 */
			if(!preg_match(StormOnDemandSSLCetrificate::DOMAIN_REGX, $name)){
				$errors []= 'name: '.$name;
			}else{
				$data['name'] = $name;
			}
			if($subaccnt && !preg_match('/^[0-9]{1,}$/D',$subaccnt)){
				$errors []= 'subaccnt: '.$subaccnt;
			}elseif($subaccnt){
				$data['subaccnt'] = $subaccnt;
			}
			if($uniq_id && !preg_match('/^[0-9a-zA-Z]{1,}$/D',$uniq_id)){
				$errors []= 'uniq_id: '.$uniq_id;
			}else{
				$data['uniq_id'] = $uniq_id;
			}
			if(!$subaccnt && !$uniq_id){
				$errors []= 'null id';
			}
			if($url && !preg_match(StormOnDemandSSLCetrificate::DOMAIN_WITH_HTTP_OR_HTTPS, $url)){
				$errors []= 'url: '.$url;
			}else{
				$data['url'] = $url;
			}

			if(!empty($errors)){
				throw new StormOnDemandSSLCetrificateException('Valid error', $errors);
			}

            return $this->__request('SSL/Certificate/verify', $data, __METHOD__);
		}

		public function confirmVerified($subaccnt = null, $uniq_id = null){

			$errors = array();
			$data   = array();

			if($subaccnt && !preg_match('/^[0-9]{1,}$/D',$subaccnt)){
				$errors []= 'subaccnt';
			}elseif($subaccnt){
				$data['subaccnt'] = $subaccnt;
			}
			if($uniq_id && !preg_match('/^[0-9a-zA-Z]{1,}$/D',$uniq_id)){
				$errors []= 'uniq_id: '.$uniq_id;
			}else{
				$data['uniq_id'] = $uniq_id;
			}

			if(!empty($errors)){
				throw new StormOnDemandSSLCetrificateException('Valid error', $errors);
			}

            return $this->__request('SSL/Certificate/confirmVerified', $data, __METHOD__);
		}

		public function renew($duration = 1, $subaccnt = null, $uniq_id = null){

			$errors = array();
			$data   = array();

			if($subaccnt && !preg_match('/^[0-9]{1,}$/D',$subaccnt)){
				$errors []= 'subaccnt';
			}else{
				$data['subaccnt'] = $subaccnt;
			}
			if($uniq_id && !preg_match('/^[0-9a-zA-Z]{1,}$/D',$uniq_id)){
				$errors []= 'uniq_id: '.$uniq_id;
			}else{
				$data['uniq_id'] = $uniq_id;
			}

			if($duration && !preg_match('/^[0-9a-zA-Z]{1,}$/D',$duration)){
				$errors []= 'duration: '.$duration;
			}else{
				$data['duration'] = $duration;
			}

			if(!empty($errors)){
				throw new StormOnDemandSSLCetrificateException('Valid error', $errors);
			}

            return $this->__request('SSL/Certificate/renew', $data, __METHOD__);
		}
        /**
         * updated by Piotr Kozd�ba <piotr.ko@modulesgarden.com>
         */
		public function revoke($subaccnt = null, $uniq_id = null){

			$errors = array();
			$data   = array();

			if($subaccnt && !preg_match('/^[0-9]{1,}$/D',$subaccnt)){
				$errors []= 'subaccnt';
			}else{
				$data['subaccnt'] = $subaccnt;
			}
			if($uniq_id && !preg_match('/^[0-9a-zA-Z]{1,}$/D',$uniq_id)){
				$errors []= 'uniq_id: '.$uniq_id;
			}else{
				$data['uniq_id'] = $uniq_id;
			}

			if(!empty($errors)){
				throw new StormOnDemandSSLCetrificateException('Valid error', $errors);
			}

            return $this->__request('SSL/Certificate/revoke', $data, __METHOD__);
		}
	}
}