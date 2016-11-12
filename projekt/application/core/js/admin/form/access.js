var adminRegistrationChecker = {

    validateImg: '<img src="'+mainVariables.pathtoimages+'icons/progress/validation.gif" alt="validating" title="validating" width="23px" />',
    errImg: '<img src="'+mainVariables.pathtoimages+'icons/validation/wrong.png" alt="wrong" title="wrong" width="23px" />',
    okImg: '<img src="'+mainVariables.pathtoimages+'icons/validation/right.png" alt="Ok" title="Ok" width="23px" />',
    accessCode: "",
    accessCodeChecked: false
};

$("#adminRegistrationAccessInput").on("change", function(){
    checkAdminAccessCode(function(result){});
});

function checkAdminAccessCode(callback) {

    var output = $("#adminRegistrationAccessErr");

    output.html(adminRegistrationChecker.validateImg);
    var pass = $("#adminRegistrationAccessInput").val();

    if(adminRegistrationChecker.accessCode == pass && adminRegistrationChecker.accessCodeChecked){
        callback(true);
    } else {

        if (pass == '') {

            output.html(adminRegistrationChecker.errImg + ' Nevyplnil/a jste email');
            adminRegistrationChecker.accessCodeChecked = false;

            callback(false);

        } else {

            adminRegistrationChecker.accessCode = pass;

            sendData({

                data: {code: pass},
                url: mainVariables.pathtofiles + "load/admin/form/checkAdminAcessCode.php",
                method: "POST",
                alert: false

            }, function (data, err) {

                if (data != false) {
                    output.html(adminRegistrationChecker.okImg);
                    adminRegistrationChecker.accessCodeChecked = true;
                    callback(true);
                } else {

                    output.html(adminRegistrationChecker.errImg + " Špatně opsaný kód");
                    adminRegistrationChecker.accessCodeChecked = false;
                    callback(false);
                }

            });

        }
    }
}

$(document).on("click","#adminRegistrationAccess", function(e) {

    var btn = $(this);    
    var output = $("#adminRegistrationAccessErr");

    var before = function(callback) {

        checkAdminAccessCode(function (result) {

            if (result) {
                callback(true);
            } else {
                callback(false);
            }

        });
    };

    sendData({

        form: $("#adminRegistrationForm"),
        method: "POST",
        progress: "window",
        beforeSend: before,
        event: e,alert: false

    },function(data){

        if(data != false){
           
           mainWindow.update(mainWindow.moveToBody(btn),data);
           
            $("#registration_obal").html(data);
        } else {
            output.html(adminRegistrationChecker.errImg + ' ' + data);
        }

    });

});
