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
   document.getElementById('terminform').header.disabled=true;
}

function enableHeader()
{
   document.getElementById('terminform').header.disabled=false;
}

function disableLiga()
{
   document.getElementById('terminform').liga.disabled=true;
}

function enableLiga()
{
   document.getElementById('terminform').liga.disabled=false;
}

function disableGegner()
{
   document.getElementById('terminform').gegner.disabled=true;
}

function enableGegner()
{
   document.getElementById('terminform').gegner.disabled=false;
}

var header='';
function fillHeader()
{
   var tempHeader=null;
   if (document.getElementById('terminform').header.disabled==false)
      tempHeader=document.getElementById('terminform').header.value;

   if (document.getElementById('terminform').typ[0].checked)
      document.getElementById('terminform').header.value='Spiel gegen ' + document.getElementById('terminform').gegner.value;
   if (document.getElementById('terminform').typ[1].checked)
      document.getElementById('terminform').header.value='Training';
   if (document.getElementById('terminform').typ[2].checked)
      document.getElementById('terminform').header.value=header;

   if (tempHeader!=null)
      header=tempHeader;
}

function initHeader()
{
   header=document.getElementById('terminform').header.value;
}

var gegner='';
function fillGegner()
{
   var tempGegner=null;
   if (document.getElementById('terminform').gegner.disabled==false)
      tempGegner=document.getElementById('terminform').gegner.value;

   if (document.getElementById('terminform').typ[0].checked)
      document.getElementById('terminform').gegner.value=gegner;
   if (document.getElementById('terminform').typ[1].checked)
      document.getElementById('terminform').gegner.value='';
   if (document.getElementById('terminform').typ[2].checked)
      document.getElementById('terminform').gegner.value='';

   if (tempGegner!=null)
      gegner=tempGegner;   
}

function initGegner()
{
   gegner=document.getElementById('terminform').gegner.value;
}

function copyGegner()
{
   if (document.getElementById('terminform').typ[0].checked)
      document.getElementById('terminform').header.value='Spiel gegen ' + document.getElementById('terminform').gegner.value;
      
   gegner=document.getElementById('terminform').gegner.value;
}

//wandelt das Terminformular in ein Objekt vom Typ Termin um und speichert die Formulardaten
//eleganter wäre, ein Objekt zu bauen, dass für jedes Formular automatisch erkennt, welche Daten gespeichert werden müssen
function termin()
{
   //Textfelder
   this.ID=document.getElementById('terminform').ID.value;
   this.ort=document.getElementById('terminform').ort.value;
   this.gegner=document.getElementById('terminform').gegner.value;
   this.header=document.getElementById('terminform').header.value;
   this.inhalt=document.getElementById('terminform').inhalt.value;
   
   //Zeitfelder
   this.stunde=document.getElementById('terminform').stunde.value;
   this.minute=document.getElementById('terminform').minute.value;
   this.tag=document.getElementById('terminform').tag.value;
   this.monat=document.getElementById('terminform').monat.value;
   this.jahr=document.getElementById('terminform').jahr.value;

   //liga   
   this.liga=document.getElementById('terminform').liga.selectedIndex;

   //typ
   var typ=document.getElementById('terminform').typ;
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


