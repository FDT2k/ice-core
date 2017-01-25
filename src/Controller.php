<?php
namespace FDT2k\ICE\CORE;


use FDT2k\Helpers\Hash as Hash;
class Controller extends IObject{
	protected $renderer; // render
	protected $preventRender=false;


	//Lifecycle
	function __construct($action){ // default behavior for loading from front.php
		$this->action = $action;

		$this->post = Env::getRequest()->post;
		$this->get = Env::getRequest()->get;
		$this->request = Env::getRequest()->request;
		$this->UserSessionService = Env::getAuthService();
		$this->initModule();
		$this->initResponse();
	}


	/**
	Have to be declared, or you'll die and kittens explode
**/
	function beforeActionRun($action){


	}
	/**
		// same here, everyone will die on Mars if you don't declare it, oops.
**/

	function afterActionRun($action,$response){


	}

	/**
	Module initialization
	**/
	function initModule(){
		$this->model = $this->getModel();
		$this->user = $this->getModelUser();
	}

	//initialize default responses.

	function initResponse(){
		if($this->action[0]=='_'){
			$this->response = new Response\JSONResponse();
		}else{
			$this->response = new Response\XTPLResponse($this);
			$this->response->setVariable('MODULE',$this);
			$this->response->post = $this->post;
			$this->response->get = $this->get;
			$this->response->website = Env::getConfig('website');
		}
	}

	function getModelUser(){
//		return new \base\model\User();
	}

	function getModel(){
//		return new \base\model\Database();
	}


	//return the string if the module is equal
	function IS($module,$output){
		if($module == Env::getRoute()->module){
			return $output;
		}
		return '';
	}

	function ISAction($module,$output){
		if($module == Env::getRoute()->action){
			return $output;
		}
		return '';
	}

	function ISBundle($module,$output){
		if($module == Env::getRoute()->bundle){
			return $output;
		}
		return '';
	}

	// Run the module
	function run(){
		$f = $this->action;

		/**
			restore a stored state
		**/
		if($f =='restore'){

			if($history = Env::getHistory()->restoreState($this->get->get('key'))){
				$f = Env::getRoute()->action;
			}else{
				$f = 'index'; // replace with default action
			}
			//$f = $history->getAction();
			#var_dump($f);
			#die();
			#var_dump(Env::$get);
			#die();

		}
		/**
		Process route
		**/
		if(method_exists($this,$f.'Action') || method_exists($this,Env::getConfig()->get('wildcardAction'))){
			if($this->check_access()){
				if($this->assert_params()){
					if(method_exists($this,'beforeActionRun')){
					//	var_dump('test');
						$this->beforeActionRun($this->action);
					}

					$this->response = $this->$f();

					if(method_exists($this,'afterActionRun') ){
						$this->afterActionRun($this->action,$this->response);
					}

					if($this->response && is_a($this->response,'\FDT2k\ICE\CORE\Response\Response') ){
						$this->response->output();
					}
				}else{
					header('HTTP/1.0 500');
					throw new  Exception\ActionException('parameter validation failed : '.$this->getError(),0);
				}
			}else{
				Env::getRoute()->setFrom();
				//throw new  \ICE\core\Exception('Unauthorized access',0);
				if (($action = Env::getConfig('auth')->get('login_action'))===false){
					throw new  Exception\Exception('Unauthorized access and nothing to redirect to',0);
				}
				Env::getRoute()->redirect(Env::getConfig('auth')->get('login_action'),'','',array('error'=>__('Vous n\'avez pas accès à cette page')));
			}
		}else{
			header('HTTP/1.0 500');
			throw new  Exception\ActionException('unkown method '.$f.' called or route not found',0);
		}

	}


	function assert_params(){
		$c = Env::getConfig('module');
		if($c){
			if( $c->get('form_security_enabled') ){

				//var_dump('params');
				$security = Env::getConfig('module')->get('form_security');
				$bundle = Env::getRoute()->bundle;
				$module = Env::getRoute()->module;
				$action = Env::getRoute()->action;
				$has_post = $this->post->hasDatas();
				$has_get = $this->get->hasDatas();
				#var_dump($security);


				//find out parameters
				$d = isset($security[$bundle][$module][$action]['allow']);
				$allow = $security[$bundle][$module][$action]['allow'];
				$fields = $security[$bundle][$module][$action]['fields'];
				if(!$d){
					$d = isset($security[$bundle][$module]['allow']);
					$allow = $security[$bundle][$module]['allow'];
					$fields = $security[$bundle][$module]['fields'];
					if(!$d){
						$d = isset($security[$bundle]['allow']);
						$allow = $security[$bundle]['allow'];
						$fields = $security[$bundle]['fields'];
						if(!$d){
							$d = isset($security['allow']);
							$allow = $security['allow'];
							$fields = $security['fields'];
						}
					}

				}

				/*var_dump($allow);
				var_dump($fields);

				var_dump($bundle);
				var_dump($module);
				var_dump(isset($security[$bundle][$module]));

				var_dump($d);*/


				if($has_post && strpos($allow, 'post')===false){
					$this->setError('POST denied by configuration');
					return false;
				}

				if($has_get && strpos($allow, 'get')===false){
					$this->setError('GET denied by configuration');
					return false;
				}

				if($has_post){
					$datas = $this->post->getDatas();
				}

				if($has_get){
					$datas = $this->get->getDatas();
				}


				foreach($datas as $key=>$value){

					if(!in_array($key, $fields)){
						$this->setError('GET['.$key.'] denied by configuration');
						return false;
					}

				}

			}
		}
		return true;
	}




