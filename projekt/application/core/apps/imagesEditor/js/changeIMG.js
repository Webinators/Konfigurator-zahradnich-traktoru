$(document).on("click","#MainIMGEditOption1",function(e) {

    e.preventDefault();

    var container = mainContentEditor.moveToContainer($(this));
    var button = $(this);

    mainWindow.normal({

        width: "auto",
        content: "",
        buttonPointer: button,
        position: "absolute",
        close: true,
        dialog: false,
        bar: {
            title: 'Nahrání nového souboru',
            help: 'dsfdsfsffds'
        },
        event: e

    }, function (window) {

        var progess = mainProgressEvent.insertInto(mainWindow.moveToBody(window), true);

        FileUploaderBasic.insertNewUploader("image", 1, "3MB", "jpg,png,gif,jpeg", function (input) {

            mainProgressEvent.remove(progess);

            var data = '<form id="mainChangeImgForm" method="post" action="' + button.attr("href") + '" enctype="multipart/form-data"><br />' +
                '' + input + '<br /><input type="submit" id="mainChangeImgFormBtn"/></form>';

           mainWindow.update(window, data);

            container.find("input").each(function () {
                $("#mainChangeImgForm").append($(this).clone());
            });

        });

    });

});

$(document).on("click","#mainChangeImgFormBtn",function(e){

    var btn = $("#mainChangeImgFormBtn");

    sendData({

        form: $("#mainChangeImgForm"),
        method: "POST",
        progress: "window",
        event: e

    },function(data){

        if(data != false){

            var win = mainWindow.moveToContainer(btn);
            var el = mainWindow.getButtonPointer(win);

                mainWindow.remove(win);
                var contEdit = mainContentEditor.moveToBody(el);

                data = data.split(",");
                var paths = "";

                for (var i = 0; i < data.length; i++){

                    var parts = data[i].split("?");
                    paths += ((paths != '') ? ","+parts[0] : parts[0]);

                    var newN = parts[0].split("/");

                    var img = contEdit.find("img").attr("src");
                    var imgp = img.split("/");
                    imgp[imgp.length-1] = newN[newN.length-1];

                    img = imgp.join("/")+"?"+parts[1];

                    contEdit.find("a").attr("href", img);
                    contEdit.find("img").attr("src", img);

                }

                contEdit.find("input[name=path]").val(paths);

        }

    });

});

$(document).on("click","#MainIMGEditOption2",function(e){

    e.preventDefault();
    var button = $(this);

    var editB = mainContentEditor.moveToContainer(button);
    var URL = editB.find("input").eq(3).val();

    mainWindow.normal({
        center: true,
        bar: false,
        width: "full",
        height: "full",
        close: true,
        bar: {
            title: 'Oříznutí obrázku'
        },
        content: '<iframe class="frame" height="100%" width="100%" frameborder="0" scrolling="no" src="'+mainVariables.pathtofiles+'apps/cropper/index.php?destination='+URL+'" allowTransparency="true"></iframe>',
        buttonPointer: button,
        event: e,
        returnPointer: false

    },function(){

    });

});

$(document).on("click","#MainIMGEditOption3",function(e){

    e.preventDefault();
    var button = $(this);
    var editB = mainContentEditor.moveToContainer(button);
    var URL = editB.find("input").eq(3).val();

    var src = mainContentEditor.moveToBody(editB).find("img").attr("src");
    src = src.split("?");
    src = src[0]+'?time='+guid();

    mainWindow.confirm(button,"Opravdu chcete otočit obrázek doleva?",e,function(result){

        if(result) {

            sendData({

                data: {path: URL,degrees: "90"},
                url: button.attr("href"),
                method: "POST"

            },function(data) {

                if (data != false) {

                    mainContentEditor.moveToBody(editB).find("img").attr("src",src);

                }

            });
        }
    });
});

$(document).on("click","#MainIMGEditOption4",function(e){

    e.preventDefault();
    var button = $(this);
    var editB = mainContentEditor.moveToContainer(button);
    var URL = editB.find("input").eq(3).val();

    var src = mainContentEditor.moveToBody(editB).find("img").attr("src");
    src = src.split("?");
    src = src[0]+'?time='+guid();

    mainWindow.confirm(button,"Opravdu chcete otočit obrázek doprava?",e,function(result){

        if(result) {

            sendData({

                data: {path: URL,degrees: "-90"},
                url: button.attr("href"),
                method: "POST"

            },function(data) {

                if (data != false) {
                    mainContentEditor.moveToBody(editB).find("img").attr("src",src);
                }

            });
        }
    });
});

$(document).on("click","#MainIMGEditOption5",function(e){

    e.preventDefault();
    var button = $(this);
    var editB = mainContentEditor.moveToContainer(button);
    var URL = editB.find("input[name=path]").val();

    mainWindow.confirm(button,"Opravdu chcete smazat obrázek?",e,function(result){

        if(result) {

            sendData({

                data: {path: URL},
                url: button.attr("href"),
                method: "POST"

            },function(data) {

                if (data != false) {

                    mainWindow.alert({

                        content: {icon: "success", data: 'Obrázek úspěšně smazán'},
                        event: e,
			type: "message"

                    }, function (data) {
                        editB.toggle(100).remove();
                    });

                }
            });
        }
    });
});