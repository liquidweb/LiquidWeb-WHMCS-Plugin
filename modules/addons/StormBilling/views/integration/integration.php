<?php
global $CONFIG;

echo '<h3>Usage Records in the Client Area</h3>';
echo '<div class="border-box">
        <div class="control-group">
             In order to enable your customers to monitor resource usage, open the file \'clientareaproductdetails.tpl\' located at:
        </div>
      </div>';

echo '<pre>'.$hosting_tpl.'</pre>';

echo '<div class="border-box">
        <div class="control-group">
           Find the following code:
        </div>
      </div>';
if (($CONFIG['Template'] == 'six') || (LW_CUSTOM_TEMPLATE_SIX == 'YES')){
  echo '<pre>'.  htmlentities('
        {if $moduleclientarea}
            <div class="text-center module-client-area">
                {$moduleclientarea}
            </div>
        {/if}').'</pre>';
}else{
    echo '<pre>'.  htmlentities('{if $moduleclientarea}<div class="moduleoutput">{$moduleclientarea|replace:\'modulebutton\':\'btn\'}</div>{/if}').'</pre>';
}
echo '<div class="border-box">
        <div class="control-group">
            Add this code <b>BEFORE</b> the line:
        </div>
      </div>';

echo '<pre>'.htmlentities('{$clientarea_pricing}').'</pre>';

echo '<h3 style="margin-top: 30px">Pricing on Order Form</h3>';
echo '<div class="border-box">
        <div class="control-group">
             In order to enable usage record pricing on order form, open the file \'configureproduct.tpl\' located at:
        </div>
      </div>';

echo '<pre>'.$order_tpl.'</pre>';

echo '<div class="border-box">
        <div class="control-group">
           Find the following line:
        </div>
      </div>';

echo '<pre>'.htmlentities('{if $productinfo.type eq "server"}').'</pre>';

echo '<div class="border-box">
        <div class="control-group">
            Add this code <b>BEFORE</b> the line:
        </div>
      </div>';

echo '<pre>'.htmlentities('{$order_pricing}').'</pre>';
?>
