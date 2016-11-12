window.addEventListener("dragover", function (e) {
    e = e || event;
    e.preventDefault();
}, false);

window.addEventListener("drop", function (e) {
    e = e || event;
    e.preventDefault();
}, false);

var FileUploaderExtendet = {

    ID: 0,
    FilesNames: [],
    DroppedFiles: [],
    counter: [],
    sizeChecker: [],
    limits: [],
    errors: [],
    type: [],



    findFilename: function(name, ID, type){

        for(var i = 0; i < FileUploaderExtendet.FilesNames[ID].length; i++){

            if(this.FilesNames[ID][i] == name){

                if(type == 0) {
                    return true;
                } else {
                    return i;
                }
            }
        }

        if(type == 0) {
            return false;
        } else {
            return -1;
        }

    },

    moveToContainer: function(target){

        target = target.closest(".FileUploaderExtendetContainer");

        if(target.length){

            return target;

        }

        return false;

    },

    moveToBody: function(target){

        target = this.moveToContainer(target);

        if(target != false){
            return target.find(".FileUploaderExtendetBody");
        }

        return target;

    },

    getID: function(target){

        target = this.moveToContainer(target);
        var ID = target.attr("id").match(/\d+/)[0];
        return ID;

    },

    buildHelpMessage: function(ID){

        var allowed = "";

        if (isDefined(this.limits[ID][4])) {
            allowed = ", <br />Povolené přípony: <b>" + this.limits[ID][4].replace(/[|]/g, ", ") + "</b>";
        } else {
            this.limits[ID][4] = "";
        }

        return 'Maximální počet souborů: '+this.limits[ID][0]+', <br />maximální velikost souboru: '+(this.limits[ID][2]/1024)/1024+' MB,<br /> Maximální velikost všech souborů: '+(this.limits[ID][1]/1024)/1024+' MB'+allowed+'';

    },


    buildExtendedUploader: function(input){

        input.attr("data-build","true");

        var limits = input.parent().find("input[type=text]").val();
        limits = limits.split(",");

        this.limits[this.ID] = [];

        for(var j = 0; j < limits.length; j++){
            this.limits[this.ID][j] = limits[j];
        }

        input.parent().find("input[type=text]").hide();
        input.parent().attr("id", 'FileUploaderExtendet['+this.ID+']');
        input.wrap('<div class="FileUploaderExtendetBody"></div>');

        this.FilesNames[this.ID] = [];
        this.counter[this.ID] = 0;
        this.DroppedFiles[this.ID] = [];
        this.sizeChecker[this.ID] = 0;
        this.errors[this.ID] = "";
        this.type[this.ID] = "graphics";

        input.wrap('<div class="FileUploaderExtendetContent"></div>');
        input.parent().prepend('<div class="FileUploaderExtendetPreviews"></div>');
        input.parent().append('<br style="clear: both;"/>');

        input.closest('div').before('' +
            '<div class="FileUploaderExtendetOptions">' +
            '<div class="FileUploaderExtendetOptionsCategory1"><a id="FileUploaderExtendetIcons" class="FileUploaderExtendetEdit"><img src="'+mainVariables.pathtoimages+'icons/edit/edit.png" height="28px" title="Edit (editace vybraných souborů)" alt="Edit (editace vybraných souborů)"/></a></div>' +
            '<div class="FileUploaderExtendetOptionsCategory2"><a data-type="graphics" id="FileUploaderExtendetIconsSelected" class="FileUploaderExtendetOption"><img src="'+mainVariables.pathtofiles+'apps/fileuploader/icons/graphics.png" height="28px" title="Grafický přehled" alt="Grafický přehled"/></a>' +
            '<a id="FileUploaderExtendetIcons" data-type="table" class="FileUploaderExtendetOption"><img src="'+mainVariables.pathtofiles+'apps/fileuploader/icons/table.png" height="28px" title="Tabulkový přehled" alt="Tabulkový přehled"/></a></div>' +
            '<a id="FileUploaderExtendetHelp"><img src="'+mainVariables.pathtofiles+'utilities/icons/help/help.png" height="28px" title="Nápověda" alt="Nápověda"/></a>' +

            '</div>');

        input.parent().prepend('<div class="FileUploaderExtendetTitle flexElem valignCenter alignElemsCenter" style="position: absolute; top: 0px; left: 0px; font-weight: bold;  width: 100%; height: 100%;">Vyberte soubory kliknutím do plochy nebo přetáhnutím souborů ze složky</div>');

        input.css({width: "100%", height: input.parent().outerHeight()+"px"});

        this.ID++;

    },

    update: function(){

        $(".FileUploaderExtendet").each(function(){

            if(($(this).attr("data-build") != "true")){
                FileUploaderExtendet.buildExtendedUploader($(this));
            }

        });
    },

    checkInsertedFile: function(file, ID) {

        var basename = file.name.replace(/\\/g, '/').replace(/.*\//, ''),
            extension = file.name.split('.').pop(),
            fileSize = file.size,
            limits = this.limits[ID][4].replace(",", "|"),
            re = new RegExp(limits, "gi");

        if (!extension.match(re)) {
            this.errors[ID] += "Soubor <b>" + basename + "</b> nepatří mezi povolené<br />";
            return false;
        }

        if (fileSize <= this.limits[ID][2]) {
            if ((this.sizeChecker[ID] + fileSize) <= this.limits[ID][1]) {
                if (this.DroppedFiles[ID].length < this.limits[ID][0]) {

                    if (!this.findFilename(basename, ID, 0)) {

                        extension = extension.toLowerCase();

                        this.sizeChecker[ID] += fileSize;
                        this.FilesNames[ID].push(basename);
                        this.DroppedFiles[ID].push(file);

                    } else {
                        this.errors[ID] += "soubor <b>" + basename + "</b> už je vložený<br />";
                    }

                } else {
                    this.errors[ID] += "Byl překročen maximální počet souborů<br />";
                    return false;
                }
            } else {
                this.errors[ID] += "Byl překročen limit celkové maximální velikosti dat<br />";
                return false;
            }
        } else {
            this.errors[ID] += "Soubor: <b>" + basename + "</b> je příliš velký<br />";
        }

        return true;

    },

    renderImageFromDevice: function(file, basename,content, callback){

        var reader = new FileReader(file);

        reader.readAsDataURL(file);

        reader.onload = function (e) {

            var img = new Image();

            var origH;
            var origW;

            img.src = e.target.result;
            img.title = basename;
            img.alt = basename;
            img.className = basename;

            img.onload = function () {

                origH = this.height;
                origW = this.width;

                if (origW >= origH) {
                    img.width = 100;
                } else if (origH > origW) {
                    img.height = 80;
                } else {

                }

                content.append($(img).fadeIn());
                content.append('<div class="FileUploaderExtendetFileName">' + basename + '</div>');
                content.append('<div class="FileUploaderExtendetPreviewOptions" title="Odebrat soubor" style="display: none"><object class="FileUploaderExtendetPreviewRemoveIcon" type="image/svg+xml" data="' + mainVariables.pathtoimages + 'icons/remove/remove.svg">Your browser does not support SVG</object></div>');

                var object = content.find(".FileUploaderExtendetPreviewOptions");

                makeSVGOverlay("FileUploaderExtendetPreviewOptionsObject", object);

                callback();

            };

        }

    },

    renderGraphicItem: function(object, callback) {

        if(object.max < object.min){callback();return;}

        var that = this;
        var data = jQuery.extend({}, object);

        object.max--;

        this.renderGraphicItem(object, function () {

            var basename = that.DroppedFiles[data.ID][data.max].name.replace(/\\/g, '/').replace(/.*\//, ''),
                extension = that.DroppedFiles[data.ID][data.max].name.split('.').pop(),
                found = false;

            data.content.find(".FileUploaderExtendetPreview").each(function () {

                if ($(this).find(".FileUploaderExtendetFileName").text() == basename) {
                    found = true;
                }

            });

            if (!found) {

                data.content.append('<div class="FileUploaderExtendetPreview"></div>');
                var progress = mainProgressEvent.insertInto(data.content.find(".FileUploaderExtendetPreview").last(), true);

                if (extension.match(/jpg|gif|png|jpeg/gi)) {

                    that.renderImageFromDevice(that.DroppedFiles[data.ID][data.max], basename, data.content.find(".FileUploaderExtendetPreview").last(), function(){

                        mainProgressEvent.remove(progress);
                        callback();

                    });

                } else {

                    var image_url = mainVariables.pathtoimages+"icons/filesTypes/";
                    var image = extension+".png";

                    $img = new Image();
                    $img.height = 70;
                    $img.title = extension;
                    $img.alt = extension;

                    imageExists(image_url+image, function(result){

                        if(result){
                            $img.src = image_url+image;
                        } else {
                            $img.src = image_url+"others.png";
                        }

                        data.content = data.content.find(".FileUploaderExtendetPreview");

                        data.content.last().append($img);
                        data.content.last().append('<div class="FileUploaderExtendetFileName">' + basename + '</div>');
                        data.content.last().append('<div class="FileUploaderExtendetPreviewOptions" title="Odebrat soubor" style="display: none"><object class="FileUploaderExtendetPreviewRemoveIcon" type="image/svg+xml" data="' + mainVariables.pathtoimages + 'icons/remove/remove.svg">Your browser does not support SVG</object></div>');

                        var object = data.content.last().find(".FileUploaderExtendetPreviewOptions");
                        makeSVGOverlay("FileUploaderExtendetPreviewOptionsObject", object);

                        mainProgressEvent.remove(progress);
                        callback();

                    });

                }

            } else{
                callback();
            }

        });

    },

    graphicsPreview: function(content, ID){

        var data = {content: content, ID: ID, min: 0, max: (this.DroppedFiles[ID].length - 1)};
        this.renderGraphicItem(data, function(){});

    },

    tablePreview: function(content, ID){

        var row = '';

        if(content.find("#FileUploaderExtendetFilesTable").length == 0)
        {
            content.append('<table id="FileUploaderExtendetFilesTable"></table>');
        }

        content = content.find("#FileUploaderExtendetFilesTable");

        for(var i = 0; i < this.DroppedFiles[ID].length; i++){

            var basename = this.DroppedFiles[ID][i].name.replace(/\\/g, '/').replace(/.*\//, ''),
                extension = this.DroppedFiles[ID][i].name.split('.').pop(),
                size = (this.DroppedFiles[ID][i].size / 1024) / 1024,
                rendered = false;

            content.find("tr").each(function() {

                if($(this).children().first().text() == basename){
                    rendered = true;
                }

            });

            if(!rendered) {
                row += '<tr><td style="text-align: left;">' + basename + '</td><td>' + size.toFixed(2) + ' MB</td><td><a class="FileUploaderExtendetFilesTableRemovefile"><img src="'+mainVariables.pathtoimages+'icons/remove/remove.png" alt="smazat" title="smazat" width="18px"/></a></td></tr>';
            }
        }

        content.append(row);
    },

    preview: function(content, ID){

        switch (this.type[ID]){

            case "graphics":
                this.graphicsPreview(content, ID);
                break;
            case "table":
                this.tablePreview(content, ID);
                break;
            default:
                break;

        }
    },

    removeFile: function(destination, name) {

        destination = this.moveToContainer(destination);
        var ID = FileUploaderExtendet.getID(destination);

        if (isDefined(ID)) {

            var index = FileUploaderExtendet.findFilename(name, ID, 1);

            if (index >= 0) {
                FileUploaderExtendet.sizeChecker[ID] -= FileUploaderExtendet.DroppedFiles[ID][index].size;
                FileUploaderExtendet.FilesNames[ID].splice(index, 1);
                FileUploaderExtendet.DroppedFiles[ID].splice(index, 1);
            }
        }

    },

    graphicsRemovePic: function(pic, name) {

        this.removeFile(pic, name);
        pic.fadeOut(300);

    },

    tableRemoveFile: function(row, name) {

        this.removeFile(row,name);
        row.remove();

    },

    highlightOption: function(btn, category) {

        $("."+category+"").find("a").each(function(){

            $(this).attr("id","FileUploaderExtendetIcons");

        });

        btn.attr("id","FileUploaderExtendetIconsSelected");

    },

    getData: function(input){

        var ID = this.getID(input);
        return this.DroppedFiles[ID];

    }

};

$(document).ready(function () {

    $(".FileUploaderExtendet").each(function () {
        FileUploaderExtendet.update($(this));
    });

});

$(document).on("change", ".FileUploaderExtendet", function (e) {

    if (!$(e.target).is(".FileUploaderExtendetPreview")) {

        var input = $(this);
        var files = this.files;

        var ID = FileUploaderExtendet.getID(input);
        var content = FileUploaderExtendet.moveToBody(input).find(".FileUploaderExtendetPreviews");

        var i = 0;

        if(files.length > 0){
            input.parent().find('.FileUploaderExtendetTitle').hide();
        }

        while (i < files.length) {

            FileUploaderExtendet.checkInsertedFile(files[i], ID);
            i++;
        }

        if(FileUploaderExtendet.errors[ID] != '') {
            mainWindow.alert({buttonPointer: false, content: FileUploaderExtendet.errors[ID]});
            FileUploaderExtendet.errors[ID] = '';
        }

        FileUploaderExtendet.preview(content,ID);

        input.replaceWith(input.val('').clone(true));

    }

});

$(document).on("drop", ".FileUploaderExtendet", function (e) {

    e.preventDefault();

    var input = $(this);
    var files = e.originalEvent.dataTransfer.files;

    var ID = FileUploaderExtendet.getID(input);
    var content = FileUploaderExtendet.moveToBody(input).find(".FileUploaderExtendetPreviews");

    var i = 0;

    if(files.length > 0){
        input.parent().find('.FileUploaderExtendetTitle').hide();
    }

    while (i < files.length) {

        if (FileUploaderExtendet.checkInsertedFile(files[i], ID)) {
            i++;
        } else {
            break;
        }

    }

    if(FileUploaderExtendet.errors[ID] != '') {
        mainWindow.alert({buttonPointer: false, content: FileUploaderExtendet.errors[ID]});
        FileUploaderExtendet.errors[ID] = '';
    }

    FileUploaderExtendet.preview(content,ID);

});

$(document).on("mouseover", ".FileUploaderExtendetPreview", function () {

    $(this).closest("div").find(".FileUploaderExtendetPreviewOptions").show();

});

$(document).on("mouseout", ".FileUploaderExtendetPreview", function () {

    $(this).closest("div").find(".FileUploaderExtendetPreviewOptions").hide();

});

$(document).on("click", ".FileUploaderExtendetPreviewOptionsObject", function () {

    var data = $(this).closest("div").parent().parent();

    FileUploaderExtendet.graphicsRemovePic(data,data.find(".FileUploaderExtendetFileName").text());

});

$(document).on("mouseover", "#FileUploaderExtendetFilesTable tr", function (e) {

    $(this).css({"background-color": "#2e2e2e","color":"#ffffff"});

}).on("mouseout", "#FileUploaderExtendetFilesTable tr", function (e) {

    $(this).css({"background-color": "","color":"#000000"});

});

$(document).on("click", ".FileUploaderExtendetFilesTableRemovefile", function (e) {

    e.preventDefault();

    FileUploaderExtendet.tableRemoveFile($(this),$(this).children().first().text());

});

$(document).on("click", "#FileUploaderExtendetFilesTable tr", function (e) {

    e.preventDefault();

    FileUploaderExtendet.tableRemoveFile($(this),$(this).children().first().text());

});

$(document).on("click", ".FileUploaderExtendetEdit", function (e) {

    e.preventDefault();
    var body = FileUploaderExtendet.moveToBody($(this));

    if($(this).attr("id") == "FileUploaderExtendetIconsSelected"){

        $(this).attr("id", "FileUploaderExtendetIcons");
        body.find(".FileUploaderExtendetContent").css({"z-index": 0});
        body.find("input[type=file]").last().show();

    } else {

        $(this).attr("id", "FileUploaderExtendetIconsSelected");
        body.find(".FileUploaderExtendetContent").css({"z-index": 2000});
        body.find("input[type=file]").last().hide();

    }

});

$(document).on("click", ".FileUploaderExtendetOption", function (e) {

    e.preventDefault();
    var btn = $(this);

    var category = btn.parent().attr("class");
    var content = FileUploaderExtendet.moveToBody($(this).closest("div")).find(".FileUploaderExtendetPreviews");
    var ID = FileUploaderExtendet.getID(content);

    FileUploaderExtendet.highlightOption($(this),category);

    if(isDefined($(this).attr("data-type"))){

        content.html("",false);
        FileUploaderExtendet.type[ID] = $(this).attr("data-type");
        FileUploaderExtendet.preview(content,ID);

    } else {

        content.find(".FileUploaderExtendetContent").css({"z-index": 2000});
        content.find("input[type=file]").last().hide();

    }


});

$(document).on("click", ".FileUploaderExtendetEditSelected", function (e) {

    e.preventDefault();

    var body = FileUploaderExtendet.moveToBody($(this));
    var content = body.find("#FileUploaderExtendetContent");

    content.css({"z-index": 0});
    body.find("input[type=file]").last().show();

    $(this).attr("id", "FileUploaderExtendetIcons");
    $(this).attr("class","FileUploaderExtendetEdit");

});


$(document).on("mouseover", "#FileUploaderExtendetHelp", function(e){

    var btn = $(this);

    var content = FileUploaderExtendet.moveToBody(btn);
    var ID = FileUploaderExtendet.getID(content);

    var help = FileUploaderExtendet.buildHelpMessage(ID);

    mainWindow.normal({

        elem: btn,
        center: false,
        bar: false,
        close: false,
        content: help,
        buttonPointer: btn,
        event: e,
        dialog: false,
        fixed: false,
        draggable: false,
        classes:{
            container: "uploaderWinContainer",
            body: "uploaderWinBody",
            content: "uploaderWinContent"
        },
        effect: function(win){
            win.slideDown(500);
        }

    }, function(window){

        if(isDefined(window)){

            var btn = mainWindow.getButtonPointer(window);

            var top = btn.offset().top + btn.outerHeight(true);
            var left = btn.offset().left;

            window.css({top: top, left: left});

            window.mouseleave(function(){

               mainWindow.remove(window, function (win){
                   win.slideUp(500, function () { $(this).remove();});
               });

            })

        }


    });

});