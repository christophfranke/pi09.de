/*
Benoetigt: tween.js
           browserdetect.js


Public vars:
   pfeilObenSrc: Pfad fuer die pfeil-zeigt-zum-zuklappen-Grafik
   pfeilUntenSrc:Pfad fuer die pfeil-zeigt-zum-aufklappen-Grafik
   duration:     Dauer des Aufklappens in sekunden (kommazahlen moeglich)

Public member functions:
   Konstruktor:
   params:
      klapptag:          In diesem tag befindet sich der zu versteckende Inhalt.
                        Es wird von einem div-tag ausgegangen, aber es sollte auch mit
                        jedem anderem block-element funktionieren.
                        Das Tag wird als DOM-Node uebergeben. Dieses Tag sollte moeglichst
                        nicht mit css Informationen versehen sein, da diese ueberschrieben
                        werden koennten.
      pfeil (optional): Ein img tag, in dem eine Pfeilgrafik gespeichert wird, die anzeigt,
                        ob man jetzt aufklappen oder zuklappen kann. Der Pfad zur grafik kann
                        entweder im Konstruktor bei den Konfigurationsvariablen oder ueber die
                        memberfunktion setPfeilSrc gesetzt werden.
                     
   setPfeilSrc: Setzt die Pfeilgrafiken
   params:
      oben:    Pfad zur grafik zum zuklappen
      unten:   pfad zur grafik zum aufklappen
      
   click:   klappt auf bzw wieder zu, je nach Zustand
   keine Parameter
*/

function Klappe(klapptag,pfeil)
{
   //Konfigurations variablen   
   this.pfeilObenSrc='img/pfeiloben.png';
   this.pfeilUntenSrc='img/pfeilunten.png';
   this.duration=0.3; //in sekunden
   this.disabled=false;
   
   //Zustandsvariablen
   this.height=0;
   this.direction=1;
   this.running=false;

   //Objekte
   this.tweenUp=null;
   this.tweenDown=null;
   this.frame=null; //zunaechst null, wird noch erstellt
   this.content=klapptag;
   this.pfeil=pfeil; //pfeilgrafik
   this.id=getUniqueId();
   
   //Events
   this.onMotion=null;
   this.onStart=null;
   this.onStop=null;
   
   if (klapptag==null)
      alert('Klappe.constructor: first argument (klapptag) must not be null');
   if(!Tween)
      alert('The tweening class is required to be imported. It is found usually in the file tween.js');

   this.init();
}

Klappe.prototype.setPfeilSrc=function(oben, unten)
{
   this.pfeilObenSrc=oben;
   this.pfeilUntenSrc=unten;
}


Klappe.prototype.init=function()
{
   if (typeof(BrowserDetect)!='undefined')
      if (BrowserDetect.browser=='Explorer' && BrowserDetect.version<8)
      {
         this.disabled=true;
         return;
      }
   
      
   var container=this;
   var duration=this.duration;

   //frame element erzeugen und im baum richtig einhaengen
   var parent=container.content.parentNode;
   var next=container.content.nextSibling;
   container.frame=document.createElement('div'); //neues div erzeugen
   removeNode(container.content);                 //alten knoten entfernen
   container.frame.appendChild(container.content);//alten knoten im neuen framediv einhaengen
   //das neue framediv an der gleichen Stelle einhaengen, wo content eingehaengt war
   if (next==null)
      parent.appendChild(container.frame);
   else
      parent.insertBefore(container.frame, next);

   
   var frame=container.frame;
   var content=container.content;
   var maxHeight;
   if (this.pfeil==null)
      maxHeight = content.clientHeight;
   else
      maxHeight = content.clientHeight + this.pfeil.clientHeight; //damit die Pfeilgrafik den unteren Textabschnitt nicht verdraengt
      
   container.tweenUp=new Tween(container,'height', Tween.regularEaseIn, maxHeight, 0, duration);
   container.tweenDown=new Tween(container,'height', Tween.regularEaseIn, 0, maxHeight, duration);
   container.tweenDown.onMotionChanged=container.tweenUp.onMotionChanged=function(){
      frame.style.height=container.height+'px';
      content.style.top=(container.height-maxHeight)+'px';
      if (container.onMotion!=null)
         container.onMotion();
   }
   container.tweenDown.onMotionFinished=container.tweenUp.onMotionFinished=function(){
      container.running=false;
      container.direction*=(-1);
      if (container.direction==1)
         content.style.display='none';
      if (container.onStop!=null)
         container.onStop();
   }
   container.tweenDown.onMotionStarted=container.tweenUp.onMotionStarted=function(){
      var pfeil=container.pfeil;

      if (container.direction==1)
      {
         content.style.display='block';
         if (pfeil!=null)
            pfeil.src=container.pfeilObenSrc;
      }
      else if (pfeil!=null)
         pfeil.src=container.pfeilUntenSrc;
      container.running=true;
      
      if (container.onStart!=null)
         container.onStart();
   }
   frame.style.overflow='hidden';
   content.style.top=(-maxHeight)+'px';
   frame.style.height='0';
   content.style.display='none'; //performance
   
   //frame und content sind quasi unsichtbare divs
   frame.style.border='0';
   content.style.border='0';
   frame.style.padding='0';
   content.style.padding='0';
   frame.style.margin='0';
   content.style.margin='0';
}


Klappe.prototype.click=function ()
{
   if (this.disabled)
      return;

   var container=this;
   if (!container.running)
   {
      if (container.direction==-1)
         container.tweenUp.start();
      else
         container.tweenDown.start();
   }
   else
   {
      if (container.direction==1)
      {
         container.tweenDown.stop();
         container.tweenUp.resume();
         container.direction=-1
      }
      else
      {
         container.tweenUp.stop();
         container.tweenDown.resume();
         container.direction=1;
      }
   }
}

//hilfsfunktionen

var uniqueIdVar=0;
function getUniqueId()
{
   uniqueIdVar++;
   return uniqueIdVar;
}

function removeNode(node)
{
   if (node.parentNode)
      node.parentNode.removeChild(node);
}

