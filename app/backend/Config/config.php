<?php 
# algoritmo di comparazione |similar_text|dev_similar_text
$appConfig['algoritmo'] = 'dev_similar_text'; 
# profondita della scansione 
$appConfig['depthLevel'] = 1; 

#estenzioni delle pagine da non analizzare
$appConfig['skipContent'] = array(
    'pdf','doc','xls', 'xlsx', 'gif','png','jpg','jpeg','bmp', 'tiff', 'zip', 
    'tar.gz','rar','tgz','js','css','txt','exe','mov','mp3','wav','avi','mid','midi',
    'mpeg','mpg'
);
?>