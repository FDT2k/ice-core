<?php
namespace ICE\core\response;

class Response extends  \ICE\core\IObject{

	var $mime = 'text/html';
	#public $response_code = 200;
	function __construct($buffer=''){
		$this->setResponseCode(200);
		$this->buffer= $buffer;
	}
	
	
	function output_headers(){
		header('HTTP/1.0 '.$this->getResponseCode());
		header('Content-type :'.$this->mime);
	
	}
	
	function output(){
		$this->output_headers();
		echo $this->buffer;
	}

	

}