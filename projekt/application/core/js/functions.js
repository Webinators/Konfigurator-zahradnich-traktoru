
/*
 Titulek: Sada užitečných funkcí a objektů
 Autor: Radim Vidlák
 */

/*--------------------------------------------------------------------------------
 ---------------------- Funkce pro práci s datovými typy --------------------------
 ---------------------------------------------------------------------------------*/

// Funkce, která zjistí, jestli proměnná nabývá nějaké hodnoty
function isDefined(prom)
{
    if ((prom !== undefined) && (typeof prom !== typeof undefined)) {
        return true;
    }
    return false;
}

// Funkce, která zjistí, jestli se jedná o datový typ objekt
function isObject(prom)
{
    if(isDefined(prom)) {
        if (typeof prom === 'object') {
            return true;
        }
    }
    return false;
}

// Funkce, která zjistí, jestli se jedná o datový typ boolean
function isBoolean(prom)
{
    if(isDefined(prom)) {
        if (typeof prom === "boolean") {
            return true;
        }
    }
    return false;
}

// Funkce, která zjistí, jestli je proměnná funkce
function isFunction(prom){

    if(isDefined(prom)) {
        if (typeof prom === 'function') {
            return true;
        }
    }
    return false;
}

/*--------------------------------------------------------------------------------
 ---------------------- Funkce pro práci se stringy a čísly-----------------------
 ---------------------------------------------------------------------------------*/

// Funkce, která vygeneruje unikátní ID stejně jako uniqid v php
function guid() {
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
    }
    return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
        s4() + '-' + s4() + s4() + s4();
}

// Funkce, která v řetězci nahradí určitou část podle regulárního výrazu na jinou na určité pozici
function replaceIndex(string, pattern, repl, at) {

    var nth = 0;
    var reg = new RegExp(pattern,"g");

    string = string.replace(reg, function (match, i, original) {
        nth++;
        return (nth === at) ? repl : match;
    });

    return string;
}

// Funkce, která nahradí všechny nalezené části v řetězci
function replaceAll(find, replace, str) {
    return str.replace(new RegExp(find, 'g'), replace);
}

// funkce, která vrátí náhodné číslo v zadaném rozsahu, ošetřeno opakování

var lastGenNum = 0;

function getRandomInt(min, max) {

    var generated = Math.floor(Math.random() * (max - min + 1)) + min;

    while(lastGenNum == generated){
        generated = Math.floor(Math.random() * (max - min + 1)) + min;
    }

    lastGenNum = generated;
    return generated;
}


/*--------------------------------------------------------------------------------
 -------------------------- Funkce na HTML tag object na SVG ----------------------
 ---------------------------------------------------------------------------------*/

// Funkce, která překryje HTML tag object divem, aby se na něj dalo kliknout přes tag A
function makeSVGOverlay(classN,object) {

    if (object.position() == '') {
        object.css({"position": "relative"});
    }

    d = document.createElement('div');
    $(d).attr("class",classN)
        .width("100%")
        .height("100%")
        .css({

            "position": "absolute",
            "left": 0,
            "top": 0

        })
        .appendTo(object);
}

function imageExists(url, callback){

    $.get(url).done(function() {
        callback(true);
    }).fail(function() {
        callback(false);
    });

}



/*--------------------------------------------------------------------------------
 -------------------------- Funkce na získání výšek ------------------------------
 ---------------------------------------------------------------------------------*/

// Funkce, která získá maximální výšku těla stránky
function getDocHeight() {
    var D = document;
    return Math.max(
        D.body.scrollHeight, D.documentElement.scrollHeight,
        D.body.offsetHeight, D.documentElement.offsetHeight,
        D.body.clientHeight, D.documentElement.clientHeight
    );
}

// Funkce, které vrátí skutečnou výšku elementu (DIV)
function getContainerHeight(selector) {
    var total = 0;
    $(selector).children().each(function() {
        total += $(this).outerHeight();
    });
    return total;
}


/*--------------------------------------------------------------------------------
 --------------------------------- JQUERY funkce ---------------------------------
 ---------------------------------------------------------------------------------*/


(function($) {

    // Funkce, která umožní posouvání elementů na stránce

    $.fn.drags = function(opt,disabled) {

        opt = $.extend({handle:"",cursor:"move"}, opt);

        if(opt.handle === "") {
            var $el = this;
        } else {
            var $el = this.find(opt.handle);
        }

        $el.css('cursor', opt.cursor);

        $.each(disabled, function( key, value ) {
            value.css({'cursor':"auto"});
        });

        return $el.on("mousedown", function(e) {

            var disable = false;

            var target = $(e.target);

            $.each(disabled, function( key, value ) {

                if(target.closest(value).length){
                    disable = true;
                }

            });

            if(!disable) {

                if (opt.handle === "") {
                    var $drag = $(this).addClass('draggable');
                } else {
                    var $drag = $(this).addClass('active-handle').parent().addClass('draggable');
                }

                var drg_h = $drag.outerHeight(),
                    drg_w = $drag.outerWidth(),
                    pos_y = $drag.offset().top + drg_h - e.pageY,
                    pos_x = $drag.offset().left + drg_w - e.pageX;
                $drag.parents().on("mousemove", function (e) {
                    $('.draggable').offset({
                        top: e.pageY + pos_y - drg_h,
                        left: e.pageX + pos_x - drg_w
                    }).on("mouseup", function () {
                        $(this).removeClass('draggable');
                    });
                });
            }

        }).on("mouseup", function() {
            if(opt.handle === "") {
                $(this).removeClass('draggable');
            } else {
                $(this).removeClass('active-handle').parent().removeClass('draggable');
            }
        });

    };

    // Funkce, která vystředí element vůči rozměrům jeho rodiče

    $.fn.center = function (parent) {

        if(isObject(parent)){

            this.css("position", "absolute");
            this.css("top", (getContainerHeight(parent)/2 - getContainerHeight(this)/2)  + "px");
            this.css("left", (parent.outerWidth()/2 - this.outerWidth()/2) + "px");

        } else {

            var top = $(window).height()/2 - getContainerHeight(this)/2;
            var left = $(window).width()/2 - this.outerWidth()/2;

            this.css("position", "fixed");
            this.css("top", top  + "px");
            this.css("left", left + "px");

            if((top + getContainerHeight(this)) > getContainerHeight()){
                this.css("position", "absolute");
            }

        }

        return this;
    };

    // Funkce, která počká na načtení všech obrázků a pak vrátí callback (důležitá fce pro správnou funkci progressEvent a mainWindow)

    $.fn.ensureLoad = function(obj, handler) {

        return this.each(function() {

            if(this.complete) {
                handler.call(this);
            } else {
                $(this).load(handler).error(handler);
            }

        });

    };

    $.fn.hasAttr = function(name) {

        var attr = this.attr(name);

        if (typeof attr !== typeof undefined && attr !== false) {
            return true;
        }

        return false;
    };

})(jQuery);


/*--------------------------------------------------------------------------------
 ----------------------------- Nadefinované objekty ------------------------------
 ---------------------------------------------------------------------------------*/


// objekt, který umožní vložit odkaz na nový js nebo css soubor do hlavičky stránky

var includeOnce = {

    jsPaths: [],
    cssPaths: [],

    inArray: function(url, type){

        for(var i = 0; i < type.length; i++){

            if(type[i] == url){
                return true;
            }

        }

        return false;
    },

    js: function(url){

        if(!this.inArray(url, this.jsPaths)){

            $("head").append('<script type="text/javascript" src="' + url + '"></script>');

            this.jsPaths.push(url);

        }

    },

    css: function(url){

        if(!this.inArray(url, this.cssPaths)) {

            $('<link>')
                .appendTo('head')
                .attr({type: 'text/css', rel: 'stylesheet'})
                .attr('href', url);

            this.cssPaths.push(url);
        }
    }

};

// objekt, který zahrnuje základní ikony a umožní je vložit na dané místo bez znalosti cesty k nim (!!! není kompletní ani testovaná)


