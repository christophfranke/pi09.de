<?php

//---------------------------------VORSICHT: Kommentare in der Dokumentation sind eventuell nicht aktuell

//db modul
//benötigt: vmember, blanklist
//Konvention: Das db Modul geht davon aus, dass für jede Tabelle in der Datenbank eine Klasse mit dem gleichen Namen existiert,
//            die von vmember geerbt hat sowie eine Klasse mit dem Namen {Tabelle}liste, die von blanklist geerbt hat.
//            Das db Modul erstellt vor der Rückgabe einer Anfrage ein Objekt der entsprechenden Klasse, füllt es mit den
//            gelesenen Daten und gibt es dann zurück.

interface db_documentation{
   //Fehlerbehandlung erfolgt über Exceptions, nicht über Rückgabewerte.
   //Fehler sind ausschliesslich MySQL-Fehler. Das Löschen oder Bearbeiten einer nicht vorhandenen ID
   //ist hierbei KEIN Fehler, eine Benachrichtigung erfolgt über den Rückgabewert.

   //einzelne Einträge
   public function anlegen(vmember $eintrag); //Rückgabe ist die ID des erstellten Eintrags oder 0, falls kein autoincrement benutzt wird.
                                              //anlegen kann nur fehlschlagen durch einen MySQL Fehler.
   public function bearbeiten(vmember $eintrag); //Rückgabe: true, falls Eintrag bearbeitet wurde, false falls der Eintrag vorhanden ist.
                                                 //Die Methode bearbeiten erstellt keinen neuen Eintrag.
   public function loeschen($id, $tabelle); //Rückgabe: true, falls Eintrag gelöscht wurde, false, falls die id nicht vorhanden ist.
   public function anzeigen($id, $tabelle, $join1=null, $join2=null); //Rückgabe: Objekt mit den entsprechenden Daten, false, falls id nicht vorhanden ist.

   //Listen
   public function liste($where, $obj, $join='', $special=''); //Rückgabe: eine Liste vom Typ blanklist. Automatisiert joins.
   public function wieviel($where, $tabelle, $special=''); //Rückgabe: Die Anzahl gefundener Zeilen in $tabelle unter $where.
   public function exist($id, $tabelle);     //Rückgabe: true, falls der Eintrag mit primary key $id in $tabelle existiert, sonst false.
   public function liste_frei($where, $tabelle, $on,  $select, $obj, $listobj, $special=''); //Rückgabe: eine Liste vom Typ $listobj. Joins nicht automatisiert.
   
   //cleanup
   public function clean($where, $tabelle); //Rückgabe: Anzahl der gelöschten Einträge.
   

}

define('PRIMARY_KEY', 'ID');  //Der Primarykey muss für alle Tabellen gleich sein und hier angegeben werden. Der Primary Key benutzt AUTO-INCREMENT.

abstract class db_configuration implements db_documentation{
   public $set_null=false; //betrifft bearbeiten und anlegen. true bedeutet: gibt es eine spalte mit dem wert 'null' als String, so wird versucht,
                           //den Wert auf null zu setzen, anstatt den String 'null' zu schreiben.
}

class db extends db_configuration
{
   private $connection;

