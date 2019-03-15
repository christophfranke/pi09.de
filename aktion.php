<?php
@session_start() or die('0Could not start session in '.__FILE__);

include 'inc/all.php';

$loginmanager=new loginmanager();
$usermanager=new usermanager($loginmanager);
$kommentarmanager=new kommentarmanager($loginmanager);
$terminmanager=new terminmanager($loginmanager);
$ergebnismanager=new ergebnismanager($loginmanager);
$einladungsmanager=new einladungsmanager($loginmanager);
$ligamanager=new ligamanager($loginmanager);

@$aktion=$_GET['aktion'];

if (empty($aktion))
   die('0Keine Aktion angegeben');
   
$status=0;
$meldung='Diese Funktion ist noch nicht implementiert';
   
switch($aktion)
{
   case 'login':
      @$status=$loginmanager->login($_POST['loginname'],$_POST['passwort']);
      if ($status)
         $meldung="Login erfolgreich. Willkommen $loginmanager->name.";
      else
         $meldung="Benutzername oder Passwort stimmen nicht.";
      break;
      
   case 'logout':
      @$loginmanager->logout();
      $status=true;
      $meldung='Du bist jetzt abgemeldet.';
      break;
      
   case 'register':
      @$account=new account($_POST);
      @$wdh=$_POST['passwortwiederholung'];
      $status=$usermanager->register($account,$wdh);
      $meldung=$usermanager->meldung();
      if ($status===true)
         $loginmanager->login($account->loginname,$wdh);
      break;
      
   case 'anlegen_account': //legt einen account an, führt aber keinen login durch
      @$account=new account($_POST);
      @$wdh=$_POST['passwortwiederholung'];
      $status=$usermanager->register($account,$wdh);
      $meldung=$usermanager->meldung();
      break;
      
   case 'bearbeiten_account':
      @$account=new account($_POST);
      $status=$usermanager->bearbeiten($account);
      $meldung=$usermanager->meldung();
      break;
      
   case 'loeschen_account':
      @$id=$_POST['ID'];
      $status=$usermanager->loeschen($id);
      $meldung=$usermanager->meldung();
      break;
      
   case 'bearbeiten_passwort':
      @$passwort=$_POST['passwort'];
      @$passwortwdh=$_POST['passwortwdh'];
      $status=$usermanager->passwort($passwort, $passwortwdh);
      $meldung=$usermanager->meldung();
      break;
      
   case 'anlegen_kommentar':
      @$kommentar=new kommentar($_POST);
      $status=$kommentarmanager->anlegen($kommentar);
      $meldung=$kommentarmanager->meldung();
      break;
      
   case 'loeschen_kommentar':
      @$id=$_POST['ID'];
      $status=$kommentarmanager->loeschen($id);
      $meldung=$kommentarmanager->meldung();
      break;
   
   case 'anlegen_termin':
      @$termin=new termin($_POST);
      @$zeit=new zeit($_POST);
      @$status=$terminmanager->anlegen($termin,$zeit);
      $meldung=$terminmanager->meldung();
      //temporäres workaround für adminbereich:
      //Der Termin muss ermittelt werden, auch das ein Workaround über direkten Zugriff auf class db
      if ($status)
      {
         //Termin mit der größten ID herrausfinden
         // Hier sollte man auf keinen Fall eine neue Datenbankverbindung aufbauen. Horrible!
         $connection=mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE) or die('Could not connect to "'.MYSQL_HOST.'".');
         mysqli_set_charset($this->connection, 'latin1') or trigger_error('Could not set names to latin1', E_USER_WARNING);
         $sql='SELECT ID FROM termin ORDER BY ID DESC LIMIT 1';
         $res=mysqli_query($connection, $sql);
         if (!$res)
            throw new Exception("Mysql Error on $sql ".mysqli_error($connection));
         $zeile=mysqli_fetch_assoc($res);
         $id=$zeile['ID'];
         $meldung.='
         <form id="sendVars">
         <input type="hidden" name="id" value="'.$id.'" />
         </form>
         ';
      }
      break;
      
   case 'bearbeiten_ergebnis':
      @$ergebnis=new ergebnis($_POST);
      $ergebnis->ID=$_POST['ergebnisID']; //diese ID soll das ergebnis haben
      unset($_POST['ergebnisID']);
      $status=$ergebnismanager->bearbeiten($ergebnis, $_POST['ID']); //bearbeiten sucht automatisch ein vorhandenes Ergebnis, ansonsten wird es neu angelegt
      $meldung=$ergebnismanager->meldung();
      //den Termin gleich mitbearbeiten, aber nur wenn bis hierhin kein Fehler aufgetreten ist
      if (!$status)
         break;

   case 'bearbeiten_termin':
      @$termin=new termin($_POST);
      @$zeit=new zeit($_POST);
      @$status=$terminmanager->bearbeiten($termin,$zeit);
      $meldung=$terminmanager->meldung();
      break;
      
   case 'loeschen_termin':
      @$id=$_POST['ID'];
      $status=$terminmanager->loeschen($id);
      $meldung=$terminmanager->meldung();
      break;
            
   case 'einladen_spieler':
      @$id=$_POST['ID'];
      $status=$einladungsmanager->einladen_spieler($id);
      $meldung=$einladungsmanager->meldung();
      break;
      
   case 'einladen_alle':
      @$id=$_POST['ID'];
      $status=$einladungsmanager->einladen_alle($id);
      $meldung=$einladungsmanager->meldung();
      break;
      
   case 'ausladen_alle':
      @$id=$_POST['ID'];
      $status=$einladungsmanager->ausladen_alle($id);
      $meldung=$einladungsmanager->meldung();
      break;
      
   case 'loeschen_einladung':
      @$id=$_POST['ID'];
      $status=$einladungsmanager->loeschen($id);
      $meldung=$einladungsmanager->meldung();
      break;
      
   case 'einladung_zusagen':
      @$id=$_POST['id'];
      @$zusage=$_POST['zusage'];
      $status=$einladungsmanager->einladung_zusagen($id,$zusage);
      $meldung=$einladungsmanager->meldung();
      break;
      
   case 'bearbeiten_zusage':
      @$einladung=new einladung($_POST);
      $status=$einladungsmanager->bearbeiten($einladung);
      $meldung=$einladungsmanager->meldung();
      break;
      
   case 'anlegen_liga':
      @$liga=new liga($_POST);
      $status=$ligamanager->anlegen($liga);
      $meldung=$ligamanager->meldung();
      break;
      
   case 'bearbeiten_liga':
      @$liga=new liga($_POST);
      $status=$ligamanager->bearbeiten($liga);
      $meldung=$ligamanager->meldung();
      break;
      
   case 'loeschen_liga':
      @$id=$_POST['ID'];
      $status=$ligamanager->loeschen($id);
      $meldung=$ligamanager->meldung();
      break;
   
      
   default:
      $status=0;
      $meldung="Die angeforderte Aktion $aktion wurde nicht gefunden";
      break;
      
}
echo ((int)$status).$meldung;
if ($_GET['debug']=='true')
   echo var_dump($_POST);
?>