function Icons_f() {

    var icons = [];
    var titles = [];

    this.insert = function(dest, type){

        if(titles.indexOf(type) > -1){
            dest.html(icons[titles.indexOf(type)]);
        } else {
            console.log("Taková ikona neexistuje");
        }

    };

    this.addIcon = function(icon, title){
        icons.push(icon);
        titles.push(title);
    };
}

var Icons = new Icons_f();


/* Progress  */

// Buďto se zobrazí přes celou obrazovku:   var progress = mainProgressEvent.insert()
// a nebo v nějakém elementu:               var progress = mainProgressEvent.insertInto(elem)
// ruší se pomocí:                          mainProgressEvent.remove(progress)

function mainProgressEvent_f(){

    var ID = 0;

    function center(obj) {

        var IMG = obj.find("img");

        if(IMG.length > 0){

            var loaded = 0;
            var numImages = IMG.length;

            IMG.ensureLoad(function () {
                ++loaded;
                if (loaded === numImages) {
                    obj.center(obj.parent());
                }
            });

        } else {
            obj.center(obj.parent());
        }
    }

    function moveToBody(obj){
        return obj.find("#mainProgressEventBody");
    }

    this.insert = function(){

        var content = '<div class="mainProgressEventT" id="mainProgressEvent'+ID+'">' +
            '<div id="mainProgressEventBody">Prosím čekejte...<br />' +
            '<img src="'+mainVariables.pathtoimages+'icons/progress/mainprogress.gif" alt="progress" title="progress"/>' +
            '</div></div>';

        $("body").append(content);
        var container = $("#mainProgressEvent"+ID+"");
        moveToBody(container).center();

        ID++;
        return container;
    };

    this.insertInto = function(target,pointer,transparent){

        if(isObject(target)) {

            if (!isDefined(transparent)) {
                transparent = true;
            }
            if (!isDefined(pointer)) {
                pointer = false;
            }

            if (transparent) {
                var id = "mainProgressEventT";
                var img = "mainprogress2B.gif";
            } else {
                var id = "mainProgressEvent";
                var img = "mainprogress2B.gif";
            }

            var content = '<div class="' + id + ' flexElem" id="mainProgressEvent' + ID + '" style="position: absolute; top: 0px; left 0px; width: 100%; height: 100%;">' +
                '<div id="mainProgressEventBody" class="flexElem valignCenter alignElemsCenter">' +
                '<img src="' + mainVariables.pathtoimages + 'icons/progress/' + img + '" alt="progress" title="progress"/>' +
                '</div></div>';

            target.append(content);
            var container = $("#mainProgressEvent" + ID + "");

            moveToBody(container).center(container.parent()).css({top: 0});

            ID++;

            if (pointer) {
                return container;
            }
        }

    };

    this.remove = function(target){
        target.remove();
    }

}

var mainProgressEvent = new mainProgressEvent_f();


/* Vyskakovací smart modální okna

 Použití:

 metody: mainWindow.

 moveToContainer() - vrátí ukazatel na kontejner okna
 moveToBody() - vrátí ukazatel do obsahové části
 getWindowId() - vrátí id okna
 refresh() - refreshne se okno, metoda určená převážně k událostem okna

 changeDetect() - detekuje změnu form elementu, volá se v předdefinovaných událostech, pokud je enabled
 changesDetectEnabled() - vrátí, jestli je povoleno hlídání změn form elementů v okně
 changesDetected() - vrátí jestli byly provedeny v okně nějaké změny, metoda se volá při zavírání okna automaticky

 normal(paramsObj, callback) - zobrazí klasické okno, callback vrací ukzatel na něj
 confirm(buttonPointer, title, event, callback) - zobrazí potvrzovací okno, callback vrací true-ano nebo false-ne
 alert(paramsObj, callback) - okno jako js alert, callback se vrací po zavření okna

 update(data) - změní obsahovou část okna
 getButtonpointer() - vrátí ukazatel na tlačítko, ze kterého se zavolala událost na otevření okna
 remove(window) - zavře okno (defaltně je definovaná událost na kliknutí na křížek, ale někdy je třeba zavřít přes ukazatele)
 isWindow(window) - zjistí jestli předaný ukazatel je okno


 Parametry window normal:

 {
 buttonPointer: $(elem), - tlačítko, které vyvolalo událost na otevření okna, výhoda nedá se stejné okno otevřít stejným tlačítekm vícekrát
 changesDetect: bool default: false, - povolí, zakáže detekci změn v okně
 center: bool default: false, - vycenteruje horizontálně a vertikálně okno
 event: event, - událost
 bar: -
 {
 help: string, - nápověda v okně
 options: string, - okno má připravenou lištu pro tlačítka na editaci
 title: string - název okna
 }
 content: string - obsah okna,
 width: string,
 height: string,
 effect: bool default: false,
 afterClose: function() - co se má provést po zavření okna,
 fixed: bool default: false - jestli má okno být fixní při scrollování
 }

 Parametry window alert:

 {
 buttonPointer: $(elem),
 event: event,
 content: string
 }

 */

