<form action="<?php echo $action;?>" method="post" id="ergebnisform">
<input type="hidden" name="ID" value="<?php if (isset($termin->ID)) echo $termin->ID;?>" <?php if (!isset($termin->ID)) echo 'disabled="disabled"';?> />
<input type="hidden" name="ergebnisID" value="<?php if (isset($termin->ergebnisID)) echo $termin->ergebnisID;?>" />
<table>
 <tr>
  <td>Sichtbar:</td>
  <td><input type="checkbox" name="sichtbar" <?php if (isset($termin->sichtbar) and $termin->sichtbar) echo "checked";?> /></td>
 </tr>
 <tr>
  <td>Termintyp:</td>
  <td>
  <input type="radio" name="typ" value="<?php echo TYP_SPIEL;?>" onClick="selectSpiel();" />
  Spiel
  <input type="radio" name="typ" value="<?php echo TYP_TRAINING;?>" onClick="selectTraining();" />
  Training
  <input type="radio" name="typ" value="<?php echo TYP_FREI;?>" onClick="selectFrei();" />
  Freier Termin
  </td>
 </tr>
 <tr>
  <td>Liga:</td>
  <td>
   <select name="liga">
   <?php
   if (isset($ligaliste))
      foreach($ligaliste as $liga)
         echo '<option value="'.$liga->ID.'">'.$liga->name.'</option>';
   ?>
   </select>
  </td>
 </tr>   
 <tr>
  <td>Gegen:</td>
  <td><input type="text" name="gegner" size=39 onkeyup="copyGegner();" value="<?php if (isset($termin->gegner)) echo $termin->gegner;?>" /></td>
 </tr>
 <tr>
  <td>&Uuml;berschrift:</td>
  <td><input type="text" name="header" size=39 value="<?php if (isset($termin->header)) echo $termin->header;?>" /></td>
 </tr>
 <tr>
  <td>Wo:</td>
  <td><input type="text" name="ort" value="<?php if (isset($termin->ort)) echo $termin->ort;?>" size=39 /></td>
 </tr>
 <tr>
  <td>Wann:</td>
  <td>
   Um <input type="text" class="zeit" name="stunde" value="<?php if (isset($zeit->stunde)) echo $zeit->stunde;?>" size=5 />
   : <input type="text" class="zeit" name="minute" value="<?php if (isset($zeit->minute)) echo $zeit->minute;?>" size=5 /> Uhr
  </td>
 </tr>
 <tr>
  <td></td>
  <td> 
   Am <input type="text" class="zeit" name="tag" value="<?php if (isset($zeit->tag)) echo $zeit->tag;?>" size=5 />
   . <input type="text" class="zeit" name="monat" value="<?php if (isset($zeit->monat)) echo $zeit->monat;?>" size=5 />
   . <input type="text" class="zeit" name="jahr" value="<?php if (isset($zeit->jahr)) echo $zeit->jahr; else echo '2011';?>" size=5 />
  </td>
 </tr>
 <tr>
  <td>Ergebnis:</td>
  <td>Wir Tore: <input type="text" name="wirtore" value="<?php if (isset($termin->wirtore)) echo $termin->wirtore;?>" size="5" />
      Die Tore: <input type="text" name="dietore" value="<?php if (isset($termin->dietore)) echo $termin->dietore;?>" size="5" />
  <td>
 </tr> 
 <tr>
  <td>Spielbericht:</td>
  <td><textarea name="spielbericht" cols="50" rows="10"><?php if (isset($termin->spielbericht)) echo $termin->spielbericht;?></textarea></td>
 </tr>
 <tr>
  <td></td>
  <td><input type="submit" class="submit" value="<?php echo $submit;?>" /></td>
 </tr>
</table>
</form>

