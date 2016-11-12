
var mainTablesEditor = {

    ID:0,
    lastRow: [],
    inputs: [],
    options: [],
    cols: [],
    type: [],
    addBtn: '<img src="'+mainVariables.pathtoimages+'icons/add/add.png" alt="Přidat řádek" title="přidat řádek"/>',
    removeBtn: '<img src="'+mainVariables.pathtoimages+'icons/remove/remove.png" title="Odstranit řádek" alt="Odstranit řádek"/>',
    addBetweenBtn: '<img src="'+mainVariables.pathtoimages+'icons/add/row_add_after.png" title="Přidat řádek pod" alt="Přidat řádek pod"/>',
    windowPointer: [],

    getRowId: function(target){

        var rowNum = target.closest("tr").find("input, select").first().attr("name");

        rowNum = rowNum.match(/\[[0-9]*\]/g);
        rowNum = parseInt(rowNum[0].replace("[","").replace("]",""));


        return rowNum;
    },

    getColId: function(target){

        var colNum = target.find("input, select").first().attr("name");

        colNum = colNum.match(/\[[0-9]*\]/g);
        colNum = parseInt(colNum[1].replace("[","").replace("]",""));

        return colNum;
    },

    getInputId: function(target, type){

        if(isDefined(type)){
            var type = "[type="+type+"]";
        } else {
            var type = ""
        }

        var InNum = target.find("input"+type+", select").first().attr("name");

        InNum = InNum.match(/\[[0-9]*\]/g);
        InNum = parseInt(InNum[2].replace("[","").replace("]",""));

        return InNum;

    },

    updateRowID: function(target, newID){

        var name = target.attr("name");
        var newN = replaceIndex(name,"[[0-9]*]","["+newID+"]",1);
        target.attr("name", newN);
    },

    updateInputNum: function(target, newID){

        var name = target.attr("name");
        var newN = replaceIndex(name,"[[0-9]*]","["+newID+"]",3);
        target.attr("name", newN);

    },

    getId: function(target){

        while(target.attr("class") != 'mainTablesEditor'){
            target = target.parent();
        }

        var ID = target.attr("id").match(/\d+/)[0];
        return ID;

    },

    checkInputTypes: function(ID, type, num, rowID, colID, reference, object){

        var i = reference.i;
        var input = "";

        switch(type)
        {
            case 'text':
            case 'number':

                if(isDefined(object)) {
                    if (isObject(object)) {
                        var defValue = object.values[0];
                    } else {
                        var defValue = "";
                    }
                } else {
                    var defValue = "";
                }

                if(this.type[ID] == "extendet") {
                    input += '<input type="hidden" name="type[' + rowID + '][' + colID + ']" value="' + type + '"/><input type="' + type + '" name="value[' + rowID + '][' + colID + ']" value="' + defValue + '"/>';
                    i++;
                } else {
                    input += '<input type="' + type + '" name="value[' + rowID + '][' + colID + ']" value="' + defValue + '"/>';
                    i++;
                }
                break;

            case 'checkbox':

                var betweenRow = '<td id="mainTablesEditorButton" class="mainTablesEditorCheckboxBetweenRow">'+this.addBetweenBtn+'</td>';
                var removeRow = '<td id="mainTablesEditorButton" class="mainTablesEditorCheckboxRemoveRow">'+this.removeBtn+'</td>';
                var addRow = '<td id="mainTablesEditorButton" colspan="3" class="mainTablesEditorCheckboxAddRow">'+this.addBtn+'</td>';

                if (this.type[ID] == "extendet") {
                    input += '<input type="hidden" name="type[' + rowID + '][' + colID + ']" value="checkbox"/><table>';
                }

                for(var j=0;j<num;j++) {

                    if (isDefined(object)) {
                        if (isObject(object)) {
                            var description = object.texts[j];
                            if (object.checked[j]) {
                                var checked = 'checked';
                            } else {
                                var checked = "";
                            }
                        } else {
                            var description = "";
                            var checked = "";
                        }
                    } else {
                        var description = "";
                        var checked = "";
                    }

                    if (this.type[ID] == "extendet") {
                        input += '<tr>' + betweenRow + '<td><input class="mainTablesEditorCheckboxTitle" style="width: 80%" name="title[' + rowID + '][' + colID + '][' + i + ']" type="text" value="' + description + '"><input type="checkbox" name="value[' + rowID + '][' + colID + '][' + i + ']" ' + checked + ' value="checked"/></td>' + removeRow + '</tr>';
                        i++;
                    } else {
                        input += ''+description+'<input type="checkbox" name="value[' + rowID + '][' + colID + '][' + i + ']" ' + checked + ' value="checked"/><br />';
                        i++;
                    }

                }
                if (this.type[ID] == "extendet") {
                    input += '<tr><td colspan="3" id="mainTablesEditorButton" class="mainTablesEditorCheckboxAddRow">'+this.addBtn+'</td></tr></table>';
                }

                break;

            case 'checkbox_only':

                if(isDefined(object)) {
                    if (isObject(object)) {
                        if(object.checked[j]) {
                            var checked = 'checked';
                        } else {
                            var checked = "";
                        }
                    } else {
                        var checked = "";
                    }
                } else {
                    var checked = "";
                }

                if (this.type[ID] == "extendet") {
                    input += '<input type="hidden" name="type[' + rowID + '][' + colID + ']" value="checkbox_only"/>';
                    input += '<input type="checkbox" name="value[' + rowID + '][' + colID + ']" value="checked" ' + checked + '/><br />';
                    i++;
                } else {
                    input += '<input type="checkbox" name="value[' + rowID + '][' + colID + ']" value="checked" ' + checked + '/><br />';
                    i++;
                }

                break;

            case 'radio':

                var betweenRow = '<td id="mainTablesEditorButton" class="mainTablesEditorRadioBetweenRow">'+this.addBetweenBtn+'</td>';
                var removeRow = '<td id="mainTablesEditorButton" class="mainTablesEditorRadioRemoveRow">'+this.removeBtn+'</td>';
                var addRow = '<td id="mainTablesEditorButton" colspan="3" class="mainTablesEditorRadioAddRow">'+this.addBtn+'</td>';

                if (this.type[ID] == "extendet") {
                    input += '<input type="hidden" name="type[' + rowID + '][' + colID + ']" value="radio"/><table>';
                }

                for(var j=0;j<num;j++) {
                    if (isDefined(object)) {
                        if (isObject(object)) {
                            var description = object.texts[j];
                        } else {
                            var description = "";
                        }
                    } else {
                        var description = "";
                    }

                    if (this.type[ID] == "extendet") {
                        input += '<tr>' + betweenRow + '<td><input class="mainTablesEditorRadioTitle" style="width: 80%" name="title[' + rowID + '][' + colID + '][' + i + ']" type="text" value="' + description + '"><input type="radio" name="value[' + rowID + '][' + colID + ']" value="' + description + '"/></td>' + removeRow + '</tr>';
                        i++;
                    } else {
                        input += '' + description + '<input type="radio" name="value[' + rowID + '][' + colID + ']" value="' + description + '"/><br />';
                        i++;
                    }
                }

                if (this.type[ID] == "extendet") {
                    input += '<tr><td colspan="3" id="mainTablesEditorButton" class="mainTablesEditorRadioAddRow">'+this.addBtn+'</td></tr></table>';
                }

                break;

            case 'select':

                var editorSelectNewRow = '<td id="mainTablesEditorButton" class="mainTablesEditorSelectBetweenRow">'+this.addBetweenBtn+'</td>';
                var editorSelectRemoveRow = '<td id="mainTablesEditorButton" class="mainTablesEditorSelectRemoveRow">'+this.removeBtn+'</td>';
                var editorSelectEdit = '<a id="mainTablesEditorButton" class="mainTablesEditorEditSelect"><img src="'+mainVariables.pathtoimages+'icons/edit/edit.png" alt="Upravit select" title="Upravit select"/></a>';

                if (this.type[ID] == "extendet") {
                    input += '<div class="mainTablesEditorSelectPlaceArea" style="display: none;"><input type="hidden" name="type[' + rowID + '][' + colID + ']" value="select"/><select name="value[' + rowID + '][' + colID + ']"></select> ' + editorSelectEdit + '</div>';
                    i++;
                    input += '<div class="mainTablesEditorSelectObal"><table><tr><td colspan="3"><center>select: <a class="mainTablesEditorUpdateSelect"><img src="' + mainVariables.pathtoimages + 'icons/update/update.png" alt="Aktualizovat select" title="Aktualizovat select" width="20px"/></a></center></td></tr>';

                    for (var j = 0; j < num; j++) {
                        if (isDefined(object)) {
                            if (isObject(object)) {
                                var defValue = object.values[j];
                                if (object.selected[j]) {
                                    var selected = '';
                                } else {
                                    var selected = '';
                                }
                            } else {
                                var defValue = "";
                                var selected = '';
                            }
                        } else {
                            var defValue = "";
                            var selected = '';
                        }

                        input += '<tr>' + editorSelectNewRow + '<td><input type="hidden" name="selected[' + rowID + '][' + colID + '][' + i + ']" value="' + selected + '"/><input type="text" name="option[' + rowID + '][' + colID + '][' + i + ']" value="' + defValue + '"/></td>' + editorSelectRemoveRow + '</tr>';
                        i++;
                    }

                    input += '<tr><td id="mainTablesEditorButton" class="mainTablesEditorSelectAddRow" colspan="3">' + this.addBtn + '</td></tr>';
                    input += '</table></div>';
                } else {

                    input += '<select name="value[' + rowID + '][' + colID + ']">';

                    for (var j = 0; j < num; j++) {
                        if (isDefined(object)) {
                            if (isObject(object)) {
                                var defValue = object.values[j];
                                if (object.selected[j]) {
                                    var selected = 'selected="selected"';
                                } else {
                                    var selected = '';
                                }
                            } else {
                                var defValue = "";
                                var selected = '';
                            }
                        } else {
                            var defValue = "";
                            var selected = '';
                        }

                        input += '<option '+selected+' vlaue="'+defValue+'">'+defValue+'</option>';
                    }

                    input += '</select>';
                }
                break;

            case 'textarea':

                if(isDefined(object)) {
                    if (isObject(object)) {
                        if(object.values != '') {
                            var defValue = object.values;
                        } else {
                            var defValue = object.texts;
                        }
                    } else {
                        var defValue = "";
                    }
                } else {
                    var defValue = "";
                }

                if (this.type[ID] == "extendet") {
                    input += '<input type="hidden" name="type[' + rowID + '][' + colID + ']" value="textarea"/>';
                    input += '<textarea class="mainTablesEditorTextarea" name="value[' + rowID + '][' + colID + ']">' + defValue + '</textarea>';
                    i++;
                } else {
                    input += '<textarea class="mainTablesEditorTextarea" name="value[' + rowID + '][' + colID + ']">' + defValue + '</textarea>';
                }

                break;

        }

        return input;
    },

    buildOptInputs: function(inputs){

        var Output = [];

        for(var i = 0; i < inputs.length; i++) {

            var types = inputs[i].split(",");

            if (types.length == 1) {
                Output.push('<input type="text" value="' + inputs[i] + '" style="display: none;"/><input type="number" value="1" min="1" style="width: 60px;display: none;"/>');
            } else {

                var select = '<select>';

                for (var j = 0; j < types.length; j++) {
                    select = select + '<option value="' + types[j] + '">' + types[j] + '</option>';
                }

                select = select + '</select><input type="number" value="1" min="1" style="width: 60px;"/>';
                Output.push(select);

            }

        }

        return Output;

    },

    buildNewRow: function(ID,cols){

        var addcolumnForAddingRows = "";
        var addcolumnForRemovingRows = "";

        if(this.options[ID][0]){
            addcolumnForAddingRows = '<td id="mainTablesEditorButton" class="mainTablesEditorBetweenRow">'+mainTablesEditor.addBetweenBtn+'</td>';
        }
        if(this.options[ID][1]){
            addcolumnForRemovingRows = '<td id="mainTablesEditorButton" class="mainTablesEditorRemoveRow">'+mainTablesEditor.removeBtn+'</td>';
        }

        var row = '<tr>'+addcolumnForAddingRows;

        for(var i = 0; i < cols.length; i++){

            row += '<td>'+cols[i]+'</td>';

        }
        row += ''+addcolumnForRemovingRows+'</tr>';

        return row;
    },

    buildNewEditor: function(id, type, cols, inputs, betweenBtn, removeBtn, addBtn) {

        $("#" + id + "").attr("id", "mainTableEditor" + this.ID + "");
        $("#mainTableEditor" + this.ID + "").attr("class", "mainTablesEditor");

        if (!isDefined(betweenBtn)) {
            betweenBtn = false;
        }
        if (!isDefined(removeBtn)) {
            removeBtn = false;
        }
        if (!isDefined(addBtn)) {
            addBtn = false;
        }

        this.inputs[this.ID] = inputs;
        this.options[this.ID] = [];
        this.type[this.ID] = type;
        this.lastRow[this.ID] = 1;

        if (type == "extendet") {
            this.cols = 2;
        } else {
            this.cols = cols;
        }

        if (betweenBtn) {
            this.options[this.ID].push(true);
        } else {
            this.options[this.ID].push(false);
        }
        if (removeBtn) {
            this.options[this.ID].push(true);
        } else {
            this.options[this.ID].push(false);
        }
        if (addBtn) {
            this.options[this.ID].push(true);
        } else {
            this.options[this.ID].push(false);
        }

        var reference = {i: 0};

        $("#mainTableEditor" + this.ID + "").find("tr").each(function () {

            var tr = $(this);
            var colId = 0;

            $(this).children().each(function () {

                if(!isDefined($(this).attr("data-edit"))) {

                    function getText(target, removeBreak) {

                        var text;

                        if (target.text() != '') {
                            text = target.html();

                            if (removeBreak) {
                                text = text.split("<br>");

                                for (var i = 0; i < text.length; i++) {
                                    text[i] = text[i].replace(/<.*[/]?>/, "");
                                }
                            } else {
                                text = text.replace(/<.*[/]?>/, "");
                            }
                        } else {
                            text = "";
                        }

                        return text;
                    }

                    var td = $(this);

                    if (td.find("input:visible").length > 0) {

                        var inputs = {
                            count: 0,
                            types: [],
                            values: [],
                            texts: "",
                            checked: []
                        };

                        inputs.texts = getText(td, true);

                        $(this).find("input:visible").each(function () {

                            inputs.count++;

                            if ($(this).attr("type") == "checkbox") {
                                if (inputs.texts != '') {
                                    inputs.types.push($(this).attr("type"));
                                } else {
                                    inputs.types.push("checkbox_only");
                                }
                            } else {
                                inputs.types.push($(this).attr("type"));
                            }

                            inputs.values.push($(this).val());
                            if (this.checked) {
                                inputs.checked.push(true);
                            } else {
                                inputs.checked.push(false);
                            }

                        });

                        if (inputs.count > 0) {
                            var data = mainTablesEditor.checkInputTypes(mainTablesEditor.ID, inputs.types[0], inputs.count, mainTablesEditor.lastRow[mainTablesEditor.ID], colId, reference, inputs);
                            td.html(data);
                        }
                    }

                    if ($(this).find("textarea").length > 0) {

                        var area = $(this).find("textarea");

                        var textarea = {
                            count: 0,
                            values: "",
                            texts: ""
                        };

                        textarea.texts = getText(td, false);

                        textarea.count++;
                        textarea.values = area.val();

                        var data = mainTablesEditor.checkInputTypes(mainTablesEditor.ID, "textarea", textarea.count, mainTablesEditor.lastRow[mainTablesEditor.ID], colId, reference, textarea);
                        td.html(data);
                    }

                    if ($(this).find("select").children().length > 0) {

                        var options = {

                            count: 0,
                            values: [],
                            selected: []

                        };

                        $(this).find("select").children().each(function () {
                            options.count++;
                            options.values.push($(this).attr("value"));

                            if (isDefined($(this).attr("selected"))) {
                                options.selected.push(true);
                            } else {
                                options.selected.push(false);
                            }
                        });

                        if (options.count > 0) {

                            var data = mainTablesEditor.checkInputTypes(mainTablesEditor.ID, "select", options.count, mainTablesEditor.lastRow[mainTablesEditor.ID], colId, reference, options);
                            td.html(data);

                        }
                    }

                    colId++;
                }
            });

            if (mainTablesEditor.options[mainTablesEditor.ID][0]) {
                tr.prepend('<td id="mainTablesEditorButton" class="mainTablesEditorBetweenRow">' + mainTablesEditor.addBetweenBtn + '</td>');
            }
            if (mainTablesEditor.options[mainTablesEditor.ID][1]) {
                tr.append('<td id="mainTablesEditorButton" class="mainTablesEditorRemoveRow">' + mainTablesEditor.removeBtn + '</td>');
            }

            mainTablesEditor.lastRow[mainTablesEditor.ID]++;

        });

        if(addBtn) {

            var inside = this.buildOptInputs(inputs);
            var col1 = "";
            var col2 = "";

            var count = 0;

            if (betweenBtn) {
                col1 = '<td class="mainTablesEditorAddRow"></td>';
                count++;
            }

            if (removeBtn) {
                col2 = '<td class="mainTablesEditorAddRow"></td>';
                count++;
            }

            var inside = this.buildOptInputs(inputs);
            count = (count + (inside.length));
            var addRow = '<tr>' + col1;

            for (var i = 0; i < inside.length; i++) {
                addRow += '<td class="mainTablesEditorAddRow">' + inside[i] + '</td>';
            }

            addRow += col2 + '</tr>';
            $("#mainTableEditor" + this.ID + "").append(addRow);
        }

        this.ID++;
    }

};

