<?php
namespace FDT2k\Noctis\Core;

use \ICE\Env as Env;
use \ICE\lib\helpers\Hash as Hash;
class Module extends  \IObject{
	protected $renderer; // render
	protected $preventRender=false;


	/**
	Have to be declared, or you'll die and kittens explode

	Called before every action

	Action context can be retrieved with $this->action or $action
**/
	function beforeActionRun($action){


	}
	/**
		// same here, everyone will die on Mars if you don't declare it, oops.
		Called after every action

		Action context can be retrieved with $this->action or $action
**/

	function afterActionRun($action,$response){


	}

	/**
	Module initialization, load default models
	**/
	function initModule(){
		parent::initModule();
	}

	//initialize default responses.
	/**
		Called after the initModule. Initialize the response object
	**/
	function initResponse(){
		parent::initResponse();
	}

	function getModel(){
		return parent::getModel();
	}


		/**
			handle undeclared actions
		**/
		function catchallAction(){

			return $this->response;
		}


	/**
		implements your actions here
	**/
	function indexAction(){

		return $this->response;
	}

}
