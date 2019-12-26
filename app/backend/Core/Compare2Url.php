<?php
namespace BECompare\Core; 
use BECompare\Core\Compare2ListUrl;
class Compare2Url{ 
  
    private $instanceCompare = null; 

    public function compareUrl($text1='',$text2='',$algoritmo='similar_text'){

        if($this->instanceCompare==null)
            $this->instanceCompare = new Compare2ListUrl();

        $this->instanceCompare->algoritmo=$algoritmo;  
        return $this->instanceCompare->compareText($text1, $text2);
    }

     
}