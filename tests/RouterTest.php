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

class RouterTest extends TestCase
{
  protected function setUp()
     {
       global $argv, $argc;
        putenv('ICE_CONFIG=test');

        \FDT2k\Noctis\Core\Env::preinit($argv);
        var_dump(\FDT2k\Noctis\Core\Env::getFSConfigPath());
        \FDT2k\Noctis\Core\Env::init($argv);

        $this->router =   \FDT2k\Noctis\Core\Env::getRouter();
     }
  public function getURI($url){
    return new \FDT2k\Helpers\URI($url);
  }
  public function getRouter(){
    return \FDT2k\Noctis\Core\Env::getRouter();
  }
  public function testRouter()
    {

      $uri = $this->getURI('http://www.example.com/ticket');
      $router = $this->router->match($uri);
      $this->assertNotNull($router);

      $router->clearData();
      $uri = $this->getURI('http://www.example.com/api/tickets');
        \FDT2k\Noctis\Core\Env::getRequest()->setMethod('GET');
      $router = $this->router->match($uri);
      $this->assertNotNull($router);
      $this->assertEquals($router->getBundle(),'app');
      $this->assertEquals($router->getModule(),'Tickets');
      $this->assertEquals($router->getAction(),'fetchAll');

  $router->clearData();
      $uri = $this->getURI('http://www.example.com/api/tickets/1');
        \FDT2k\Noctis\Core\Env::getRequest()->setMethod('GET');
      $router = $this->router->match($uri);
      $this->assertEquals($router->getBundle(),'app');
      $this->assertEquals($router->getModule(),'Tickets');
      $this->assertEquals($router->getAction(),'fetch');

  $router->clearData();
      $uri = $this->getURI('http://www.example.com/api/tickets/1');
        \FDT2k\Noctis\Core\Env::getRequest()->setMethod('PUT');
      $router = $this->router->match($uri);

      $this->assertEquals($router->getBundle(),'app');
      $this->assertEquals($router->getModule(),'Tickets');
      $this->assertEquals($router->getAction(),'update');
      $this->assertEquals($router->getVariables()['ticket_id'],1);

  $router->clearData();
      $uri = $this->getURI('http://www.example.com/api/tickets/1');
        \FDT2k\Noctis\Core\Env::getRequest()->setMethod('POST');
      $router = $this->router->match($uri);

      $this->assertEquals($router->getBundle(),'app');
      $this->assertEquals($router->getModule(),'Tickets');
      $this->assertEquals($router->getAction(),'update');
      $this->assertEquals($router->getVariables()['ticket_id'],1);

        $router->clearData();
      $uri = $this->getURI('http://www.example.com/api/tickets/');
        \FDT2k\Noctis\Core\Env::getRequest()->setMethod('POST');
      $router = $this->router->match($uri);

      $this->assertEquals($router->getBundle(),'app');
      $this->assertEquals($router->getModule(),'Tickets');
      $this->assertEquals($router->getAction(),'add');
      $router->clearData();
      $uri = $this->getURI('http://www.example.com/api/tickets');
        \FDT2k\Noctis\Core\Env::getRequest()->setMethod('POST');
      $router = $this->router->match($uri);
      $this->assertEquals($router->getBundle(),'app');
      $this->assertEquals($router->getModule(),'Tickets');
      $this->assertEquals($router->getAction(),'add');

      $router->clearData();
      $uri = $this->getURI('http://www.example.com/api/tickets');
        \FDT2k\Noctis\Core\Env::getRequest()->setMethod('DELETE');
      $router = $this->router->match($uri);

      $this->assertEquals($router->getBundle(),'api');
      $this->assertEquals($router->getModule(),'tickets');
      $this->assertEquals($router->getAction(),'index');

      $router->clearData();
      $uri = $this->getURI('http://www.example.com/api/myRestController');
        \FDT2k\Noctis\Core\Env::getRequest()->setMethod('GET');
      $router = $this->router->match($uri);

      $this->assertEquals($router->getBundle(),'app');
      $this->assertEquals($router->getModule(),'myRestController');
      $this->assertEquals($router->getAction(),'fetchAll');


      $router->clearData();
      $uri = $this->getURI('http://www.example.com/api/myRestController/1');
        \FDT2k\Noctis\Core\Env::getRequest()->setMethod('GET');
      $router = $this->router->match($uri);

      $this->assertEquals($router->getBundle(),'app');
      $this->assertEquals($router->getModule(),'myRestController');
      $this->assertEquals($router->getAction(),'view');

      $router->clearData();
      $uri = $this->getURI('http://www.example.com/api/v2/myRestController/1');
        \FDT2k\Noctis\Core\Env::getRequest()->setMethod('GET');
      $router = $this->router->match($uri);

      $this->assertEquals($router->getBundle(),'appV2');
      $this->assertEquals($router->getModule(),'myRestController');
      $this->assertEquals($router->getAction(),'view');

      //this one should not match, because of the previous one.
      $router->clearData();
      $uri = $this->getURI('http://www.example.com/api/v2/myRestController/1');
        \FDT2k\Noctis\Core\Env::getRequest()->setMethod('GET');
      $router = $this->router->match($uri);

      $this->assertEquals($router->getBundle(),'appV2');
      $this->assertEquals($router->getModule(),'myRestController');
      $this->assertEquals($router->getAction(),'view');


      $router->clearData();


      $uri = $this->getURI('http://www.example.com/api/tickets/1');
      $this->router->configGroup= 'router2';
      \FDT2k\Noctis\Core\Env::getRequest()->setMethod('POST');
      $router = $this->router->match($uri);
    //  var_dump($uri->baseurl,$this->router->getLastRegexp()."___________-",$this->router->getRouteName());

      //var_dump($router->getLastRegexp());
      $this->assertNotNull($router);
      $this->assertEquals($router->getBundle(),'app');
      $this->assertEquals($router->getModule(),'tickets');
      $this->assertEquals($router->getAction(),'save');

    }


}
