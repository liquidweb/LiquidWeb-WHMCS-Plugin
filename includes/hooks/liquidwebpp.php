<?php

define('LIQUIDWEBPP_HOOKS_CLONE_FROME_SERVER_FIELD_NAME', 'Clone From Server');
define('LIQUIDWEBPP_HOOKS_SERVER_TYPE', 'LiquidWebPrivateParent');

function liquidwebpp_hook_cart($cart){

   //LIQUIDWEBPP_HOOKS_SERVER_TYPE
   if(!isset($_SESSION['uid']) || !$_SESSION['uid']){
    	return;
   }
   
   /* GETTING MODULES PRODUCTS */
   $SystemProductsIds = array();
   $rq = mysql_query('SELECT * FROM `tblproducts` WHERE servertype = "'.LIQUIDWEBPP_HOOKS_SERVER_TYPE.'"');
   while(($res = mysql_fetch_assoc($rq))){
   		$SystemProductsIds []= (int) $res['id'];
   }

   if(empty($SystemProductsIds)){
   		return;
   }
   
   
   /* GETTING INFO ABOUT Clone CUSTOM FIELD */
   //$domainName = $_SESSION['cartdomain']['sld'].$_SESSION['cartdomain']['tld'];
   
   if(!isset($cart['products']) || empty($cart['products'])){
   		return;
   }
   
   $custFieldsIDs = array();
   $customField   = array();
   
   foreach($cart['products'] as $product){
   	
      if(empty($product['customfields']) || in_array((int)$product['pid'],$SystemProductsIds) === false){
      	continue;
      }
	  
	  foreach($product['customfields'] as $id => $customField){
	  	$custFieldsIDs []= (int) $id;
	  }
	  
	  break;
   }
   
   if(empty($custFieldsIDs)){
   		return;
   }
   
   $q = mysql_query('SELECT * FROM `tblcustomfields` WHERE type="product" AND id IN('.implode(',', $custFieldsIDs).')') ;
   while(($res = mysql_fetch_assoc($q))){
   	 if(strcmp($res['fieldname'], LIQUIDWEBPP_HOOKS_CLONE_FROME_SERVER_FIELD_NAME) === 0){
   	 	$customField = $res;
		break;
   	 }
   }
   
   if(empty($customField)){
   		return;
   }
   
   /* GETTING ALL, ACTIVE WITH uniq_id USER LIQUIDWEB SERVERS */
   $hostingList = array();
   
   require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';
   $allHosting = StormOnDemand_Helper::getAllLiquidWebHosting($_SESSION['uid']);
   if(!empty($allHosting)){
   		foreach($allHosting as $hosting){
   			$hostingList []= array(
				'id'   => $hosting['uniq_id'],
				'name' => $hosting['package']['domain'],
			);
   		}
   }
    if($_REQUEST['a'] == "confproduct"){
       echo '<script type="text/javascript">
          (function(){
            $(document).ready(function(){
              var hostings         = '.json_encode($hostingList).';
              var customFieldsIDs  = '.json_encode($custFieldsIDs).'; 
              
                //Fix for double clone serwers
                if (eventsFired == 0) {
                    for(var cI = 0; cI < customFieldsIDs.length; cI++){

                        var slc = $("select[name=\"customfield["+customFieldsIDs[cI]+"]\"");

                        for(var iH = 0; iH < hostings.length; iH++){
                          slc.append("<option value=\""+hostings[iH]["id"]+"\">"+hostings[iH]["name"]+"</option>");
                        }
                    }
                    eventsFired++;
                }
              
              $(".checkout").attr("onclick","");
              
              $(".checkout").click(function(event){
                var hostname = $("input[name=\"hostname\"]").val();
                var sbs = false;
                if(typeof hostname === "undefined"){
                  hostname = $("#owndomainsld").val()+"."+$("#owndomaintld").val();
                  sbs = true;
                }
                
                
                $(".op_error1").remove();
                $(".op_error2").remove();
                var html = "<div class=\"op_error1\" style=\"margin: 10px auto 10px auto;"+
                              "display:none;"+
                              "padding: 10px 15px;"+
                              "background-color: #FBEEEB;"+
                              "border: 1px dashed #cc0000;"+
                              "width: 90%;"+
                              "font-weight: bold;"+
                              "color: #cc0000;"+
                              "text-align: center;"+
                              "-moz-border-radius: 6px;"+
                              "-webkit-border-radius: 6px;"+
                              "-o-border-radius: 6px;"+
                              "border-radius: 6px;\">"+
                    "</div>";
                    
                var html2 = "<div class=\"op_error2\" style=\"margin: 10px auto 10px auto;"+
                                          "display:none;"+
                                          "padding: 10px 15px;"+
                                          "background-color: #FBEEEB;"+
                                          "border: 1px dashed #cc0000;"+
                                          "width: 90%;"+
                                          "font-weight: bold;"+
                                          "color: #cc0000;"+
                                          "text-align: center;"+
                                          "-moz-border-radius: 6px;"+
                                          "-webkit-border-radius: 6px;"+
                                          "-o-border-radius: 6px;"+
                                          "border-radius: 6px;\">"+
                                "</div>";
                
                
                var hostexpr = /^[a-z0-9-\.]+\.[a-z]{2,4}/;

                 if( hostexpr.test(hostname) === false ){
                   $("#configproducterror").parent().prepend(html);
                   var li_host = "<li>You must enter a valid domain name.</li>";
                   $(".op_error1").html(li_host);
                   $(".op_error1").show();
                 }; 
                 var password = $("input[name=\"rootpw\"]").val();
                 
                 var containsBigLetter  = /[A-Z]/;
                 var containsSmallLetter  = /[a-z]/;
                 var containsDigit   = /\d/;

                 if(sbs == false){
                   if(containsBigLetter.test(password) === false || containsSmallLetter.test(password) === false || containsDigit.test(password) === false ){
                     $("#configproducterror").parent().prepend(html2);
                     var li = "<li>You must enter a valid password. Password must contain: one big, one small and one dig .</li>";
                     $(".op_error2").last().html(li);
                     $(".op_error2").show();
                   }
                 }
                 
                 if( (containsBigLetter.test(password) === true && containsSmallLetter.test(password) === true && containsDigit.test(password) === true) && hostexpr.test(hostname)  === true && sbs == false){
                  $(".checkout").attr("onclick","addtocart()");
                  addtocart();
                 }else if(sbs == true && hostexpr.test(hostname) !== false){
                  $(".checkout").attr("onclick","addtocart()");
                  addtocart();
                 }
                
              });
              $(".prodconfigcol1 h3").each(function(){
                if($(this).html() == "Additional Required Information"){
                  $(this).html("Additional Information");
                }
              });
            });	
          })();
        </script>';
    }
}

