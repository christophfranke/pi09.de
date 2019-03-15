<?php

//modul usermanager
//benötigt loginmanager, db

//Das Usermanager-Modul bietet solche Funktionen, die ein User oder Admin ausführen möchte und überprüft alle Berechtigungsfragen
//vor der Ausführung.
interface usermanager_documentation
{
   public function __construct(loginmanager $loginmanager=null); //Konstruktor erwartet einen loginmanager.
   public function set_db(db $db); //Benutze dieses db-Objekt.

   //Diese vier Funktionen sind die Aktionen, die ein User durhführen kann. Der Usermanager überprüft in jedem Fall, ob der User
   //für die angeforderte Aktion berechtigt ist und gibt im Erfolgsfall true, ansonsten false zurück. In jedem Fall wird eine
   //Textmeldung gespeichert, die mit der Funktion Meldung abgerufen werden kann.
   public function anlegen(account $account, $passwortwdh); //Einen Account anlegen. Nicht möglich mit leerem Passwort,
                                                            //statusflag ist immer STATUS_LOGIN.
   public function register(account $account, $passwortwdh);//Alias von anlegen
   public function bearbeiten(account $account); //Einen Account bearbeiten. Nicht bearbeitet werden spielerID und passwort.
   public function passwort($passwort, $passwortwiederholung); //Ändert das Passwort von dem User, der gerade angemeldet ist.
   public function loeschen($id); //einen Account löschen. Wird keine id übergeben, wird angenommen, dass der User sich selbst löschen will.
   
   public function alle_anzeigen(); //Gibt eine accountliste zurück mit allen in der Datenbank abgelegten Accounts. Benötigt Adminrechte.

   public function meldung(); //Gibt die Textmeldung der zuletzt aufgerufenen Funktion zurück. Gibt die letzte Textmeldung beliebig oft zurück.
}

abstract class usermanager_configuration implements usermanager_documentation
{
   protected $warnings=true; //true berechtigt das Modul, Warnungen und Notizen zu erzeugen.
}

//Textmeldungen
define('UM_KEINE_MELDUNG','');
define('UM_KEIN_LOGIN', 'Deine Session ist nicht mehr aktiv. Bitte melde dich an und versuche es dann noch einmal.');
define('UM_KEIN_ADMIN', 'Du hast keine Administratorrechte. Bitte melde dich erneut als Administrator an.');
define('UM_KEIN_BEARBEITEN', 'Die Aktion wurde verarbeitet, hat aber keine Ver&auml;nderung bewirkt.');
define('UM_KEIN_ANZEIGEN', 'Das Benutzerkonto kann nicht angezeigt werden, da es nicht gefunden wurde.');
define('UM_KEIN_LOESCHEN', 'Der Account konnte nicht gel&ouml;scht werden. Die ID scheint ung&uuml;ltig zu sein.');
define('UM_LETZTER_ADMIN','Du bist der einzige Administrator. Wenn du dieses Konto l&ouml;schen willst, bestimme vorher einen anderen Administrator.');
define('UM_KEIN_PASSWORT', 'Es wurde kein Passwort angegeben.');
define('UM_KEIN_HARTADMIN','Das Konto mit dem Namen \'admin\' kann nicht von aussen ge&auml;ndert oder gel&ouml;scht werden.');
define('UM_KEIN_NAME', 'Es wurde kein Benutzername angegeben.');
define('UM_KEINE_ID','Ein Systemfehler ist aufgetreten: Es wurde keine ID angegeben oder die ID ist ung&uuml;ltig.');
define('UM_KEIN_DEMOTE','Du darfst dir selbst weder die Administratorrechte noch die Loginrechte entziehen.');
define('UM_PASSWORTWDH_FALSCH', 'Die Passw&ouml;rter stimmen nicht &uuml;berein.');
define('UM_NAME_VERGEBEN', 'Der Benutzername ist schon vergeben.');
define('UM_ERFOLG_BEARBEITEN','Die &Auml;nderungen an dem Benutzerkonto wurden gespeichert.');
define('UM_ERFOLG_PASSWORT','Dein Passwort wurde ge&auml;ndert.');
define('UM_ERFOLG_ANLEGEN','Dein Benutzerkonto wurde angelegt.');
define('UM_ERFOLG_LOESCHEN','Das Benutzerkonto wurde gel&ouml;scht');
define('UM_ERFOLG_ALLEZEIGEN','Die Benutzerkonten wurden gelesen');
define('UM_ERFOLG_ANZEIGEN', 'Das Benutzerkonto wurde gelesen');
define('UM_ERFOLG','Die Aktion wurde erfolgreich verarbeitet.');




