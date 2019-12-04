<?php
namespace Crawl;
 
class CrawlAndCompareUrl extends CompareUrl{ 

    var $returnResult = 'csv' ; 

 
    function findAndCompare($domain1='', $domain2=''){
 
        $validateInput = true;
        if (empty($domain1) || empty($domain2))
            $validateInput=false; 

     
        if(substr($domain1,-1)!='/')
                $domain1.='/';
        
        if(substr($domain2,-1)!='/')
                $domain2.='/';
        
          
        if (false && $validateInput === true)
            $validateInput =  $domain1 === $domain2 ? false : true;
    
        if ($validateInput === true)
            $validateInput =  CrawlWebsite::validateDomain($domain1);
        
        if ($validateInput === true)
            $validateInput =  CrawlWebsite::validateDomain($domain2);
    
           
        if ($validateInput === true){ 
    
            $errorParse = false; 
            $list1 = CrawlWebsite::crawl($domain1);  
            if(empty($list1))
                $errorParse = true;
              
            if ($errorParse === false)    
                $list2 = CrawlWebsite::crawl($domain2);
            
            if(empty($list2))
                $errorParse = true; 
      
        }  
        if ($validateInput === false)  {
    
            $this->writeResult('Error Input;;;');
     
        }elseif ($errorParse === true)  {
            $this->writeResult('Error Parsing Websites;;;'); 
     
        }else
             $this->compareUrl($list1, $list2);   
     
    }  
    

}