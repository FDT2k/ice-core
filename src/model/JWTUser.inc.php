<?php
namespace ICE\core\model;

use \ICE\Env as Env;

use \Firebase\JWT\JWT;

class JWTUser extends \ICE\core\model\User{
/*	protected $account_key= "account_id";

	function check_login($user,$password){
		$member = $this->prepareQuery("select * from account where username=:email and password=:password and status='active'",
				array('email'=>$user,'password'=>$password))->fetchOne();

		$this->setDefaultDatas($member);
		return !empty($member) && sizeof($member )>0;
	}

	function get_account_id(){
			return $this->get($this->account_key);
	}


	function login ($user,$password){

		if($this->check_login($user,$password)){
			$_SESSION['id']=$this->get_account_id();
			$this->load_user($_SESSION['id']);
			$key = Env::getConfig("jwt")->get('key');
			$token = array(
			    "iss" => $_SERVER['HTTP_HOST'],
			    "iat" => time(),
			    "nbf" => time(),
			    "exp" => time()+(86400*365),
			    "data" =>  array(
      		$this->account_key   => $this->get_account_id(), // userid from the users table
            		)


			);
			$jwt = JWT::encode($token, $key);

			$this->setToken($jwt);
			return true;
		}
		return false;
	}

	function loginJWT($token){
		$result = false;

		try{
	//		var_dump($token);
			$key = Env::getConfig("jwt")->get('key');
			$decoded = JWT::decode($token, $key, array('HS256'));

			//var_dump($decoded);

			if($decoded){
				//authenticated
				$result = true;
				$this->setToken($token);
				$key = $this->account_key;
				$this->load_user($decoded->data->$key);

			}
		}
		catch(\Firebase\JWT\ExpiredException $e){
			$this->setError("Expired token, please log in again",101);
		}
		catch( \Exception $e){
			$this->setError("Invalid token ".var_export($e,true),102);
		}finally{
			if (!$result) {
				$this->logout();
				$this->setDefaultDatas(array($this->account_key=>0,));
			}
		}
		return $result;
	}


	function init(){
		$default_user = array('id'=>0,'access_level'=>0);
		$this->setDatas($default_user);

		if(!empty($_SESSION['id'])){
			$this->load_user($_SESSION['id']);
		}
	}

	function load_user($id){

		if($user = $this->prepareQuery("Select * from account where account_id = :id",array('id'=>$id))->fetchOne()){


			$this->setDefaultDatas($user);
		}
	}

	function is_logged(){
		$level = $this->get('access_level');
		$id = $this->get('account_id');

		return !empty($id)&& !empty($level);
	}
*/

}
