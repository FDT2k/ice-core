<?php
namespace FDT2k\Noctis\Core\Response;

class Response extends  \IObject{

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
