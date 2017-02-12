<?php

use FDT2k\Noctis\Core\Env ;

class NoctisBoot
{

	static function boot()
	{
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
	}
}
