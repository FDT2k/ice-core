<?php

namespace FDT2k\Noctis\Core\Response;
use \FDT2k\Noctis\Core\Env as Env;
class JSONResponse extends Response{

	var $mime = 'application/json';
	var $result = false;
	//var $data = new Object();;
	function __construct($buffer=''){
		$this->data= $buffer;
		//$this->result = true;
		//$this->build_response();
		$this->setApiMode(true);
	}


	function build_response(){
		if($this->isApiMode()){
			$response = array();
			$response['result']	=	!$this->hasError();
			$response['data']=$this->data;
			$response['error']=$this->error_message;
			$response['error_code']= $this->error_code;
			return $response;
		}else{
			$response = $this->data;
			return $response;
		}
	}

	function output_headers(){
		header('Content-type:'.$this->mime);
		header('HTTP/1.0 '.$this->getResponseCode());
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: origin, content-type, accept, Authorization");
		header("Access-Control-Allow-Methods: PUT,GET,POST,DELETE, OPTIONS");
	}


	function output(){
	#var_dump($this->mime);

		$response = $this->build_response();
		//var_dump($response);
		$output = json_encode($response);

		// disable the default profiler output in json output
		if(Env::getProfiler()->isEnabled()){
			Env::getProfiler()->setEnabled(false);
		}

		if(!$output){
			throw new \FDT2k\ICE\CORE\Exception("No output ",0);
		}else{
			$this->output_headers();

			echo $output;
		}
	}
}
