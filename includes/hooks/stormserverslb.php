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
    function liquidweblb_ProductEdit($params){
        
        $productdetails = mysql_get_row("SELECT * FROM tblproducts WHERE id=?", array($params['pid']));
        $servertype = $productdetails['servertype'];
        if($servertype == 'LiquidWebLoadBalancer' || $servertype == 'StormOnDemandLoadBalancer'){
            echo '<script type="text/javascript">
            jQuery(function(){
              jQuery( document ).ready(function() {
                jQuery(".load-configuration").click(function(event){
                    event.preventDefault();
                    
                    if($("#conf-dialog").is(":data(dialog)"))
                    {
                        $("#conf-dialog").dialog("destroy");
                    }
                    $("#conf-dialog").attr("title", $(this).html());
                    $("#ui-id-3").html($(this).html());
                    $("#conf-dialog").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWebLoadBalancer/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
                    $("#conf-dialog").dialog({minWidth: 650});
                    
                    val = $(this).parent().find("input").val();
                    jQuery.post("configproducts.php?action=edit&id='.(int)$_REQUEST['id'].'&conf_id="+val,jQuery(this).attr("href"), function(data){
                        $("#conf-dialog").html(data);
                    });
                });
            });
            });
            </script>
            <div id="conf-dialog" style="display:none;" title="">
            </div>';
        }

    }
    
add_hook("AdminProductConfigFields", 1, "liquidweblb_ProductEdit");