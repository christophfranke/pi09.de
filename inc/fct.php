<?php

function in($string)
{
   return mysql_real_escape_string(html_entity_decode($string,ENT_QUOTES,'iso-8859-1'));
}

function mask_br($string)
{
   return str_replace("\n",'\n',$string);  
}

function parse_links($text)
{
    $urlsearch[] = "/([^]_a-z0-9-=\"'\/])((https?|ftp):\/\/|www\.)([^ \r\n\(\)\*\^\$!`\"'\|\[\]\{\};<>]*)/si";
    $urlsearch[] = "/^((https?|ftp):\/\/|www\.)([^ \r\n\(\)\*\^\$!`\"'\|\[\]\{\};<>]*)/si";
    $urlreplace[]= "\\1[URL]\\2\\4[/URL]";
    $urlreplace[]= "[URL]\\1\\3[/URL]";
    $text = preg_replace($urlsearch, $urlreplace, $text);
    $text = preg_replace("/\[URL\](.*?)\[\/URL\]/si"      , "<a href=\"\\1\" target=\"_blank\">\\1</a>", $text);
    $text = preg_replace("/\[URL=(.*?)\](.*?)\[\/URL\]/si", "<a href=\"\\1\"target=\"_blank\">\\2</a>", $text);
    $text = str_replace("href=\"www","href=\"http://www",$text);
      
    return $text;
}

//Diese Funktion könnte man auch zum bb parser ausbauen. Das ist aber wahrscheinlcih garnicht nötig und eher verwirrend für den User.
function format_text($text)
{
   $text=str_replace("\n","<br />",$text);
   return parse_links($text);
}
?>
