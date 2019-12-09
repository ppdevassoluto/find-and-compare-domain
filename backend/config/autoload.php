<?php 
 
spl_autoload_register(function($className) {
     
    $autoloadPath = realpath(dirname(__FILE__));  
    if (DIRECTORY_SEPARATOR!=='\\')
        $className = str_replace('\\',DIRECTORY_SEPARATOR,$className);

    $fileClass = dirname($autoloadPath).DIRECTORY_SEPARATOR.$className.'.php'; 

    if (!file_exists($fileClass)){
        print "Classe $className - file $fileClass  non presente";
        die(); 
    } 

    require_once($fileClass);
}); 
?>