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

<table class="table table-framed table table-condensed" style="width: 100%">
    <thead>
        <tr>
            <th>Message</th>
            <th>Severity</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$history item=h}
            <tr class="storm-{$h.severity|strtolower}">
                <td>{$h.description}</td>
                <td>{$h.severity}</td>
            </tr>
        {/foreach}
    </tbody>
    <thead>
        <tr>
            <td colspan="2" style="text-align: center;"><a href="clientarea.php?action=productdetails&id={$params.serviceid}&modop=custom&a=history"><b>Show Full History</b><a></td>
        </tr>
    </thead>
</table>