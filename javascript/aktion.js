//aktion.php
//In dieser Version wird fader.js benötigt.
//Eine Implementierung sollte ohne fader.js problemlos möglich sein

//Klasse aktion
//Diese Klasse regelt die asynchrone Kommunikation mit dem Server.
//Beispiel:
//var myAktion=new aktion("login", contentFrame); //contentFrame definiert wie oben
//myAktion.form=document.getElementById("myLoginForm");
//myAktion.onResponse=myResponseHandler;
//myAktion.send();

function aktion(befehl)
{
   //initialisiere uebergebene Parameter
   this.befehl=befehl; //Moegliche Befehle sind login, logout, register, 
                       //bearbeiten_account, loeschen_account, anlegen_kommentar, loeschen_kommentar.
   this.refresh=null;  //Nachdem die Anfrage erfolgt ist, werden diese frames neu geladen.
                       //Die Frames koennen entweder dem Konstruktor uebergeben werden als weitere Parameter
                       //oder spaeter mit der Funktion addRefresh hinzugefuegt werden.
   
   //Standartwerte
   this.status=1;      //Beinhaltet den Rückgabestatus der letzten Anfrage.
   this.response='';   //Beinhaltet die Fehlermeldung bzw Erfolgsmeldung der letzten Anfrage
   this.form=null;     //Diese Variable kann die id eines Formulars beinhalten.
   this.autoClear=true;//Wenn true, dann wird im Erfolgsfall der Aktion das Formular (this.form) geleert.
   this.remoteFile='/aktion.php'; //An diese Datei wird die Anfrage geschickt
   this.stdReponseId=null; //Id von dem Element, das von StdResponse mit dem Inhalt von response gefüllt wird
   this.debug=false;  //true erfragt Debuginformationen von remoteFile
   this.xhrObj=null;
   this.refreshOnFail=false; //Fenster auch neuladen, falls die aktion nicht gekappt hat?
         
   //oeffentliche Funktionen
   this.addRefresh=null; //Frame hinzufuegen, der nach der Anfrage refresht werden soll.
   this.send=sendAktion;              //myAktion.send() startet die Anfrage.

   //Eventhandler
   this.onResponse=null;       //Wird ausgefuehrt, wenn die Aktion eine Antwort erhalten hat.

   //Interne Funktionen
   this.formdata=createFormString;    //Formulardaten Parser. Wandelt das Formular in einen String um.
   this.clearForm=clearForm;          //Loescht jede Einstellung am Formular.


   //Konstruktor fuellt das refresh-Array mit den zusaetzlichen Parametern.
   this.refresh=new Array(0);
   for(i=1;i<arguments.length;i++)
      this.refresh.push(arguments[i]);
   this.addRefresh=this.refresh.push;
}


function stdResponse(_aktion)
{
   if (_aktion==null)
      return;
   if (_aktion.stdResponseId==null)
      return;
   elem=document.getElementById(_aktion.stdResponseId);
   if (elem==null)
      return;
   elem.innerHTML=_aktion.response;
}


function sendAktion()
{
   var _aktion=this;
   if (_aktion==null)
   {
      produceError('sendAktion: _aktion is nul');
      return;
   }
      
   //xhrObjekt erstellen
   _aktion.xhrObj=new xhr();
   
   //uri angeben
   _aktion.xhrObj.uri=_aktion.remoteFile+'?aktion='+_aktion.befehl+'&debug='+_aktion.debug;
   
   //Requestbody mit Formulardaten füllen
   _aktion.xhrObj.body=_aktion.formdata();
   
   //responseKontext sicherstellen
   _aktion.xhrObj.responseKontext=_aktion;
   
   //onResponse Funktion registrieren
   _aktion.xhrObj.onResponse=onResponseAktion;
   
   //alles fertig, abschicken
   _aktion.xhrObj.post();

}

function onResponseAktion(responseText)
{
   _aktion=this; //xhr sorgt dafür, dass diese Fkt im richtigen Kontext aufgerufen wird
   if (_aktion==null)
   {
      produceError('onResponseAktion: _aktion is null');
      return;
   }
   
   //status und response speichern
   _aktion.status=parseInt(responseText.charAt(0));
   _aktion.response=responseText.substring(1, responseText.length);
   
   //onResponse Handler aufrufen
   if (_aktion.onResponse!=null)
      _aktion.onResponse(_aktion.status, _aktion.response);
         
   //falls definiert, response in bestimmtes element schreiben (stdResponse verhalten)
   stdResponse(_aktion);
   
   //Refresh im Erfogsfall durchführen, bei Misserfolg eventuell nicht
   if (_aktion.refreshOnFail || _aktion.status==1)    
      for (var r in _aktion.refresh)
         _aktion.refresh[r].refresh();

   //Im Erfolgsfall eventuell das verknüpfte Formular leeren         
   if (_aktion.status==1 && _aktion.autoClear)
      _aktion.clearForm();
}


function clearForm(form)
{
   if (form==null)
      form=document.getElementById(this.form);
   else
      form=document.getElementById(form);
   if (form==null)
      return null;  
      
   var e;
      
   for (i=0;i<form.elements.length;i++)
   {
      e=form.elements[i];
      switch(e.type)
      {
         case 'text':
            e.value='';
            break;
         case 'hidden':
            break;
         case 'password':
            e.value='';
            break;
         case 'select-one':
            e.value='';
            break;
         case 'textarea':
            e.value='';
            break;
         case 'checkbox':
            e.checked=false;
            break;
         case 'radio':
            e.checked=false;
            break;
         case 'submit':
            break;
         case 'button':
            break;
         default:
            produceError('Warnung: Unbekannter Typ: '+e.type);
            break;
      }
   }
}


function createFormString(form)
{
   if (form==null)
      form=document.getElementById(this.form);
   else
      form=document.getElementById(form);
   if (form==null)
      return null;  
    
   var text='';
   for (i=0;i<form.elements.length;i++)
   {
      switch(form.elements[i].type)
      {
         case 'text':
            text+=form.elements[i].name+'='+myEscape(form.elements[i].value);
            break;
         case 'hidden':
            text+=form.elements[i].name+'='+form.elements[i].value;
            break;
         case 'password':
            text+=form.elements[i].name+'='+myEscape(form.elements[i].value);
            break;
         case 'checkbox':
            text+=form.elements[i].name+'='+form.elements[i].checked;
            break;
         case 'radio':
            if (!form.elements[i].checked)
               continue;
            text+=form.elements[i].name+'='+form.elements[i].value;
            break;
         case 'select-one':
            text+=form.elements[i].name+'='+form.elements[i].value;
            break;
         case 'textarea':
            text+=form.elements[i].name+'='+myEscape(form.elements[i].value);
            break;
         case 'submit':
            continue;
            break;
         case 'button':
            continue;
            break;
         default:
            produceError('Warnung: Unbekannter Typ: '+form.elements[i].type);
            break;
      }
      text+='&';      
   }
   text = text.substring(0,text.length-1);
   return text;
}

function myEscape(str)
{
   //escapen:
   esc = str.replace(/\+/g, '%2B'); // + -> %2B
   esc = esc.replace(/&/g, '%26'); // & -> %26

   return esc;
}


