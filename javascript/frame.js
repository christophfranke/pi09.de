//class frame
//Die frame Klasse ist eine Hilfsklasse fuer die Klasse aktion.
//Ein frame hat eine id, eine uri und ein onRefresh Eventhandler.
//Erstellt man eine frame Klasse, wird das Element mit der id eid automatisch
//mit dem Inhalt von uri bestueckt (per asynchrone Httpanfrage im Hintergrund).
//Beispiel:
//contentFrame=new frame('content.php', 'elementId');
//contentFrame.onRefresh=myInitFunction;
//Diese beiden Zeilen sind aequivalent zu:
//contentFrame=new frame('content.php', 'elementId', myInitFunction);
function frame(uri, eid, onRefreshHandler)
{
   //Konfigurationsvariaben
   this.alwaysLoadThis=false;
   this.disableFader=false;

   //Eventhandler
   this.onRefresh=onRefreshHandler; //Diese Funktion wird jedesmal ausgefuehrt nachdem der Inhalt erneuert wurde.

   //Public Funktionen
   this.refresh=refreshFrame; //Diese Funktion lädt den Frame neu
   this.load=loadFrame;    //Wie refresh, aber alwaysLoadThis wird temporär als 'true' angenommen
   this.navigate=navigateFkt; //Den Inhalt dieses Frames ändern
   this.addSubFrame=addSubFrameFkt; //subFrame registrieren
   this.addSubFrames=addSubFramesFkt; //mehrere subFrames registrieren
   this.clearSubFrames=clearSubFramesFkt; //Alle registrierten subFrames löschen
   this.setInvisible=setInvisibleFkt; //Den Frame davon informieren, dass er jetzt unsichtbar ist

   //Interne Variablen initialisieren
   this.uri=uri; //Von dieser Addresse wird der Frameinhalt geholt
   this.eid=eid; //Das Html-Element mit dieser Id wird mit dem Frameinhalt gefuellt
   this.element=null; //Das HTML-Element, das später befüllt wird. Late Binding, um keine unnötigen Fehler zu erzeugen
   this.xhrObj=new xhr(); //Das xhr Objekt stellt sicher, dass mehrere XMLHttpRequests sich nicht in die Quere kommen
   this.parent=null;  //Der Vaterframe, für Frames, die Subframes sind
   this.subFrameIndex=null; //Zum wiederfinden im subFrames Array des Vaters
   this.subFramesWait=null; //Auf wie viele subFrames muss noch gewartet werden, bis der onRefreshHandler aufgerufen wird
   this.waitForSubFrames=false; //Warten auf subFrames?
   this.subFrames=new Array(); //registrierte subframes
   
   //Interne Funktionen
   this.subFrameRefreshed=subFrameRefreshed; //Nachrichtenschnittstelle für subFrames

   //Konstruktor
   this.fadeObj=new fader(eid); //Fader anlegen und mit eid verknüpfen
}


function subFrameRefreshed()
{
   var _frame=this;
   if (_frame==null)
   {
      produceError('subFrameRefreshed: frame is null');
      return;
   }
   
   //Nicht in Wartestellung -> Nachricht verwerfen
   if (_frame.waitForSubFrames==false)
      return;
      
   //In Wartestellung, aber nicht definiert, auf wieviele subFrames noch gewartet wird
   if (_frame.subFramesWait==null)
   {
      produceError('subFramesWait is null');
      return;
   }

   //Nachricht registrieren   
   _frame.subFramesWait--;
   
   //Ok, alle subFrames geladen
   if (_frame.subFramesWait==0)
   {
      //eigenen onRefresh Handler ausführen
      if (_frame.onRefresh!=null)
         _frame.onRefresh();
      
      //Jetzt nicht mehr in Wartestellung
      _frame.waitForSubFrames=false;

      //Parent benachrichtigen, falls vorhanden      
      if (_frame.parent!=null)
         _frame.parent.subFrameRefreshed();
   }
      
   //das würde bedeuten, man ist in Wartestellung und hat auf 0 subFrames gewartet, als man eine Nachricht bekommen hat
   if (_frame.subFramesWait<0)
      produceError('subFramesWait negativ: '+_frame.subFramesWait);
}


function loadFrame()
{
   var _frame=this;
   if (_frame==null)
   {
      produceError('loadFrame: _frame is null');
      return;
   }
   
   var temp=_frame.alwaysLoadThis;
   _frame.alwaysLoadThis=true;
   _frame.refresh();
   _frame.alwaysLoadThis=temp;
}


