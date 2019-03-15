<?php

//Der Ergebnismanager

interface ergebnismanager_documentation{

   public function bearbeiten(ergebnis $ergebnis, $termin_id); //Bearbeitet ein bestehendes ergebnis oder legt ein neues an, falls nicht vorhanden.
   
   public function meldung(); //Gibt den Status der letzten Fehlermeldung zurück;
}

abstract class ergebnismanager_configuration implements ergebnismanager_documentation{
   protected $sichtbar_default=false; //wenn sichtbarkeit nicht gesetzt ist, dann ist das der Standartwert
}

class ergebnismanager extends ergebnismanager_configuration{

   private $db;
   private $loginmanager;
   
   private $meldung;
   
   public function __construct(loginmanager $loginmanager=null)
   {
      if ($loginmanager==null)
         $this->loginmanager=new loginmanager();
      else
         $this->loginmanager=$loginmanager;
         
      $this->db=new db();
      $this->db->set_null=true; //erlaubt, einmal gesetzte werte wieder zu löschen
   }
   
   public function bearbeiten(ergebnis $ergebnis, $termin_id)
   {
      switch($ergebnis->sichtbar)
      {
         case 'true':
            $ergebnis->sichtbar=true;
            break;
            
         case 'false':
            $ergebnis->sichtbar=false;
            break;
            
         default:
            $ergebnis->sichtbar=$this->sichtbar_default;
            break;
      }
         
      //ermöglicht, die Tore wieder zu löschen
      if ($ergebnis->wirtore==='')
         $ergebnis->wirtore='null';
      if ($ergebnis->dietore==='')
         $ergebnis->dietore='null';

      //der spielbericht darf nicht aus dem einzigen Wort 'null' bestehen, da dies ein Steuerwort ist
      if ($ergebnis->spielbericht==='null')
      {
         $this->meldung=EM_KEIN_NULL;
         return false;
      }
      
      //in Integer konvertieren. hier könnte auf gültigkeit geprüft werden.
      if(isset($ergebnis->ID))
         $ergebnis->ID=(int)$ergebnis->ID;
      if (isset($termin_id))
         $termin_id=(int)$termin_id;
      
      //neues ergebnis anlegen?
      if (empty($ergebnis->ID))
      {
         if (empty($termin_id))
         {
            $this->meldung=EM_KEIN_BEZUG;
            return false;
         }
         
         //ok ergebnis erstellen
         $ergebnis->ID=$this->db->anlegen($ergebnis);
         
         //den entsprechenden termin updaten
         $termin=new termin();
         $termin->ID=$termin_id;
         $termin->ergebnisID=$ergebnis->ID;
         
         $this->db->bearbeiten($termin);
      }
      else
      {
         $this->db->bearbeiten($ergebnis);
      }

      return true;
   }
   
   public function meldung()
   {
      return $this->meldung;
   }
   
}

?>
