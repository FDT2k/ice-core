<?php
namespace FDT2k\Noctis\Core;

class RestAPIModule extends Controller{



	function publicAccess(){
		return Env::getRequest()->getMethod() == "OPTIONS" ||$this->action == "_authenticate" || $this->action == '_register';
	}

	function beforeActionRun($action){

			$this->is_logged = Env::getAuthService()->is_logged();

			if(!$this->is_logged && !$this->publicAccess()){
				$this->response->setResponseCode(401);
				$this->response->setError("unauthorized access");
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

}
