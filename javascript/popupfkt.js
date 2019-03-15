//Diese Funktion ermöglicht das Registrierfenster
var registerWindow;
function registerPopup(onCloseArg)
{
   registerWindow=new popup();
   registerWindow.top=20;
   registerWindow.left='center';
   
   registerWindow.windowStyle.border='solid 1px #BBBBBB';
   registerWindow.windowStyle.backgroundColor='#F0F0F0';
   registerWindow.windowStyle.minWidth='250px';
      
   registerWindow.onClose=onCloseArg;
   registerWindow.uri='fenster/register.php';
   
   registerWindow.show();
}

var zusageWindow;
function zusagePopup(id, adminUri)
{
   zusageWindow=new popup();
   zusageWindow.top='center';
   zusageWindow.left='center';
   
   zusageWindow.windowStyle.border='solid 1px #BBBBBB';
   zusageWindow.windowStyle.backgroundColor='#F0F0F0';
   zusageWindow.windowStyle.padding='10px';
   
   zusageWindow.uri='fenster/zusage.php?id='+id;
   //Falls das popup im adminunterordner aufgerufen wird, muss der relative pfad korregiert werden
   if (adminUri==true)
      zusageWindow.uri='../fenster/zusage.php?id='+id;
   
   zusageWindow.show();
}


var softAlertWindow=null;
function softAlertCaption(msg, onCloseArg)
{
   softAlertWindow=new popup();
   softAlertWindow.top=window.innerHeight/4;
   softAlertWindow.left='center';
   if (isAgent('MSIE'))
      softAlertWindow.top=document.clientHeight/4;
   
   softAlertWindow.windowStyle.border='solid 1px #BBBBBB';
   softAlertWindow.windowStyle.backgroundColor='#F0F0F0';
   softAlertWindow.windowStyle.minWidth='250px';
   
   softAlertWindow.DOMtree=document.createElement('div');
   var header=document.createElement('div');
   var body=document.createElement('div');
   var button=document.createElement('input');
   var br1=document.createElement('br');
   var br2=document.createElement('br');
   
   header.style.backgroundColor='#444444';
   header.style.color='#EEEEEE';
   header.style.padding='5px';
   header.innerHTML='Die Seite meldet :';
   body.style.backgroundColor='#F0F0F0';
   body.style.padding='5px';
   body.innerHTML=msg;
   br1.style.clear='both';
   button.type='button';
   button.value='OK';
   button.style.cssFloat='right';
   button.onblur=function(){button.focus();} //Funktioniert nicht mit Firefox
   body.appendChild(br1);
   body.appendChild(br2);
   body.appendChild(button);
      
   softAlertWindow.DOMtree.appendChild(header);
   softAlertWindow.DOMtree.appendChild(body);
   
   softAlertWindow.closeButton=button;
   softAlertWindow.onClose=onCloseArg;
   
   softAlertWindow.show();
}


function softAlert(msg, onCloseArg)
{
   softAlertWindow=new popup();
   softAlertWindow.top=window.innerHeight/4;
   softAlertWindow.left='center';
   if (isAgent('MSIE'))
      softAlertWindow.top=document.clientHeight/4;
   
   softAlertWindow.windowStyle.border='solid 1px #BBBBBB';
   softAlertWindow.windowStyle.backgroundColor='#F0F0F0';
   softAlertWindow.windowStyle.minWidth='250px';
   
   softAlertWindow.DOMtree=document.createElement('div');
   var body=document.createElement('div');
   var button=document.createElement('input');
   var br1=document.createElement('br');
   var br2=document.createElement('br');
   
   body.style.backgroundColor='#F0F0F0';
   body.style.padding='5px';
   body.innerHTML=msg;
   br1.style.clear='both';
   button.type='button';
   button.value='OK';
   button.style.cssFloat='right';
   button.onblur=function(){button.focus();} //Funktioniert nicht mit Firefox
   body.appendChild(br1);
   body.appendChild(br2);
   body.appendChild(button);
      
   softAlertWindow.DOMtree.appendChild(body);
   
   softAlertWindow.closeButton=button;
   softAlertWindow.onClose=onCloseArg;
   
   softAlertWindow.show();
}

var softConfirmWindow=null;
function softConfirm(msg, onTrue, onFalse)
{
   softConfirmWindow=new popup();
   softConfirmWindow.top=document.body.clientHeight/4;
   softConfirmWindow.left='center';
   
   softConfirmWindow.windowStyle.border='solid 1px #BBBBBB';
   softConfirmWindow.windowStyle.backgroundColor='#F0F0F0';
   softConfirmWindow.windowStyle.minWidth='250px';
   
   softConfirmWindow.DOMtree=document.createElement('div');
   var header=document.createElement('div');
   var body=document.createElement('div');
   var okButton=document.createElement('input');
   var cancelButton=document.createElement('input');
   var br1=document.createElement('br');
   var br2=document.createElement('br');
   
   header.style.backgroundColor='#444444';
   header.style.color='#EEEEEE';
   header.style.padding='5px';
   header.innerHTML='Bitte best&auml;tigen:';
   body.style.backgroundColor='#F0F0F0';
   body.style.padding='5px';
   body.innerHTML=msg;
   br1.style.clear='both';
   okButton.type='button';
   okButton.value='OK';
   okButton.style.cssFloat='right';
   okButton.onblur=function(){okButton.focus();} //Funktioniert nicht mit Firefox
   cancelButton.type='button';
   cancelButton.value='Abbrechen';
   cancelButton.style.cssFloat='right';
   body.appendChild(br1);
   body.appendChild(br2);
   body.appendChild(okButton);
   body.appendChild(cancelButton);
      
   softConfirmWindow.DOMtree.appendChild(header);
   softConfirmWindow.DOMtree.appendChild(body);
   
   softConfirmWindow.defaultButton=okButton; //Keinen Closebutton definieren um den Eventhandler nicht zu überschreiben
   okButton.onclick=function(){
      softConfirmWindow.onClose=onTrue;
      softConfirmWindow.close(true);
   }
   
   cancelButton.onclick=function(){
      softConfirmWindow.onClose=onFalse;
      softConfirmWindow.close(false);
   }
   
   softConfirmWindow.show();
}


