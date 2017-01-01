/**
 * Created by Radim on 18.3.2016.
 */

$(document).on("click","#mainGalerieAddNewTheme", function(e){
    
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
           title: "Přidání nového článku",
           content: data,
           buttonPointer: btn,
           event: e

       });
       
   });
    
});

$(document).on("click","#mainGalerieAddTheme",function(e){

    sendData({

        form: $(this).closest("form"),
        method: "POST",
        progress: "window",
        event: e

    }, function (data) {

        if(data != false){
            mainWindow.alert({
                content: "Článek úspěšně přidán"
            }, function(){
                location.reload();
            });
        }

    });

});

$(document).on("click","#mainGalerieAddNewClanek", function(e){
    
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
           title: "Přidání nového videa",
           content: data,
           buttonPointer: btn,
           event: e

       });
       
   });
    
});

$(document).on("click","#mainGalerieAddClanek",function(e){

    sendData({

        form: $(this).closest("form"),
        method: "POST",
        progress: "window",
        event: e

    }, function (data) {

        if(data != false){
            mainWindow.alert({
                content: "Video úspěšně přidáno"
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

$(document).on("click","#mainGalerieSaveClanek",function(e){

    sendData({

        form: $(this).closest("form"),
        method: "POST",
        progress: "window",
        event: e

    }, function (data) {

        if(data != false){
            mainWindow.alert({
                content: "Článek úspěšně upraven"
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

$(document).on("click","#mainGalerieRemoveClanek",function(e){

    e.preventDefault();

    var btn = $(this);

    mainWindow.confirm(btn,"Opravdu chcete odebrat video?",e,function(result){

        if(result){

            sendData({

                url: btn.attr("href"),
                data: {id_video: btn.attr("data-id-v")},
                method: "POST",
                progress: "window",
                event: e

            }, function (data) {

                if(data != false){

                    mainWindow.alert({
                        content: "Čláenk úspěšně smazán"
                    },function(){
                        btn.closest("div").remove();
                    });

                }

            });
        }
    });

});






$(document).on("click","#mainPoradnaLoginCustomer",function(e){

    sendData({

        form: $(this).closest("form"),
        method: "POST",
        progress: "window",
        event: e

    }, function (data) {

        if(data != false){
            mainWindow.alert({
                content: "Byl jste úspěšně přihlášen do poradny"
            }, function(){
                location.reload();
            });
        }

    });

});




