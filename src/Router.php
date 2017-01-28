<?php
namespace FDT2k\Noctis\Core;


use \FDT2k\Helpers as h;

class Router extends \IObject{

	function match(){
		$match = false;
		$uri = Env::getRequest()->getURI();
		$keys = Env::getConfig('router')->getKeys();
		$allowed_bundles = Env::getConfig('router')->get('allowed_bundles');
		foreach ( $keys as $route_name =>$route){
			$route = Env::getConfig('router')->get($route);
			$regexp = $route['rules']['pattern'];
			$fqdn_regexp = $route['rules']['fqdn_pattern'];
			$method = $route['rules']['method'];
			$r = $route['route'];
			$match_var = "/(:[a-z0-9_]*)/";
			preg_match_all($match_var, $regexp, $matches);


			//generating the regex to match the uri;

			if($regexp[0]=="/"){
				$regexp ="^".substr($regexp, 1);
			}
			$regexp = str_replace("/","\/",$regexp);
			$regexp = "/".$regexp."/";
			if(is_array($matches[0])){
				foreach ($matches[0] as $m){
					$name = str_replace(":","",$m);
					$regexp = str_replace($m,"(?P<$name>[a-z0-9_]*)",$regexp);
				}
			}




			if(!empty($fqdn_regexp) && preg_match('/'.$fqdn_regexp.'/',$uri->hostname)){
				$fqdn_match = true;
			}else{
				$fqdn_match = false;
			}

			if (empty($fqdn_pattern) || $fqdn_pattern == '*'){

				$fqdn_match = true;
				$fqdn_pattern = "";
			}

			//match against the uri
		//var_dump($fqdn_match,$fqdn_pattern);
			if($fqdn_match){
					//var_dump($regexp,$uri->hostname,$r);

				if(preg_match_all($regexp, $uri->baseurl, $result)){
					//matching against the method, empty method assumes all methods
					if(strpos($method, Env::getRequest()->getMethod()) !== false || empty($method)) {
						// apply variables
						if(is_array($matches[0])){

							foreach ($matches[0] as $m){
								$name = str_replace(":","",$m);
								$value = $result[$name][0];
								$var[$name]=$value;
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
