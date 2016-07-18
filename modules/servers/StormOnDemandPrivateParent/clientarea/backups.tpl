<link href="{$systemurl}/modules/servers/StormOnDemandPrivateParent/assets/css/ui.all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{$systemurl}/modules/servers/StormOnDemandPrivateParent/assets/js/jqueryui.js"></script>

{literal}
    <script type="text/javascript">
        $(function(){
            $(".restore").click(function(event){
                form = $(this).closest('form');
                event.preventDefault();
                $("#restore-dialog").dialog({
                resizable: false,
                height:140,
                modal: true,
                buttons:
                {
                    "Yes": function() {
                        $( this ).dialog( "close" );
                        $(form).submit();
                    },
                    Cancel: function() {
                    $( this ).dialog( "close" );
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

<table class="table table-framed table table-condensed table-vertical-middle" style="width: 100%">
    <thead>
        <tr>
            <th>Backup</th>
            <th style="width: 70px">Size</th>
            <th style="width: 70px"></th>
        </tr>
    </thead>
    <tbody>
        {if $list}
            {foreach from=$list item=b}
                <tr>
                    <td>{$b.name}</td>
                    <td>{$b.size}GB</td>
                    <td>
                        <form action="" method="post" style="margin: 0!important">
                            <input type="hidden" name="modaction" value="restore" />
                            <input type="hidden" name="backup_id" value="{$b.id}" />
                            <input type="submit" value="Restore" class="restore btn btn-danger btn-small"/>
                        </form>
                    </td>
                </tr>
            {/foreach}
        {else}
            <tr>
                <td colspan="3" style="text-align: center"><b>You have no backups</b></td>
            </tr>
        {/if}
    </tbody>
</table>

<div id="restore-dialog" title="Restore" style="display: none">Are you sure you want to restore?</div>