<?php
                                                                   
//Der Einladungmanager prüft die Gültigkeit der Anfrage und ob der User für die Anfrage berechtigt ist.
interface einladungsmanager_documentation
{
   //Standartfunktionen
   public function anlegen(einladung $einladung); //Legt diese Einladung an
   public function loeschen($id);    //Löscht die Einladung mit dieser Id. Fraglich, ob man das jemals braucht.
   public function anzeigen($terminID); //gibt eine Einladungsliste zurück zur entsprechenden terminID
   
   //Alle Einadung zum Account ansehen
   public function einladungen_zum_account(account $account); //Gibt eine terminliste zurück

   //Auf den Besonderheiten der Datenbank beruhende Funktionen, schlechte Wiederverwendbarkeit
   public function einladen_spieler($id);  //Lädt alle Accounts mit dem Status Spieler zum Termin ein.
   public function einladen_alle($id);     //Lädt alle Accounts zum Termin ein.
   
   public function ausladen_alle($id);  //Löscht alle Einladungen zu diesem Termin
   
   public function termin_zusagen($id, $zusage); //Setzt die zusageID der Einladung, die mit diesem Termin verknüpft ist.
   public function einladung_zusagen($id, $zusage);//Setzt die zusageID der Einladung mit dieser id.
   
   public function meldung(); //Enthält Eine Textmeldung mit dem Ausgang der letzten Anfrage.
}


define('EM_KEIN_ADMIN','Du hast keine Administratorrechte. Bitte melde dich als Administrator an.');
define('EM_KEIN_SPIELER','Für diese Aktion musst du Adminstrator oder Spieler sein.');
define('EM_KEIN_ACCOUNT','Die Einadung wurde nicht angelegt, da der Eingeladene Benutzer nicht existiert.');
define('EM_KEIN_TERMIN','Die Aktion wurde nicht verarbeitet, da der angegebene Termin nicht existiert.');
define('EM_KEIN_LOESCHEN','Die Einladung wurde bereits gel&ouml;scht. Sie ist nicht mehr vorhanden.');
define('EM_KEIN_CLEAN','Zu diesem Termin gibt es keine Einladung.');
define('EM_KEINE_EINLADUNG','Ein Fehler ist aufgetreten. Die Einladung, zu der du zusagen m&ouml;chtest, existiert leider nicht.');
define('EM_KEIN_BEARBEITEN','Dein Zusagestatus ist bereits wie gew&uuml;nscht.');
define('EM_KEINE_LISTE','Beim Auslesen der Einladungen zu deinem Benutzerkonto ist ein Fehler aufgetreten.');
define('EM_KEINE_ID','Ein Systemfehler ist aufgetreten. Es ist nicht klar, zu welchem Benutzerkonto Einladungen gelesen werden sollen oder das Konto existiert nicht.');
define('EM_ERFOLG_EINLADUNGACCOUNT','Die Einladungen zum Benutzerkonto wurden gelesen.');
define('EM_ERFOLG_ZUSAGEN','Dein Zusagestatus wurde ge&auml;ndert.');
define('EM_ERFOLG_ANLEGEN','Die Einladung wurde angelegt.');
define('EM_ERFOLG_LOESCHEN','Die Einladung wurde gel&ouml;scht.');
define('EM_ERFOLG_ANZEIGEN','Die Einladungen wurden gelesen.');
define('EM_ERFOLG_SPIELEREINLADEN','Alle Spieler wurden eingeladen.');
define('EM_ERFOLG_ALLEEINLADEN','Alle aktiven Accounts wurden eingeladen.');
define('EM_ERFOLG_AUSLADEN','Alle Einladungen zu diesem Termin wurden gel&ouml;scht.');


abstract class einladungsmanager_configuration implements einladungsmanager_documentation
{

   public $vergangene_anzeigen=true;

   //Welche Werte sind gültig für das Feld zusageID
   protected function gueltige_zusage($zusage)
   {
      switch($zusage)
      {
         case ZUSAGE_KEINEANTWORT:
            return true;
         case ZUSAGE_JA:
            return true;
         case ZUSAGE_VIELLEICHT:
            return true;
         case ZUSAGE_NEIN:
            return true;
      }
      
      //Zusage ist nicht gültig, sonst wäre sie oben dabei
      return false;
   }
   
}


class einladungsmanager extends einladungsmanager_configuration
{

   private $loginmanager;
   private $db;
   
   private $meldung;
   
   public function __construct(loginmanager $loginmanager=null)
   {
      if ($loginmanager==null)
         $this->loginmanager=new loginmanager();
      else
         $this->loginmanager=$loginmanager;
         
      $this->db=new db();
   }

   public function anlegen(einladung $einladung)
   {
      if (!($this->loginmanager->is_admin() or !$this->loginmanager->is_spieler()))
      {
         $this->meldung=EM_KEIN_SPIELER;
         return false;
      }
      
      if (isset($einladung->ID))
      {
         trigger_error('Cannot create einladung with a specific ID. ID is beeing ignored.', E_USER_NOTICE);
         unset($einladung->ID);
      }
      
      if (!$this->db->exist($einladung->accountID,'account'))
      {
         $this->meldung=EM_KEIN_ACCOUNT;
         return false;
      }
      
      if (!$this->db->exist( $einladung->terminID,'termin'))
      {
         $this->meldung=EM_KEIN_TERMIN;
         return false;
      }
      
      $wieviel=$this->db->wieviel("accountID=$einladung->accountID AND terminID=$einladung->terminID",'einladung');
      if ($wieviel>0)
      {
         $this->meldung=EM_EINLADUNG_DOPPELT;
         return false;
      }
      
      //Diese Regel ist generell fragwürdig, eventuell möchte man das lockern. An dieser Stelle aber sinnvoll.
      if (isset($einladung->zusageID) and $einladung->zusageID!=0)
      {
         trigger_error('It is not allowed to set zusageID to a different value than 0. zusageID is beeing set to 0.', E_USER_NOTICE);
      }
      $einladung->zusageID=0;

      //Alle angaben gültig
      $this->db->anlegen($einladung);
      $this->meldung=EM_ERFOLG_ANLEGEN;
      return true;
   }
   
