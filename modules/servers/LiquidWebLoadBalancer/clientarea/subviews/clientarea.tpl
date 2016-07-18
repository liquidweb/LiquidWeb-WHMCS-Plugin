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
        }

        .storm-menu li a
        {
            /*width: 90%;*/ 
        }
    </style>
{/literal}

{if $buttons}
    <ul class="storm-menu" style="-webkit-padding-start:0px;padding-left:0px;">
        {foreach from=$buttons key=k item=i}
            <li>
                <a class="btn btn-default" href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a={$i}">{$k}</a>
            </li>
        {/foreach}
    </ul>
{/if}

<h4 style="text-align: left">Load Balancer Details</h4>
<table class="table table-framed table table-condensed" style="width: 100%">          
    <tr>
        <td style="width: 50%">VIP</td>
        <td>{$details.vip}</td>
    </tr>
    <tr>
        <td>Strategy</td>
        <td>{$details.strategy}</td>
    </tr>
    <tr>
        <td>Session Persistence</td>
        <td>{if $details.session_persistence} enabled {else} disabled {/if}</td>
    </tr>
</table> 


{if $details.services}
    <h4 style="text-align: left">Services</h4>
    <table id="table_service" class="table table-framed table table-condensed" style="width: 100%">
        <thead>
            <tr>
                <td style="width: 50%">Source Port</td>
                <td>Destination Port</td>
            </tr>
        </thead>
        <tbody>
            {foreach from=$details.services item=i}
                <tr>
                    <td>{$i.src_port}</td>
                    <td>{$i.dest_port}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}

{if $details.nodes}
    <h4 style="text-align: left">Nodes</h4>
    <table id="table_node" class="table table-framed table table-condensed" style="width: 100%">
        <thead>
            <tr>
                <td style="width: 50%">Domain</td>
                <td>IP</td>
            </tr>
        </thead>
        <tbody>
            {foreach from=$details.nodes item=i}
                <tr>
                    <td>{$i.domain}</td>
                    <td>{$i.ip}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}