<hr />
<div class="col2half">
    <h3>{$mg_lang.usage_records}:</h3>
</div>
<div class="clear"></div>
{foreach from=$resources item=r}
    {if $r.type != 'disabled'}
        <div class="col2half">
            <p> 
            <h4>{$r.FriendlyName}</h4>
                {if $r.ClientAreaDescription}
                    {$r.ClientAreaDescription}
                {else}
                    {$r.usage}{if $r.unit}{$r.unit}{else}{$r.Unit}{/if} ({$r.total})
                {/if}
            </p>
        </div>
    {/if}
{/foreach}
<div class="clear"></div> 

{if $credit_billing}
    <hr />
    <div class="col2half">
        <h3>{$mg_lang.credit_billing}:</h3>
    </div>
    <div class="clear"></div> 
    <div class="col2half">
        <p> 
        <h4>{$mg_lang.current_credit}</h4>
        {$credit_billing.total}
        </p>
    </div> 
    {*<div class="col2half">
        <p> 
        <h4>{$mg_lang.current_paid}</h4>
        {$credit_billing.paid}
        </p>
    </div>*}
    <div class="clear"></div> 
{/if}
