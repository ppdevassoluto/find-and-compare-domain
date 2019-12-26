<?php
namespace BECompare\Core;
class Compare2ListUrl {
 
    /**
    * Implementa 
    * - la comparazione delle stringhe presenti in due liste ricevute  
    */   

    public $algoritmo = 'similar_text';  
    public $filenameCsv = '';

    private $listUrl = array(); 
    private $headerCsv = array();
    private $csvFolderPath='';
     
 

    public function __construct(){

        global $appConfig, $appAutoConfig;  
        $this->csvFolderPath = $appAutoConfig['csvFolderPath']; 
        $this->algoritmo = $appConfig['algoritmo']; 
    }
 
    public function analyzeAndCreateFile(){  
 
        #apertura file csv e intestazione
        if (!empty($this->filenameCsv)) 
            $filename = $this->filenameCsv;
        else
            $filename = "EsitoConfronto_".date('YmdHis').".csv";    
            //$filename = "EsitoConfronto_".date('YmdHis')."_".$this->algoritmo.".csv";
        
        $fp = fopen($this->csvFolderPath.$filename, 'w'); 
        if (!empty($this->headerCsv)){
            $this->headerCsv[3]='%';
            fputcsv($fp, $this->headerCsv,';');
        }
            
 
        #confronto testo e scrittura esito confronto 
        if (is_array($this->listUrl[1]) && count($this->listUrl[1])>0
            && is_array($this->listUrl[2]) && count($this->listUrl[2])>0 
            ){
            sort($this->listUrl[1]);  
   
            foreach($this->listUrl[1] as $key => $text1){
            
                $maxValue = 0;
                $csvLine =   array($text1, '', '0');
                foreach($this->listUrl[2] as $text2){ 
                 
                    #confronto
                    $compareValue = $this->compareText($text1, $text2);
 
                    if ($compareValue >= $maxValue){ 
                        $maxValue = $compareValue;  
    
                        $csvLine =  array($text1, $text2, $maxValue); 
                        if ($maxValue==100)
                            break;
                    }     
                }   
                #scrittura riga csv  
                fputcsv($fp,  $csvLine,';' );
                unset( $csvLine );
            }  
        }     
        #chiusura file csv
        fclose($fp); 

        return $filename;
 
    }
    public function setList1($list = array(), $headCsv=''){ 
        $this->_setList(1, $list, $headCsv); 
    }
    public function setList2($list = array(), $headCsv=''){ 
        $this->_setList(2, $list, $headCsv); 
    }
    public function countList1(){ 
        return count($this->listUrl[1]);  
    }
    public function countList2(){ 
        return count($this->listUrl[2]);  
    }          
    public function getList1(){ 
        return $this->listUrl[1];  
    }
    public function getList2(){ 
        return $this->listUrl[2];  
    }    
         

    public function compareText($text1='',$text2=''){

        if ($text1===$text2)
            $compareValue = 100;
        else{ 

            switch ($this->algoritmo) {
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


    private function _formatTextToCompare($string=''){ 

        $string = strtolower($string); 
        $string = str_replace('//','-',$string);
        $string =  str_replace('/','-',$string); 
        $string =  str_replace('+','-',$string); 
        return $string;

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
 
        $text1 = $this->_formatTextToCompare($text1);
        $text2 = $this->_formatTextToCompare($text2); 
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
 
    private function _setList($numberList=1, $list = array(), $headCsv=''){ 

        $this->listUrl[$numberList] = $list;
        $this->headerCsv[$numberList] = $headCsv;
 
 
    }

}