{if $template eq "six"  or $custom_template eq "YES"}
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
                        <form action="" method="post">
                            <input type="hidden" name="modaction" value="restore" />
                            <input type="hidden" name="backup_id" value="{$b.id}" />
                            <input type="submit" value="Restore" />
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