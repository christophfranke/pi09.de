<?php
if (!$loginmanager->is_admin())
   {
      echo '<br />Kein Zugriff. Bitte melde dich als Administrator an.';
      die();
   }
?>
<!--  Alle Javascript Module Laden  -->
<script type="text/javascript" language="javascript" src="../javascript/develop.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/util.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/fader.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/multixhr.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/aktion.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/frame.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/popup.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/popupfkt.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/ergebnis.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/variablen.js"></script>

<script type="text/javascript" languabe="javascript">

//Aktionen
var loeschen;
var bearbeiten;
var kommentieren;
var anlegen;

//Frames
var ergebnisNavi;
var ergebnisDetail;
var kommentarFrame;
var einladungenFrame;

//InitFunktion
function init()
{
   ergebnisNavi=new frame('ergebnis_navi.php','ergebnisnavi');
   ergebnisNavi.load();
   
   ergebnisDetail=new frame('ergebnis_detail.php','ergebnisdetail',initErgebnisdetail);
   ergebnisDetail.load();
   
   kommentarFrame=new frame('ergebnis_kommentare.php','kommentare');
   einladungenFrame=new frame('ergebnis_einladungen.php','einladungen');

   loeschen=new aktion('loeschen_termin', ergebnisNavi);
   loeschen.form='loeschform';
   loeschen.stdResponseId='status';
   loeschen.onResponse=function(status, response){
      softAlert(response);
      if (status==1)
         terminDetail.navigate('ergebnis_detail.php');
   }
   
   bearbeiten=new aktion('bearbeiten_ergebnis', ergebnisDetail);
   bearbeiten.form='ergebnisform';
   bearbeiten.debug=false;
   bearbeiten.stdResponseId='status';
   bearbeiten.autoClear=false;
   bearbeiten.onResponse=function(status,response){softAlert(response);}
   
}

function kommentarLoeschen(id)
{
   var form=document.createElement('form');
   var daten=document.createElement('input');
   daten.type='hidden';
   daten.name='ID';
   daten.value=id;
   form.appendChild(daten);
   document.body.appendChild(form);

   form.id='tempKommentarDelForm';
   var tempAktion=new aktion('loeschen_kommentar',kommentarFrame);
   tempAktion.form='tempKommentarDelForm';
   tempAktion.stdResponseId='status';
   tempAktion.onResponse=function(status, response){
      softAlert(response);
   }

   tempAktion.send();
   
   removeNode(form);
}

function spielerEinladen(id)
{
   aktionAusfuehren('einladen_spieler',id);
}

function alleEinladen(id)
{
   aktionAusfuehren('einladen_alle',id);
}

function benutzerAusladen(id)
{
   aktionAusfuehren('loeschen_einladung',id);
}

function alleAusladen(id)
{
   aktionAusfuehren('ausladen_alle',id);
}

function aktionAusfuehren(aktionString, id)
{
   var form=document.createElement('form');
   var daten=document.createElement('input');
   daten.type='hidden';
   daten.name='ID';
   daten.value=id;
   form.appendChild(daten);
   document.body.appendChild(form);
   
   form.id='tempAktionForm';
   var tempAktion=new aktion(aktionString,einladungenFrame);
   tempAktion.form='tempAktionForm';   
   tempAktion.stdResponseId='status';
   tempAktion.onResponse=function(status, response){
      if (status==0)
         softAlert(response);
   }  

   tempAktion.send();
   
   removeNode(form);
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
   tempAktion=new aktion('einladung_zusagen',einladungenFrame);
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

function initErgebnisdetail()
{
   readInputVars();
   var bearbeiten=inputVars['bearbeiten'];
   var id=inputVars['id'];
   var terminTyp=inputVars['terminTyp'];
   var spielTyp=inputVars['spielTyp'];
   
   if (bearbeiten=='false')
      return;

   //Kommentarfunktionen und Einladungen gibt es nur beim bearbeiten
   kommentarFrame.navigate('ergebnis_kommentare.php?id='+id);
   einladungenFrame.navigate('ergebnis_einladungen.php?id='+id);
         
   kommentieren=new aktion('anlegen_kommentar', kommentarFrame);
   kommentieren.form='kommentarform';
   kommentieren.stdResponseId='status';
   kommentieren.onResponse=function(status, response){ if (status==0) softAlert(response);}

   //Ab hier werden nur noch Werte gesetzt, die sich auf Terminbearbeiten beziehen
   initHeader();
   initGegner();
   copyGegner();

   //Spieltyp setzen
   var liga=document.getElementById('ergebnisform').liga;
   for (i=0;i<liga.length;i++)
   {
      if (liga[i].value==spielTyp)
         liga.selectedIndex=i;
   }

   
   //Termintyp setzen
   var typ=document.getElementById('ergebnisform').typ;
   for (i=0;i<typ.length;i++)
   {
      if (typ[i].value==terminTyp)
      {
         typ[i].checked=true;
         if (i==0)
            selectSpiel();
         if (i==1)
            selectTraining();
         if (i==2)
            selectFrei();
      }
         
   }

}

var terminUnveraendert;

function terminLoeschen()
{
   softConfirm('Soll dieser Termin wirklich gel&ouml;scht werden?',function(){loeschen.send();});
}


function verlassen()
{
   var terminJetzt=new termin();
   if (!compare(terminJetzt, terminUnveraendert))
      softConfirm('Du hast deine &Auml;nderungen nicht gespeichert. Wenn du diese Seite verl&auml;sst, gehen deine &Auml;nderungen verloren. Seite jetzt Verlassen?',function(){window.location='termin.php';});
   else
      window.location='termin.php';
}
</script>

<h2>Der Ergebnismanager</h2>
<div id="ergebnisnavi"></div>
<br /><div id="status"></div>
<div id="ergebnisdetail"></div>
