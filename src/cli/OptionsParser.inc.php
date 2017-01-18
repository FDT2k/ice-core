<?php


namespace ICE\core\cli;

/*
	leaving out php option parser
*/

class OptionsParser extends \ICE\core\iObject{


	function parse($input=false){
		$this->input = $input;

		$this->values = array();
		// flag to say when we've reached the end of options
		$done = false;
		// sequential argument count;
		$args = 0;
		$last_option= "script_name";
		// loop through a copy of the input values to be parsed
		while ($this->input) {
			 // shift each element from the top of the $this->input source
			 $arg = array_shift($this->input);
			 // after a plain double-dash, all values are args (not options)
			 if ($arg == '--') {
					 $done = true;
					 continue;
			 }

			 var_dump($arg);
			 // long option, short option, or numeric argument?
			 if (! $done && strpos($arg, '--') === 0) {
				 	$arg = substr($arg,2);
					$this->addLongFlag($arg);
					$last_option=$arg;
			 } elseif (! $done && strpos($arg, '-') === 0) {
				 $arg = substr($arg,1);
					$this->addShortFlag($arg);
					$last_option=$arg;
			 } else {
					//var_dump(array($last_option,$arg));
					$this->values[$args ++] = $arg;
					$this->setDatas(array($last_option=>$arg));
			 }

		}
		// done
		return $this->errors ? false : true;
	}
}
