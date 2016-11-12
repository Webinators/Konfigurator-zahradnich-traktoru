includeOnce.js(''+mainVariables.pathtofiles+'js/admin/makepass.js"');

var registerUserConstants = {

    validationImg: '<img src="'+mainVariables.pathtoimages+'icons/progress/validation.gif" alt="validating" title="validating" width="23px" />',
    errImg: '<img src="'+mainVariables.pathtoimages+'icons/validation/wrong.png" alt="wrong" title="wrong" width="23px" />',
    okImg: '<img src="'+mainVariables.pathtoimages+'icons/validation/right.png" alt="OK" title="OK" width="23px" />'

};

var registerUserChecker = {

    name: ["",false],
    SecondName: ["",false],
    email: ["",false],
    code: ["",false],
    confirmCode: ["",false]
};

$("#regUsername").on("change",function(){
    regCheckUserName(function(result){});
});

function regCheckUserName(callback){

    var name = $("#regUsername");
    var nameErr = $("#regUsernameerr");

    nameErr.html(registerUserConstants.validationImg);

    var username = name.val();

    if(username == registerUserChecker.name[0] && registerUserChecker.name[1]){

        setTimeout(function () {
            nameErr.html(registerUserConstants.okImg);
        }, 1000);

        callback(true);

    } else {

        if(username == ''){

            setTimeout(function () {
                nameErr.html(registerUserConstants.errImg + ' Nevyplnil/a jste jméno');
            }, 1000);

            callback(false);

        } else {
            registerUserChecker.name[0] = username;

            sendData({

                data: {name: username},
                url: "" + mainVariables.pathtofiles + "load/users/form/checkname.php",
                method: "POST",
                alert: false

            }, function (data, err) {

                if(data != false){

                    setTimeout(function () {
                        nameErr.html(registerUserConstants.okImg);
                    }, 1000);

                    registerUserChecker.name[1] = true;
                    callback(true);

                } else {

                    setTimeout(function () {
                        nameErr.html(registerUserConstants.errImg + ' ' + err);
                    }, 1000);

                    registerUserChecker.name[1] = false;
                    callback(false);
                }
            });
        }
    }
}

$("#regUsersecondname").on("change",function(){
    regCheckUserSecondName(function(result){});
});

function regCheckUserSecondName(callback){

    var name = $("#regUsersecondname");
    var nameErr = $("#regUsersecondNameErr");

    nameErr.html(registerUserConstants.validationImg);

    var username = name.val();

    if(username == registerUserChecker.SecondName[0] && registerUserChecker.SecondName[1]){

        setTimeout(function () {
            nameErr.html(registerUserConstants.okImg);
        }, 1000);

        callback(true);

    } else {

        if(username == ''){

            setTimeout(function () {
                nameErr.html(registerUserConstants.errImg + ' Nevyplnil/a jste Přijmení');
            }, 1000);

            callback(false);

        } else {

            registerUserChecker.SecondName[0] = username;

            sendData({

                data: {name: username},
                url: "" + mainVariables.pathtofiles + "load/users/form/checksecondname.php",
                method: "POST",
                alert: false

            }, function (data, err) {

                if(data != false){

                    setTimeout(function () {
                        nameErr.html(registerUserConstants.okImg);
                    }, 1000);

                    registerUserChecker.SecondName[1] = true;
                    callback(true);

                } else {

                    setTimeout(function () {
                        nameErr.html(registerUserConstants.errImg + ' ' + err);
                    }, 1000);

                    registerUserChecker.SecondName[1] = false;
                    callback(false);
                }

            });
        }
    }
}

$("#regEmail").on("change",function(){
    regCheckEmail(function(result){});
});