function mainWindow_f() {

    var options = [];
    var ID = 0;
    var clicked = [];

    var self = this;

    function inArray(button) {

        var founded = false;

        if (isDefined(button)) {

            for (var i = 0; i < clicked.length; i++) {

                if(isDefined(clicked[i])) {
                    if (clicked[i].is(button)) {
                        founded = true;
                    }
                }

            }
        }
        return founded;
    }

    function _positionWindow(container, object, id, callback) {

        var viewportWidth = $(window).width();
        var viewportHeight = $(window).height();

        var documentHeight = getDocHeight();
        var documentWidth = $("body").width();

        var ContainerHeight = getContainerHeight(container);
        var ContainerWidth = container.width();

        var viewPortTop = $(document).scrollTop();
        var viewPortLeft = $(document).scrollLeft();

        var newPosX;
        var newPosY;
        var position = "absolute";

        if ((container.attr("data-parent-window") != '' && options[id].insertIntoParent) && !options[id].dialog) {

            var parent = self.getParentContainer(container);

            var parentTop = parent.offset().top;
            var parentLeft = parent.offset().left;

            var parentH = getContainerHeight(self.moveToBody(parent).parent());
            var parentW = self.moveToBody(parent).width();
            var parentScroll = self.moveToBody(parent).parent().scrollTop();

            newPosX = object.pageX - parentLeft;
            newPosY = object.pageY - parentTop;

            if(options[id].centered) {

                if (viewportHeight < parentH) {

                    if (parentTop < viewPortTop) {

                        if ((parentTop + parentH) > (viewPortTop + viewportHeight)) {
                            newPosY = parentScroll + (viewPortTop - parentTop) + (viewportHeight / 2) - (ContainerHeight / 2);
                        } else {
                            var visible = viewportHeight - ((viewPortTop + viewportHeight) - (parentTop + parentH));
                            newPosY = parentScroll + viewPortTop - parentTop + visible / 2 - (ContainerHeight / 2);
                        }

                    } else {

                        newPosY = parentScroll + (viewPortTop - parentTop) + ((viewPortTop + viewportHeight - parentTop) / 2) - (ContainerHeight / 2);

                        if(newPosY < parentTop){
                            newPosY = parentTop;
                        }

                        if((newPosY + ContainerHeight) > (parentTop + parentH)){
                            newPosY = parentTop + parentH - 10;
                        }

                    }

                } else {
                    newPosY = (parentH / 2) - (ContainerHeight / 2);
                }

                newPosX = (parentW / 2) - (ContainerWidth / 2);

            } else {

                if ((newPosY + ContainerHeight) >= (parentH)) {

                    newPosY = parentH - (ContainerHeight + 40);

                    if (newPosY < 0) {
                        newPosY = 30;
                    }
                }

                if ((newPosX + ContainerWidth) > parentW) {
                    newPosX = parentW - (ContainerWidth + 50);
                }

            }

        } else {

            if (!options[id].centered) {

                if(container.css("left") != "auto"){
                    newPosX = parseInt(container.css("left"));
                } else {
                    newPosX = object.pageX;
                }

                if(container.css("top") != "auto"){
                    newPosY = parseInt(container.css("top"));
                } else {
                    newPosY = object.pageY;
                }

                if(options[id].fixed) {
                    if (screen.width >= 700) {

                        if ((newPosY + ContainerHeight) > (viewPortTop + viewportHeight)) {
                            newPosY = (viewPortTop + viewportHeight) - ContainerHeight - 50;
                        }

                        if ((newPosY + ContainerHeight) > documentHeight) {
                            newPosY = (documentHeight - 50) - ContainerHeight;
                        }


                        newPosY = viewPortTop + (newPosY - options[id].lastTop);


                        if ((newPosX + ContainerWidth) > (viewportWidth)) {
                            newPosX = viewportWidth - ContainerWidth - 70;
                        }

                    }  }

            }

            if (options[id].centered) {

                if (ContainerHeight > viewportHeight) {

                    if (container.css("left") != 'auto' && container.css("top") != 'auto') {

                        var top = parseInt(container.css("top"));
                        var left = parseInt(container.css("left"));

                        if ((viewPortTop + viewportHeight) < (top + ContainerHeight)) {

                            if (viewPortTop + 50 < top) {
                                newPosY = viewPortTop + 50;
                            } else {
                                newPosY = top;
                            }

                        } else {

                            if(viewPortTop < options[id].lastTop){
                                newPosY = viewPortTop + 50;
                            } else {
                                newPosY = (viewPortTop + viewportHeight - 30) - ContainerHeight;
                            }

                        }

                        if ((newPosY + ContainerHeight) > documentHeight) {
                            newPosY = (documentHeight - 30) - ContainerHeight;
                        }


                    } else {
                        newPosY = viewPortTop + 50;
                    }

                } else {
                    newPosY = viewPortTop + (viewportHeight / 2) - (ContainerHeight / 2);
                }

                newPosX = viewPortLeft + (viewportWidth / 2) - (ContainerWidth / 2);
            }


        }

        options[id].lastTop = viewPortTop;
        container.css({position: position, left: newPosX, top: newPosY});

        callback();
    }

    function positionWindow(container, object, id, callback) {

        if (screen.width < 700) {
            options[id].centered = true;
        }

        if (self.moveToBody(container).find("img").length > 0) {

            var loaded = 0;
            var IMG = self.moveToBody(container).find("img");
            var numImages = IMG.length;

            var obj = {processed: 0};

            IMG.ensureLoad(obj, function () {
                ++loaded;
                if (loaded === numImages) {
                    _positionWindow(container, object, id, function(){
                        callback();
                    });
                }
            });

        } else {
            _positionWindow(container, object, id, function(){
                callback();
            });
        }
    }

    /**
     * Metoda, která vrátí rodičovské okno aktuálního okna
     *
     * @param target - jakýkoliv element, který se nachází v mainWindowContainer
     * @returns {object|bool}
     */
    this.getParentContainer = function(target){

        target = self.moveToContainer(target);

        if(target.closest(".mainWindowContainer").length > 0){
            return target.parent().closest(".mainWindowContainer");
        } else {
            return false;
        }

    };

    /**
     * Metoda, která vrátí obal aktuálního okna
     *
     * @param target - jakýkoliv element, který se nachází v mainWindowContainer
     * @returns {object|bool}
     */
    this.moveToContainer = function(target) {

        if(target.hasClass("mainWindowContainer")){
            return target;
        } else {

            if(target.closest(".mainWindowContainer").length > 0) {
                return target.closest(".mainWindowContainer");
            } else {
                return false;
            }

        }

    };

    /**
     * Metoda, která vrátí id okna
     *
     * @param Button - jakýkoliv element, který se nachází v mainWindowContainer
     * @returns {int}
     */
    this.getWindowId = function(Button) {
        var target = self.moveToContainer(Button);
        var id = target.attr("id").match(/\d+/)[0];
        return id;
    };

    /**
     * Metoda, která vrátí obsahovou část aktuálního okna
     *
     * @param from - jakýkoliv element, který se nachází v mainWindowContainer
     * @returns {Object|bool}
     */
    this.moveToBody = function(from) {

        var target = self.moveToContainer(from);
        target = target.find(".mainWindowBody").find(".mainWindowEditorBody").first();
        return target;
    };

    function setWidth(obj, width, id, orientation) {

        var objW = obj.width();
        var viewPortW;

        if ((obj.attr("data-parent-window") && options[id].insertIntoParent) && !options[id].dialog) {
            viewPortW = self.getParentContainer(self.moveToContainer(obj)).width();
        } else {
            if (isDefined(orientation)) {
                if (orientation == 90 || orientation == -90) {
                    viewPortW = $(window).width();
                } else {
                    viewPortW = $(window).height();
                }
            } else {
                viewPortW = $(window).width();
            }
        }

        var maxWidth;
        var viewPortLeft = $(document).scrollLeft();

        var percent = 80;

        if (!isDefined(orientation) && viewPortW > 700) {

            if (options[id].centered) {

                maxWidth = ($(window).width() * percent) / 100 + "px";

            } else {

                if (((viewPortLeft + viewPortW) - objW) > 0) {
                    maxWidth = (viewPortLeft + viewPortW) - objW;
                } else {
                    maxWidth = ($(window).width() * percent) / 100 + "px";
                }

            }

            if (isDefined(width)) {

                if (width == "full") {

                    width = (viewPortW * percent) / 100 + "px";

                } else if (width == "auto") {

                    width = "auto";

                } else {

                    var patt = /[0-9]*%/;

                    if (!patt.test(width)) {

                        width = parseInt(width);

                        if (width > maxWidth) {
                            width = maxWidth + "px";
                        } else {
                            width = width + "px";
                        }

                    }
                }

            } else {
                width = "auto";
            }

            if (maxWidth > 1900) {
                maxWidth = 1900 + "px";
            }

        } else {
            maxWidth = (viewPortW * 80) / 100 + "px";
        }

        obj.css({"width": width, "max-width": maxWidth});
    }

    function setHeight(obj, height, id, event) {

        var objH = obj.height();

        var maxHeight;
        var viewPortTop = $(document).scrollTop();
        var viewPortH;

        if ((obj.attr("data-parent-window") && options[id].insertIntoParent) && !options[id].dialog) {
            viewPortH = getContainerHeight(self.moveToBody(self.getParentContainer(self.moveToContainer(obj))).parent());
        } else {
            viewPortH = $(window).height();
        }

        var percent = 90;

        if (options[id].centered) {
            maxHeight = ((getDocHeight() - 30) - (viewPortTop + 30)) + "px";
        } else {

            if (((viewPortTop + viewPortH) - objH) > 0) {
                maxHeight = (viewPortTop + viewPortH) - objH;
            } else {
                maxHeight = (viewPortH * percent) / 100 + "px";
            }
        }

        if (isDefined(height)) {

            if (height == "full") {

                height = (viewPortH * 90) / 100 + "px";

            } else if (height == "auto") {

                height = "auto";

            } else {

                var patt = /[0-9]*%/;

                if (!patt.test(height)) {

                    height = parseInt(height);

                    if (height > maxHeight) {
                        height = maxHeight + "px";
                    } else {
                        height = height + "px";
                    }

                }
            }

        } else {
            height = "auto";
        }

        self.moveToBody(obj).parent().css({"height": height, "max-height": maxHeight});
    }

    function setSublayerDim(parent, id, orientation) {

        if(options[id].dialog) {

            var docW;
            var docH;

            if ((isDefined(parent) && options[id].insertIntoParent) && !options[id].dialog) {

                if (isDefined(orientation) && (orientation == 90 || orientation == -90)) {
                    docW = getContainerHeight(self.moveToBody($("#" + parent.id + "")).parent());
                    docH = self.moveToBody($("#" + parent.id + "")).outerWidth();
                } else {
                    docW = self.moveToBody($("#" + parent.id + "")).outerWidth();
                    docH = getContainerHeight(self.moveToBody($("#" + parent.id + "")).parent());
                }

            } else {

                if (isDefined(orientation) && (orientation == 90 || orientation == -90)) {
                    docW = screen.width;
                    docH = screen.height;
                } else {
                    docW = $(window).width();
                    docH = getDocHeight();
                }
            }

            $("#mainWindowTransparentBackground" + id + "").css({
                "width": "" + docW + "px",
                "height": "" + docH + "px"
            });

        }

    }

    /**
     * Metoda, která přizpůsobuje výšku, šířku a pozici okna
     *
     * @param window - okno, které je třeba obnovit
     * @param orientation - pouze pro mobily, jaká je orientace
     * @param callback - až se vše provede
     */
    this.refresh = function(window, orientation, callback){

        if(!_isWindow(window)){
            window = self.moveToContainer(window);
        }

        var id = self.getWindowId(window);
        var event;

        if(!isDefined(options[id])){
            return;
        }

        if (options[id].centered) {
            event = {};
        } else {
            event = options[id].event;
        }

        var width = undefined, height = undefined;

        if(isDefined(window.attr("data-width-property"))){
            width = window.attr("data-width-property");
        }
        if(isDefined(window.attr("data-height-property"))){
            height = window.attr("data-height-property");
        }

        setWidth(window,width, id, orientation);
        setHeight(window,height, id, event);
        positionWindow(window, event, id, function () {

            if(window.attr("data-parent-window")){
                setSublayerDim({id: window.attr("data-parent-window")},id,orientation);
            } else {
                setSublayerDim(undefined,id,orientation);
            }

            if(isFunction(callback)) {
                callback();
            }

        });
    };

    function setOptions(id){

        options[id] = {

            clicked: false,
            centered: false,
            fixed: false,
            event: {},

            close: true,
            bar: {
                title: "Okno" + id,
                options: undefined,
                help: false
            },

            dialog: true,
            insertIntoParent: true,
            content: "",
            width: undefined,
            height: undefined,

            draggable: true,

            lastTop: 0,
            afterClose: undefined,

            changesDetect: false,
            changeFound: false
        };

    }

    /**
     * Metoda, která nastaví, že byla provedena změna v okně
     *
     * @param windowID - id okna
     */
    this.changeDetect = function(windowID){
        options[windowID].changeFound = true;
    };

    /**
     * Metoda, která zjistí, jestli je povelená detekce změn
     *
     * @param windowID - id okna
     * @returns {boolean}
     */
    this.changesDetectEnabled = function(windowID){
        return options[windowID].changesDetect;
    };

    /**
     * Metoda, která zjistí, jestli byly provedeny změny v okně
     *
     * @param windowID
     * @returns {boolean}
     */
    this.changesDetected = function(windowID){
        return options[windowID].changeFound;
    };

    function _basic(objectData, callback) {

        var parentWindowID = "";

        if (isObject(objectData.buttonPointer)) {

            if(inArray(objectData.buttonPointer)){
                callback(false); return;
            }

            clicked.push(objectData.buttonPointer);

            var parentWindow = self.moveToContainer(objectData.buttonPointer);

            if (parentWindow != false) {
                parentWindowID = parentWindow.attr("id");
            }

        } else {
            clicked.push(undefined);
        }

        setOptions(ID); // set defaults

        if (isBoolean(objectData.changesDetect)) {
            options[ID].changesDetect = objectData.changesDetect;
        }

        if(isObject(objectData.event)){
            options[ID].event = objectData.event;
        }

        if (isBoolean(objectData.center)) {
            options[ID].centered = objectData.center;
        }

        if(isBoolean(objectData.insertIntoParent)){
            options[ID].insertIntoParent = objectData.insertIntoParent;
        }

        if(isBoolean(objectData.close)){
            options[ID].close = objectData.close;
        }

        if(!isDefined(objectData.classes)){
            objectData.classes = {};
        }

        objectData.classes = {
            container: isDefined(objectData.classes.container) ? objectData.classes.container : "",
            bar: isDefined(objectData.classes.bar) ? objectData.classes.bar : "",
            title: isDefined(objectData.classes.title) ? objectData.classes.title : "",
            close: isDefined(objectData.classes.close) ? objectData.classes.close : "",
            body: isDefined(objectData.classes.body) ? objectData.classes.body : "",
            content: isDefined(objectData.classes.content) ? objectData.classes.content : ""
        };

        var closeV = {
            0: '<div id="mainWindowBarClose" class="mainWindowBarClose '+objectData.classes.close+'"><object class="mainWindowBarObj" data="' + mainVariables.pathtoimages + 'icons/close/close.svg" type="image/svg+xml"></object></div>',
            1: '<div id="mainWindowBarClose" class="mainWindowBarCloseDynamic '+objectData.classes.close+'"><object class="mainWindowBarObj" data="' + mainVariables.pathtoimages + 'icons/close/close.svg" type="image/svg+xml"></object></div>'
        };

        var bar = "";
        var close = "";

        if(isObject(objectData.bar)){

            if(options[ID].close){
                close = closeV[0];
            }

            bar += '<div class="mainWindowBar '+objectData.classes.bar+'"><div class="mainWindowBarTitle '+objectData.classes.title+'" style="text-align: center;">'+((isDefined(objectData.bar.title) && objectData.bar.title != "") ? objectData.bar.title : options[ID].bar.title) + '</div>';

            if (isDefined(objectData.bar.help) && objectData.bar.help != '' && objectData.bar.help != false) {
                options[ID].help = objectData.help;
                bar += '<a class="mainWindowBarHelp" id="mainWindowBarHelp' + ID + '"><img src="' + mainVariables.pathtoimages + 'icons/help/help.png" alt="Nápověda" title="Nápověda"/></a>';
            }

            bar += close;
            bar += '</div>';

            if (isDefined(objectData.bar.options) && objectData.bar.options != '' && objectData.bar.options != false) {
                bar += '<div class="mainWindowEditorMenu">' + objectData.options + '</div>';
            }

        } else {

            if(options[ID].close){
                close = closeV[1];
            }

            bar = close;
        }

        if(isBoolean(objectData.dialog)){
            options[ID].dialog = objectData.dialog;
        }

        if(isDefined(objectData.content) && objectData.content != ''){
            options[ID].content = objectData.content;
        }

        var data = (options[ID].dialog ? '<div id="mainWindowTransparentBackground' + ID + '" class="mainWindowTransparentBackground">' : "") +
            '<div class="mainWindowContainer '+objectData.classes.container+'" id="mainWindowContainer' + ID + '" data-arr-pos="' + (clicked.length - 1) + '" data-parent-window="' + parentWindowID + '">' +
            bar +
            '<div id="resizeSenzor' + ID + '"><div class="mainWindowBody '+objectData.classes.body+'"><div class="mainWindowEditorBody '+objectData.classes.content+'">' + options[ID].content + '</div></div></div>' +
            '</div>' +
            (options[ID].dialog ? '</div>' : '');

        if (parentWindowID != "" && options[ID].insertIntoParent && !options[ID].dialog) {

            self.moveToBody($("#" + parentWindowID + "")).append(data);
            setSublayerDim({id: parentWindowID}, ID);

        } else {

            $("body").append(data);
            setSublayerDim(undefined, ID);
        }

        var container = $('#mainWindowContainer' + ID + '');

        if (isDefined(objectData.width) && objectData.width != "") {
            container.attr("data-width-property", objectData.width);
        }
        if (isDefined(objectData.height) && objectData.height != "") {
            container.attr("data-height-property", objectData.width);
        }

        makeSVGOverlay("mainWindowBarObjSvg", container.find("#mainWindowBarClose"));

        self.refresh(container, undefined, function () {

            if (isFunction(objectData.effect)) {
                objectData.effect(container);
            } else {
                container.css({display: "inline-block"});
            }

            if (isFunction(objectData.afterClose)) {
                options[ID].afterClose = objectData.afterClose;
            }

            if(options[ID].close) {

                $(document).on("click", "#" + container.attr("id") + " .mainWindowBarObjSvg", function (e) {

                    var btn = $(this);
                    var win = mainWindow.moveToContainer(btn);
                    var id = mainWindow.getWindowId(win);

                    if (mainWindow.changesDetectEnabled(id)) {

                        if (mainWindow.changesDetected(id)) {

                            mainWindow.confirm(btn, "Byly detekovány změny polí, opravdu chcete zavřít okno?", e, function (result) {

                                if (result) {
                                    mainWindow.remove(btn);
                                }

                            });

                        } else {
                            mainWindow.remove(btn);
                        }
                    } else {
                        mainWindow.remove(btn);
                    }


                });
            }

            self.moveToBody(container).parent().on("scroll", function () {
                $(this).find(".mainWindowContainer").each(function () {
                    self.refresh($(this));
                });
            });

            ID++;

            if (isFunction(callback)) {
                callback(container, ID - 1);
            }

        });

    }


    this.normal = function(objectData, callback) {

        _basic(objectData, function(container, id){

            if(container != false) {

                if(isBoolean(objectData.draggable)){
                    options[id].draggable = objectData.draggable;
                }

                if(!options[id].dialog && options[id].draggable){

                    var disable = [];

                    if(options[id].close) {
                        disable.push(container.find("#mainWindowBarClose"));
                    }

                    disable.push(container.find("#mainWindowBody"));

                    if(isDefined(options[id].bar.options)) {
                        disable.push(container.find("#mainWindowEditorMenu"));
                    }

                    if(options[id].bar.help != false) {
                        disable.push(container.find("#mainWindowBarHelp"));
                    }

                    container.drags("", disable);

                }

                new ResizeSensor($("#resizeSenzor" + id + ""), function (el) {
                    if(isDefined(el)) {
                        mainWindow.refresh(el,undefined);
                    }
                });

                if(isBoolean(objectData.fixed)){
                    options[id].fixed = objectData.fixed;
                }

                if(isFunction(callback)) {
                    callback(container);
                }

            } else {
                if(isFunction(callback)) {
                    callback(false);
                }
            }

        });

    };

    this.confirm = function(buttonPointer, title, event, callback) {

        var properties = {
            bar: {
                title: 'Hlášení programu'
            },
            buttonPointer: buttonPointer,
            center: true,
            event: event,
            close: false,
            content: '<div style="text-align: center;">' + title + '<br /><br /><a id="mainWindowYes">Ano</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="mainWindowNo">Ne</a></div>'
        };

        if (buttonPointer.nodeName == "INPUT" || buttonPointer.nodeName == "BUTTON") {

            var form = $(buttonPointer).closest("form");
            var formData;

            form.submit(function (e) {
                formData = new FormData(this);
                e.preventDefault();
            });

        }

        event.preventDefault();

        _basic(properties, function (container, id) {

            if (container != false) {

                var obj = $("#mainWindowContainer" + id + "");

                if (event.target.nodeName == "INPUT" || event.target.nodeName == "BUTTON") {

                    $("#"+container.attr("id")+" #mainWindowYes").click(function (e) {
                        e.preventDefault();

                        if (e.handled !== true) {
                            mainWindow.remove($(this));
                            e.handled = true;
                            callback(formData);
                        }

                    });

                    $("#"+container.attr("id")+" #mainWindowNo").click(function (e) {
                        e.preventDefault();

                        if (e.handled !== true) {
                            mainWindow.remove($(this));
                            e.handled = true;
                            callback(undefined);
                        }

                    });

                } else {

                    $("#"+container.attr("id")+" #mainWindowYes").click(function (e) {
                        e.preventDefault();

                        if (e.handled !== true) {
                            mainWindow.remove($(this));
                            e.handled = true;
                            callback(true);
                        }

                    });

                    $("#"+container.attr("id")+" #mainWindowNo").click(function (e) {
                        e.preventDefault();

                        if (e.handled !== true) {
                            mainWindow.remove($(this));
                            e.handled = true;
                            callback(false);
                        }

                    });

                }
            }
        });

    };

    this.alert = function(obj, callback) {

        var properties = {
            buttonPointer: obj.buttonPointer,
            center: true,
            event: obj.event,
            close: true,
            content: '<br /><div style="text-align: center;">'+obj.content+'</div><br />'
        };

        if(isDefined(obj.type) && obj.type == "message"){

            if($(document).find("#mainMessagesContainer").length == 0){
                var data = '<div id="mainMessagesContainer"></div>';
                $("body").append(data);
            }

            var parent = "";

            if(isObject(obj.buttonPointer)) {
                parent = mainWindow.moveToContainer(obj.buttonPointer).attr("id");
            }

            var $icon = '<img src="';

            if(obj.content.icon == "success"){
                $icon += mainVariables.pathtoimages + 'icons/validation/right.png';
            } else {
                $icon += mainVariables.pathtoimages + 'icons/validation/wrong.png';
            }

            $icon += '" width="25px"/>';

            var messages = $(document).find("#mainMessagesContainer");
            var $message = $('<div data-parent-window="'+parent+'"><div class="message flexElem valignCenter"><div style="padding-right: 10px;">'+$icon+'</div><div class="flex">'+obj.content.data+'</div></div></div>');

            messages.append($message.fadeIn(500));

            if ($message.attr("data-parent-window")) {
                parent = $(document).find("#" + $message.attr("data-parent-window") + "");
            }

            if (isBoolean(obj.closeParent)) {
                if (obj.closeParent && isObject(parent)) {
                    self.remove(parent);
                }
            }

            setTimeout(function(){

                $message.fadeOut(9000, function(){
                    $(this).remove();
                });

            },500);

            if (isFunction(callback)) {
                callback(true);
            }

        } else {

            _basic(properties, function (container, id) {

                if (container.attr("data-parent-window")) {
                    var parent = $(document).find("#" + container.attr("data-parent-window") + "");
                }

                $(document).on("click", "#" + container.attr("id") + " .mainWindowBarObjSvg", function (e) {

                    if (isBoolean(obj.closeParent)) {
                        if (obj.closeParent && isObject(parent)) {
                            self.remove(parent);
                        }
                    }

                    if (isFunction(callback)) {
                        callback(true);
                    }

                });

                var ContWidth = container.width();
                container.find("#mainWindowBarTitle").css({"width": (ContWidth - 55)});

            });

        }

    };

    this.getHelp = function(button){

        var ID = mainWindow.getWindowId(button);
        return options[ID].help;

    };

    this.update = function(pointer, content){
        if(isObject(pointer)) {
            var body = self.moveToBody(pointer);
            body.html(content);
        }
    };

    this.getButtonPointer = function(window){
        return(clicked[self.moveToContainer(window).attr("data-arr-pos")]);
    };

    this.remove = function(button, effect) {

        var container = self.moveToContainer(button);
        var containerID = self.getWindowId(container);
        var afterClose = options[containerID].afterClose;

        if(container.find("resizeSenzor"+containerID+"").length > 0){
            ResizeSensor.detach(container.find("resizeSenzor"+ID+""));
        }

        container.find(".mainWindowContainer").each(function () {

            var id = self.getWindowId($(this));
            var dialog = options[id].dialog;
            options[id] = undefined;
            clicked[id] = undefined;

            if(dialog) {
                $(this).closest(".mainWindowTransparentBackground").remove();
            } else {
                $(this).remove();
            }

        });

        var dialog = options[containerID].dialog;
        options[containerID] = undefined;
        clicked[containerID] = undefined;

        if(dialog) {
            container.closest(".mainWindowTransparentBackground").remove();
        } else {

            if(isFunction(effect)){
                effect(container);
            } else {
                container.remove();
            }

        }

        if (isFunction(afterClose)) {
            afterClose();
        }

    };

    function _isWindow(el){

        if(isObject(el)) {
            if (el.attr("class") == "mainWindowContainer") {
                return true;
            }
        }
        return false;
    }

    this.isWindow = function(el){
        return _isWindow(el);
    };
}

