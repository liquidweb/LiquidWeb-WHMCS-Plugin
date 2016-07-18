
<script type="text/javascript">
    {literal}
        $(function(){
            var max = 0;

            $(".remove").click(function(event){
                event.preventDefault();
                $(this).parents("tr").remove();
            });

            $("#add_rule").click(function(event){
                event.preventDefault();

                if(!max)
                {
                    last = $("#table_rules tbody tr").size();
                    last--;
                    if(last <= 0)
                    {
                        last = 1;
                    }
                    max = last;
                }
                else
                    max++;

                $("#table_rules tbody").append('<tr><td><input type="text" name="advanced['+max+'][label]" value="" style="width: 100px"/></td><td><input type="text" name="advanced['+max+'][source_ip]" value="" style="width: 105px"/></td><td><input type="text" name="advanced['+max+'][destination_ip]" value="" style="width: 105px"/></td><td><input type="text" name="advanced['+max+'][destination_port]" value="" style="width: 50px"/></td><td><select name="advanced['+max+'][protocol]"><option value="any">Any</option><option value="tcp">TCP</option><option value="udp">UDP</option><option value="icmp">ICMP</option></select></td><td><select name="advanced['+max+'][action]"><option value="allow">Allow</option><option value="deny">Deny</option></select></td><td><a href="#" class="remove btn">Remove</a></td></tr>');

                $(".remove").click(function(event){
                    event.preventDefault();
                    $(this).parents("tr").remove();
                });
            });
            $("input[type=radio][name=type]").click(function(){
                if($(this).val() == "basic")
                {
                    $("#basic_firewall").show();
                    $("#advanced_firewall").hide();
                }
                else if($(this).val() == 'advanced')
                {
                    $("#basic_firewall").hide();
                    $("#advanced_firewall").show();
                }
                else
                {
                    $("#basic_firewall").hide();
                    $("#advanced_firewall").hide();
                }
            });

            jQuery('span.option_name').each(function(){
            	var _val = jQuery(this).text();

            	jQuery(this).text(_val.replace('_','/'));
            });
        });
    {/literal}
</script>

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

<form action="" method="post">
    <input type="hidden" name="firewall" value="1" />
    <div style="overflow: hidden">
        <h3>Firewall Settings</h3>
        <ul>
            <li style="width: 33%; float: left; list-style-type: none"><input type="radio" name="type" value="none" {if $type == 'none'} checked="checked" {/if} />Disable Firewall</li>
            <li style="width: 33%; float: left; list-style-type: none"><input type="radio" name="type" value="basic" {if $type == 'basic'} checked="checked" {/if} />Enable Basic Firewal</li>
            <li style="width: 33%; float: left; list-style-type: none"><input type="radio" name="type" value="advanced" {if $type == 'advanced'} checked="checked" {/if} />Enable Advanced Firewall</li>
        </ul>
    </div>

    <div id="basic_firewall" {if $type != 'basic'} style="display: none; margni-top: 10px"{else} style="margni-top: 10px" {/if}>
        <h3>Basic Configuration</h3>
        <ul style="overflow: hidden">
            {foreach from=$options item=option}
                <li style="width: 33%; float: left; list-style-type: none"><input style="margin-right: 3px" type="checkbox" name="basic_opt[{$option}]" value="1" {if $rules[$option]}checked="checked"{/if} /><span class="option_name">{$option}</span></li>
            {/foreach}
        </ul>
    </div>

    <div id="advanced_firewall" {if $type != 'advanced'} style="display: none; margni-top: 10px"{else} style="margni-top: 10px"{/if}>
        <h3>Advanced Configuration</h3>
        <table class="table table-framed table table-condensed" style="width: 100%" id="table_rules">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>Source IP</th>
                    <th>Destination IP</th>
                    <th>Port</th>
                    <th>Protocol</th>
                    <th>Action</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$advanced_rules item=option key=k}
                    <tr>
                        <td><input type="text" name="advanced[{$k}][label]" value="{$option.label}" style="width: 100px"/></td>
                        <td><input type="text" name="advanced[{$k}][source_ip]" value="{$option.source_ip}"/ style="width: 105px"></td>
                        <td><input type="text" name="advanced[{$k}][destination_ip]" value="{$option.destination_ip}" style="width: 105px"/></td>
                        <td><input type="text" name="advanced[{$k}][destination_port]" value="{$option.destination_port}" style="width: 50px"/></td>
                        <td>
                            <select name="advanced[{$k}][protocol]">
                                <option {if $option.protocol == 'any'}selected="selected"{/if} value="any">Any</option>
                                <option {if $option.protocol == 'tcp'}selected="selected"{/if} value="tcp">TCP</option>
                                <option {if $option.protocol == 'udp'}selected="selected"{/if} value="udp">UDP</option>
                                <option {if $option.protocol == 'udp'}selected="selected"{/if} value="icmp">ICMP</option>
                            </select>
                        </td>
                        <td>
                            <select name="advanced[{$k}][action]">
                                <option {if $option.action == 'allow'}selected="selected"{/if} value="allow">Allow</option>
                                <option {if $option.action == 'deny'}selected="selected"{/if} value="deny">Deny</option>
                            </select>
                        </td>
                        <td><a href="#" class="remove btn">Remove</a></td>
                    </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7"><a href="#" id="add_rule" class="btn">Add Rule</a></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div style="margin-top: 10px">
        <input class="btn" type="submit" value="Save" />
    </div>
</form>