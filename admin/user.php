<?php
if (!$loginmanager->is_admin())
   {
      echo '<br />Kein Zugriff. Bitte melde dich als Administrator an.';
      die();
   }
?>
<script type="text/javascript" language="javascript" src="../javascript/develop.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/util.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/fader.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/multixhr.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/aktion.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/frame.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/popup.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/popupfkt.js"></script>
<script type="text/javascript" language="javascript" src="../javascript/user.js"></script>
<script type="text/javascript" language="javascript">

//Frames
var userNavi;
var userDetail;

//Aktionen
var anlegen;
var bearbeiten;
var loeschen;

function init()
{
   userNavi=new frame('user_navi.php','usernavi');
   userNavi.load();
   
   userDetail=new frame('user_detail.php','userdetail');
   userDetail.onRefresh=initUserdetail;
   userDetail.load();
   
   anlegen=new aktion('anlegen_account', userNavi);
   anlegen.form='userform';
   anlegen.stdResponseId='status';
   anlegen.onResponse=function(status, response){
      softAlert(response);
   }
   
   bearbeiten=new aktion('bearbeiten_account', userDetail);
   bearbeiten.form='userform';
   bearbeiten.autoClear=false;
   bearbeiten.stdResponseId='status';
   bearbeiten.onResponse=function(status, response){
      softAlert(response);
   }
   
   loeschen=new aktion('loeschen_account', userNavi);
   loeschen.form='userform';
   loeschen.stdResponseId='status';
   loeschen.onResponse=function(status, response){
      if (status==0)
         softAlert(response);
      else
         userDetail.navigate('user_detail.php');
   }
}


function initUserdetail()
{
   statusToBox();
}

</script>

<h2>Der Usermanager</h2>
<div id="usernavi"></div>
Letzte Meldung:
<div id="status">Keine</div>
<div id="userdetail"></div>
