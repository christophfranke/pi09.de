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
var ligaNavi;
var ligaDetail

//Aktionen
var ligaAnlegen;
var ligaBearbeiten;
var ligaLoeschen;

function init()
{
   //Frames initialisieren
   ligaNavi=new frame('liga_navi.php','liganavi');
   ligaNavi.load();
   
   ligaDetail=new frame('liga_detail.php','ligadetail');
   ligaDetail.load();
   
   //Aktionen initialisieren
   ligaAnlegen=new aktion('anlegen_liga',ligaNavi);
   ligaAnlegen.form='ligaform';
   ligaAnlegen.stdResponseId='status';
   ligaAnlegen.onResponse=function(status, response){
      if (status==0)
         softAlert(response);
   }
   
   ligaBearbeiten=new aktion('bearbeiten_liga',ligaDetail);
   ligaBearbeiten.stdResponseId='status';
   ligaBearbeiten.form='ligaform';
   ligaBearbeiten.onResponse=function(status, response){
      if (status==0)
         softAlert(response);
   }
   
   ligaLoeschen=new aktion('loeschen_liga',ligaNavi);
   ligaLoeschen.stdResponseId='status';
   ligaLoeschen.form='ligaform';
   ligaLoeschen.onResponse=function(status, response){
      if (status==0)
         softAlert(response);
   }
   
}
</script>
<h2>Der Ligamanager</h2>
<h3>&Uuml;bersicht Ligen</h3>
<div id="liganavi"></div>
<br />
<div id="status"></div>
<div id="ligadetail"></div>
