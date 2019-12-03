<?php
namespace Crawl;
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