function regCheckEmail(callback){

    var emailInput = $("#regEmail");
    var emailErr = $("#regEmailerr");

    emailErr.html(registerUserConstants.validationImg);

    var email = emailInput.val();

    if(email == registerUserChecker.email[0] && registerUserChecker.email[1]){

        setTimeout(function () {
            emailErr.html(registerUserConstants.okImg);
        }, 1000);

         callback(true);

    } else {

        if(email == ''){

            setTimeout(function () {
                emailErr.html(registerUserConstants.errImg + ' Nevyplnil/a jste email');
            }, 1000);

            callback(false);

        } else {

            registerUserChecker.email = email;

            sendData({

                data: {"email": email, "noduplicate": "true"},
                url: "" + mainVariables.pathtofiles + "load/users/form/checkemail.php",
                method: "POST",
                alert: false


            }, function (data, err) {

                if(data != false){

                    setTimeout(function () {
                        emailErr.html(registerUserConstants.okImg);
                    }, 1000);

                    registerUserChecker.email[1] = true;
                    callback(true);

                } else {

                    setTimeout(function () {
                        emailErr.html(registerUserConstants.errImg + ' ' + err);
                    }, 1000);

                    registerUserChecker.email[1] = false;
                    callback(false);
                }


            });
        }
    }
}

$("#regPassword1").on("change",function(){
    regCheckPassword(function(result) {});
});

function regCheckPassword(callback){

    var passwordInput = $("#regPassword1");
    var passwordErr = $("#regPassword1err");

    passwordErr.html(registerUserConstants.validationImg);

    var password = passwordInput.val();

    if(password == ""){

        setTimeout(function () {
            passwordErr.html(''+registerUserConstants.errImg+' Nevyplnil/a jste heslo');
        },1000);

        callback(false);

    } else {

        setTimeout(function () {
            passwordErr.html(registerUserConstants.okImg);
        },1000);

        callback(true);

    }

}

$("#regPassword2").on("change",function(){
    regCheckPasswordRepeat(function(result){});
});

function regCheckPasswordRepeat(callback){

    var passwordInput = $("#regPassword2");
    var passwordErr = $("#regPassword2err");

    passwordErr.html(registerUserConstants.validationImg);

    var passwordRepeat = passwordInput.val();
    var password = $("#regPassword1").val();

    if(passwordRepeat == ""){

        setTimeout(function () {
            passwordErr.html(''+registerUserConstants.errImg+' Nevyplnil/a jste heslo znovu');
        },1000);

        callback(false);
    } else {

        if(passwordRepeat != password){

            setTimeout(function () {
                passwordErr.html(''+registerUserConstants.errImg+' Heslo znovu se neshoduje s heslem');
            },1000);

            callback(false);

        } else {

            setTimeout(function () {
                passwordErr.html(registerUserConstants.okImg);
            },1000);

            callback(true);

        }
    }
}


$("#regGeneratedpic").on("change",function(){
    regCheckGeneratedPic(function(result){});
});

function regCheckGeneratedPic(callback){

    var pic = $("#regGeneratedpic");
    var picErr = $("#regGeneratedpicerr");

    picErr.html(registerUserConstants.validationImg);

    var generatedCode = pic.val();

    if(generatedCode == registerUserChecker.code[0] && registerUserChecker.code[1]){

        setTimeout(function () {
            picErr.html(''+registerUserConstants.okImg);
        },1000);
        callback(true);

    } else {

        if(generatedCode == ''){

            setTimeout(function () {
                picErr.html(registerUserConstants.errImg + ' Nevyplnil/a jste kód');
            }, 1000);

            callback(false);

        } else {

            registerUserChecker.code[0] = generatedCode;

            sendData({

                data: {key: generatedCode},
                url: "" + mainVariables.pathtofiles + "apps/userRegister/load/checkgeneratedcode.php",
                method: "POST",
                alert: false

            }, function (data, err) {

                if (data != false) {

                    setTimeout(function () {
                        picErr.html(registerUserConstants.okImg);
                    }, 1000);

                    registerUserChecker.code[1] = true;
                    callback(true);

                } else {

                    setTimeout(function () {
                        picErr.html(registerUserConstants.errImg + ' ' + err);
                    }, 1000);

                    registerUserChecker.code[1] = false;
                    callback(false);
                }

            });
        }
    }
}

