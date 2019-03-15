<?php


//modul terminmanager
//benötigt db, termin, terminliste

//Datenbank Konventionen für Termine sind:
//1. Typ Spiel:
//    gegner ist NICHT leer
//    liga ist NICHT leer
//    header ist leer
//2. Typ Training:
//    gegner ist leer
//    liga ist leer
//    header ist leer
//3. Typ Frei:
//    gegner ist leer
//    liga ist leer
//    header ist NICHT leer
interface terminmanager_documentation
{

   //Diese Funktionen prüfen die erhaltenen Daten und führen nur dann eine Aktion aus, wenn die Eingabe gültig ist.
   //Die Rückgabe ist true, falls die Aktion erfolgreich ausgeführt wurde, sonst false.
   //Zugriffsrechte werden überprüft.
   public function anlegen(termin $termin, $zeit=null); //Die Funktion anlegen akzeptiert optional als zweiten Parameter ein Objekt der Klasse zeit.
                                                        //Wird ein zweiter Parameter angegeben, muss das Feld zeit in termin leer bleiben.
   public function bearbeiten(termin $termin, $zeit=null); //Das gleiche gilt für die Funktion bearbeiten. bearbeiten überprüft den übergebenen
                                                           //Termin auf die oben genannten Datenbank Konventionen und verweigert ein Update, falls
                                                           //der Termin sie nicht erfüllt, selbst wenn das Update die Datenbank konsistent lassen würde.
   public function loeschen($id);
   public function anzeigen($id); //Rückgabe ist der entsprechende Termin oder falls id nicht gefunden false.
                                  //Falls ein Ergebnis zu diesem Termin existiert, wird das im gleichen Objekt zurückgegeben.
   
   public function alle_anzeigen(); //Rückgabe ist eine terminliste aller in der Datenbank vorhandenen Termine. Die Liste kann auch leer sein.
   public function monat_anzeigen($monat); //Rückgabe ist eine terminliste für den angegebenen Monat.
                                           //besondere monate sind MONAT_AKTUELL (gibt die 10 neuesten termine) und
                                           //MONAT_EINADUNG (gibt alle Termine in der Zukunft, zu denen man eingeladen);
   public function monat_exist($monat); //Rückgabe ist true, falls monat_anzeigen für den jeweiligen Monat eine nichtleere Liste liefern würde, sonst false.
   
   public function meldung(); //Gibt die Textmeldung der zuletzt aufgerufenen Funktion zurück. Gibt die letzte Textmeldung beliebig oft zurück.
}

define('TM_KEINE_MELDUNG','');
define('TM_KEIN_ADMIN','Du hast keine Administratorrechte. Bitte melde dich als Administrator an.');
define('TM_KEIN_TYP','Bitte w&auml;hle einen Termintyp aus.');
define('TM_KEIN_ORT','Bitte gib den Ort an, an dem der Termin stattfindet.');
define('TM_KEINE_ZEIT','Bitte gib den Zeitpunkt an, zu der der Termin stattfindet.');
define('TM_KEIN_GEGNER','Wir brauchen einen Gegner.');
define('TM_KEINE_LIGA','Bitte w&auml;hle "Freundschaftsspiel" aus oder bestimme eine Liga.');
define('TM_KEIN_HEADER','Bitte gib diesem Termin eine &Uuml;berschrift..');
define('TM_KEIN_INHALT','Bitte gib eine kleine Beschreibung, was an diesem Termin passiert.');
define('TM_KEIN_TERMIN','Der Termin wurde nicht gefunden');
define('TM_KEIN_BEARBEITEN','Der Termin wurde gespeichert.');
define('TM_KEINE_STUNDE','Bitte gib eine g&uuml;ltige Stunde an.');
define('TM_KEINE_MINUTE','Bitte gib eine g&uuml;ltige Minute an.');
define('TM_KEIN_TAG','Bitte gib einen g&uuml;ltigen Tag an.');
define('TM_KEIN_MONAT','Bitte gib einen g&uuml;ltigen Monat an.');
define('TM_KEIN_JAHR','Bitte gib ein g&uuml;ltiges Jahr an.');
define('TM_KEIN_LOGIN','F&uuml;r diese Funktion musst du angemeldet sein.');
define('TM_FALSCHES_DATUM','Das angegebene Datum ist ung&uuml;ltig.');
define('TM_FALSCHE_ZEIT','Die angegebene Zeit existiert nicht.');
define('TM_ERFOLG_ANLEGEN','Der Termin wurde angelegt.');
define('TM_ERFOLG_ANZEIGEN','Der Termin wurde gelesen.');
define('TM_ERFOLG_LOESCHEN','Der Termin wurde gel&ouml;scht.');
define('TM_ERFOLG_BEARBEITEN','Die &Auml;nderungen wurden gespeichert.');
define('TM_ERFOLG_EXIST','Es wurde ermittelt, ob Termine im angegebenen Zeitraum existieren.');


