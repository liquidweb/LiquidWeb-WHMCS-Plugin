{if $template neq "six" and $custom_template neq "YES"}
  {include file="$template/pageheader.tpl" title=$product}
{/if}

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
        {if $template neq "six" and $custom_template neq "YES"}
        <p>
            <input class="btn" type="button" onclick="window.location='clientarea.php?action=productdetails&id={$id}'" value="« Back to Server Details">
        </p>
        {/if}
    </div>
<div>

<div {if $template neq "six" and $custom_template neq "YES"}style="margin-left: 200px"{/if}>
    <h3>Your Certificate</h3>
    <div style="padding-top:10px">
     <textarea style="width:100%; min-height:350px;">{if $your_certificate}{$your_certificate}{else}Certificate is empty.. {/if}</textarea>
    </div>
</div>