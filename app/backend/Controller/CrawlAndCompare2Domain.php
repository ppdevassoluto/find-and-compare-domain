<?php
namespace BECompare\Controller; 

use BECompare\Core\AnalyzeUrl;  
use BECompare\Core\CrawlWebsite;
use BECompare\Core\Compare2ListUrl;
use BECompare\Controller\Component\CompareDataMsg;

class CrawlAndCompare2Domain{ 
  
    
    /**
    * Questa Classe implementa :
    * - l' esecuzione del processo di analisi dei singoli url inseriti 
    * - l'esevucuzione del processo di comparazione degli url
    * - il ritorno delle informazioni in json  del ritorno delle informazioni
    */

    public function findAndCompare($domain1='', $domain2=''){
       
        /**
         * @params
         * @domain1 - @domain2 :   domini da confrontare 
         * 
         * return:
         * json array:  error, error_message, filename
         */

    
        #validazione domini 
        $validateInput = true;
        $validateMsg = '';
        if (empty($domain1) || empty($domain2)){
            $validateInput=false; 
            $validateMsg = 'Verificare che siano indicati entrambi i domini.';
            CompareDataMsg::getInstance()->responseError($validateMsg,'Input');
        }
    
        if(substr($domain1,-1)!='/')
                $domain1.='/';
        
        if(substr($domain2,-1)!='/')
                $domain2.='/';
         
       
        $validateInput =  $domain1 === $domain2 ? false : true;

        if ($validateInput==false){ 
            $validateInput=false; 
            $validateMsg = 'Hai indicato lo stesso dominio per dominio1 e dominio2';
            CompareDataMsg::getInstance()->responseError($validateMsg,'Input'); 
        }
  
        $validateInput =  AnalyzeUrl::validateDomain($domain1);
        if ($validateInput==false){ 
            $validateInput=false; 
            $validateMsg = 'Verificare che il dominio1 sia valido';
            CompareDataMsg::getInstance()->responseError($validateMsg,'Input'); 
        } 
 
        $validateInput =  AnalyzeUrl::validateDomain($domain2); 
        if ($validateInput == false){ 
            $validateInput=false; 
            $validateMsg = 'Verificare che il dominio2 sia valido';
            CompareDataMsg::getInstance()->responseError($validateMsg,'Input'); 
        } 
  
        $errorParse = false; 
        $crawlWebsite = new CrawlWebsite();
        $Compare2ListUrl = new Compare2ListUrl();
        $Compare2ListUrl->setList1($crawlWebsite->crawl($domain1), $domain1); 

        if($Compare2ListUrl->countList1()<=1){
            
            $errorParse = true;
            $validateMsg = 'Non sono stati trovati link nel dominio1';
            CompareDataMsg::getInstance()->responseError($validateMsg,'Parsing');

        }   
        $Compare2ListUrl->setList2($crawlWebsite->crawl($domain2), $domain2);   
        if($Compare2ListUrl->countList2()<=1){
        
            $errorParse = true;
            $validateMsg = 'Non sono stati trovati link nel dominio2';
            CompareDataMsg::getInstance()->responseError($validateMsg,'Parsing');

        }  
     
        CompareDataMsg::getInstance()->responseFile($Compare2ListUrl->analyzeAndCreateFile());

    }  
  
}