abstract class terminmanager_configuration implements terminmanager_documentation
{
   protected $alles_loeschen=true; //true: wenn ein termin gelöscht wird, werden auch alle Daten gelöscht, die von dem Termin abhängen.
                                   //false: nur der termin wird gelöscht.
   //kann zur Laufzeit konfiguriert werden
   public $vergangene_anzeigen=true; //true: Auch termine aus der Vergangenheit anzeigen, false: nur zukünftige anzeigen
   public $zukuenftige_anzeigen=true; //true: Auch zukünftige Termin anzeigen, false: nur vergangene.
   public $nur_sichtbar_anzeigen=false; //true: zeigt nur diejenigen termine an, die ein ergebnis haben, dessen feld sichtbar den wert 1 hat.
   public $reihenfolge='ASC'; //DESC=absteigend, ASC=aufsteigend, alle anderen Werte produzieren mysqlfehler. Sollte besser abgefangen werden
}


class terminmanager extends terminmanager_configuration
{

   private $db;
   private $loginmanager;
   
   private $meldung=TM_KEINE_MELDUNG;
   
   public function __construct(loginmanager $loginmanager=null)
   {
      if ($loginmanager==null)
         $this->loginmanager=new loginmanager();
      else
         $this->loginmanager=$loginmanager;
         
      $this->db=new db();
   }