   function __construct()
   {
      $this->connection=mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE) or die('Could not connect to "'.MYSQL_HOST.'".');
      mysqli_set_charset($this->connection, 'latin1') or trigger_error('Could not set names to latin1', E_USER_WARNING);
   }
   
   function anlegen(vmember $eintrag)
   {
      $tabelle=get_class($eintrag);
      $header=$values='';
      foreach($eintrag as $key=>$value)
      {
         $value=in($value);
         if ($key==PRIMARY_KEY)
            continue;
            
         if ($this->set_null and $value==='null')
            continue;
            
         $header.="$key,";
         
         if (is_string($value))
            $values.="'$value',";
         else
            $values.="$value,";
      }
      $header=substr($header,0,-1);
      $values=substr($values,0,-1);

      $sql="INSERT INTO $tabelle ($header) VALUES ($values);";
      
      $res=mysqli_query($this->connection, $sql);
      if(!$res)
         throw new Exception("Error on $sql: ".mysqli_error($this->connection));
         
      return mysqli_insert_id($this->connection);
   }
   

   function bearbeiten(vmember $eintrag)
   {
      $tabelle=get_class($eintrag);
      $pairs='';
      foreach($eintrag as $key=>$value)
      {
         $value=in($value);
         if ($key==PRIMARY_KEY)
         {
            $where=PRIMARY_KEY."=$value";
            continue;
         }
                  
         if (is_string($value) and !($this->set_null and $value==='null'))
            $pairs.="$key='$value',";
         else
            $pairs.="$key=$value,";
      }
      if (empty($where))
      {
         trigger_error('Could not update database in bearbeiten, no primary key given',E_USER_ERROR);
         return false;
      }
      
      $pairs=substr($pairs,0,-1);
      
      $sql="UPDATE $tabelle SET $pairs WHERE $where;";
      
      $res=mysqli_query($this->connection, $sql);
      if (!$res)
         throw new Exception("Error on $sql: ".mysqli_error($this->connection));

      $rows=mysqli_affected_rows($this->connection);
      
      if ($rows==0)
         return false;
      if ($rows==1)
         return true;
      throw new Exception("DB inconsistency: $tabelle has more than 1 entry with ".PRIMARY_KEY."=$id");         
   }
   
   function loeschen($id, $tabelle)
   {
      $sql="DELETE FROM $tabelle WHERE ".PRIMARY_KEY."=$id;";
      
      $res=mysqli_query($this->connection, $sql);
      if (!$res)
         throw new Exception("Error on $sql: ".mysqli_error($this->connection));

      $rows=mysqli_affected_rows($this->connection);
      if ($rows==0)
         return false;
      if ($rows==1)
         return true;
      throw new Exception("DB inconsistency: $tabelle has more than 1 entry with ".PRIMARY_KEY."=$id");         
   }
   
   function anzeigen($id, $tabelle, $join1=null, $join2=null)
   {
      if ($join1==null and $join2==null)
         $sql="SELECT * FROM $tabelle WHERE ".PRIMARY_KEY."=$id;";

      if ($join1!=null and $join2==null)
         $sql="SELECT *,$tabelle.".PRIMARY_KEY." AS ".PRIMARY_KEY." FROM $tabelle
            LEFT JOIN $join1 ON $tabelle.$join1".PRIMARY_KEY."=$join1.".PRIMARY_KEY."
            WHERE $tabelle.".PRIMARY_KEY."=$id;";

      if ($join2!=null)
         trigger_error('This function is not capable of handle two joins yet, although the interface is given.',E_USER_ERROR);
      

      $res=mysqli_query($this->connection, $sql);
      if (!$res)
         throw new Exception("Error on $sql: ".mysqli_error($this->connection));
      if (mysqli_num_rows($res)>1)
      throw new Exception("DB inconsistency: $tabelle has more than 1 entry with ".PRIMARY_KEY."=$id");         
         
      $daten=mysqli_fetch_assoc($res);
      if ($daten===false)
         return false;
      $obj=new db_entry($daten);
      return $obj;
   }
   
   
   function liste($where, $obj, $join='', $special='')
   {
      if (empty($where))
         $where='1=1';

      $sql="SELECT *,$obj.".PRIMARY_KEY." FROM
               $obj LEFT JOIN $join ON $obj.$join".PRIMARY_KEY."=$join.".PRIMARY_KEY."
            WHERE $where $special;";

      if ($join==='')
         $sql="SELECT * FROM $obj WHERE $where $special;";
         
      $res=mysqli_query($this->connection, $sql);
      if (!$res)
         throw new Exception("Error on $sql: ".mysqli_error($this->connection));

      $liste=new blanklist();
         
      while ($zeile = mysqli_fetch_assoc($res))
         $liste->add(new db_entry($zeile));

      return $liste;
   }
   

   //hoffentlich kann diese Funktion bald abgeschafft werden
   public function liste_frei($where, $tabelle, $on,  $select, $obj, $listobj, $special='')
   {
      trigger_error('The method liste_frei does not check your input yet and is therefor not safe to use. If possible, do not use it.',E_USER_NOTICE);

      $sql="SELECT $select FROM

               $tabelle ON $on

            WHERE $where $special;";
         
      $res=mysqli_query($this->connection, $sql);
      if (!$res)
         throw new Exception("Error on $sql: ".mysqli_error($this->connection));

      $liste=new $listobj();
         
      while ($zeile = mysqli_fetch_assoc($res))
         $liste->add(new $obj($zeile));

      return $liste;
   }

   
   function clean($where, $tabelle)
   {
      if (empty($where))
         $where='1=1';
      $sql="DELETE FROM $tabelle WHERE $where;";
      
      $res=mysqli_query($this->connection, $sql);
      if (!$res)
         throw new Exception("Error on $sql: ".mysqli_error($this->connection));
      
      return mysqli_affected_rows($this->connection);
   }
   
   
   function wieviel($where, $tabelle, $special='')
   {
      if (empty($where))
         $where='1=1';
         
      $sql="SELECT COUNT(*) AS wieviel FROM $tabelle WHERE $where $special;";
      
      $res=mysqli_query($this->connection, $sql);
      if (!$res)
         throw new Exception("Error on $sql: ".mysqli_error($this->connection));
         
      $erg=mysqli_fetch_assoc($res);
      if ($erg===false or !isset($erg['wieviel']))
         trigger_error('Error in MySQL: Sent count query and nothing came back although MySQL did not have an error.',E_USER_ERROR);
   
      return $erg['wieviel'];
   }
   
   
   function exist($id, $tabelle)
   {
      if (!isset($id))
      {  
         trigger_error('You have to provide an id to the exist function, which may also be a MySQL variable name instead of an integer.',E_USER_WARNING);
         return false;
      }
      $res=$this->wieviel(PRIMARY_KEY."=$id",$tabelle);
      if ($res==0)
         return false;                    
      if ($res==1)
         return true;
      throw new Exception("Discovered a database inconsistency: The table $tabelle has two more than one ($res) entry with the primary key ".PRIMARY_KEY."=$id.");
   }
      
}


?>
