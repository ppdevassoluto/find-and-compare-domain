<?php
namespace BECompare\Core; 

class Compare2Url{ 
     
    static private $instance = null; 
 

    private function _construct(){
    }
    static public function getInstance(){

        if(self::$instance==null)
            self::$instance = new Compare2Url();

        return self::$instance;       
    }   

    public function compareUrl($text1='',$text2='', $algoritmo='similar_text'){

        /**
         * summary: 
         * - esegue la comparazione di due stringhe secondo un algoritmo definito
         * @params:
         * @text1: stringa 1
         * @text2: stringa 1
         * return:
         * esito numerico della comparazione: valore da 0 a 100
         */  
        if ($text1===$text2)
            $compareValue = 100;
        else{ 
            #selezione funzione collegata all'algoritmo scelto  
            switch ( $algoritmo ) {
                case 'levenshtein':
                    $compareValue = levenshtein(strtolower($text1), strtolower($text2)); 
                break;
                case 'similar_text':
                    $compareValue = $this->cmp_similar_text($text1,$text2);     
                case 'dev_similar_text':
                        $compareValue = $this->dev_similar_text($text1,$text2);                                        
                default:
                    $compareValue = $this->cmp_similar_text($text1,$text2); 
            } 
        }
        return  number_format ( round($compareValue,2) , 2); 

    }


    private function _formatTextToCompare(&$stringa=''){ 

        /**
         * summary: formattazione url per la comparazione
         * 
         */

        $stringa = strtolower($stringa); 
        $stringa = str_replace('//','-',$stringa);
        $stringa =  str_replace('/','-',$stringa); 
        $stringa =  str_replace('+','-',$stringa); 
  

    }

    private function cmp_similar_text($text1,$text2){
  
        if ($text1===$text2)
            return 100; 
      
        similar_text($text1,$text2,$percent); 
 
        return $percent;
    } 
    private function dev_similar_text($text1,$text2){
  
        if ($text1===$text2)
            return 100;
 
        $this->_formatTextToCompare($text1);
        $this->_formatTextToCompare($text2); 
        similar_text($text1,$text2,$percent); 
 
        return $percent;
    } 


    private function dev2_similar_text($text1,$text2){ 
        
        if ($text1===$text2)
            return 100;
 
        $text1 = $this->_formatTextToCompare($text1);
        $text2 = $this->_formatTextToCompare($text2);

        $text1_parse =  preg_split('/-/', $text1, null, PREG_SPLIT_NO_EMPTY);
        $text2_parse =  preg_split('/-/', $text2, null, PREG_SPLIT_NO_EMPTY);
  
        //print_r($text1_parse);
        if (!count($text1_parse) || !count($text2_parse))
            return 0;

        $conta_res=0;
        $conta_all =0;
        foreach($text1_parse as  $w1){
            $key_compare[$w1]=0;
        
            foreach($text2_parse as $w2){  
                $res = similar_text($w1,$w2,$perc); 
 
                if ($perc==100)
                    $conta_res += 1;

                $conta_all+=1;
            } 
        }  
 
        $percent =  ($conta_res /  max(count($text1_parse), count($text2_parse)))*100  ; 


        return $percent;
    }        

     
}