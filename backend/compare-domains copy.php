<?php 
//test instilla

class CrawlWebsite {


    static function crawl($domain=''){ 
 
		return  self::_parseDomain($domain);
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

        
        $contentPage = file_get_contents($urlPage); 
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
class CompareUrl {

 
    static function compare($domain1, &$list1=array(), $domain2, &$list2=array(), $layout='csv'){ 
   

        if ($layout=='csv'){
             echo  $domain1.';'.$domain2.';percentage compare;'.PHP_EOL;            
        }     

        $similLink = array();
        foreach($list1 as $key => $text1){
        
            $valueCompare = 0;
            $similLink[$key] = $text1.';;0%;'.PHP_EOL;
            foreach($list2 as $text2){
        
                //$value = self::_levperc(_formatLinkToCompare($link1),_formatLinkToCompare($link2)); 
                $res_value = self::_myLevCompareLink(self::_formatLinkToCompare($text1),self::_formatLinkToCompare($text2));
        
                if ($res_value > $valueCompare){
                   
                    $valueCompare = $res_value;
                    if ($res_value>0.15)
                        $similLink[$key]  = $text1.';'.$text2. ';'.$res_value.'%;'.PHP_EOL;
        
                }     
            } 
            if ($layout=='csv'){
                echo $similLink[$key];
                unset($similLink[$key]); 
            }                
        } 
        if ($layout=='csv')
            return true;
        else     
            return $similLink;
    }
    
    static private function _formatLinkToCompare($string=''){ 

        $string = strtolower($string); 
        $string = str_replace('//','-',$string);
        $string =  str_replace('/','-',$string); 
        return $string;
    
    }

    static private function _myLevCompareLink($link1,$link2){

     
        $word1 = explode( '-', $link1);
        $word2 = explode( '-', $link2);
         
        $word1 =  preg_split('/-/', $link1, null, PREG_SPLIT_NO_EMPTY);
        $word2 =  preg_split('/-/', $link2, null, PREG_SPLIT_NO_EMPTY);
    
        if (count($word1)==0 && count($word2)==0)
            return 0;
    
        $conta_ok=0;
        foreach($word1 as  $w1){
            $key_compare[$w1]=0;
         
            foreach($word2 as $w2){  
                $res_lev = levenshtein($w1,$w2);  
                if (!$res_lev)
                    $conta_ok += 1;  
            } 
        }  
     
        $percent =  round(($conta_ok   / max(count($word1), count($word2)))*100,2); 
        return $percent;
    } 
    
    static private function _levperc($t1,$t2){
       
        $sim = levenshtein($t1, $t2) ;
        $percent = 1 - levenshtein($t1, $t2) / max(strlen($t1), strlen($t2));
        return $percent;
    }   
    
}
 
$domain1 = urldecode($_GET['domain1']);
$domain2 = urldecode($_GET['domain2']);
 


function findAndCompare ($domain1, $domain2){
 
   $list1=CrawlWebsite::crawl($domain1);
   $list2=CrawlWebsite::crawl($domain2);
   CompareUrl::compare($domain1, $list1, $domain2,$list2,'csv');
   
}  

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
$filename="ResultCompareWebSite_".date('YmdHis').".csv";
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0"); 
findAndCompare($domain1, $domain2);
?>