//Fehlerroutine
onerror=onError;
window.onerror=onError;
function onError(msg,url,line) {
   produceError("Fehler in "+url+" Zeile "+line+": "+msg);
   return true;
}

var errorCount=0;
var showError=true;
function produceError(msg)
{
   //Nach 4 Versuchen abbrechen, falls ein Fehler beim Logvorgang passiert
   errorCount++;
   if (errorCount>4)
      return;
      
   sendError(msg);

   //KEINE ERRORS ANZEIGEN
   //erstmal simples alertfenster benutzen. Eventuell generell keine gute Idee, softAlert zu benutzen. Mal sehen.
//   if (showError)
//      alert("Es ist ein Fehler aufgetreten.");
   //   softAalert('Es ist leider ein Fehler aufgetreten. Das k&ouml;nnte daran liegen, dass einen alten Browser benutzt. Aktuelle, schnelle Browser gibt es kostenlos unter <a href="http://www.mozilla.de" target="_blank">www.mozilla.de</a> und <a href="http://www.chrome.de" target="_blank">www.chrome.de</a>');

   showError=false;
   
   setTimeout("showError=true;errorCount=0",3000);
}

function sendError(msg)
{
   var xhrObj=new xhr();
   xhrObj.get('/phrasendrescher/errorlog.php?msg='+escape(msg));
}


function showAll(obj)
{
   var text='';
   for (var k in obj)
      text+=k+',  ';
   return text;
}

function showAllValues(obj)
{
   var text='';
   for (var k in obj)
      text+=k+'='+obj[k]+',  ';
   return text;
}

function findProperty(obj,property)
{
   for (var k in obj)
      if (k==property)
         return 'property '+property+' found';
   return 'property '+property+' not found';
}
