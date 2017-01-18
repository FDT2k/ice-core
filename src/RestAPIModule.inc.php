<?php
namespace FDT2K\ICE\CORE;

use \ICE\Env as Env;
use \ICE\lib\helpers\Hash as Hash;
use \ICE\lib\helpers\MP3File as MP3File;
use \ICE\lib\helpers\HTTP as HTTP;
use \ICE\lib\scaffolding as scaff;
use \ICE\lib\helpers\Formatter as Formatter;
use \ICE\lib\helpers\CachedWebserviceFetcher as CachedWebserviceFetcher;

class RestAPIModule extends \ICE\core\Module{

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
