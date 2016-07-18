<link href="{$systemurl}/modules/servers/LiquidWeb/assets/css/ui.all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{$systemurl}/modules/servers/LiquidWeb/assets/js/jqueryui.js"></script>

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

<table class="table table-framed table table-condensed" style="width: 100%">
    <thead>
        <tr>
            <th>IP</th>
            <th>Netmask</th>
            <th>Gateway</th>
            <th>Broadcat</th>
            <th></th>
        </tr>
    </thead>
    {foreach from=$list item=ip key=k}
        <tr>
            <td>{$ip.ip}</td>
            <td>{$ip.netmask}</td>
            <td>{$ip.gateway}</td>
            <td>{$ip.broadcast}</td>
            <td>
                {if $k > 0}
                    <form action="" method="post">
                       <input type="hidden" name="modaction" value="remove" />
                       <input type="hidden" name="ip" value="{$ip.ip}" />
                       <input type="submit" value="Remove" class="btn remove"/>
                    </form>
                {/if}
            </td>
        </tr>
    {/foreach}
    <tfoot>
        <tr>
            <td colspan="5">
                {if $ip_count && count($list) < $ip_count}
                    <form action="" method="post">
                        <input type="hidden" name="modaction" value="add" />
                        New IP's to add <input type="text" name="ip_amount" value="0" style="width: 30px;margin-bottom:0px !important;"/><input type="submit" value="Add" class="btn" style="margin-left: 5px"/>
                    </form>
                {else}
                    <div style="text-align: center; color: red">IP's limit reached.</div>
                {/if}
            </td>
        </tr>
    </tfoot>
</table>


<div id="remove-dialog" title="Restore" style="display: none">Are you sure you want to remove?</div>