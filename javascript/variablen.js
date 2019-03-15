//Ein Modul zum Transportieren von Variaben über XMHttpRequest
//Um die Variablen zu transportieren, legt der remote ein Formular mit id="sendVars" an.
//In diesem Formular werden alle tags input mit type="hidden" gelesen und die variablen
//dann von readInputVars als eigenschaften zu inputVars hinzugefügt und entsprechend gefüllt.
//Es gibt keine Funktion, die das Senden erleichtern kann, weil es technisch unmöglich wäre, diese auszuführen.
var inputVars=new Object();

//Einlesen der Variablen
function readInputVars()
{
   var form=document.getElementById('sendVars');
   if (form==null)
      return;
      
   var e;
   for(var i=0;i<form.elements.length;i++)
   {
      e=form.elements[i];
      if (e.type=='hidden' && e.name!=null && e.value!=null)
         inputVars[e.name]=e.value;
         
   }
   removeNode(form);
}
