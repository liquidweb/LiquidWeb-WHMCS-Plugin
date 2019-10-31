/*
jQuery(function(){
	Query( document ).ready(function() {
     var configToSend = {$config_to_send};
     
     jQuery(".load-configuration").click(function(event){
        event.preventDefault();
        if($("#conf-dialog").is(":data(dialog)")){
           $("#conf-dialog").dialog("destroy");
        }
        $("#conf-dialog").attr("title", $(this).html());
        $("#ui-id-3").html($(this).html());
        $("#conf-dialog").html("<p style=\"text-align:center\"><img src=\"../modules/servers/LiquidWebPrivateParent/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>");
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
});

*/

$(document).ready(function() {
   $('#load-storm-options').on('show.bs.modal', function (e) {
      $("#load-storm-options-body").html('<p style="text-align:center"><img src="../modules/servers/LiquidWeb/assets/images/admin/loading.gif" alt="loading..."/></p><p style="text-align:center;font-size:12px;margin-top:0px;margin-bottom:0px;">Please note that this may take 30+ seconds to load all the data from our API.</p>');
   });

	$('#Option-OK').on('click', function(){
		if ($('#load-storm-options').find(".modal-title").html() == 'Load Template') {
         val = $("#load-storm-options").find("input[name='template-id']:checked").val();
         $("#load-storm-template").parent().find("input").val(val).change();
         $("#load-storm-image").prev().val("");
         $("#load-storm-config").prev().val("");
      } else if ($('#load-storm-options').find(".modal-title").html() == 'Load Image') {
         val = $("#load-storm-options").find("input[name='image-id']:checked").val();
         $("#load-storm-image").parent().find("input").val(val).change();
         $("#load-storm-template").prev().val("");
         $("#load-storm-config").prev().val("");
		}
		$('#load-storm-options').modal('hide');
	});
});

function showOptions(option, el, pid)
{
	$('#load-storm-options').modal('show');
	$('#load-storm-options').removeClass("bd-example-modal-lg");
	$('#load-storm-options').find('.modal-dialog').removeClass("modal-lg");
   $('.modal-footer').show();
	if (option == 'Template') {
      $('#load-storm-options').find(".modal-title").html('Load '+option);
      val = $(el).parent().find("input").val();
      var sendUrlStr = "../modules/servers/LiquidWebPrivateParent/stormajax.php?action=edit&id="+pid+"&conf_id="+val+"&stormajax=load-template";
		$('#load-storm-options-body').load(sendUrlStr);
	} else if (option == 'Image') {
      $('#load-storm-options').find(".modal-title").html('Load '+option);
		$('#load-storm-options').addClass("bd-example-modal-lg");
		$('#load-storm-options').find('.modal-dialog').addClass("modal-lg");
      val = $(el).parent().find("input").val();
      var sendUrlStr = "../modules/servers/LiquidWebPrivateParent/stormajax.php?action=edit&id="+pid+"&conf_id="+val+"&stormajax=load-image";
		$('#load-storm-options-body').load(sendUrlStr);
   } else if (option == 'Generate Custom Fields') {
      $('#load-storm-options').find(".modal-title").html(option);
      $('.modal-footer').hide();
      var sendUrlStr = "../modules/servers/LiquidWebPrivateParent/stormajax.php?action=edit&id="+pid+"&modaction=generate_custom_fields";
      $('#load-storm-options-body').load(sendUrlStr, function(responseTxt, statusTxt, xhr){
         var obj = JSON.parse(responseTxt);
         $('#load-storm-options-body').html(obj.message);
         if (obj.status == '1') {
            window.location.href = obj.goto;
         }
       });      
   } else if (option == 'Generate Default Configurable Options') {
      $('#load-storm-options').find(".modal-title").html(option);
      $('.modal-footer').hide();
      var sendUrlStr = "../modules/servers/LiquidWebPrivateParent/stormajax.php?action=edit&id="+pid+"&modaction=generate_configurable_options";
      $('#load-storm-options-body').load(sendUrlStr, function(responseTxt, statusTxt, xhr){
         var obj = JSON.parse(responseTxt);
         $('#load-storm-options-body').html(obj.message);
         if (obj.status == '1') {
            window.location.href = obj.goto;
         }
       });      
   }
}
