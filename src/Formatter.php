<?php
namespace FDT2k\Noctis\Core;

use \ICE\Env as Env;

class Formatter extends \ICE\core\Config{
	function __construct(){
		parent::__construct($group);
		$this->setGroup('formats');
	}
	
	//format from db to output
	function dateFromSource($date){
		
	}
	
	//format from input to db
	function dateForSource($date){
	
	}
	
	
	
	
}