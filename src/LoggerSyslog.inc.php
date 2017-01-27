<?php

namespace FDT2k\Noctis\Core;

use \ICE\Env as Env;
class LoggerSyslog extends Logger{

	protected $strings;
	protected $timers;
	protected $log;
	public $total_time;
	function __construct(){
		$this->key= time();
		$this->strings = "";
		$this->setCategory('core');
		if(Env::getConfig('core')->get('loggerEnabled')){
			//avoid dummy if
			$this->logf= function ($string,$timing='',$count=-1){
				/*$string = str_replace(array("\n","\r"),"",$string);
				if(empty($timing)){
					$log=array();



					$t = microtime(true);
					$micro = sprintf("%06d",($t - floor($t)) * 1000000);
					$d = new \DateTime( date('Y-m-d H:i:s.'.$micro, $t) );


					$log['timer']=$d->format("H:i:s.u");;
					$log['string']=$string;
					//$this->strings[]=date("H:i:s.u",microtime(true)).' '.$string;
					$this->strings[$this->category][]=$log;
				}else{
					$t = microtime(true);
					$micro = sprintf("%06d",($t - floor($t)) * 1000000);
					$d = new \DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
					$a['timer']=$d->format("H:i:s.u");;
					$a['string']= $string;;
					$a['backtrace']=debug_backtrace( false );
					$a['total_time']= $timing."ms";
					if($count>-1){
						$a['count']= $count;

					}
					$this->strings[$this->category][]=$a;
				}*/

        $result = \syslog(LOG_WARNING,$string);
        
			};
		}else{
			$this->logf = function($string,$timing='',$count=-1){};

		}
	}

}