var mainWindow = new mainWindow_f();

$(window).on("orientationchange", function( event ){

    var orientation = this.orientation;

    $(document).find(".mainWindowContainer").each(function() {
        mainWindow.refresh($(this), orientation);
    });

});

$(document).on("click",".mainWindowBarHelp",function(e){

    mainWindow.normal({

        bar: {
            title: "Nápověda"
        },
        title: "Nápověda",
        event: e,
        content: mainWindow.getHelp($(this)),
        buttonPointer: $(this),
        insertIntoParent: false,
        dialog: false
    });

});


$(window).on('resize', function(){

    var orientation = this.orientation;

    $(document).find(".mainWindowContainer").each(function() {
        mainWindow.refresh($(this), orientation);
    });

});

$(window).scroll(function () {

    var orientation = this.orientation;

    $(document).find(".mainWindowContainer").each(function() {
        mainWindow.refresh($(this), orientation);
    });

});

/*
 $(document).on("change","input,select", function(e){

 var el = $(this);

 if(mainWindow.isWindow(mainWindow.moveToContainer(el))){
 mainWindow.changeDetect(mainWindow.getWindowId(mainWindow.moveToContainer(el)));
 }

 });
 */

$(document).on("keyup","textarea", function(e){

    var el = $(this);

    if(mainWindow.isWindow(mainWindow.moveToContainer(el))){
        mainWindow.changeDetect(mainWindow.getWindowId(mainWindow.moveToContainer(el)));
    }

});

