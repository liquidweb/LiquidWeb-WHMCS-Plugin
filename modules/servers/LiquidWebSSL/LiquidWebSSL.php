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
 * updated by Piotr Kozdęba <piotr.ko@modulesgarden.com>
 */

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
include_once dirname(__FILE__) .DIRECTORY_SEPARATOR.'loader.php';

//ini_set('display_errors', E_ALL);
//error_reporting(1);

if(file_exists(dirname(__FILE__).DS.'moduleVersion.php')){
    require_once dirname(__FILE__).DS.'moduleVersion.php';
     define('LIQUID_WEB_SSL_VERSION', $moduleVersion);
}else{
     define('LIQUID_WEB_SSL_VERSION', 'Development Version');
}


define('LiquidWebSSL_DefaultDuration', 1);


$LiquidWebSSLConfig = array
(
    'debug_mode_errors' => true,
);

function LiquidWebSSL_AdminServicesTabFields($params){ }

function LiquidWebSSL_ClientArea($params){

  $ModuleInformationClient = Smart_Load('modulesgarden_ModuleInformationClient');
  $r  = mysql_fetch_assoc($ModuleInformationClient::mysql_safequery("SELECT * FROM tblsslorders WHERE serviceid = ?", array($params['serviceid'])));
  $mod  = mysql_fetch_assoc($ModuleInformationClient::mysql_safequery("SELECT * FROM mg_LiquidWebSSL_ssl WHERE account_id = ?", array($params['serviceid'])));


    if (!$r){
        $errors[] =  "No SSL Order exists for this product";
    }

   // $_SERVER['HTTP_REFERER'] = $CONFIG['SystemURL'] . "/whmcs/configuressl.php?cert=" . md5($r['id']."&step=2");
  list($subfolder , $opscript) = explode('clientarea.php',parse_url('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], PHP_URL_PATH));



    $veryfyurl  = $CONFIG['SystemURL'] . $subfolder. "configuressl.php?cert=" . $mod['cert'] . "&step=3&order=approved";
    $stepurl2   = $CONFIG['SystemURL'] . $subfolder. "configuressl.php?cert=" . $mod['cert'] . "&step=2";
    $stepurl3   = $CONFIG['SystemURL'] . $subfolder. "configuressl.php?cert=" . $mod['cert'] . "&step=3&order=continue";


  $configData = unserialize($r['configdata']);

  $step1 = 'close';
  $step2 = 'close';
  $step3 = 'close';

  $retry = 'show';

  if(empty($configData)){
    $step1 = 'open';
  }elseif(empty($configData['approveremail'])){
    $step2 = 'open';
  }else{
    $step3 = 'open';
  }

  $notordered = false;

  if($mod['uniq_id'] == '' || $mod['uniq_id'] == null ){
    $notordered = true;
  }

  $verification_data = json_decode($mod['verification_data'], true);

  if($configData['fields']['verification_method'] == 'dns'){
    $rData = $verification_data['rdata'];
    $domaintxt = $rData;
    //list($gsign,$domaintxt) = explode('=',$rData);
    if($notordered == false){
      $retry = 'hide';
    }
  }

  if($configData['fields']['verification_method'] == 'metatag'){
    $metatag_approved_domain = $configData['fields']['metatag_approved_domain'];
    $metatag = htmlspecialchars($verification_data['MetaTag']);
    if($notordered == false){
      $retry = 'hide';
    }
  }

$links = LiquidWebSSL_ClientAreaCustomButtonArray();

  return array(
        'vars' => array(
            'storm_links' => $links,
            'veryfyurl' => $veryfyurl,
            'urlstep2' => $stepurl2,
            'urlstep3' => $stepurl3,
            'verification_method'     => $configData['fields']['verification_method'],
            'metatag_approved_domain' => $metatag_approved_domain,
            'servertype'              => $configData['servertype'],
            'csr'                     => $configData['csr'],
            'firstname'               => $configData['firstname'],
            'lastname'                => $configData['lastname'],
            'orgname'                 => $configData['orgname'],
            'jobtitle'                => $configData['jobtitle'],
            'email'                   => $configData['email'],
            'address1'                => $configData['address1'],
            'address2'                => $configData['address2'],
            'city'                    => $configData['city'],
            'state'                   => $configData['state'],
            'postcode'                => $configData['postcode'],
            'country'                 => $configData['country'],
            'phonenumber'             => $configData['phonenumber'],
            'approveremail'           => $configData['approveremail'],
            'status'                  => $mod['status'],
            'domaintxt'               => $domaintxt,
            'metatag'                 => $metatag,
            'notordered'              => $notordered,
            'step1'                   => $step1,
            'step2'                   => $step2,
            'step3'                   => $step3,
            'retry'                   => $retry

        ),
  );


}


function LiquidWebSSL_ConfigOptions()
{
    $ModuleInformationClient = Smart_Load('modulesgarden_ModuleInformationClient');

    //CREATE TABLE
    $ModuleInformationClient::mysql_safequery('CREATE TABLE IF NOT EXISTS `mg_LiquidWebSSL_ssl`
    (
        `account_id` INT(11) NOT NULL,
        `uniq_id`    VARCHAR(16),
        `cert`       TEXT,
        `status`     VARCHAR(16),
        `verification_data`     TEXT ,
        `sslid`      VARCHAR(16),
        UNIQUE KEY(`account_id`)
    ) DEFAULT CHARACTER SET UTF8 ENGINE = MyISAM');


    //EMAIL FOR NEW CERTS
    $q   = $ModuleInformationClient::mysql_safequery('SELECT COUNT(*) as `count` FROM tblemailtemplates WHERE name = "LiquidWebSSL - SSL Certificate Configuration Required"');
    $row = mysql_fetch_assoc($q);
    if(!mysql_num_rows($q) || !$row['count'])
    {
        $ModuleInformationClient::mysql_safequery("INSERT INTO `tblemailtemplates` (`type` ,`name` ,`subject` ,`message` ,`fromname` ,`fromemail` ,`disabled` ,`custom` ,`language` ,`copyto` ,`plaintext` )VALUES ('product', 'LiquidWebSSL - SSL Certificate Configuration Required', 'SSL Certificate Configuration Required', '<p>Dear {\$client_name},</p><p>Thank you for your order for an SSL Certificate. Before you can use your certificate, it requires configuration which can be done at the URL below.</p><p>{\$ssl_configuration_link}</p><p>Instructions are provided throughout the process but if you experience any problems or have any questions, please open a ticket for assistance.</p><p>{\$signature}</p>', '', '', '', '', '', '', '0')");
    }

    $script = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR) + 1);
    $newVersion = LiquidWebSSLForWHMCS_getLatestVersion();
    if($newVersion && $script == 'configproducts.php' && $_GET['action'] != 'save')
    {
        echo '<p style="text-align: center;" class="infobox op_version">
            <span style="font-weight: bold">New version of Liquid Web module is available!</span>
            <span style="font-weight: bold"><br />Check this address to find out more <a target="_blank" href="'.$newVersion['site'].'">'.$newVersion['site'].'</a></span>
         </p>';
    }

    $config =   array(
            'Username'          =>  array
            (
                'Type'          =>  'text',
                'Size'          =>  '25',
            ),
            'Password'          =>  array
            (
                'Type'          =>  'password',
                'Size'          =>  '25'
            ),
        );

    return $config;
}

function LiquidWebSSL_CreateAccount($params)
{
	//global WHMCS system configuration
	global $CONFIG;

    try{
	$ModuleInformationClient = Smart_Load('modulesgarden_ModuleInformationClient');

        $r = mysql_fetch_assoc($ModuleInformationClient::mysql_safequery("SELECT * FROM tblsslorders WHERE serviceid = ?", array($params['serviceid'])));
        if($r){
            return 'Certificate already exists';
        }

            $ModuleInformationClient::mysql_safequery('INSERT INTO `tblsslorders` (`id`, `userid`, `serviceid`, `remoteid`, `module`, `certtype`, `configdata`, `completiondate`, `status`)
            VALUES ("", ?, ?, ?, ?, ?, ?, ?, ?)', array
            (
                $params['clientsdetails']['userid'],
                $params['serviceid'],
                '',
                'LiquidWebSSL',
                '',
                '',
                '',
                'Awaiting Configuration'
            )
        );

        $id  = md5(mysql_insert_id());
        $url = '<a href="'.$CONFIG["SystemURL"].'/configuressl.php?cert='.$id.'">'.$CONFIG["SystemURL"].'/configuressl.php?cert='.$id.'</a>';

        $ModuleInformationClient::mysql_safequery('INSERT INTO mg_LiquidWebSSL_ssl(account_id, uniq_id,cert,status,verification_data, sslid ) VALUES(?,?,?,?,?,?)', array($params['serviceid'], null , $id, null, null, null ));

        sendMessage("LiquidWebSSL - SSL Certificate Configuration Required",
            $params["serviceid"],array(
                "ssl_configuration_link"    =>  $url
            )
        );



    }catch(Exception $e){
            global $LiquidWebSSLConfig;
            logModuleCall('LiquidWebSSL','CreateAccount','SCRIPT ERRORS FOUND:','',$e->getMessage(),array());
            if(!$LiquidWebSSLConfig['debug_mode_errors']) {return  'Script errors occured. Please contact with support.';}
            else {return  'Script errors occured: '.$e->getMessage();}
    }



    return "success";
}

function LiquidWebSSL_ResendEmail($params)
{
	//global WHMCS system configuration
    global $CONFIG;

    try {
	$ModuleInformationClient = Smart_Load('modulesgarden_ModuleInformationClient');

        $r      = mysql_fetch_assoc($ModuleInformationClient::mysql_safequery("SELECT * FROM tblsslorders WHERE serviceid = ?", array($params['serviceid'])));
        if (!$r){
            return "No SSL Order exists for this product";
        }
        $link = $CONFIG['SystemURL'] . "/configuressl.php?cert=" . md5($r['id']);
        $link = "<a href=\"{$link}\">{$link}</a>";
        $res  = sendMessage("LiquidWebSSL - SSL Certificate Configuration Required", $params['serviceid'], array(
            "ssl_configuration_link" => $link
        ));

    }catch(Exception $e){
            global $LiquidWebSSLConfig;
            logModuleCall('LiquidWebSSL','ResendEmail','SCRIPT ERRORS FOUND:','',$e->getMessage(),array());
            if(!$LiquidWebSSLConfig['debug_mode_errors']) {return  'Script errors occured. Please contact with support.';}
            else {return  'Script errors occured: '.$e->getMessage();}
    }
    return "success";
}

/**
 * First step
 * @param type $params
 * @return boolean
 */
function LiquidWebSSL_SSLStepOne($params)
{
    global $CONFIG;
    $ModuleInformationClient = Smart_Load('modulesgarden_ModuleInformationClient');
    $r  = mysql_fetch_assoc($ModuleInformationClient::mysql_safequery("SELECT * FROM tblsslorders WHERE serviceid = ?", array($params['serviceid'])));
    $redirect = $CONFIG['SystemURL'] . "/clientarea.php?action=productdetails&id=".$params['serviceid'];

    if(!empty($r['configdata']) && strpos($_SERVER['HTTP_REFERER'], "/clientarea.php?action=productdetails&amp;id=".$params['serviceid']) === false ){
      header("Location: ".$redirect."&error=s1filled");die();
    }


    $fields = array();
    $fields['additionalfields']['Verification']['verification_method'] = array(
          'FriendlyName'  => 'Verification method',
              "Type"          => "dropdown",
              "Size"          => "30",
              "Options"       => implode(",",array_merge(array('Please choose one...'),
                                 LiquidWebSSL_getVerificationMethod())),
              "Description"   => "",
              "Default"       => "",
          'Required'      => true);

   $dsc = '<script type="text/javascript">'.LiquidWebSSL__loadAsset('js/firststep.tpl.js', array(
          'metatag_approved_domain' => 'http://'.$params['domain']
   )).'</script>';
   $fields['additionalfields']['Verification']['metatag_approved_domain'] = array(
          'FriendlyName'  => 'URL to the root of the approved domain',
              "Type"          => "text",
              "Size"          => "30",
              "Description"   => 'A valid http or https uri (i.e. http://www.example.com)'. $dsc,
              "Default"       => "",
          'Required'      => false);

  /*	//api doesnt support this
    $fields['additionalfields']['Verification']['approver_email'] = array(
          'FriendlyName'  => 'Approver E-Mail (optional)',
              "Type"          => "text",
              "Size"          => "30",
              "Description"   => "",
              "Default"       => "",
          'Required'      => false); */


    return $fields;
}


/**
 * Second step
 * @param type $params
 * @return array
 * updated by Piotr kozdęba <piotr.ko@modulesgarden.com>
 */

function LiquidWebSSL_SSLStepTwo($params)
{

    //global WHMCS system configuration
    global $CONFIG;
    $emails = array('approveremails' => array());


    try {

      $ModuleInformationClient = Smart_Load('modulesgarden_ModuleInformationClient');
      $StormOnDemandSSLCert	 = Smart_Load('StormOnDemand_bleed_StormOnDemandSSLCetrificate');

      $row = mysql_fetch_assoc($ModuleInformationClient::mysql_safequery("SELECT certtype FROM tblsslorders WHERE MD5(id) = ?", array($_REQUEST['cert'])));
      if(!$row){
          return array('error' => 'No SSL Order exists for this product');
      }


      $domain   = '';
      $csrEmail = '';
       //Fix
       if(!isset($params['configdata']['csr'])){
        $row2 = mysql_fetch_assoc($ModuleInformationClient::mysql_safequery("SELECT * FROM tblsslorders WHERE MD5(id) = ?", array($_REQUEST['cert'])));
        $confData = unserialize($row2['configdata']);

        $params['configdata']['csr'] = $confData['csr'];
       }

        if(isset($params['configdata']['csr'])){

            $csrfrompost = $_POST['csr'];//csr validation (WHMCS have some issues with transfering csr between steps)
            if (trim($csrfrompost)!='' && trim($params['configdata']['csr']) != trim($csrfrompost))
            {
                return array(
                    'error' => 'Certificate mismatch. Please try again.',
                );
            }


            $params['configdata']['csr'] = trim($params['configdata']['csr']);
            $data     = openssl_csr_get_subject($params['configdata']['csr']);

            $domain   = $data['CN'];
            $csrEmail = $data['emailAddress'];




            if(!$data || !$domain){
                return array(
                            'error' => 'CSR is invalid',
                );
            }

            if (preg_match('/^\s*\*/i',$domain))
            {
                return array(
                    'error' => 'Wildcard is not supported',
                );
            }
        }else{
                    return array(
                            'error' => 'CSR is empty',
                    );
        }


            $emails =  array
        (
            'approveremails'   =>   array(
               /* 1 =>  $params['clientsdetails']['email'], /**/ //api doesnt support this
                2 => 'admin@'.$domain,
                3 => 'administrator@'.$domain,
                4 => 'postmaster@'.$domain,
                5 => 'hostmaster@'.$domain,
                6 => 'postmaster@'.$domain,
                7 => 'webmaster@'.$domain,

                8  => 'admin@www.'.$domain,
                9  => 'administrator@www.'.$domain,
                10 => 'postmaster@www.'.$domain,
                11 => 'hostmaster@www.'.$domain,
                12 => 'postmaster@www.'.$domain,
                13 => 'webmaster@www.'.$domain,
             )
        );

	/*//api doesnt support this
    if(!empty($params['fields']['approver_email'])){

		$params['fields']['approver_email'] = trim($params['fields']['approver_email']);
		if(filter_var($params['fields']['approver_email'], FILTER_VALIDATE_EMAIL)){
			if(!in_array($params['fields']['approver_email'],$emails['approveremails'])){
				$emails['approveremails'] = array_merge(array($params['fields']['approver_email']),
													    $emails['approveremails']);
			}
		}else{
			return array('error' => 'Invalid Approver E-Mail');
		}
    }*/

    if(!$params['fields']['verification_method'] || !in_array($params['fields']['verification_method'],LiquidWebSSL_getVerificationMethod())){
        return array('error' => 'You must select verification method');
    }

    if($params['fields']['verification_method'] === 'email' && !$csrEmail){
            return array(
                'error' => 'CSR is invalid(Email Address is empty)',
            );
    }

    if(($params['fields']['verification_method'] === 'metatag') &&
        (!preg_match($StormOnDemandSSLCert::DOMAIN_WITH_HTTP_OR_HTTPS,$params['fields']['metatag_approved_domain']))){
             return array(
                 'error' => 'URL to the root of the approved domain must be a valid http or https uri (i.e. http://www.example.com).',
             );
    }


    }catch(StormOnDemandSSLCetrificateException $e){
            global $LiquidWebSSLConfig;
            logModuleCall('LiquidWebSSL','SSLStepTwo','REQUEST ERRORS FOUND:','',print_r($e->getErrors(),true).'<br>'.$e->getMessage(),array());
            if(!$LiquidWebSSLConfig['debug_mode_errors']) {return  array( 'error'=>'Request errors occured. Please contact with support.');}
            else {return  array('error'=>'Request errors occured: '.print_r($e->getErrors(),true).'<br>'.$e->getMessage());}

    }catch(Exception $e){
            global $LiquidWebSSLConfig;
            logModuleCall('LiquidWebSSL','SSLStepTwo','SCRIPT ERRORS FOUND:','',$e->getMessage(),array());
            if(!$LiquidWebSSLConfig['debug_mode_errors']) {return  array( 'error'=>'Script errors occured. Please contact with support.');}
            else {return  array('error'=>'Script errors occured: '.$e->getMessage());}

    }

    return $emails;
}

/**
 * Third step
 * @param type $params
 * @return boolean
 * updated by Piotr kozdęba <piotr.ko@modulesgarden.com>
 */
function LiquidWebSSL_SSLStepThree($params)
{

    //global WHMCS system configuration
    global $CONFIG;
    $SSLId = null;
    $redirect = $CONFIG['SystemURL'] . "/clientarea.php?action=productdetails&id=".$params['serviceid'];

    try{

        $ModuleInformationClient = Smart_Load('modulesgarden_ModuleInformationClient');

        $c = Smart_Load('StormOnDemand_bleed_StormOnDemandSSLCetrificate');
        $StormOnDemandSSL = new $c($params['configoption1'], $params['configoption2']);

        //Prepare CSR
        $csr = trim($params['configdata']['csr']);
        $csr = trim(preg_replace('/(.*)CERTIFICATE(.*)/','',$csr));
        $csr = "-----BEGIN CERTIFICATE REQUEST-----\n".$csr."\n-----END CERTIFICATE REQUEST-----";
        $csr = trim($csr);

            $csrArray = openssl_csr_get_subject($csr);
            if(!$csrArray){
                    header("Location: ".$redirect."&error=CSR is invalid");die();  /* Redirect browser */
                    /*
                    return array(
                            'error' => 'CSR is invalid',
                    );
                    */
            }
        //Chcek if order exist
        $mod  = mysql_fetch_assoc($ModuleInformationClient::mysql_safequery("SELECT * FROM mg_LiquidWebSSL_ssl WHERE account_id = ?", array($params['serviceid'])));

        if( $_GET['order'] != 'approved' || $mod['uniq_id'] == '' || $mod['uniq_id'] == null ){

          $apiRes = $StormOnDemandSSL->order($csr, $csrArray['CN'], LiquidWebSSL_DefaultDuration, $params['fields']['verification_method']);

          logModuleCall('LiquidWebSSL','SSLStepThree',$StormOnDemandSSL->getLastRequest(),'',$StormOnDemandSSL->getLastResponse(),array());

          $error  = $StormOnDemandSSL->getError();
          if($error){
                  header("Location: ".$redirect."&error=orderfiled");die();  /* Redirect browser */
                  /*
                  return array(
                          'error' => 'Api error: '.$error,
                  );
                  */
          }
          $SSLId = $apiRes['id'];
          $ModuleInformationClient::mysql_safequery('UPDATE mg_LiquidWebSSL_ssl SET uniq_id=?, cert=?, status=?,verification_data=?, sslid=?  WHERE account_id = ?', array( $apiRes['uniq_id'] , $_GET['cert'],$apiRes['verification_status'],json_encode($apiRes['verification_data']), $apiRes['id'],$params['serviceid'] ));
          //$ModuleInformationClient::mysql_safequery('INSERT INTO mg_LiquidWebSSL_ssl(account_id, uniq_id,cert,status,verification_data, sslid ) VALUES(?,?,?,?,?,?)', array($params['serviceid'], $apiRes['uniq_id'] , $_GET['cert'],$apiRes['verification_status'],json_encode($apiRes['verification_data']), $apiRes['id'] ));
        }else{
          $r  = mysql_fetch_assoc($ModuleInformationClient::mysql_safequery("SELECT * FROM tblsslorders WHERE serviceid = ?", array($params['serviceid'])));

          $configData                                  = unserialize($r['configdata']);
          $csrArray['CN']                              = $configData['jobtitle'];
          $params['fields']['metatag_approved_domain'] = $configData['fields']['metatag_approved_domain'];
          $params['fields']['verification_method']     = $configData['fields']['verification_method'];
          $params['approveremail']                     = $configData['approveremail'];
          $SSLId                                       = $mod['sslid'];
          $apiRes['uniq_id']                           = $mod['uniq_id'];

          $bkUrl = str_replace('http://','',$csrArray['CN']);
          $bkUrl = 'http://'.$bkUrl;


          //send order to verify
            $apiVer = $StormOnDemandSSL->verfiy(              $params['approveremail'], //email
                                                              $csrArray['CN'],          //name
                                                              null,                     //subaccnt if uniq_id is not given
                                                              $apiRes['uniq_id'],       //uniq_id
                                                              ($params['fields']['metatag_approved_domain'])?$params['fields']['metatag_approved_domain']:$bkUrl //url
                                                              );

            logModuleCall('LiquidWebSSL','SSLStepThree',$StormOnDemandSSL->getLastRequest(),'',$StormOnDemandSSL->getLastResponse(),array());

            $error  = $StormOnDemandSSL->getError();
            if($error){
              header("Location: ".$redirect."&error=unexpected");die(); /* Redirect browser */
              /*
                    return array(
                            'error' => 'Api error: '.$error,
                    );
              */
            }
          // Change Status
          $ModuleInformationClient::mysql_safequery("UPDATE mg_LiquidWebSSL_ssl SET status = ? WHERE account_id=?", array($apiVer['verification_status'] , $params['serviceid']));

          if(strcmp($params['fields']['verification_method'], 'email') === 0){
                  //Note: If the verification method was email, an additional call to confirmVerified is needed to refresh the data cache.
                  $StormOnDemandSSL->confirmVerified(null,$apiRes['uniq_id']);

                  logModuleCall('LiquidWebSSL','SSLStepThree',$StormOnDemandSSL->getLastRequest(),'',$StormOnDemandSSL->getLastResponse(),array());

                  $error  = $StormOnDemandSSL->getError();
                  if($error){

                          header("Location: ".$redirect."&error=unexpected");die(); /* Redirect browser */
                          /*
                          return array(
                                  'error' => 'Api error: '.$error,
                          );
                          */
                  }
          }
          header("Location: ".$redirect."&success=1");die(); /* Redirect browser */
        }


    }catch(StormOnDemandSSLCetrificateException $e){
            global $LiquidWebSSLConfig;
            logModuleCall('LiquidWebSSL','SSLStepThree','REQUEST ERRORS FOUND:','',print_r($e->getErrors(),true).'<br>'.$e->getMessage(),array());
            if(!$LiquidWebSSLConfig['debug_mode_errors']) {
              header("Location: ".$redirect."&error=unexpected");die(); /* Redirect browser */
              //return  array( 'error'=>'Request errors occured. Please contact with support.');
            }else {
              header("Location: ".$redirect."&error=unexpected");die(); /* Redirect browser */
              //return  array('error'=>'Request errors occured: '.print_r($e->getErrors(),true).'<br>'.$e->getMessage());
            }

    }catch(Exception $e){
            global $LiquidWebSSLConfig;
            logModuleCall('LiquidWebSSL','SSLStepThree','SCRIPT ERRORS FOUND:','',$e->getMessage(),array());
            if(!$LiquidWebSSLConfig['debug_mode_errors']) {
               header("Location: ".$redirect."&error=unexpected");die(); /* Redirect browser */
              //return  array( 'error'=>'Script errors occured. Please contact with support.');
            }else {
               header("Location: ".$redirect."&error=unexpected");die(); /* Redirect browser */
              //return  array('error'=>'Script errors occured: '.$e->getMessage());
            }

    }

    header("Location: ".$redirect); /* Redirect browser */
    /*
    return array(
        'remoteid' => $SSLId,
        'domain'   => $params['domain'],
    );
    */
}

function LiquidWebSSL_TerminateAccount($params){

	//global WHMCS system configuration
    global $CONFIG;
    try {
	$ModuleInformationClient = Smart_Load('modulesgarden_ModuleInformationClient');

    $uniqIdQ = $ModuleInformationClient::mysql_safequery('SELECT * FROM mg_LiquidWebSSL_ssl WHERE account_id = ? LIMIT 1', $params['serviceid']);
    $uniqId  = mysql_fetch_assoc($uniqIdQ);

    if($uniqId['uniq_id'] == null || $uniqId['uniq_id'] == ''){
        return 'uniq_id doesnt exists, probably user did not finish the order';
    }

    $sslOrderQ = $ModuleInformationClient::mysql_safequery('SELECT * FROM tblsslorders  WHERE serviceid = ? AND userid = ? LIMIT 1', array(
    $params['serviceid'],
    $params['clientsdetails']['userid']
    ));
    $sslOrder  = mysql_fetch_assoc($sslOrderQ);

    if(!$sslOrder){
        return 'SSL order doesnt exists for this service_id';
    }

    $c = Smart_Load('StormOnDemand_bleed_StormOnDemandSSLCetrificate');
    $StormOnDemandSSL = new $c($params['configoption1'], $params['configoption2']);

    $StormOnDemandSSL->revoke(null, $uniqId['uniq_id']);

        logModuleCall('LiquidWebSSL','Terminate',$StormOnDemandSSL->getLastRequest(),'',$StormOnDemandSSL->getLastResponse(),array());

    $error = $StormOnDemandSSL->getError();
    if($error){

            while($er = $StormOnDemandSSL->getError()){
                    $error .= '<br>'.$er;
            }
        return $error;
    }


    $ModuleInformationClient::mysql_safequery('DELETE  FROM mg_LiquidWebSSL_ssl WHERE account_id = ? AND uniq_id = ? LIMIT 1', array(
        $params['serviceid'],
        $uniqId['uniq_id'],
    ));
    $ModuleInformationClient::mysql_safequery('DELETE FROM tblsslorders WHERE serviceid = ? AND userid = ? LIMIT 1', array($params['serviceid'], $params['clientsdetails']['userid']));

    }catch(StormOnDemandSSLCetrificateException $e){
            global $LiquidWebSSLConfig;
            logModuleCall('LiquidWebSSL','Terminate','REQUEST ERRORS FOUND:','',print_r($e->getErrors(),true).'<br>'.$e->getMessage(),array());
            if(!$LiquidWebSSLConfig['debug_mode_errors']) {return  'Request errors occured. Please contact with support.';}
            else {return  'Request errors occured: '.print_r($e->getErrors(),true).'<br>'.$e->getMessage();}
    }catch(Exception $e){
            global $LiquidWebSSLConfig;
            logModuleCall('LiquidWebSSL','Terminate','SCRIPT ERRORS FOUND:','',$e->getMessage(),array());
            if(!$LiquidWebSSLConfig['debug_mode_errors']) {return  'Script errors occured. Please contact with support.';}
            else {return  'Script errors occured: '.$e->getMessage();}
    }

    return "success";
}

function LiquidWebSSL_AdminCustomButtonArray()
{
    $buttonarray = array(
        "Resend Configuration Email"    => "resendEmail",
        "Resend Approver Email"         => "resendApprover",
        'Renew Certificate'       		=> 'renew',
    );
    return $buttonarray;
}


function LiquidWebSSL_ClientAreaCustomButtonArray()
{
    return array
    (
   		'Get Certificate' => 'userGetCertificate',
    );
}

function LiquidWebSSL_UserGetCertificate($params){

    //global WHMCS system configuration
    global $CONFIG;

    $vars = array(
        'error' => '',
        'sbs'=>'',
        'subpage'=>dirname(__FILE__).DS.'assets'.DS.'tpl'.DS.'clientarea'.DS.'getcertificate.tpl',
        'your_certificate'=>''
    );
    try {
        $ModuleInformationClient = Smart_Load('modulesgarden_ModuleInformationClient');

        $sslOrderQ = $ModuleInformationClient::mysql_safequery('SELECT * FROM tblsslorders  WHERE serviceid = ? AND userid = ? LIMIT 1', array(
                $params['serviceid'],
                $params['clientsdetails']['userid']
        ));
        $sslOrder  = mysql_fetch_assoc($sslOrderQ);

        if(!$sslOrder){
                return 'SSL order doesnt exists for this service_id';
        }

        $sslOrderConfigData = unserialize($sslOrder['configdata']);

        //$vars['sbs']		 		= $avSbs;
        $vars['your_certificate']   =  $sslOrderConfigData['csr'];


    }catch(Exception $e){
            global $LiquidWebSSLConfig;
            logModuleCall('LiquidWebSSL','UserGetCertificate','SCRIPT ERRORS FOUND:','',$e->getMessage(),array());
            if(!$LiquidWebSSLConfig['debug_mode_errors']) {$vars ['error']= 'Script errors occured. Please contact with support.';}
            else {$vars ['error']=  'Script errors occured: '.$e->getMessage();}
    }
    $pagearray = array(
        'templatefile'  => 'assets'.DS.'tpl'.DS.'clientarea'.DS.'getcertificate',
        'breadcrumb'    =>  ' > <a href="#" onclick="return false;">Get Certificate</a>',
        'vars'        =>  $vars,
    );

    return $pagearray;
}


function LiquidWebSSL_Renew($params){

	//global WHMCS system configuration
    global $CONFIG;
    try {
    $ModuleInformationClient = Smart_Load('modulesgarden_ModuleInformationClient');

    $uniqIdQ = $ModuleInformationClient::mysql_safequery('SELECT * FROM mg_LiquidWebSSL_ssl WHERE account_id = ?', $params['serviceid']);
    $uniqId  = mysql_fetch_assoc($uniqIdQ);

    if(!$uniqId){
        return 'uniq_id doesnt exists, probably user did not finish the order';
    }

    $c = Smart_Load('StormOnDemand_bleed_StormOnDemandSSLCetrificate');
    $StormOnDemandSSL = new $c($params['configoption1'], $params['configoption2']);

        $StormOnDemandSSL->renew(1,null, $uniqId['uniq_id']);

        logModuleCall('LiquidWebSSL','Renew',$StormOnDemandSSL->getLastRequest(),'',$StormOnDemandSSL->getLastResponse(),array());

    }catch(StormOnDemandSSLCetrificateException $e){
            global $LiquidWebSSLConfig;
            logModuleCall('LiquidWebSSL','Renew','REQUEST ERRORS FOUND:','',print_r($e->getErrors(),true).'<br>'.$e->getMessage(),array());
            if(!$LiquidWebSSLConfig['debug_mode_errors']) {return  'Request errors occured. Please contact with support.';}
            else {return  'Request errors occured: '.print_r($e->getErrors(),true).'<br>'.$e->getMessage();}
    }catch(Exception $e){
            global $LiquidWebSSLConfig;
            logModuleCall('LiquidWebSSL','Renew','SCRIPT ERRORS FOUND:','',$e->getMessage(),array());
            if(!$LiquidWebSSLConfig['debug_mode_errors']) {return  'Script errors occured. Please contact with support.';}
            else {return  'Script errors occured: '.$e->getMessage();}
    }

    return 'success';
}

function LiquidWebSSL_ResendApprover($params){

	//global WHMCS system configuration
    global $CONFIG;

    try {
    $ModuleInformationClient = Smart_Load('modulesgarden_ModuleInformationClient');

    $uniqIdQ = $ModuleInformationClient::mysql_safequery('SELECT * FROM mg_LiquidWebSSL_ssl WHERE account_id = ?', $params['serviceid']);
    $uniqId  = mysql_fetch_assoc($uniqIdQ);

    $sslOrderQ = $ModuleInformationClient::mysql_safequery('SELECT * FROM tblsslorders WHERE serviceid = ?', $params['serviceid']);
    $sslOrder  = mysql_fetch_assoc($sslOrderQ);

    if(!$uniqId || !$sslOrder || !$sslOrder['configdata']){
        return 'uniq_id doesnt exists, probably user did not finish the order';
    }
    $sslOrderConfigData = unserialize($sslOrder['configdata']);

    $c = Smart_Load('StormOnDemand_bleed_StormOnDemandSSLCetrificate');
    $StormOnDemandSSL = new $c($params['configoption1'], $params['configoption2']);

        //Prepare CSR
        $csr = trim($sslOrderConfigData['csr']);
        $csr = trim(preg_replace('/(.*)CERTIFICATE(.*)/','',$csr));
        $csr = "-----BEGIN CERTIFICATE REQUEST-----\n".$csr."\n-----END CERTIFICATE REQUEST-----";
        $csr = trim($csr);

    $csrArray = openssl_csr_get_subject($csr);
    if(!$csrArray){
        return 'CSR is invalid';
    }

        $bkUrl = str_replace('http://','',$csrArray['CN']);
        $bkUrl = 'http://'.$bkUrl;

        //send order to verify
        $StormOnDemandSSL->verfiy($sslOrderConfigData['approveremail'],
                                                          $csrArray['CN'],
                                                          null,
                                                          $uniqId['uniq_id'],
                                                          ($sslOrderConfigData['fields']['metatag_approved_domain'])?$sslOrderConfigData['fields']['metatag_approved_domain']:$bkUrl
                                                          );
        logModuleCall('LiquidWebSSL','ResendApprover',$StormOnDemandSSL->getLastRequest(),'',$StormOnDemandSSL->getLastResponse(),array());

        if(strcmp($sslOrderConfigData['fields']['verification_method'], 'email') === 0){
                //Note: If the verification method was email, an additional call to confirmVerified is needed to refresh the data cache.
                $StormOnDemandSSL->confirmVerified(null,$uniqId['uniq_id']);

                logModuleCall('LiquidWebSSL','ResendApprover',$StormOnDemandSSL->getLastRequest(),'',$StormOnDemandSSL->getLastResponse(),array());

                $error  = $StormOnDemandSSL->getError();
                if($error){
                        return array(
                                'error' => 'Api error: '.$error,
                        );
                }
        }

    }catch(StormOnDemandSSLCetrificateException $e){
            global $LiquidWebSSLConfig;
            logModuleCall('LiquidWebSSL','ResendApprover','REQUEST ERRORS FOUND:','',print_r($e->getErrors(),true).'<br>'.$e->getMessage(),array());
            if(!$LiquidWebSSLConfig['debug_mode_errors']) {return  'Request errors occured. Please contact with support.';}
            else {return  'Request errors occured: '.print_r($e->getErrors(),true).'<br>'.$e->getMessage();}
    }catch(Exception $e){
            global $LiquidWebSSLConfig;
            logModuleCall('LiquidWebSSL','ResendApprover','SCRIPT ERRORS FOUND:','',$e->getMessage(),array());
            if(!$LiquidWebSSLConfig['debug_mode_errors']) {return  'Script errors occured. Please contact with support.';}
            else {return  'Script errors occured: '.$e->getMessage();}
    }

    return 'success';
}

function LiquidWebSSL_getVerificationMethod(){
	return array(
		'auto',
		'dns',
		'email',
		'metatag',
	);
}


function LiquidWebSSL__loadAsset($assetPath, $vars = array())
{
	$str = '';
	if(file_exists(dirname(__FILE__).DS.'assets'.DS.$assetPath)){
		$str = file_get_contents(dirname(__FILE__).DS.'assets'.DS.$assetPath);

		if(!empty($vars)){
			foreach($vars as $k => $v){

				if(is_array($v)){
					$v = json_encode($v);
				}

				$str = str_replace('{$'.$k.'}', $v,$str);
			}
		}

	}
	return $str;
}


/****************** MODULE INFORMATION ************************/

//Register instance
LiquidWebSSLForWHMCS_registerInstance();
function LiquidWebSSLForWHMCS_registerInstance()
{
    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   "Liquid Web SSL For WHMCS";
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   LIQUID_WEB_SSL_VERSION;
    //Encryption key
    $moduleKey          =   "4qFumq5J6eAEheFysT9N10fdeGaoKudmuPTXGPUiBpbhya05vfBWMN2mw6VZh1U5";
    //Server URL
    //$serverUrl          =   "https://www.liquidweb.com/manage/modules/addons/ModuleInformation/server.php";


    /***************************************************
     *                      DO NOT TOUCH!
     ***************************************************/

    //Load API Class
    require_once ROOTDIR.DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."modulesgarden".DIRECTORY_SEPARATOR."class.ModuleInformationClient.php";

    //Is Already Registered?
    $currentVersion = ModuleInformationClient::getLocalVersion($moduleName);
    if($currentVersion == $moduleVersion)
    {
        return false;
    }

    //Create Client Class
    $client = new ModuleInformationClient($moduleName, $moduleKey);

    //Register current instance
    $ret = $client->registerModuleInstance($moduleVersion, $_SERVER["SERVER_ADDR"], $_SERVER["SERVER_NAME"]);

    if($ret->status == 1)
    {
        ModuleInformationClient::clearCache($moduleName, "getLatestModuleVersion");
        ModuleInformationClient::setLocalVersion($moduleName, $moduleVersion);
    }
}

function LiquidWebSSLForWHMCS_getLatestVersion()
{
    /****************************************************
     *              EDIT ME
     ***************************************************/
    //Set up name for your module.
    $moduleName         =   "Liquid Web SSL For WHMCS";
    //Set up module version. You should change module version every time after updating source code.
    $moduleVersion      =   LIQUID_WEB_SSL_VERSION;
    //Encryption key
    $moduleKey          =   "4qFumq5J6eAEheFysT9N10fdeGaoKudmuPTXGPUiBpbhya05vfBWMN2mw6VZh1U5";
    //Server URL
    //$serverUrl          =   "https://www.liquidweb.com/manage/modules/addons/ModuleInformation/server.php";


    /***************************************************
     *                      DO NOT TOUCH!
     ***************************************************/

    //Load API Class
    require_once ROOTDIR.DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."modulesgarden".DIRECTORY_SEPARATOR."class.ModuleInformationClient.php";

    //Is Already Registered?
    $currentVersion = ModuleInformationClient::getLocalVersion($moduleName);
    if(!$currentVersion)
    {
        return false;
    }

    //Is current instance registered?
    if($currentVersion != $moduleVersion)
    {
        return false;
    }

    //Create Client Class
    $client = new ModuleInformationClient($moduleName, $moduleKey);

    //Get Information about latest version
    $res = $client->getLatestModuleVersion();

    if(!$res)
    {
        return false;
    }

    if($res->data->version == $moduleVersion)
    {
        return false;
    }

    return array
    (
        "version"   =>  $res->data->version,
        "site"      =>  $res->data->site,
    );
}