   public function anlegen(termin $termin, $zeit=null)
   {
      if (!$this->loginmanager->is_admin())
      {
         $this->meldung=TM_KEIN_ADMIN;
         return false;
      }
      //existiert termin typ?
      if (empty($termin->typ))
      {
         $this->meldung=TM_KEIN_TYP;
         return false;
      }
      
      //existiert ort?
      if (empty($termin->ort))
      {
         $this->meldung=TM_KEIN_ORT;
         return false;
      }
      
      //inhalt vorhanden?
      if (empty($termin->inhalt))
      {
         $this->meldung=TM_KEIN_INHALT;
         return false;
      }
      
      //zeit angegeben?
      if (empty($termin->zeit))
      {
         if (get_class($zeit)=='zeit') //ok zeit kommt über zeit parameter
         {
            if (!is_numeric($zeit->stunde))
            {
               $this->meldung=TM_KEINE_STUNDE;
               return false;
            }
            if (!is_numeric($zeit->minute))
            {
               $this->meldung=TM_KEINE_MINUTE;
               return false;
            }
            if (!is_numeric($zeit->tag))
            {
               $this->meldung=TM_KEIN_TAG;
               return false;
            }
            if (!is_numeric($zeit->monat))
            {
               $this->meldung=TM_KEIN_MONAT;
               return false;
            }
            if (!is_numeric($zeit->jahr))
            {
               $this->meldung=TM_KEIN_JAHR;
               return false;
            }
            if(!checkdate($zeit->monat,$zeit->tag,$zeit->jahr))
            {
               $this->meldung=TM_FALSCHES_DATUM;
               return false;
            }
            if(($zeit->stunde<0) or ($zeit->stunde>23) or ($zeit->minute<0) or ($zeit->minute>59))
            {
               $this->meldung=TM_FALSCHE_ZEIT;
               return false;
            }
            $termin->zeit=$zeit->timestamp();
         }
         else if($zeit===null) //keine zeit angegeben, abbruch
         {
            $this->meldung=TM_KEINE_ZEIT;
            return false;
         }
         else //zeit liegt in falschem format vor
         {
            trigger_error('The second parameter of anlegen has to be null or an object of class "zeit"',E_USER_WARNING);
            $this->meldung=TM_KEINE_ZEIT;
            return false;
         }
      }
      else //zeit doppelt angegeben?
      {
         if (!$zeit===null)
         {
            trigger_error('The time for this termin is ambigous',E_USER_NOTICE);
            if (get_class($zeit)!='zeit')
            {
               trigger_error('The second parameter of anlegen has to be null or an object of class "zeit"',E_USER_WARNING);
            }
         }
      }
      
      //gegner und liga sind nur dann relevant, wenn der termin ein Spiel ist.
      if ((int)$termin->typ==TYP_SPIEL)
      {
         if (empty($termin->gegner))
         {
            $this->meldung=TM_KEIN_GEGNER;
            return false;
         }
         if (empty($termin->liga))
         {
            $this->meldung=TM_KEINE_LIGA;
            return false;
         }
      }
      else
      {
         if (!empty($termin->gegner))
         {
            trigger_error('The field gegner in termin is not empty but typ does not equal TYP_SPIEL',E_USER_WARNING);
            unset($termin->gegner);
         }
         if (!empty($termin->liga))
         {
            trigger_error('The field liga in termin is not empty but typ does not equal TYP_SPIEL',E_USER_WARNING);
            unset($termin->liga);
         }
      }
      
      //der header ist nur interessant bei freien Terminen.
      if (empty($termin->header))
      {
         if ($termin->typ==TYP_FREI)
         {
            $this->meldung=TM_KEIN_HEADER;
            return false;
         }
      }
      else
      {
         if ($termin->typ!=TYP_FREI)
         {
            trigger_error('The field header is not empty but typ does not equal TYP_FREI',E_USER_WARNING);
            unset($termin->header);
         }
      }
      
      //alles ok, jetzt anlegen
      $this->db->anlegen($termin);
      $this->meldung=TM_ERFOLG_ANLEGEN;
      return true;
   }
   