$(document).on("click",".mainTablesEditorAddRow",function(e){

    if (!$(e.target).is("input,select,option")) {

        var ID = mainTablesEditor.getId($(this));

        var columns = [];
        var reference = {td:0,i:0};

        var pattern;

        if(mainTablesEditor.options[ID][0] && mainTablesEditor.options[ID][1]){
            pattern = $(this).closest("tr").find("td").not(":last").not(":first");
        } else if (mainTablesEditor.options[ID][0]){
            pattern = $(this).closest("tr").find("td").not(":first");
        } else if (mainTablesEditor.options[ID][1]) {
            pattern = $(this).closest("tr").find("td").not(":last");
        } else {
            pattern = $(this).closest("tr").find("td");
        }

        pattern.each(function(){

            if($(this).find("input,select").length > 0){

                var type =  $(this).find("input[type=text],select").val();
                var count =  $(this).find("input[type=number]").val();

                columns.push(mainTablesEditor.checkInputTypes(ID,type,count,mainTablesEditor.lastRow[ID],reference.td,reference));
            }
            reference.td++;
        });

        var row = mainTablesEditor.buildNewRow(ID,columns);

        $(row).insertBefore($(this).closest("tr"));

        mainTablesEditor.lastRow[ID]++;
    }

});

$(document).on("click",".mainTablesEditorBetweenRow",function(e){

    var ID = mainTablesEditor.getId($(this));
    var inside = mainTablesEditor.buildOptInputs(mainTablesEditor.inputs[ID]);
    var length = inside.length - 1;

    var content = '<table><tr>';

    for(var i = 0; i <= length; i++){
        content += '<td>'+inside[i]+'</td>';
    }

    content += '</tr><tr><td colspan="'+length+'"><input class="mainTablesEditorAddRowBetweenRows" type="submit" value="Přidat" /></td></tr></table>';

    mainTablesEditor.windowPointer[ID] = mainWindow.normal(ID,"auto",500,"Přidat nový řádek",false,false,content,$(this),e,true);

});

