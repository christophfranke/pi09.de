<?php

//der Ligamanager

interface ligamanager_documentation
{
   public function anzeigen($id); //Zeigt die Liga mit dieser ID an
   public function anlegen(liga $liga); //legt eine Liga an
   public function loeschen($id); //Löscht die Liga mit dieser ID
   public function bearbeiten(liga $liga); //Bearbeitet die angegebene Liga
   
   public function alle_anzeigen(); //gibt eine ligaliste zurück mit allen ligen
   
   public function meldung(); //gibt die letzte Meldung zurück
}


abstract class ligamanager_configuration implements ligamanager_documentation
{
}


define('LM_KEIN_NAME','Bitte gib einen Namen f&uuml;r die Liga an.');
define('LM_KEIN_ADMIN','Du hast derzeit kein Recht, diese Aktion auszuf&uuml;hren. Bitte melde dich als Administrator an.');
define('LM_KEINE_LIGA','Die angeforderte Liga existiert nicht.');
define('LM_ERFOLG_ANZEIGEN','Die Liga wird angezeigt.');
define('LM_KEIN_ANLEGEN','Die Liga konnte aus einem unbekannten Grund nicht angelegt werden.');
define('LM_KEIN_BEARBEITEN','Deine Bearbeitung wurde ausgef&uuml;hrt, hat aber keine &Auml;nderung bewirkt.');
define('LM_KEIN_LOESCHEN','Die Liga eistiert nicht. Wahrscheinlch wurde sie bereits gel&ouml;scht.');
define('LM_ERFOLG_ANLEGEN','Die Liga wurde angelegt.');
define('LM_ERFOLG_BEARBEITEN','Die Daten zur Liga wurden gespeichert.');
define('LM_ERFOLG_LOESCHEN','Die Liga wurde gel&ouml;scht.');

class ligamanager extends ligamanager_configuration
{
   private $meldung='';
   
   private $loginmanager;
   private $db;
   
   public function __construct(loginmanager $loginmanager=null)
   {
      if ($loginmanager==null)
         $thisloginmanager=new loginmanager();
      else
         $this->loginmanager=$loginmanager;
         
      $this->db=new db();
   }  
   
   public function anzeigen($id)
   {
      $result=$this->db->anzeigen($id,'liga');
      if ($result===false)
         $this->meldung=LM_KEINE_LIGA;
      else
         $this->meldung=LM_ERFOLG_ANZEIGEN;
      return $result;
   }
   
   public function anlegen(liga $liga)
   {
      if (isset($liga->ID))
      {
         trigger_error('When creating a new liga entry, it is not possible to specify the ID. The ID is beeing ignored.');
         unset($liga->ID);
      }
      
      if (!$this->loginmanager->is_admin())
      {
         $this->meldung=LM_KEIN_ADMIN;
         return false;
      }
      
      if (empty($liga->name))
      {
         $this->meldung=LM_KEIN_NAME;
         return false;
      }
      
      //Alles OK, anlegen
      $result=$this->db->anlegen($liga);
      if ($result)
         $this->meldung=LM_ERFOLG_ANLEGEN;
      else
         $this->meldung=LM_KEIN_ANLEGEN;
         
      return $result;
   }
   
   
   public function loeschen($id)
   {
      if (!$this->loginmanager->is_admin())
      {
         $this->meldung=LM_KEIN_ADMIN;
         return false;
      }
      
      if (empty($id) or $id!=(int)$id)
      {
         $this->meldung=LM_KEINE_LIGA;
         return false;
      }
      else
         $id=(int)$id;
      
      $result=$this->db->loeschen($id, 'liga');
      if ($result)
         $this->meldung=LM_ERFOLG_LOESCHEN;
      else
         $this->meldung=LM_KEIN_LOESCHEN;
      return $result;
   }
   
   public function bearbeiten(liga $liga)
   {
      if (!$this->loginmanager->is_admin())
      {
         $this->meldung=LM_KEIN_ADMIN;
         return false;
      }
      
      if (empty($liga->name))
      {
         $this->meldung=LM_KEIN_NAME;
         return false;
      }
      
      if (empty($liga->ID) or $liga->ID!=(int)$liga->ID)
      {
         $this->meldung=LM_KEINE_LIGA;
         return false;
      }
      else
         $liga->ID=(int)$liga->ID;
      
      $result=$this->db->bearbeiten($liga);
      if ($result)
         $this->meldung=LM_ERFOLG_BEARBEITEN;
      else
         $this->meldung=LM_KEIN_BEARBEITEN;
      return $result;
   }   
   
   
   public function alle_anzeigen()
   {
      $liste=$this->db->liste('1=1','liga');
      return $liste;
   }
   
   
   public function meldung()
   {
      return $this->meldung;
   }

   
}
