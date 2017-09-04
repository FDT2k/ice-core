<?php
$dir = dirname(dirname(__FILE__));
set_include_path($dir);

use PHPUnit\Framework\TestCase;

define('ICE_ROOT',$dir);
define('ICE_PATH','');
require ($dir."/vendor/autoload.php");
require ($dir."/src/Env.php");
require ($dir."/src/Service/ServiceManager.php");
require ($dir."/src/Cli/OptionsParser.php");

use \FDT2k\Noctis\Core\Env as Env;

class EnvTest extends TestCase
{
  protected function setUp()
     {
       global $argv, $argc;
        putenv('ICE_CONFIG=env_test');

      /*  \FDT2k\Noctis\Core\Env::preinit($argv);
        var_dump(\FDT2k\Noctis\Core\Env::getFSConfigPath());
        \FDT2k\Noctis\Core\Env::init($argv);

        $this->router =   \FDT2k\Noctis\Core\Env::getRouter();*/
     }
  public function getURI($url){
    return new \FDT2k\Helpers\URI($url);
  }
  public function getRouter(){
    return \FDT2k\Noctis\Core\Env::getRouter();
  }
  public function testLoadConfig()
  {
     global $argv, $argc;
    Env::preinit($argv);

    $v = Env::getConfig('test')->get('test');

    $this->assertTrue($v=='nofqdn');

  }
  public function testLoadConfigFQDN()
  {
    global $argv, $argc,$_SERVER;
    $_SERVER['HTTP_HOST']="www.test.com";
    Env::preinit($argv);

    $v = Env::getConfig('test')->get('test');

    $this->assertTrue($v=='fqdn');

    $v = Env::getConfig('test')->get('test2');

    $this->assertTrue($v=='extended');

  }





}
