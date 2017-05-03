<?php
namespace FDT2k\Noctis\Core\Response;
use \FDT2k\Helpers\CLIColors ;

class CLIResponse extends Response{




	function output($string,$fgColor=null,$bgColor=null){
		$colors = new CLIColors();
		echo $colors->getColoredString($string,$fgColor,$bgColor);
	}
}
