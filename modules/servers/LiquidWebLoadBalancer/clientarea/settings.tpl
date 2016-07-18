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

{literal}
    <script type="text/javascript">
        $(function(){
            {/literal}
            var max_nodes = {$max_nodes};
            var max_services = {$max_services};
            {literal}
            var del = 0;
            var max = 0;

            $(".remove").on("click", function(event){
                event.preventDefault();
                $(this).parents("tr").remove();

                if($("#table_service tbody tr").size() < max_services && max_services)
                {
                    $("#add_service").show();
                    $("#add_service_error").hide();
                }
            });

            $("#add_service").click(function(event){
                event.preventDefault();

                if(!max)
                {
                    last = $("#table_service tbody tr").size();
                    max = last;
                }
                else
                    max++;

                $("#table_service tbody").append('<tr><td><input type="text" name="services['+max+'][src_port]" value=""/></td><td><input type="text" name="services['+max+'][dest_port]" value=""/></td><td><a href="#" class="remove btn-default btn">Remove</a></td></tr>');
                if($("#table_service tbody tr").size() >= max_services && max_services)
                {
                    $("#add_service").hide();
                    $("#add_service_error").show();
                }

                $(".remove").on("click", function(event){
                    event.preventDefault();
                    $(this).parents("tr").remove();

                    if($("#table_service tbody tr").size() < max_services && max_services)
                    {
                        $("#add_service").show();
                        $("#add_service_error").hide();
                    }
                });
            });

            /** NODES **/
            $( document ).ready(function() {
                $("a.remove_node").on("click", function(event){
                    event.preventDefault();

                    host = $(this).closest("tr").find(".node_domain").html();
                    ip = $(this).closest("tr").find("input").val();

                    $("#new_node").append("<option value='"+ip+"'>"+host+" - "+ip+"</option>");
                    $(this).closest("tr").remove();

                    if(max_nodes && $("#table_node tbody tr").size() < max_nodes)
                    {
                        $("#new_nodes").show();
                        $("#new_nodes_error").hide();
                    }
                });
            });

            $("#add_new_node").click(function(){
                val = $("#new_node").val();
                if(!val || val == 0)
                {
                    return;
                }

                host = $("#new_node option:selected").text();
                host = host.replace(" - "+val, "");

                t_max = $("#table_node tbody tr").size();
                $("#table_node tbody").append("<tr><td class='node_domain'>"+host+"</td><td><input type='hidden' name='nodes["+t_max+"]' value='"+val+"' />"+val+"</td><td><a href='#' class='btn btn-default remove_node'>Remove</a></td></tr>");

                $("#new_node option:selected").remove();

                if(max_nodes && $("#table_node tbody tr").size() >= max_nodes)
                {
                    $("#new_nodes").hide();
                    $("#new_nodes_error").show();
                }

                $("a.remove_node").on("click", function(event){
                    event.preventDefault();

                    host = $(this).closest("tr").find(".node_domain").html();
                    ip = $(this).closest("tr").find("input").val();

                    $("#new_node").append("<option value='"+ip+"'>"+host+" - "+ip+"</option>");
                    $(this).closest("tr").remove();

                    if(max_nodes && $("#table_node tbody tr").size() < max_nodes)
                    {
                        $("#new_nodes").show();
                        $("#new_nodes_error").hide();
                    }
                });
            });

            $("#ssl_termination").click(function(){
                if($(this).is(":checked"))
                {
                    $("#ssl_termination_enabled").show();
                }
                else
                {
                    $("#ssl_termination_enabled").hide();
                }
            });

            $("#ssl_includes").click(function(){
                if($(this).is(":checked"))
                {
                    $("#ssl_includes_enabled").show();
                }
                else
                {
                    $("#ssl_includes_enabled").hide();
                }
            });
        });
    </script>
{/literal}

