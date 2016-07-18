
{if isset($smarty.get.error) && $smarty.get.error neq 's1filled'}
  <div class="errorbox" style="
    background-repeat: no-repeat !important;
    background-position: 5px !important;
    margin: 10px 5px 10px 5px !important;
    padding: 6px 5px 6px 45px !important;
    min-height: 28px !important;
    background-color: #F2D4CE !important;
    border: 1px solid #AE432E !important;
    color: #cc0000 !important;
  ">
    Request errors occured. Please contact with support.
  </div>
  {elseif $smarty.get.error eq 's1filled' || $step2 eq 'open'}
    <div class="errorbox" style="
    background-repeat: no-repeat !important;
    background-position: 5px !important;
    margin: 10px 5px 10px 5px !important;
    padding: 6px 5px 6px 45px !important;
    min-height: 28px !important;
    background-color: #F2D4CE !important;
    border: 1px solid #AE432E !important;
    color: #cc0000 !important;margin-top:5px;
    ">You have filled data</div>
    <div style="margin-bottom:10px;margin: 10px 5px 10px 5px !important;
    padding: 6px 5px 6px 45px !important;
    min-height: 28px !important">
      Verification method : {$verification_method}<br>
      First Name: {$firstname}<br>
    {if $metatag_approved_domain neq ''}
      Metatag Url: {$metatag_approved_domain}<br>
    {/if}
      Last Name: {$lastname}<br>
      Organization Name: {$orgname}<br>
      Job Title: {$jobtitle}<br>
      Email Address: {$email}<br>
      Address 1: {$address1}<br>
      Address 2: {$address2}<br>
      City: {$city}<br>
      State/Region: {$state}<br>
      Zip Code: {$postcode}<br>
      Country: {$country}<br>
      Phone Number:{$phonenumber}<br>
    </div>
    <form action="{$urlstep2}" method="POST" style="margin-bottom:10px;margin: 10px 5px 10px 5px !important;
    padding: 6px 5px 6px 45px !important;
    min-height: 28px !important">
      <input type="hidden" name="servertype" value="{$servertype}">
      <input type="hidden" name="csr" value="{$csr}">
      <input type="hidden" name="fields[verification_method]" value="{$verification_method}">
      <input type="hidden" name="fields[metatag_approved_domain]" value="{$metatag_approved_domain}">
      <input type="hidden" name="firstname" value="{$firstname}">
      <input type="hidden" name="lastname" value="{$lastname}">
      <input type="hidden" name="orgname" value="{$orgname}">
      <input type="hidden" name="jobtitle" value="{$jobtitle}">
      <input type="hidden" name="email" value="{$email}">
      <input type="hidden" name="address1" value="{$address1}">
      <input type="hidden" name="address2" value="{$address2}">
      <input type="hidden" name="city" value="{$city}">
      <input type="hidden" name="state" value="{$state}">
      <input type="hidden" name="postcode" value="{$postcode}">
      <input type="hidden" name="country" value="{$country}">
      <input type="hidden" name="phonenumber" value="{$phonenumber}">
 
      <button class="btn" >Continue Order</button>
    </form>   
{/if}

  {if $smarty.get.error eq 'orderfiled' || $step3 eq 'open'}  
    <div style="margin-bottom:10px;margin: 10px 5px 10px 5px !important;
      padding: 6px 5px 6px 45px !important;
      min-height: 28px !important">
        Verification method : {$verification_method}<br>
        First Name: {$firstname}<br>
      {if $metatag_approved_domain neq ''}
        Metatag Url: {$metatag_approved_domain}<br>
      {/if}
        Last Name: {$lastname}<br>
        Organization Name: {$orgname}<br>
        Job Title: {$jobtitle}<br>
        Email Address: {$email}<br>
        Address 1: {$address1}<br>
        Address 2: {$address2}<br>
        City: {$city}<br>
        State/Region: {$state}<br>
        Zip Code: {$postcode}<br>
        Country: {$country}<br>
        Phone Number:{$phonenumber}<br>
        Certificate Approver Email: {$approveremail}
      </div>
      {if $retry eq 'show'}
        <form action="{$urlstep3}" method="POST" style="margin-bottom:10px;margin: 10px 5px 10px 5px !important;
          padding: 6px 5px 6px 45px !important;
          min-height: 28px !important">
          <input type="hidden" name="approveremail" value="{$approveremail}">
          <button class="btn" >ReTry</button>
        </form> 
      {/if}
  {/if}


<div class="op_info">
  {if $verification_method eq 'dns' && $notordered eq 0}

    Verifying by DNS Record is the simplest of the verification methods.<br>
    It requires adding a specific TXT record to the DNS zone for the domain identified in the Certificate Signing Request (CSR); access to change DNS entries is required.<br><br>
    TXT record that you must add to your domain  to process verify:<b style="color:red;">{$domaintxt}</b><br> <br>

    SSL Status:{$status}
     
  {elseif $verification_method eq 'metatag' && $notordered eq 0}

    Verifying by HTML Meta Tag requires adding a specific HTML Meta Tag to the index page for the domain identified in the Certificate Signing Request (CSR); access to edit HTML is required.<br><br>

    The meta that tag you must place in meta to process verify: <b style="color:red;">{$metatag|escape}</b><br> <br>
    SSL Status:{$status}  

  {elseif $verification_method eq 'email' && $notordered eq 0}

    Verifying by Email is the slowest of the available verification methods.<br>
    An email will be sent to the either the Administrator address for the domain identified in the Certificate Signing Request (CSR),<br>
    or a generic address on a pre-selected list.<br> <br>
    You must click on the link in that email and follow the included instructions to verify by email.<br> <br>
    SSL Status:{$status}  

  {/if}
</div>
  
{if $notordered eq 0 && $verification_method neq 'email' && $status neq 'Verified'}
  <form action="{$veryfyurl}" method="POST" style="margin-bottom:10px;margin: 10px 5px 10px 5px !important;
    padding: 6px 5px 6px 45px !important;
    min-height: 28px !important">
    <input type="hidden" name="approveremail" value="{$approveremail}">
    <button class="btn" >Verify</button>
  </form>
{else}
    {if $verification_method neq 'email' && $step1 eq 'open'}You should have an email with details to configure your SSL Product.{/if}
{/if}