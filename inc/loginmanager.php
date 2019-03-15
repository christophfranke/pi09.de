<?php

//modul loginmanager
//loginmanager benötigt account
//Das Loginmanager-Modul ist dafür verantwortlich, einen User über das Sessionmodul zu verfolgen und 
interface loginmanager_documentation
{

   public function login($name, $passwort); //Loginversuch mit diesem Namen/Passwort. Rückgabe true bei Erfolg, sonst false.
   public function logout(); //Zerstört die Session und die dazugehörigen Variablen.
   public function get_user(); //Gibt den momentan eingeloggten User zurück. Gibt false zurück, falls niemand eingeloggt.
   public function update_user(); //Zwingt den loginmanager, die Informationen zum aktuell eingeloggten User neu aus der Datenbank zu lesen.
   public function login_status(); //Gibt an, ob ein User eingeloggt ist oder nicht (true/false).
   public function has_right($right); //Gibt an, ob ein User die angegebenen Rechte besitzt (true/false).
   public function has_login(); //Gibt an, ob der User das Login-Recht besitzt (STATUS_LOGIN).
   public function is_spieler();//Gibt an, ob der User das Spieler-Recht besitzt (STATUS_SPIELER).
   public function is_admin();  //Gibt an, ob der User das Admin-Recht besutzt (STATUS_ADMIN).
}

abstract class loginmanager_configuration implements loginmanager_documentation
{

   //diese funktionen werden vom loginmanager aufgerufen, um mit der datenbank zu kommunizieren.
   protected $func_login='login'; //funktion, die name und passwort erwartet und mit der datenbank abgleicht.
                                  //Gibt account zurück bei erfolg, sonst false.
   protected $func_info='info'; //funktion, die einen account mit der gewünschten id zurückgibt. existiert die id nicht, ist die rückgabe false.
   protected $func_register='register'; //funktion, die einen account erwartet und diesen in der Datenbank anlegt.
                                        //Falls schon ein account mit dem gewünschten loginname existiert,
                                        //bricht die registrierung ab und die Funktion liefert false als Rückgabe (sonst true im Erfolgsfall).
   protected $obj; //obj, dem die obigen funktionen gehören. null falls, keinem objekt angehörig.
   
   
   public function __construct()
   {
      $this->obj=new loginmanager_db();
   }
   
      
}

//Schnittstelle zum db modul
class loginmanager_db{


   private $db;


   public function __construct()
   {
      $this->db=new db();
   }


   public function login($name, $passwort)
   {
      $account=$this->db->liste("loginname='$name' AND (statusflag & 1)=1",'account');
      if ($account->get_size()==0)
         return false;
      if ($account[0]->passwort!=md5($passwort))
         return false;
      return $account[0];
   }
   
   public function info($id)
   {
      return $this->db->anzeigen($id, 'account');
   }
   
   public function register(account $account)
   {
      $exist=$this->db->liste("loginname='$account->loginname' AND (statusflag & 1)=1",'account');
      if ($exist->get_size()>0)
         return false;
         
      $this->db->anlegen($account);
      return true;
   }
   
   public function update(account $account)
   {
      return $this->db->bearbeiten($account);
   }
}



class loginmanager extends loginmanager_configuration
{

   private $login_status;
   public $user;
   
   function __construct()
   {
      parent::__construct();
      $this->login_status=false;
      if (isset($_SESSION['ID']))
         $this->login_status=true;
   }
   
   
   function login($name,$passwort)
   {
      if ($this->obj==null)
         $account=call_user_fun($this->func_login, $name, $passwort);
      else
         $account=call_user_func(array($this->obj,$this->func_login), $name, $passwort);
      if ($account===false)
         return false;

      $this->user=$account;
      $this->login_status=true;
      $this->set_session();
      return true;
   }

   function logout()
   {
      unset($this->user);
      unset($_SESSION['ID']);
      unset($_SESSION['name']);
      unset($_SESSION['status']);
      $_SESSION=array();
      session_destroy();
      $this->login_status=false;
   }


   function get_user()
   {
      if (!$this->login_status)
         return false;
      if (isset($this->user))
         return $this->user;
      return $this->update_user();
   }
   
   public function update_user()
   {
      if (!$this->login_status)
         return false;
      if ($this->obj==null)
         $account=call_user_func($this->func_info, $this->ID);
      else
         $account=call_user_func(array($this->obj, $this->func_info), $this->ID);
      if ($account===false)
      {
         $this->logout();
         return false;
      }
      $this->user=$account;
      return $account;
   }
   
   
   //veraltet. Diese Funktion wird übernommen vom Modul usermanager
   private function register(account $account)
   {
      if (empty($account->loginname))
         return false;
      if (empty($account->passwort))
         return false;
      $account->statusflag=STATUS_LOGIN;
      $account->passwort=md5($account->passwort);
      
         
        
      if ($this->obj==null)
         $res=call_user_func($this->func_register, $account);
      else
         $res=call_user_func(array($this->obj, $this->func_register), $account);
      if (!$res)
         return false; 
         
      $this->login($account->loginname, $account->passwort);
      return $this->login_status;
   }
   
   function login_status()
   {
      return $this->login_status;
   }
   
   //Es gibt keinen vernünftigen Grund, dass diese Funktion von Aussen gebraucht wird
   private function set_session()
   {
      $_SESSION=array();
      $_SESSION['ID']=$this->user->ID;
      $_SESSION['name']=$this->user->loginname;
      $_SESSION['status']=$this->user->statusflag;
   }
   
   function has_right($right)
   {
      if (!$this->login_status)
         return false;
         
      return ( ($this->status & $right) == $right);
   }
   
   function has_login()
   {
      return $this->has_right(STATUS_LOGIN);
   }
   
   function is_spieler()
   {
      return $this->has_right(STATUS_SPIELER);
   }
   
   function is_admin()
   {
      return $this->has_right(STATUS_ADMIN);
   }

   function __get($var)
   {
      if (!$this->login_status)
         return false;
      return $_SESSION[$var];
   }
   
   function __set($var, $val)
   {
      if(!$this->login_status)
         return;
      if ($var=='ID' or $var=='name' or $var=='status') //schreibschutz für status, id und name
      {
         trigger_error('The session variables "ID", "name" and "status" are read only',E_USER_WARNING);
         return;
      }
      $_SESSION[$var]=$val;
   }
   
   function __isset($var)
   {
      if (!$this->login_status)
         return false;
      return isset($_SESSION[$var]);
   }
   
   function __empty($var)
   {
      if (!$this->login_status)
         return true;
      return empty($_SESSION[$var]);
   }

}


?>
