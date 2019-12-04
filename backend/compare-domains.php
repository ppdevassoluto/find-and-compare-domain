<?php 
 
namespace Crawl;
require_once('./config/config.php');
require_once('./src/core/CrawlWebsite.php');
require_once('./src/core/CompareUrl.php');
require_once('./src/core/CrawlAndCompareUrl.php');
  
 
function findAndCompare($returnResult='csv'){
    
    if(!isset($_GET['domain1']))
        $domain1 = '';
    else
        $domain1 = urldecode($_GET['domain1']);   

    if(!isset($_GET['domain2']))
        $domain2 = '';
    else
        $domain2 = urldecode($_GET['domain2']);    
        
    $compareDomain = new CrawlAndCompareUrl();  
    $rowHeaderCsv=$domain1.';'.$domain2.';percentage compare;';
    $compareDomain->writeRowHeaderCsv($rowHeaderCsv);
    $compareDomain->returnResult($returnResult);
    $compareDomain->findAndCompare($domain1,$domain2);

}
 
findAndCompare($appConfig['returnResultCompare']); 
?>