   public function bearbeiten(termin $termin, $zeit=null)
   {
      if(!$this->loginmanager->is_admin())
      {
         $this->meldung=TM_KEIN_ADMIN;
         return false;
      }
      //zeit angegeben?
      if (!isset($termin->zeit))
      {
         if (get_class($zeit)=='zeit') //ok zeit kommt über zeit parameter
         {
            if (!is_numeric($zeit->stunde))
            {
               $this->meldung=TM_KEINE_STUNDE;
               return false;
            }
            if (!is_numeric($zeit->minute))
            {
               $this->meldung=TM_KEINE_MINUTE;
               return false;
            }
            if (!is_numeric($zeit->tag))
            {
               $this->meldung=TM_KEIN_TAG;
               return false;
            }
            if (!is_numeric($zeit->monat))
            {
               $this->meldung=TM_KEIN_MONAT;
               return false;
            }
            if (!is_numeric($zeit->jahr))
            {
               $this->meldung=TM_KEIN_JAHR;
               return false;
            }
            if(!checkdate($zeit->monat,$zeit->tag,$zeit->jahr))
            {
               $this->meldung=TM_FALSCHES_DATUM;
               return false;
            }
            if(($zeit->stunde<0) or ($zeit->stunde>23) or ($zeit->minute<0) or ($zeit->minute>59))
            {
               $this->meldung=TM_FALSCHE_ZEIT;
               return false;
            }
            $termin->zeit=$zeit->timestamp();
         }
         else if($zeit===null) //keine zeit angegeben, alte Zeit wird beibehalten
         {
            unset($termin->zeit);
         }
         else //zeit liegt in falschem format vor, alte Zeit behalten
         {
            trigger_error('The second parameter of anlegen has to be null or an object of class "zeit"',E_USER_WARNING);
            unset($termin->zeit);
         }
      }
      else //zeit doppelt angegeben?
      {
         if (!$zeit===null)
         {
            trigger_error('The time for this termin is ambigous',E_USER_NOTICE);
            if (get_class($zeit)!='zeit')
            {
               trigger_error('The second parameter of anlegen has to be null or an object of class "zeit"',E_USER_WARNING);
            }
         }
      }


      //gegner und liga sind nur dann relevant, wenn der termin ein Spiel ist.
      if ($termin->typ==TYP_SPIEL)
      {
         if (empty($termin->gegner))
         {
            $this->meldung=TM_KEIN_GEGNER;
            return false;
         }
         if (empty($termin->liga))
         {
            $this->meldung=TM_KEINE_LIGA;
            return false;
         }
      }
      else
      {
         if (!empty($termin->gegner))
         {
            trigger_error('The field gegner in termin is not empty but typ does not equal TYP_SPIEL',E_USER_WARNING);
            unset($termin->gegner);
         }
         if (!empty($termin->liga))
         {
            trigger_error('The field liga in termin is not empty but typ does not equal TYP_SPIEL',E_USER_WARNING);
            unset($termin->liga);
         }
      }
      
      //der header ist nur interessant bei freien Terminen.
      if (empty($termin->header))
      {
         if ($termin->typ==TYP_FREI)
         {
            $this->meldung=TM_KEIN_HEADER;
            return false;
         }
      }
      else
      {
         if ($termin->typ!=TYP_FREI)
         {
            trigger_error('The field header is not empty but typ does not equal TYP_FREI',E_USER_WARNING);
            unset($termin->header);
         }
      }


      //alle Daten ok, Datenbank aktualisieren
      $result=$this->db->bearbeiten($termin);

      if (!$result)
      {
         $this->meldung=TM_KEIN_BEARBEITEN;
         return true;
      }
      $this->meldung=TM_ERFOLG_BEARBEITEN;
      return true;
   }
   
   public function loeschen($id)
   {
      if (!$this->loginmanager->is_admin())
      {
         $this->meldung=TM_KEIN_ADMIN;
         return false;
      }
      
      //kommentar und einadung zum termin löschen
      if ($this->alles_loeschen)
      {
         $this->db->clean("bezugID=$id AND bezugstabelle='termin'",'kommentar');
         $this->db->clean("terminID=$id",'einladung');         
      }
      
      //termin selbst löschen
      $result=$this->db->loeschen($id,'termin');
      if (!$result)
      {
         $this->meldung=TM_KEIN_TERMIN;
         return false;
      }
      $this->meldung=TM_ERFOLG_LOESCHEN;
      return true;
   }
   
   public function anzeigen($id)
   {
      $result=$this->db->anzeigen($id, 'termin','ergebnis');
      if ($result===false)
         $this->meldung=TM_KEIN_TERMIN;
      else
         $this->meldung=TM_ERFOLG_ANZEIGEN;
         
      return $result;
   }
   
   public function alle_anzeigen()
   {
      if ($this->vergangene_anzeigen)
         $von='1=1';
      else
         $von='zeit>='.time();
      if ($this->zukuenftige_anzeigen)
         $bis='1=1';
      else
         $bis='zeit<='.time();
      if ($this->nur_sichtbar_anzeigen)
         $vis="ergebnis.sichtbar=1";
      else
         $vis='1=1';
      return $this->db->liste("$von AND $bis AND $vis",'termin','ergebnis');
   }
   
   
   public function meldung()
   {
      return $this->meldung;
   }
   

