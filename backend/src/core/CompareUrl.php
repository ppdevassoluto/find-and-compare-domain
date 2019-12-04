<?php
namespace Crawl;
class CompareUrl {

    protected $returnResult = 'csv' ; 
    protected $returnHeaderCsv = false ; 
    protected $rowHeaderCsv= '' ; 

    function returnResult($returnResult='csv'){ 
        $this->returnResult = $returnResult; 
    }
    function writeRowHeaderCsv($text=''){
        
        $this->rowHeaderCsv =$text; 
    }

    protected function compareUrl(&$list1=array(), &$list2=array() ){  

        $similLink = array();
 
        foreach($list1 as $key => $text1){
           
            $valueCompare = 0;
            $similLink[$key] = $text1.';/;0%;'.PHP_EOL;
            foreach($list2 as $text2){ 
               
                //$value = $this->_levperc(_formatLinkToCompare($link1),_formatLinkToCompare($link2)); 
                $res_value = $this->_myLevCompareLink($this->_formatLinkToCompare($text1),$this->_formatLinkToCompare($text2));
        
                if ($res_value > $valueCompare){ 
                    $valueCompare = $res_value; 
                    //if ($res_value>0.15)
                    $similLink[$key]  = $text1.';'.$text2. ';'.$res_value.'%;'.PHP_EOL; 
                }     
            }   
            $this->writeResult($similLink[$key]); 
        } 
 
        return true;
 
    }
    
    protected function writeResult($text=''){

        if ($this->returnResult =='csv' && $this->returnHeaderCsv==false){

            $this->prepareHeaderCsv();
            $this->returnHeaderCsv = true;

            if(!empty($this->rowHeaderCsv))
                echo  $this->rowHeaderCsv.PHP_EOL; 
        }
            
        echo $text.PHP_EOL;
    }
    private function prepareHeaderCsv(){

        $filename="ResultCompareWebSite_".date('YmdHis').".csv";
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=".$filename);
        header("Pragma: no-cache");
        header("Expires: 0");

    }

    private function _formatLinkToCompare($string=''){ 

        $string = strtolower($string); 
        $string = str_replace('//','-',$string);
        $string =  str_replace('/','-',$string); 
        return $string;

    }

    private function _myLevCompareLink($text1,$text2){

        
        if ($text1===$text2)
            return 100;
 
        $word1 =  preg_split('/-/', $text1, null, PREG_SPLIT_NO_EMPTY);
        $word2 =  preg_split('/-/', $text2, null, PREG_SPLIT_NO_EMPTY);
 
        if (count($word1)==0 && count($word2)>0)
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

    private function _levperc($t1,$t2){
    
        $sim = levenshtein($t1, $t2) ;
        $percent = 1 - levenshtein($t1, $t2) / max(strlen($t1), strlen($t2));
        return $percent;
    }   

}