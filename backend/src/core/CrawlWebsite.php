<?php
namespace Crawl;
use  \DOMDocument;
class CrawlWebsite { 

    static function crawl($domain=''){ 

        if(substr($domain,-1)!='/')
            $domain.='/';

        return  self::_parseDomain($domain);
   
    } 	 
    static function validateDomain($domain=''){

        
        $domain = filter_var($domain, FILTER_SANITIZE_URL);  
        $domainInfo = self::_extractInfoLink($domain); 
        
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

   
    static private function _parseDomain($domain=''){

        $list = self::_getHrefFromPage($domain,$domain,1);  
     
        $listLinks = $list['links'];
        foreach($list['dephtLinks']  as $link)
        {
        
            $linkPage = self::_getHrefFromPage($link,$domain,-1);  
            $listLinks = array_merge($listLinks,$linkPage['links']);
         
        }
       
        return  array_unique($listLinks);
    }  

    static private function _getHrefFromPage($urlPage='', $domain='', $depthLink=-1){

        $hrefList=array();
        $hrefDepthList =array();
        $hrefExaminated = array();


        $contentPage = @file_get_contents($urlPage); 
        
        if ($contentPage === false) 
            return array('links' =>$hrefList, 'dephtLinks'=>$hrefDepthList);
       
              
        $document= new DOMDocument;
        @$document->loadHTML($contentPage);
        $linksObj = $document->getElementsByTagName('a');
        //print_r($links);

        $hrefList=array();
        $hrefDepthList =array();
        $hrefExaminated = array();

        $domainInfo = self::_extractInfoLink($domain); 
        foreach($linksObj as $link){

            $href =  $link->getAttribute('href'); 
            if(in_array($href, $hrefExaminated))
                continue;
            
            $hrefExaminated[] = $href; 

            if(empty($href) || $href[0] == '#' || substr($href,0,10) == 'javascript'){
                continue;
            }elseif($href[0] == '/'){
                $href = substr($domain,0,-1).$href; 
            }elseif($href[0] == './'){
                $href = $domain.$href; 
            }   
            
            $infoLink = self::_extractInfoLink($href); 

            if (!isset($infoLink['scheme']) || ($infoLink['scheme']!='https' && $infoLink['scheme']!='http'))
                continue;

            if ($infoLink['host']==$domainInfo['host']) 
                $hrefList[] = $infoLink['path'];  

            if ($infoLink['host']==$domainInfo['host'] && $depthLink>0 && !empty($domain)  
                && $infoLink['depthLevel']==$depthLink )           
                $hrefDepthList[] = $href;   
                
            unset($infoLink); 
        }

        return array('links' =>$hrefList, 'dephtLinks'=>$hrefDepthList);
    }

    static private function _extractInfoLink($urlPage=''){


        $parseUrl = parse_url($urlPage);  
    
        if (isset($parseUrl['path'])){
 
            // $parseUrl['depthLevel'] = substr_count($parseUrl['path'],'/') -1; 
            $parseUrl['depthLevel'] = substr_count($parseUrl['path'],'/'); 
        
        }else{
            $parseUrl['depthLevel'] = 0;
            $parseUrl['path'] = '/';    
        } 
        return  $parseUrl;
    }   
    

}