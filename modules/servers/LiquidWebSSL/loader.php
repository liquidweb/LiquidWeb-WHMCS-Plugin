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
 * Class loader 
 * example: $class = Smart_Load('modulesgarden_ModuleInformationCLient')  //without construct
 * 			$class = new Smart_Load('StormOnDemand_bleed_StormOnDemandProduct'); //with construct
 * 
 * @author Kamil Szela <kamil@modulesgarden.com>
 * @param  string $class
 * @throws Exception
 */
 
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

if(!function_exists('Smart_Loader')){
	function Smart_Loader($class){
		
	   $dir 		  = ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DS;
	   $className     = '';    
	   $classPrefixes = array(
	   		'class.',
	   );
		
	   if(!is_dir($dir)){
	   	throw new Exception('Dir: '.$dir.' does\'t exists');
	   }	
		   
	   if(strpos($class,'_') >= 0){
	   	
			$classExp  = explode('_', $class);
		    $className = array_pop($classExp);
			
			if(!$className){
				throw new Exception('Class name is null');
			}
			
		    foreach($classExp as $k => $v){
		    	if(!$v){
		    		continue;
		    	}
				
				if(!is_dir($dir.''.$v)){
					throw new Exception('Dir: '.$dir.$v.' doesn\'t exists');
				}
				$dir .= $v.DS;
		    }
		
	   }else{
	   	  $className = $class;
		  if(!$className){
				throw new Exception('Class name is null');
		  }
	   }
	   
	   if(file_exists($dir.$className.'.php')){
	   		require_once $dir.$className.'.php';
		    return $className;
	   }else if(!empty($classPrefixes)){
	   		foreach($classPrefixes as $k => $v){
	   			if(file_exists($dir.$v.$className.'.php')){
	   				require_once $dir.$v.$className.'.php';
					return $className;
	   			}
	   		}
	   }
	   
	   throw Exception('Class: '.$class.' not found');
	}
} 

if(!function_exists('Smart_Load')){
		
	function Smart_Load($className){
		$class = Smart_Loader($className);
		return $class;
	}	
}
	