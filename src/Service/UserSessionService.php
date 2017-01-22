<?php
namespace FDT2k\ICE\CORE\Service;
use \ICE\Env as Env;
use \Firebase\JWT\JWT;

class UserSessionService extends \ICE\core\IObject {

	function __construct(){
		$this->recover_session();

	}
	/**
	Recover a jwt user session from a cookie
	**/
	function recover_session(){
		//$r = Env::getRequest();
		$key = Env::getConfig("jwt")->get('key');
		$expiration = Env::getConfig("jwt")->get('token_expiration');
		$cookie_key=   Env::getConfig("jwt")->get('cookie_key');
		//retrieve user token // priority order -> cookie-> headers
		$result = false;
		$token = (isset($_COOKIE[$cookie_key]))? $_COOKIE[$cookie_key] : Env::getRequest()->getToken();

	//	var_dump($_COOKIE);
		$decoded = array();
		try{


			//var_dump($token);

			$decoded = array();
			if(!empty($token)){
				$decoded = JWT::decode($token, $key, array('HS256'));
				//var_dump($decoded);
				if($decoded){
					//authenticated
					$result = true;
					$this->setToken($token);
				//	var_dump($token);
					if(Env::getConfig("jwt")->get('auto_renew')){
						$this->create_session((array)$decoded->data);
					}

					//$key = $this->getEntity()->onePKName();
				//	$this->load_user($decoded->data->$key);

				}
			}
		}
		catch(\Firebase\JWT\ExpiredException $e){

			$this->setError("Expired token, please log in again",101);
			return false;
		}
		catch( \Exception $e){

			$this->setError("Invalid token ".var_export($e,true),102);
			return false;
		}finally{
			if ($result) {
			//	$this->logout();
				$this->setDefaultDatas($decoded);
				return true;
			}
		}
		return false;
	}


	/**
	Create a jwt user session
	**/
	function create_session($data){

		$key = Env::getConfig("jwt")->get('key');
		$expiration = Env::getConfig("jwt")->get('token_expiration');
		$cookie_key=   Env::getConfig("jwt")->get('cookie_key');
		$token = array(
				"iss" => $_SERVER['HTTP_HOST'],
				"iat" => time(),
				"nbf" => time(),
				"exp" => time()+($expiration),
				"data" =>  $data
		);

		$jwt = JWT::encode($token, $key);

		$this->setToken($jwt);
		$_SESSION[$cookie_key]=$jwt;
		setcookie($cookie_key,$jwt,time()+$expiration,'/');
		//var_dump($cookie_key);
		Env::getRequest()->setToken($jwt);
		$this->setDefaultDatas($data);
	}

	/**
	Destroy a jwt user session
	**/
	function destroy(){
		$cookie_key=   Env::getConfig("jwt")->get('cookie_key');
		setcookie($cookie_key,'',time()-10000,'/');
		unset($_SESSION[$cookie_key]);
		session_destroy();
	}
}
