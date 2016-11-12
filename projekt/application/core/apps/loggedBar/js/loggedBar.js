var specialProfilPointer;

$(document).ready(function(){

    var url = new URL();

    if(url.getUrlParameter("profil")){
        $("#showLoggedUserProfil").click();
    }

});


$(document).on("click","#showLoggedUserProfil",function(e){

    if(e.target.nodeName != "INPUT") {

    	 var url = new URL();

        if (isObject(specialProfilPointer)) {
            mainWindow.remove(specialProfilPointer);
        }

        var id = "";
        var button = $(this);

        if (isDefined($(this).attr("data-user-id"))) {
            id = $(this).attr("data-user-id");
        }

        if(url.getUrlParameter("userId")){
            id = mainUrl.getUrlParameter("userId")
        }

        sendData({

            data: {showprofil: "true",id: id},
            url: "" + mainVariables.pathtofiles + "apps/userProfil/load/showuserprofil.php",
            progress: "window",
            method: "POST"

        }, function (data) {

            if(data != false) {

                specialProfilPointer = mainWindow.normal({

                    width: "full",
                    bar: false,
                    content: data,
                    buttonPointer: button,
                    center: true,
                    position: "absolute",
                    event: e,
                    returnPointer: true,
                    afterClose: function () {
                        url.removeParameter("profil");
                        url.removeParameter("userId");
                        url.useThisUrl("",true);
                    }
                });

                url.addParameter("profil", "true");
                url.addParameter("userId", id);
              
                url.useThisUrl("",true);

            }
            
        });
    }
});

$(document).on("keyup", "#LoggedBarSearcherUsers" , function(){

    var text = $(this).val();

    if(text != ''){

        $("#LoggedBarsearchedUsers").show();

        sendData({

            data: {text: text},
            url: ""+mainVariables.pathtofiles+"load/users/searchregistereduser.php",
            method: "POST",
            progress: $("#LoggedBarsearchedUsersData")

        },function(data){
            $("#LoggedBarsearchedUsersData").html(data);
        });

        $(document).click(function(event) {
            if(!$(event.target).closest('#LoggedBarSearcherUsers').length) {
                if(!$(event.target).closest('#LoggedBarsearchedUsers').length) {
                    $("#LoggedBarsearchedUsersData").html(''); $("#LoggedBarsearchedUsers").hide();
                }}
        });

    } else {$("#LoggedBarsearchedUsers").hide();}
});

$(document).on("focus", "#LoggedBarSearcherUsers" , function(){
    $(this).keyup();
});

$(document).on("mouseover", ".LoggedBarsSearchedUserBox", function(){

    $(this).attr("class","LoggedBarsSearchedUserBoxOnmouseOver");
    $(this).find(".LoggedBarsSearchedUserImgBackground").attr("class","LoggedBarsSearchedUserImgBackgroundOnmouseOver")
});

$(document).on("mouseout", ".LoggedBarsSearchedUserBoxOnmouseOver", function(){

    $(this).attr("class","LoggedBarsSearchedUserBox");
    $(this).find(".LoggedBarsSearchedUserImgBackgroundOnmouseOver").attr("class","LoggedBarsSearchedUserImgBackground")
});

function logoutUser()
{

    sendData({

        data: {logout: "true"},
        url: ""+mainVariables.pathtofiles+"load/users/logout.php",
        method: "GET"

    },function(){
        location.reload();
    });

}

$(document).on("click",".LoggedBarShowUserLoginForm",function(e){

    var button = $(this);

    mainWindow.normal({

        fixed: true,
        width: "auto",
        height: "auto",
        title: "Login form",
        content: "",
        buttonPointer: button,
        event: e,
        dialog: false,
        callback: true

    },function(OBJ){

        mainProgressEvent.insertInto(mainWindow.moveToBody(OBJ),false,true);

        sendData({

            data: {show:"true"},
            url: ""+mainVariables.pathtofiles+"apps/userLogin/load/showLoginForm.php",
            method: "POST"

        },function(data){
            mainWindow.update(OBJ,data);
        });

    });

});

$(document).on("click","#showLoggedUser",function(e){

    var button = $(this);

    var progress = mainProgressEvent.insert();

    sendData({

        data: {show:"true"},
        url: ""+mainVariables.pathtofiles+"load/admin/showUsers.php",
        method: "POST",
        progress: "window"

    },function(data){

        mainProgressEvent.remove(progress);

        mainWindow.normal({

            width: "auto",
            height: "auto",
            bar: false,
            center: true,
            content: data,
            buttonPointer: button,
            event: e,
            returnPointer: false

        });

    });

});