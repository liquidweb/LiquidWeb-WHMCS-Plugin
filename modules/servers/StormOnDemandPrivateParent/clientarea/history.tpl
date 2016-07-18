{literal}
    <style type="text/css">
        .storm-notification
        {
            background-color: #FCF8E3;
        }

        .storm-error
        {
            background-color: #F2DEDE;
        }
    </style>
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
            <th>Message</th>
            <th>Severity</th>
            <th>Start Date</th>
            <th>End Date</th>
        </tr>
    </thead>
    <tbody>
        {if $history}
            {foreach from=$history item=h}
                <tr class="{if $h.severity == 'Error'}alert-danger{/if}">
                    <td>{$h.description}</td>
                    <td>{$h.severity}</td>
                    <td>{$h.startdate}</td>
                    <td>{$h.enddate}</td>
                </tr>
            {/foreach}
        {else}
            <tr>
                <tr>
                    <td colspan="4"><b>Nothing to display</b></td>
                </tr>
            </tr>
        {/if}
    </tbody>
</table>

<div style="margin-top: 10px; overflow: hidden">
    {if $page > 1}
        <form action="" method="post" style="float: left; margin: 0!important">
            <input type="hidden" name="page" value="{$page-1}" />
            <input type="submit" value="Prev" class="btn"/>
        </form>
    {/if}

    {if $page < $page_total}
        <form action="" method="post" style="float: right; margin: 0!important">
            <input type="hidden" name="page" value="{$page+1}" />
            <input type="submit" value="Next" class="btn"/>
        </form>
    {/if}
</div>