	function check_access(){

		if(Env::getConfig('auth')->get('security_enabled') && $this->user){

			Env::getLogger()->log("Module security is enabled");
			$flags = Env::getConfig('auth')->get('security_flags');
			$security = Env::getConfig('auth')->get('security');
			$has_access = false;
			$user_flag = $this->user->get('access_level');

			$bundle_flag = 0;
			$bundle = Env::getRoute()->bundle;
			$module = Env::getRoute()->module;
			$action = Env::getRoute()->action;
		//	var_dump('test');
		//	var_dump($user_flag);
		#	var_dump($flags);
		#	var_dump($user_flag);
		#	var_dump($action);
		#	var_dump($security[$bundle][$module]['has_flag']);
			Env::getLogger()->log('Processing module auth');
			if(
					(isset($security[$bundle][$module][$action]['has_flag']) && ($flag = $security[$bundle][$module][$action]['has_flag'])) ||
					(isset($security[$bundle]['*'][$action]['has_flag']) && ($flag = $security[$bundle]['*'][$action]['has_flag'])) ||
					(isset($security['*']['*'][$action]['has_flag'])&&($flag = $security['*']['*'][$action]['has_flag']))
			){
				$flag= $flags[$flag];

				if((intval($user_flag) & intval($flag))==$flag){
					$has_access=true;
				}
			}else if(

					(isset($security[$bundle][$module]['has_flag'])&&($flag = $security[$bundle][$module]['has_flag'])) ||
					(isset($security[$bundle]['*']['has_flag'])&&($flag = $security[$bundle]['*']['has_flag'])) ||
					(isset($security['*']['*']['has_flag'])&&($flag = $security['*']['*']['has_flag']))
			){
				$flag= $flags[$flag];
				if((intval($user_flag) & intval($flag))==$flag){
					$has_access=true;
				}
			}else if(

					(isset($security[$bundle]['has_flag'])&&($flag = $security[$bundle]['has_flag']))||
					(isset($security['*']['has_flag'])&&($flag = $security['*']['has_flag']))

			){
				$flag= $flags[$flag];
				if((intval($user_flag) & intval($flag))==$flag){
					$has_access=true;
				}
			}else if($flag = $security['has_flag']){
				$flag= $flags[$flag];

				if((intval($user_flag) & intval($flag))==$flag){

					$has_access=true;
				}
			}
			//var_dump($has_access);
			Env::getLogger()->log("Required flag is :".$flag." user flag:".$user_flag);
			return $has_access;
		}else{
			Env::getLogger()->log("Module security is disabled");
		}

	//	Env::getProfiler()->render();
		return true;

	}

	//override php function call (if the method doesn't exist)
	/*
		Meaning that when we do a call to an object, if it does not exists, it assume
	*/
	function __call($name,$args){

		if(method_exists($this,$name.'Action')){ // check if there is an action
			$f = $name.'Action';
			$response = $this->$f($args);
			return $response;
		}else{ // redirect to wildcardaction
			$f= Env::getConfig('core')->get('wildcardAction');
			if(method_exists($this,$f)){
				$response = $this->$f($name,$args);
				return $response;
			}


		}
		// report an error, something gone wrong
		header("HTTP/1.0 500 Internal Server Error");
		echo $name." not found";
		die();
	}





	function loginAction(){

		if($this->post->hasDatas()){

			$name = $this->post->get('email');
			$password = $this->post->get('password');
			//$result = $this->user->login($name,$password);
			$result = Env::getAuthService()->authenticate($name,$password);
		//	var_dump($result);
			/*f($this->user->get('email_confirmed') ==0 && $result){
				Env::getRoute()->redirect('confirm','register');
				die();
			}*/
			if($result){
				if($this->get->get('redirect')!=""){
					
					header('Location: '.urldecode($this->get->get('redirect')));
					die()
				}else{
					Env::getRoute()->redirectFrom('index');
				}
			}else{
			//	Env::getRoute()->redirectFrom('login','','',array('error'=>'login failed'));
				$this->response->setError(__('Le nom d\'utilisateur ou le mot de passe est incorrect'));
			}
		}
		if($error = $this->get->get('error')){
			$error = strip_tags($error);
			$this->response->setError($error);
		}



		$this->response->setTemplate('login.xml');
		return $this->response;
	}



	function _logoutAction(){
	//	$this->user->logout();
		Env::getUserSessionService()->destroy();
		Env::getRoute()->redirect('index','base');
	}


	// Legacy crap

	function setActionRedirect($action,$url){
		trigger_error("Not used anymore, you should use ROUTER OBJECT to redirect", E_USER_WARNING);
		$s = Env::getSession();
		$s->set('redir_'.$action,$url);
	}

	function actionRedirect(){
		trigger_error("Not used anymore, you should use ROUTER OBJECT to redirect", E_USER_WARNING);
		$s = Env::getSession();
		if($url = $s->get('redir_'.$this->action)){
			$s->set('redir_'.$this->action,'');
			header('Location: '.$url);
			die();
		}
	}

	function redirectWithError($url,$error,$info = array()){
		trigger_error("Not used anymore, you should use ROUTER OBJECT to redirect", E_USER_WARNING);

		$s = "&";
		foreach($info as $k => $value){
			$p .= $s.$k."=".$value;
			$s = "&";
		}
		header('Location: '.$url.'?err='.Hash::signAndURLEncode($error).$p);
		die();
	}


	function is_logged(){
		return Env::getAuthService()->is_logged();
	}

	function redirect($url,$info=array()){
		trigger_error("Not used anymore, you should use ROUTER OBJECT to redirect", E_USER_WARNING);
		$s = "?";
		foreach($info as $k => $value){
			$p .= $s.$k."=".$value;
			$s = "&";
		}
		header('Location: '.$url.$p);
		die();
	}

}
