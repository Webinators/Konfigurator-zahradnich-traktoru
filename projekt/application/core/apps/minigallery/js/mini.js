$(document).on("click", ".miniGalleryBtn", function(e){

    var dest = $(this).closest(".miniGallery").find(".miniGalleryFiles")
    
    sendData({

        url: $(this).attr("data-url"),
        elem: $(this),
        event: e,
        bar: true,
        progress: dest,
        method: "GET"

    },function(data){

        setTimeout(function(){
            dest.html(data);
        }, 500);

    });
    
});