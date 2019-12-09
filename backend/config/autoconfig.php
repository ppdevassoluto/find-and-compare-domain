<?php 
 
if (isset($_SERVER['REQUEST_SCHEME']))
    $protocol =  $_SERVER['REQUEST_SCHEME'].'://';
elseif(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
    $protocol= "https://"; 
else
    $protocol= "http://";  
 

$rootFolder =  $protocol.$_SERVER['HTTP_HOST']. dirname(dirname($_SERVER['PHP_SELF'] ))."/";
$frontendFolder = $rootFolder.'frontend/'; 
$backendFolder = $rootFolder.'backend/';
$appConfig['folderFileCsv'] ='logcompare/'; 
$appAutoConfig =  array(
        'rootFolderUrl' => $rootFolder,
        'frontendFolderUrl' => $frontendFolder,
        'backendFolderUrl' => $backendFolder,
        'resultFolderUrl' => $backendFolder.'logcompare/',
        'csvFolderPath' =>  'logcompare/'  
);
 
?>