function refreshFrame()
{
   var _frame=this;
   if (_frame==null)
   {
      produceError('refreshFrame: frame is null');
      return;
   }
   
   //schauen, ob das fadeObj benutzt werden soll   
   _frame.fadeObj.disabled=_frame.disableFader;
      
   //Eventuell Refresh weitergeben an subFrames
   if (_frame.alwaysLoadThis==false && _frame.subFrames.length>0)
   {
      //Registrieren, auf wie viele subFrames gewartet werden muss
      _frame.subFramesWait=_frame.subFrames.length;
      //In Wartestellung begeben
      _frame.waitForSubFrames=true;
      
      //Auftrag zum Refresh an die subFrames verteilen
      for(var i=0;i<_frame.subFrames.length;i++)
         _frame.subFrames[i].refresh();
         
      return; //Der eigene Frame wird NICHT neugeaden
   }
      
   _frame.fadeObj.fadeOut();
      
   //In diesem Objektkontext wird readyFkt aufgerufen werden
   _frame.xhrObj.responseKontext=_frame;
    
   //Anfrage senden
   _frame.xhrObj.get(_frame.uri,readyFkt);
}

function readyFkt(responseText)
{
   var _frame=this;
   if (_frame==null)
   {
      produceError('readyStateChangeFkt: _frame is null');
      return;
   }
   
   //HTML Element referenzieren
   _frame.element=document.getElementById(_frame.eid);
   if (_frame.element==null)
   {
      produceError('refreshFrame: '+_frame.eid+' nicht gefunden');
      return;
   }
   //neuen Inhalt schreiben
   _frame.element.innerHTML=responseText;
   //onRefresh handler ausführen, falls vorhanden
   if (_frame.onRefresh!=null)
      _frame.onRefresh(responseText);
   //parent Frame bescheid sagen, falls vorhanden
   if (_frame.parent!=null)
      _frame.parent.subFrameRefreshed();
   //Alles fertig, dann einfaden
   _frame.fadeObj.fadeIn();
}


function addSubFrameFkt(subFrame)
{
   var _frame=this;
   if (_frame==null)
   {
      produceError('addSubFrameFkt: frame is null');
      return;
   }
   
   //Aus der alten Elternklasse Löschen
   if (subFrame.parent!=null)
      subFrame.parent.subFrames.splice(_frame.subFrameIndex,1);

   //Verknüpfung zum neuen Vater
   subFrame.subFrameIndex=_frame.subFrames.length;
   subFrame.parent=_frame;
      
   //Einfügen in subFrames
   _frame.subFrames.push(subFrame);
}

//Mehrere subfarmes auf einmal adden
function addSubFramesFkt()
{  
   _frame=this;
   var callArray;
   for(var i=0;i<addSubFrameFkt.arguments.length;i++)
   {
      callArray=new Array(addSubFrameFkt.arguments[i]);
      addSubFrameFkt.call(_frame, callArray);
   }
}


function clearSubFramesFkt()
{
   var _frame=this;
   if (_frame==null)
   {
      produceError('clearSubFramesFkt: frame is null');
      return;
   }
   

   //Über alle registrierten subframes iterieren
   for(var i=0;i<_frame.subFrames.length;i++)
   {
   
      //Alle subFrames über Unsichtbarkeit benachrichtigen
      _frame.subFrames[i].setInvisible();

      //Verknüpfung aufheben
      _frame.subFrames[i].parent=null;
      _frame.subFrames[i].subFrameIndex=null;
   }
   _frame.subFrames=new Array();
}

function setInvisibleFkt()
{
   var _frame=this;
   if (_frame==null)
   {
      produceError('setInvisibeFkt: frame is null');
      return;
   }
   
   //Diese Prozesse benötigen Sichtbarkeit
   _frame.xhrObj.abort();
   _frame.fadeObj.stop();

   //subFrames bescheid sagen
   for (var i=0;i<_frame.subFrames.length;i++)
      _frame.subFrames[i].setInvisible();
}

function navigateFkt(newUri,onRefreshHandler)
{
   var _frame=this;
   if (_frame==null)
   {
      produceError('clearSubFramesFkt: frame is null');
      return;
   }
   
   //nur refresh, nicht ganz neuladen 
   //(also insbesondere keinen alwaysLoadThis erzwingen und onRefreshHandler ignorieren)
   if (newUri==_frame.uri)
   {
      _frame.refresh();
      return;
   }

   //falls vorhanden neuer refreshHandler
   if (onRefreshHandler!=null)
      _frame.onRefresh=onRefreshHandler;

   //neue uri
   _frame.uri=newUri;
   
   //neu laden, zum Navigieren immer alwaysLoadThis aktivieren
   _frame.load();
}
