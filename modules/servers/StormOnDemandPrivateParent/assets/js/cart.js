
(function(){
  $(document).ready(function(){
    var hostings         = '.json_encode($hostingList).';
    var customFieldsIDs  = '.json_encode($custFieldsIDs).'; 
    
    $(".prodconfigcol1 h3").each(function(){
      console.log("sadasd");
      if($(this).html() == "Additional Required Information"){
        $(this).html("Additional Information");
      }
    });
    
    for(var cI = 0; cI < customFieldsIDs.length; cI++){
      
      var slc = $("select[name=\"customfield["+customFieldsIDs[cI]+"]\"");
      
      for(var iH = 0; iH < hostings.length; iH++){
        slc.append("<option value=\""+hostings[iH]["id"]+"\">"+hostings[iH]["name"]+"</option>");
      }
    }
    
    $(".checkout").attr("onclick","");
    
    $(".checkout").click(function(event){
      var hostname = $("input[name=\"hostname\"]").val();
      var sbs = false;
      if(typeof hostname === "undefined"){
        hostname = $("#owndomainsld").val()+"."+$("#owndomaintld").val();
        sbs = true;
      }
      console.log(hostname);
      
      
      $(".op_error1").remove();
      $(".op_error2").remove();
      var html = "<div class=\"op_error1\" style=\"margin: 10px auto 10px auto;"+
                    "display:none;"+
                    "padding: 10px 15px;"+
                    "background-color: #FBEEEB;"+
                    "border: 1px dashed #cc0000;"+
                    "width: 90%;"+
                    "font-weight: bold;"+
                    "color: #cc0000;"+
                    "text-align: center;"+
                    "-moz-border-radius: 6px;"+
                    "-webkit-border-radius: 6px;"+
                    "-o-border-radius: 6px;"+
                    "border-radius: 6px;\">"+
          "</div>";
          
      var html2 = "<div class=\"op_error2\" style=\"margin: 10px auto 10px auto;"+
                                "display:none;"+
                                "padding: 10px 15px;"+
                                "background-color: #FBEEEB;"+
                                "border: 1px dashed #cc0000;"+
                                "width: 90%;"+
                                "font-weight: bold;"+
                                "color: #cc0000;"+
                                "text-align: center;"+
                                "-moz-border-radius: 6px;"+
                                "-webkit-border-radius: 6px;"+
                                "-o-border-radius: 6px;"+
                                "border-radius: 6px;\">"+
                      "</div>";
      
      
      var hostexpr = /^[a-z0-9-\.]+\.[a-z]{2,4}/;

       if( hostexpr.test(hostname) === false ){
         $("#configproducterror").parent().prepend(html);
         var li_host = "<li>You must enter a valid domain name.</li>";
         $(".op_error1").html(li_host);
         $(".op_error1").show();
       }; 
       var password = $("input[name=\"rootpw\"]").val();
       
       var containsBigLetter  = /[A-Z]/;
       var containsSmallLetter  = /[a-z]/;
       var containsDigit   = /\d/;

       if(sbs == false){
         if(containsBigLetter.test(password) === false || containsSmallLetter.test(password) === false || containsDigit.test(password) === false ){
           $("#configproducterror").parent().prepend(html2);
           var li = "<li>You must enter a valid password. Password must contain: one big, one small and one dig .</li>";
           $(".op_error2").last().html(li);
           $(".op_error2").show();
         }
       }
       
       if( (containsBigLetter.test(password) === true && containsSmallLetter.test(password) === true && containsDigit.test(password) === true) && hostexpr.test(hostname)  === true && sbs == false){
        $(".checkout").attr("onclick","addtocart()");
        addtocart();
       }else if(sbs == true && hostexpr.test(hostname) !== false){
        $(".checkout").attr("onclick","addtocart()");
        addtocart();
       }
      
    });

    
    
  });	
})();
