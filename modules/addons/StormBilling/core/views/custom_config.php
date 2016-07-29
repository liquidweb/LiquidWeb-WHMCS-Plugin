<?php

include_once StormBillingDIR.DS.'class.LWCustomConfig.php';

$p = new LWCustomConfig();
$conf = $p->getConfigs();

if(isset($_REQUEST['savecustomconfig'])) {
    $configs = array('custom_template'=>'NO', 'log_api_calls'=>'NO', 'log_api_errors'=>'NO');

    if(isset($_REQUEST['custom_template'])) {
        $configs['custom_template'] = 'YES';
    }
    if(isset($_REQUEST['modulelog_api_errors'])) {
        $configs['log_api_errors']='YES';
    }
    if(isset($_REQUEST['modulelog_api_calls'])) {
        $configs['log_api_calls']='YES';
    }

    $conf = $p->saveConfigs($configs);
}
?>
    <div id="mg-content" class="right">

    	<div id="top-bar">

        	<div id="module-name">
            	<h2>Liquid Web Custom Configuration</h2>
            </div>
            <!--<div class="clear"></div>-->
            <a class="slogan" href="http://www.liquidweb.com" target="_blank" alt="Liquid Web">
                <span class="lw-logo"></span>
            </a>
        	</div><!-- end of TOP BAR -->

    	<div class="inner">
        <h2 class="section-heading">

<i class="icon-edit"></i>Configuration

</h2>

<?php
$using_cust_tmplt = '';
if ($conf['custom_template'] == 'YES') {
    $using_cust_tmplt = ' checked ';
}

$modulelog_api_errors = '';
if ($conf['log_api_errors'] == 'YES') {
    $modulelog_api_errors = ' checked ';
}

$modulelog_api_calls = '';
if ($conf['log_api_calls'] == 'YES') {
    $modulelog_api_calls = ' checked ';
}
?>

<form id="migrationForm" action="" method="post" >
	<h3>Custom theme</h3>
	<input type="hidden" value="1" name="savecustomconfig" />
   	<div class="row controls" style="margin-bottom:20px;">
  		<div style="margin-left:50px">
  			<input type="checkbox" name="custom_template" <?php echo $using_cust_tmplt;?> id="custom_template" />
  			<span class="help-inline"><?php echo MG_Language::translate('I am using custom template, which is derived from WHMCS template "SIX".');?></span>
  		</div>
	</div>

	<h3>Module Log</h3>
   	<div class="row controls" style="margin-bottom:20px;">
	  	<div style="margin-left:50px">
  			<input type="checkbox" name="modulelog_api_errors" <?php echo $modulelog_api_errors;?> id="modulelog_api_errors" />
  			<span class="help-inline"><?php echo MG_Language::translate('Log Liquid Web api error to <a href="systemmodulelog.php" target="_blank">"System Module Debug Log"</a>');?></span>
  		</div>
	  	<div style="margin-left:50px">
  			<input type="checkbox" name="modulelog_api_calls" <?php echo $modulelog_api_calls;?> id="modulelog_api_calls" />
  			<span class="help-inline"><?php echo MG_Language::translate('Log all Liquid Web api calls to <a href="systemmodulelog.php" target="_blank">"System Module Debug Log"</a>');?></span>
  		</div>
	</div>
	<div style="margin-left:70px; margin-top:20px;">
		<button class="btn btn-success clearfix"><?php echo MG_Language::translate('Save Changes');?></button>
	</div>



</form>

        </div><!-- end of INNER -->
        <div class="overlay hide">
        </div>
    </div><!-- end of CONTENT -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <script src="'.$ASSETS_DIR.'/js/jquery.js"></script>
    <script src="'.$ASSETS_DIR.'/js/jquery-ui-1.9.1.custom.min.js"></script>
    <script src="'.$ASSETS_DIR.'/js/bootstrap.js"></script>

    <script src="'.$ASSETS_DIR.'/js/application.js"></script>
    <script src="'.$ASSETS_DIR.'/js/modulesgarden.js"></script>
