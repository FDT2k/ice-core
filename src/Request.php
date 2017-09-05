<?php
namespace FDT2k\Noctis\Core;


class Request extends \IObject{


	function __construct(){
		$request = $_SERVER;

		$this->setMethod(strtoupper($request['REQUEST_METHOD']));
		//$this->setURI(new \ICE\lib\helpers\URI(str_replace($_SERVER['SCRIPT_NAME'],"","http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])));

		$this->setURI(Env::getURI());
		$this->setContentType($request['CONTENT_TYPE']);

		if(Env::$platform == ICE_ENV_PLATFORM_WS_APACHE){
			//$this->setHeaders(\getallheaders());
			$headers = \getallheaders();
			$_headers = array();
			foreach($headers as $key => $value){
				$_headers[strtolower($key)]=$value;
			}
			$this->setHeaders($_headers);
		}

		$this->get = \IObject::create()->setDefaultDatas($_GET);
		$this->post = \IObject::create()->setDefaultDatas($_POST);

		$vars = $_GET;

		$vars = array_merge_recursive($vars,$_POST);

		if(strtolower($this->getContentType())=='application/x-www-form-urlencoded'){

			$this->setContent(file_get_contents('php://input'));
			$m = strtolower($this->getMethod());
			parse_str($this->getContent(),$method_var);
			//var_dump($this->getContent());
			//var_dump($vars);

			if(is_array($method_var)){
				$vars = array_replace_recursive($vars,$method_var);
			}
			$this->$m = \IObject::create()->setDefaultDatas($method_var);
		}else if (strpos($this->getContentType(), "application/json") !== false){
			$postdata = file_get_contents("php://input");
			$method_var = json_decode($postdata,true);
			$m = strtolower($this->getMethod());
			if(is_array($method_var)){
				$vars = array_replace_recursive($vars,$method_var);
			}
			$this->$m = \IObject::create()->setDefaultDatas($method_var);
		}

		$this->request = \IObject::create()->setDefaultDatas($vars);

		if($str = $this->getHeader('Authorization')){
			 list($jwt) = sscanf( $str, 'Bearer %s');
			 $this->setToken($jwt);
			// var_dump($jwt);
		}
	}

	function getHeader($key){
		$key = strtolower($key);
		$h = $this->getHeaders();
		return $h[$key];
	}

}