$(document).on("click",".mainTablesEditorAddRowBetweenRows",function(e){
    e.preventDefault();

    var ID = mainWindow.getTargetId($(this));
    var windowBody = mainWindow.moveToBody(mainTablesEditor.windowPointer[ID]);

    var columns = [];
    var reference = {td:0,i:0};

    mainWindow.getTarget($(this),function(pointer){

        var rowNum = mainTablesEditor.getRowId(pointer);

        windowBody.find("table").find("tr").first().find("td").each(function(){

            if($(this).find("input,select").length > 0){

                var type =  $(this).find("input[type=text],select").val();
                var count =  $(this).find("input[type=number]").val();

                columns.push(mainTablesEditor.checkInputTypes(ID,type,count,(rowNum+1),reference.td,reference));
            }
            reference.td++;
        });

        var row = mainTablesEditor.buildNewRow(ID,columns);
        $(row).insertAfter(pointer.closest("tr"));

        var lastId = rowNum+2;

        pointer.closest("table").find("> tbody > tr:gt("+(rowNum)+")").not(":last-child").each(function(){

            $(this).find("input,select,textarea").each(function(){
                mainTablesEditor.updateRowID($(this),lastId);
            });
            lastId++;
        });

        mainTablesEditor.lastRow[ID]++;
    });
});

