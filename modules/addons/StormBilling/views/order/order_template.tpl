<h2>{$mg_lang.usage_records_pricing}:</h2>
<table>
    <thead>
        <tr>
            <th>{$mg_lang.usage_record}</th>
            <th>{$mg_lang.price}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$resources item=i}
        <tr>
            <td>{$i.FriendlyName}</td> 
            <td>{$i.price} {if $i.unit}{$i.unit}{else}{if $i.Unit|trim == 'Item'} Unit {else} {$i.Unit} {/if}{/if}</td>
        </tr>
        {/foreach}
    </tbody>
</table> 