class usermanager extends usermanager_configuration
{
   
   private $loginmanager;
   private $db;
   
   private $meldung=UM_KEINE_MELDUNG;
   
   
   public function __construct(loginmanager $loginmanager=null)
   {
      if ($loginmanager==null)
         $this->loginmanager=new loginmanager();
      else
         $this->loginmanager=$loginmanager;

      $this->db=new db();
   }
   
   public function set_db(db $db)
   {
      $this->db=$db;
   }
   
   
   public function anlegen(account $account, $passwortwdh)
   {
      //Ein Account benötigt einen loginnamen
      if (empty($account->loginname))
      {
         $this->meldung=UM_KEIN_NAME;
         return false;
      }

      //Ist der Benutzername noch frei?
      $exist=$this->db->liste("loginname='$account->loginname' AND statusflag!=0",'account');
      if ($exist->size()>0)
      {
         $this->meldung=UM_NAME_VERGEBEN;
         return false;
      }
   
      //Ein neuer Account benötigt ein Passwort
      if (empty($account->passwort))
      {
         $this->meldung=UM_KEIN_PASSWORT;
         return false;
      }
      
      //Stimmen die Passwörter übereein
      if ($account->passwort!==$passwortwdh)
      {
         $this->meldung=UM_PASSWORTWDH_FALSCH;
         return false;
      }

      //spielerID nicht setzen      
      if (isset($account->spielerID))
      {
         if ($this->warnings)
            trigger_error('The usermanager modul will not set a spielerID when creating an account',E_USER_NOTICE);
         unset($account->spielerID);
      }
      
      //Wenn statusflag verschieden von STATUS_LOGIN, werden Adminrechte verlangt
      if (isset($account->statusflag) and $account->statusflag!=STATUS_LOGIN and !$this->loginmanager->is_admin())
      {
         $this->meldung=UM_KEIN_ADMIN;
         return false;
      }
      
      

      $account->passwort=md5($account->passwort);
      if (!isset($account->statusflag))
         $account->statusflag=STATUS_LOGIN;
      
      $this->db->anlegen($account);
      $this->meldung=UM_ERFOLG_ANLEGEN;
      return true;      
   }
   
   public function register(account $account, $passwortwdh)
   {
      return $this->anlegen($account, $passwortwdh);
   }
   
   
   public function bearbeiten(account $account)
   {
      //Ist der User eingeloggt?
      $status=$this->loginmanager->login_status();
      if (!$status)
      {
         $this->meldung=UM_KEIN_LOGIN;
         return false;
      }
      
      //ID vorhanden?
      if (!isset($account->ID))
      {
         $this->meldung=UM_KEINE_ID;
         return false;
      }
      
      //Versucht der User, einen fremden Account zu editieren und hat er das Recht dazu?
      $is_admin=$this->loginmanager->is_admin();
      if (!$is_admin and $this->loginmanager->ID!=$account->ID)
      {
         $this->meldung=UM_KEIN_ADMIN;
         return false;
      }
      
      //Versucht jemand, den Account mit dem Namen 'admin' zu editieren?
      if ($account->loginname=='admin' and $this->loginmanager->name!='admin')
      {
         $this->meldung=UM_KEIN_HARTADMIN;
         return false;
      }
      
      //Ist der neue Benutzername schon vergeben und ist die ID gültig?
      $old=$this->db->anzeigen($account->ID,'account');
      if ($old===false)
      {
         $this->meldung=UM_KEINE_ID;
         return false;
      }
      $exist=$this->db->liste("loginname='$account->loginname' AND statusflag!=0",'account');
      if ($exist->get_size()>0 and $exist[0]->loginname!=$old->loginname)
      {
         $this->meldung=UM_NAME_VERGEBEN;
         return false;
      }
      
      //Versucht der User, die Rechte zu editieren, obwohl er keine Adminrechte hat?
      if (isset($account->statusflag) and !$this->loginmanager->is_admin())
      {
         $this->meldung=UM_KEIN_ADMIN;
         return false;
      }
      
      //Versucht der User, seine eigenen Administratorrechte oder Loginrechte zu tilgen?
      if (isset($account->statusflag) and
         ($account->statusflag & (STATUS_ADMIN + STATUS_LOGIN)) != (STATUS_ADMIN + STATUS_LOGIN) and
         $account->ID==$_SESSION['ID'])
      {
         $this->meldung=UM_KEIN_DEMOTE;
         return false;
      }


      $new=$account->get_copy();

      //Ein User ist niemals berechtigt, spielerID zu verändern
      if (isset($new->spielerID))
      {
         if ($this->warnings)
            trigger_error('The usermanager modul will not change spielerID in function bearbeiten',E_USER_NOTICE);
         unset($new->spielerID);
      }
      //Das Passwort niemals mit dieser Funktion geändert
      if (isset($new->passwort))
      {
         if ($this->warnings)
            trigger_error('The usermanager modul will not change passwort in function bearbeiten', E_USER_NOTICE);
         unset($new->passwort);
      }


      $result=$this->db->bearbeiten($new);
   
      if (!$result)
      {
         $this->meldung=UM_KEIN_BEARBEITEN;
         return false;
      }

      $this->loginmanager->update_user(); //Weise den Loginmanager an, den Useraccount neu aus der Datenbank zu lesen.
      $this->meldung=UM_ERFOLG_BEARBEITEN;
      return true;
   }
   
