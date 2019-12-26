<?php   
require_once('./app_bootstap.php');
 
use BECompare\Controller\CrawlAndCompare2Domain;
function findAndCompare(){
    
    if(!isset($_GET['domain1']))
        $domain1 = '';
    else
        $domain1 = urldecode($_GET['domain1']);   

    if(!isset($_GET['domain2']))
        $domain2 = '';
    else
        $domain2 = urldecode($_GET['domain2']);    
        
    $compare2Domain = new CrawlAndCompare2Domain();  
    $compare2Domain->findAndCompare($domain1,$domain2);

}
 
findAndCompare(); 
?>