$(document).on("keyup,click","input[type='number']", function(e){

    var el = $(this);

    if(mainWindow.isWindow(mainWindow.moveToContainer(el))){
        mainWindow.changeDetect(mainWindow.getWindowId(mainWindow.moveToContainer(el)));
    }

});



/*--------------------------------------------------------------------------------
 ---------------------------- Objekt pro editor ----------------------------------
 ---------------------------------------------------------------------------------*/


var mainContentEditor = {

    moveToContainer: function(target){

        if(target.closest(".MainContentEditContainer").length > 0){
            target = target.closest(".MainContentEditContainer");
        } else {
            target = false;
        }

        return target;
    },

    moveToBody: function(target){

        target = this.moveToContainer(target);

        if(target != false) {
            return target.find(".MainContentEditBody");
        } else {
            return target;
        }

    }

};



$(document).on("mouseenter",".MainContentEditContainer",function(e){

    e.stopPropagation();

    var el = $(this).find(".mainContentEditorOptionsSlide").first();

    if(el.closest(".MainContentEditContainer").is($(this))){
        el.slideDown();
    }

});

$(document).on("mouseleave",".MainContentEditContainer",function(e){

    e.stopPropagation();

    var el = $(this).find(".mainContentEditorOptionsSlide").first();

    if(el.closest(".MainContentEditContainer").is($(this))){
        el.slideUp();
    }

});

