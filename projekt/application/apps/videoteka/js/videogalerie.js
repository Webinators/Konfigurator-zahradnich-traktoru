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

$(document).on("click","#mainGalerieAddNewVideo", function(e){
    
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
           width: "500px",
           buttonPointer: btn,
           event: e

       });
       
   });
    
});

$(document).on("click","#mainGalerieAddVideo",function(e){

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


$(document).on("click",".galleryShowVideo", function(e){

   e.preventDefault();    
   var btn = $(this); 
   var video = btn.attr("data-url");    

       mainWindow.normal({

           center: true,
           bar: true,
           title: "Přidání nové galerie",
           content: '<iframe style="border: 2px #000 solid;" width="560px" height="315px" src="http://www.youtube.com/embed/'+video+'?rel=0" frameborder="0" allowfullscreen></iframe>',
           buttonPointer: btn,
           event: e

       });

});


$(document).on("click","#mainGalerieRemoveVideo",function(e){

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
                        content: "Video úspěšně smazáno"
                    },function(){
                        btn.closest("div").remove();
                    });

                }

            });
        }
    });

});