$(document).on("click",".mainTablesEditorRemoveRow",function(e){

    var ID = mainTablesEditor.getId($(this));
    var rowID = mainTablesEditor.getRowId($(this));
    var lastId = rowID;

    $(this).closest("table").find("> tbody > tr:gt("+(rowID - 1)+")").not(":last-child").each(function(){
        $(this).find("input,select,textarea").each(function(){
            mainTablesEditor.updateRowID($(this),lastId);
        });
        lastId++;
    });

    mainTablesEditor.lastRow[ID]--;

    $(this).closest("tr").remove();
});

/* Options for Editor Select */

$(document).on("click",".mainTablesEditorSelectAddRow",function(e){

    var lastRow = $(this).closest("table").find("tr").last().prev();

    var rowId = mainTablesEditor.getRowId(lastRow);
    var colId = mainTablesEditor.getColId(lastRow);
    var id = (mainTablesEditor.getInputId(lastRow))+1;

    var editorSelectNewRow = '<td id="mainTablesEditorButton" class="mainTablesEditorSelectBetweenRow">'+mainTablesEditor.addBetweenBtn+'</td>';
    var editorSelectRemoveRow = '<td id="mainTablesEditorButton" class="mainTablesEditorSelectRemoveRow">'+mainTablesEditor.removeBtn+'</td>';

    $('<tr>'+editorSelectNewRow+'<td><input type="text" name="option['+rowId+']['+colId+']['+id+']"/></td>'+editorSelectRemoveRow+'</tr>').insertBefore($(this).closest('tr'));
});

