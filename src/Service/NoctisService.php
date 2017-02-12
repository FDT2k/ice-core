<?php
namespace FDT2k\Noctis\Core\Service;
use \FDT2k\Noctis\Core\Env as Env;

class NoctisService

{

 function runOnAutoload($name){}

 function runOnFrameworkInit(){}

 function runOnFrameworkExits(){}

 function runOnShutdown(){}

 function runBeforeControllerExec($controller){}

 function runAfterControllerExec($controller){}

 // is ran before framework Initialization. At boot
 function runBeforeInit(){}


}
