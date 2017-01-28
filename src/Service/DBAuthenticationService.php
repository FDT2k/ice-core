<?php
namespace FDT2k\Noctis\Core\Service;
use \FDT2k\Noctis\Core\Env as Env;

class DBAuthenticationService extends AuthenticationService {

	public function	__construct(){
		if($model = Env::getConfig("auth")->get('user_model')){
			$this->model = new $model();
			if(!$this->model instanceOf  \FDT2k\Noctis\Core\Iface\UserInterface){
				throw new \ICE\core\Exception("error, assigned model ".$model." should implements  \FDT2k\Noctis\Core\Iface\UserInterface",0,"");
			}
		}
	}

	public function authenticate($login,$password,$opts=array()){
	//	$password = sha1($password);
	//	if($user = $this->model->prepareQuery("select * from users where email=:email and password=:password and email_confirmed=1",array('email'=>$login,'password'=>$password))->fetchOne()){


	//	if($user = $this->model->select(array('email'=>$login,'password'=>$password))){
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
