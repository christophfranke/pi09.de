
<?php
$action='javascript:document.getElementById(\'registerbutton\').disabled=true;register.send();';
$submit='registrieren';
$mit_passwort=true;
$pflichtfelder=true;
$mit_status=false;
$mit_abbrechen=true;
$abbrechen='javascript:registerWindow.close();';
?>

<div id="registerbox">
<h3>Benutzerkonto erstellen</h3>
<br />
<?php
include '../formulare/userform.php';
?>
</div>

<div id="registertext">
<br />
Felder mit einem * sind Pflichtfelder.
<br /> 
<br />
Information zur Emailaddresse:
<br />Die Emailaddresse ist optional und und kann nur von den Administratoren angesehen werden.
Wenn du eine Emailaddresse angibst, bekommst du eine Benachrichtigung, wenn du zu einem Termin eingeladen wurdest.
Wenn du keine Emails mehr bekommen m&ouml;chtest, kannst du diese Funktion jederzeit deaktivieren.
<br />
<br />
Informationen zum Passwort
<br />Dein Passwort wird nicht im Klartext gespeichert. Das bedeutet niemand - auch nicht die Programmierer oder Admins - kann dein Passwort sehen.
</div>

