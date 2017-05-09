<?php
namespace FDT2k\Noctis\Core;

class RestController extends Controller{




	function publicAccess(){
		return Env::getRequest()->getMethod() == "OPTIONS" || $this->action == "authenticate" || $this->action == '_register';
	}

	function hasAccess(){
		return Env::getAuthService()->is_logged();
	}

	function beforeActionRun($action){


			if(!$this->hasAccess() && !$this->publicAccess()){
				$this->response->setResponseCode(401);
				$this->response->setError("unauthorized access");
				return $this->response;
			}

	}

	function initResponse(){
		$this->response = new Response\JSONResponse();
	}



	function assertMethodAllowed($array){
		if (!in_array(Env::getRequest()->getMethod(), $array) && Env::getRequest()->getMethod() != "OPTIONS"){
			$this->response->setResponseCode(405);
			$this->response->setError("Method Not Allowed");
			return false;
		}
		return true;
	}

	function authenticateAction(){
		if(!Env::getAuthService()->authenticate($name,$password)){
			$this->response->setError("user / password is not valid");
		}
		return $this->response;
	}


		function optionRequestAction(){

			return $this->response;
		}

	function assertParams($data,$keys){
		foreach($keys as $key=>$options){

			if(!$data[$key]){
				return false;
			}
		}
		return true;
	}
}
