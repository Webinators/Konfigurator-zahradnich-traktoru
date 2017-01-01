    function zobrazpravymkliknutim(odkud,co,name)
    {

        $(document).ready(function () {
            try {
                $("#"+odkud+"").bind("contextmenu", function (e) {
                    e.preventDefault();
                    $("#"+co+"").css({ top: e.pageY + "px", left: e.pageX + "px" }).show(100);
                });
                $("#"+odkud+"").mouseup(function (e) {
                    var container = $("#"+co+"");
                    if (container.has(e.target).length == 0) {
                        container.hide();
                    }
                });
                $(document).mouseup(function (e) {
                    var container = $("#"+odkud+"");
                    if (container.has(e.target).length == 0) {
                        $("#"+co+"").hide(100);
                    }
                });
            }
            catch (err) {
                alert(err);
            }

      });
     } 

function zvyraznidiv(odkud,name)
{
$(document).ready(function () {


var atr1 = $("#"+odkud+"").attr('onmouseover');
var atr2 = $("#"+odkud+"").attr('onmouseout');
var hodnota = $("#foldername"+odkud+"").text();

      $("#scrolldiv2").mouseup(function(e)
     {
        var subject = $("#"+odkud+""); 

        if(e.target.id != subject.attr('id') && !subject.has(e.target).length)
        {
      $("#foldername"+odkud+"").html(''+name+''); 
      $("#"+odkud+"").removeClass('zvyraznenydiv').addClass('seznamsouboru');
      $("#"+odkud+"").attr("onmouseover",atr1);
      $("#"+odkud+"").attr("onmouseout",atr2);
        }
    });

       
       $("#"+odkud+"").removeClass('seznamsouboruponajeti').addClass('zvyraznenydiv');
       $("#"+odkud+"").removeAttr('onmouseover').removeAttr('onmouseout');
        var inputNode = '<input id="deletefolder" name="deletefolder" type="text" value="'+name+'" style="position: absolute;-moz-opacity:0;-khtml-opacity: 0;opacity: 0;"/>';

        var spoj = hodnota + inputNode;

        $("#foldername"+odkud+"").html(spoj);

          setTimeout(function() {
            $("#deletefolder").focus();
        }, 200);

       $("#deletefolder").on( "keydown", function(event) {
if(event.which == 46) 
{

var answer = confirm("Opravdu chcete odstranit složku "+name+" ?")
if (answer){

var slozka = $("#deletefolder").val();

$(document).ready(function(){
$("#seznam").load('apps/wysiwyg_textarea/load/deletefolder.php',{name: slozka},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });
  });
	}
	else{

	}

}

});

});
}

function zvyraznisoubor(odkud,name,way)
{
$(document).ready(function () {


var atr1 = $("#"+odkud+"").attr('onmouseover');
var atr2 = $("#"+odkud+"").attr('onmouseout');
var hodnota = $("#filename"+odkud+"").text();

      $("#scrolldiv2").mouseup(function(e)
     {
        var subject = $("#"+odkud+""); 

        if(e.target.id != subject.attr('id') && !subject.has(e.target).length)
        {
      $("#filename"+odkud+"").html(''+name+''); 
      $("#"+odkud+"").removeClass('zvyraznenydiv').addClass('seznamsouboru');
      $("#"+odkud+"").attr("onmouseover",atr1);
      $("#"+odkud+"").attr("onmouseout",atr2);
      $("#insert"+odkud+"").hide();
        }
    });

       
       $("#"+odkud+"").removeClass('seznamsouboruponajeti').addClass('zvyraznenydiv');
       $("#"+odkud+"").removeAttr('onmouseover').removeAttr('onmouseout');
        var inputNode = '<input id="deletefile" name="deletefile" type="text" value="'+name+'" style="position: absolute;-moz-opacity:0;-khtml-opacity: 0;opacity: 0;width: 10px;"/>';

        var spoj = hodnota + inputNode;

        $("#filename"+odkud+"").html(spoj);

          setTimeout(function() {
            $("#deletefile").focus();
        }, 200);

        $("#insert"+odkud+"").show();

       $("#deletefile").on( "keydown", function(event) {
if(event.which == 46) 
{

var answer = confirm("Opravdu chcete odstranit soubor "+name+" ?")
if (answer){

var soubor = $("#deletefile").val();

$(document).ready(function(){
$("#seznam").load('apps/wysiwyg_textarea/load/deletefile.php',{name: soubor},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });
  });
	}
	else{

	}

}

});

});
}

