<?php
namespace ICE\core\model;

use \ICE\Env as Env;

class User extends  \ICE\core\model\Database{
	//var $user ;


/*
	function check_login($user,$password){
		$member = $this->prepareQuery("
			Select a.ice_account_id,a.nom,a.email,a.password,sum(r.access_level) as access_level

			from ice_account a
			inner join ice_account_role ar on a.ice_account_id = ar.ice_account_id
			inner join ice_role r on ar.ice_role_id = r.ice_role_id
			where email=:email and password=:password
			group by a.ice_account_id,a.nom,a.email,a.password
			",
				array('email'=>$user,'password'=>$password))->fetchOne();

		$member['access_level']=intval($member['access_level']);
		//var_dump($member);
		$this->setDefaultDatas($member);
		return !empty($member) && sizeof($member )>0;
	}

	function login ($user,$password){
		if($this->check_login($user,$password)){
			$_SESSION['id']=$this->get('ice_account_id');
			$this->load_user($_SESSION['id']);
			// generate auth token if database session handling is enabled
			if ( Env::getConfig('session')->get('handler') == 'database'){
				$token = bin2hex(openssl_random_pseudo_bytes(16));
				//store it
				$this->prepareQuery(
						"update php_sessions set auth_token = :token, user_id=:user_id where sess_id=:id",
						array('token'=>$token,'id'=>$_COOKIE['PHPSESSID'],'user_id'=>$_SESSION['id'])
					)->executeUpdate();
			}
			return true;
		}
		return false;
	}

	function logout(){
		//var_dump('logout');
		$_SESSION['id']='';
		unset($_SESSION['id']);
		if ( Env::getConfig('session')->get('handler') == 'database'){
			$this->prepareQuery(
							"update php_sessions set auth_token = NULL, user_id=NULL where sess_id=:id",
							array('id'=>$_COOKIE['PHPSESSID'])
						)->executeUpdate();
			//var_dump($this->getLastQuery());
		}
		\session_unset();

	}

	function init(){
		$default_user = array('id'=>0,'access_level'=>0);
		$this->setDatas($default_user);

		if(!empty($_SESSION['id'])){
			$this->load_user($_SESSION['id']);
		}
	}

	function load_user($id){
	//	$this->user =
		//var_dump($id);
		if($user = $this->prepareQuery("Select a.ice_account_id,a.nom,a.email,a.password,sum(r.access_level) as access_level

			from ice_account a
			inner join ice_account_role ar on a.ice_account_id = ar.ice_account_id
			inner join ice_role r on ar.ice_role_id = r.ice_role_id
			where a.ice_account_id = :id group by a.ice_account_id,a.nom,a.email,a.password",array('id'=>$id))->fetchOne()){
		//	unset($user['password']);
		//	var_dump($this->getLastQuery());
		//	var_dump($user);
			//Env::shutdown();
			$user['access_level']=intval($user['access_level']);
		//var_dump($member);
			$this->setDefaultDatas($user);
		}
	}

	function is_logged(){
		//var_dump($this->get('id'));
		$level = $this->get('access_level');
		$id = $this->get('id');
	//	var_dump($id); var_dump($level);
	//	var_dump($_SESSION);
		return !empty($id)&& !empty($level);
	}

	function change_password($old_password,$password){
		if( $this->is_logged() ){
			$this->prepareQuery("update ice_account set password=:password where password=:old_password and email=:email",array(
				'password'=>sha1($password),
				'old_password'=>sha1($old_password),
				'email'=>$this->user['email']
				)
			)->executeUpdate();
		}
	}

	function reset_password_code($email,$code){
		$result = $this->prepareQuery("select * from ice_account where email=:email",array('email'=>$email))->fetchOne();
		if($result && count($result) >0){
			return $this->prepareQuery("update ice_account set password_reset_code=:code where email = :email",array('code'=>$code,'email'=>$email))->executeUpdate();
		}else{
			return false;
		}
	}

	function verify_password_code($email,$code,$new_password){
		$result = $this->prepareQuery("select * from ice_account where email=:email and password_reset_code=:code",array('code'=>$code,'email'=>$email))->fetchOne();
		//var_dump($email,$code);
		if($result){
			$this->prepareQuery("update ice_account set password=:password,password_reset_code= null where id=:id",array('password'=>sha1($new_password) ,'id'=>$result['id']))->executeUpdate();
			return true;
		}
		return false;
	}*/

	
}
