function selectSpiel()
{
   fillGegner();
   fillHeader();
   
   enableLiga();
   disableHeader();
   enableGegner();
}

function selectTraining()
{
   fillGegner();
   fillHeader();
   
   disableLiga();
   disableHeader();
   disableGegner();
}

function selectFrei()
{
   fillGegner();
   fillHeader();
   
   disableLiga();
   enableHeader();
   disableGegner();
}


function disableHeader()
{
   document.getElementById('ergebnisform').header.disabled=true;
}

function enableHeader()
{
   document.getElementById('ergebnisform').header.disabled=false;
}

function disableLiga()
{
   document.getElementById('ergebnisform').liga.disabled=true;
}

function enableLiga()
{
   document.getElementById('ergebnisform').liga.disabled=false;
}

function disableGegner()
{
   document.getElementById('ergebnisform').gegner.disabled=true;
}

function enableGegner()
{
   document.getElementById('ergebnisform').gegner.disabled=false;
}

var header='';
function fillHeader()
{
   var tempHeader=null;
   if (document.getElementById('ergebnisform').header.disabled==false)
      tempHeader=document.getElementById('ergebnisform').header.value;

   if (document.getElementById('ergebnisform').typ[0].checked)
      document.getElementById('ergebnisform').header.value='Spiel gegen ' + document.getElementById('ergebnisform').gegner.value;
   if (document.getElementById('ergebnisform').typ[1].checked)
      document.getElementById('ergebnisform').header.value='Training';
   if (document.getElementById('ergebnisform').typ[2].checked)
      document.getElementById('ergebnisform').header.value=header;

   if (tempHeader!=null)
      header=tempHeader;
}

function initHeader()
{
   header=document.getElementById('ergebnisform').header.value;
}

var gegner='';
function fillGegner()
{
   var tempGegner=null;
   if (document.getElementById('ergebnisform').gegner.disabled==false)
      tempGegner=document.getElementById('ergebnisform').gegner.value;

   if (document.getElementById('ergebnisform').typ[0].checked)
      document.getElementById('ergebnisform').gegner.value=gegner;
   if (document.getElementById('ergebnisform').typ[1].checked)
      document.getElementById('ergebnisform').gegner.value='';
   if (document.getElementById('ergebnisform').typ[2].checked)
      document.getElementById('ergebnisform').gegner.value='';

   if (tempGegner!=null)
      gegner=tempGegner;   
}

function initGegner()
{
   gegner=document.getElementById('ergebnisform').gegner.value;
}

function copyGegner()
{
   if (document.getElementById('ergebnisform').typ[0].checked)
      document.getElementById('ergebnisform').header.value='Spiel gegen ' + document.getElementById('ergebnisform').gegner.value;
      
   gegner=document.getElementById('ergebnisform').gegner.value;
}

//wandelt das ergebnisformular in ein Objekt vom Typ Termin um und speichert die Formulardaten
//eleganter wäre, ein Objekt zu bauen, dass für jedes Formular automatisch erkennt, welche Daten gespeichert werden müssen
function termin()
{
   //Textfelder
   this.ID=document.getElementById('ergebnisform').ID.value;
   this.ort=document.getElementById('ergebnisform').ort.value;
   this.gegner=document.getElementById('ergebnisform').gegner.value;
   this.header=document.getElementById('ergebnisform').header.value;
   this.inhalt=document.getElementById('ergebnisform').inhalt.value;
   
   //Zeitfelder
   this.stunde=document.getElementById('ergebnisform').stunde.value;
   this.minute=document.getElementById('ergebnisform').minute.value;
   this.tag=document.getElementById('ergebnisform').tag.value;
   this.monat=document.getElementById('ergebnisform').monat.value;
   this.jahr=document.getElementById('ergebnisform').jahr.value;

   //liga   
   this.liga=document.getElementById('ergebnisform').liga.selectedIndex;

   //typ
   var typ=document.getElementById('ergebnisform').typ;
   for (var i=0;i<typ.length;i++)
   {
      if (typ[i].checked)
         this.typ=i;
   }
}


function compare(a,b)
{
   for(var key in a)
   {
      if (a[key]!==b[key])
         return false;
   }
   return true;
}


