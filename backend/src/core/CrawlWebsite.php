<?php
namespace src\core;
use  \DOMDocument;
class CrawlWebsite { 

    /**
    * Implementa: 
    * - il processo di estrazione degli url da una pagina 
    * - la reiterazione del processo per la profondita indicata  
    */    
 
    private $skipContent = array(
        'pdf','doc','xls', 'xlsx', 'gif','png','jpg','jpeg','bmp', 'tiff', 'zip', 
        'tar.gz','rar','tgz','js','css','txt','exe','mov','mp3','wav','avi','mid','midi',
        'mpeg','mpg'
    );
 
    
    private $domain = '';
    private $hrefList = array();
    private $domainInfo = array();
    public $depthLevel = 1;

    public function __construct(){ 
        global $appConfig, $appAutoConfig; 
        $this->depthLevel =  $appConfig['depthLevel'];
 
    }

    public function crawl($domain=''){ 

        if(substr($domain,-1)!='/')
            $domain.='/';

        $this->domain = $domain;   
        $this->domainInfo = UtilityUrl::extractInfoUrl($domain);   
        

        $this->hrefList=array();
        $this->_parseDomainAndDepth($domain); 
      
        $this->hrefList = array_keys($this->hrefList); 
        return  $this->hrefList;
   
    } 	  

    private function _parseDomainAndDepth($urlContent=''){
  
         
        $infoUrlContent = UtilityUrl::extractInfoUrl($urlContent); 
        $compareUrl = $infoUrlContent['path'];

        if (isset($this->hrefList[$compareUrl])) 
            return false;

        # aggiungo alla lista link esaminati    
        $this->hrefList[$compareUrl]=1;  
        
        if (in_array(UtilityUrl::getExtension($urlContent), $this->skipContent))
            return false; 
           
        # estrazione contenuto dalla pagina
        $contentPage = @file_get_contents($urlContent); 
        if ($contentPage === false) 
            return false;
          
        # estrazione links dal contenuto      
        $document= new DOMDocument;
        @$document->loadHTML($contentPage);
        $linksObj = $document->getElementsByTagName('a');
        //print_r($links);
  
        foreach($linksObj as $link){ 

            $href =  $link->getAttribute('href'); 
            if(empty($href) || $href[0] == '#' || substr($href,0,10) == 'javascript') 
                continue;
                  
            $init_href =  substr($href,0,2);
            if ($init_href=='//')
                $href = $this->domainInfo['scheme'].$href; 
            elseif($init_href == './')
                $href = $this->domain.substr($href,2); 
            elseif($href[0] == '/')
                $href = substr($this->domain,0,-1).$href;  
            
            $infoUrl = UtilityUrl::extractInfoUrl($href); 
            if (!isset($infoUrl['scheme']) || ($infoUrl['scheme']!='https' && $infoUrl['scheme']!='http'))
                continue;
       
            if ($infoUrl['host']==$this->domainInfo['host']) { 
                
                // print PHP_EOL.$urlContent." -> ".$infoUrl['host']."==".$this->domainInfo['host']." - ".$href. ' -> '.$infoUrl['path'];

                if ($infoUrl['depthLevel']<=$this->depthLevel)
                    $this->_parseDomainAndDepth($href) ;
                elseif (!isset($this->hrefList[$infoUrl['path']])) 
                    $this->hrefList[$infoUrl['path']]=1;   //$this->hrefList[$href]=1;  
            } 
                  
            unset($infoLink); 
        } 
              
    } 
 
}