<link href="{$systemurl}/modules/servers/LiquidWebPrivateParent/assets/css/ui.all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{$systemurl}/modules/servers/LiquidWebPrivateParent/assets/js/jqueryui.js"></script>

{literal}
    <script type="text/javascript">
        $(function(){
            $(".remove").click(function(event){
                form = $(this).closest('form');
                event.preventDefault();
                $("#remove-dialog").dialog({
                resizable: false,
                height:200,
                modal: true,
                buttons:
                {
                    "Yes": function() {
                        $(".ui-dialog-titlebar-close").trigger('click');
                        $(form).submit();
                    },
                    Cancel: function() {
                        $(".ui-dialog-titlebar-close").trigger('click');
                }
                }
                });
            });
        });
    </script>
{/literal}

{if $template eq "six" or $custom_template eq "YES"}
    {literal}
    <script type="text/javascript">
          jQuery( document ).ready(function() {
              $("#Primary_Sidebar-Service_Details_Overview-Information").attr("href", "{/literal}{$systemurl}{literal}clientarea.php?action=productdetails&id={/literal}{$id}{literal}");
              $("#Primary_Sidebar-Service_Details_Overview-Information").click(function(){
                window.location.href = "{/literal}{$systemurl}{literal}clientarea.php?action=productdetails&id={/literal}{$id}{literal}";
              });

          });
     </script>
    {/literal}
{/if}

{if count($list) >= $ip_count}
    <div class="alert alert-warning">IP's limit reached.</div>
{/if}

<table class="table table-framed table table-condensed" style="width: 100%">
    <thead>
        <tr style="height: 30px!important">
            <th>IP</th>
            <th>Netmask</th>
            <th>Gateway</th>
            <th>Broadcat</th>
            <th style="width: 60px"></th>
        </tr>
    </thead>
    {foreach from=$list item=ip key=k}
        <tr style="height: 30px!important">
            <td>{$ip.ip}</td>
            <td>{$ip.netmask}</td>
            <td>{$ip.gateway}</td>
            <td>{$ip.broadcast}</td>
            <td>
                {if $k > 0}
                    <form action="" method="post" class="form-inline" style="margin: 0!important">
                       <input type="hidden" name="modaction" value="remove" />
                       <input type="hidden" name="ip" value="{$ip.ip}" />
                       <input type="submit" value="Remove" {if $template eq "six" or $custom_template eq "YES" }style="height:30px !important;"{/if} class="btn remove btn-danger btn-small"/>
                    </form>
                {/if}
            </td>
        </tr>
    {/foreach}
</table>

{if $ip_count && count($list) < $ip_count}
    <form action="" method="post" class="form-inline">
        <label class="control-label">New IP's to add</label>
        <input type="text" name="ip_amount" value="0" style="width: 30px"/>
        <input type="submit" class="btn btn-success" value="Add" />
        <input type="hidden" name="modaction" value="add" />
    </form>
{/if}
<div id="remove-dialog" title="Restore" style="display: none">Are you sure you want to remove?</div>