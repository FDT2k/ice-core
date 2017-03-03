<?php
namespace FDT2k\Noctis\Core\Service;
use \FDT2k\Noctis\Core\Env as Env;

class NoctisModelAuthenticationService extends AuthenticationService {

	public function	__construct(){
		if($model = Env::getConfig("auth")->get('user_model')){
			$this->model = new $model();
			if(!$this->model instanceOf  \FDT2k\Noctis\Core\Iface\UserInterface){
				throw new \Exception("error, assigned model ".$model." should implements  \FDT2k\Noctis\Core\Iface\UserInterface",0);
			}
		}
	}

	public function authenticate($login,$password,$opts=array()){

		if($user = $this->model->check_login($login,$password)){

			$user_id = $user[$this->model->getEntity()->onePKName()];
			Env::getUserSessionService()->create_session(array('uid'=>$user_id,'login'=>$login));
			return true;
		}
		return false;
	}

	public function register($login,$password){

	}

	public function is_logged(){
		return Env::getUserSessionService()->getToken()!="";
	}

	public function fetch_groups($groupdn){

	}

}
