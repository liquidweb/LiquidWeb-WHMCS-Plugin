{**********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright (c) ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-12-17)
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
 **********************************************************************}

{**
 * @author Paweł Kopeć <pawelk@modulesgarden.com>
 *}
<link rel="stylesheet" type="text/css" href="{$assetsUrl}/css/style.css" />
<div>
    <h2 class="set_main_header">{$lang.index.main_header}</h2> 
	<div id="vm_alerts">
		{if $errors}
                    {foreach from=$errors item="error"}
                         <div class="box-error">{$error}</div> 
                    {/foreach}
		{else}
                    {foreach from=$infos item="info"}
                         <div class="box-success">{$info}</div> 
                    {/foreach}
              {/if}
	</div>
              <div>
                    <form method="post">
                          <table width="90%" class="table table-striped">
                                <tr><td width="25%">{$lang.index.name}</td><td class="vps_label">{$datails.domain}</td></tr>  
                                <tr><td>{$lang.index.size}</td><td>{$datails.size} GB</td></tr>
                                <tr><td>{$lang.index.status}</td><td class="vps_label">{if $datails.status == "active"}<span class="green">{$lang.index.active}</span>{elseif $datails.status == "attached"}<span class="green">{$lang.index.attached}</span> {else}<span class="red">{$datails.status}</span>{/if}</td></tr>
                                <tr><td>{$lang.index.cross_attaching}</td><td>{if $datails.cross_attach}{$lang.general.on}{else}{$lang.general.off}{/if}</td></tr>
                                <tr><td>{$lang.index.attach_to}</td>
                                      <td class="vps_label">                                           
                                          <span id="attach_form" style="display: none;">
                                              {if $hostings}
                                                  <select name="attach[to]" id="firewall_type" style="width: 250px;">
                                                      {foreach from=$hostings item=h key=k}
                                                          <option value="{$h.uniq_id}">{$h.name} {if $h.domain} - ({$h.domain}){/if}</option>
                                                      {/foreach}    
                                                  </select>
                                                  <input type="submit" class="btn btn-primary" id="so_button_attach" value="{$lang.index.attach}"   style="margin-bottom: 10px;"/>
                                              {else}
                                                  <select name="attach[to]" style="width: 250px;" disabled="disabled" >
                                                      <option value="">{$lang.index.empty_servers}</option>
                                                  </select>
                                              {/if} 
                                                  <button class="btn btn-default"   id="so_button_cancel" style="margin-bottom: 10px;"> {$lang.general.cancel} </button>
                                            </span>
                                            <button class="btn btn-primary"   id="so_button_attach_show" style="margin-bottom: 10px;"> {$lang.index.attach} </button>
                                </td>
                          </tr>
                    </table>
                  </form>  
                    <h3 class="header_label">{$lang.index.attached_servers}</h3> 
                    <table width="90%" class="table table-striped">
                    <table class="table table-bordered">
                          <thead>
                                <tr>
                                      <th>{$lang.index.server}</th>
                                      <th>{$lang.general.action}</th>
                                </tr>
                          </thead> 
                          <tbody>
                          {if $datails.notset neq '1'}
                            {foreach from=$datails.attachedTo item="attached"}
                                  <tr>
                                        <td>{if $attached.domain} {$attached.domain}{else}{$attached.resource}{/if}</td>
                                        <td><a class="btn btn-small btn-danger detach"  href="{$serviceMainUrl}&detach={$attached.resource}">{$lang.index.detach}</a></td>
                                  </tr>
                            {foreachelse}
                                  <tr>
                                      <td colspan="2" style="text-align: center">{$lang.index.any_servers}</td>
                                  </tr> 
                            {/foreach}
                          {/if}
                           </tbody>
                    </table>              
                                      
      </div>  
</div>      
<script type="text/javascript">
	{literal}
	
	var OSUI = {
		'clearMessages': function(){
			$("#vm_alerts").html('');
		},
              'disableMessages': function(){
                   if($("#vm_alerts .box-success").size()){
                         $("#vm_alerts .box-success").delay(18200).fadeOut(300);
                   }
                   if($("#vm_alerts .box-error").size()){
                         $("#vm_alerts .box-error").delay(18200).fadeOut(300);
                   }
		},
		'addMessage': function(success, msg){
			var cl = success ? "box-success" : "box-error";
			$("#vm_alerts").show().append('<div class="'+cl+'">'+msg+'</div>').delay(8200).fadeOut(300);
		},
		'addLoading': function(){
			$("#vm_alerts").show().html('<div style="margin:10px 0;"><img src="modules/servers/StormOnDemandSBS/assets/images/loadingsml.gif" /> {/literal}{$lang.general.pleasewait}{literal}</div>');
		}
              
	};
	
	jQuery(document).ready(function(){
		OSUI.disableMessages();
              $('#so_button_attach_show').click(function(){
                    $(this).hide();
                    $("#attach_form").show();
                    return false;
              });
              $('#so_button_cancel').click(function(){
                    $("#attach_form").hide();
                    $('#so_button_attach_show').show();
                    return false;
              });
              $('.detach').click(function(){
                   return confirm("{/literal}{$lang.index.conf}{literal}");
              });     
              
              
	});
	{/literal}
</script>
