{if $template neq "six" and $custom_template neq "YES"}
  {include file="$template/pageheader.tpl" title=$product}
{/if}
{literal}
    <style>
        .op_errorbox{
          background-repeat: no-repeat;
          background-position: 5px;
          margin: 10px 5px 10px 5px;
          padding: 6px 5px 6px 45px;
          min-height: 28px;
          background-color: #F2D4CE;
          border: 1px solid #AE432E;
          color: #cc0000;
          text-align: left;
          -moz-border-radius: 5px;
          -webkit-border-radius: 5px;
          -o-border-radius: 5px;
          border-radius: 5px;
        }
        .op_success{
          background-repeat: no-repeat;
          background-position: 5px;
          margin: 10px 5px 10px 5px;
          padding: 6px 5px 6px 45px;
          min-height: 28px;
          background-color: #D9E6C3;
          border: 1px solid #77AB13;
          color: #000;
          text-align: left;
          -moz-border-radius: 5px;
          -webkit-border-radius: 5px;
          -o-border-radius: 5px;
          border-radius: 5px;

        }
    </style>
{/literal}
{if $error}
    <div class="op_errorbox" {if $template eq "six" or $custom_template eq "YES" }style="padding: 6px 5px 6px 5px;"{/if}>
        <center><strong><span style="color:#AE432E;">{$error}</span></strong></center>
    </div>
{/if}

{if $info}
    <div class="op_success" {if $template eq "six" or $custom_template eq "YES" }style="padding: 6px 5px 6px 5px;"{/if}>
        <center><strong><span style="color:#69990F;">{$info}</span></strong></center>
    </div>
{/if}

<div style="position: relative; min-height: 200px">
    <div style="width: 180px; position: absolute;">
    <!--
        <ul style="list-style-type: none">
            {foreach from=$storm_links key=k item=l}
                <li><a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a={$l}">{$k}</a></li>
            {/foreach}
        </ul>
    -->
    {if $template neq "six" and $custom_template neq "YES"}
        <p>
            <input class="btn" type="button" onclick="window.location='clientarea.php?action=productdetails&id={$id}'" value="« Back">
        </p>
    {/if}
    </div>
    <div {if $template neq "six" and $custom_template neq "YES" }style="margin-left: 200px"{/if}>
        {include file="$subpage"}
    </div>
</div>