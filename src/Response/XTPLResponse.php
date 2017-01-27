<?php

namespace FDT2k\Noctis\Core\Response;



use FDT2k\XTPL as XTPL;

use FDT2k\ICE\CORE\Env as Env;

class XTPLResponse extends Response{

	protected $template = 'template.xml';
	protected $includes;
	function __construct($object='',$renderer=''){

		if(empty($renderer)){
			$this->renderer = new \FDT2k\XTPL\XTPLHTML();
		}else{
			$this->renderer = $renderer;

		}
		$count = 0;
		foreach($object->getAncestors() as $namespace){


			$this->renderer->addPath(Env::getBundlePath($namespace).'/templates/'.Env::getModuleName($namespace),XTPL_INCLUDE_PATH);

			$this->renderer->addPath(Env::getBundlePath($namespace).'/templates/'.Env::getBundleName($namespace),XTPL_INCLUDE_PATH);

			$this->renderer->addPath(Env::getBundlePath($namespace).'/templates',XTPL_INCLUDE_PATH);


			$this->renderer->addPath(Env::getWebFSPath().'/'.Env::getBundleName($namespace).'/styles/'.Env::getModuleName($namespace),XTPL_CSS_PATH);
			$this->renderer->addPath(Env::getWebFSPath().'/'.Env::getBundleName($namespace).'/styles/',XTPL_CSS_PATH);



			$this->renderer->addPath(Env::getWebFSPath().'/'.Env::getBundleName($namespace).'/images/'.Env::getModuleName($namespace),XTPL_IMAGE_PATH);
			$this->renderer->addPath(Env::getWebFSPath().'/'.Env::getBundleName($namespace).'/images/',XTPL_IMAGE_PATH);



			$this->renderer->addPath(Env::getWebFSPath().'/'.Env::getBundleName($namespace).'/scripts/'.Env::getModuleName($namespace),XTPL_SCRIPT_PATH);
			$this->renderer->addPath(Env::getWebFSPath().'/'.Env::getBundleName($namespace).'/scripts/',XTPL_SCRIPT_PATH);

			$count++;
		}
		$this->renderer->addPath(Env::getWebFSPath().'/styles',XTPL_CSS_PATH);
		$this->renderer->addPath(Env::getWebFSPath().'/images',XTPL_IMAGE_PATH);
		$this->renderer->addPath(Env::getWebFSPath().'/scripts',XTPL_SCRIPT_PATH);



		//setting default path for browsable content
		$this->renderer->defaultPrefixForWebContent= Env::getWebWSPath().'/'.Env::getBundleName($object->getClassName());
		$this->renderer->defaultFSPrefixForWebContent= Env::getWebFSPath().'/'.Env::getBundleName($object->getClassName());

		//var_dump($this->renderer->path);
	}

	function imagePath($image){
		return $this->renderer->findImagePath($image);
	}

	function setTemplate($tpl){
		$this->template = $tpl;
	}
	function addBlock($zone,$tpl){
		$this->blocks[$zone][] = $tpl;
		$this->currentBlockID = $zone.(sizeof($this->blocks[$zone])-1);
		$this->currentForm = 'IMCoreForm'.$this->currentBlockID;
		$tpl = $this->renderer->findIncludeFile($tpl);
		//$this->appendVariable($zone,$block);
		$this->renderer->appendToVariable($zone,$tpl);

	}
	function includeFile($file){
		$this->includes[]=$file;
		return $this;
	}
	//shortcut
	function setVariable($name,$value, $scope='global'){
		$this->renderer->setVariable($name,$value,$scope);
	}

	function output_headers(){
		header('HTTP/1.0 '.$this->getResponseCode());
		header('Content-type :'.$this->mime);

	}

	function output(){

		$this->output_headers();


		$this->setVariable('self',$this);
		$this->setVariable('ROUTE',Env::getRoute());
		$this->setVariable('SESSION',Env::getSession());
		$this->setVariable('HISTORY',Env::getHistory());

		$this->renderer->parseFile($this->template);
		//including files after everything, so we can use system variables. This can do weird things with blocks. We should do this another way.
		if(is_array($this->includes)){
			foreach($this->includes as $include){
				$this->renderer->includeFile($include);
			}
		}
		//var_dump($this);
		echo $this->renderer->render($this->template);
		//var_dump(Env::getLogger()->dump());

	}

}
