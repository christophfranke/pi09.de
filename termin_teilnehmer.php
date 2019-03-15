<?php
@session_start() or die('Could not start session in '.__FILE);

if (empty($_GET['id']))
{
   echo 'Kein Termin ausgew&auml;hlt.';
}
else
{
   include 'inc/all.php';
   $loginmanager=new loginmanager();
   $einladungsmanager=new einladungsmanager($loginmanager);
   $id=$_GET['id'];
   
   $termin=new termin();
   $termin->ID=$id;
   $einladungsliste=$einladungsmanager->anzeigen($termin->ID);
   
   if ($einladungsliste->size()==0)
      echo 'Keine Teilnehmer.';
   else
   {
      foreach($einladungsliste as $einladung)
         if ($einladung->zusageID==ZUSAGE_JA)
         {
            if ($einladung->accountID==$loginmanager->ID or $loginmanager->is_admin())
               echo '<a href="javascript:zusagePopup('.$einladung->ID.');"><img src="img/ja_small.png" /> '.$einladung->name().'</a><br />';
            else
               echo '<img src="img/ja_small.png" /> '.$einladung->name().'<br />';
         }
      echo '<br />';
      foreach($einladungsliste as $einladung)
         if ($einladung->zusageID==ZUSAGE_VIELLEICHT)
         {
            if ($einladung->accountID==$loginmanager->ID or $loginmanager->is_admin())
               echo '<a href="javascript:zusagePopup('.$einladung->ID.');"><img src="img/vielleicht_small.png" /> '.$einladung->name().'</a><br />';
            else
               echo '<img src="img/vielleicht_small.png" /> '.$einladung->name().'<br />';
         }
      echo '<br />';
      foreach($einladungsliste as $einladung)
         if ($einladung->zusageID==ZUSAGE_NEIN)
         {
            if ($einladung->accountID==$loginmanager->ID or $loginmanager->is_admin())
               echo '<a href="javascript:zusagePopup('.$einladung->ID.');"><img src="img/nein_small.png" /> '.$einladung->name().'</a><br />';
            else
               echo '<img src="img/nein_small.png" /> '.$einladung->name().'<br />';
         }
      echo '<br />';
      echo '<hr />';
      echo '<br />';
      foreach($einladungsliste as $einladung)
         if ($einladung->zusageID==ZUSAGE_KEINEANTWORT)
         {
            if ($einladung->accountID==$loginmanager->ID or $loginmanager->is_admin())
               echo '<a href="javascript:zusagePopup('.$einladung->ID.');"><img src="img/unbeantwortet_small.png" /> '.$einladung->name().'</a><br />';
            else
               echo '<img src="img/unbeantwortet_small.png" /> '.$einladung->name().'<br />';
         }
         
   }
   
   
}
?>
