<?php 
 
namespace Crawl;

include('./src/core/CrawlWebsite.php');
include('./src/core/CompareUrl.php');
  
 
 
function findAndCompare (){
 
    $validateInput = true;
    if (empty($_GET['domain1']) || empty($_GET['domain2']))
        $validateInput=false; 

    $domain1 = urldecode($_GET['domain1']);
    $domain2 = urldecode($_GET['domain2']);
 
    if(substr($domain1,-1)!='/')
            $domain1.='/';
    
    if(substr($domain2,-1)!='/')
            $domain2.='/';
    
      
    if ($validateInput == true)
        $validateInput =  $domain1 === $domain2 ? false : true;

    if ($validateInput == true)
        $validateInput =  CrawlWebsite::validateDomain($domain1);
    
    if ($validateInput == true)
        $validateInput =  CrawlWebsite::validateDomain($domain2);

       
    if ($validateInput === true){ 

        $errorParse = false; 
        $list1 = CrawlWebsite::crawl($domain1);  
        if(empty($list1))
            $errorParse = true;
          
        if ($errorParse = false)    
            $list2 = CrawlWebsite::crawl($domain2);
        
        if(empty($list2))
            $errorParse = true;        
        
        if ($errorParse === false)    
            CompareUrl::compare($domain1, $list1, $domain2,$list2,'csv');        
    }  
    if ($validateInput === false)  {

        echo 'Error Input';
 
    }elseif ($errorParse === true)  {

        echo 'Error Parsing Website';
 
    }else
     CompareUrl::compare($domain1, $list1, $domain2,$list2,'csv');      
 
}  

/*
$testCsv = true;
$testCsv = false;

if ($testCsv==true){
    $filename="ResultCompareWebSite_".date('YmdHis').".csv";
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=".$filename);
    header("Pragma: no-cache");
    header("Expires: 0"); 
    //findAndCompare($domain1, $domain2);
    echo 'testtt';
}else 
    echo 'testtt';

die();
*/
$filename="ResultCompareWebSite_".date('YmdHis').".csv";
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0");
findAndCompare();
?>