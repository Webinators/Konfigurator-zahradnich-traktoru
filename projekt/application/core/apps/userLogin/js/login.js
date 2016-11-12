/**
 * Created by Radim on 23.4.2015.
 */

includeOnce.js(''+mainVariables.pathtofiles+'js/admin/makepass.js');

$(document).ready(function(){

    if($("#userLogEmail").val() != ''){
        $("#userLogEmail").val("");
    }
    if($("#userLogPassword").val() != ''){
        $("#userLogPassword").val("");
    }

});


$(document).on("click","#LoggedBarUserLoginFormSubmit",function(e){

    var emailD = $("#userLogEmail");
    var passD = $("#userLogPassword");

    var password = passD.val();
    var createdPass = makepasswords(password, log_normalalphabet, log_newalphabet);

    passD.val(createdPass);

    sendData({

        form: $(this).closest("form"),
        method: "POST",
        progress: "window",
        event: e

    },function(data){

        if(data != false){
            location.reload();
        } else {
            passD.val(password);
        }

    });

});


// Forgotten password

$(document).on("change", "#ForgottenPasswordEmail", function(){

   $("#ForgottenPasswordBtn").show();
    
    /*
   if($(this).val() != ''){
       $("#ForgottenPasswordBtn").show();
   } else {
       $("#ForgottenPasswordBtn").hide();
   }
    */
});