function vlozobrdotextarey(celeid, way, i)
{

var zarovnani = $( "#zarovnaniobrazku" ).val();

var tagname = $(celeid).get(0).tagName;

if(tagname == "INPUT")
{
$(celeid).val('<center><img src='+way+' /></center>');
$("#file_popisek").change();
$("#obrazky").hide(200);
}
else
{
if(zarovnani == "left")
{
$(celeid).append('<img src='+way+' />');
}
if(zarovnani == "right")
{
$(celeid).append('<img src='+way+' />');
}
if(zarovnani == "absolute")
{
$(celeid).append('<img src='+way+' />');
}

alert("obrázek byl vložen");
}

}

function skryjobrazky(idelementu){
$("#"+idelementu+"").hide(200);
}

function zobrazobrazky(idelementu,icko,celeid){

$("#"+idelementu+"").show(200);

$(document).ready(function(){

$("#seznam").html("<img src='apps/wysiwyg_textarea/ikonka/loading.gif' width='30px' style='z-index: 10;background-color: #ffffff;'/>");

$("#seznam").load('apps/wysiwyg_textarea/load/vypsaniobrazkuktextaku.php',{id : ''+icko+'', id_textarey : ''+celeid+''},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
      
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });

});

}

function nahraj ()
{

$(document).ready(function()
{

$("#multiform").submit(function(e)
{
		$("#multi-msg").html("<img src='apps/wysiwyg_textarea/ikonka/loading.gif' width='30px' style='z-index: 10;background-color: #ffffff;'/>");

	var formObj = $(this);
	var formURL = formObj.attr("action");

		var formData = new FormData(this);
		$.ajax({
        	url: formURL,
	       type: 'POST',
		data:  formData,
              beforeSend: function(xhr, options){

var file = $('#file').val();

if(file == false)
{
$("#multi-msg").html('<font color="#ff0000">Vyberte prosím soubor!</font>'); return false;
}
              },
		mimeType:"multipart/form-data",
		contentType: false,
    	       cache: false,
        	processData:false,
			success: function(data, textStatus, jqXHR)
		    {
					$("#multi-msg").html('');$("#seznam").html(data);
					
		    },
		  	error: function(jqXHR, textStatus, errorThrown) 
	    	{
				$("#multi-msg").html('<pre><code class="prettyprint">AJAX Request Failed<br/> textStatus='+textStatus+', errorThrown='+errorThrown+'</code></pre>');
	    	} 	        
	   });
        e.preventDefault();
        e.unbind();

});

});

}



function najdi(text)
{
if(text == false)
{
$(document).ready(function(){

$("#seznam").load('apps/wysiwyg_textarea/load/vypsaniobrazkuktextaku.php',function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
      
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });

});
}
else
{
$("#loading").html("<img src='apps/wysiwyg_textarea/ikonka/loading.gif' width='30px' style='z-index: 10;background-color: #ffffff;'/>");

$("#seznam").load('apps/wysiwyg_textarea/load/search.php?search='+text+'',function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
      $("#loading").html();
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });
}
}

function createfolder()
{

$(document).ready(function(){

$("#seznam").load('apps/wysiwyg_textarea/load/createfolder.php',function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")

    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });

});

}

