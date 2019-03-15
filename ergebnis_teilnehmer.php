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
   
   $keine_teilnehmer=true;
   foreach($einladungsliste as $einladung)
      if ($einladung->zusageID==ZUSAGE_JA)
      {
         if ($loginmanager->is_admin())
            echo '<a href="javascript:zusagePopup('.$einladung->ID.');"><img src="img/ja_small.png" /> '.$einladung->name().'</a><br />';
         else
            echo '<img src="img/ja_small.png" /> '.$einladung->name().'<br />';
            
         $keine_teilnehmer=false;
      }
         
   if ($keine_teilnehmer)
      echo 'Keine Teilnehmer.';
   
   
}
?>
