<?php

/**********************************************************************
 *  
 * *
 *
 *  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
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
 * @author Dariusz Bijos <dariusz.bi@modulesgarden.com>
 */
 
if (!defined("WHMCS"))
  die("This file cannot be accessed directly");

if(function_exists('mysql_safequery') == false) {
    function mysql_safequery($query,$params=false) {
        if ($params) {
            foreach ($params as &$v) { $v = mysql_real_escape_string($v); }
            $sql_query = vsprintf( str_replace("?","'%s'",$query), $params );
            $sql_query = mysql_query($sql_query);
        } else {
            $sql_query = mysql_query($query);
        }
        
        return ($sql_query);
    }
}

if(!function_exists('mysql_get_array'))
{
    function mysql_get_array($query, $params = false)
    {
        $q = mysql_safequery($query, $params);
        $arr = array();
        while($row = mysql_fetch_assoc($q))
        {
            $arr[] = $row;
        }
        
        return $arr;
    }
}

if(!function_exists('mysql_get_row'))
{
    function mysql_get_row($query, $params = false)
    {
        $q = mysql_safequery($query, $params);
        $row = mysql_fetch_assoc($q);
        return $row;
    }
}

function TemplateProduct_ProductEdit($params)
{
  
  $productdetails = mysql_get_row("SELECT * FROM tblproducts WHERE id=?", array($params['pid']));
  $servertype = $productdetails['servertype'];

  $predefiniedconfigs_modpath = ROOTDIR . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'servers' . DIRECTORY_SEPARATOR . $servertype . DIRECTORY_SEPARATOR . 'predefiniedconfigs';
  $tempalteexist = TemplateProduct_loadTemplates($predefiniedconfigs_modpath);
  if(!empty($tempalteexist)){
    $template = json_encode(TemplateProduct_loadTemplates($predefiniedconfigs_modpath));

    $producttemplate = '<br><table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">\'+
                            \'<tbody>\'+
                              \'<tr>\'+
                                \'<td class="fieldlabel" width="150">Product template</td>\'+
                                \'<td class="fieldarea"><a id="product-template-load" href="#">Load default product templates</a><div id="conf-dialog-product-templates" title=""></div></td>\'+
                              \'</tr>\'+
                            \'</tbody>\'+
                          \'</table>';
                          
                          
      $module_confoptions_name = $servertype.'_ConfigOptions';  
      $confoptionsarray = $module_confoptions_name();
      $confoptionsarraylower = TemplateProduct_transformKeys($confoptionsarray);
      
      $i=0;
      foreach($confoptionsarraylower as $kay => $value){
        $i++;
        //$value['name'] = 'packageconfigoption['.$i.']';
        $confoptionsarraylower[$kay] = $value;
      }  

      $confoptions =   json_encode($confoptionsarraylower);      
     echo '<link href="../modules/addons/StormBilling/core/assets/css/jquery-ui.1.12.1.min.css" rel="stylesheet" type="text/css" />
     <script src="../modules/addons/StormBilling/core/assets/js/jquery-ui.1.12.1.min.js"></script>
     
     <script>
     $(function(){
      var servertype = $("select[name=\'servertype\'] option:selected" ).val();
      var productTemplates = {"templates_info":'.$confoptions.',"templates":'.$template.'};
   
      var producttemplate = \''.$producttemplate.'\';
      var whmcs_version = \''.TemplateProduct_getWHMCSVersion().'\';
      if(whmcs_version == 5){
          $("#tab2box #tab_content table:first-child").after(producttemplate);
      }else if(whmcs_version == 6){
           $("#tab3 table:first-child").after(producttemplate);
      }
      
      
      $(\'#product-template-load\').on(\'click\', function(event){
        event.preventDefault();
        loadProductTemplates();
      });
      
      function loadProductTemplates(){
        
          if($("#conf-dialog-product-templates").is(":data(dialog)")){
             $("#conf-dialog-product-templates").dialog("destroy");
          }
          
          $("#conf-dialog-product-templates").dialog({minWidth: 650});
          $("#conf-dialog-product-templates").attr("title", \'Product templates list\');
          $("#conf-dialog-product-templates").html(\'<table class="datatable" style="width: 100%"><tr><th>Template Name</th><th>Action</th></tr></table>\');
          
          $tableContainer = $("#conf-dialog-product-templates").find(\'table.datatable\');
          
          var noNameIter = 1;
          for(var i = 0; i < productTemplates[\'templates\'].length; i++){
            
            var name = \'\';
            if(productTemplates[\'templates\'][i][\'name\'] && typeof productTemplates[\'templates\'][i][\'name\'] !== \'object\'){
              name = productTemplates[\'templates\'][i][\'name\'];
            }else{
              name = \'noname \'+noNameIter;
              noNameIter++;
            }
            
            $tableContainer.append(\'<tr><td>\'+name+\'</td><td style="text-align:center"><a href="#" data-template-id="\'+i+\'" class="load-product-template">load</a></td></tr>\');
          }
          
          $tableContainer.find(\'.load-product-template\').each(function(){
            $(this).on(\'click\', function(event){
              
          event.preventDefault();
              var idTemplate = parseInt($(this).attr(\'data-template-id\'));
              if(productTemplates[\'templates\'][idTemplate]){
                for(var iOption in productTemplates[\'templates_info\']){
                  var valToSet = \'\';
                  if (iOption.toLowerCase().indexOf("_") >= 0){
                    iOption = iOption.replace(" ", "_");
                  }

                  if(productTemplates[\'templates\'][idTemplate][iOption] && typeof productTemplates[\'templates\'][idTemplate][iOption] !== \'object\'){
                    valToSet = productTemplates[\'templates\'][idTemplate][iOption];
                  }
                   
                  if(valToSet === \'\' && typeof productTemplates[\'templates_info\'][iOption][\'reset\'] !== "undefined" && productTemplates[\'templates_info\'][iOption][\'reset\'] === false){
                    continue;
                  } 
                  switch(productTemplates[\'templates_info\'][iOption][\'type\']){
                    
                    case \'checkbox\':
                      if(valToSet === \'yes\'){
                        $(\'input[name="\'+productTemplates[\'templates_info\'][iOption][\'name\']+\'"]\').prop(\'checked\', true);
                      }else{
                        $(\'input[name="\'+productTemplates[\'templates_info\'][iOption][\'name\']+\'"]\').prop(\'checked\', false);
                      }
                    break;
                    
                    case \'yesno\':
                      if(valToSet === \'yes\'){
                        $(\'input[name="\'+productTemplates[\'templates_info\'][iOption][\'name\']+\'"]\').prop(\'checked\', true);
                      }else{
                        $(\'input[name="\'+productTemplates[\'templates_info\'][iOption][\'name\']+\'"]\').prop(\'checked\', false);
                      }
                    break;
                    
                    case \'text\' : 
                        $(\'input[name="\'+productTemplates[\'templates_info\'][iOption][\'name\']+\'"]\').val(valToSet).change();
                    break;
                    
                    case \'html\' : 
                      $(productTemplates[\'templates_info\'][iOption][\'id\']).html(valToSet);
                    break; 
                    
                    case \'textarea\' : 
                       $(\'textarea[name="\'+productTemplates[\'templates_info\'][iOption][\'name\']+\'"]\').val(valToSet).change();
                    break; 
                    
                    case \'select\':
                    
                      if(iOption === \'backup_quota\'){
                        backupQuota    = parseInt(valToSet);
                      }else if(iOption === \'bandwidth_quota\'){
                        bandwidthQuota = valToSet;
                      }else{
                        $(\'select[name="\'+productTemplates[\'templates_info\'][iOption][\'name\']+\'"]\').val(valToSet).change();
                      }
                      
                    case \'dropdown\':
                    
                      if(iOption === \'backup_quota\'){
                        backupQuota    = parseInt(valToSet);
                      }else if(iOption === \'bandwidth_quota\'){
                        bandwidthQuota = valToSet;
                      }else{
                        $(\'select[name="\'+productTemplates[\'templates_info\'][iOption][\'name\']+\'"]\').val(valToSet).change();
                      }
                      
                    break;
                    
                  }
                }
              }
              
                $("#conf-dialog-product-templates").dialog("destroy");
                $("#conf-dialog-product-templates").hide();
            });
          });
      }
      $("#tab8 .op_version").hide();
     });
    </script>';
  }
}

function TemplateProduct_getWHMCSVersion() {
        // GET CONFIG
        global $CONFIG;
        // Get WHMCS Version
        $whmcsVersion = preg_replace('[^0-9\.]', '', $CONFIG['Version']);

        $varsionarr0 = explode('-', $whmcsVersion);

        $varsionarr1 = explode('.', $varsionarr0[0]);
        $versionnum = 0;
        foreach ($varsionarr1 as $k => $num) {
            $versionnum += $num * pow(10, (3 - $k) *  2);
        }
        unset($k, $num);

        return substr($versionnum, 0 ,1 );
}

function TemplateProduct_transformKeys(&$array)
{
  foreach (array_keys($array) as $key):
    $value = &$array[$key];
    unset($array[$key]);
    $transformedKey = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', ltrim($key, '!')));
    $transformedKey = str_replace(" ", "_",$transformedKey);
    if (is_array($value)) TemplateProduct_transformKeys($value);
    $array[$transformedKey] = $value;      
    unset($value);
  endforeach;
  return $array;
}

function TemplateProduct_loadTemplates($predefiniedconfigs_modpath){
	
	$dirToTemplates = $predefiniedconfigs_modpath;
	$templates 	    = array();

	if(is_dir($dirToTemplates)){
    
	   if ($handle = opendir($dirToTemplates)) {
		    while (false !== ($entry = readdir($handle))) {
		        if ($entry != "." && $entry != "..") {
					$xml = simplexml_load_file($dirToTemplates.DS.$entry);
					$tpl = json_decode(json_encode($xml), TRUE);
					
					if(isset($tpl['visible']) && (bool)$tpl['visible'] === true){
						$templates []= $tpl;
					}
		        }
		    }
    	    closedir($handle);
		}
	   return $templates;
	}
	return array();
}

add_hook("AdminProductConfigFields", 1, "TemplateProduct_ProductEdit");