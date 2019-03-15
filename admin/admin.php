<?php
@session_start() or die('Could not start session in '.__FILE__);

include ('../inc/all.php');

$loginmanager=new loginmanager();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
 <title>Adminbereich</title>
 <link rel="stylesheet" type="text/css" href="admin.css" />
</head>

<body onload="init();">

<a href="admin.php?frame=login">Login</a>
<a href="admin.php?frame=user">Usermanager</a>
<a href="admin.php?frame=termin">Terminmanager</a>
<a href="admin.php?frame=ergebnis">Ergebnismanager</a>
<a href="admin.php?frame=liga">Ligamanager</a>
<br />
<div>
<?php
if (isset($_GET['frame']))
{
   switch($_GET['frame'])
   {
      case 'user':
         include('user.php');
         break;
      case 'termin':
         include('termin.php');
         break;
      case 'ergebnis':
         include('ergebnis.php');
         break;
      case 'liga':
         include('liga.php');
         break;
      default:
         include('login.php');
         break;
   }
}
else
   include('login.php');
?>

</body>
</html>
