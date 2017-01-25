<?php

namespace FDT2k\ICE\CORE;

use \FDT2k\ICE\CORE\Env as Env;

class Profiler extends IObject{

	#public static function

	function render(){


		if($this->isEnabled()){


			$errfile = "unknown file";
			$errstr  = "shutdown";
			$errno   = E_CORE_ERROR;
			$errline = 0;

			$error = error_get_last();

			if( $error !== NULL) {
				$errno   = $error["type"];
				$errfile = $error["file"];
				$errline = $error["line"];
				$errstr  = $error["message"];

	#error_mail(format_error( $errno, $errstr, $errfile, $errline));
	//throw new \ICE\core\Exception("FATAL: ",0);
				$trace = print_r( debug_backtrace( false ), true );

				$this->setError($error);
				$this->setBacktrace($trace);
	//var_export(self::getProfiler());

			}
			$post =(Env::$post->getDatas());
			$this->setEnv(Env::$env);
			$this->setPost(var_export($post,true));
			$get =(Env::$get->getDatas());
			$this->setGet(var_export($get,true));
			$this->setExecTime(Env::getLogger()->total_time." ms");
			$this->setLogs(Env::getLogger()->dump());
			// /$this->setAuth(\ICE\Env::getAuthService()->is_logged(). " ".\ICE\Env::getUserSessionService()->getToken());



			echo "
			<script>
				function show_ice_profiler(){
					$('#profiler').find('div').show();
					$('#profiler').addClass('ice-profiler-visible');
					$('#profiler').css('background-color','rgba(255,255,255,1)');
					$('#profiler').css('opacity','1');
					$('#profiler').css('width','800px');
					$('#profiler').css('overflow','scroll');
					Cookies.set('ice_profiler_hidden', 0);

				}

				function hide_ice_profiler(){
					$('#profiler').find('div').hide();
					$('#profiler').removeClass('ice-profiler-visible');
					$('#profiler').css('background-color','rgba(255,255,255,0.2)');
					$('#profiler').css('opacity','0.2');
					$('#profiler').css('width','90px');
						$('#profiler').css('overflow','hidden');
						Cookies.set('ice_profiler_hidden', 1);
				}

				jQuery(document).ready(function($){
					var startHidden = Cookies.get('ice_profiler_hidden');
					if(startHidden==1){
						hide_ice_profiler();
					}
					console.log('profiler loaded');
					$('#profiler').click(function(e){
						//$(this).find('pre').toggle();
						if($(this).hasClass('ice-profiler-visible')){
							hide_ice_profiler();
						}else{
							show_ice_profiler();
						}
						e.stopPropagation();
					});

					$('#profiler li').each(function(e){
					//	$(this).addClass('hidden');
						$(this).find('ul').hide();

					});

					$('#profiler li').click(function(e){

							$(this).find('>ul').toggle();
							e.stopPropagation();
					});

				});
			</script>

			";
			echo "<div id=\"profiler\" class=\"ice-profiler-visible\" style=\"width:800px; height:800opx; max-height:800px;overflow:scroll;border: 2px solid black; border-radius: 10px; position:absolute; top:0px; left:0px; z-index:2000; background-color:white;\"><h1>ICE PROFILER</h1>";
//var_dump($this->getDatas());
			echo "<div style=\"border: 2px solid #FF0000;\"><ul>";
				foreach($this->getDatas() as $key => $value){
					echo "<li>".$key.":<ul>";
					if($key == 'logs'){
						if(is_array($value)){
							foreach($value as $group => $log){
								echo "<li class=\"grouplog\">".$group." (".Env::getLogger()->category_times[$group]." ms)<ul>";
								foreach ($log as $l){
									echo "<li class=\"log\" rel=\"$group\"><b>[".$l['timer']."] [".$l['total_time']."]</b>".$l['string'];
									if(is_array($l['backtrace'])){
										echo "<ul class=\"backtrace\" rel=\"\">";
											foreach($l['backtrace'] as $b){
												echo "<li><b>Line: ".$b['line']." </b>  ".$b['class']."::<b>".$b['function']."</b>  ".$b['file']."   args:".count($b['args'])."</li>";
											}
										echo "</ul>";
									}
									echo "</li>";
								}
								echo "</ul></li>";
							}
						}
					}else{
						echo "<pre>".$value."</pre>";
					}
					echo "</ul></li>";
				}
			echo "</ul></div></div>";
		}

	}
}
