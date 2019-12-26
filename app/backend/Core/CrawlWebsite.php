<?php
namespace BECompare\Core;
use  \DOMDocument;
use  BECompare\Core\AnalyzeUrl;
/**
* Summary: 
* - esegue il parsing di una pagina web ed estrae gli url dai link 
* - reitera il processo di analisi per le pagine di un dominio per la profondità indicata 
*/  

class CrawlWebsite {  
    
    private $domain = '';   //dominio da analizzare
    private $hrefList = array(); //lista url analizzati
    private $domainInfo = array(); //informazioni sul dominio
    private $skipContent;  //estenzione pagine da non analizzare
    public $depthLevel = 1; //profondita analisi dominio

    public function __construct(){ 

        /**
         * Summary: 
         *  impostazione della profondità di analisi desiderata
         * 
         * @parametri globali
         * @appConfig: impostato in Config/config.php 
         */        
        
        global $appConfig; 
        $this->depthLevel = $appConfig['depthLevel'];  
        $this->skipContent = $appConfig['skipContent'] ; 
    }

    public function crawl($domain=''){ 

          /**
         * summary: 
         * attiva il processo di analisi ricorsiva delle pagine di un dominio 
         * 
         * @params:
         * @domain: dominio da analizzare
         * 
         */        
        if(substr($domain,-1)!='/')
            $domain.='/';

        $this->domain = $domain;   
        $this->domainInfo = AnalyzeUrl::getInfoUrl($domain);   

        $this->hrefList=array();
        $this->_parsePageAndDepth($domain); 
      
        $this->hrefList = array_keys($this->hrefList); 
        return  $this->hrefList;
    } 	  

    private function _parsePageAndDepth($page=''){
          /**
         * summary: 
         * - esegue l' analisi del contenuto una pagina 
         * - l'url  della pagina viene aggiunto alla lista delle pagine analizzate: $this->hrefList
         * - la lista link presenti nella pagina sono iterati: 
         *      * se appartengono al dominio stesso e soddisfano il criterio della profondit allora viene richiesta l'analisi del contenuto
         *      * altrimenti viene aggiunto alla lista dei url estratti
         * - reitera il processo per gli url estratti della profondita desiderata
         * - al termine dell'analizi gli url sono presenti lista $this->hrefList
         * 
         * @params:
         * @page: url pagina da analizzare
         * 
         */  
         
        $infoPage= AnalyzeUrl::getInfoUrl($page); 
        $compareUrl = $infoPage['path']; 
        
        if (isset($this->hrefList[$compareUrl])) 
        {
            #url gia analizzato non viene richiesto la sua analisi
            return null;
        }
        # aggiungo alla lista url esaminati    
        $this->hrefList[$compareUrl]=1;  
        
        if (\in_array(AnalyzeUrl::getExtension($page), $this->skipContent)){
            #presente nell'elenco delle estenzioni da non analizzare
            return null;
        } 
        # estrazione contenuto dalla pagina
        $contentPage = @file_get_contents($page); 
        if ($contentPage === false) 
            return null;
          
        # estrazione links  
        $document= new DOMDocument;
        @$document->loadHTML($contentPage);
        $linksObj = $document->getElementsByTagName('a'); 
        foreach($linksObj as $link){ 

            $href =  $link->getAttribute('href'); 

            /**
             * non accetta:
             * - url vuoto
             * - url che inizia con # 
             * - url che inizia con javascritp
             * 
             * accetta:
             * - url nel formato: http(s):://....
             * - url che iniziano con: //, ./, / 
             */
            if(empty($href) || $href[0] == '#' || substr($href,0,10) == 'javascript') 
                continue;
                  
            $init_href =  substr($href,0,2);
            if ($init_href=='//')
                $href = $this->domainInfo['scheme'].$href; 
            elseif($init_href == './')
                $href = $this->domain.substr($href,2); 
            elseif($href[0] == '/')
                $href = substr($this->domain,0,-1).$href;  
            
            $infoUrl = AnalyzeUrl::getInfoUrl($href); 
            if (!isset($infoUrl['scheme']) || ($infoUrl['scheme']!='https' && $infoUrl['scheme']!='http'))
                continue; 

            if ($infoUrl['host']==$this->domainInfo['host']) { 
                
                #appartiene al dominio analizzato
                // Debug::debug(PHP_EOL.$page." -> ".$infoUrl['host']."==".$this->domainInfo['host']." - ".$href. ' -> '.$infoUrl['path']);
                               
                if ($infoUrl['depthLevel']<=$this->depthLevel){
                    #soddisfa il criterio della profondità
                    #viene richiesto l'analisi della pagina
                    $this->_parsePageAndDepth($href) ;
                }                   

                elseif (!isset($this->hrefList[$infoUrl['path']])) 
                    $this->hrefList[$infoUrl['path']]=1;   //$this->hrefList[$href]=1;  
            } 
                  
            unset($infoLink); 
        } 
              
    } 
 
}