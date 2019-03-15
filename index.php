<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
 <title>Phrasendrescher</title>

<script type="text/javascript" language="javascript" src="javascript/develop.js"></script>
<script type="text/javascript" language="javascript" src="javascript/util.js"></script>
<script type="text/javascript" language="javascript" src="javascript/browserdetect.js"></script>
<script type="text/javascript" language="javascript" src="javascript/tween.js"></script>
<script type="text/javascript" language="javascript" src="javascript/klappe.js"></script>
<script type="text/javascript" language="javascript" src="javascript/fader.js"></script>
<script type="text/javascript" language="javascript" src="javascript/multixhr.js"></script>
<script type="text/javascript" language="javascript" src="javascript/frame.js"></script>
<script type="text/javascript" language="javascript" src="javascript/aktion.js"></script>
<script type="text/javascript" language="javascript" src="javascript/popup.js"></script>
<script type="text/javascript" language="javascript" src="javascript/popupfkt.js"></script>
<script type="text/javascript" language="javascript" src="javascript/variablen.js"></script>

<script type="text/javascript" language="javascript">

//Frames
var contentFrame;
var loginFrame;
//terminFrames
var terminFrame;
var terminkalenderFrame;
var terminnaviFrame;
var termindetailFrame;
var terminteilnehmerFrame;
//ergebnisFrames
var ergebnisFrame;
var ergebniskalenderFrame;
var ergebnisnaviFrame;
var ergebnisdetailFrame;

//Aktionen
var login;
var loginStart;
var logout;
var register;
var kommentarAnlegen;
var kommentarLoeschen;
var userBearbeiten;

//Angaben zur Navi
var currentContent=1;

function init()
{
   //Frames erstellen
   
   //loginbox laden
   loginFrame=new frame('login.php','loginbox');
   loginFrame.load();

   //startseite laden
   contentFrame=new frame('start.php','contentbox', initStart);
   contentFrame.load();
   
   //terminseite vorbereiten
   terminkalenderFrame=new frame('termin_kalender.php','terminkalender');
   terminnaviFrame=new frame('termin_navi.php','terminnavi');
   termindetailFrame=new frame('termin_detail.php','termindetail',initTermindetail);
   termindetailFrame.alwayLoadThis=true;
   terminteilnehmerFrame=new frame('termin_teilnehmer.php','termindetail_teilnehmer',initTermindetailTeilnehmer);
   termindetailFrame.addSubFrame(terminteilnehmerFrame);
   
   //ergebnisseite vorbereiten
   ergebniskalenderFrame=new frame('ergebnis_kalender.php','terminkalender', initErgebniskalender);
   ergebnisnaviFrame=new frame('ergebnis_navi.php','terminnavi');
   ergebnisdetailFrame=new frame('ergebnis_detail.php','termindetail');
   ergebnisdetailFrame.alwaysLoadThis=true;
   
   
   //-------------------------
   
   //Aktionen erstellen
   
   //login
   login=new aktion('login',contentFrame);
   login.form='loginform';
   login.onResponse=function(status, response){
      if (status==1)
         loginFrame.navigate('login_welcome.php');
      else
         softAlert(response);
   }
   
   //login auf der startseite
   loginStart=new aktion('login', loginFrame, contentFrame);
   loginStart.form='loginstartform';
   loginStart.onResponse=function(status, response){
      if (status==0)
         softAlert(response);
   }
   
   //logout
   logout=new aktion('logout',contentFrame);
   logout.onResponse=function(status, response){
      if (status==1)
         loginFrame.navigate('login.php');
      else
         softAlert(response);
   }
   
   //registrieren
   register=new aktion('register',contentFrame, loginFrame);
   register.form='userform';
   register.onResponse=function(status, response){
      if (status==0)
         softAlert(response,function(){document.getElementById('registerbutton').disabled=false;});
      else
         registerWindow.close();
   }
   
   //kommentieren
   kommentarAnlegen=new aktion('anlegen_kommentar');
   kommentarAnlegen.onResponse=function(status, response){
      if (status==0)
      {
         document.getElementById('kommentierbutton').disabled=false;
         softAlert(response);
      }
      else
      {
         if (currentContent==2)
            termindetailFrame.refresh();
         if (currentContent==3)
            ergebnisdetailFrame.refresh();
      }
   }
   kommentarAnlegen.form='kommentarform';
   
   //Kommentar löschen
   kommentarLoeschen=new aktion('loeschen_kommentar', termindetailFrame)
   kommentarLoeschen.onResponse=function(status, response){
      if (status==0)
         softAlert(response);
   }
   
   //Kontodaten bearbeiten
   userBearbeiten=new aktion('bearbeiten_account');
   userBearbeiten.form='userform';
   userBearbeiten.autoClear=false;
   userBearbeiten.onResponse=function(status, response){
      softAlert(response);
   }
   
   //Passwort ändern
   passwortBearbeiten=new aktion('bearbeiten_passwort');
   passwortBearbeiten.form='passwortform';
   passwortBearbeiten.onResponse=function(status, response){
      softAlert(response);
   }
   //------------------------------
   
   
   loadBackground();
}


function loadBackground()
{
   var backgrounds=['1024','1280','1366','1400','1440','1600','1680','1920','2048','2560','3840'];
   var screensize=screen.width;
   var k=0;
   for (i in backgrounds)
   {
      if (backgrounds[i]>=screensize)
      {
         k=parseInt(i);
         break;
      }
   }
   screensize=backgrounds[k];
   var backgroundImage=document.createElement('img');
   backgroundImage.src="img/hintergrund_"+screensize+".jpg";
   backgroundImage.className="hintergrund";
   document.body.appendChild(backgroundImage);
}


