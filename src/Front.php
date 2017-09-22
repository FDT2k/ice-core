<?php
namespace FDT2k\Noctis\Core;

use \FDT2k\Noctis\Core\Env;

class Front {

  function run(){

    try{
    	Env::getLogger()->startLog('page');
    	$router = Env::getRouter();
    	$isRendered = false;
    	//for backward compatibility
    	Env::getRoute()->match();
    	// launch the routing process
    	if($route = $router->match()){

    		$class = "\\".$route->getBundle()."\\controller\\".ucFirst($route->getModule());
    		Env::getRoute()->bundle = $route->getBundle();
    		if(class_exists($class)){

    			$m = new $class($route->getAction());
          \FDT2k\Noctis\Core\Service\ServiceManager::triggerBeforeController($m);
    			$m->run();
          \FDT2k\Noctis\Core\Service\ServiceManager::triggerAfterController($m);
    		}else{
    			throw new  \Exception("Class \".".$class."\" not found",0);
    		}
    	}else{
    		throw new  \Exception("No Route found",0);
    	}

    	Env::getLogger()->endLog('page');

    	//$logs = ICE\Env::getLogger()->dump();
    }catch(IMCoreException $e){
    	echo "Exception :".$e;
    }catch(\FDT2k\Libs\DatabaseException $e){
    	echo "Database Error: ".$e;
    	die();
    }catch(\Exception $e){
    	//var_dump($e);
      echo $e;
    	if(Env::getConfig('core')->get('allow_create_missing_path')){
    		echo "<a href='".Env::getRoute()->link('createMissingPath','scaffolder','dev',array('uri'=>urlencode(Env::getURI()->pathAsString())))."'>create it</a>";
    	}
    }catch(ActionException $e){
    	echo $e;
    	if(ICE\Env::getConfig('core')->get('allow_create_missing_action')){
    		echo "<a href='".Env::getRoute()->link('createMissingPath','scaffolder','dev',array('uri'=>urlencode(Env::getURI()->pathAsString())))."'>create action</a>";
    	}
    }
  }

}
