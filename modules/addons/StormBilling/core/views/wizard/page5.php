<?php
$backpage = 'addonmodules.php?module=StormBilling&action=setup&pg=4';
if ((isset($_SESSION['api_username'])) && (isset($_SESSION['api_password']))){
    if (! isset($_SESSION['setup_lw_ssd_vps'])){
        $backpage = 'addonmodules.php?module=StormBilling&action=setup';
    }
}
global $CONFIG;
?>
<style type="text/css">

.showdiv {
    width: 100%;
    height: 400px;
    overflow: auto;
}

.hidediv {
    width: 100%;
    height: 0px;
    overflow: auto;
    visibility: hidden;
}
</style>

<link href="../modules/addons/StormBilling/core/assets/css/jquery-ui-slider-pips.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../modules/addons/StormBilling/core/assets/js/jquery-ui-slider-pips.min.js"></script>


    	<h2 class="section-heading">
    		<i class="icon-tasks"></i>Liquidweb Storm Private parent / cloud configuration
    	</h2>

	<form id="setupWizPg5" action="addonmodules.php?module=StormBilling&action=setup&pg=5" method="post">
    	<input type="hidden" name="wiz_page" value="5">
        <input type="hidden" name="wiz_page5_action" id="wiz_page5_action" value="<? echo $private_parent['page5_action'];?>">
        <input type="hidden" name="setup_lw_add_server_name" id="setup_lw_add_server_name" value="">
        <!--<input type="hidden" name="setup_lw_add_server_password" id="setup_lw_add_server_password" value="">-->
        <input type="hidden" name="setup_lw_add_server_configid" id="setup_lw_add_server_configid" value="">
        <div id="load-storm-add-server" style="overflow-x: hidden; overflow-y: hidden;">
            <div style="text-align: left; width: 100%;">
                <? echo $servertypetable;?>
            </div>
            <div style="text-align: left; width: 100%;">
                <? echo $servertable;?>
            </div>
            <br/>
            <div style="text-align: center;; width: 100%;">
                 Host Name:<input type="text" name="lw_add_server_name" id="lw_add_server_name" style="width: 330px;"/>
            </div>
        </div>
    	<h3 class="section-heading">
    		<i class="icon-book"></i> <?php echo MG_Language::translate('Product details');?>
    	</h3>
		<br/>
        <table class="table" width="100%" border="0" cellspacing="2" cellpadding="3">
			<tbody>
				<tr>
                    <td class="fieldlabel"><?php echo MG_Language::translate('Product Name :');?></td>
    				<td class="fieldarea">
    					<input type="text" name="setup_lw_spp_productname" id="setup_lw_spp_productname" style="width: 200px;" value="<?php if (!@$private_parent['name']) { echo "Liquidweb PP"; } else { echo $private_parent['name']; };?>"/>
					</td>
					<td class="fieldlabel" rowspan="2"><?php echo MG_Language::translate('Product Description :');?></td>
    				<td class="fieldarea" rowspan="2">
                        <textarea name="setup_lw_spp_description" id="setup_lw_spp_description" rows="4" class="form-control"><?php  echo htmlspecialchars(@$private_parent['description']); ?></textarea>
					</td>
				</tr>
                <tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('Product Group :');?></td>
					<td class="fieldarea">
    					<select name="setup_lw_spp_productgroup" id="setup_lw_spp_productgroup" style="width: auto;">
    					<?php foreach ($productgroup as $key=>$value) { ?>
    						<option value="<?php echo $key;?>" <?php if ($key==@$private_parent['gid']) {echo 'selected';}?>><?php echo $value;?></option>
    					<?php } ?>
    					</select>
                    </td>
				</tr>
			</tbody>
		</table>
    	<h3 class="section-heading">
    		<i class="icon-cog"></i> <?php echo MG_Language::translate('Module Settings');?>
    	</h3>
        <br/>
		<table class="table" width="100%" border="0" cellspacing="2" cellpadding="3">
			<tbody>
                <tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('Parent :');?></td>
					<td class="fieldarea"><select name="setup_lw_spp_parent" id="setup_lw_spp_parent" style="width: 220px;" onchange="setSlider()">
						<?php foreach ($priParents as $uniq_id=>$domain) { ?>
						<option value=<?php echo $uniq_id;?> <?php if ($private_parent['parent']==$uniq_id) {echo 'selected';}?>><?php echo $domain;?></option>
						<?php }?>
					</select>
        			<input type="hidden" name="setup_lw_spp_parent_unique_id" id="setup_lw_spp_parent_unique_id" value="">
					</td>
					<td class="fieldlabel"><?php echo MG_Language::translate('OS Templates :');?></td>
					<td class="fieldarea">
						<b><span id="setup_lw_ostemplates_name"><?php echo $ostemplate[$private_parent['template']];?></span></b>
						<input type="hidden" name="setup_lw_ostemplates" id="setup_lw_ostemplates" style="width: 200px;" value="<?php echo $private_parent['template'];?>"/>
						<a href="javascript:;" onclick="showTemplates()"> <img width="16" height="16" class="absmiddle" alt="Load Template" src="images/icons/search.png"></a>
						<div id="load-storm-templates"></div>
					</td>
				</tr>
				<tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('Memory (MB) :');?></td>
					<td class="fieldarea">
						<input type="text" name="setup_lw_spp_memory" id="setup_lw_spp_memory"  style="float: left; width: 20%;" value="<?php echo $private_parent['memory'];?>" onchange="setSlider(<?php echo (($CONFIG['Template'] == 'six') || (LW_CUSTOM_TEMPLATE_SIX == 'YES'));?>);"/>
						<div id="memory_slider" style="margin-left: 120px; width: 50%;"></div>
					</td>

					<td class="fieldlabel"><?php echo MG_Language::translate('Disk Space (GB) :');?></td>
					<td class="fieldarea">
						<input type="text" name="setup_lw_spp_diskspace" id="setup_lw_spp_diskspace" style="float: left; width: 20%;" value="<?php echo $private_parent['diskspace'];?>" onchange="setSlider(<?php echo (($CONFIG['Template'] == 'six') || (LW_CUSTOM_TEMPLATE_SIX == 'YES'));?>);"/>
						<div id="diskspace_slider" style="margin-left: 140px; width: 50%;"></div>
					</td>
				</tr>
				<tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('Virtual CPU :');?></td>
					<td class="fieldarea"><input type="text" name="setup_lw_spp_virtcpu" id="setup_lw_spp_virtcpu" style="width: 230px;" value="<?php echo $private_parent['vcpu'];?>"/></td>

					<td class="fieldlabel"><?php echo MG_Language::translate('Backup Quota :');?></td>
					<td class="fieldarea">
						<input type="hidden" name="setup_lw_spp_backupplan" id="setup_lw_spp_backupplan" value="quota"/>
    					<select name="setup_lw_spp_ipsbackupquota" id="setup_lw_spp_ipsbackupquota" style="width: 150px;">
    					<?php foreach ($backupQuota as $options) { ?>
    						<option value="<?php echo $options['value'];?>" <?php if ($private_parent['backup_quota']==$options['value']) {echo 'selected';}?>><?php echo $options['description'];?></option>
    					<?php } ?>
    					</select>
					</td>
				</tr>
				<tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('Bandwidth Quota (GB) :');?></td>
					<td class="fieldarea">
						<input type="hidden" name="setup_lw_ips" id="setup_lw_ips" style="width: 230px;" value="1"/>
						<select name="setup_lw_spp_bandwidthquota" id="setup_lw_spp_bandwidthquota" style="width: 220px;">
    					<?php foreach ($bandwidthQuota as $options) { ?>
    						<option value="<?php echo $options['value'];?>" <?php if ($private_parent['bandwidth_quota']==$options['value']) {echo 'selected';}?>><?php echo $options['description'];?></option>
    					<?php } ?>
					</select>
					</td>
					<td class="fieldlabel"><?php echo MG_Language::translate('Monitoring :');?></td>
					<td class="fieldarea"><input type="checkbox" name="setup_lw_spp_monitoring" id="setup_lw_spp_monitoring" <?php if ($private_parent['monitoring']=='1') {echo 'checked';}?>/><?php echo MG_Language::translate(' Tick to give possibility to monitoring server from Client Area');?></td>
				</tr>
				<tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('Firewall :');?></td>
					<td class="fieldarea"><input type="checkbox" name="setup_lw_spp_firewall" id="setup_lw_spp_firewall" <?php if ($private_parent['firewall']=='1') {echo 'checked';}?>/><?php echo MG_Language::translate(' Tick to give possibility to manage firewall from Client Area');?></td>
					<td class="fieldlabel"><?php echo MG_Language::translate('IPs Management :');?></td>
					<td class="fieldarea"><input type="checkbox" name="setup_lw_spp_ipsmgmnt" id="setup_lw_spp_ipsmgmnt" <?php if ($private_parent['ips_management']=='1') {echo 'checked';}?>/><?php echo MG_Language::translate(' Tick to give possibility to manage IPs addresses from Client Area');?></td>
				</tr>
			</tbody>
		</table>
    	<br/>
    	<table align="center" width="100%" style="text-align: center; border: 0px;">
        	<tbody>
            	<tr>
                	<td width="35%">
                		<a class="btn btn-success clearfix" href="<?php echo $backpage; ?>"><i class="icon-chevron-left"></i> Back</a>
                	</td>
                	<td width="30%">
                		<a id="advmode_submit" class="btn btn-success clearfix" href="" onclick="submitForm('advmode'); return false;">Save & goto Advanced mode <i class="icon-share"></i></a>
                	</td>
                	<td width="35%">
                		<a id="save_submit" class="btn btn-success clearfix" href="" onclick="submitForm('save'); return false;">Save & Continue <i class="icon-chevron-right"></i></a>
                	</td>
            	</tr>
        	</tbody>
    	</table>
	</form>
