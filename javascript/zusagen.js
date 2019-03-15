//Diese Funktion ermöglicht das Fenster, in dem man Terminen zu/absagen kann
var zusagenWindow;
function zusagenPopup(onCloseArg)
{
   zusagenWindow=new popup();
   zusagenWindow.top=20;
   zusagenWindow.left='center';
   
   zusagenWindow.windowStyle.border='solid 1px #BBBBBB';
   zusagenWindow.windowStyle.backgroundColor='#F0F0F0';
   zusagenWindow.windowStyle.minWidth='250px';
      
   zusagenWindow.onClose=onCloseArg;
   zusagenWindow.uri='/phrasendrescher/fenster/zusagen.php';
   
   zusagenWindow.show();
}

