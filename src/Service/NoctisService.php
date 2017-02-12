<?php
namespace FDT2k\Noctis\Core\Service;
use \FDT2k\Noctis\Core\Env as Env;

class NoctisService

{

//loading function, ordered by priority

// is ran before framework Initialization. At boot Environmnent is not initialized yet
  function runBeforeInit(){}

// is ran after Env::preinit(). Config is already available
  function runAfterFrameworkPreInit(){}

// is ran after Env::init()  Config available and Full env.
  function runAfterFrameworkInit(){}

//controller is instanciated and is about to be run
  function runBeforeControllerExec($controller){}

// controller has finished
  function runAfterControllerExec($controller){}

// Framework has finished
  function runOnFrameworkExits(){}

// PHp is shutdowning for any reason
  function runOnShutdown(){}

// PHP spl_autoload
  function runOnAutoload($name){}



}