<script>

$(document).ready(function() {
    if ($("#wiz_page5_action").val() == "Add Server") {
        $("#load-storm-add-server").dialog({
            autoOpen : true,
            resizable: false,
            closeOnEscape: false,
            width: 850,
            modal: true,
            position: { my: 'top', at: 'top+150' },
            title : 'Add New Server',
            buttons: {'OK': {
                text: "Add Server",
                id: "btnserver-Ok",
                click: function() {
                    action = $("#wiz_page5_action").val();
                    $("#setup_lw_add_server_name").val($("#lw_add_server_name").val());
                    $("#setup_lw_add_server_password").val($("#lw_add_server_password").val());
                    $(this).dialog('close');
                    submitForm(action);
                }
            }}
        }).prev().find(".ui-dialog-titlebar-close").hide();
    } else {
        $("#load-storm-add-server").dialog({
            autoOpen : false,
            resizable: false,
            closeOnEscape: false,
            width: 850,
            modal: true,
            position: { my: 'top', at: 'top+150' },
            title : 'Add New Server',
            buttons: {'OK': {
                text: "Add Server",
                id: "btnserver-Ok",
                click: function() {
                    action = $("#wiz_page5_action").val();
                    $("#setup_lw_add_server_name").val($("#lw_add_server_name").val());
                    $("#setup_lw_add_server_password").val($("#lw_add_server_password").val());
                    $(this).dialog('close');
                    submitForm(action);
                }
            }}
        }).prev().find(".ui-dialog-titlebar-close").hide();
    }

    $("#load-storm-templates").dialog({
        autoOpen: false,
        resizable: false,
        closeOnEscape: true,
        width: 600,
        modal: true,
        position: { my: 'top', at: 'top+150' },
        title : 'Load Templates',
        buttons: {'OK': {
            text: "OK",
            id: "selTemplates-Ok",
            click: function() {
            	val = $(this).parent().find("input[name='template-id']:checked").val();
            	$('#setup_lw_ostemplates').val(val).trigger('change');
            	name = $(this).parent().find("input[name='template-id']:checked").attr("data-template-name");
            	$('#setup_lw_ostemplates_name').html(name);
            	$("#setup_lw_ostemplates_name").css("color", "#4c4c4c");
            	$(this).dialog('close');
            }
        },'Cancel': {
            text: "Cancel",
            id: "selTemplates-Cancel",
            click: function() {
            $(this).dialog('close');
            }
        }},
        open: function(event, ui) {
            /*$('#load-storm-templates').load(location+'&ajaxload=template&zone='+$('#setup_lw_zonecode').val()+'&conf_id='+$('#setup_lw_ostemplates').val(), function() {
              //
            });*/
            $('#load-storm-templates').load(location+'&ajaxload=pp_template&conf_id='+$('#setup_lw_ostemplates').val(), function() {
              //
            });
          }
    });

    $('#setup_lw_ostemplates').on('change', function(){
        if (($('#setup_lw_ostemplates').val() == '0') || ($('#setup_lw_vpstype').val() == '0')){
        	$('#advmode_submit').addClass('disabled');
        	$('#save_submit').addClass('disabled');
        } else {
			$('#advmode_submit').removeClass('disabled');
            $('#save_submit').removeClass('disabled');
        }
    });

    var previous;

    $("#ddl_zone").on('focus', function () {
        // Store the current value on focus and on change
        previous = this.value;
    }).change(function() {
        // Do something with the previous value after the change
        var divid = "div_"+this.value;
        var olddivid = "div_"+previous;
        $('#'+divid).removeClass("hidediv");
        $('#'+divid).addClass("showdiv");

        $('#'+olddivid).removeClass("showdiv");
        $('#'+olddivid).addClass("hidediv");
        // Make sure the previous value is updated
        previous = this.value;
    });
});

