<?php
require('control.php');

class test
{
   //tests werden hier reingeschrieben
   function termin_anlegen()
   {
      $termin=new termin();

      $termin->ort="Ehrenfeld";
      $termin->zeit=time();
      $termin->gegner="Testeintrag";
      $termin->artID=ART_SPIEL;
      $termin->inhalt="Das ist unser Testspiel gegen Gegner x.";

      $db=new db();
      $db->anlegen($termin);

      $liste=$db->liste("gegner='Testeintrag'", 'termin');

      foreach($liste as $eintrag)
         foreach($termin as $key=>$value)
         {
            if ($eintrag->$key!=$value)
            {
               echo "<br/>$key stimmt nicht überein:";
               echo "<br/>soll: $termin";
               echo "<br/>ist: $eintrag";
               $db->clean("gegner='Testeintrag'", 'termin');
               return false;
            }
         }

      $db->clean("gegner='Testeintrag'", 'termin');
      return true;
   }
   
   
   function termin_loeschen()
   {
      $termin=new termin();

      $termin->ort="Ehrenfeld";
      $termin->zeit=time();
      $termin->gegner="Testeintrag";
      $termin->artID=ART_SPIEL;
      $termin->inhalt="Das ist unser Testspiel gegen Gegner x.";

      $db=new db();
      $db->anlegen($termin);
      
      $liste=$db->liste("gegner='Testeintrag'",'termin');
      foreach($liste as $eintrag)
      {
         $db->loeschen($eintrag->ID,'termin');
      }
      $liste=$db->liste("gegner='Testeintrag'",'termin');
      if ($liste->get_size()>0)
      {
         echo "liste hat ".$liste->get_size()." eintr&auml;ge";
         $db->clean("gegner='Testeintrag'",'termin');
         return false;
      }
      return true;
   }
   
   function termin_bearbeiten()
   {
      $termin=new termin();

      $termin->ort="Ehrenfeld";
      $termin->zeit=time();
      $termin->gegner="Testeintrag";
      $termin->artID=ART_SPIEL;
      $termin->inhalt="Das ist unser Testspiel gegen Gegner x.";
      
      $db=new db();
      $liste=$db->liste("1=1",'termin');
      $bearbeiten=$liste[0];
      echo "<br/>bearbeiten hat die klasse: ".get_class($bearbeiten);
      $rollback=$bearbeiten->get_copy();
      foreach($termin as $key=>$value)
         $bearbeiten->$key=$value;
         
      $db->bearbeiten($bearbeiten);
      
      $check=$db->anzeigen($bearbeiten->ID,'termin');
      foreach($termin as $key=>$value)
         if(!$check->$key==$value)
            return false;
            
      $db->bearbeiten($rollback);
      return true;
   }


   //go test
   function run()
   {
      echo "Starte Test: ";
      $func_array=get_class_methods('test');
      $ok=0;
      foreach($func_array as $func)
      {
         if ($func=='run' or $func=='__invoke')
            continue;

         if (!$this->$func())
         {
            echo "<br/>$ok Tests ok, Test fehlgeschlagen: $func";
            return;
         }
         echo "<br/>$func erfolgreich";;
         $ok++;
      }
      echo "<br/><b>$ok Tests erfolgreich</b>";
   }
   
   function __invoke()
   {
      $this->run();
   }

}

$test=new test();
?>
<HTML>
<HEAD>
 <TITLE>Testsuite</TITLE>
</HEAD>
<BODY>
<?php

$test();

?>
</BODY>
</HTML>
