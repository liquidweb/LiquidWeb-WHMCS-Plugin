    	<h2 class="section-heading">
    		<i class="icon-magic"></i>Select the product you wish to configure
    	</h2>
    <div style="width: 800px;text-align: justify;">
    <form id="setupWizPg1" action="addonmodules.php?module=StormBilling&action=setup&pg=2" method="post" >
    	<input type="hidden" name="wiz_page" value="1">
       	<div class="row controls" style="margin-bottom:20px;">
      		<div style="margin-left:50px">
      			<h3><input type="checkbox" name="setup_lw_ssd_vps" id="setup_lw_ssd_vps" checked="checked"/>
      			<span class="help-inline"><?php echo MG_Language::translate('LiquidWeb SSD VPS'); ?></span></h3>
      			<div style="margin-left:50px;"><?php echo MG_Language::translate('Choose this option if you want to resell the Liquidweb\'s standard SSD VPS line, ');?></div>
      			<div style="margin-left:50px;"><?php echo MG_Language::translate('for more info please visit ');?><a target="_blank" href="https://www.liquidweb.com/vps.html">https://www.liquidweb.com/vps.html</a></div>
      		</div>
    	</div>

       	<div class="row controls" style="margin-bottom:20px;">
    	  	<div style="margin-left:50px">
      			<h3><input type="checkbox" name="setup_lw_pri_cld" id="setup_lw_pri_cld"  checked="checked"/>
      			<span class="help-inline"><?php echo MG_Language::translate('LiquidWeb Private Cloud');?></span></h3>
      			<div style="margin-left:50px;"><?php echo MG_Language::translate('Storm Private Cloud allows you to create your own private cloud environment, within which you can create, move, resize or destroy any number of virtual instances. With minute control of your resources and the ability to move instances to the public cloud or create a public network of private cloud servers, the usage possibilities are endless.');?></div>
      			<div style="margin-left:50px;"><?php echo MG_Language::translate('for more info please visit ');?><a target="_blank" href="https://www.liquidweb.com/storm/private-cloud.html">https://www.liquidweb.com/storm/private-cloud.html</a></div>
      		</div>
    	</div>
    	<div style="margin-left:70px; margin-top:20px;text-align: right;">
    		<a class="btn btn-success clearfix" href="" onclick="document.forms['setupWizPg1'].submit();return false;">Next <i class="icon-chevron-right"></i></a>
    	</div>
    </form>
