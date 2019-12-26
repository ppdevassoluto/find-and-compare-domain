<?php
namespace BECompare\Core;
use BECompare\Core\Compare2Url;
/**
* Summary: 
*  esegue la comparazione delle stringhe presenti in due array secondo un determinato algoritmo
*  l'analisi viene memorizzata in un file formato csv
*/   

class Compare2ListUrl {

   
    public $filenameCsv = '';

    private $listUrl = array();  //contenitore due liste $listUrl[1] e $listUrl[2] contenente le stringhe da comparare
    private $headerCsv = array(); //intestazione del csv
    private $csvFolderPath=''; // cartella files csv  
    public $algoritmo = 'similar_text'; // algoritmo di comparazione  
     
 

    public function __construct(){

        /**
         * Summary: 
         *  impostazione algoritmo di comparazione e cartella contenitore dei files generati
         * 
         * @parametri globali
         * @appConfig: impostato in Config/config.php
         * @algoritmo: impostato in app_bootstrap.php
         */
        
        global $appConfig, $csvFolderPath;  

        $this->csvFolderPath = $csvFolderPath;  
        $this->algoritmo = $appConfig['algoritmo'];   
    }
 
    public function analyzeAndCreateFile(){  
 
        /**
         * Summary:
         *  compara il contenuto di due liste (di url) e produce in output il file csv
         *  
         * @appConfig: impostato in Config/config.php
         * @algoritmo: impostato in app_bootstrap.php
         * 
         * return:
         * @filename: nome file csv contenente i risultati dell' analisi
         */

        #definizione nome csv - se il nome non è definito dalla procedura allora si imposta quella di default
        if (!empty($this->filenameCsv)) 
            $filename = $this->filenameCsv;
        else
            $filename = "EsitoConfronto_".date('YmdHis').".csv";    
            //$filename = "EsitoConfronto_".date('YmdHis')."_".$this->algoritmo.".csv";
        
        #apertura file csv
        $fp = fopen($this->csvFolderPath.$filename, 'w'); 
        if (!empty($this->headerCsv)){
            #impostazione header se non definito in procedura 
            $this->headerCsv[3]='%';
            fputcsv($fp, $this->headerCsv,';');
        }           
 
        #confronto liste se entrambe definite
        if (is_array($this->listUrl[1]) && count($this->listUrl[1])>0
            && is_array($this->listUrl[2]) && count($this->listUrl[2])>0 
            ){
             
            /**
             * todo: si vuole ordinato per contento listaUrl[1] ?
             *  */    
            sort($this->listUrl[1]);  
             
            $compare2Url = Compare2Url::getInstance();  

            foreach($this->listUrl[1] as $key => $text1){
                
                /**
                 * - iterazione di comparazione del contenuto della stringa nella lista1 con tutte le stringhe della lista2
                 * - per ogni stringa della lista1 si prende la stringa di lista1 che ha generato il risultato piu
                 *   alto nella comparazione  
                 * - se una comparazione genera 100 come risultato  allora si interrompe la comparazione 
                 *   con le altre stringhe della lista2 e si passa alla successiva stringa della lista1
                 * 
                 */

                $maxValue = 0;
                $csvLine =   array($text1, '', '0');
                foreach($this->listUrl[2] as $text2){ 
                 
                    #comparazione di due stringhe - ritorno valore della comparazione 
                    $compareValue = $compare2Url->compareUrl($text1,$text2, $this->algoritmo);
 
                    if ($compareValue >= $maxValue){ 
                        $maxValue = $compareValue;  
    
                        $csvLine =  array($text1, $text2, $maxValue); 
                        if ($maxValue==100)
                            break;
                    }     
                }   
                #terminata la comporazione di una stringa della lista1 con le stringhe di lista1
                #scrittura dell'esito massimo nella riga csv  
                fputcsv($fp,  $csvLine,';' );
                unset( $csvLine );
            }  
        }     
        #chiusura file csv
        fclose($fp); 

        return $filename;
 
    }
    public function setList1($list = array(), $headCsv=''){ 
        /**
         * summary: 
         * - impostazione della lista1
         * @params:
         * @list: lista di stringhe/url
         * @headCsv: nome colonna header csv  
         */

        $this->_setList(1, $list, $headCsv); 
    }
    public function setList2($list = array(), $headCsv=''){ 
        /**
         * summary: 
         * - impostazione della lista2
         * @params:
         * @list: lista di stringhe/url
         * @headCsv: nome colonna header csv  
         */  

        $this->_setList(2, $list, $headCsv); 
    }
    private function _setList($numberList=1, $list = array(), $headCsv=''){ 

        $this->listUrl[$numberList] = $list;
        $this->headerCsv[$numberList] = $headCsv;
 
 
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
         
 

}