<?php 
  
require_once('../app_bootstap.php');
 
 use BECompare\Core\Compare2Url;
 use BECompare\Core\AnalyzeUrl;
 use BECompare\Core\Debug;

$host1="";
$host2="";

$text1=$host1.'udinese-esonerato-massimo-oddo';
$text2=$host2.'massimo-oddo-lascia-l-udinese';

$text3=$host1.'il-napoli-vince-lo-scudetto';
$text4=$host2.'grande-sarri-scudetto-al-napoli'; 



Debug::debug(AnalyzeUrl::validateDomain("https://www.miamammausalinux.org/2019/12/php-arriva-alla-versione-7-4-e-le-novita-sono-veramente-parecchie/"));


Debug::debug(AnalyzeUrl::getInfoUrl("https://www.miamammausalinux.org/2019/12/php-arriva-alla-versione-7-4-e-le-novita-sono-veramente-parecchie/"));


$compare = Compare2Url::getInstance();  
echo $text1. ' - '.$text2.' ='.$compare->compareUrl($text1,$text2, 'levenshtein').PHP_EOL;
echo $text3. ' - '.$text4.' ='.$compare->compareUrl($text1,$text2, 'levenshtein').PHP_EOL; 
 
 
 
echo $text1. ' - '.$text2.' ='.$compare ->compareUrl($text1,$text2, 'similar_text').PHP_EOL;
echo $text3. ' - '.$text4.' ='.$compare->compareUrl($text1,$text2, 'similar_text').PHP_EOL;   

 
echo $text1. ' - '.$text2.' ='.$compare->compareUrl($text1,$text2, 'similar_text2').PHP_EOL;
echo $text3. ' - '.$text4.' ='.$compare->compareUrl($text1,$text2, 'similar_text2').PHP_EOL; 

die();
 
?>