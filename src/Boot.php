<?php
/*
This is the ICE boottrap. include this and you'll have access to namespaces loading system
*/
define('ICE_PATH','');

define('ICE_ROOT',__DIR__);
define('ICE_WEB_FS_PATH','/www');
define('DEFAULT_ICE_WEB_WS_PATH','/src/app/www');
require_once (dirname(dirname(ICE_ROOT))."/vendor/autoload.php");
use FDT2k\Noctis\Core\Env ;


//load composer things

//booting ICE

#require_once("ice/core/History.inc.php");

//var_dump(ini_get('short_tag'));

try{
	//booting Environnement
	if(!isset($argv)){
		$argv=array();
	}

  #Env::getConfig();
	Env::preinit($argv);
	Env::init($argv);
	#ICE\Env::init($argv);

}catch(ICEException $e){

	echo $e;

}catch(Exception $e){

	echo $e;

}
?>
