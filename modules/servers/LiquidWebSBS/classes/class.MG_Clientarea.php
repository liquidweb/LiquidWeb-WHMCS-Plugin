<?php

/**********************************************************************
 * Product developed. (2014-12-18)
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

abstract class MG_Clientarea {

      public $serviceId;
      public $serviceMainUrl;
      public $servicePageUrl;
      public $assetsUrl;
      public $module;
      public static $run=false;
      private $act='index';
      protected $mainDir;
	/**
	 *
	 * @var array
	 */
	public $_lang;
       
      public function __construct($serviceId) {
            $this->serviceId =$serviceId;
            $this->module = get_class($this);
            $this->serviceMainUrl = 'clientarea.php?action=productdetails&id=' . $this->serviceId;
            $this->servicePageUrl = 'clientarea.php?action=productdetails&id=' . $this->serviceId . '&modop=custom&a=management&';
            $this->assetsUrl = 'modules/servers/'.  $this->module.'/assets';
            $this->mainDir = dirname(__FILE__) . DS."..";
      }
      
      public function init(){
      }
	
	public function run($action='index',$params){
             if($action==null)
                  $this->redToMainPage ();
		if (!method_exists($this, $action. 'Action'))
			throw new Exception('Client Area action not found');
              
		$this->_lang = $this->getLang($params);
                      
		$run = $action.'Action';
		$vars = $this->$run($params);
              $vars['lang'] =  $this->_lang;
              $vars['assetsUrl'] =  $this->assetsUrl;
              $vars['serviceMainUrl'] = $this->serviceMainUrl;
              return array(
                                'templatefile' => 'templates/'.$action,
                                'vars' => $vars,
                   );
	}
       // === errors and infos ===
	protected function addError($error) {
		$errors = isset($_SESSION[get_class($this)]['errors']) ? $_SESSION[get_class($this)]['errors'] : array();
		if (!in_array($error, $errors))
			$_SESSION[get_class($this)]['errors'][] = $error;
	}
	protected function addInfo($info){
		$infos = isset($_SESSION[get_class($this)]['infos']) ? $_SESSION[get_class($this)]['infos'] : array();
		if (!in_array($info, $infos))
			$_SESSION[get_class($this)]['infos'][] = $info;
	}

	protected function getErrors(){
		$errors = isset($_SESSION[get_class($this)]['errors']) ? $_SESSION[get_class($this)]['errors'] : array();
		$_SESSION[get_class($this)]['errors'] = null;
		return $errors;
	}

	protected function getInfos(){
		$infos = isset($_SESSION[get_class($this)]['infos']) ? $_SESSION[get_class($this)]['infos'] : array();
		$_SESSION[get_class($this)]['infos'] = null;
		return $infos;
	}
	
	protected function isErrors() {
		$errors = isset($_SESSION[get_class($this)]['errors']) ? $_SESSION[get_class($this)]['errors'] : array();
		return empty($errors) ? false : true;
	}
       
       protected function getLang($params) {
            global $CONFIG;
            if (!empty($_SESSION['Language']))
                  $language = strtolower($_SESSION['Language']);
            else if (strtolower($params['clientsdetails']['language']) != '')
                  $language = strtolower($params['clientsdetails']['language']);
            else
                  $language = $CONFIG['Language'];

            require_once($this->mainDir . DS . 'lang' . DS . 'english.php');
            
            $langfilename = $this->mainDir . DS . 'lang' . DS . $language . '.php';
            if (file_exists($langfilename)) {
                  require_once($langfilename);
            }
                 
            return isset($lang) ? $lang : array();
      }
      
      protected function redToMainPage(){
             header("Location: ". $this->serviceMainUrl);
             die();
       }

}