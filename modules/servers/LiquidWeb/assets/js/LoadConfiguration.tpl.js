jQuery(function(){
	
   var configToSend = {$config_to_send};
   
   jQuery(".load-configuration").click(function(event){
      event.preventDefault();
      if($("#conf-dialog").is(":data(dialog)")){
         $("#conf-dialog").dialog("destroy");
      }
      
      $("#conf-dialog").attr("title", $(this).html());
      $("#ui-id-3").html($(this).html());
      $("#conf-dialog").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWeb/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
      $("#conf-dialog").dialog({minWidth: 650});
                        
      val = $(this).parent().find("input").val();
      
      var sendUrlStr = "configproducts.php?action=edit&id={$id}&conf_id="+val;
      
      for(var i = 0; i < configToSend.length; i++){
      	
      	var _val = jQuery('[name="'+configToSend[i]['name']+'"]').val();
      	if(typeof _val === "undefined"){
      		_val = '';
      	}
      	
      	sendUrlStr = sendUrlStr + '&' + configToSend[i]['var_name'] + '=' + _val; 
      }
      
      jQuery.post(sendUrlStr,jQuery(this).attr("href"), function(data){
         $("#conf-dialog").html(data);
      });
   });
});