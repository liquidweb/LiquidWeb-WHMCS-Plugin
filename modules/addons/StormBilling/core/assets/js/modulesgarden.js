function reloadScripts()
{
    jQuery('.tooltip-box').tooltip({
      selector: "a[rel=tooltip]",
      delay: { show: 0, hide: 0 } 
    });
}

jQuery(function()
{
    jQuery('section [href^=#]').click(function (e) {
      e.preventDefault();
    });
    
    jQuery(document).delegate(".btn-delete", "click", (function(event)
    {
        return confirm("Are you sure you want to proceed?");
    }));
    
    jQuery('.tooltip-box').tooltip({
      selector: "a[rel=tooltip]",
      delay: { show: 0, hide: 0 } 
    });
     
    //We don't want jQuery buttons!
    var options = {
        buttons: {}
    };
    jQuery('.body button').button("destroy");
    
});

/****************************
 *          PAGINATION
 *****************************/

/**
 * Get paginaton and insert it!
 **/
function getPagination(parent)
{
    $.get(document.location, "ajax=1&pagination=1&get=1&parent="+parent, function(data){
        form = $("form.pagination input[name$=\"parent\"][value$=\""+parent+"\"]").parents("div.pagination");
        $(form).html(data);
        $("div.pagination a").click(onClickPagination);
    });
}

function resetPagination(parent)
{
    
}

function onClickPagination(event)
{
    event.preventDefault();
    if($(this).parent().hasClass('disabled'))
    {
        return;
    }

    href = $(this).attr("href");
    href = href.substring(1);
    vars = href.split('&');
    parent = vars[1].split('=');
    parent = parent[1];
    pagination = $(this).parents().find('div.pagination');
    
    amount = $("#"+parent+" thead tr th").size();
    $("#"+parent+" tbody").html("<tr><td colspan="+amount+" style='text-align: center'><img src='images/loader.gif' /></td></tr>");
    
    $.get(document.location, "ajax=1&pagination=1&"+href, function(data)
    {
        $("#"+parent+" tbody").html(data);
        getPagination(parent);
        reloadScripts();
    }); 
}




/****************************
 *          FILTERING
 ***************************/
function enableFiltering()
{
    form = $(this).parents('form.filtering');
    parent = $(form).children("input[name$=\"parent\"]").val();
    
    $(this).addClass("hide");
    $(form).children(".disable-filtering").removeClass("hide");
    
    $("form.filtering-options input[name$=\"parent\"][value$=\""+parent+"\"]").parents('.filtering-options').parent().parent().removeClass("hide");
}

function disableFiltering()
{
    form = $(this).parents('form.filtering');
    parent = $(form).children("input[name$=\"parent\"]").val()
    
    $(this).parent().children(".enable-filtering").removeClass("hide");
    $(this).addClass("hide");
    $(this).parent().parent().next().addClass("hide");
    
    resetFiltering(parent);
    getPagination(parent)
}


/**
 * Disable filtering and get new content!
 **/
function resetFiltering(parent)
{
   $.get(document.location, "ajax=1&pagination=1&reset=1&parent="+parent, function(data){
        $("#"+parent).html(data);
    }); 
}

function addKeyPressEvent()
{
    var timeout_id = 0;
    
    parent = $(this).parents(".filtering-options");
    parent_name = $(parent).find('input[name$="parent"]').val();
    clearTimeout(timeout_id);
    timeout_id = setTimeout(function(){
        $.get(document.location, "ajax=1&pagination=1&"+$(parent).serialize(), function(data){
            $("#"+parent_name+" tbody").html(data);
            getPagination(parent_name)
            addPagination();
        });
    }, 500);
}

function enableOrderBy(event)
{
    parent = $(this).parents('table.pagination').attr('id');
    event.preventDefault();
    href = $(this).attr("href");
    order_by = href.substring(1);

    $.get(document.location, "ajax=1&pagination=1&order_by="+order_by+"&parent="+parent, function(data)
    {
        $("#"+parent+" tbody").html(data);
    });
}

$(function()
{
    //FIND PAGINATION AND ADD EVENTS
    $("div.pagination a").click(onClickPagination);
    
    //BIND KEY PRESS EVENT FOR FILTERING
    $(".filtering-options input").keypress(addKeyPressEvent);
    
    //BIND TABLE HEADER
    $("table.pagination thead a").click(enableOrderBy);
    
    
    /*$("table.pagination").each(function(){
        parent = $(this).attr("id");
        $.get(document.location, "ajax=1&pagination=1&parent="+parent+"&"+$(this).serialize(), function(data){
        $("#"+parent).find('.next').attr("disabled", "disabled");
        $("#"+parent).find('.prev').attr("disabled", "disabled");

        $("#"+parent).find('tbody').html(data);

        $.get(document.location, "ajax=1&pagination=1&parent="+parent+"&check=1", function(data){
            if(data.next)
            {
                $("#"+parent).find('.next').removeAttr("disabled");
            }
            else
            {
                $("#"+parent).find('.next').attr("disabled", "disabled");
            }

            if(data.prev)
            {
                $("#"+parent).find('.prev').removeAttr("disabled");
            }
            else
            {
                $("#"+parent).find('.prev').attr("disabled", "disabled");
            }
        },"json");
    })
        
    });*/
    

    
    /*$("form.pagination").submit(function(event){
        event.preventDefault();
        parent = $(this).parents('table.pagination').attr("id");
        
        $.get(document.location, "ajax=1&pagination=1&parent="+parent+"&"+$(this).serialize(), function(data){
            $("#"+parent).find('.next').attr("disabled", "disabled");
            $("#"+parent).find('.prev').attr("disabled", "disabled");
            
            $("#"+parent).find('tbody').html(data);
            
            $.get(document.location, "ajax=1&pagination=1&parent="+parent+"&check=1", function(data){
                if(data.next)
                {
                    $("#"+parent).find('.next').removeAttr("disabled");
                }
                else
                {
                    $("#"+parent).find('.next').attr("disabled", "disabled");
                }
                
                if(data.prev)
                {
                    $("#"+parent).find('.prev').removeAttr("disabled");
                }
                else
                {
                    $("#"+parent).find('.prev').attr("disabled", "disabled");
                }
            },"json");
        })
    });*/
    
    
});
