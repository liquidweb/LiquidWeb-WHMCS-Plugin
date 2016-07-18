{if $template eq "six" or $custom_template eq "YES"}
    {literal}
    <script type="text/javascript">
          jQuery( document ).ready(function() {
              $("#Primary_Sidebar-Service_Details_Overview-Information").attr("href", "{/literal}{$systemurl}{literal}clientarea.php?action=productdetails&id={/literal}{$id}{literal}");
              $("#Primary_Sidebar-Service_Details_Overview-Information").click(function(){
                window.location.href = "{/literal}{$systemurl}{literal}clientarea.php?action=productdetails&id={/literal}{$id}{literal}";
              });

          });
     </script>
    {/literal}
{/if}

{if $smarty.get.message eq 'attached'}
  <div class="op_success"><center style="margin-top:5px;"><strong><span style="color:#69990F;">Block Storage has been successfully attached. Configuration in progress...</span></strong></center></div>
{/if}

<div>
    <h3>All Available Block Storage</h3>
</div>
<div class="ava_blocks" style="margin-top:20px;font-size:14px;">
	<table class="table table-framed table table-condensed blockstorage_lists">
	 <thead>
	   <tr style="font-size:13px;">
	    <th>Block Storage Product</th>
	    <th>Status</th>
	    <th>Action</th>
	   </tr>
	 </thead>
	 <tbody>
	    {if $sbs}
            {foreach from=$sbs item=s}
                <tr>
                    <td class="name"><b>{$s.sbs.system_config.productinfo_name}</b><br/><span style="font-size:12px"><a href="http://{$s.sbs.system_config.hosting_domain}" target="_blank">{$s.sbs.system_config.hosting_domain}</a></span></td>
                    <td class="status" {if $template eq "six" or $custom_template eq "YES"}style="margin-top:10px;background-color:#fafafa;"{/if}>{$s.status}</td>
                    <td class="actions">
                       {if !$s.is_assigned}
                       <a href="{$request_uri}&ajaxaction=attach&uid={$s.sbs.uniq_id}" class="btn btn-success send-ajax-req">Attach</a>
                       {else}
                       <a href="{$request_uri}&ajaxaction=detach&uid={$s.sbs.uniq_id}" class="btn btn-danger send-ajax-req">Detach</a>
                       {/if}
                    </td>
                </tr>
            {/foreach}
        {else}
            <tr>
                <tr>
                    <td colspan="3">Nothing to display</td>
                </tr>
            </tr>
        {/if}
	 </tbody>
	</table>

</div>

{literal}
<style type="text/css">
table.blockstorage_lists thead th
{
	text-align: center !important;
}

table.blockstorage_lists tbody td.status, table.blockstorage_lists tbody td.actions
{
	text-align:center;
	vertical-align: middle !important;
}

.whait-loader img.preolader{
	width:120px !important;
	padding-top: 5px;
}

</style>

<script type="text/javascript">

var pInterval = null;

 jQuery('a.send-ajax-req').each(function(){
 	jQuery(this).click(function(e){
 		e.preventDefault();

 		var _href = jQuery(this).attr('href');
 					jQuery(this).addClass('exp_f_resp');
 		jQuery(this).parent().append('<br/><span class="whait-loader"><img src="modules/servers/StormOnDemand/assets/images/loading.gif"alt="loading..." class="preolader"/></span>');
 		jQuery(this).addClass('disabled');
 		jQuery.get(_href, function(data){

 			try{
 				data = JSON.parse(data);

 				if(data['type'] === 'success'){
                    if(_href.indexOf("attach") !== -1 ){
                        jQuery('.op_success').show();
                        jQuery('.whait-loader').remove();
                        jQuery('.exp_f_resp').attr('href',_href.replace('attach', 'detach'));
                        jQuery('.status').html('Assigned');
                        jQuery('.exp_f_resp').html('Detach');
                        jQuery('.exp_f_resp').removeClass('btn-succes');
                        jQuery('.exp_f_resp').addClass("btn-danger");
                        jQuery('.exp_f_resp').removeClass('disabled');
                        jQuery('.exp_f_resp').removeClass('exp_f_resp');
                    }else{
                        pInterval = setInterval(function(){
                            clearInterval(pInterval);
                            window.location.reload();
                            jQuery('.exp_f_resp').removeClass('exp_f_resp');
                        }, 10000);
                    }
 				}else{
 					jQuery('.exp_f_resp').parent().find('.whait-loader').html(data['data']['error']);
 					jQuery('.exp_f_resp').removeClass('exp_f_resp');
 				}
 			}catch(e){
 				jQuery('.exp_f_resp').parent().find('.whait-loader').html('<span style="padding-top:5px;color:red;">error</span>');
 			    jQuery('.exp_f_resp').removeClass('exp_f_resp');
 			    console.log(e);
 				    pInterval = setInterval(function(){
 				    	clearInterval(pInterval);
 				    	window.location.reload();
 				    }, 3000);
 			}

 		});
 		return false;
 	});
 });

</script>
{/literal}