   public function loeschen($id)
   {
      if (!$this->loginmanager->is_admin())
      {
         $this->meldung=EM_KEIN_ADMIN;
         return false;
      }
      $res=$this->db->loeschen($id,'einladung');
      if (!$res)
      {
         $this->meldung=EM_KEIN_LOESCHEN;
         return false;
      }
      $this->meldung=EM_ERFOLG_LOESCHEN;
      return true;
   }
   
   public function anzeigen($terminID)
   {
      if (!isset($terminID))
      {
         $this->meldung=EM_KEIN_TERMIN;
         return false;
      }
  
      $einladungliste=$this->db->liste("terminID=$terminID",
                                       'einladung',
                                       'account');
      $this->meldung=EM_ERFOLG_ANZEIGEN;
      return $einladungliste;
   }
   
   
   private function einladen_nach_status($id, $status)
   {
      if (!$this->loginmanager->is_admin())
      {
         $this->meldung=EM_KEIN_ADMIN;
         return false;
      }
      if(!$this->db->exist($id,'termin'))
      {
         $this->meldung=EM_KEIN_TERMIN;
         return false;        
      }
      
      $accountliste=$this->db->liste("$status=(statusflag & $status)",'account');
      $einladung=new einladung();
      $einladung->terminID=$id;
      $einladung->zusageID=ZUSAGE_KEINEANTWORT;
      foreach($accountliste as $account)
      {
         $einladung->accountID=$account->ID;
         $wieviel=$this->db->wieviel("accountID=$einladung->accountID AND terminID=$einladung->terminID",'einladung');
         //einladung existiert schon
         if ($wieviel>0)
            continue;
         $this->db->anlegen($einladung);         
      }
      return true;
   }
   
   public function einladen_spieler($id)
   {
      $res=$this->einladen_nach_status($id, STATUS_SPIELER);
      if ($res===true)
         $this->meldung=EM_ERFOLG_SPIELEREINLADEN;
      return $res;
   }
   
   public function einladen_alle($id)
   {
      $res=$this->einladen_nach_status($id, STATUS_LOGIN);
      if ($res===true)
         $this->meldung=EM_ERFOLG_ALLEEINLADEN;
      return $res;
   }
   
   public function ausladen_alle($id)
   {
      if(!$this->loginmanager->is_admin())
      {
         $this->meldung=EM_KEIN_ADMIN;
         return false;
      }
      
      if (!$this->db->exist($id,'termin'))
      {
         $this->meldung=EM_KEIN_TERMIN;
         return false;
      }
      
      $res=$this->db->clean("terminID=$id",'einladung');
      if ($res==0)
      {
         $this->meldung=EM_KEIN_CLEAN;
         return false;
      }
      $this->meldung=EM_ERFOLG_AUSLADEN;
      return true;
   }
   
   
   public function einladung_zusagen($id, $zusage)
   {
      if (!$this->db->exist($id, 'einladung'))
      {
         $this->meldung=EM_KEINE_EINLADUNG;
         return false;
      }
      if (!$this->gueltige_zusage($zusage))
      {
         $this->meldung=EM_FALSCHE_ZUSAGE;
         return false;
      }
      
      $einladung=new einladung();
      $einladung->ID=$id;
      $einladung->zusageID=$zusage;
      $res=$this->db->bearbeiten($einladung);
      if (!$res)
         $this->meldung=EM_KEIN_BEARBEITEN;
      else 
         $this->meldung=EM_ERFOLG_ZUSAGEN;
         
      return $res;
   }
   
   public function termin_zusagen($id, $zusage)
   {
      $accountID=$this->loginmanager->ID;
      $einladungsliste=$this->db->liste("accountID=$accountID AND terminID=$id",'einladung');
      if ($einladungsliste->size()==0)
      {
         $this->meldung=EM_KEINE_EINLADUNG;
         return false;
      }
      return $this->einladung_zusagen($einladungsliste[0]->ID, $zusage);
   }
      
   
   public function einladungen_zum_account(account $account)
   {
      if (!isset($account->ID) or !$this->db->exist($account->ID,'account'))
      {
         $this->meldung=EM_KEINE_ID;
         return false;
      }
      
      if ($this->vergangene_anzeigen)
         $von='1=1';
      else
         $von='termin.zeit>='.time();
      
      //Parameter für liste_frei: $where, $tabelle, $on, $select, $obj, $listobj, $special=''
      @$terminliste=$this->db->liste_frei("$von AND einladung.accountID=$account->ID",
                                          'termin LEFT JOIN einladung',
                                          'einladung.terminID=termin.ID',
                                          '*, einladung.ID AS ID',
                                          'db_entry',
                                          'blanklist',
                                          'ORDER BY termin.zeit ASC');
      if ($terminliste==false)
         $this->meldung=EM_KEINE_LISTE;
      else
         $this->meldung=EM_ERFOLG_EINLADUNGACCOUNT;
      return $terminliste;
   }

   
   
   public function meldung()
   {
      return $this->meldung;
   }
     
   
}


?>