$(document).on("click",".mainTablesEditorSelectBetweenRow",function(e){

    var editorSelectNewRow = '<td id="mainTablesEditorButton" class="mainTablesEditorSelectBetweenRow">'+mainTablesEditor.addBetweenBtn+'</td>';
    var editorSelectRemoveRow = '<td id="mainTablesEditorButton" class="mainTablesEditorSelectRemoveRow">'+mainTablesEditor.removeBtn+'</td>';

    var rowId = mainTablesEditor.getRowId($(this));
    var colId = mainTablesEditor.getColId($(this).closest("tr"));
    var id = (mainTablesEditor.getInputId($(this).closest("tr")))+1;

    $('<tr>'+editorSelectNewRow+'<td><input type="text" name="option['+rowId+']['+colId+']['+id+']"/></td>'+editorSelectRemoveRow+'</tr>').insertAfter($(this).closest('tr'));

    var lastId = id + 1;

    $(this).closest("table").find("tr:gt("+id+")").not(":last").each(function(){

        $(this).find("input,select,textarea").each(function(){
            mainTablesEditor.updateInputNum($(this),lastId);
        });
        lastId++;
    });
});

$(document).on("click",".mainTablesEditorSelectRemoveRow",function(e){

    var id = mainTablesEditor.getInputId($(this).closest("tr"));

    $(this).closest("table").find("tr:gt("+id+")").not(":last").each(function(){

        $(this).find("input,select,textarea").each(function(){
            mainTablesEditor.updateInputNum($(this),id);
        });
        id++;
    });

    $(this).closest("tr").remove();
});