$(document).ready(function(){

    setTimeout(function(){
        $(document).find(".mainContentEditorOptionsSlide").slideUp();
    }, 500);

});




/*--------------------------------------------------------------------------------
 -------------------------- Funkce na validaci -----------------------------------
 ---------------------------------------------------------------------------------*/

/* základní validace formuláře */

var mainValidator_f = function(){

    var self = this;

    var validationImg = '<img src="' + mainVariables.pathtoimages + 'icons/progress/validation.gif" alt="validating" title="validating" width="15px" />';
    var errImg = '<img src="' + mainVariables.pathtoimages + 'icons/validation/wrong.png" alt="wrong" title="wrong" width="15px" />';
    var okImg = '<img src="' + mainVariables.pathtoimages + 'icons/validation/right.png" alt="OK" title="OK" width="15px" />';

    var getOutput = function(elem){

        if(elem.closest(".inputValidator").length == 0){

            if(elem.parent().find(".validatorOutput").length == 0) {
                elem.parent().append('<div class="validatorOutput"></div>');
            }

            elem.wrap('<div class="inputValidator"></div>');
            elem.closest(".inputValidator").append('<div class="inputValidatorProgress flexElem alignElemsCenter valignCenter"></div>');

        }

        while(elem.find(".validatorOutput").length == 0){
            elem = elem.parent();
        }

        var out = elem.find(".validatorOutput");

        if(out.children().length == 0) {
            out.addClass("flexElem").append('<div></div>').append('<div class="flexElem valignCenter"></div>');
        }

        return [elem.find("div").last(),elem.find(".inputValidatorProgress")];

    };

    this.validate = function (elem, callback) {

        var out = getOutput(elem);
        out[1].show();
        out[1].html("<span>kontroluji..."+validationImg+"</span>");

        var result = true;

        setTimeout(function () {

            out[1].hide();
            out[0].show();

            var prom = elem.val();

            if(elem.hasAttr("required")){
                if(prom == ""){
                    out[0].html(""+errImg+"&nbsp;&nbsp;Toto políčko je povinné!");
                    result = false;
                }
            }

            if(elem.hasAttr("pattern")){

                var regE = new RegExp(elem.attr("pattern"));

                if(!regE.test(prom)){
                    out[0].html(""+errImg+"&nbsp;&nbsp;Toto políčko je špatně vyplněné!");
                    result = false;
                }
            }

            if(elem.hasAttr("data-url") && result){

                var postd = {};
                postd[elem.attr("name")] = elem.val();

                sendData({

                    data: postd,
                    url: elem.attr("data-url"),
                    bar: false,
                    alert: false,
                    progress: false

                }, function(data, err){

                    if(data != false){

                        out[0].html(okImg);
                        elem.css({"border-color": "#008000"});
                        callback(false);

                    } else {

                        out[0].html(errImg+"&nbsp;&nbsp;"+err);
                        elem.css({"border-color": "#ff0000"});
                        callback(false);
                    }

                });

            } else {

                if(result){
                    out[0].html(okImg);
                    elem.css({"border-color": "#008000"});
                } else {
                    elem.css({"border-color": "#ff0000"});
                }

                callback(result);

            }

        }, 1000);

    };

    var validateElems = function(el, callback){

        var result = true;
        var len = el.length;

        el.each(function(i) {

            self.validate($(this), function (res) {

                result = result ? res : result;

                if ((i + 1) == len) {
                    callback(result);
                }

            });

        });

    };

    this.validateForm = function (form, callback) {

        if(form.hasClass("formValid")) {

            var inputs = form.find(':input:not(:submit,:button,:image,:hidden,:file), textarea, select');

            if (inputs.length > 0) {
                validateElems(inputs, function (result) {
                    callback(result);
                });
            } else {
                callback(true);
            }

        } else {
            callback(true);
        }
    };

};

var mainValidator = new mainValidator_f();

$(document).on("change", "form.formValid :input:not(:submit,:button,:image,:hidden,:file), form.formValid textarea, form.formValid select" ,function(){
    mainValidator.validate($(this), function(){});
});


/*--------------------------------------------------------------------------------
 -------------------------- Funkce na odesílání dat ------------------------------
 ---------------------------------------------------------------------------------*/

/*
 tato funkce zajišťuje odesílání dat přes ajax jak přes formuláře, tak ručním definováním dat

 Parametry:
 {
 form: object typu form,
 mimeType: enctype - když není definován, použije se defaultní nebo z form,
 method: POST|GET - když je definován form a toto není definováno, doplní se method z formu,
 event: event - velmi důležité při odesílání formuláře a pokud je definován beforesend,

 timeout: ms,
 conType: content-type,
 beforeSend: function() - funkce co se má udělat před odesláním, musí vracet callback(true|false),
 progress: object|string - když je definován object tak se do vloží progress do něj, jinak je celookenní,

 alert: bool default - true - pokud aplikace vrátí chybovou hlášku, automaticky se vyhodí alert okno,

 url: url - kam se mají data odeslat, když je definován form a toto ne, automaticky se použije action,
 data: object - data k odeslání, pokud se nejedná o form, nutno definovat alespoň jednu hodnotu,
 }
 */

