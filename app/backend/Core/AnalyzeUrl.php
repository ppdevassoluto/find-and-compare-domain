<?php
namespace BECompare\Core;

use  \DOMDocument;
use BECompare\Core\Debug;
class AnalyzeUrl { 
 

    static public function validateDomain($domain=''){
        /**
         * summary: 
         * - esegue la validazione dell'url dominio 
         *   accetta il formato: http(s)://www.test.it(/)
         * @params:
         * @domain: url dominio 
         * return:
         * bool
         */  
  
        $domain = filter_var($domain, FILTER_SANITIZE_URL);  
        $domainInfo = self::getInfoUrl($domain); 
        
        if (    
            !isset($domainInfo['scheme']) 
            || ($domainInfo['scheme']!='https' && $domainInfo['scheme']!='http') 
            || !isset($domainInfo['host']) )
            return false;  

        if ($domainInfo['path']!=='/')
            return false;

        if (! \filter_var($domain, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) 
            return false; 
        else 
            return true;
     
    } 

    static public function getInfoUrl($url=''){
        /**
         * summary: 
         * - estrae le informazioni da un url
         * @params:
         * @url: url da analizzare 
         * return:
         * bool
         */   
        $parseUrl = parse_url($url);  
    
        if (isset($parseUrl['path'])){
           $parseUrl['depthLevel'] = substr_count($parseUrl['path'],'/'); 
        }else{
            $parseUrl['depthLevel'] = 0;
            $parseUrl['path'] = '/';    
        } 
 
        return  $parseUrl;
    }   
   
    static public function getExtension($file_name) {
 
        $extparse = explode('?',$file_name); 
        return substr(strrchr($extparse[0],'.'),1);
    }


}