$(document).on("click",".mainTablesEditorUpdateSelect",function(e){

    e.preventDefault();

    var options = '';
    var numberOfInputs = $(this).closest("div").find("input").length;
    var numberofFullInputs = 0;

    $(this).closest("div").find("input").each(function(){
        var inputVal = $(this).val();
        if(inputVal != ''){
            options += '<option value="'+inputVal+'">'+inputVal+'</option>';
            numberofFullInputs++;
        }
    });

    if(numberofFullInputs > 0)
    {
        $(".mainTablesEditorSelectPlaceArea").find("select").html(options);
        $(".mainTablesEditorSelectObal").hide();
        $(".mainTablesEditorSelectPlaceArea").show();
    } else {
        alert("Všechny volby jsou prázdné!");
    }
});

$(document).on("click",".mainTablesEditorEditSelect",function(e){

    e.preventDefault();

    $(".mainTablesEditorSelectObal").show();
    $(".mainTablesEditorSelectPlaceArea").hide();
});

$(document).on("click",".mainTablesEditorCheckboxAddRow",function(e){

    var lastRow = $(this).closest("table").find("tr").last().prev();

    var rowID = mainTablesEditor.getRowId(lastRow);
    var colID = mainTablesEditor.getColId(lastRow);
    var id = (mainTablesEditor.getInputId(lastRow))+1;

    var betweenRow = '<td id="mainTablesEditorButton" class="mainTablesEditorCheckboxBetweenRow">'+mainTablesEditor.addBetweenBtn+'</td>';
    var removeRow = '<td id="mainTablesEditorButton" class="mainTablesEditorCheckboxRemoveRow">'+mainTablesEditor.removeBtn+'</td>';

    $('<tr>'+betweenRow+'<td><input class="mainTablesEditorCheckboxTitle" style="width: 80%" name="title['+rowID+']['+colID+']['+id+']" type="text"><input type="checkbox" name="value['+rowID+']['+colID+']['+id+']" value="checked"/></td>'+removeRow+'</tr>').insertBefore($(this).closest('tr'));
});

