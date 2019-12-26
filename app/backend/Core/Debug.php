<?php 
namespace BECompare\Core;
class Debug{
 
	private function __construct(){ 
	}  
   
	static public function debug($infoView){ 
		$bt = debug_backtrace(); 
		$caller = array_shift($bt);
		echo '<div style="padding:10px 10px;background-color:#ccc; margin-top:15px">';
		echo 'File: '.$caller['file']; 
		echo '<br>Line: '.$caller['line'];
		//echo '<br>Function: '.$caller['function'];
		//echo '<br>Class: '.$caller['class']; 
		echo '<pre>';
		print_r($infoView);
		echo '</pre>';
		echo '</div>';
	}
 		
}   