//init funktionen

function initStart()
{
   currentContent=1;
   //subFrames, die zu einem anderem Reiter vom contentFrame loslösen.
   contentFrame.clearSubFrames();
   //initStart nur einmal nötig bei Navigation auf diese Seite
   contentFrame.onRefresh=null;
   
   contentFrame.alwayLoadThis=false;
}


function initTermin()
{
   currentContent=2;   
   
   //eventuelle vorherige subFrames löschen und initTermin nicht nocheinmal ausführen
   contentFrame.clearSubFrames();
   contentFrame.onRefresh=null;

   //subFrames registrieren
   contentFrame.addSubFrame(terminkalenderFrame);
   contentFrame.addSubFrame(terminnaviFrame);
   contentFrame.addSubFrame(termindetailFrame);

   //subFrames anzeigen
   terminkalenderFrame.load();
   terminnaviFrame.load();
   termindetailFrame.load();

   contentFrame.alwayLoadThis=false;
}

function initErgebnis()
{
   currentContent=3;
   
   contentFrame.clearSubFrames();
   contentFrame.onRefresh=null;
   
   contentFrame.addSubFrame(ergebniskalenderFrame);
   contentFrame.addSubFrame(ergebnisnaviFrame);
   contentFrame.addSubFrame(ergebnisdetailFrame);
   
   ergebniskalenderFrame.load();
   ergebnisnaviFrame.load();
   ergebnisdetailFrame.load();

   contentFrame.alwayLoadThis=false;
}

function initKontakt()
{
   contentFrame.alwayLoadThis=true;
}

function initTermindetail()
{
   if (document.getElementById('termindetail_links')==null)
      return;   
      
   //teilnehmer subsubframe füllen
   readInputVars();
   id=inputVars.id;
   terminteilnehmerFrame.navigate('termin_teilnehmer.php?id='+id);
}

var klappObjects;
function initErgebniskalender()
{
   klappObjects = Array();
   
   var year;
   var klappLinks = findAllTags(document.body, 'klappe');
   for(var i = 0;i<klappLinks.length;i++)
   {
      eid = "jahr" + klappLinks[i].getAttribute('klappe');
      klappDiv = document.getElementById(eid);
      klappObj = new Klappe(klappDiv);
      klappObjects.push(klappObj);
      klappLinks[i].href = "javascript:klappObjects["+i+"].click();";
   }
   if(klappObjects.length>0)
      klappObjects[0].click();
}

function initTermindetailTeilnehmer()
{
   var teilnehmer=document.getElementById('termindetail_teilnehmer');
   var balken=document.getElementById('termindetail_balkenrechts');
   var links=document.getElementById('termindetail_links');
   var kommentare=document.getElementById('termindetail_kommentare');
   if (links==null)
   {
       return;
   }
   
   if (links.clientHeight>teilnehmer.clientHeight)
      var height=links.clientHeight;
   else
      var height=teilnehmer.clientHeight;
   
   teilnehmer.style.height=height+'px';
   balken.style.height=height+'px';
   kommentare.style.height=(kommentare.clientHeight+height-links.clientHeight)+'px';
   links.style.height=height+'px';
}

//--------

//verschiedene Funktionen

function kommentarLoeschenFkt(id)
{  
   kommentarLoeschen.form='kommentarloeschen'+id;
   softConfirm('Willst du den Kommentar wirklich l&ouml;schen?',kommentarLoeschenSend);
}

function kommentarLoeschenSend()
{
   kommentarLoeschen.send();
}


function zusagen(id, zusage)
{
   var form=document.createElement('form');
   var daten=document.createElement('input');
   var daten2=document.createElement('input');
   daten.type='hidden';
   daten.name='id';
   daten.value=id;
   daten2.type='hidden';
   daten2.name='zusage';
   daten2.value=zusage;
   form.appendChild(daten);
   form.appendChild(daten2);
   document.body.appendChild(form);
   
   form.id='tempAktionForm';
   var tempAktion;
   if (currentContent==1)
      tempAktion=new aktion('einladung_zusagen',contentFrame);
   else
      tempAktion=new aktion('einladung_zusagen',terminteilnehmerFrame);
   tempAktion.form='tempAktionForm';   
   tempAktion.stdResponseId='status';
   tempAktion.onResponse=function(status, response){
      if (status==0)
         softAlert(response);
      else
         zusageWindow.close();
   }  

   tempAktion.send();
   
   removeNode(form);
}


function kommentierenFkt()
{
   kommentarAnlegen.send();
   document.getElementById('kommentierbutton').disabled=true;
}

</script>

<link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body onload="init();">

<div id="topbox">
 <img src="img/headerlogo.png" alt="" />
 <div id="loginbox" class="transparent"></div>
 <div id="reiterbox">
  <a class="transparent" href="javascript:contentFrame.navigate('start.php',initStart);">Start</a>
  <a class="transparent" href="javascript:contentFrame.navigate('termin.php',initTermin);">Termine</a>
  <a class="transparent" href="javascript:contentFrame.navigate('ergebnis.php',initErgebnis);">Ergebnisse</a>
  <a class="transparent" href="javascript:contentFrame.navigate('kontakt.php', initKontakt);">Kontakt</a>
 </div>
</div>

<div id="contentbox"><h1>Phrasendrescher Ihrefeld, Kölner Fußball Mannschaft</h1></div>

</body>
</html>