function sendData(obj,callback) {

    if (!isDefined(obj.timeout)) {
        obj.timeout = 50000;
    }
    if (!isDefined(obj.conType)) {
        obj.conType = "application/x-www-form-urlencoded";
    }
    if (!isDefined(obj.method)) {
        obj.method = "POST";
    }
    if (!isDefined(obj.beforeSend) && !isFunction(obj.beforeSend)) {
        obj.beforeSend = function (callback) {
            callback(true);
        };
    } else {
        if (!isDefined(obj.event)) {
            console.log("Když používáte beforeSend, musíte přidat také event: událost");
        }
    }
    if (isDefined(obj.progress)) {
        if (!isObject(obj.progress) && obj.progress != false) {
            obj.progress = "window";
        }
    }
    if (isObject(obj.closeWindow)) {
        mainWindow.remove(obj.closeWindow);
    }
    if (!isDefined(obj.alert) && !isBoolean(obj.alert)) {
        obj.alert = true;
    }

    function addFileUploaderData(pointer, formData) {

        if (isDefined(FileUploaderBasic)) {

            var basic = pointer.find(".FileUploaderBasic");

            basic.each(function () {

                var files = FileUploaderBasic.getData($(this));
                var name = $(this).attr("name");

                if (files.length > 1) {
                    for (var i = 0; i < files.length; i++) {
                        formData.append(name + "[]", files[i]);
                    }
                } else {
                    if (files.length == 1) {
                        formData.append(name, files[0]);
                    }
                }
            });
        }

        if (isDefined(FileUploaderExtendet)) {

            var extendet = pointer.find(".FileUploaderExtendet");

            extendet.each(function () {

                var files = FileUploaderExtendet.getData($(this));
                var name = $(this).attr("name");

                if (files.length > 1) {
                    for (var i = 0; i < files.length; i++) {
                        formData.append(name + "[]", files[i]);
                    }
                } else {
                    if (files.length == 1) {
                        formData.append(name, files[0]);
                    }
                }

            });
        }
    }

    function sendFormData(obj, callback) {

        if (!isDefined(obj.elem)) {
            obj.elem = obj.form;
        }

        if (obj.elem.hasAttr("formmethod")) {
            obj.method = obj.elem.attr("formmethod");
        } else {
            if (obj.form.hasAttr("method")) {
                obj.method = obj.form.attr("method").toUpperCase();
            }
        }

        if (obj.elem.hasAttr("formaction")) {
            obj.url = obj.elem.attr("formaction");
        } else {
            if (obj.form.hasAttr("action")) {
                obj.url = obj.form.attr("action");
            }
        }

        if (obj.elem.hasAttr("enctype")) {
            obj.mimeType = obj.elem.attr("enctype");
        } else {
            if (obj.form.hasAttr("enctype")) {
                obj.method = obj.form.attr("enctype").toUpperCase();
            }
        }

        mainValidator.validateForm(obj.form, function(result) {

            if(result) {

                if (isDefined(obj.progress) && obj.progress != false) {
                    if (isObject(obj.progress)) {
                        var progress = mainProgressEvent.insertInto(obj.progress, true, true);
                    } else {
                        var progress = mainProgressEvent.insert();
                    }
                }

                addFileUploaderData(obj.form, obj.formData);

                obj.beforeSend(function (result) {

                    if (result) {

                        $.ajax({
                            url: obj.url,
                            type: obj.method,
                            data: obj.formData,
                            mimeType: obj.mimeType,
                            processData: false,
                            contentType: false,
                            cache: false,
                            timeout: obj.timeout,
                            success: function (data) {

                                if (isDefined(progress)) {
                                    mainProgressEvent.remove(progress);
                                }

                                var pomData = data.split("->");

                                switch (parseInt(pomData[0])) {

                                    case 0:
                                        callback(pomData[1]);
                                        break;
                                    case 1:

                                        if (obj.alert) {

                                            mainWindow.alert({
                                                content: {icon: "error", data: pomData[1]},
                                                type: "message"
                                            }, function () {
                                                callback(false);
                                            });

                                        } else {
                                            callback(false, pomData[1]);
                                        }

                                        break;

                                    default:
                                        callback(data);
                                        break;
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                alert(errorThrown);
                            }
                        });

                    } else {
                        if (isDefined(progress)) {
                            mainProgressEvent.remove(progress);
                        }
                    }

                });

            }

        });

    }

    var elemN = isDefined(obj.elem) ? obj.elem.prop("tagName").toLowerCase() : "";

    if (elemN in {button: 1, input: 1, a: 1, form: 1}) {

        if (elemN == "input") {
            if (obj.elem.attr("type") != "submit" && obj.elem.attr("type") != "button") {
                callback(false);
            }

            if (obj.elem.closest("form").length > 0) {
                obj.form = obj.elem.closest("form");
            } else {
                callback(false);
                return;
            }
        }

    }

    if (isObject(obj.form)) {

        obj.form.attr("novalidate", "novalidate");

        if (!isDefined(obj.formData)) {

            obj.form.submit(function (e) {

                obj.formData = new FormData(this);
                e.preventDefault();
                obj.form.unbind('submit');

                sendFormData(obj, function (a, b) {
                    callback(a, b);
                });

                obj.event.preventDefault();
            });

        } else {

            sendFormData(obj, function (a, b) {
                callback(a, b);
            });

            obj.event.preventDefault();
        }

    } else {

        if(isDefined(obj.event)){obj.event.preventDefault();}

        if (elemN == "a") {

            if (obj.elem.hasAttr("href")) {

                var url = new URL(obj.elem.attr("href"));
                obj.data = url.getParameters();
                obj.url = url.getBaseUrl();
                obj.method = "GET";

            }

        }

        if (!isObject(obj.data)) {
            if (obj.data.length == 0) {
                console.log("žádná data k odeslání");
                callback(false);
                return;
            }
        }

        if (isDefined(obj.progress) && obj.progress != false) {
            if (isObject(obj.progress)) {
                var progress = mainProgressEvent.insertInto(obj.progress, true, true);
            } else {
                var progress = mainProgressEvent.insert();
            }
        }

        obj.beforeSend(function (result) {

            if (result) {

                $.ajax({

                    url: obj.url,
                    type: obj.method,
                    data: obj.data,
                    contentType: obj.conType,
                    timeout: obj.timeout,
                    success: function (data) {

                        if (isDefined(progress)) {
                            mainProgressEvent.remove(progress);
                        }

                        var pomData = data.split("->");

                        switch (parseInt(pomData[0])) {

                            case 0:

                                callback(pomData[1]);

                                break;
                            case 1:

                                if (obj.alert) {

                                    mainWindow.alert({
                                        content: {icon: "error", data: pomData[1]},
                                        type: "message"
                                    }, function () {
                                        callback(false);
                                    });

                                } else {
                                    callback(false, pomData[1]);
                                }

                                break;
                            default:
                                callback(data);
                                break;
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        if (isDefined(progress)) {
                            mainProgressEvent.remove(progress);
                        }
                        alert(errorThrown);
                    }

                });

            }
        });


    }

}

$(document).on("click",".ajaxWin",function(e){

    var btn = $(this);

    sendData({

        elem: btn,
        event: e,
        bar: true,
        progress: "window"

    }, function(data){

        mainWindow.normal({

            center: true,
            bar:
            {
                title: btn.hasAttr("data-win-title") ? btn.attr("data-win-title") : undefined
            },
            close: true,
            content: data,
            buttonPointer: btn,
            event: e

        });

    });

});

$(document).on("click",".ajax",function(e){

    var btn = $(this);

    sendData({

        elem: btn,
        progress: "window",
        event: e

    }, function (data) {

        if(data != false){

            mainWindow.alert({
                buttonPointer: btn,
                content: {icon: "success", data: (btn.hasAttr("data-win-message") ? btn.attr("data-win-message") : "Data úspěšně odeslána")},
                closeParent: btn.hasAttr("data-win-closeParent"),
                type: "message"
            }, function(){

            });
        }

    });

});

$(document).on("click",".ajaxAdd",function(e){

    var btn = $(this);

    sendData({

        elem: btn,
        progress: "window",
        event: e

    }, function (data) {

        if(data != false){

            var params = undefined;

            if(btn.hasAttr("data-destination")) {
                params = btn.attr("data-destination").split(",");
            }

            mainWindow.alert({
                buttonPointer: btn,
                content: {icon: "success", data: (btn.hasAttr("data-win-message") ? btn.attr("data-win-message") : "Záznam úspěšně Přidán")},
                closeParent: !!(btn.hasAttr("data-win-closeparent") ? btn.attr("data-win-closeparent") : false),
                type: "message"
            }, function(){

                if(isDefined(params)){

                    var newEl = $(document).find("" + params[0] + "");

                    if(params[1]=="top") {
                        $(document).find("" + params[0] + "").prepend(data);
                        newEl = newEl.children().first();
                    } else {
                        $(document).find("" + params[0] + "").append(data);
                        newEl = newEl.children().last();
                    }

                    newEl.hide();
                    newEl.fadeIn(500);

                } else {
                    location.reload();
                }

            });
        }

    });


});

$(document).on("click",".ajaxSave",function(e){

    var btn = $(this);
    var btn2 = undefined;

    if(mainWindow.moveToContainer(btn) != false){
        btn2 = mainWindow.getButtonPointer(mainWindow.moveToContainer(btn));
    }

    var params = undefined;

    if(btn.hasAttr("data-destination")) {
        params = btn.attr("data-destination");
    }

    var El, progress;

    if(isDefined(btn2)) {
        El = btn2.closest("" + params + "");
        progress = mainProgressEvent.insertInto(El, true);
    } else {
        El = btn.closest("" + params + "");
        progress = mainProgressEvent.insertInto(El, true);
    }

    sendData({

        elem: btn,
        progress: "window",
        event: e

    }, function (data) {

        if(data != false){

            mainWindow.alert({

                buttonPointer: btn,
                content: {icon: "success", data: (btn.hasAttr("data-win-message") ? btn.attr("data-win-message") : "Záznam úspěšně upraven")},
                closeParent: btn.hasAttr("data-win-closeparent"),
                type: "message"

            },function(result) {

                if (isDefined(params)) {

                    //var parent = El.parent();
                    //parent.find("" + params + "");

                    setTimeout(function(){

                        mainProgressEvent.remove(progress);
                        El.replaceWith(data);
                        //parent.find("" + params + "").toggle(300);

                    }, 300);

                } else {
                    location.reload();
                }

            });


        }

    });


});

$(document).on("click",".ajaxDel",function(e){

    var btn = $(this);

    mainWindow.confirm(btn,(btn.hasAttr("data-win-title") ? btn.attr("data-win-title") : "Opravdu chcete odebrat položku?"), e, function(result){

        if(result){

            sendData({

                elem: btn,
                data: isObject(result)?result:{},
                progress: "window",
                event: e

            }, function (data) {

                if(data != false){

                    var params = undefined;

                    if(btn.hasAttr("data-destination")) {

                        params = btn.attr("data-destination");

                    }

                    mainWindow.alert({

                        buttonPointer: undefined,
                        content: {icon: "success", data: (btn.hasAttr("data-win-message") ? btn.attr("data-win-message") : "Záznam úspěšně smazán")},
                        event: e,
                        type: "message"

                    },function(result, pointer){

                        if(result) {

                            if (isDefined(params)) {

                                params = $(document).find("" + params + "");

                                if(params.parent(".MainContentEditBody").length){
                                    params = mainContentEditor.moveToContainer(params);
                                }

                                btn.closest(params).fadeOut(300, function() { $(this).remove(); });

                            } else {
                                location.reload();
                            }
                        }

                    });

                }

            });
        }
    });

});


/*--------------------------------------------------------------------------------
 -------------------------- Objekt pro práci s odkazy ----------------------------
 ---------------------------------------------------------------------------------*/

var URL = function(url){

    var URL = ((isDefined(url))?url:window.location.href);

    var URLbefore;
    var params = {};

    var self = this;
    getParams();

    function getParams(){

        var sPageURL = URL.split("?");

        URLbefore = sPageURL[0];
        sPageURL = sPageURL[1];

        if(isDefined(sPageURL)){

            var sURLVariables = sPageURL.split('&');

            for (var i = 0; i < sURLVariables.length; i++) {

                var sParameterName = sURLVariables[i].split('=');
                params[sParameterName[0]] = sParameterName[1];

            }

        }
    }

    var clear = function(){
        params = {};
    };

    this.getParameters = function(){
        return params;
    };

    this.getBaseUrl = function(){
        return URLbefore;
    };

    this.changeUrl = function(url){

        if(isDefined(url)){
            URL = url;
            clear();
            getParams();
        }

    };

    this.parameterExists = function(sParam) {

        if(isDefined(params[sParam])){
            return true;
        }
        return false;
    };

    this.getUrlParameter = function(sParam) {

        if(self.parameterExists(sParam)) {
            return params[sParam];
        } else {
            return false;
        }

    };

    this.useThisUrl = function(title,noreload){

        if(!isBoolean(noreload)){
            noreload = false;
        }

        var full = URLbefore+"?";
        var after = "";

        for (var key in params) {
            // skip loop if the property is from prototype
            if (!params.hasOwnProperty(key)) continue;

            after += ((after != '')?"&":"")+key+"="+params[key];

        }

        full += after;


        if (noreload) {
            if (typeof (history.pushState) != "undefined") {
                var obj = {Title: title, Url: full};
                history.pushState(obj, obj.Title, full);
            } else {
                location.replace(full);
            }
        } else {
            location.replace(full);
        }

    };

    this.removeParameter = function (sParam) {

        if(self.parameterExists(sParam)){
            delete params[sParam]
        }

    };

    this.changeParameter = function(sParam, sValue){

        if(self.parameterExists(sParam)){
            params[sParam] = sValue;
        }

    };

    this.addParameter = function(sParam, sValue){

        if(!self.parameterExists(sParam)){
            params[sParam] = sValue;
        } else {
            self.changeParameter(sParam, sValue);
        }

    }


};


/*--------------------------------------------------------------------------------
 -------------------------- Funkce na přizpůsobení inputu obsahu -----------------
 ---------------------------------------------------------------------------------*/

// není jistý jestli funguje
function adjustInputSize(input,minsize,maxsize)
{

    var text = input.val();

    var calc = '<div style="clear:both;display:block;visibility:hidden;"><span style="width;inherit;margin:0;font-family:'  + input.css('font-family') + ';font-size:'  + input.css('font-size') + ';font-weight:' + input.css('font-weight') + '">' + text + '</span></div>';

    $('body').append(calc);
    var width = $('body').find('span:last').width();
    $('body').find('span:last').parent().remove();

    if(isDefined(maxsize))
    {
        if(width <= maxsize){
            if (width > minsize) {
                input.width(width);
            } else {
                input.width(minsize);
            }
        }
    } else {

        if (width > minsize) {
            input.width(width);
        } else {
            input.width(minsize);
        }
    }
}

$(document).on("keyup",".resizableInput",function(){

    var min = $(this).attr('data-min-size');
    var max = $(this).attr('data-max-size');

    if(!isDefined(min)) {
        var width = $(this).width();
        $(this).attr("data-min-size",width);
        min = width;
    }

    if(!isDefined(max)) {

        var width = $(this).parent().width()*(85/100);
        $(this).attr("data-max-size",width);
        max = width;
    }

    adjustInputSize($(this),min,max);

});

$(document).on("keyup",".resizableInputU",function(){

    var min = $(this).attr('data-min-size');

    if(!isDefined(min)) {
        var width = $(this).width();
        $(this).attr("data-min-size",width);
        min = width;
    }

    adjustInputSize($(this),min);

});

$(document).ready(function(){
    
    sendData({

        data: {show: true},
        url: mainVariables.pathtofiles+'load/loadIcons.php',
        method: "POST",
        progress: "window"

    },function(data){

        var arr = $.parseJSON(data);

        for(var i = 0; i < arr[0].length; i++){
            Icons.addIcon(arr[1][i],arr[0][i]);
        }

    });

    $(document).find("object").each(function(){
        makeSVGOverlay("SVGOverlay",$(this));
    });

});