function showTemplates()
{
    $("#load-storm-templates").dialog('open');
    $("#load-storm-templates").html("<p style=\"text-align:center\"><img src=\"../modules/servers/StormOnDemand/assets/images/admin/loading.gif\" alt=\"loading...\"/></p>.");
}

function submitForm(action)
{
	$("#wiz_page5_action").val(action);
	$('form#setupWizPg5').submit();
}

function setconfigid(id) {
    $("#setup_lw_add_server_configid").val(id);
}

setSlider(<?php echo (($CONFIG['Template'] == 'six') || (LW_CUSTOM_TEMPLATE_SIX == 'YES'));?>);

function setSlider(tmlSix) {

	var obj = jQuery.parseJSON($("#setup_lw_spp_parent").val());
	sUniqueId = obj.unique_id
	iMem = obj.free_mem
	iDisk = obj.free_disk

	$( "#setup_lw_spp_parent_unique_id" ).val(sUniqueId);

	//memory
    $( "#memory_slider" ).slider({
      value: $("#setup_lw_spp_memory").val(),
      min: 1024,
      max: iMem,
      step: 1,
      slide: function( event, ui ) {
        $( "#setup_lw_spp_memory" ).val(ui.value );
      }
    });
	$( "#memory_slider" ).css('width','50%');
    if (tmlSix == 1) {
    	$( "#memory_slider" ).slider('pips').slider('float');
    	$( "#memory_slider" ).find("span").not( ".ui-slider-tip" ).css({'width':'2%','padding':'0px'});
    	$( "#memory_slider" ).find("span .ui-slider-tip" ).css({'width':'auto','padding':'0px'});
    }
    $( "#setup_lw_spp_memory" ).val( $( "#memory_slider" ).slider( "value" ) );

    //disk space
    $( "#diskspace_slider" ).slider({
        value: $("#setup_lw_spp_diskspace").val(),
        min: 1,
        max: iDisk,
        step: 1,
        slide: function( event, ui ) {
          $( "#setup_lw_spp_diskspace" ).val(ui.value );
        }
      });
    $( "#diskspace_slider" ).css('width','50%');
    if (tmlSix == 1) {
      $( "#diskspace_slider" ).slider('pips').slider('float');
      $( "#diskspace_slider" ).find("span").not( ".ui-slider-tip" ).css({'width':'2%','padding':'0px'});
      $( "#diskspace_slider" ).find("span .ui-slider-tip" ).css({'width':'auto','padding':'0px'});
    }
    $( "#setup_lw_spp_diskspace" ).val( $( "#diskspace_slider" ).slider( "value" ) );
}


</script>