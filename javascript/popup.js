//Das popup Objekt.
function popup()
{
   //Konfiguration

   //Diese Variablen können sich gegenseiteig wiedersprechen. Relevant ist immer die erste, die verschieden ist von null,
   //wobei die Variablen in der gleichen Reihenfolge geprüft werden, wie sie hier stehen.
   this.innerHTML=null; //HTML Quellcode zur Beschreibung des Fensters. Besser nicht benutzen.
   this.DOMtree=null;   //DOM äquivalent zum HTML Quellcode.
   this.uri=null;       //Remote Addresse um Beschreibung des Fensters per HTTP Anfrage vom Server zu holen
   
   //Dieses Rechteck beschreibt die Größe des Fensters.
   this.top=0; //möchte man das Fenster zentrieren, setzt man top bzw left auf den wert 'center'.
   this.left=0;
   this.width=null; //Wenn die Parameter width und height nicht angegeben werden, wird die Größe des Fensters
   this.height=null;//durch den Inhalt definiert.
   this.closeButton=null; //Dieser Eintrag muss auf einen input vom typ Button zeigen, oder null sein. Dieser Button wird automatisch mit
                          //dem Eventhandler onclick=this.close versehen und bekommt beim anzeigen den Fokus, es sei denn, es ist ein defaultButton gesetzt.
   this.defautButton=null;//Dieser Button bekommt immer den Fokus.
   
   //Hier können Style Attribute gesetzt werden
   this.windowStyle=new Object();      //Dieses Objekt enthält alle Style Angaben für das Fenster.
   this.backgroundStyle=new Object();  //Dieses Objekt enthält alle Style Angaben für den Hintergrund.

   this.backgroundOpacity=0.4 //Deckkraft vom Hintergrund wenn Popup Fenster aktiv. Wert muss zwischen 0 und 1 liegen.
   this.z=globalZ();  //Das Fenster muss vor allen anderen Objekten platziert werden. Man wähle ein z, das garantiert größer ist als
                 //alle anderen z-index Werte auf dieser Seite und erhöhe diesen Wert für jedes neue Popup per globalZ Funktion.
   this.closeOnEnter=true; //Schliesst das Fenster, falls der User Enter drückt.
   this.getDocumentHeightExternal=null; //Liefert die Möglichkeit, eine Externe Funktion anzugeben, mit der die Dokumenthöhe berechnet wird
                                        //Das ist eventuell notwendig, wenn Ajax-Requests die Höhe ändern, da dann document.clientHeight nicht aktualisiert wird

   this.getDocumentHeightInternal=heightFkt; //Falls getDocumentHeightExternal nicht definiert wird, wird diese Funktion benutzt

   //Eventhandler   
   this.onClose=null;       //Dieser Eventhandler wird aufgerufen, wenn das Fenster geschlossen wird.
   

   //Public Funktionen                 
   this.show=showPopup;     //Erzeugt das Fenster
   this.close=closePopup;   //Schliesst das Fenster wieder


   //Interne Variablen
   
   this.root=null;   //root bezeichnet die Wurzel des Popup Objektes nachdem es in den DOM Baum eingefügt wurde.
   this.blende=null; //blende bezeichnet das Objekt, das den Hintergrund verdeckt.
   this.aktiv=false;              
   this.alterFokus=null;   //Das Element, das vorher fokussiert war bekommt nach dem Schliessen den Fokus zurück
   this.id=uniqueId();     //Eine einmalig vergebene Id
}


function showPopup(_popup)
{
   if (_popup==null)
      _popup=this;
   if (_popup==null)
   {
      produceError('_popup is null');
      return;
   }
   
   if(_popup.aktiv)
      return;
         
   //Blende setzen 
   blende=document.createElement('div');
   blende.style.position='absolute';
   blende.style.top='0px';
   blende.style.left='0px';
   blende.style.backgroundColor='#FFFFFF';
   blende.style.opacity=1-_popup.backgroundOpacity;
   blende.style.zIndex=_popup.z;
   if (window.innerHeight>document.body.clientHeight)
      blende.style.height=window.innerHeight+'px';
   else
      blende.style.height=document.body.clientHeight+'px';   
   if (window.innerWidth>document.body.clientWidth)
      blende.style.width=window.innerWidth+'px';
   else
      blende.style.width=document.body.clientWidth+'px';   

   blende.id='blende'+_popup.id;
   //Userwerte für blende setzen
   for (var attrib in _popup.backgroundStyle)
      blende.style[attrib]=_popup.backgroundStyle[attrib];
   //blende im objekt speichern
   _popup.blende=blende;
   //fader für blende
   if (fader)
   {
      _popup.blendeFader=new fader(blende.id);
      _popup.blendeFader.onFadeOut=function(){
         removeNode(_popup.blende);
         _popup.blende=null;
      }
      _popup.blendeFader.maxFadeIn=1-_popup.backgroundOpacity;
      blende.style.opacity=0;
   }
   //blende aktivieren
   document.body.appendChild(blende);

   //root setzen
   root=document.createElement('div');
   root.id='popupnr'+_popup.id;
   root.style.position='absolute';
   //Diese beiden zeilen machen Probleme mit IE
   try{
      root.style.top=_popup.top+'px';
      root.style.left=_popup.left+'px';
   }
   catch(e){
      root.style.top='0';
      root.style.left='0';
   }
   root.style.zIndex=_popup.z+1;
   //nur wenn width/height angegeben, den wert setzen
   if (_popup.width!=null)
      root.style.width=_popup.width+'px';
   if (_popup.height!=null)
      root.style.height=_popup.height+'px';
   //userwerte für fenster setzen
   for (var attrib in _popup.windowStyle)
      root.style[attrib]=_popup.windowStyle[attrib];
      
   //falls der closeButton gesetzt ist, den onclick handler setzen
   if (_popup.closeButton!=null)
   {
      _popup.closeButton.onclick=function(){  //Diese Konstruktion verhindert, dass irgendwelche Parameter an close übergeben werden
         _popup.close();
      }
   }
   
   //falls das fader.js geladen ist
   if (fader)
   {
      _popup.rootFader=new fader(root.id);
      _popup.rootFader.onFadeOut=function(){
         removeNode(_popup.root);
         _popup.root=null;
      }
      root.style.opacity=0;
   }
   //root im objekt speichern
   _popup.root=root;

   //Fensterinhalt in Fenster schreiben bzw per XMLHttpRequest vom Server holen
   if (_popup.innerHTML!=null)
   {
      root.innerHTML=_popup.innerHTML;
      showPopupPart2(_popup);
   }
   else if (_popup.DOMtree!=null)
   {
      root.appendChild(_popup.DOMtree);
      showPopupPart2(_popup);
   }
   else if (_popup.uri!=null)
   {
      var xhrObj=new xhr();
      xhrObj.uri=_popup.uri;
      xhrObj.responseKontext=_popup;
      xhrObj.onResponse=function(responseText){
         root.innerHTML=responseText;
         showPopupPart2(this);
      }
      xhrObj.get();
   }
   else
   {
      produceError('popup hat keinen Inhalt');
      return;
   }   
}

