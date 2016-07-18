<?php


if(!class_exists('StormOnDemand_Helper'))
{
    class StormOnDemand_Helper
    {

        const StormOnDemandPrivateParentStormOnDemandServerType         = 'StormOnDemandPrivateParent';
        const StormOnDemandStormOnDemandServerType                      = 'StormOnDemand';
        const StormOnDemandSBSStormOnDemandServerType                   = 'StormOnDemandSBS';

        const LiquidWebPPLiquidWebServerType                            = 'LiquidWebPrivateParent';
        const LiquidWebLiquidWebServerType                              = 'LiquidWeb';
        const LiquidWebSBSLiquidWebServerType                           = 'LiquidWebSBS';

        const SBSServerType                                             = 'SBSModule';

		private static $_vps_type_aliases = array(
			'StormOnDemand' => 'mg_storm_on_demand',
			'LiquidWeb' 	=> 'mg_liquid_web',
		);

		public static function getAllHostingIdsByType($idUser, $serverType, $onlyUniqId = true){

			$results = array();
			$q 		 = mysql_query('SELECT * FROM tblproducts WHERE servertype = "'.$serverType.'"');

			while(($row = mysql_fetch_assoc($q))){
				$results []= (int)$row['id'];
			}

			$hostings = array();
			$q        = mysql_query('SELECT * FROM tblhosting WHERE packageid IN('.join(',',$results).') AND userid = "'.$idUser.'"');

			while(($row = mysql_fetch_assoc($q))){
				if($onlyUniqId){
					$hostings []= (int)$row['id'];
				}else{
					$hostings []= $row;
				}
			}

			return $hostings;
		}

		public static function getAllVpsUserUniqIds($idUser, $vpsType, $onlyUniqId = true){

			if(isset(self::$_vps_type_aliases[(string)$vpsType])){

				$allProducts = self::getAllHostingIdsByType($idUser, $vpsType);
				if(empty($allProducts)){
					return array();
				}

				$tab = self::$_vps_type_aliases[(string)$vpsType];
				$q   = mysql_query('SELECT * from tblhosting JOIN '.$tab.' ON id = hosting_id WHERE userid = "'.$idUser.'" AND uniq_id IS NOT NULL AND uniq_id != "" AND id in('.join(',',$allProducts).')');

				$uniqs = array();
				while(($row = mysql_fetch_assoc($q))){
					if($onlyUniqId){
						$uniqs []= $row['uniq_id'];
					}else{
						$uniqs []= array(
							'hosting_id' => (int)$row['id'],
							'uniq_id' 	 => $row['uniq_id'],
							'package' 	 => $row,
						);
					}
				}
				return $uniqs;
			}else{
				return array();
			}
		}

		public static function getAllPrivateParentUserUniqIds($idUser, $ppType, $onlyUniqId = true){

			if($onlyUniqId){
				$allProductsIds = self::getAllHostingIdsByType($idUser, $ppType);
			}else{

				$r 			 = self::getAllHostingIdsByType($idUser, $ppType,false);
				$allProducts = array();

				if(!empty($r)){
					foreach($r as $row){
						$allProductsIds 				[]= (int)$row['id'];
						$allProducts[(int)$row['id']] 	  = $row;
					}
				}
			}
			if(empty($allProductsIds)){
				return array();
			}

			$qAllUniqsId  = mysql_query('SELECT cf.id, cf.fieldname, cfv.relid, cfv.value
		                        FROM tblcustomfields AS cf
		                        JOIN tblcustomfieldsvalues AS cfv ON cfv.fieldid = cf.id
		                        WHERE cf.type = "product" AND cfv.relid IN('.join(',', $allProductsIds).') AND cfv.value IS NOT NULL AND cfv.value != "" AND cf.fieldname LIKE "%uniq_id%"');

			$rAllUniqsId  = array();
			while(($row = mysql_fetch_assoc($qAllUniqsId))){
				if($onlyUniqId){
					$rAllUniqsId []= $row['value'];
				}else{
					$rAllUniqsId []= array(
					    'hosting_id' => (int)$row['relid'],
						'uniq_id'    => $row['value'],
						'package'    => $row,
					);
				}
			}

			if(!$onlyUniqId && !empty($rAllUniqsId)){
				foreach($rAllUniqsId as $k => $row){
					$row['package']['relid'] = (int)$row['package']['relid'];
					if(isset($allProducts[$row['package']['relid']])){
						$rAllUniqsId[$k]['package'] = array_merge($row['package'], $allProducts[$row['package']['relid']]);
					}
				}
			}
			return $rAllUniqsId;
		}

		public static function addUniqIdToCustomFields($idHosting, $idUniq){

			$qField = mysql_query('SELECT cs.id FROM tblhosting as ths JOIN tblcustomfields as cs ON ths.packageid = cs.relid WHERE ths.id = "'.$idHosting.'" AND cs.fieldname LIKE "%uniq_id%" LIMIT 1');
			$rField = mysql_fetch_assoc($qField);

			if(empty($rField)){
				return false;
			}

			$qRes = mysql_query('SELECT * FROM tblcustomfieldsvalues WHERE fieldid = "'.$rField['id'].'" AND relid = "'.$idHosting.'" LIMIT 1');
			$rRes = mysql_fetch_assoc($qRes);

			if(empty($rRes)){
	    		mysql_query("INSERT INTO tblcustomfieldsvalues (`fieldid`, `relid`, `value`) VALUES ('".$rField['id']."', '".$idHosting."', '".$idUniq."')");
			}else{
				mysql_query('UPDATE tblcustomfieldsvalues SET value = "'.$idUniq.'" WHERE fieldid = "'.$rField['id'].'" AND relid = "'.$idHosting.'" LIMIT 1');
			}

			return true;

		}

		public static function isHostingCloneServerField($idField, $idHosting, $hostingType){

			$q = mysql_query('SELECT pr.servertype from tblhosting as ths JOIN tblproducts as pr ON ths.packageid = pr.id WHERE ths.id = '.$idHosting);
			$r = mysql_fetch_assoc($q);

			if(strcmp($r['servertype'],$hostingType) !== 0){
				return false;
			}

			$q = mysql_query('SELECT *FROM tblcustomfields WHERE id = "'.$idField.'" AND fieldname LIKE "%Clone From Server%"');
			$r = mysql_fetch_assoc($q);

			if(!empty($r)){
				return true;
			}

			return false;
		}

		public static function isLiquidWebVpsCloneServerField($idField, $idHosting){
			return self::isHostingCloneServerField($idField, $idHosting,self::LiquidWebLiquidWebServerType);
		}

		public static function isLiquidWebPrivateParentCloneServerField($idField, $idHosting){
			return self::isHostingCloneServerField($idField, $idHosting,self::LiquidWebPPLiquidWebServerType);
		}

		public static function isStormOnDemandVpsCloneServerField($idField, $idHosting){
			return self::isHostingCloneServerField($idField, $idHosting,self::StormOnDemandStormOnDemandServerType);
		}

		public static function isStormOnDemandPrivateParentCloneServerField($idField, $idHosting){
			return self::isHostingCloneServerField($idField, $idHosting,self::StormOnDemandPrivateParentStormOnDemandServerType);
		}

		public static function getAllStormOnDemandUniqIds($idUser){

			$parentIds  = self::getAllPrivateParentUserUniqIds($idUser, self::StormOnDemandPrivateParentStormOnDemandServerType);
			$vpsIds     = self::getAllVpsUserUniqIds($idUser,self::StormOnDemandStormOnDemandServerType);

			return array_merge($parentIds, $vpsIds);
		}

		public static function getAllStormOnDemandHosting($idUser){

			$parentIds  = self::getAllPrivateParentUserUniqIds($idUser, self::StormOnDemandPrivateParentStormOnDemandServerType,false);
			$vpsIds     = self::getAllVpsUserUniqIds($idUser,self::StormOnDemandStormOnDemandServerType,false);

			return array_merge($parentIds, $vpsIds);
		}

		public static function getAllLiquidWebUniqIds($idUser){

			$parentIds  = self::getAllPrivateParentUserUniqIds($idUser, self::LiquidWebPPLiquidWebServerType);
			$vpsIds     = self::getAllVpsUserUniqIds($idUser,self::LiquidWebLiquidWebServerType);

			return array_merge($parentIds, $vpsIds);
		}

		public static function getAllLiquidWebHosting($idUser){

			$parentIds  = self::getAllPrivateParentUserUniqIds($idUser, self::LiquidWebPPLiquidWebServerType,false);
			$vpsIds     = self::getAllVpsUserUniqIds($idUser,self::LiquidWebLiquidWebServerType,false);

			return array_merge($parentIds, $vpsIds);
		}

        public static function saveSSDVPSConfigurations($data)
        {
            $last_record_id = '';
            $group_id = $data['gid'];
            if ($data['gid'] == '0') {
    		    $qry = "INSERT INTO tblproductgroups SET ";
                $qry .= " name='Liquid Web Products'";
    		    mysql_safequery($qry);
    		    $group_id = mysql_insert_id();
            }

		    $qry = "INSERT INTO tblproducts SET ";
            $qry .= " type='other'";
            $qry .= ", gid=" . $group_id;
            $qry .= ", name='".$data['name']."'";
            $qry .= ", description='".$data['description']."'";
            $qry .= ", paytype='recurring'";
            $qry .= ", showdomainoptions=0";
            $qry .= ", servertype='" . self::LiquidWebLiquidWebServerType."'";
		    $qry .= ", configoption1='".$_SESSION['api_username']."'";
		    $qry .= ", configoption2='".$_SESSION['api_password']."'";
		    $qry .= ", configoption4='".$data['configoption4']."'";
		    $qry .= ", configoption5='".$data['configoption5']."'";
		    $qry .= ", configoption7='".$data['configoption7']."'";
		    $qry .= ", configoption8='".$data['configoption8']."'";
		    $qry .= ", configoption9='".$data['configoption9']."'";
		    $qry .= ", configoption10='".$data['configoption10']."'";
		    $qry .= ", configoption11='".$data['configoption11']."'";
		    $qry .= ", configoption12='".$data['configoption12']."'";
		    $qry .= ", configoption13='".$data['configoption13']."'";
		    $qry .= ", configoption14='".$data['configoption14']."'";
		    $qry .= ", configoption15='".$data['configoption15']."'";
		    $qry .= ", configoption16='".$data['configoption16']."'";

		    mysql_safequery($qry);
		    $last_record_id = mysql_insert_id();

            $qry = "DELETE FROM `mg_LiquidWebSSDVPSProduct` WHERE product_id=".$last_record_id;
            mysql_safequery($qry);
		    foreach($data as $key => $value) {
                $qry = "INSERT INTO `mg_LiquidWebSSDVPSProduct`(`setting`, `product_id`, `value`) VALUES ('".$key."',".$last_record_id.",'".$value."')";
                mysql_safequery($qry);
            }

            return $last_record_id;
        }

        public static function savePrivateCloudConfigurations($data)
        {
            $last_record_id = '';

            $group_id = $data['gid'];
            if ($data['gid'] == '0') {
    		    $qry = "INSERT INTO tblproductgroups SET ";
                $qry .= " name='Liquid Web Products'";
    		    mysql_safequery($qry);
    		    $group_id = mysql_insert_id();
            }

            $qry = "INSERT INTO tblproducts SET ";
            $qry .= " type='other'";
            $qry .= ", gid=" . $group_id;
            $qry .= ", name='".$data['name']."'";
            $qry .= ", description='".$data['description']."'";
            //$qry .= ", showdomainoptions=1";
            $qry .= ", paytype='free'";
            $qry .= ", showdomainoptions=0";
            $qry .= ", servertype='" . self::LiquidWebPPLiquidWebServerType."'";
		    $qry .= ", configoption1='".$_SESSION['api_username']."'";
		    $qry .= ", configoption2='".$_SESSION['api_password']."'";

            mysql_safequery($qry);
		    $last_record_id = mysql_insert_id();

            $qry = "DELETE FROM `mg_LiquidWebPrivateParentProduct` WHERE product_id=".$last_record_id;
            mysql_safequery($qry);
		    foreach($data as $key => $value) {
                $qry = "INSERT INTO `mg_LiquidWebPrivateParentProduct`(`setting`, `product_id`, `value`) VALUES ('".$key."',".$last_record_id.",'".$value."')";
                mysql_safequery($qry);
            }

            return $last_record_id;
        }

        public static function saveConfigs($conf)
        {
            //Save custom configuration settings
            foreach($conf as $key=>$value)
            {
                $q = mysql_safequery("insert into StormBilling_customconfig (config_name,config_value)
                						values ('$key', '$value')
										ON DUPLICATE KEY UPDATE config_value='$value'");
            }
        }

		public static function getCustomConfigValues(){
            $configs = array();
            $q = mysql_safequery("SELECT config_name,config_value FROM StormBilling_customconfig");
            while(($row = mysql_fetch_assoc($q))){
                $configs[$row['config_name']] = $row['config_value'];
            }
            unset($q);
            return $configs;
		}

		public static function random_password( $length = 8 ) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $password = substr( str_shuffle( $chars ), 0, $length );
            return $password;
        }

        public static function getAdmin()
        {
            $q = mysql_safequery('SELECT username FROM tbladmins WHERE roleid = 1 LIMIT 1');
            $row = mysql_fetch_assoc($q);

            return $row['username'];
        }

        public static function encrypt_decrypt($string, $method = "decrypt")
        {
            $output = false;

            $encrypt_method = "AES-256-CBC";
            $secret_key = "Liquidweb_GT";
            $secret_iv = 'This is my secret iv';

            // hash
            $key = hash('sha256', $secret_key);

            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);

            if ($method == "encrypt") {
                $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
            } else {
                $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
            }

            return $output;
        }
	}
}
