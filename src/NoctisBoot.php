<?php
namespace FDT2k\Noctis\Core;
use FDT2k\Noctis\Core\Env ;
use FDT2k\Noctis\Core\Service as CoreService;
class NoctisBoot
{

	static function boot()
	{
		try{
			//booting Environnement
			if(!isset($argv)){
				$argv=array();
			}
			CoreService\ServiceManager::triggerBoot();
		  #Env::getConfig();
			Env::preinit($argv);
			Env::init($argv);
			#ICE\Env::init($argv);

		}catch(ICEException $e){

			echo $e;

		}catch(Exception $e){

			echo $e;

		}
	}
}