   public function passwort($passwort, $passwortwiederholung)
   {
      //Ist der User eingeloggt?
      if (!$this->loginmanager->login_Status())
      {
         $this->meldung=UM_KEIN_LOGIN;
         return false;
      }
      
      //leeres Passwort wird nicht akzeptiert
      if (empty($passwort))
      {
         $this->meldung=UM_KEIN_PASSWORT;
         return false;
         
      }
      
      //Stimmen Passwort und Wiederholung überein?
      if ($passwort!==$passwortwiederholung)
      {
         $this->meldung=UM_PASSWORTWDH_FALSCH;
         return false;
      }
      
      
      @$account=new account();
      $account->passwort=md5($passwort);
      $account->ID=$this->loginmanager->ID;
      
      $result=$this->db->bearbeiten($account);
      
      if (!$result)
      {
         $this->meldung=UM_KEIN_BEARBEITEN;
         return false;
      }
      
      $this->meldung=UM_ERFOLG_PASSWORT;
      return true;
   }
   
   public function loeschen($id)
   {
      if (empty($id))
      {
         if ($this->warnings)
            trigger_error('Id parameter for function loeschen is empty. Assuming id=$_SESSION[\'ID\']',E_USER_NOTICE);
         $id=$this->loginmanager->ID;
      }

      //Ist der User eingeloggt?
      if (!$this->loginmanager->login_status())
      {
         $this->meldung=UM_KEIN_LOGIN;
         return false;
      }
      
      //Will der User einen anderen Account löschen, benötigt er Adminrechte.
      if (!$this->loginmanager->is_admin() and $this->loginmanager->ID!=$id)
      {
         $this->meldung=UM_KEIN_ADMIN;
         return false;
      }
      
      //Will der User den Account mit dem Namen 'admin löschen?
      $account=$this->db->anzeigen($id, 'account');
      if ($account->loginname=='admin' and $this->loginmanager->name!='admin')
      {
         $this->meldung=UM_KEIN_HARTADMIN;
         return false;
      }
      
      //Ist das der letzte Admin Account?
      $admins=$this->db->liste('(statusflag & 4)=4','account');
      if ($admins->size()==1 and $admins[0]->ID==$id)
      {
         $this->meldung=UM_LETZTER_ADMIN;
         return false;
      }
      
      $result=$this->db->loeschen($id, 'account');
      
      if (!$result)
      {
         $this->meldung=UM_KEIN_LOESCHEN;
         return false;
      }

      $this->loginmanager->update_user();      
      $this->meldung=UM_ERFOLG_LOESCHEN;
      return true;
   }
   
   public function alle_anzeigen()
   {
      if (!$this->loginmanager->is_admin())
      {
         $this->meldung=UM_KEIN_ADMIN;
         return false;
      }
      $userliste=$this->db->liste('1=1','account');
      $this->meldung=UM_ERFOLG_ALLEZEIGEN;
      return $userliste;
   }
   
   public function anzeigen($id)
   {
      if (!$this->loginmanager->is_admin())
      {
         $this->meldung=UM_KEIN_ADMIN;
         return false;
      }
      $result=$this->db->anzeigen($id, 'account');
      if (!$result)
      {
         $this->meldung=UM_KEIN_ANZEIGEN;
         return false;
      }
      $this->meldung=UM_ERFOLG_ANZEIGEN;
      return $result;
   }
   
   public function meldung()
   {
      return $this->meldung;
   }
   
   
}
?>
