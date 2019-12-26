<?php 
namespace BECompare\Controller\Component;
use \stdClass;  

/**
 * summary;
 * messaggio di ritorno analisi e comparazioni link di due domini
 */
class CompareDataMsg{
  
	private $error = NULL;
	private $error_type = NULL;
	private $error_message = NULL;
	private $filename = NULL;
	private static $instance = NULL;
	
	private function _construct(){

	}
	static public function getInstance(){
		if (self::$instance==NULL)
			self::$instance = new CompareDataMsg();
		return  self::$instance;
	}
	private function setErrorMsg($msg = NULL,$type = NULL){
		$this->error = 1; 
		$this->error_type = $type; 
		$this->error_message = $msg; 
	}
	private function setFilename($filename = NULL){ 
		$this->filename = $filename; 
	}
	 
	private function response(){  
		
		$pack = new stdClass();
		$pack->error = $this->error;
		$pack->error_message = $this->error_message;
		$pack->error_type = $this->error_type;
		$pack->filename = $this->filename;  

		return $pack;
	}		

	public function responseError($msg = null , $error_type = null){
		$this->setErrorMsg($msg, $error_type);
		echo json_encode($this->response());
		die();
	}
	public function responseFile($filename){
		$this->setFilename($filename);
		echo json_encode($this->response());
		die();
	}
}   
?>