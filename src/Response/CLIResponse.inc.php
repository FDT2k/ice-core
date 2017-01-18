<?php
namespace ICE\core\response;
use \ICE\lib\helpers\CLIColors ;

class CLIResponse extends Response{

	


	function output($string,$fgColor=null,$bgColor=null){
		$colors = new CLIColors();
		echo $colors->getColoredString($string,$fgColor,$bgColor);
	}
}
