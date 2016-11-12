$(document).on("click","#poptavkaSendBtn",function(e){

    sendData({

        form: $(this).closest("form"),
        method: "POST",
        progress: "window",
        event: e

    }, function (data) {

        if(data != false){
            mainWindow.alert({
                content: "Poptávka úspěšně odeslána"
            });
        } 

    });

});