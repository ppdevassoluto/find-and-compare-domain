<?php
namespace BECompare\Core;
use  \DOMDocument;
class UtilityUrl { 
 
 

    static public  function validateDomain($domain=''){

  
        $domain = filter_var($domain, FILTER_SANITIZE_URL);  
        $domainInfo = self::extractInfoUrl($domain); 
        
        if (    
            !isset($domainInfo['scheme']) 
            || ($domainInfo['scheme']!='https' && $domainInfo['scheme']!='http') 
            || !isset($domainInfo['host']) )
            return false;  

        if ($domainInfo['path']!=='/')
            return false;

        if (!filter_var($domain, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) 
            return false; 
        else 
            return true;
     
    } 

    static public function extractInfoUrl($url=''){


        $parseUrl = parse_url($url);  
    
        if (isset($parseUrl['path'])){
           $parseUrl['depthLevel'] = substr_count($parseUrl['path'],'/'); 
        }else{
            $parseUrl['depthLevel'] = 0;
            $parseUrl['path'] = '/';    
        } 

        /*print"----".PHP_EOL;
        print_r($url);
        print"".PHP_EOL;
        print_r($parseUrl);
        print"----".PHP_EOL;
        print"----".PHP_EOL;*/

        return  $parseUrl;
    }   
   
    static public function getExtension($file_name) {
 
        $extparse = explode('?',$file_name); 
        return substr(strrchr($extparse[0],'.'),1);
    }


}