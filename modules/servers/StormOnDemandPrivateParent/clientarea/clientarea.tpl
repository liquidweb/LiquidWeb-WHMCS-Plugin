{if $template neq "six" and $custom_template neq "YES"}
    {include file="$template/pageheader.tpl" title=$product}
{/if}
{literal}
    <style>
        .op_errorbox{
          background-repeat: no-repeat;
          background-position: 5px;
          margin: 10px 5px 10px 5px;
          padding: 6px 5px 6px 45px;
          min-height: 28px;
          background-color: #F2D4CE;
          border: 1px solid #AE432E;
          color: #cc0000;
          text-align: left;
          -moz-border-radius: 5px;
          -webkit-border-radius: 5px;
          -o-border-radius: 5px;
          border-radius: 5px;
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
{if $loadBootstrap}
    <link href="includes/StormOnDemand/assets/css/bootstrap.css" rel="stylesheet">
{/if}

{if $error}
    <div class="op_errorbox">
        <center><strong><span style="color:#AE432E;">{$error}</span></strong></center>
    </div>
{/if}

{if $info}
    <div class="op_success">
        <center><strong><span style="color:#69990F;">{$info}</span></strong></center>
    </div>
{/if}

{literal}
    <style type="text/css">
        .table-vertical-middle td
        {
            vertical-align: middle!important;
        }

        #mg-wrapper table select
        {
            width: auto!important;
        }

        #mg-wrapper table input[type="text"]
        {
            width: 96%!important;
        }
    </style>
{/literal}

<div id="mg-wrapper">
    <div style="position: relative; min-height: 200px">
        {if $template neq "six" and $custom_template neq "YES"}
        <div style="width: 155px; position: absolute;">
            <ul style="list-style-type: none; margin-left: 0px;">
                <li><a class="" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=clientStart"><i class="icon-arrow-up"></i> Start</a></li>
                <li><a class="" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=clientShutdown"><i class="icon-arrow-down"></i> Shutdown</a></li>
                <li><a class="" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=clientReboot"><i class="icon-refresh"></i> Reboot</a></li>
                <li><a class="" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=restore"><i class="icon-repeat"></i> Restore</a></li>
                <li><a class="" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=history"><i class="icon-list-alt"></i> History</a></li>

                {if in_array('firewall', $buttons)}
                     <li><a class="" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=firewall"><i class="icon-fire"></i> Firewall</a></li>
                {/if}
                {if in_array('ipmanagement', $buttons)}
                     <li><a class="" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=ipmanagement"><i class="icon-globe"></i> IP Management</a></li>
                {/if}
                {if in_array('backups', $buttons)}
                     <li><a class="" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=backups"><i class="icon-backward"></i> Backups</a></li>
                {/if}
                {if in_array('blockStorage', $buttons)}
                    <li><a class="" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=blockStorage"><i class="icon-cloud"></i> Block Storage</a></li>
                {/if}
            </ul>

            <p>
                <input class="btn" type="button" onclick="window.location='clientarea.php?action=productdetails&id={$id}'" value="« Server Details">
            </p>
        </div>
    {/if}
        <div {if $template neq "six" and $custom_template neq "YES"}style="margin-left: 200px"{/if}>
            {include file="$subpage"}
        </div>
    </div>
</div>