function showPopupPart2(_popup)
{
   if (_popup.left=='center' || _popup.top=='center')
   {
      _popup.root.style.visibility='hidden'; //Popup noch nicht zeigen, damit es nicht "springt", wenn man es verschiebt
      document.body.appendChild(_popup.root);//aber schonmal einbinden, damit clientWidth und clientHeight definiert ist

      if(_popup.left=='center') //horizontal zentrieren
      {
         var popupWidth=_popup.root.clientWidth;
         var bodyWidth;
         if (window.innerWidth>document.body.clientWidth)
            bodyWidth=window.innerWidth;
         else
            bodyWidth=document.body.clientWidth;
         _popup.root.style.left=((bodyWidth-popupWidth)/2)+'px';
      }

      if(_popup.top=='center') //vertikal zentrieren
      {
         var popupHeight=_popup.root.clientHeight;
         var bodyHeight;
         if (window.innerHeight>document.body.clientHeight)
            bodyHeight=window.innerHeight;
         else
            bodyHeight=document.body.clientHeight;
         _popup.root.style.top=((bodyHeight-popupHeight)/2)+'px';
      }

      _popup.root.style.visibility='visible'; //jetzt anzeigen
   }
   else
      document.body.appendChild(_popup.root);
   
   //einfaden, falls fader.js geladen
   if (fader)
   {
      _popup.blendeFader.fadeIn();
      _popup.rootFader.fadeIn();
   }

   if (_popup.defaultButton!=null)
      _popup.defaultButton.focus();
   else if (_popup.closeButton!=null)
      _popup.closeButton.focus();
   
   _popup.aktiv=true;
}

function definePopup(innerHTML)
{
   this.innerHTML=innerHTML;
}

function removeNode(node)
{
   if (node.parentNode)
      node.parentNode.removeChild(node);
}

function closePopup(arg)
{
   var _popup=this;
   if (_popup==null)
   {
      produceError('_popup is null');
      return;
   }
   if (!_popup.aktiv)
      return;
      
   if (_popup.onClose!=null)
      _popup.onClose(arg);
   
   _popup.aktiv=false;
   if (_popup.alterFokus!=null)
      _popup.alterFokus.focus();

   //die abfrage if(fader) verfehlt ihr ziel: Liefert true falls ok und erzeugt scripterror, falls fader unbekannt      
   if (fader)
   {
      _popup.rootFader.fadeOut();
      _popup.blendeFader.fadeOut();
   }
   else
      hidePopup(_popup);
      
   //Falls das Fadermodul deaktiviert ist, muss hidePopup per Hand ausgeführt werden,
   //Da fader nicht den onFade event feuert
   if (isAgent('MSIE')) //FEHLERGEFAHR HIER falls das fadermodul manuell deaktiviert wurde. if abfrage verbessern!
      hidePopup(_popup);
}

function hidePopup(_popup)
{
   if (_popup==null)
      _popup=this;
   if (_popup==null)
   {
      produceError('_popup is null');
      return;
   }

   removeNode(_popup.blende);
   removeNode(_popup.root);
   _popup.blende=null;
   _popup.root=null;
}


//Diese Funktion ist nur da, um die Abfrage dieses Wertes zu Bündeln
function heightFkt()
{
   return document.body.clientHeight;
}


function getScrollXY() {
    var scrOfX = 0, scrOfY = 0;
 
    if( typeof( window.pageYOffset ) == 'number' ) {
        //Netscape compliant
        scrOfY = window.pageYOffset;
        scrOfX = window.pageXOffset;
    } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
        //DOM compliant
        scrOfY = document.body.scrollTop;
        scrOfX = document.body.scrollLeft;
    } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
        //IE6 standards compliant mode
        scrOfY = document.documentElement.scrollTop;
        scrOfX = document.documentElement.scrollLeft;
    }
    return [ scrOfX, scrOfY ];
}


var uniqueIdvar=0;
function uniqueId()
{
   uniqueIdvar++;
   return uniqueIdvar;
}


var globalZvar=1000; //hoher startwert
function globalZ()
{
   globalZvar++;
   return globalZ;
}

