<?php

namespace FDT2K\ICE\CORE;

use \ICE\Env as Env;
class Logger{

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
				$string = str_replace(array("\n","\r"),"",$string);
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
				}

			};
		}else{
			$this->logf = function($string,$timing='',$count=-1){};

		}
	}
	public function __call($method, $args) {
		if(isset($this->$method) && is_callable($this->$method)) {
			return call_user_func_array(
				$this->$method,
				$args
				);
		}
	}

	function setCategory($category=''){
		if(empty($category)){
			$category='core';
		}
		$this->category = $category;
		return $this;
	}

	function log($string,$timing='',$count=-1){
		$this->logf($string,$timing,$count);

	}

	function startLog($identifier){
		$this->timers[$identifier]= microtime(true);
		$this->log('[START] : '.$identifier);
	}

	function endLog($identifier,$count=-1){
		$time2 = microtime(true);
		$this->log('[FINISH] : '.$identifier, (($time2-$this->timers[$identifier])*1000),$count);
		$this->category_times[$this->category]+=(($time2-$this->timers[$identifier])*1000);
		$this->total_time += (($time2-$this->timers[$identifier])*1000);
		unset($this->timers[$identifier]);

	}



	function dump($key=''){
		// finish unterminated timers
		if(is_array($this->timers)){
			foreach($this->timers as $key => $value){
				$this->endLog($key);
			}
		}
		return $this->strings;
	}


}
