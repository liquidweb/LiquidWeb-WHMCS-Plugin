<?php
$action = 'addonmodules.php?module=StormBilling&action=setup&pg=5';
     if (isset($_SESSION['setup_lw_ssd_vps'])){
        $action = 'addonmodules.php?module=StormBilling&action=setup&pg=4';
     }
?>
    	<h2 class="section-heading">
    		<i class="icon-check"></i>Liquidweb new API account credentials
    	</h2>

	<div style="width: 80%;text-align: justify;">
	<form id="setupWizPg3" action="<?php echo $action;?>" method="post" >
    	<input type="hidden" name="wiz_page" value="3">
    	<div class="col-md-12 text-center" style="margin: 30px 0px 20px 0px; color:#CC0000;"><?php echo MG_Language::translate('"Please write down these credentials, after you close the box you will not be able to view the credentials again."');?></div>
		<table class="table" width="40%" border="0" cellspacing="2" cellpadding="3">
        	<tbody>
				<tr>
					<td class="fieldlabel"><h2><?php echo MG_Language::translate('API Username :');?></h2></td>
					<td class="fieldarea"><h1><label style="margin-left: 10px;"><?php echo $_SESSION['api_username'];?></label></h1></td>
				</tr>
				<tr>
					<td class="fieldlabel"><h2><?php echo MG_Language::translate('API Password :');?></h2></td>
					<td class="fieldarea"><h1><label style="margin-left: 10px;"><?php echo $_SESSION['api_password']; ?></label></h1></td>
				</tr>
        	</tbody>
        	</table>
    		<br/><br/><br/>
        <a class="btn btn-success clearfix" href="addonmodules.php?module=StormBilling&action=setup&pg=1"><i class="icon-chevron-left"></i> Back</a>
        <a class="btn btn-success clearfix pull-right" href="" onclick="document.forms['setupWizPg3'].submit();return false;">Continue <i class="icon-chevron-right"></i></a>
	</form>
	</div>
