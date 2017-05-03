<?php
namespace FDT2k\Noctis\Core;


use \FDT2k\Helpers as h;

class Router extends \IObject{

	function __construct($group = 'router'){

		$this->configGroup = $group;
	}

	function match($uri = ''){
		$match = false;
		if(empty($uri)){
			$uri = Env::getRequest()->getURI();
		}
		$config_group = $this->configGroup;

		$keys = Env::getConfig($config_group)->getKeys();

		$allowed_bundles = Env::getConfig($config_group)->get('allowed_bundles');
		$lazyCheck = Env::getConfig($config_group)->get('lazy'); // lazy check stop at the first match
		$matched_rule = false;
		foreach ( $keys as $route_idx =>$route_name){
			$route = Env::getConfig($config_group)->get($route_name);
			$regexp = $route['rules']['pattern'];
			$fqdn_regexp = $route['rules']['fqdn_pattern'];
			$method = $route['rules']['method'];
			$r = $route['route'];
			$match_var = "/(:[a-z0-9_]*)/";
			preg_match_all($match_var, $regexp, $matches);


			//generating the regex to match against the uri;

			if($regexp[0]=="/"){
				$regexp ="^".substr($regexp, 1);
			}
			$regexp = str_replace("/","\/",$regexp);
			$regexp = "/".$regexp."$/";

			if(is_array($matches[0])){
				foreach ($matches[0] as $m){
					$name = str_replace(":","",$m);
					$regexp = str_replace($m,"(?P<$name>[A-Za-z0-9_]*)",$regexp);
				}
			}




			if(!empty($fqdn_regexp) && preg_match('/'.$fqdn_regexp.'/',$uri->hostname)){
				$fqdn_match = true;
			}else{
				$fqdn_match = false;
			}
			// no fqdn match
			if (empty($fqdn_pattern) || $fqdn_pattern == '*'){

				$fqdn_match = true;
				$fqdn_pattern = "";
			}

			$this->setLastRegexp($regexp);
			//match against the uri
			if($fqdn_match){
//var_dump(preg_match_all($regexp, $uri->baseurl, $result),$regexp,$uri->baseurl);
				if(preg_match_all($regexp, $uri->baseurl, $result)){

					//matching against the method, empty method assumes all methods
					if(empty($method)){
						$method="GET";
					}
					if(strpos($method, Env::getRequest()->getMethod()) !== false || empty($method)) {
						// apply variables
						if(is_array($matches[0])){

							foreach ($matches[0] as $m){
								$name = str_replace(":","",$m);
								$value = $result[$name][0];
								$var[$name]=$value;
								//apply variable results to route // variable prefix is %
								foreach($r as $key => $route_value){
									if(strpos($route_value,'%'.$name.'%')!==false){ // route item value is matching a var name
										$r[$key]=str_replace('%'.$name.'%',$value,$route_value);
									}
								}
							}

							$this->setVariables($var);
						}
						$match = true;
						$this->setRouteName($route_name);

						if(is_array($r)){

							$this->setBundle($r['bundle']);

							$this->setModule($r['controller']);

							$this->setAction($r['action']);
						}


							break;


					}
				}
			}
		}

		if(empty($this->getBundle())){
			if(!empty($uri->path[0])){
				$this->setBundle($uri->path[0]);
			}else{
				$this->setBundle('base');
			}
		}

		if(empty($this->getModule())){
			if(!empty($uri->path[1])){
				$this->setModule($uri->path[1] );
			}else{
				$this->setModule('base');
			}
		}

		if(empty($this->getAction())){
			if(!empty($uri->path[2])){
				$this->setAction($uri->path[2]);
			}else {
				$this->setAction('index');
			}
		}
//var_dump($this,$uri);
		if($allowed_bundles && !in_array($this->getBundle(), $allowed_bundles)){
			return false;
		}
		if(empty($this->getBundle())||empty($this->getAction()) || empty($this->getModule()) ){
			return false;
		}
		$this->applyVariables(Env::getRequest()->request);
		return $this;

	}

	function applyVariables($controller){
		$variables = $this->getVariables();
		//var_dump($variables);
		if (is_array($variables)) {
			//foreach ($variables as $key => $value) {

			$controller->setDatas($variables);
			//}
		}
	}
}
