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

<div id="modcmdworking" style="display:none;text-align:center;"><img src="admin/images/loader.gif" /> &nbsp; Working...</div>
  <div id="op_action">
  <div class="alert alert-warning">Are you sure you want to start this machine?</div>

  <form action="" method="post" style="margin-top: 10px">
      <input type="hidden" name="modaction" value="start" />
      <input type="submit" id="op_submit" value="Yes, start machine" class="btn btn-success" style="float: left; margin-left: 250px"/>
      <input type="button" class="btn btn-danger" onclick="document.location='clientarea.php?action=productdetails&id={$params.serviceid}'" style="margin-left: 20px" value="Cancel" />
  </form>

</div>

{literal}
<script>
  $('#op_submit').click(function(){
    $("#op_action").css("filter","alpha(opacity=20)");
    $("#op_action").css("-moz-opacity","0.2");
    $("#op_action").css("-khtml-opacity","0.2");
    $("#op_action").css("opacity","0.2");

    $("#modcmdworking").css("display","block");
    $("#modcmdworking").css("padding","9px 50px 0");
    $("#modcmdworking").fadeIn();
  });
</script>
{/literal}