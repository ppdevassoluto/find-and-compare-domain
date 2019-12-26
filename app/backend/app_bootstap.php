<?php
$appFolder=  realpath(__DIR__).DIRECTORY_SEPARATOR; 
$pathFolderProject = dirname(dirname($appFolder)).DIRECTORY_SEPARATOR;
require_once($appFolder.'Config/config.php');  
require_once($pathFolderProject.'vendor'.DIRECTORY_SEPARATOR.'autoload.php'); 

if (isset($_SERVER['REQUEST_SCHEME']))
    $protocol =  $_SERVER['REQUEST_SCHEME'].'://';
elseif(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
    $protocol= "https://"; 
else
    $protocol= "http://";  

$AppUrl =  $protocol.$_SERVER['HTTP_HOST']. dirname(dirname($_SERVER['PHP_SELF'] ))."/";

#impostazione path cartella contenente i files generati dall'analisi di comparazione degli url.
$csvFolderPath = $pathFolderProject.'logcompare'.DIRECTORY_SEPARATOR;