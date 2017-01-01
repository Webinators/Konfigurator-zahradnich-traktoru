/* ============================= *
 * === Definování promìnných === *
 * ============================= */
 
 var startTag;
 var endTag;
 var bb;
 var inText;
 var predText;
 var zaText;
 var enter;
 
/* ======================= *
 * === vložení BB kódu === *
 * ======================= */

function bbCode(tag, parovy){
  
  var textarea = document.frm.text;
 	
	 first = document.getElementById('text').value;	
	
    if (typeof textarea.selectionStart != 'efined') { 
        var selection = textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
        var stringStart = textarea.selectionStart;
        var stringEnd = textarea.selectionEnd;
        predText = textarea.value.substring(0,stringStart);
        var konec = document.frm.text.value.length; 
        zaText = textarea.value.substring(stringEnd, konec);
    } else if (typeof document.selection != 'efined') { 
        var selection = document.selection.createRange().text;
    } else {
        //alert('Bug!');
    }
  
  if(parovy == true){
    startTag = '<' + tag + '>';
    endTag = '</' + tag + '>';
    inText = selection;
    bb = predText + startTag + inText + endTag + zaText;
  }else if(parovy == false){
    startTag = '<' + tag + '>';
    endTag = '';
    enter = '\n'
    textarea.focus();
  }  
  
/* === Nastavení speciálních tagù === */

   if(tag == 'li'){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<li>text';
    endTag = '</li>\n';
     } 

   if(tag == 'ul1'){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<ul type="circle">\n\n';
    endTag = '</ul>';
     } 

   if(tag == 'ul2'){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<ul type="disc">\n\n';
    endTag = '</ul>';
     } 
   if(tag == 'ul3'){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<ul type="square">\n\n';
    endTag = '</ul>';
     } 

   if(tag == 'text1'){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<p align="left">';
    endTag = '</p>';
     } 

   if(tag == 'text2'){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<p align="center">';
    endTag = '</p>';
     } 
   if(tag == 'text3'){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<p align="right">';
    endTag = '</p>';
     } 
   if(tag == 'text4'){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<p align="justify">';
    endTag = '</p>';
     } 
    
    var j = 10;	
    for(i=1;i<=17;i++)
    {
    if(tag == 'font'+i){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<font style="font-size:' + j + 'px;">';
    endTag = '</font>';
     }
	j++;
	} 

    if(tag == 'font18'){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<font style="font-family:Times New Roman;">';
    endTag = '</font>';
    }

     if(tag == 'font19'){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<font style="font-family:Verdana;">';
    endTag = '</font>';
    }
	
    if(tag == 'font20'){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<font style="font-family:Comic Sans MS;">';
    endTag = '</font>';
    }

        if(tag == 'font21'){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<font style="font-family:Calibry;">';
    endTag = '</font>';
    }
	
        if(tag == 'font22'){
    if(inText == false){
      inText = '';
    }else{
      inText = selection;
    }
    startTag = '<font style="font-family:Myriad PRO;">';
    endTag = '</font>';
    }

  bb = predText + startTag + inText + endTag + zaText;
  document.frm.text.value = bb;    
}
function back(co) 
{

predchozi = document.getElementById('text').value;

document.frm.text.value = first;

}

function next(co) 
{
zaloha = document.getElementById('text').value;
document.frm.text.value = predchozi;
}