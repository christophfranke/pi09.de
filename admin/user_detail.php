<?php
@session_start() or die('Could not start session in '.__FILE__);

include '../inc/all.php';

$loginmanager=new loginmanager();
$usermanager=new usermanager($loginmanager);
$user=$loginmanager->get_user();

if (empty($_GET['id']))
{
   $account=false;
   $action='javascript:anlegen.send();';
   $submit='anlegen';
   $mit_passwort=true;
   $mit_status=true;
   $pflichtfelder=true;
   echo '<h3>Benutzer erstellen</h3>';
   include 'user_form.php';
}
else
{
   $id=$_GET['id'];
   $account=$usermanager->anzeigen($id);
   $meldung=$usermanager->meldung();
   if ($account!==false)
   {
      $action='javascript:bearbeiten.send();';
      $submit='speichern';
      $mit_passwort=false;
      $mit_status=true;
      $mit_loeschen=true;
      $loeschen='loeschen.send();';
      echo '<h3>'.$account->name().'</h3>';
      include 'user_form.php';
   }
   else
      echo $meldung;
}
?>
