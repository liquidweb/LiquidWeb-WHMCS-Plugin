<link href="{$systemurl}/modules/servers/StormOnDemand/assets/css/ui.all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{$systemurl}/modules/servers/StormOnDemand/assets/js/jqueryui.js"></script>


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

{literal}
    <script type="text/javascript">
        $(function(){
            $(".restore").click(function(event){
                form = $(this).closest('form');
                event.preventDefault();

                $("#restore-dialog").dialog({
                resizable: false,
                height:200,
                modal: true,
                buttons:
                {
                    "Yes": function() {
                        $(".ui-dialog-titlebar-close").trigger('click');
                        $("#op_action").css("filter","alpha(opacity=20)");
                        $("#op_action").css("-moz-opacity","0.2");
                        $("#op_action").css("-khtml-opacity","0.2");
                        $("#op_action").css("opacity","0.2");

                        $("#modcmdworking").css("display","block");
                        $("#modcmdworking").css("padding","9px 50px 0");
                        $("#modcmdworking").fadeIn();
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
<div id="modcmdworking" style="display:none;text-align:center;"><img src="admin/images/loader.gif" /> &nbsp; Working...</div>
<div id="op_action">
{if $images}
    <h3>Restore from image</h3>
    <table class="table table-framed table table-condensed" style="width: 100%">
        <thead>
            <tr>
                <th>Image</th>
                <th style="width: 70px">Action</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$images item=i}
            <tr>
                <td>{$i.name}</td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="modaction" value="restore" />
                        <input type="hidden" name="type" value="image" />
                        <input type="hidden" name="image_id" value="{$i.id}" />
                        <input type="submit" value="Restore" class="restore btn"/>
                    </form>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/if}

{if $templates}
    <h3>Restore from template</h3>
    <table class="table table-framed table table-condensed" style="width: 100%">
        <thead>
            <tr>
                <th>Template</th>
                <th style="width: 70px">Action</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$templates item=t}
            <tr>
                <td>{$t.description}</td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="modaction" value="restore" />
                        <input type="hidden" name="type" value="template" />
                        <input type="hidden" name="template_id" value="{$t.id}" />
                        <input type="submit" value="Restore" class="restore btn"/>
                    </form>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/if}

{if $servers}
    <h3>Restore from your Servers</h3>
    <table class="table table-framed table table-condensed" style="width: 100%">
        <thead>
            <tr>
                <th>Template</th>
                <th style="width: 70px">Action</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$servers item=t}
            <tr>
                <td>{$t.package.domain}</td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="modaction" value="restore" />
                        <input type="hidden" name="type" value="server" />
                        <input type="hidden" name="server_id" value="{$t.uniq_id}" />
                        <input type="submit" value="Restore" class="restore btn"/>
                    </form>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/if}

<div id="restore-dialog" title="Restore" style="display: none">Are you sure you want to restore?</div>
</div>