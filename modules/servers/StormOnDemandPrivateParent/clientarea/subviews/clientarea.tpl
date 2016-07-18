{if $loadBootstrap}
    <link href="includes/StormOnDemand/assets/css/bootstrap.css" rel="stylesheet">
{/if}
    
{literal}
    <style>
        .whmcscontainer .moduleoutput
        {
            border: 0!important;
            padding: 0!important;
        }

        .storm-menu
        {
            list-style-type: none;
            overflow: hidden;
            margin: 0 0 20px 0;
        }

        .storm-menu li
        {
            padding: 4px;
            /*width: 50%;*/
            margin: auto;
            display: inline-block;
        }

        .storm-menu li a
        {
            /*width: 90%;*/ 
        }
        
        #mg-wrapper table select
        {
            width: auto!important;
        }

        #mg-wrapper table input[type="text"]
        {
            width: 96%!important;
        }
        .op_success{
          background-repeat: no-repeat;
          background-position: 5px;
          margin: 10px 5px 10px 5px;
          padding: 6px 5px 6px 45px;
          min-height: 28px;
          background-color: #D9E6C3;
          border: 1px solid #77AB13;
          color: #000;
          text-align: left;
          -moz-border-radius: 5px;
          -webkit-border-radius: 5px;
          -o-border-radius: 5px;
          border-radius: 5px;
        
        }
    </style>
{/literal}

{literal}
    <script type="text/javascript">
          jQuery( document ).ready(function() {
              setTimeout(function(){
              $(".op_success").hide("slow");
           }, 2000);
          });
            jQuery(function(){
                jQuery.get("clientarea.php?action=productdetails&id={/literal}{$params.serviceid}{literal}&stormajax=status", function(data){
                    jQuery("#server-status").html(data);
                });
                
                jQuery.get("clientarea.php?action=productdetails&id={/literal}{$params.serviceid}{literal}&stormajax=history", function(data){
                    jQuery("#server-history").html(data);
                });
                
                setInterval(function(){
                    jQuery.get("clientarea.php?action=productdetails&id={/literal}{$params.serviceid}{literal}&stormajax=status", function(data){
                        jQuery("#server-status").html(data);
                    });
                }, 10000);
            });
     </script>
 {/literal}
 
 {if $monitoring}
    {literal}
        <script type="text/javascript">
                jQuery(function(){
                    jQuery.get("clientarea.php?action=productdetails&id={/literal}{$params.serviceid}{literal}&stormajax=bandwidth_stats", function(data){
                        jQuery("#bandwidth-stats").html(data);
                    });

                    jQuery.get("clientarea.php?action=productdetails&id={/literal}{$params.serviceid}{literal}&stormajax=load_stats", function(data){
                        jQuery("#load-stats").html(data);
                    });

                    /* BANDWIDTH */
                    jQuery("#select-bandwidth-frequency").change(function(){
                        val = jQuery(this).val();
                        jQuery("#bandwidth-graph").attr("src", "clientarea.php?action=productdetails&id={/literal}{$params.serviceid}{literal}&stormajax=bandwidth_graph&frequency="+val);
                    });

                    /* LOAD */
                    jQuery("#select-load-duration").change(function(){
                        val = jQuery(this).val();
                        jQuery("#load-graph").attr("src", "clientarea.php?action=productdetails&id={/literal}{$params.serviceid}{literal}&stormajax=load_graph&duration="+val);
                    });

                });
         </script>
     {/literal}
 {/if}

