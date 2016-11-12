$(document).on("click","#adderuserProfilName",function(){

    $("#adderuserProfilName").attr("id","adderuserProfilNameActive");

    var data = $("#userProfilName").html();
    var dataParts = data.split(" ");
    var Username = dataParts[0];

    for(var i = 1; i < (dataParts.length - 1); i++)
    {
        Username += " "+dataParts[i];
    }

    var Secondname = dataParts[dataParts.length - 1];

    $("#userProfilName").html('<form id="FormProfilUserName" method="post" action="'+mainVariables.pathtofiles+'apps/userProfil/load/saveusername.php"><table><tr><td><input id="newProfilUsername" type="text" name="username" value="'+Username+'" required/></td></tr><tr><td><input id="newProfilSecnodname" type="text" name="usersecondname" value="'+Secondname+'" required/></td></tr><tr><td><center><input id="userProfilNameSubmit" type="submit" name="changeUsername" value="Uložit" /></center></td></tr><tr><td><div id="ProfilUserNameErr"></div></td></tr></table></form></div>');

    $(document).click(function(event) {
        if(!$(event.target).closest('#userProfilName').length) {
            $('#userProfilName').html(data);
            $("#adderuserProfilNameActive").attr("id","adderuserProfilName");
        }
    });

});

$(document).on("click","#userProfilNameSubmit",function(){

    sendData({

        form: $("#FormProfilUserName"),
        method: "POST",
        progress: "window",
        alert: false

    },function(data,err){

        $("#ProfilUserNameErr").show();

        if(data != false){
            $("#ProfilUserNameErr").html(err);
        } else {
            $("#ProfilUserNameErr").html(data);
        }

        setTimeout(function() {
            $("#ProfilUserNameErr").fadeOut("slow");
        }, 500);

    });
});

$(document).on("click","#userInsertNewUpload",function(){

    $("#userNewProfil").css('width','100%');
    $("#userNewProfilForm").show();

    $(document).click(function(event) {
        if(!$(event.target).closest('#userNewProfil').length) {
            $("#userNewProfil").css('width','');
            $("#userNewProfilForm").hide();
        }
    });

});

$(document).on("click","#userUplodProfilFormBtn",function(e){

    var btn = $(this);
    $("#userUplodProfilFormOutput").html('<img src="'+mainVariables.pathtoimages+'icons/progress/validation.gif" alt="validating" title="validating" width="23px" />');

    sendData({

        form: $("#userUplodProfilForm"),
        method: "POST",
        event: e

    }, function(data){

        if(data != false) {

            var arr = data.split("|");
            var index = parseInt(arr[0]);

            switch (index) {
                case 0:
                    location.reload();
                    break;
                case 1:

                    var windowH = $(window).height();
                    var newHeight = ($(window).height() * 75) / 100;

                    var percentage = newHeight / windowH;
                    var newWidth = $(window).width() * percentage;

                    mainWindow.normal({
                        center: true,
                        bar: false,
                        content: '<iframe class="frame" height="' + newHeight + '" width="' + newWidth + '" frameborder="0" scrolling="no" src="' + mainVariables.pathtofiles + 'apps/cropper/index.php?destination=' + arr[1] + '&height=' + (newHeight - 50) + '" allowTransparency="true"></iframe>',
                        buttonPointer: btn,
                        event: e,
                        returnPointer: false

                    });

                    break;
                case 2:
                    alert(data);
                    break;
            }
        }

    });

});

$(document).on("click","#imagesCropperBtn",function(){

    sendData({

        form: $("#imagesCropper"),
        method: "POST",
        progress: "window"

    },function(data){

        if(data != false) {
            location.reload();
        }
    });

});

$("#userChangeUserDetailsBtn").click(function(){

    sendData({

        form: $("#userChangeUserDetails"),
        method: "POST",
        progress: "window"

    },function(data){

        if(data != false) {
            mainWindow.alert({
                content: data
            });
        }

    });

});