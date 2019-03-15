<?php

//der Kommentarmanagerüberprüft die Gültigkeit der Anfrage und die Rechte des Users, bevor er die Anfrage ausführt.
interface kommentarmanager_documentation
{
   public function anlegen(kommentar $kommentar); //erzeugt ein Kommentar
   public function loeschen($id); //löscht den Kommentar mit der entsprechenden id.
   public function anzeigen($bezugID, $bezugstabelle); //Gibt die Kommentarliste zu diesem Bezug zurück.
   
   
   public function meldung(); //Gibt eine Textmeldung über den Ausgang der letzten Aktion.
}


abstract class kommentarmanager_configuration implements kommentarmanager_documentation
{
   protected $kommentartabellen=array('termin','ergebnis','news');  //Sicherheitsfunktion: Nur Inhalte aus diesen Tabellen können kommentiert werden
   protected $sortorder='DESC';   //Kommentare werden nach der Zeit absteigend sortiert. Also der neueste Kommentar steht ganz oben.
}


define('KM_KEIN_LOGIN','Du bist nicht angemeldet. Bitte melde dich an und versuche es noch einmal.');
define('KM_KEIN_INHALT','Der Kommentar wurde nicht angelegt, denn er hat keinen Inhalt.');
define('KM_KEIN_BEZUG','Dieser Fehler sollte niemals auftreten. Der Kommentar wurde nicht gespeichert, da er sich auf nichts bezieht.');
define('KM_KEINE_BEZUGSTABELLE','Dieser Fehler sollte niemals auftreten. Der Kommentar wurde nicht gespeichert,
                           da nicht angegeben wurde, auf welche Tabelle er sich bezieht.');
define('KM_FALSCHE_BEZUGSTABELLE','Dieser Fehler sollte niemals auftreten.
                             Der Kommentar wurde nicht gespeichert, da die angegebene Bezugstabelle ung&uuml;ltig ist.');
define('KM_KEIN_KOMMENTAR','Der Kommentar wurde nicht gefunden');
define('KM_KEIN_ADMIN','Du hast keine Administratorrechte. Bitte melde dich als Administrator an.');
define('KM_ERFOLG_ANLEGEN','Der Kommentar wurde angelegt.');
define('KM_ERFOLG_LOESCHEN','Der Kommentar wurde gel&ouml;scht.');
define('KM_ERFOLG_ANZEIGEN','Die Kommentare wurden gelesen.');


class kommentarmanager extends kommentarmanager_configuration
{

   private $loginmanager;
   private $db;
   
   private $meldung='';
   
   
   public function __construct(loginmanager $loginmanager=null)
   {
      if ($loginmanager==null)
         $this->oginmanager=new loginmanager();
      else
         $this->loginmanager=$loginmanager;
      
      $this->db=new db();
   }   
   
   public function anlegen(kommentar $kommentar)
   {
      if (isset($kommentar->ID))
      {
         trigger_error('Cannot create kommentar with specified ID. ID is being ignored.',E_USER_NOTICE);
         unset($kommentar->ID);
      }
      
      if (!$this->loginmanager->login_status())
      {
         $this->meldung=KM_KEIN_LOGIN;
         return false;
      }
      $kommentar->accountID=$this->loginmanager->ID;
      
      if(isset($kommentar->zeit))
         trigger_error('Cannot create kommentar with specified zeit. zeit is set to now.',E_USER_NOTICE);
      $kommentar->zeit=time();
      
      if(empty($kommentar->inhalt))
      {
         $this->meldung=KM_KEIN_INHALT;
         return false;
      }
      
      if (!isset($kommentar->bezugID))
      {
         $this->meldung=KM_KEIN_BEZUG;
         return false;
      }
      
      if (!isset($kommentar->bezugstabelle))
      {
         $this->meldung=KM_KEINE_BEZUGSTABELLE;
         return false;
      }
      
      if (!in_array($kommentar->bezugstabelle,$this->kommentartabellen))
      {
         $this->meldung=KM_FALSCHE_BEZUGSTABELLE;
         return false;
      }
      
      $this->db->anlegen($kommentar);
      $this->meldung=KM_ERFOLG_ANLEGEN;
      return true;
   }
   
   
   function loeschen($id)
   {
      $kommentar=$this->db->anzeigen($id,'kommentar');
      if ($kommentar===false)
      {
         $this->meldung=KM_KEIN_KOMMENTAR;
         return false;
      }
      
      if( ($kommentar->accountID!=$this->loginmanager->ID) and (!$this->loginmanager->is_admin()) )
      {
         $this->meldung=KM_KEIN_ADMIN;
         return false;
      }
      
      $status=$this->db->loeschen($id,'kommentar');
      if (!$status)
      {
         $this->meldung=KM_KEIN_KOMMENTAR;
         return false;
      }
      
      $this->meldung=KM_ERFOLG_LOESCHEN;
      return true;
   }
   
   public function anzeigen($bezugID, $bezugstabelle)
   {
      if (!isset($bezugID))
      {
         $this->meldung=KM_KEIN_BEZUG;
         return false;
      }

      if (!in_array($bezugstabelle,$this->kommentartabellen))
      {
         $this->meldung=KM_FALSCHE_BEZUGSTABELLE;
         return false;
      }      

      $kommentarliste=$this->db->liste("bezugID=$bezugID AND bezugstabelle='$bezugstabelle'",
                                       'kommentar',
                                       'account',
                                       "ORDER BY zeit $this->sortorder");
      $this->meldung=KM_ERFOLG_ANZEIGEN;
      return $kommentarliste;
   }
   
   public function meldung()
   {
      return $this->meldung;
   }
   
}


?>
