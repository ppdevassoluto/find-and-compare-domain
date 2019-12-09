<?php   
require_once('./config/config.php');
require_once('./config/autoconfig.php');  
require_once('./config/autoload.php');
 
use src\service\CrawlAndCompare2Domain;
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