<?php
namespace ICE\core\service;
use \ICE\Env as Env;

class RadiusAuthenticationService extends AuthenticationService {

	public function	__construct(){

	}

	public function authenticate($login,$password,$opts=array()){

	}

	public function register($login,$password){

	}

	public function is_logged(){
		return Env::getUserSessionService()->getToken()!="";
	}

	public function fetch_groups($groupdn){
	
	}

}