function renamefolder(id,nazev,i)
{

$("#"+id+"").html('<input id="foldername" type="text" name="foldername"/><input type="hidden" id="iddivu" name="id" value="'+id+'"/><input type="hidden" id="nazevslozky" name="nazevslozky" value="'+nazev+'"/><input type="hidden" id="i" name="i" value="'+i+'"/>');

$("#foldername").focus();

$('#foldername').on('blur', function () {

$('#foldername').hide();

var name = $("#nazevslozky").val();

var foldname = $("#foldername").val();

if(foldname == name || foldname == false)
{
$("#"+id+"").html('<a style="cursor: pointer;">'+nazev+'</a> ');
}
else
{

$("#seznam").load('apps/wysiwyg_textarea/load/renamefolder.php',{starynazev: name, novynazev: foldname},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
      
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });

}

});


$( "#foldername" ).on( "keydown", function(event) {
if(event.which == 13) 
{
$('#foldername').hide();

var name = $("#nazevslozky").val();

var foldname = $("#foldername").val();

if(foldname == name || foldname == false)
{
$("#"+id+"").html('<a style="cursor: pointer;">'+nazev+'</a> ');
}
else
{

$("#seznam").load('apps/wysiwyg_textarea/load/renamefolder.php',{starynazev: name, novynazev: foldname},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
      
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });

}

    }
});

}

function renamefile(id,nazev,i)
{

var explode = nazev.split('.');

var pripona = '.'+explode[1];

$("#"+id+"").html('<input id="foldername" type="text" name="foldername" value="'+ pripona +'"/><input type="hidden" id="iddivu" name="id" value="'+id+'"/><input type="hidden" id="nazevslozky" name="nazevslozky" value="'+nazev+'"/><input type="hidden" id="i" name="i" value="'+i+'"/>');

$("#foldername").focus();

$('#foldername').on('blur', function () {

$('#foldername').hide();

var name = $("#nazevslozky").val();

var foldname = $("#foldername").val();

if(foldname == name || foldname == pripona || foldname == false)
{
$("#"+id+"").html('<a style="cursor: pointer;">'+nazev+'</a> ');
}
else
{

$("#seznam").load('apps/wysiwyg_textarea/load/renamefolder.php',{starynazev: name, novynazev: foldname},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
      
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });

}

});


$( "#foldername" ).on( "keydown", function(event) {
if(event.which == 13) 
{
$('#foldername').hide();

var name = $("#nazevslozky").val();

var foldname = $("#foldername").val();

if(foldname == name || foldname == pripona || foldname == false)
{
$("#"+id+"").html('<a style="cursor: pointer;">'+nazev+'</a> ');
}
else
{

$("#seznam").load('apps/wysiwyg_textarea/load/renamefolder.php',{starynazev: name, novynazev: foldname},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
      
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });

}

    }
});

}

function nastavstyldivu(i)
{

$("#"+i+"").removeClass('seznamsouboru').addClass('seznamsouboruponajeti');

}

function zrusstyldivu(i)
{

$("#"+i+"").removeClass('seznamsouboruponajeti').addClass('seznamsouboru');
}

$("#deletefolder").on( "keydown", function(event) {
if(event.which == 46) 
{

var answer = confirm("Opravdu chcete odstranit složku?")
if (answer){

var slozka = $("#deletefolder").val();

$(document).ready(function(){
$("#seznam").load('apps/wysiwyg_textarea/load/deletefolder.php',{name: slozka},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });
  });
	}
	else{

	}

}

});

function deletefolder(i,name)
{

	var answer = confirm("Opravdu chcete odstranit složku: "+name+"?")
	if (answer){

$(document).ready(function(){
$("#seznam").load('apps/wysiwyg_textarea/load/deletefolder.php',{name: name},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });
  });
	}
	else{

	}

}

function deletefile(i,name)
{

	var answer = confirm("Opravdu chcete odstranit soubor: "+name+"?")
	if (answer){

$(document).ready(function(){
$("#seznam").load('apps/wysiwyg_textarea/load/deletefile.php',{name: name},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });
  });
	}
	else{

	}

}

function zobrazobsahslozky(name)
{
$("#seznam").load('apps/wysiwyg_textarea/load/showfoldercontents.php',{name: name},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });
}

function movetofolder(name)
{
$("#seznam").load('apps/wysiwyg_textarea/load/movetofolder.php',{name: name},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });
}