   public function monat_anzeigen($monat)
   {
      if ($this->vergangene_anzeigen)
         $von='1=1';
      else
         $von='termin.zeit>='.time();
      if ($this->zukuenftige_anzeigen)
         $bis='1=1';
      else
         $bis='termin.zeit<='.time();
      if ($this->nur_sichtbar_anzeigen)
         $vis="ergebnis.sichtbar=1";
      else
         $vis='1=1';

      if ($monat===MONAT_AKTUELL)
      {
         $this->meldung=TM_ERFOLG_ANZEIGEN;
         return $this->db->liste("$von AND $bis AND $vis",'termin','ergebnis',"ORDER BY zeit $this->reihenfolge LIMIT 10");
      }
      
      if ($monat===MONAT_EINLADUNG)
      {
         if (!$this->loginmanager->login_status())
         {
            $this->meldung=TM_KEIN_LOGIN;
            return false;
         }
         
         $id=$this->loginmanager->ID;
         $this->meldung=TM_ERFOLG_ANZEIGEN;
         return $this->db->liste("$von AND $bis AND $vis AND EXISTS (SELECT * FROM einladung WHERE einladung.accountID=$id AND einladung.terminID=termin.ID)",
                                 'termin',
                                 'ergebnis',
                                 'ORDER BY zeit '.$this->reihenfolge);
      }


      $year=(int)date('Y',time());
      if ($monat<1)
      {
         $monat+=12;
         $year--;
      }
      if ($monat>12)
      {
         $monat-=12;
         $year++;
      }

      //kein besonderer Monat, also Zeitraum ausrechnen:
      $datevon=mktime(0,0,0,$monat,0,$year);
      $datebis=mktime(0,0,0,$monat+1,0,$year);
            
      $this->meldung=TM_ERFOLG_ANZEIGEN;
      return $this->db->liste("$von AND $bis AND $vis AND termin.zeit>=$datevon AND termin.zeit<=$datebis",
                              'termin',
                              'ergebnis',
                              'ORDER BY zeit '.$this->reihenfolge);
      
   }
   
   public function monat_exist($monat)
   {
      if ($this->vergangene_anzeigen)
         $von='1=1';
      else
         $von='zeit>='.time();
      if ($this->zukuenftige_anzeigen)
         $bis='1=1';
      else
         $bis='zeit<='.time();
      if ($this->nur_sichtbar_anzeigen)
         $vis="EXISTS(SELECT * FROM ergebnis WHERE ergebnis.ID=termin.ergebnisID AND ergebnis.sichtbar=1)";
      else
         $vis='1=1';


      if ($monat===MONAT_AKTUELL)
      {

         $this->meldung=TM_ERFOLG_EXIST;
         if ($this->db->wieviel("$von AND $bis AND $vis",'termin')>0)
            return true;
         else
            return false;
      }
      
      if ($monat===MONAT_EINLADUNG)
      {
         if (!$this->loginmanager->login_status())
         {
            $this->meldung=TM_KEIN_LOGIN;
            return false;
         }
         $id=$this->loginmanager->ID;
         $this->meldung=TM_ERFOLG_EXIST;
         $where="$von AND $bis AND $vis AND
                  EXISTS (SELECT * FROM einladung WHERE einladung.accountID=$id AND einladung.terminID=termin.ID)";
         if ($this->db->wieviel($where,'termin')>0)
            return true;
         else
            return false;
      }
      
      $year=(int)date('Y',time());
      if ($monat<1)
      {
         $monat+=12;
         $year--;
      }
      if ($monat>12)
      {
         $monat-=12;
         $year++;
      }


      //Kein besonderer Monat
      $datevon=mktime(0,0,0,$monat,0,$year);
      $datebis=mktime(0,0,0,$monat+1,0,$year);
      $this->meldung=TM_ERFOLG_EXIST;
      if ($this->db->wieviel("$von AND $bis AND $vis AND termin.zeit>=$datevon AND termin.zeit<=$datebis",'termin')>0)
         return true;
      else
         return false;
   }
   
   
}
?>
