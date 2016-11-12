// JavaScript Document

$(document).on("mouseover","#usersPermissions li, #usersPermissions table tr",function(){

    if ($(this).children("ul").length == 0) {
        $(this).css({"background-color": "#858585"});
    }

}).on("mouseout","#usersPermissions li, #usersPermissions table tr",function(){

    if ($(this).children("ul").length == 0) {
        $(this).css({"background-color": "#fff"});
    }

});

$(".userPermissionsCategoryKK").on("click",function(){

    var next = $(this).closest("li").next("li");

    next.find(">table").find("input[type=checkbox]").each(function(){

        if($(this).is(':disabled')){
            $(this).prop("disabled", false);
            $(this).prop("checked",true);
        } else {
            $(this).prop("disabled", true);
            $(this).prop("checked",false);
        }

    });

    next.find("ul").find(".userPermissionsCategoryKK").each(function(){

        if($(this).is(':disabled')){
            $(this).prop("disabled", false);
        } else {
            $(this).prop("disabled", true);
            $(this).prop("checked",false);

            $(this).closest("li").next("li").find("input[type=checkbox]").each(function(){

                if(!$(this).is(':disabled')){
                    $(this).prop("disabled", true);
                    $(this).prop("checked",false);
                }

            });

        }

    });
    
});

$(document).on("click",".userPermissionsEnableC",function(){

    $(this).closest("li").next("li").find("ul").find("input[type=checkbox]").each(function(){

        $(this).prop('checked', true);

        if($(this).attr("class") == "userPermissionsEnableC"){
            $(this).attr("class","userPermissionsDisableC");
        } else {

            if($(this).attr("class") == "userPermissionsDisableC"){
                $(this).attr("class","userPermissionsEnableC");
            }

        }

    });

    $(this).attr("class","userPermissionsDisableC");

});


$(document).on("click",".userPermissionsDisableC",function(){
    $(this).closest("li").next("li").find("ul").find("input[type=checkbox]").each(function(){
        $(this).prop('checked', false);
    });

    $(this).attr("class","userPermissionsEnableC");
});

$(document).on("click",".userPermissionsRollDown",function(){

    var area = $(this).closest("li");
    var img = area.find("img .userPermissionsRollDownImg");

    if(img.first().is(":visible")){
        img.first().hide();
    } else {
        img.first().show();
    }

    if(img.next().is(":visible")){
        img.next().hide();
    } else {
        img.next().show();
    }


    area.next("li").find("table").toggle();

});

$("#usersPermitionsFormButton").click(function(e){

    sendData({

        form: $(this).closest("form"),
        method: "POST",
        progress: "window",
        event: e

    },function(data){

        if(data != false){

            mainWindow.alert({
                content: data
            });

        }

    });

});