function regCheckAll(callback){

    regCheckUserName(function(result){

        if(result){
            regCheckUserSecondName(function(result){
                if(result){
                    regCheckEmail(function(result){
                        if(result){
                            regCheckPassword(function(result){
                                if(result){
                                    regCheckPasswordRepeat(function(result){
                                        if(result){
                                            regCheckGeneratedPic(function(result){
                                                if(result){
                                                    callback(true);
                                                } else {
                                                    callback(false);
                                                }
                                            });
                                        } else {
                                            callback(false);
                                        }
                                    });
                                } else {
                                    callback(false);
                                }
                            });
                        } else {
                            callback(false);
                        }
                    });
                } else {
                    callback(false);
                }
            });
        } else {
            callback(false);
        }
    });

}

$("#regPostmail").on("click",function(e){

    $("#regPostmail").attr('disabled','disabled');
    var postMail = $("#regPostmail");
    var email = $("#regEmail").val();

    regCheckAll(function(result){

        if(result) {

            sendData({

                data: {email: email},
                url: "" + mainVariables.pathtofiles + "apps/userRegister/load/regsendmail.php",
                method: "POST",
                progress: "window",
                alert: false

            }, function (data, err) {

                if (data != false) {
                    $("#regPostmail").hide();
                    $(".registrationNextPart").show();
                    $("#regallerr").html("Na email vám byl poslán potvrzovací kód.");
                }

            });

        } else {
            postMail.removeAttr("disabled");
        }

    });
});

$(document).on("change","#regConfirmcode",function(){
    regCheckConfirmCode(function(result){});
});

function regCheckConfirmCode(callback){

    var confirm = $("#regConfirmcode");
    var confirmErr = $("#regConfirmErr");

    confirmErr.html(registerUserConstants.validationImg);

    var confirmCode = confirm.val();

    if(confirmCode == registerUserChecker.confirmCode[0] && registerUserChecker.confirmCode[1]){

        setTimeout(function () {
            confirmErr.html(registerUserConstants.okImg);
        }, 1000);
        callback(true);

    } else {

        if(confirmCode == ''){

            setTimeout(function () {
                confirmErr.html(registerUserConstants.errImg + " Nevyplnil/a jste potvrzovací kód");
            }, 1000);
            callback(true);

        } else {

            registerUserChecker.confirmCode[0] = confirmCode;

            sendData({

                data: {confirmkey: confirmCode},
                url: "" + mainVariables.pathtofiles + "apps/userRegister/load/regcheckconfirmcode.php",
                method: "POST",
                alert: false

            }, function (data, err) {

                if (data != false) {

                    setTimeout(function () {
                        confirmErr.html(registerUserConstants.okImg);
                    }, 1000);

                    registerUserChecker.confirmCode[1] = true;
                    callback(true);

                } else {

                    setTimeout(function () {
                        confirmErr.html(registerUserConstants.errImg + ' ' + err);
                    }, 1000);

                    registerUserChecker.confirmCode[1] = false;
                    callback(false);
                }

            });
        }
    }
}

$(document).off().on("click","#usersRegistrationFormBtn",function(e) {

    var origpass = $("#regPassword1").val();
    var secondpass = $("#regPassword2").val();
    var neworigpass = makepasswords(origpass, reg_normalalphabet, reg_newalphabet);
    var newsecondpass = makepasswords(secondpass, reg_normalalphabet, reg_newalphabet);
    $("#regPassword1").val(neworigpass);
    $("#regPassword2").val(newsecondpass);

    var beforeSend = function (callback) {

        regCheckAll(function (result) {
            if (result) {
                regCheckConfirmCode(function (result) {
                    if (result) {
                        callback(true);
                    } else {
                        callback(false);
                    }
                });
            } else {
                callback(false);
            }
        });

    };

    sendData({

        form: $("#usersRegistrationForm"),
        method: "POST",
        progress: "window",
        beforeSend: beforeSend,
        event: e

    }, function (data) { alert(data);

        if (data != false) {
            alert("Byl/a jste úspěšně registrován/a. Nyní budete přesměrován/a na hlavní stránku");
            var nowUrl = window.location.href;
            var urlParts = nowUrl.split("?");
            window.location.href = urlParts[0];
        } else {
            $("#regPassword1").val(origpass);
            $("#regPassword2").val(secondpass);
        }

    });

});