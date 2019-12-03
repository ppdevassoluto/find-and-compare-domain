<?php 
//test instilla

class crawlCompareWebsite {


    static function compareAnd(){





    }

    private function _getHrefFromPage($urlPage='', $domain='', $depthLink=-1){

        
        $contentPage = file_get_contents($urlPage); 
        $document= new DOMDocument;
        @$document->loadHTML($contentPage);
        $linksObj = $document->getElementsByTagName('a');
        //print_r($links);

        $hrefList=array();
        $hrefDepthList =array();
        $hrefExaminated = array();

        $domainInfo = _extractInfoLink($domain); 
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
    
            
            $infoLink = _extractInfoLink($href); 
    
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

    private function _extractInfoLink($urlPage=''){
 

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

echo urldecode($_GET['domain1']). ' - '.urldecode($_GET['domain2']);
 die();


  
 
function getHrefFromPage($urlPage='', $domain='', $depthLink=-1){

    
    $contentPage = file_get_contents($urlPage); 
    $document= new DOMDocument;
    @$document->loadHTML($contentPage);
    $linksObj = $document->getElementsByTagName('a');
    //print_r($links);

    $hrefList=array();
    $hrefDepthList =array();
    $hrefExaminated = array();

    $domainInfo = _extractInfoLink($domain); 
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
 
         
        $infoLink = _extractInfoLink($href); 
 
        if (!isset($infoLink['scheme']) || ($infoLink['scheme']!='https' && $infoLink['scheme']!='http'))
            continue;
      
        if(!isset($infoLink['host'])){
            print $href.PHP_EOL;
            print_r( $infoLink);die();
        } 

        if ($infoLink['host']==$domainInfo['host']) 
            $hrefList[] = $infoLink['path']; //$domain.$infoLink['path'];   

        if ($infoLink['host']==$domainInfo['host'] && $depthLink>0 && !empty($domain)  
            && $infoLink['depthLevel']==$depthLink )           
            $hrefDepthList[] = $href;   
            
        unset($infoLink); 
    }
  
    return array('links' =>$hrefList, 'dephtLinks'=>$hrefDepthList);
}
  
 
function parseDomain($domain=''){

    $list = getHrefFromPage($domain,$domain,1);  
 
    $listLinks = $list['links'];
    foreach($list['dephtLinks']  as $link)
    {
    
        $linkPage = getHrefFromPage($link,$domain,-1);  
        $listLinks = array_merge($listLinks,$linkPage['links']);
     
    }
   
    return  array_unique($listLinks);
} 
 

function formatLinkToCompare($string=''){ 

    $string = strtolower($string); 
    $string = str_replace('//','-',$string);
    $string =  str_replace('/','-',$string); 
    return $string;

}
function myLevCompareLink($link1,$link2){

     
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
    
            $key_compare_w[$w1][$w2]['lev'] = $res_lev;
            
        } 
    }  
    $percent =  $conta_ok   / max(count($word1), count($word2)); 
    return $percent;
} 

function levperc($t1,$t2){
   
    $sim = levenshtein($t1, $t2) ;
    $percent = 1 - levenshtein($t1, $t2) / max(strlen($t1), strlen($t2));
    return $percent;
}  
 
$dominio = "http://www.argentariobasket.it/";
$domain2 = "https://www.tuttosport.com/";
$domain1 = "https://www.gazzetta.it/";

 
/*$urlPageTest ="https://www.gazzetta.it/test/";
print_r(_extractInfoLink($urlPageTest ));
die();*/

echo PHP_EOL.'--'.PHP_EOL;
echo "Inizio : ".date("H:i:s");

echo PHP_EOL.'--'.PHP_EOL;
$result1 = parseDomain($domain1);
//print_r($result1);


echo PHP_EOL.'--'.PHP_EOL;
$result2 = parseDomain($domain2);
//print_r($result2 ); 
echo PHP_EOL.'--'.PHP_EOL;
//echo "FINE : ".date("H:i:s"); 
 


$similLink = array();
foreach($result1 as $key => $link1){

    $valueCompare = -1;
    foreach($result2 as $link2){

        //$value = levperc(formatLinkToCompare($link1),formatLinkToCompare($link2)); 
        $value = myLevCompareLink(formatLinkToCompare($link1),formatLinkToCompare($link2));

        if ($value > $valueCompare){
           
            $valueCompare = $value;

            if ($value>0.15)
                $similLink[$key]  = $link1.' - '.$link2. ' - '.$value;
             

        }     
 
    }
  
}
print_r($similLink); 
echo PHP_EOL.'--'.PHP_EOL;
echo "FINE : ".date("H:i:s"); 
die();

