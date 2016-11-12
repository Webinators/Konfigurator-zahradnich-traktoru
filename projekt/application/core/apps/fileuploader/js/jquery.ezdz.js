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

        while(target.attr("class") != "FileUploaderExtendetContainer")
        {
            target = target.parent();
        }

        return target;

    },

    moveToBody: function(target){

        target = this.moveToContainer(target);
        target.find(".FileUploaderExtendetBody");

        return target;

    },

    getID: function(target){

        target = this.moveToContainer(target);
        var ID = target.attr("id").match(/\d+/)[0];
        return ID;

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

        var allowed = "";

        if (isDefined(this.limits[this.ID][4])) {
            allowed = ", přípony: <b>" + this.limits[this.ID][4].replace(/[|]/g, ", ") + "</b>";
        } else {
            this.limits[this.ID][4] = "";
        }

        input.parent().prepend('Maximální počet souborů: '+this.limits[this.ID][0]+', maximální velikost souboru: '+(this.limits[this.ID][2]/1024)/1024+' MB, Maximální velikost všech souborů: '+(this.limits[this.ID][1]/1024)/1024+' MB'+allowed+'');

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
        '<div class="FileUploaderExtendetOptionsCategory1"><a id="FileUploaderExtendetIcons" class="FileUploaderExtendetEdit"><img src="'+mainVariables.pathtoimages+'icons/edit/basicedit.png" height="28px" title="edit" alt="edit"/></a></div>' +
        '<div class="FileUploaderExtendetOptionsCategory2"><a data-type="graphics" id="FileUploaderExtendetIconsSelected" class="FileUploaderExtendetOption"><img src="'+mainVariables.pathtofiles+'apps/fileuploader/icons/graphics.png" height="28px" title="Grafický režim" alt="Grafický režim"/></a>' +
        '<a id="FileUploaderExtendetIcons" data-type="table" class="FileUploaderExtendetOption"><img src="'+mainVariables.pathtofiles+'apps/fileuploader/icons/table.png" height="28px" title="Řádkový režim" alt="Řádkový režim"/></a></div>' +
        '</div>');

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

    loadImageFromDevice: function(file, basename,content){

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

            };

        }

    },

    graphicsPreview: function(content, ID){

        var wait = false;

        for(var i = 0; i < this.DroppedFiles[ID].length; i++){

            var basename = this.DroppedFiles[ID][i].name.replace(/\\/g, '/').replace(/.*\//, ''),
                extension = this.DroppedFiles[ID][i].name.split('.').pop(),
                found = false;

            content.find(".FileUploaderExtendetPreview").each(function () {

                if ($(this).find(".FileUploaderExtendetFileName").text() == basename) {
                    found = true;
                }

            });

            if (!found) {

                content.append('<div class="FileUploaderExtendetPreview"></div>');

                if (extension.match(/jpg|gif|png|jpeg/gi)) {

                    this.loadImageFromDevice(this.DroppedFiles[ID][i], basename, content.find(".FileUploaderExtendetPreview").last());

                } else {

                    var img = '<img src="' + mainVariables.pathtoimages + 'icons/filesTypes/';

                    if (extension.match(/doc|docx|odt|pdf|rar|xls|zip/gi)) {
                        img = img + extension + ".png";
                    } else {
                        img = img + "others.png";
                    }

                    img = img + '" title="' + extension + '" alt="' + extension + '" height="70px"/>';

                    content.children().last().append(img);
                    content.children().last().append('<div class="FileUploaderExtendetFileName">' + basename + '</div>');
                    content.children().last().append('<div class="FileUploaderExtendetPreviewOptions" title="Odebrat soubor" style="display: none"><object class="FileUploaderExtendetPreviewRemoveIcon" type="image/svg+xml" data="' + mainVariables.pathtoimages + 'icons/remove/remove.svg">Your browser does not support SVG</object></div>');

                    var object = content.children().last().find(".FileUploaderExtendetPreviewOptions");
                    makeSVGOverlay("FileUploaderExtendetPreviewOptionsObject", object);

                }
            }

        }

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
        pic.remove();

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
    var body = FileUploaderExtendet.moveToBody($(this).closest("div"));

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

    var category = $(this).parent().attr("class");
    var content = FileUploaderExtendet.moveToBody($(this).closest("div")).find(".FileUploaderExtendetPreviews");
    var ID = FileUploaderExtendet.getID(content);

    FileUploaderExtendet.highlightOption($(this),category);

    if(isDefined($(this).attr("data-type"))){

        content.html("",false);
        FileUploaderExtendet.type[ID] = $(this).attr("data-type");
        FileUploaderExtendet.preview(content,ID);

    } else {

        body.find(".FileUploaderExtendetContent").css({"z-index": 2000});
        body.find("input[type=file]").last().hide();

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
