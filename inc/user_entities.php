<?php

class user_vmember extends vmember{

   public function beschreibung()
   {
      if (!isset($this->typ))
      {
         trigger_error('Could not make a description of termin because typ is not set',E_USER_NOTICE);
         return 'Keine Beschreibung verf&uuml;gbar';
      }
      if (empty($this->ort))
      {
         trigger_error('Could not make a description of termin because ort is empty',E_USER_NOTICE);
         return 'Keine Beschreibung verf&uuml;gbar';
      }
      if (!isset($this->zeit))
      {
         trigger_error('Could not make a description of termin because zeit is not set',E_USER_NOTICE);
         return 'Keine Beschreibung verf&uuml;gbar';         
      }

      switch($this->typ)
      {
         case TYP_SPIEL:
            if (empty($this->gegner))
            {
               trigger_error('Could not make a description of termin because gegner is not set and typ equals TYP_SPIEL', E_USER_NOTICE);
               return 'Keine Beschreibung verf&uuml;gbar';         
            }
            $beschreibung="Spiel gegen $this->gegner ($this->ort, ".date('d. m.',$this->zeit).")";
            break;
         case TYP_TRAINING:
            $beschreibung="Training ($this->ort, ".date('d. m.',$this->zeit).")";
            break;
         case TYP_FREI:
            if (empty($this->header))
            {
               trigger_error('Could not make a description of termin because header is empty and typ equals TYP_FREI', E_USER_NOTICE);
               return 'Keine Beschreibung verf&uuml;gbar';         
            }
            $beschreibung="$this->header ($this->ort, ".date('d. m.',$this->zeit).")";
            break;
         default:
            trigger_error('Could not make a description of termin because typ is not valid',E_USER_NOTICE);
            return 'Keine Beschreibung verf&uuml;gbar';
      }
   
      return $beschreibung;
   }


   public function name()
   {
      if (!empty($this->vorname))
         $name=$this->vorname;
      if (!empty($this->spitzname))
         $name.=" \"$this->spitzname\"";
      if (isset($name))
         return $name;
        
      //Ist der Loginname auch leer, dann können wir annehmen, dass der User gelöscht wurde
      if (empty($this->loginname))
         return "gel&ouml;schte Person";
      return $this->loginname;
   }
   
}


class termin extends user_vmember{

   protected $allowed=array('ID','typ','ort','zeit','gegner','liga','header','inhalt','ergebnisID');

   //Gibt die Möglichkeit, kommentar/einladungs Listen hier einzuhängen
   public $kommentarliste=null;
   public $einladungliste=null;
}

class ergebnis extends user_vmember{
   protected $allowed=array('ID','spielbericht','sichtbar','wirtore','dietore');
}

class liga extends user_vmember
{
   protected $allowed=array('ID','name','homepage');
}

class account extends user_vmember{
   protected $allowed=array('ID','loginname','passwort','vorname','spitzname','spielerID','statusflag','email');
}

class einladung extends user_vmember{
   protected $allowed=array('ID','accountID','terminID','zusageID');
}


class kommentar extends user_vmember{
   protected $allowed=array('ID','accountID','zeit','inhalt','bezugID','bezugstabelle');
}

class zeit extends user_vmember
{
   protected $allowed=array('stunde','minute','tag','monat','jahr');
   
   public function timestamp()
   {
      //Das hier muss praktischer werden und braucht Fehlerbehandlung.
      return mktime((int)$this->stunde, (int)$this->minute, 0, (int)$this->monat, (int)$this->tag, (int)$this->jahr);
   }
   
   public function set($timestamp)
   {
      $this->stunde=date('H',$timestamp);
      $this->minute=date('i',$timestamp);
      $this->tag=date('d',$timestamp);
      $this->monat=date('m',$timestamp);
      $this->jahr=date('Y',$timestamp);
   }
}

class db_entry extends user_vmember{

   public function __construct($array=null)
   {
      //ist wie vmember, aber erzeugt keine Warnung, obwohl only_allowed ausgeschaltet ist
      $this->only_allowed=false;
      @parent::__construct($array);
   }
}
?>
