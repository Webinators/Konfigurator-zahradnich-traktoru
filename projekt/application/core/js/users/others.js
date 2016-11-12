var idsResizableinputs = 0;

function insertResizableInput(destination,inputtype,inputname,min,max)
{

    idsResizableinputs++;

    if (typeof min !== typeof undefined && min !== false && typeof max !== typeof undefined && max !== false) {
    } else {
        min = $("#"+destination+"").width() * (50/100);
        max = $("#"+destination+"").width() * (95/100);
    }

    var orig = $("#"+destination+"").html();
    var text = $("#"+destination+" a").text();

    $("#"+destination+"").html('<input class="resizableInput" data-min-size="'+min+'" data-max-size="'+max+'" id="Resizableinput'+idsResizableinputs+'" name="'+inputname+'" type="'+inputtype+'" value="'+text+'" />');

    setTimeout(function() {
        $('#Resizableinput'+idsResizableinputs+'').focus();
    }, 500);

    adjustInputSize($('#Resizableinput'+idsResizableinputs+''),min,max);

    $(document).on("blur","#Resizableinput"+idsResizableinputs+"",function(){

        if($(this).val() == text || $(this).val() == ''){
            $("#"+destination+"").html(orig);
        }

    });

}