$(document).on("click",".mainTablesEditorCheckboxBetweenRow",function(e){

    var betweenRow = '<td id="mainTablesEditorButton" class="mainTablesEditorCheckboxBetweenRow">'+mainTablesEditor.addBetweenBtn+'</td>';
    var removeRow = '<td id="mainTablesEditorButton" class="mainTablesEditorCheckboxRemoveRow">'+mainTablesEditor.removeBtn+'</td>';

    var rowID = mainTablesEditor.getRowId($(this));
    var colID = mainTablesEditor.getColId($(this).closest("tr"));
    var id = (mainTablesEditor.getInputId($(this).closest("tr")))+1;

    $('<tr>'+betweenRow+'<td><input class="mainTablesEditorCheckboxTitle" style="width: 80%" name="title['+rowID+']['+colID+']['+id+']" type="text"><input type="checkbox" name="value['+rowID+']['+colID+']['+id+']" value="checked"/></td>'+removeRow+'</tr>').insertAfter($(this).closest('tr'));

    var lastId = id + 1;

    $(this).closest("table").find("tr:gt("+id+")").not(":last").each(function(){

        $(this).find("input").each(function(){
            mainTablesEditor.updateInputNum($(this),lastId);
        });
        lastId++;
    });
});

$(document).on("click",".mainTablesEditorCheckboxRemoveRow",function(e){

    var id = mainTablesEditor.getInputId($(this).closest("tr"));

    $(this).closest("table").find("tr:gt("+id+")").not(":last").each(function(){

        $(this).find("input").each(function(){
            mainTablesEditor.updateInputNum($(this),id);
        });
        id++;
    });

    $(this).closest("tr").remove();
});

