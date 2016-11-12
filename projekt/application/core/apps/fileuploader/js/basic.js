var FileUploaderBasic = {

    ID:0,
    files: [],
    FilesNames: [],
    sizeChecker: [],
    limits: [],
    errors: [],
    windowPointer: [],

    findFilename: function(name, ID, type){

        for(var i = 0; i < this.FilesNames[ID].length; i++){

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

        while(target.attr("class") != "FileUploaderBasicContainer")
        {
            target = target.parent();
        }

        return target;

    },

    moveToBody: function(target){

        target = this.moveToContainer(target);
        target = target.find(".FileUploaderBasicBody");

        return target;

    },

    getID: function(target){

        target = this.moveToContainer(target);
        var ID = target.attr("id").match(/\d+/)[0];
        return ID;

    },

    insertNewUploader: function(inputName, maxFiles, maxFileSize, allowedFormats, callback){

        sendData({

            url: ""+mainVariables.pathtofiles+"apps/fileuploader/newBasic.php",
            data: {"name":inputName, "maxFiles":maxFiles, "maxFileSize":maxFileSize, "allowedFormats":allowedFormats},
            method: "POST"

        },function(data){

            if(data != false){
                callback(data);
            }

        });

    },

    buildBasicUploader: function(input){

        input.attr("data-build", "true");

        var limits = input.parent().find("input[type=text]").val();
        limits = limits.split(",");

        FileUploaderBasic.limits[this.ID] = [];

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

        input.parent().prepend("max souborů: " + this.limits[this.ID][0] + ", soubor max: " + (this.limits[this.ID][2] / 1024) / 1024 + " MB, celkem max: " + (this.limits[this.ID][1] / 1024) / 1024 + " MB" + allowed + "");

        input.closest("div").attr("id", 'FileUploaderBasic['+this.ID+']');

        this.FilesNames[this.ID] = [];
        this.files[this.ID] = [];
        this.sizeChecker[this.ID] = 0;
        this.errors[this.ID] = "";
        this.windowPointer[this.ID] = "";

        input.wrap('<div class="FileUploaderBasicBody">');
        input.parent().prepend('<div class="FileUploaderBasicDescribe">Vyberte soubor</div>');
        input.parent().append('<a class="FileUploaderBasicEdit" id="FileUploaderBasicEdit'+this.ID+'"><img src="'+mainVariables.pathtoimages+'icons/edit/basicedit.png" alt="edit" title="edit" style="width: 20px; height: 20px;"/></a>');

        this.ID++;
    },

    update: function(){

        $(document).find(".FileUploaderBasic").each(function(){

            if(!($(this).attr("data-build") == "true")){
                FileUploaderBasic.buildBasicUploader($(this));
            }

        });

    },

    showFiles: function(content, ID){

        var describe = content.find(".FileUploaderBasicDescribe");

        var btn = content.find(".FileUploaderBasicEdit img");

        if(FileUploaderBasic.files[ID].length == 1){
            describe.html('Vybrán: '+FileUploaderBasic.files[ID].length+' soubor');btn.show();
        } else if(FileUploaderBasic.files[ID].length > 1) {
            describe.html('Vybráno: '+FileUploaderBasic.files[ID].length+' souborů');btn.show();
        } else {
            describe.html('Vyberte soubor/y');
            btn.hide();
        }

    },

    makeFilePreview: function(file, ID) {

        var basename = file.name.replace(/\\/g, '/').replace(/.*\//, ''),
            extension = file.name.split('.').pop(),
            fileSize = file.size,
            limits = FileUploaderBasic.limits[ID][4].replace(",", "|"),
            re = new RegExp(limits, "gi");

        if (!extension.match(re)) {
            FileUploaderBasic.errors[ID] += "Soubor <b>" + basename + "</b> nepatří mezi povolené<br />";
            return false;
        }

        if (fileSize <= FileUploaderBasic.limits[ID][2]) {
            if ((FileUploaderBasic.sizeChecker[ID] + fileSize) <= FileUploaderBasic.limits[ID][1]) {
                if (FileUploaderBasic.files[ID].length < FileUploaderBasic.limits[ID][0]) {

                    if (!FileUploaderBasic.findFilename(basename, ID, 0)) {

                        extension = extension.toLowerCase();

                        FileUploaderBasic.sizeChecker[ID] += fileSize;
                        FileUploaderBasic.FilesNames[ID].push(basename);
                        FileUploaderBasic.files[ID].push(file);

                    } else {
                        FileUploaderBasic.errors[ID] += "soubor <b>" + basename + "</b> už je vložený<br />";
                    }

                } else {
                    FileUploaderBasic.errors[ID] += "Byl překročen maximální počet souborů<br />";
                    return false;
                }
            } else {
                FileUploaderBasic.errors[ID] += "Byl překročen limit celkové maximální velikosti dat<br />";
                return false;
            }
        } else {
            FileUploaderBasic.errors[ID] += "Soubor: <b>" + basename + "</b> je příliš velký<br />";
        }

        return true;

    },

    editFiles: function(ID,event,btn){

        var data = '<table id="FileUploaderBasicTable" data-id="'+ID+'">';

        for(var i = 0; i < this.files[ID].length; i++){

            var basename = this.files[ID][i].name.replace(/\\/g, '/').replace(/.*\//, ''),
                fileSize = this.files[ID][i].size;

            data = data + '<tr><td style="text-align: left;">'+basename+'</td><td>'+((fileSize/1024)/1024).toFixed(2)+' MB</td><td><img src="'+mainVariables.pathtoimages+'icons/remove/remove.png" alt="smazat" title="smazat" width="18px"/></td></tr>';

        }

        data = data + "</table>";
        var options = '<a id="FileUploaderBasicEditRemoveAll"><img src="'+mainVariables.pathtoimages+'icons/remove/removeall.png" alt="Odstranit vše" alt="odstranit vše" width="30px"/></a>';

        mainWindow.normal({

            bar: {
                title: "Editace souborů",
                help: "pokus",
                options: options
            },
            content: data,
            event: event,
            buttonPointer: btn

        },function(window){
            FileUploaderBasic.windowPointer[ID] = window;

        });


    },

    removeFile: function(destination, ID) {

        var filename = destination.children().first().text();
        var index = this.findFilename(filename, ID, 1);

        if (index >= 0) {
            FileUploaderBasic.sizeChecker[ID] -= FileUploaderBasic.files[ID][index].size;
            FileUploaderBasic.FilesNames[ID].splice(index, 1);
            FileUploaderBasic.files[ID].splice(index, 1);
        }

        destination.remove();

    },

    getData: function(input){

        var ID = this.getID(input);
        return this.files[ID];
    }

};

$(document).ready(function() {

    $(".fileUploaderBasic").each(function () {
        FileUploaderBasic.buildBasicUploader($(this));
    });

});

$(document).on("change", ".FileUploaderBasic", function (e) {

    var input = $(this);
    var files = this.files;

    var ID = FileUploaderBasic.getID(input);
    var content = FileUploaderBasic.moveToBody(input);

    var i = 0;

    while (i < files.length) {

        if (FileUploaderBasic.makeFilePreview(files[i], ID)) {
            i++;
        } else {
            break;
        }

    }

    if(FileUploaderBasic.errors[ID] != '') {
        mainWindow.alert({buttonPointer: false, content: FileUploaderBasic.errors[ID]});
        FileUploaderBasic.errors[ID] = '';
    }

    FileUploaderBasic.showFiles(content, ID);

    input.replaceWith(input.val('').clone(true));

});

$(document).on("click", ".FileUploaderBasicEdit", function (e) {

    e.preventDefault();

    var ID = FileUploaderBasic.getID($(this));
    FileUploaderBasic.editFiles(ID,e,$(this));

});

$(document).on("click", "#FileUploaderBasicEditRemoveAll", function (e) {

    var ID = mainWindow.moveToBody($(this)).find("table").attr("data-id");
    var btn = $(this);

    mainWindow.getButtonPointer(FileUploaderBasic.windowPointer[ID],function(pointer){console.log(pointer);

        mainWindow.confirm(btn,"Opravdu chcete odebrat všechny soubory?", e, function(result){

            if(result){

                mainWindow.moveToBody(btn).find("table").find("tr").each(function(){
                    FileUploaderBasic.removeFile($(this),ID);
                });

                pointer.closest("div").find(".FileUploaderBasic").change();
            }

        });

    });

});

$(document).on("click", "#FileUploaderBasicTable tr", function (e) {

    var ID = $(this).closest("table").attr("data-id");
    var row = $(this);

    mainWindow.getButtonPointer(FileUploaderBasic.windowPointer[ID],function(pointer) {

        FileUploaderBasic.removeFile(row,ID);
        pointer.closest("div").find(".FileUploaderBasic").change();

    });

});

$(document).on("mouseover", "#FileUploaderBasicTable tr", function (e) {

    $(this).css({"background-color": "#2e2e2e","color":"#ffffff"});

});

$(document).on("mouseout", "#FileUploaderBasicTable tr", function (e) {

    $(this).css({"background-color": "","color":"#000000"});

});