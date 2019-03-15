//Fehlerroutine
window.onerror=oe;
function oe(msg,url,line) {
   alert("Fehler in "+url+" Zeile "+line+":\n"+msg);
   return true;
}


function statusToBox()
{
   var userform=document.getElementById('userform');
   if (userform==null)
      return;
      
   var status=parseInt(userform.statusflag.value);
   
   var status_login=(status & 1);
   var status_spieler=(status & 2);
   var status_admin=(status & 4);
   
   if (status_login==1)
      userform.status_login.checked=true;
   else
      userform.status_login.checked=false;
      
   if (status_spieler==2)
      userform.status_spieler.checked=true;
   else
      userform.status_spieler.checked=false;

   if (status_admin==4)
      userform.status_admin.checked=true;
   else
      userform.status_admin.checked=false;
      
}


function boxToStatus()
{
   var userform=document.getElementById('userform');
   if (userform==null)
      return;
      
   var status=0;
   if (userform.status_login.checked)
      status=status+1;
   if (userform.status_spieler.checked)
      status=status+2;
   if (userform.status_admin.checked)
      status=status+4;
   
   userform.statusflag.value=status;
}