$(document).on("click",".mainTablesEditorRadioAddRow",function(e){

    var lastRow = $(this).closest("table").find("tr").last().prev();

    var rowID = mainTablesEditor.getRowId(lastRow);
    var colID = mainTablesEditor.getColId(lastRow);
    var id = mainTablesEditor.getInputId(lastRow);

    var betweenRow = '<td id="mainTablesEditorButton" class="mainTablesEditorRadioBetweenRow">'+mainTablesEditor.addBetweenBtn+'</td>';
    var removeRow = '<td id="mainTablesEditorButton" class="mainTablesEditorRadioRemoveRow">'+mainTablesEditor.removeBtn+'</td>';

    $('<tr>'+betweenRow+'<td><input class="mainTablesEditorRadioTitle" style="width: 80%" name="title['+rowID+']['+colID+']['+(id+1)+']" type="text"><input type="radio" name="value['+rowID+']['+colID+']"/></td>'+removeRow+'</tr>').insertBefore($(this).closest('tr'));
});

$(document).on("click",".mainTablesEditorRadioBetweenRow",function(e){

    var betweenRow = '<td id="mainTablesEditorButton" class="mainTablesEditorRadioBetweenRow">'+mainTablesEditor.addBetweenBtn+'</td>';
    var removeRow = '<td id="mainTablesEditorButton" class="mainTablesEditorRadioRemoveRow">'+mainTablesEditor.removeBtn+'</td>';

    var rowID = mainTablesEditor.getRowId($(this));
    var colID = mainTablesEditor.getColId($(this).closest("tr"));
    var id = mainTablesEditor.getInputId($(this).closest("tr"));

    $('<tr>'+betweenRow+'<td><input class="mainTablesEditorRadioTitle" style="width: 80%" name="title['+rowID+']['+colID+']['+(id+1)+']" type="text"><input type="radio" name="value['+rowID+']['+colID+']"/></td>'+removeRow+'</tr>').insertAfter($(this).closest('tr'));

    var lastId = id + 2;

    $(this).closest("table").find("tr:gt("+(id+1)+")").not(":last").each(function(){

        $(this).find("input[type=text]").each(function(){
            mainTablesEditor.updateInputNum($(this),lastId);
        });
        lastId++;
    });
});

$(document).on("click",".mainTablesEditorRadioRemoveRow",function(e){

    var id = mainTablesEditor.getInputId($(this).closest("tr"));

    $(this).closest("table").find("tr:gt("+id+")").not(":last").each(function(){

        $(this).find("input[type=text],select,textarea").each(function(){
            mainTablesEditor.updateInputNum($(this),id);
        });
        id++;
    });

    $(this).closest("tr").remove();
});

$(document).on("change",".mainTablesEditorRadioTitle",function(e){

    var value = $(this).val();
    $(this).closest("tr").find("input[type=radio]").val(value);
});

$(document).on("click","#permitionsFormSubmit",function(e){

    sendData("#permitionsForm","","","POST","","","",function(data){
        alert(data);
    });
});
