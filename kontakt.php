<?php session_start() or die('Coud not start session in file '.__FILE__);

include 'inc/all.php';

?>
<div id="kontakt" class="transparent">
<h3>Impressum und Kontakt</h3>
<br />
<h4>Administratoren und inhaltliche Pflege sowie sportliche Ansprechpartner</h4>
fab #at# pi09 punkt de
<br />alex #at# pi09 punkt de
<br />remy #at# pi09 punkt de
<br /><br />
<h4>Webmaster</h4>
webmaster #at# pi09 punkt de
<br /><br />
<?php
$loginmanager=new loginmanager();
if ($loginmanager->is_admin() or $loginmanager->is_spieler())
   echo "<h4>Kontodaten</h4>
         Phrasendrescher-Konto<br />
         Kontoinhaber: Alexander Apke<br />
         IBAN: DE12 2004 1144 0360 4733 00<br />
         Bank: comdirect bank AG
         <br /><br />

<h4>Teamkontakte</h4>
        <table>
        <tr>
        <th>Name</th> <th>Email</th> <th>Handy</th>
        </tr>
        <tr>
        <td>Alexander Apke</td> <td>alexander.apke@gmail.com</td> <td>017622786623</td>
        </tr>
        <tr>
        <td>Sebastian Diekmann</td> <td>seb_diekmann@web.de</td> <td>017697842045</td>
        </tr>
        <tr>
        <td>Stefan Effing</td> <td>stefan_effing@web.de</td> <td>017661749339</td>
        </tr>
        <tr>
        <td>Dominik Forstmaier</td> <td>dforstmaier@gmail.com</td> <td>017623349496</td>
        </tr>
        <tr>
        <td>Fabian Gratzla</td> <td>fabiangratzla@gmx.de</td> <td>015159455675</td>
        </tr>
        <tr>
        <td>Markus Hallmann</td> <td>markushallmann5@googlemail.com</td> <td>015732343003</td>
        </tr>
        <tr>
        <td>Hannes Poyda</td> <td>hpoyda@web.de</td> <td>01724576532</td>
        </tr>
        <tr>
        <td>Gerke Rademacher</td> <td>gerke.rade@web.de</td> <td>01783234188</td>
        </tr>
        <tr>
        <td>Florian Stockschläger</td> <td>tba</td> <td>01783438547</td>
        </tr>
        <tr>
        <td>Pedro Villa</td> <td>pedrito_vee@hotmail.com</td> <td>017623686467</td>
        </tr>
        <tr>
        <td>Stephan Vogelgsang</td> <td>joergbeach@gmx.de</td> <td>tba</td>
        </tr>
        <tr>
        <td>Jack Voss</td> <td>jackvoss2000@yahoo.de</td> <td>tba</td>
        </tr>
        <tr>
        <td>Tammo Willenberg</td> <td>lord-tammo@hotmail.de</td> <td>015755999317</td>
        </tr>
        <tr>
        <td>Dennis Wolf</td> <td>dennis@96zig.de</td> <td>016097670096</td>
        </tr>
        <tr>
        <td>Chris Zuther</td> <td>chrisZ2158@gmx.de</td> <td>tba</td>
        </tr>
        <tr>
        <td>Kailton</td> <td>tba</td> <td>017661188971</td>
        </tr>
        </table>

        <br />";
?>
<h4>Credits</h4>
Programmierung: Christoph Franke
<br />Design: Fabian Kampa
<br /><br />
</div>