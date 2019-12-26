<?php 
  
require_once('../app_bootstap.php');
 
 use BECompare\Core\Compare2Url;

$host1="";
$host2="";

$text1=$host1.'udinese-esonerato-massimo-oddo';
$text2=$host2.'massimo-oddo-lascia-l-udinese';

$text3=$host1.'il-napoli-vince-lo-scudetto';
$text4=$host2.'grande-sarri-scudetto-al-napoli';



$compare = new Compare2Url();  
echo $text1. ' - '.$text2.' ='.$compare  -> compareUrl($text1,$text2, 'levenshtein').PHP_EOL;
echo $text3. ' - '.$text4.' ='.$compare  -> compareUrl($text1,$text2, 'levenshtein').PHP_EOL; 
 
 
$compare->algoritmo='similar_text';
echo $text1. ' - '.$text2.' ='.$compare  -> compareUrl($text1,$text2, 'similar_text').PHP_EOL;
echo $text3. ' - '.$text4.' ='.$compare  -> compareUrl($text1,$text2, 'similar_text').PHP_EOL;   

$compare->algoritmo='similar_text2';
echo $text1. ' - '.$text2.' ='.$compare  -> compareUrl($text1,$text2, 'similar_text2').PHP_EOL;
echo $text3. ' - '.$text4.' ='.$compare  -> compareUrl($text1,$text2, 'similar_text2').PHP_EOL; 

die();
 
?>