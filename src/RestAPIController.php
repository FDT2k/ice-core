<?php
namespace FDT2k\Noctis\Core;

class RestAPIModule extends Controller{

	function getModelUser(){
		return new \ICE\core\model\JWTUser();
	}

	function getModel(){
		return new \base\model\Database();
	}

	function publicAccess(){
		return Env::getRequest()->getMethod() == "OPTIONS" ||$this->action == "_authenticate" || $this->action == '_register';
	}

	function beforeActionRun($action){


			$token = Env::getRequest()->getToken();
//			var_dump($token);
			$logged= $this->user->loginJWT($token);
			$this->response->data["token"]=$token;
			if(!$logged && !$this->publicAccess()){
			//	$this->response->setResponseCode(401);
				$this->response->setError($this->user->getError(),$this->user->getErrorCode());
			}/*else if($this->user->willExpireSoon($token)){
				//send a new token;
				//$this->response->
			}*/

	}

	function _authenticateAction(){ // token generation
		#$this->response->data="test";
		if(Env::getRequest()->getMethod() == "POST"){
			$this->response->setError("Failed");

			#$this->response->data=$this->request->get('username');
			if($this->user->login($this->request->get('username'),sha1($this->request->get('password')))){

				$this->response->clearError();
				$this->response->data["token"] = $this->user->getToken();
			}

		}
		return $this->response;
	}

	function allowMethod($array){
		if (!in_array(Env::getRequest()->getMethod(), $array) && Env::getRequest()->getMethod() != "OPTIONS"){
			$this->response->setResponseCode(405);
			$this->response->setError("Method Not Allowed");
			return false;
		}
		return true;
	}

}
