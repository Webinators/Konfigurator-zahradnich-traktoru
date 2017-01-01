
$(document).on("change",".productCategory",function(e){

    var dest = $(document).find("#productCategoryDest");

    sendData({

        url: $(this).attr("data-url"),
        data: {Kategorie: $(this).val()},
        event: e,
        bar: true,
        progress: dest,
        method: "POST"

    },function(data){

        setTimeout(function(){
            dest.html(data);
        }, 500);

    });

});

function ProductParamsEnable1(input){

    $("#ProductParamsEnable1Target").toggle();

}

$(document).on("click",".productParam:last",function(){

    var parent = $(this).closest("tr");
    var prev = parent.prev();
    var input = parent.find('input[type="text"]');

    if(input.val() != ""){
        var clone = prev.clone();
        clone.insertBefore(parent).show().find('input[type="text"]').val(input.val());
        input.val("");
    }

});

$(document).on("click", ".removeProductParam", function(){

    $(this).closest("tr").remove();

});

$(document).on("click",".productParamAddAfter",function(){

    var clone = $(this).closest("tr").clone();
    clone.insertAfter($(this).closest("tr"));
    $(this).closest("tr").next().find("input[type='text']").val("");

});

