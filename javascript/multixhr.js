//ein xhr Objekt ist (hoffentlich) in der Lage, mehrere xhr requests geichzeitig durchzuführen
//und kümmert sich um Fehlerbehandlung von XMLHttpRequest

function xhr()
{
   var self=this;
   
   //interne Variablen
   //Das erste Argument von onResponse ist immer http.responseText, das zweite ist self.responseArgs.
   //Man kann also Variablen hier registrieren und an damit implizit an die onResponse Funktion weitergeben
   self.responseArgs=new Array();
   self.responseKontext=null; //Hier lässt sich ein Objekt einfügen, in dessen Kontext wird onResponse ausgeführt
                              //ein Aufruf im Kontext null entspricht einem globalem Aufruf
   self.busy=false;        //Eventuell muss ein laufender Request abgebrochen werden

   //Public Variablen
   self.uri=null;          //diese uri wird angefragt
   self.onResponse=null;   //der onResponseHandler wird aufgerufen, wenn der Server vollständig geantwortet hat
   self.body=null;         //bei Aufruf über post wird String im Http-Body gesendet
   
   //Pubic Funktionen
   self.get=xhrGet;  //Hierum gehts, die get Funktion führt den tatsächlichen XMLHttpRequest durch
   self.post=xhrPost; //Die post Funktion ist imgrunde das gleiche wie die get Funktion nur mit der Zusätzlichen Möglichkeit, den body mitzuschicken
   self.abort=xhrAbort; //Die Abortfunktion sorgt dafür, dass der Request abgebrochen wird
   self.addResponseArg=function(arg){self.responseArgs.push(arg);}
   
   //Konstruktor
   self.http=new XMLHttpRequest();
}

function xhrGet(uriArg, onResponseArg)
{  
   var self=this;

   if (uriArg!=null)
      self.uri=uriArg;
   if (onResponseArg!=null)
      self.onResponse=onResponseArg;

   self.busy=true;   
         
   self.http.open('GET',self.uri,true);
   self.http.onreadystatechange=function(){
      if (self.http.readyState==4 && self.onResponse!=null)
      {
         self.busy=false;
         self.responseArgs.unshift(self.http.responseText); //Das erste Argument für den Aufruf von onResponse soll die http Antwort sein
         self.onResponse.apply(self.responseKontext, self.responseArgs); //onResponse aufrufen mit den richtigen Argumenten im angegebenen Objektkontext
         self.responseArgs.shift(); //Das Array auf den Stand vor dem Aufruf bringen (reparieren).
      }
   }
   self.http.send();
}

function xhrPost(uriArg, onResponseArg, bodyArg)
{
   var self=this;

   if (uriArg!=null)
      self.uri=uriArg;
   if (onResponseArg!=null)
      self.onResponse=onResponseArg;
   if (bodyArg!=null)
      self.body=bodyArg;
      
   //Referenz auf self hinterlegen, um bei Kontextwechsel Zugriff zu bekommen
   //self.http.backupReference=self; //derzreit nicht benötigt
      
   self.http.open('POST',self.uri,true);
   self.http.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
   self.http.onreadystatechange=function(){
      //this verweist in diesem Fall auf das gerade aktive XMLHttpRequest Objekt, da die Funktion von diesem aufgerufen wird
      //self muss neu belegt werden, da die Variable self beim nächsten Aufruf von xhrGet überschrieben wird.
      //self=this.backupReference; //funktioniert derzeit auch ohne dieses workaround
      if (self.http.readyState==4 && self.onResponse!=null)
      {
         self.responseArgs.unshift(self.http.responseText); //Das erste Argument für den Aufruf von onResponse soll die http Antwort sein
         self.onResponse.apply(self.responseKontext, self.responseArgs); //onResponse aufrufen mit den richtigen Argumenten im angegebenen Objektkontext
         self.responseArgs.shift(); //Das Array auf den Stand vor dem Aufruf bringen (reparieren).
      }
   }
   self.http.send(self.body);
}


function xhrAbort()
{
   var self=this;
   
   if (self.busy)
      self.http.onreadystatechange=function(){};
   
   self.busy=false;
}