function liquidwebpp_hook_admin_user_order($conf){

    require_once ROOTDIR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.StormOnDemand_Helper.php';

	$idUser 	= (int) $_REQUEST['userid'];
	$idHosting  = (int) $_REQUEST['id'];
	
	if(!isset($_REQUEST['userid'])  || !StormOnDemand_Helper::isLiquidWebPrivateParentCloneServerField($conf['fieldid'],$idHosting)){
		return;
	}			
	
    $allHosting = StormOnDemand_Helper::getAllLiquidWebHosting($idUser);
    if(!empty($allHosting)){
   		foreach($allHosting as $hosting){
   			$hostingList []= array(
				'id'   => $hosting['uniq_id'],
				'name' => $hosting['package']['domain'],
			);
   		}
    }

    $conf['value'] = $conf['value'];
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo '<script type="text/javascript">
                    (function(){
                            jQuery(document).ready(function(){
                                    var hostings         = '.json_encode($hostingList).';
                                    var customFieldsID   = '.$conf['fieldid'].'; 
                                    var selectedValue    = "'.$conf['value'].'";

                                    var $slc = jQuery("select[name=\"customfield["+customFieldsID+"]\"");
                                    if(typeof $slc !== "undefined"){

                                            for(var iH = 0; iH < hostings.length; iH++){

                                                    var selected = "";
                                                    if(parseInt(selectedValue) === parseInt(hostings[iH]["id"])){
                                                            selected = "selected=selected";
                                                    }

                                                    $slc.append("<option value=\""+hostings[iH]["id"]+"\" "+selected+">"+hostings[iH]["name"]+"</option>");
                                            }

                                            $slc.val(selectedValue);
                                    }
                            });	
                    })();
        </script>';
    }

}

//Fix for double clone serwers
function liquidwebpp_ClientAreaHeadOutput(){
    $head_return = '<script type="text/javascript">
                        var eventsFired = 0 ;
                        $(document).ready(function(){})
                    </script>';
    return $head_return;
}

add_hook("PreCalculateCartTotals",1,"liquidwebpp_hook_cart");
add_hook("CustomFieldLoad",1,"liquidwebpp_hook_admin_user_order");
add_hook("ClientAreaHeadOutput",1,"liquidwebpp_ClientAreaHeadOutput");
?>