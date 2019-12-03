<?php 
//test instilla



function _extractInfoLink_OLD($urlPage='',$domain='',$depthLink=-1){
 

    $parseUrl = parse_url($urlPage);  

    if (!isset($parseUrl['host']))
        $parseUrl['host'] = $domain; 
     

    if (isset($parseUrl['path']))
        $parseUrl['depthLevel'] = substr_count($parseUrl['path'],'/')-1; 
    else{
        $parseUrl['depthLevel'] = 0;
        $parseUrl['path'] = '/';    
    }



   /* if ($depthLink>0 && !empty($domain) && $parseUrl['host']==$domain 
        && $parseUrl['depthLevel']==$depthLink ){

            //$parseUrl['html'] = _isHtmlPage($urlPage); 

            $parseUrl['dephLink'] = 1;

    }else{
 
        $parseUrl['dephLink'] = 0;
    }  */
 
    return  $parseUrl;
 }
 function _extractInfoLink($urlPage=''){
 

    $parseUrl = parse_url($urlPage);  

   // if (!isset($parseUrl['host']))
    //    $parseUrl['host'] = $domain; 

    if (isset($parseUrl['path']))
        $parseUrl['depthLevel'] = substr_count($parseUrl['path'],'/')-1; 
    else{
        $parseUrl['depthLevel'] = 0;
        $parseUrl['path'] = '/';    
    }
 
 
    return  $parseUrl;
 }

 function _isHtmlPage($urlPage=''){

    global $acceptedHtml, $deniedHtml, $useCurl;
 
    $extension = substr(strrchr($urlPage,'.'), 1);
    if (in_array($extension, $deniedHtml))
        return false;
    elseif (in_array($extension,$acceptedHtml))
        return true;
   
   
    if ($useCurl==1){

        $ch = curl_init($urlPage);
        curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_CONTENT_TYPE );
        curl_close($ch);
        
    }else{

        $infoContent = get_headers($urlPage,1);
        if (!isset($infoContent['Content-Type'])){
            print_r($infoContent);
            die();
        }
         
        $httpcode =  $infoContent['Content-Type'];
        unset($infoContent); 
    }    

    //echo 'HTTP code: ' . $httpcode . ' '.$urlPage.' '.PHP_EOL;
 
    if (substr($httpcode, 0,9)=='text/html')
        return 1;
    else 
        return 0;    
  
     
 }
 
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
            $href = $domain.$href; 
        }elseif($href[0] == './'){
            $href = $domain.'/'.$href; 
        }  
 
         
        $infoLink = _extractInfoLink($href); 
 
        if (!isset($infoLink['scheme']) || ($infoLink['scheme']!='https' && $infoLink['scheme']!='http'))
            continue;
      
        if(!isset($infoLink['host'])){
            print $href.PHP_EOL;
            print_r( $infoLink);die();
        }
        

        if ($infoLink['host']==$domainInfo['host']) 
            $hrefList[] = $domain.$infoLink['path'];   

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
 


/**
 * 
 * Inizio procedura 
 */
// http://127.0.0.1/siti-applicazioni/altre-applicazioni/ZTL/cron/app/servizi/contrassegni/allineamento/v2.0/compare-domains.php

/*
function closest_word($input, $words, &$percent = null) {
    $shortest = -1;
    foreach ($words as $word) {
      $lev = levenshtein($input, $word);

      if ($lev == 0) {
        $closest = $word;
        $shortest = 0;
        break;
      }

      if ($lev <= $shortest || $shortest < 0) {
        $closest  = $word;
        $shortest = $lev;
      }
    }

    $percent = 1 - levenshtein($input, $closest) / max(strlen($input), strlen($closest));

    return $closest;
  }
function result_similar_link($t1,$t2){
    echo  PHP_EOL;
    $sim = similar_text($t1, $t2, $perc);
    echo "- lnk sim: $sim (".$perc."%)".PHP_EOL;
 
}
function levperc($t1,$t2){
   
    $sim = levenshtein($t1, $t2) ;
    $percent = 1 - levenshtein($t1, $t2) / max(strlen($t1), strlen($t2));
   
    echo "- lev sim: $sim (".round($percent * 100, 2)."%) ".PHP_EOL;
}
*/


function myLevCompareLink($link1,$link2){

    $word1= explode( '-', $link1);
    $word2= explode( '-', $link2);
    
    $conta_ok=0;
    foreach($word1 as  $w1){
        $key_compare[$w1]=0;
     
        foreach($word2 as $w2){ 
            
            $res_lev = levenshtein($w1,$w2); //levperc($w1,$w2); //myLevCompareLink($w1,$w2); 
            
            if (!$res_lev)
                $conta_ok += 1;
    
            $key_compare_w[$w1][$w2]['lev'] = $res_lev;
            
        } 
    }  
    $percent = 1 - $conta_ok  / max(count($w1), strlen($w2));
    return $percent;
} 

  
 
$dominio = "http://www.argentariobasket.it/";
$domain2 = "https://www.tuttosport.com";
$domain1 = "https://www.gazzetta.it";


$acceptedHtml = array('html','shtml');
$deniedHtml = array('pdf','doc', 'jpg', 'jpeg','png', 'gif','bmp');
$useCurl=1;
echo PHP_EOL.'--'.PHP_EOL;
echo "Inizio : ".date("H:i:s");

echo PHP_EOL.'--'.PHP_EOL;
$result1 = parseDomain($domain1);
print_r($result1);


echo PHP_EOL.'--'.PHP_EOL;
$result2 = parseDomain($domain2);
print_r($result2 ); 
echo PHP_EOL.'--'.PHP_EOL;
echo "FINE : ".date("H:i:s"); 
die();
$similLink = array();
foreach($result1 as $link1){

    $valueCompare = -1;
    foreach($result2 as $link2){

        $value = myLevCompareLink($link1,$link2);

        if ($value > $valueCompare){
           
            $valueCompare = $value;

            if($value>=10)
                $similLink[$link1][$value] = $link2;

        }     
 
    }
  
}
print_r($similLink); 
die();

