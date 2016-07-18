{if $template neq "six" and $custom_template neq "YES"}
  {include file="$template/pageheader.tpl" title=$product}
{/if}


{if $error}
    <div class="alert-message error alert">
        <p>{$error}</p>
    </div>
{/if}

{if $info}
    <div class="alert-message success alert">
        <p>{$info}</p>
    </div>
{/if}

<div style="position: relative; min-height: 200px">
    <div style="width: 180px; position: absolute;">
        <ul style="list-style-type: none">
            {foreach from=$storm_links key=k item=l}
                <li><a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a={$l}">{$k}</a></li>
            {/foreach}
        </ul>

        <p>
            <input class="btn" type="button" onclick="window.location='clientarea.php?action=productdetails&id={$id}'" value="« Back to Server Details">
        </p>
    </div>
    <div style="margin-left: 200px">
        {include file="$subpage"}
    </div>
</div>