<?php
$backpage = 'addonmodules.php?module=StormBilling&action=setup&pg=3';
if ((isset($_SESSION['api_username'])) && (isset($_SESSION['api_password']))){
    $backpage = 'addonmodules.php?module=StormBilling&action=setup';
}
?>
<style type="text/css">

    #menus {
        list-style-type:none !important;
        margin-top:0 !important;
        margin-bottom: 2px !important;
        margin-right:0 !important;
        margin-left:0 !important;
        padding:0 !important;
        height:31px !important;
        width: 100% !important;
    }
    #menus li {
        float:left !important;
        height:30px !important;
        line-height:27px !important;
    }
    #menus li a {
        display:block !important;
        padding:0 20px !important;
        text-decoration:none !important;
        height:28px !important;
    }
    #menus li a:hover {
        background-color:#d0e5f5 !important;
        text-decoration:none !important;
        height:28px !important;
        color:#2e6e9e !important;
        border-top-right-radius: 5px !important;
        border-top-left-radius: 5px !important;
    }

</style>
<script type="text/javascript">
    $(document).ready(function() {

    $("#load-storm-zones").dialog({
        autoOpen: false,
        resizable: false,
        closeOnEscape: true,
        width: 600,
        modal: true,
        position: { my: 'top', at: 'top+150' },
        title : 'Load Zones',
        buttons: {'OK': {
            text: "OK",
            id: "selZones-Ok",
            click: function() {
            	val = $(this).parent().find("input[name='zone-id']:checked").val();
            	name = $(this).parent().find("input[name='zone-id']:checked").attr("data-zone-name");
				if(val != '' && val > 0) {
					$('#setup_lw_zonecode').val(val).trigger('change');
					$('#zone_name').html(name);
				}
            	$('#setup_lw_ostemplates').val('0').trigger('change');
            	$('#setup_lw_ostemplates_name').html('Please Select OS Template');
            	$("#setup_lw_ostemplates_name").css("color", "#CC0000");
            	$('#setup_lw_vpstype').val('0').trigger('change');
            	$('#vpstype_name').html('Please Select VPS Type');
            	$("#vpstype_name").css("color", "#CC0000");

            	$(this).dialog('close');
            }
        },'Cancel': {
            text: "Cancel",
            id: "selZones-Cancel",
            click: function() {
            $(this).dialog('close');
            }
        }},
        open: function(event, ui) {
            $('#load-storm-zones').load(location+'&ajaxload=zone&conf_id='+$('#setup_lw_zonecode').val(), function() {
              //
            });
          }
    });

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
            $('#load-storm-templates').load(location+'&ajaxload=template&zone='+$('#setup_lw_zonecode').val()+'&conf_id='+$('#setup_lw_ostemplates').val(), function() {
              //
            });
          }
    });

    $("#load-storm-vpstypes").dialog({
        autoOpen: false,
        resizable: false,
        closeOnEscape: true,
        width: 600,
        modal: true,
        position: { my: 'top', at: 'top+150' },
        title : 'Load VPS Types',
        buttons: {'OK': {
            text: "OK",
            id: "selVPSType-Ok",
            click: function() {
            	val = $(this).parent().find("input[name='config-id']:checked").val();
            	name = $(this).parent().find("input[name='config-id']:checked").attr("data-VPSType-name");
				price = $(this).parent().find("input[name='config-id']:checked").attr("data-VPSType-price");
            	$('#setup_lw_vpstype').val(val).trigger('change');
				if (price != '') {
					var vpstype = name + ' / $' + price;
				} else {
					var vpstype = name ;
				}
            	$('#vpstype_name').html(vpstype);
            	$("#vpstype_name").css("color", "#4c4c4c");
            	$(this).dialog('close');
            }
        },'Cancel': {
            text: "Cancel",
            id: "selVPSType-Cancel",
            click: function() {
            $(this).dialog('close');
            }
        }},
        open: function(event, ui) {
		      $('#load-storm-vpstypes').load(location+'&ajaxload=vpstypes&conf_id='+$('#setup_lw_vpstype').val()+'&zone='+$('#setup_lw_zonecode').val(), function() {
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

    $('#setup_lw_vpstype').on('change', function(){
        if (($('#setup_lw_ostemplates').val() == '0') || ($('#setup_lw_vpstype').val() == '0')){
        	$('#advmode_submit').addClass('disabled');
        	$('#save_submit').addClass('disabled');
        } else {
			$('#advmode_submit').removeClass('disabled');
            $('#save_submit').removeClass('disabled');
        }
    });
});



function showTemplates()
{
    $("#load-storm-templates").dialog('open');
    $("#load-storm-templates").html("<p style=\"text-align:center\"><img src=\"../modules/servers/StormOnDemand/assets/images/admin/loading.gif\" alt=\"loading...\"/></p><p style=\"text-align:center;font-size:12px;margin-top:0px;margin-bottom:0px;\">Please note that this may take 30+ seconds to load all the data from our API.</p>");
}

function showZone()
{
    $("#load-storm-zones").dialog('open');
    $("#load-storm-zones").html("<p style=\"text-align:center\"><img src=\"../modules/servers/StormOnDemand/assets/images/admin/loading.gif\" alt=\"loading...\"/></p><p style=\"text-align:center;font-size:12px;margin-top:0px;margin-bottom:0px;\">Please note that this may take 30+ seconds to load all the data from our API.</p>");
}

function showVPSType()
{
    $("#load-storm-vpstypes").dialog('open');
    $("#load-storm-vpstypes").html("<p style=\"text-align:center\"><img src=\"../modules/servers/StormOnDemand/assets/images/admin/loading.gif\" alt=\"loading...\"/></p><p style=\"text-align:center;font-size:12px;margin-top:0px;margin-bottom:0px;\">Please note that this may take 30+ seconds to load all the data from our API.</p>");
}

function submitForm(action) {
	$("#wiz_page4_action").val(action);
	$('form#setupWizPg4').submit();
}

</script>

    	<h2 class="section-heading">
    		<i class="icon-tasks"></i>Liquid Web SSD VPS configuration
    	</h2>

	<form id="setupWizPg4" action="addonmodules.php?module=StormBilling&action=setup&pg=5" method="post">
    	<input type="hidden" name="wiz_page" value="4">
    	<input type="hidden" name="wiz_page4_action" id="wiz_page4_action" value="">
    	<h3 class="section-heading">
    		<i class="icon-book"></i> <?php echo MG_Language::translate('Product details');?>
    	</h3>
		<br/>
        <table class="table" width="100%" border="0" cellspacing="2" cellpadding="3">
			<tbody>
				<tr>
                    <td class="fieldlabel"><?php echo MG_Language::translate('Product Name :');?></td>
    				<td class="fieldarea">
    					<input type="text" name="setup_lw_productname" id="setup_lw_productname" style="width: 200px;" value="<?php if (!@$row['name']) { echo "Liquid Web VPS"; } else { echo $row['name']; };?>"/>
					</td>
					<td class="fieldlabel" rowspan="2"><?php echo MG_Language::translate('Product Description :');?></td>
    				<td class="fieldarea" rowspan="2">
                        <textarea name="setup_lw_description" id="setup_lw_description" rows="4" cols="80" class="form-control"><?php  echo htmlspecialchars(@$row['description']); ?></textarea>
					</td>
				</tr>
                <tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('Product Group :');?></td>
					<td class="fieldarea">
    					<select name="setup_lw_productgroup" id="setup_lw_productgroup" style="width: auto;">
    					<?php foreach ($productgroup as $key=>$value) { ?>
    						<option value="<?php echo $key;?>" <?php if ($key==@$row['gid']) {echo 'selected';}?>><?php echo $value;?></option>
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
					<td class="fieldlabel"><?php echo MG_Language::translate('Zone :');?></td>
					<td class="fieldarea">
						<b><span id="zone_name"><?php echo $zone[$row['configoption4']];?></span></b>
						<a href="javascript:;" onclick="showZone()"> <img width="16" height="16" class="absmiddle" alt="Load Zone" src="images/icons/search.png"></a>
						<div id="load-storm-zones"></div>
						<input type="hidden" name="setup_lw_zonecode" id="setup_lw_zonecode" value="<?php echo $row['configoption4'];?>">
					</td>
					<td class="fieldlabel"><?php echo MG_Language::translate('OS Templates :');?></td>
					<td class="fieldarea">
						<b><span id="setup_lw_ostemplates_name"><?php echo $ostemplate[$row['configoption5']];?></span></b>
						<input type="hidden" name="setup_lw_ostemplates" id="setup_lw_ostemplates" style="width: 200px;" value="<?php echo $row['configoption5'];?>"/>
						<a href="javascript:;" onclick="showTemplates()"> <img width="16" height="16" class="absmiddle" alt="Load Template" src="images/icons/search.png"></a>
						<div id="load-storm-templates"></div>
					</td>
				</tr>
				<tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('VPS Type :');?></td>
					<td class="fieldarea">
						<b><span id="vpstype_name"><?php echo $vpstype[$row['configoption7']];?></span></b>
    					<a href="javascript:;" onclick="showVPSType()"> <img width="16" height="16" class="absmiddle" alt="Load Zone" src="images/icons/search.png"></a>
    					<div id="load-storm-vpstypes"></div>
    					<input type="hidden" name="setup_lw_vpstype" id="setup_lw_vpstype" style="width: 200px;" value="<?php echo $row['configoption7'];?>" />
					</td>
					<td class="fieldlabel"><?php echo MG_Language::translate('Backup Enabled :');?></td>
					<td class="fieldarea"><input type="checkbox" name="setup_lw_backup" id="setup_lw_backup" <?php if ($row['configoption8']=='on') {echo 'checked';}?>/></td>
				</tr>
				<tr>
					<!--<td class="fieldlabel"><?php //echo MG_Language::translate('Backup Plan :');?></td>
					<td class="fieldarea">
    					<select name="setup_lw_backupplan" id="setup_lw_backupplan" style="width: 150px;">
    					<?php /*foreach ($backupPlans as $options) { ?>
    						<option value="<?php echo $options['name'];?>" <?php if ($row['configoption9']==$options['name']) {echo 'selected';}?>><?php echo $options['value'];?></option>
    					<?php } */?>
    					</select>
                    </td>-->
                    <input type="hidden" name="setup_lw_backupplan" id="setup_lw_backupplan" value="quota"/></td>
    				<td class="fieldlabel"><?php echo MG_Language::translate('Backup Quota :');?></td>
    				<td class="fieldarea"><select name="setup_lw_backupquota" id="setup_lw_backupquota" style="width: 150px;">
    					<?php foreach ($backupQuota as $key=>$val) { ?>
    						<option value="<?php echo $key;?>" <?php if ($row['configoption10']==$key) {echo 'selected';}?>><?php echo $val;?></option>
    					<?php } ?>
    					</select>
					</td>
					<td class="fieldlabel"><?php echo MG_Language::translate('Monitoring :');?></td>
					<td class="fieldarea"><input type="checkbox" name="setup_lw_monitoring" id="setup_lw_monitoring" <?php if ($row['configoption14']=='on') {echo 'checked';}?>/><?php echo MG_Language::translate('Check if you want to display monitoring in the Client Area');?></td>
					<td class="fieldarea"><input type="hidden" name="setup_lw_ips" id="setup_lw_ips" style="width: 200px;" value="1"/></td>
				</tr>
				<tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('Firewall :');?></td>
					<td class="fieldarea"><input type="checkbox" name="setup_lw_firewall" id="setup_lw_firewall" <?php if ($row['configoption15']=='on') {echo 'checked';}?>/><?php echo MG_Language::translate('Check if you want to enable firewall managing in the Client Area');?></td>
					<td class="fieldlabel"><?php echo MG_Language::translate('IPs Management :');?></td>
					<td class="fieldarea"><input type="checkbox" name="setup_lw_ipsmgt" id="setup_lw_ipsmgt" <?php if ($row['configoption16']=='on') {echo 'checked';}?>/><?php echo MG_Language::translate('Check if you want to enable IP managing in the Client Area');?></td>
				</tr>
			</tbody>
		</table>

    	<h3 class="section-heading">
    		<i class="icon-money"></i> <?php echo MG_Language::translate('Pricing (Monthly)');?>
    	</h3>
		<br/>
		<table class="table" width="100%" border="0" cellspacing="2" cellpadding="3">
			<tbody>
				<tr>
					<td class="fieldlabel" width="10%;" >
					    <?php echo MG_Language::translate('Fixed');?>
    				</td>
					<td class="fieldarea" width="10%;" style="text-align: left;">
						<?php $price = $row['price'];
						      if ($price == '') {$price = '0.00';}
						?>
						<input type="text" name="setup_lw_price" id="setup_lw_price" value="<?php echo $price;?>">
					</td>
					<td class="fieldarea" width="80%;">&nbsp;</td>
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
