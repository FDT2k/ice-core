<?php
namespace FDT2k\Noctis\Core;
use FDT2k\Noctis\Core\Env ;

class NoctisBoot
{

	static function boot($argv=array())
	{
		try{
			//booting Environnement
			if(!isset($argv)){
				$argv=array();
			}
			\FDT2k\Noctis\Core\Service\ServiceManager::triggerBoot();
		  #Env::getConfig();
			Env::preinit($argv);
			Env::init($argv);
			#ICE\Env::init($argv);

		}catch(ICEException $e){

			echo $e;

		}catch(Exception $e){

			echo $e;

		}catch(\Exception $e){

			echo $e;

		}
	}
}
