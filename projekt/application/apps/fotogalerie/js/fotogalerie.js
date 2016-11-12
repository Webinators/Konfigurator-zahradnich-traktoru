/**
 * Created by Radim on 18.3.2016.
 */

$(document).on("click","#mainGalerieAddNewGallery", function(e){
    
   var btn = $(this); 
    
   sendData({
       
       form: btn.closest("form"),
       event: e,
       method: "POST",
       progress: "window"
       
   }, function(data){

       mainWindow.normal({

           center: true,
           bar: true,
           title: "Přidání nové galerie",
           content: data,
           buttonPointer: btn,
           event: e

       });
       
   });
    
});

$(document).on("click","#mainGalerieAddGallery",function(e){

    sendData({

        form: $(this).closest("form"),
        method: "POST",
        progress: "window",
        event: e

    }, function (data) {

        if(data != false){
            mainWindow.alert({
                content: "Galerie úspěšně přidána"
            }, function(){
                location.reload();
            });
        }

    });

});

$(document).on("click","#mainGalerieSaveGallery",function(e){

    sendData({

        form: $(this).closest("form"),
        method: "POST",
        progress: "window",
        event: e

    }, function (data) {

        if(data != false){
            mainWindow.alert({
                content: "Galerie úspěšně uložena"
            });
        }

    });

});

$(document).on("click","#mainGalerieRemoveGallery",function(e){

    e.preventDefault();

    var btn = $(this);

    mainWindow.confirm(btn,"Opravdu chcete odebrat galerii?",e,function(result){

        if(result){

            sendData({

                url: btn.attr("href"),
                data: {id_galerie: btn.attr("data-id-g")},
                method: "POST",
                progress: "window",
                event: e

            }, function (data) {

                if(data != false){

                    mainWindow.alert({
                        content: "Galerie úspěšně smazána"
                    },function(){
                        btn.closest("div").remove();
                    });

                }

            });
        }
    });

});


$(document).on("click", "#uploadMoreFiles", function (e) {

    sendData({

        form: $(this).closest("form"),
        method: "POST",
        progress: "window",
        event: e,
        timeout: 200000

    }, function (data) { alert(data);

        if (data != false) {
            location.reload();
        }

    });

});