<div id="mg-wrapper"> 
    {if $smarty.get.success}
      <div class="op_success"><center style="margin-top:5px;"><strong><span style="color:#69990F;">{$smarty.get.success}.</span></strong></center></div> 
    {/if}
    {if $buttons}
        <ul class="storm-menu">
            <div style="overflow: auto; padding: 4px 0;">
                <a style="width: 15%" class="btn btn-success" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=clientStart"><i style="background-image: url('modules/servers/StormOnDemandPrivateParent/assets/images/glyphicons-halflings.png') !important;" class="icon-arrow-up"></i> Start</a>
                <a style="width: 15%" class="btn btn-danger" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=clientShutdown"><i style="background-image: url('modules/servers/StormOnDemandPrivateParent/assets/images/glyphicons-halflings.png') !important;" class="icon-arrow-down"></i> Shutdown</a>
                <a style="width: 15%" class="btn btn-warning" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=clientReboot"><i style="background-image: url('modules/servers/StormOnDemandPrivateParent/assets/images/glyphicons-halflings.png') !important;" class="icon-refresh"></i> Reboot</a>
                <a style="width: 15%" class="btn btn-primary" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=restore"><i style="background-image: url('modules/servers/StormOnDemandPrivateParent/assets/images/glyphicons-halflings.png') !important;" class="icon-repeat"></i> Restore</a>
                <a style="width: 15%" class="btn btn-info" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=history"><i style="background-image: url('modules/servers/StormOnDemandPrivateParent/assets/images/glyphicons-halflings.png') !important;" class="icon-list-alt"></i> History</a>
            </div>
            
            <div style="overflow: auto; padding: 4px 0;">
                {if in_array('firewall', $buttons)}
                    <a style="width: 20%" class="btn" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=firewall"><i class="icon-fire"></i> Firewall</a>
                {/if} 
                {if in_array('ipmanagement', $buttons)}
                    <a style="width: 20%" class="btn" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=ipmanagement"><i class="icon-globe"></i> IP Management</a>
                {/if}
                {if in_array('backups', $buttons)}
                    <a style="width: 20%" class="btn" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=backups"><i class="icon-backward"></i> Backups</a>
                {/if} 
                {if in_array('blockStorage', $buttons)}
                   <a style="width: 20%" class="btn" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=blockStorage"><i class=""></i> Block Storage</a>
                {/if}
            </div>
        </ul>
    {/if}

    <table class="table table-framed table table-condensed" style="width: 100%">
        <tr>
            <td style="width: 50%">Server</td>
            <td>{$details.domain}</td>
        </tr>               
        <tr>
            <td>IP</td>
            <td>{$details.ip}</td>
        </tr>
        <tr>
            <td>Template</td>
            <td>{$details.template_description}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td id="server-status"><img src="modules/servers/StormOnDemandPrivateParent/assets/images/loading.gif" alt="Loading..."/></td>
        </tr>
    </table>

    {if $monitoring} 
        <div id="server-bandwidth">
            <h4 style="text-align: left; overflow: hidden; margin-bottom: 10px">
                Bandwidth Statistics
                <form action="" method="post" style="float: right">{$template}
                    <select id="select-bandwidth-frequency" name="frequency" style="margin: 0!important;height:44px !important;">
                        <option selected="selected" value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </form>
            </h4>
            <img id="bandwidth-graph" src="clientarea.php?action=productdetails&id={$params.serviceid}&stormajax=bandwidth_graph&frequency=daily" alt="Bandwidth Graph"/>
            <div id="bandwidth-stats" style="margin-top: 10px">
                <p style="text-align: center"><img src="modules/servers/StormOnDemandPrivateParent/assets/images/loading.gif" alt="Loading..."/></p>
            </div>
        </div>  
        <div id="server-health">
            <h4 style="text-align: left; overflow: hidden; margin-bottom: 10px">
                Server Load
                <form action="" method="post" style="float: right">
                    <select id="select-load-duration" name="duration" style="margin: 0!important;height:44px !important;">
                        <option selected="selected" value="6hour">6 Hour</option>
                        <option value="12hour">12 Hour</option>
                        <option value="day">Day</option>
                        <option value="3day">3 Day</option>
                        <option value="week">Week</option>
                        <option value="2week">2 Week</option>
                    </select>
                </form>
            </h4>
            <img id="load-graph" src="clientarea.php?action=productdetails&id={$params.serviceid}&stormajax=load_graph&duration=6hour" alt="Load Graph"/>
            <div id="load-stats" style="margin-top: 10px">
                <p style="text-align: center"><img src="modules/servers/StormOnDemandPrivateParent/assets/images/loading.gif" alt="Loading..."/></p>
            </div>
        </div>
    {/if}

    <div>
        <h4 style="text-align: left; overflow: hidden; margin-bottom: 10px">History</h4>
        <div id="server-history">
            <p style="text-align: center"><img src="modules/servers/StormOnDemandPrivateParent/assets/images/loading.gif" alt="Loading..."/></p>
        </div>
    </div>
</div>