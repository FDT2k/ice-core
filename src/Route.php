<?php
namespace FDT2k\ICE\CORE;

use FDT2k\Helpers as Helpers;

class Route extends \ConfigManager{
	public $querystring;
	protected $appendQS;

	function __construct(){
		parent::__construct(Env::getFSConfigPath(), Env::env());
		$this->setGroup('route');
		if(!empty($_SESSION['ice_from'])){
			$this->from=$_SESSION['ice_from'];
		}
		$this->querystring = $_GET;
		$this->appendQS = true;
	}

	function clearQS(){
		//$this->querystring = array();
		$this->appendQS = false;
		return $this;
	}

	//Return the output if the route matches
	function IS($action,$module,$bundle,$output){
		$match = true;

		if($action != '' && $action != $this->action ){
			$match = false;
		}
		if($module != '' && $module != $this->module ){
			$match = false;
		}
		if($bundle != '' && $bundle != $this->bundle ){
			$match = false;
		}
		if($match){
			return $output;
		}
		return '';
	}

	function match(){
		$bFound = false;
		$uri = Env::getURI();
		$routes = $this->get('routes');

		$matches = $this->get('matches');
		$allowed_bundles = $this->get('allowed_bundles');
		// is the route matching any configuration ?
		$querystring = array();
		if($matches && count($matches) > 0){
			foreach ($matches as $match=> $route){
				//var_dump(str_replace('/','\/',$match));
				#var_dump($uri->baseurl);
				if(preg_match("/".$match."/",$uri->baseurl,$querystring)){
					if($r = $routes[$route]){
						$this->bundle = $r['bundle'];
						$this->module = $r['default_module'];
						$this->action = $r['default_action'];
						if(isset( $r['varmap'])){
							$vm= $r['varmap'];
							$qs = array();
							foreach($querystring as $k => $value){
								if(!empty($vm[$k])){
									$qs[$vm[$k]]=$value;
								}
							}
							if(!empty($qs)){
								Env::$get->setDatas($qs);

							}
						}
						if(isset($uri->path[3]) && !empty($uri->path[3])){
							$this->subaction=$uri->path[3];
						}
						return array('bundle'=>$r['bundle'],'module'=>$r['default_module'],'action'=>$r['default_action']);
					}else{
						// really bad bad error, we could end up anywhere
						throw new  Exception("Route Misconfiguration. \"".$route."\" does not exist.",0);
					}

				}
			}
		}

		// just use the route AS is

		if(isset($uri->path[0]))
		$this->bundle = $uri->path[0];

		if(isset($uri->path[1]))
		$this->module = $uri->path[1];

		if(isset($uri->path[2]))
		$this->action = $uri->path[2];

		if(isset($uri->path[3]) && !empty($uri->path[3])){
			$this->subaction=$uri->path[3];
		}

		if(empty($this->bundle)){
			$this->bundle='base';
		}

		if(empty($this->module)){
			$this->module = 'base';
		}

		if(empty($this->action)){
			$this->action = 'index';
		}
		if($allowed_bundles && !in_array($this->bundle, $allowed_bundles)){
			return false;
		}
		return array('bundle'=>$this->bundle,'module'=>$this->module,'action'=>$this->action);

	}

	//quick call for nav
	function moduleIS($module,$output){
		if($module == $this->module){
			return $output;
		}
		return '';
	}
	// reverse a route to it's configuration state
	function short($action,$module,$bundle){

		if(is_array($this->get('routes'))){
			foreach($this->get('routes') as $r){ //precalculate short links
				if($bundle == $r['bundle'] && $module ==$r['default_module'] && $action==$r['default_action'] && !empty($r['short'])){

					return $r['short'];
				}
			}

			return "/".$bundle."/".$module."/".$action;
		}
		return "/".$bundle."/".$module."/".$action;
	}

	function link($action='',$module='',$bundle='',$info=array()){

		$querystring = '';
		if(strpos($action, '/')===0){
			// if we pass something like the full path on the first param we have to parse it and ignore other parameters.
			// no support for relative pathes
//var_dump(Env::getServerPrefix()."".$action);
			$uri = new Helpers\URI(Env::getServerPrefix()."".$action);
			//var_dump($action);
	//		var_dump($uri);
			$bundle = $uri->path[0];
			$module = $uri->path[1];
			$action = $uri->path[2];
		//	var_dump($module);
			$info= $uri->query;
		}

		if(empty($bundle)){
			$bundle = $this->bundle;
		}

		if(empty($module)){
			$module = $this->module;
		}

		if(empty($action)){

			$action = $this->action;
		}else{
			if(strpos($action, '?')!==false){
				list($action,$querystring)=explode('?',$action);

			}
		}
		$qs = $this->getQS($info,$querystring);
		$this->appendQS = false;
//		var_dump($action,$module,$bundle);
		return $this->short($action,$module,$bundle).$qs;
	}

	function getQS($info = array(),$more=""){
		/*if(count($info)>0){
			$qs = $this->generateQuery($info);
		}else if($this->appendQS){
			$qs = $this->generateQuery($this->querystring);
		}else{
			$qs ="";
		}*/

		if(!$this->appendQS){
			$this->querystring = array();
		}
		if(count($info)>0){
			$this->querystring = array_merge($this->querystring,$info);
		}
$qs = $this->generateQuery($this->querystring);

		if(!empty($qs) && !empty($more)){
			$qs .="&".$more;
		}else if (!empty($more)){
			$qs = "?".$more;
		}
		return $qs;
	}


	function setFrom($from=""){
		if(empty($from)){
			$from = $this->currentURL();
	//		var_dump($from);
		}
		//var_dump($from);
		$_SESSION['ice_from'][$this->module]=$from;
		$this->from = $from;
	//	var_dump($_SESSION);

		return $this;
	}

	function browsable($path){
		return Env::getWSPath()."/".$this->bundle."/".$path;
	}

	function currentURL(){
		return $this->short($this->action,$this->module,$this->bundle).(count($info)>0 ? $this->generateQuery($info):$this->generateQuery($this->querystring));
	}

	function redirectFrom($action='',$module='',$bundle='',$info=array()){

		//var_dump($this->from);
		if( !empty($this->from[$this->module])){
			$url = $this->from[$this->module];
			$this->from[$this->module] = "";
			$_SESSION['ice_from'][$this->module]=$from;
			header('Location: '.$url);
			die();
		}
		//var_dump('test');
		$this->redirect($action,$module,$bundle,$info);
	}

	function redirect($action='',$module='',$bundle='',$info=array()){

		if(empty($bundle)){
			$bundle = $this->bundle;
		}
		if(empty($module)){
			$module = $this->module;
		}
		if(empty($action)){
			$action = $this->action;
		}
		$qs = $this->getQS($info,$querystring);
		$this->appendQS = true;
		header('Location: '.$this->short($action,$module,$bundle).$qs);
		die();
	}

		//return the string if the module is equal
	function ISModule($module,$output){
		if($module == $this->module){
			return $output;
		}
		return '';

	}

	function ISAction($module,$output){
		if($module == $this->action){
			return $output;
		}
		return '';

	}

	function ISBundle($module,$output){
		if($module == $this->bundle){
			return $output;
		}
		return '';

	}


	function generateQuery($info){
		$sep = "?";
		$string = '';
		if(is_array($info)){
		foreach($info as $key => $value){
			$string.=$sep.$key."=".urlencode($value);
			$sep ="&";
		}
		}
		return $string;
	}

	function redirectToLastState(){
		$this->clearQS()->redirectFrom('restore','','',array('key'=>Env::getHistory()->getLastState()));
	}
}
