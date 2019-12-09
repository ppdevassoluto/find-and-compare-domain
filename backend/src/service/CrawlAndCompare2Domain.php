<?php
namespace src\service; 

use src\core\UtilityUrl;
use src\core\CrawlWebsite;
use src\core\Compare2ListUrl;

class CrawlAndCompare2Domain extends Compare2ListUrl{ 
  
    
    /**
    * Questa Classe implementa :
    * - l' esecuzione del processo di analisi dei singoli url inseriti 
    * - l'esevucuzione del processo di comparazione degli url
    * - il ritorno delle informazioni in json  del ritorno delle informazioni
    */

    public function findAndCompare($domain1='', $domain2=''){
       
        /**
         * @domain1 - @domain2 : parametri domini da confrontare 
         * 
         */


        #validazione 
        $validateInput = true;
        $validateMsg = '';
        if (empty($domain1) || empty($domain2)){
            $validateInput=false; 
            $validateMsg = 'Verificare che siano indicati entrambi i domini.';
            $this->responseError($validateMsg,'Input');
        }
    
        if(substr($domain1,-1)!='/')
                $domain1.='/';
        
        if(substr($domain2,-1)!='/')
                $domain2.='/';
         
       
        $validateInput =  $domain1 === $domain2 ? false : true;

        if ($validateInput==false){

            $validateInput=false; 
            $validateMsg = 'Hai indicato lo stesso dominio per dominio1 e dominio2';
            $this->responseError($validateMsg,'Input');

        }
  
        $validateInput =  UtilityUrl::validateDomain($domain1);
        if ($validateInput==false){
            
            $validateInput=false; 
            $validateMsg = 'Verificare che il dominio1 sia valido';
            $this->responseError($validateMsg,'Input');

        } 
 
        $validateInput =  UtilityUrl::validateDomain($domain2); 
        if ($validateInput == false){
            
            $validateInput=false; 
            $validateMsg = 'Verificare che il dominio2 sia valido';
            $this->responseError($validateMsg,'Input');

        } 
        $errorParse = false; 
        $crawlWebsite = new CrawlWebsite();
        $this->setList1($crawlWebsite->crawl($domain1), $domain1); 

        if($this->countList1()<=1){
            
            $errorParse = true;
            $validateMsg = 'Non sono stati trovati link nel dominio1';
            $this->responseError($validateMsg,'Parsing');

        }   
        $this->setList2($crawlWebsite->crawl($domain2), $domain2);   
        if($this->countList2()<=1){
        
            $errorParse = true;
            $validateMsg = 'Non sono stati trovati link nel dominio2';
            $this->responseError($validateMsg,'Parsing');

        }  
     
        $this->responseFile($this->analyzeAndCreateFile());

    }  
    private function responseError($msg='',$errore_type=''){

        $result['error'] = 1; 
        $result['error_message'] = $msg; 
        $result['erro_typer'] = $errore_type;
        
        echo json_encode($result);
        die();
    } 
    private function responseFile($filename){

        $result['error'] = 0; 
        $result['filename'] = $filename;

        echo json_encode($result); 
        die();

    } 
}