<form action="" method="post">
    <input type="hidden" name="modaction" value="save" />
    <h4>Main Settings</h4>
    <table class="table table-framed table table-condensed" style="width: 100%">
        <tr>
            <td style="width: 50%">VIP</td>
            <td>{$details.vip}</td>
        </tr>
        <tr>
            <td>Strategy</td>
            <td>
                <select name="strategy">
                    {foreach from=$strategies item=i}
                        <option value="{$i.strategy}" {if $details.strategy == $i.strategy} selected="selected" {/if}>{$i.name}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td>
                Session Persistence
            </td>
            <td>
                <input type="checkbox" name="session_persistence" {if $details.session_persistence} checked="ckecked"{/if}/>
            </td>
        </tr>
    </table>

    <h4>Services</h4>
    <table id="table_service" class="table table-framed table table-condensed" style="width: 100%">
        <thead>
            <tr>
                <td>Source Port</td>
                <td>Destination Port</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            {foreach from=$details.services item=i key=k}
                <tr>
                    <td><input type="text" name="services[{$k}][src_port]" value="{$i.src_port}" /></td>
                    <td><input type="text" name="services[{$k}][dest_port]" value="{$i.dest_port}" /></td>
                    <td><a href="#" class="btn btn-default remove">Remove</a></td>
                </tr>
            {/foreach}
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">
                    <a id="add_service" class="btn" href="#" style="{if $max_services && $max_services <= count($details.services)} display: none{/if}">Add Service</a>
                    <small id="add_service_error" style="color: red; {if $max_services && $max_services > count($details.services)} display: none{/if}">Services limit reached</small>
                </td>
            </tr>
        </tfoot>
    </table>

    <h4>Nodes</h4>
    <table id="table_node" class="table table-framed table table-condensed" style="width: 100%">
        <thead>
            <tr>
                <td>Domain</td>
                <td>IP</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            {foreach from=$details.nodes item=i key=k}
                <tr>
                    <td class="node_domain">{$i.domain}</td>
                    <td><input type="hidden" name="nodes[{$k}]" value="{$i.ip}" />{$i.ip}</td>
                    <td><a href="#" class="btn btn-default remove_node">Remove</a></td>
                </tr>
            {/foreach}
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">
                    <div id="new_nodes" style="{if $max_nodes && $max_nodes  <= count($details.nodes)} display: none{/if}">
                        <select name="new_node" id="new_node">
                            <option value="0">-- CHOOSE NODE --</option>
                            {if $nodes}

                                    {foreach from=$nodes.items item=i}
                                        <option value="{$i.ip}">{$i.domain} - {$i.ip}</option>
                                    {/foreach}
                            {/if}
                        </select>
                        <input type="button" style="margin-bottom:9px;" id="add_new_node" value="Add" class="btn"/>
                    </div>
                    <small style="color: red; {if $max_nodes && $max_nodes > count($details.nodes)} display: none{/if}" id="new_nodes_error">Nodes limit reached</small>
                </td>
             </tr>
        </tfoot>
    </table>

{if $ssl}
    <h4>SSL Termination</h4>
    <p>
        <input {if $details.ssl_termination == 1} checked="checked" {/if} type="checkbox" id="ssl_termination" name="ssl_termination" value="1" style="margin-right: 4px"/>Enable SSL Termination
    </p>
    <div id="ssl_termination_enabled" style="{if $details.ssl_termination != 1}display: none{/if}">
        <p style="margin-top: 5px">SSL Certificate</p>
        <textarea name="ssl_certificate" style="width: 100%; height: 100px">{if $details.ssl_termination == 1}SSL Certficate Hidden{/if}</textarea>

        <p style="margin-top: 5px">Private Key</p>
        <textarea name="ssl_private_key" style="width: 100%; height: 100px">{if $details.ssl_termination == 1}Private Key Hidden{/if}</textarea>

        <p style="margin-top: 5px">
            <input {if $details.ssl_includes == 1} checked="checked" {/if} type="checkbox" id="ssl_includes" name="ssl_includes" value="1" style="margin-right: 4px"/>Include Intermediate Certificate
        </p>
        <div id="ssl_includes_enabled" style="{if $details.ssl_includes != 1} display: none; {/if}">
            <textarea name="ssl_int" style="width: 100%; height: 100px">{if $details.ssl_includes == 1}Intermediate Certificate Hidden{/if}</textarea>
        </div>
    </div>
{/if}

    <div style="margin-top: 10px">
        <input type="submit" value="Save" class="btn"/>
    </div>
</form>