<?php
$action = 'addonmodules.php?module=StormBilling&action=setup&pg=5';
if (isset($_SESSION['setup_lw_ssd_vps'])){
    $action = 'addonmodules.php?module=StormBilling&action=setup&pg=4';
}
?>

<script>
  $(function() {
    $( "#accordion" ).accordion();
  });
  </script>

	<h2 class="section-heading">
		<i class="icon-signin"></i>Liquid Web account authentication
	</h2>
		<div id="accordion">
		<h3 style="line-height: 2.5; color: #1c4b8c;">&nbsp;&nbsp;<?php echo MG_Language::translate('I have Liquid Web API credentials');?></h3>
    	<form id="setupWizPg2API" action="<?php echo $action;?>" method="post" >
    		<input type="hidden" name="wiz_page" value="2">
    		<input type="hidden" name="wiz_page2_usertype" value="API">
		    <div class="col-md-12 text-center" style="margin-bottom: 15px; margin-top: 20px;"><h3><?php echo MG_Language::translate('Please provide your Liquid Web API username and password to continue');?></h3></div>
    		<table class="table" width="40%" border="0" cellspacing="2" cellpadding="3">
        	<tbody>
				<tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('API Username :');?></td>
					<td class="fieldarea"><input type="text" name="setup_lw_username" id="setup_lw_username" style="width: 320px;" /></td>
				</tr>
				<tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('API Password :');?></td>
					<td class="fieldarea"><input type="password" name="setup_lw_password" id="setup_lw_password" style="width: 310px;"/></td>
				</tr>
        	</tbody>
        	</table>
        	<br/>
            <a class="btn btn-success clearfix" href="addonmodules.php?module=StormBilling&action=setup"><i class="icon-chevron-left"></i> Back</a>
            <a class="btn btn-success clearfix pull-right" href="" onclick="document.forms['setupWizPg2API'].submit();return false;">Continue <i class="icon-chevron-right"></i></a>
    	</form>
		<h3 style="line-height: 2.5; color: #1c4b8c;">&nbsp;&nbsp;<?php echo MG_Language::translate('Create a NEW Liquid Web API username and password for me');?></h3>
    	<form id="setupWizPg2ACCOUNT" action="addonmodules.php?module=StormBilling&action=setup&pg=3" method="post" >
    		<input type="hidden" name="wiz_page" value="2">
    		<input type="hidden" name="wiz_page2_usertype" value="ACCOUNT">
			<div class="col-md-12 text-center" style="margin-bottom: 15px; margin-top: 20px;"><h3><?php echo MG_Language::translate('Please provide your Liquid Web ACCOUNT username and password to continue');?></h3></div>
    		<table class="table" width="40%" border="0" cellspacing="2" cellpadding="3">
        	<tbody>
				<tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('Account Username :');?></td>
					<td class="fieldarea"><input type="text" name="setup_lw_username" id="setup_lw_username" style="width: 320px;" /></td>
				</tr>
				<tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('Account Password :');?></td>
					<td class="fieldarea"><input type="password" name="setup_lw_password" id="setup_lw_password" style="width: 310px;"/></td>
				</tr>
				<tr>
					<td class="fieldlabel"><?php echo MG_Language::translate('Email Address :');?></td>
					<td class="fieldarea"><input type="email" name="setup_lw_email" id="setup_lw_email" style="width: 310px;"/></td>
				</tr>
        	</tbody>
        	</table>
        	<br/>
            <a class="btn btn-success clearfix" href="addonmodules.php?module=StormBilling&action=setup"><i class="icon-chevron-left"></i> Back</a>
            <a class="btn btn-success clearfix pull-right" href="" onclick="document.forms['setupWizPg2ACCOUNT'].submit();return false;">Create new API account <i class="icon-chevron-right"></i></a>
    	</form>
	</div>
