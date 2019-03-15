//Browser erkennung
/* User Agent (Browserkennung) auf einen bestimmten Browsertyp prüfen */
function isAgent(name)
{
   var agent = navigator.userAgent.toLowerCase();
   if (agent.indexOf(name.toLowerCase())>-1) {
      return true;
   }
   return false;
}
//Derzeit unschöne Lösung, das modul deaktiviert sich automatisch,
//falls der Internet Explorer erkannt wurde


//fader objekt ------------------------------------------------------
function fader(eid)
{
   //User Konfiguration
   this.disabled=false; //Setzte auf true deaktiviert den fader.
   this.time=500.0;     //Die Zeit, die der Fader braucht um komplett zu faden
   this.delay=50;       //erneuere das bild alle {delay} millisekunden.
   this.maxFadeIn=1;    //Ab diesem Opacitywert gilt das Objekt als eingefadet
   this.maxFadeOut=0;   //bzw ausgefadet. Beide Werte müssen zwischen 0 und 1 liegen.
   
   //Eventhandler
   this.onFadeIn=null;  //Wenn fertig eingefadet...
   this.onFadeOut=null; //Wenn fertig ausgefadet...
   
   //Interne Variablen
   this.busy=false;     //busy=true heisst "es wird gerade gefadet"
   this.eid=eid;        //Um diese element id geht es hier
   
   if (isAgent('MSIE')) //fader funktioniert nicht mit Internet Exporer
      this.disabled=true;

   this.fadeIn=fadein;
   this.fadeOut=fadeout;
   this.stop=fadestop;
}




function fade(amount, oldValue, fadeobj)
{
   obj=document.getElementById(fadeobj.eid);
   if (obj==null)
   {
      produceError("fade: fade object lost, eid="+fadeobj.eid);
      return;
   }
   if (fadeobj.maxFadeIn>1 || fadeobj.maxFadeOut<0 || fadeobj.maxFadeOut>fadeobj.maxFadeIn)
   {
      produceError('Ungültige Werte für maxFadeOut und maxFadeIn ('+fadeobj.maxFadeIn+', '+fadeObj.maxFadeOut+')');
      return;
   }

   newValue = oldValue + amount;

   if (newValue<fadeobj.maxFadeOut)
      newValue=fadeobj.maxFadeOut;

   if (newValue>fadeobj.maxFadeIn)
      newValue=fadeobj.maxFadeIn;

   obj.style.opacity=newValue;
   //obj.style.filter="alpha(opacity = " + 100*newValue + ")"; //kompatibilität zu IE klapp sowieso nicht

   if (newValue>fadeobj.maxFadeOut && newValue<fadeobj.maxFadeIn)
      fadeobj.busy=setTimeout(fade, fadeobj.delay, amount, newValue, fadeobj);
   else
   {
      if (newValue==fadeobj.maxFadeOut && fadeobj.onFadeOut!=null)
         fadeobj.onFadeOut();
      if (newValue==fadeobj.maxFadeIn && fadeobj.onFadeIn!=null)
         fadeobj.onFadeIn();
   }
}

function fadein()
{
   if (this.disabled || isAgent('MSIE'))
      return;
   var fadestatus=this.maxFadeOut;
   var amount=this.delay/this.time;
   if (this.busy!=false)
   {
      clearTimeout(this.busy);
      this.busy=false;
      //Chromiumbug: In bestimmten Lokalisationen wird hier ein Komma statt einem Punkt zur Zahlendarstellung verwendet
      //und wird dann von parseFloat nicht mehr als Float erkannt
      var opacity=document.getElementById(this.eid).style.opacity.replace(',','.');
      fadestatus=parseFloat(opacity);
   }
   fade(amount, fadestatus, this);
}

function fadeout()
{
   if (this.disabled || isAgent('MSIE'))
      return;
   var fadestatus=this.maxFadeIn;
   var amount=-this.delay/this.time;
   if (this.busy!=false)
   {
      clearTimeout(this.busy);
      this.busy=false;
      //Chromiumbug: In bestimmten Lokalisationen wird hier ein Komma statt einem Punkt zur Zahlendarstellung verwendet
      //und wird dann von parseFloat nicht mehr als Float erkannt
      var opacity=document.getElementById(this.eid).style.opacity.replace(',','.');
      fadestatus=parseFloat(opacity);
   }
   fade(amount, fadestatus, this);
}

function fadestop()
{
   var self=this;
   if (this==null)
   {
      produceError('fadestop: Contexterror, this is null');
      return;
   }
   if (self.busy!=false)
      clearTimeout(self.busy);
      
   self.busy=false;
}
//ende fader objekt -------------------------------------------

