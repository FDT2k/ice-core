<?php

namespace FDT2K\ICE\CORE;

date_default_timezone_set("Europe/Zurich");

function __($string){
	if($translator = Env::getTranslator()){
		return $translator->getTranslation($string);
	}else{
		return $string;
	}
}

function array_flip_key($array,$key){
	$result = array();
	if (is_array($array)){
		foreach($array as $k => $value){
			$result[$value[$key]]=$value;
		}
	}
	return $result;
}

function isEmpty($val) {
	if(!is_string($val)){return false;}
	$val = trim($val);
	return empty($val) && $val !== 0 && $val !=="0";
}
define('ICE_ENV_PLATFORM_WS_APACHE',"apache");
define('ICE_ENV_PLATFORM_CLI',"console");
define('ICE_ENV_PLATFORM_UNKOWN',"unkown");


class Env{
	public static $translator;
	public static $platform;
	public static $browser;
	public static $logger;
	public static $uri;
	public static $config;
	public static $session;
	public static $route;
	public static $history;
	public static $post;
	public static $get;
	public static $env;
	public static $argv;
	public static $profiler;
	public static $request;
	public static $router;
	public static $authenticationService;
	public static $options;

	public static $userSessionService;
	public static function shutdown(){

		self::getProfiler()->render();

	}

	public static function autoload($name){
		//var_dump($name);
		if(strpos($name, '\\')!==false){
			$path = self::getNSPath($name);

		/*	if($name =='Model_Product'){
				var_dump($path);
				throw new \Exception();
			}*/
			if(Env::getLogger()){
				Env::getLogger()->log("Loading: ".$name." = " .$path."<br>");
			}
			include_once($path.".".ICE_EXT);
		}
	}


	public static function getDatabase(){
		$c = Env::getConfig('database');

		//var_dump($c);
		//var_dump($c->get('sgbd'));
		return  \ICE\lib\database\Database::factory('id',
			$c->get('sgbd').'://'.$c->get('username').':'.$c->get('password').'@'.$c->get('host').'/'.$c->get('database')
			);

	}
	public static function getNSPath($name){
		if($l = explode("\\",$name)){
			if(sizeof($l) > 1){
				list($ns,$class) = $l;
			}else{
				$class = $l;
				$ns = '';
			}
		}
		if($ns == 'ICE'){
			$path = ICE_ROOT.ICE_PATH;
			//var_dump($path);
			$path = str_replace(array("\\","ICE"),array("/",ICE_ROOT.ICE_PATH),$name);

		}else{
			$path = ICE_ROOT.ICE_PATH."/bundles/".str_replace(array("\\"),array("/"),$name);
		}
		return $path;
	}

	public static function getBundlesPath(){
		return ICE_ROOT.ICE_PATH."/bundles/";
	}


	public static function getBundlePath($name){
		$ns = $name;
		if($l = explode("\\",$name)){
			$ns = $l[0];

		}
		return self::getBundlesPath().$ns;
	}
	public static function getModuleName($name){
		$ns = $name;
		if($l = explode("\\",$name)){
			$ns = $l[count($l)-1];

		}
		return strtolower($ns);
	}

	public static function getBundleName($name){
		$ns = $name;
		if($l = explode("\\",$name)){
			$ns = $l[0];

		}
		return $ns;
	}

	public static function getTemplatesPath($namespace){
		$path = self::getConfig('path')->get('templatePath');
		return array(
			self::getBundlePath($namespace).'/'.$path.'/'.self::getBundleName($namespace),
			self::getBundlePath($namespace).'/'.$path
			);
	}

	public static function getImagesPath($namespace){
		$path = self::getConfig('path')->get('imagePath');
		return array(
			self::getWebFSPath().'/'.self::getBundleName($namespace).'/'.$path,
			self::getWebFSPath().'/'.$path
			);
	}

	public static function getStylesPath($namespace){
		$path = self::getConfig('path')->get('stylesheetPath');
		return array(
			self::getWebFSPath().'/'.self::getBundleName($namespace).'/'.$path,
			self::getWebFSPath().'/'.$path
			);
	}


	function getScriptsPath($namespace){
		$path = self::getConfig('path')->get('scriptPath');
		return array(
			self::getWebFSPath().'/'.self::getBundleName($namespace).'/'.$path,
			self::getWebFSPath().'/'.$path
			);
	}



	public static function getBrowser(){
		return self::$browser;
	}

	public static function getConfig($group='core'){
		return self::$config->setGroup($group);
	}

	public static function getSession(){
		return self::$session;
	}

	public static function getAuthService(){
		return self::$authenticationService;
	}
	public static function getUserSessionService(){
		return self::$userSessionService;
	}
	public static function getClientIP(){
	//var_dump($_SERVER);
		return (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strlen($_SERVER['HTTP_X_FORWARDED_FOR']) > 4) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
	}
	public static function assertCLI(){
		if(self::$platform != ICE_ENV_PLATFORM_CLI){
			throw new ICE\Exception("This script have to be used with Command line interface");
		}
	}

	/*getting options without failing*/
	public static function getOptions($s,$l=array()){
			return \getopt($s,$l);
	}

	public static function preinit($argv){
//		spl_autoload_register(__NAMESPACE__ .'\Env::autoload');
		self::$profiler = new core\Profiler;
		register_shutdown_function('\ICE\Env::shutdown');
		ini_set('output_buffering','0');
		ini_set('error_reporting','E_ALL');
		error_reporting( E_ALL & ~E_NOTICE &~E_STRICT);
		ini_set('display_startup_errors', 'on');
		ini_set('display_errors', 'on');
		ini_set('session.gc_probability','1');

		ini_set('session.gc_divisor','20');
		ini_set('session.gc_maxlifetime','300');
		//session.gc_divisor
		self::$options = new \ICE\core\cli\OptionsParser();
		self::$options->parse($argv);
		//var_dump(self::$options->getDatas());
		switch(php_sapi_name()){
			case 'apache2handler':
			case 'apache':
			case 'apache2filter':

				self::$platform = ICE_ENV_PLATFORM_WS_APACHE;
				self::$browser = new lib\helpers\Browser();
			break;
			case 'cli':
				self::$platform = ICE_ENV_PLATFORM_CLI;
				break;
			default:
				self::$platform = ICE_ENV_PLATFORM_UNKOWN;
			break;
		}

		if(self::$platform==ICE_ENV_PLATFORM_WS_APACHE){
			self::$uri = new lib\helpers\URI(str_replace($_SERVER['SCRIPT_NAME'],"","http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
		}else{ // assuming cli env
			$o = \getopt("u:");

			/*$str = "";
			foreach($argv as $k=>$value){
				if($k==0){
					continue;
				}
				$str .= $sep.$value;
				if($k==1){
					$sep="?";
				}else{
					$sep="&";
				}
			}*/
		//	var_dump($str);
			self::$uri = new lib\helpers\URI(str_replace($_SERVER['SCRIPT_NAME'],"","cli://localhost".$o['u']));
		}
		self::$request = new core\Request();
		// retrieving ENV, for current configuration
		$found = false;
		// if environment variable is set we take this one
		if(getenv('ICE_CONFIG')!=''){
			self::$env = getenv('ICE_CONFIG');
			$found= true;
		}else{ // searching in FQDN config
			$c = new core\Config('','core'); // fqdn.yaml is always in core
		//	$found = false;

			if($map = $c->setGroup('fqdn')->get('config_mapping')){

				foreach($map as $fqdn =>$config){
					#var_dump(self::getFQDN());

					if(preg_match('/'.$fqdn.'/',self::getFQDN())){

						self::$env =$config;
						$found = true;
						break;
					}
				}
			}
		}
		// at last we get the one defined in config.inc.php

		if(!$found){
			self::$env = ICE_ENV;
		}
		self::$config = new core\Config('',self::$env);

		//grabbing root ws path
		if(($path = self::getConfig()->get('web_ws_path',true))!== false){
			define('ICE_WEB_WS_PATH',$path);
		}else{
			define('ICE_WEB_WS_PATH',DEFAULT_ICE_WEB_WS_PATH);
		}
		$profiler =self::getConfig('core')->get('profiler',true);
#var_dump($profiler);

		if(!empty($profiler)){
			self::getProfiler()->setEnabled(true);
		}else{
			self::getProfiler()->setEnabled(false);
		}

	}

	public static function init($argv=array()){
		//self::preinit();
		//core\Config::init();
		self::$argv = $argv;
		self::$post = \ICE\core\Post::create()->setPost($_POST);
		self::$get = \ICE\core\Get::create()->setGet($_GET);
		//var_dump(self::$env);
		//self::$config = new core\Config('',self::$env); // moved in preinit
		date_default_timezone_set(self::getConfig('core')->get('timezone'));
		self::initLogger();
		self::$session = new core\Session();
		if ( self::getConfig('session')->get('handler') == 'database'){

			session_set_save_handler(self::$session, true);
		}
		session_start();
		self::$route = new core\Route();
		self::$router = new core\Router();
		self::$session->init();
		if($s = self::getConfig('auth')->get('auth_service')){
			self::$authenticationService=  new $s();
		}
		if($s = self::getConfig('auth')->get('session_service')){
			self::$userSessionService=  new $s();
		}

		if($locale = Env::getConfig()->get('locale')){
		//var_dump($locale);
			setlocale(LC_ALL,$locale);
		}
		self::initTranslator();
		Env::getLogger()->startLog('env init');




	//	var_dump($_SERVER);
		Env::getLogger()->endLog('env init');

//$r = new
	//	self::dump();
	}

	public static function getRoute(){
		return self::$route;
	}
	public static function getRouter(){
		return self::$router;
	}
	public static function getRequest(){
		return self::$request;
	}

	public static function getProfiler(){
		return self::$profiler;
	}
	public static function getURI(){

		return self::$uri;
	}
	public static function initLogger(){

		$class = self::getConfig('logger')->get('logger_class');
		if($class && class_exists($class)){
			$logger = new $class;
		}else{
			$logger=  new core\Logger();
		}


		Env::setLogger($logger);


	}

	public static function setLogger($logger){
		self::$logger = $logger;
	}

	public static function getLogger($category=''){
		if(self::$logger){
			return self::$logger->setCategory($category);
		}else{
			return false;
		}
	}

	public static function initTranslator(){
		//langage should go in im core env... temporary for testing
		$translation = new core\TranslationBase();
		Env::setTranslator($translation);
		//self::$langage = $translation->language;
	}

	public static function setTranslator(&$object){
		self::$translator = $object;
	}

	public static function getTranslator(){
		return self::$translator;
	}
	public static function getHistory(){
		if(!isset(self::$history)){

			self::$history = new core\History();
		}
		return self::$history;
	}

	public static function getBuildPath(){
		return ICE_ROOT."/build";

	}

	public static function getFSPath(){
		return ICE_ROOT.ICE_PATH;
	}
	public static function getCachePath($folder=''){
		$path= Env::getFSPath()."/caches/".$folder;
		if(!file_exists($path)){
			mkdir($path,0777,true);

		}else{
			@chmod($path,0777);
		}
		return $path;
	}
	public static function getWebFSPath(){
		return ICE_ROOT.ICE_WEB_FS_PATH;
	}

	public static function getUploadFSPath(){
		return Env::getWebFSPath()."/uploads";
	}

	public static function getWebWSPath(){
		//var_dump(ICE_WEB_WS_PATH);
		return ICE_WEB_WS_PATH;
	}

	public static function getWSPath(){
		//var_dump(ICE_WEB_WS_PATH);
		return ICE_WEB_WS_PATH;
	}

	public static function env(){
		return self::$env;
	}

	public static function toWSPath($path){
		$path = str_replace(self::getWebFSPath(),self::getWebWSPath(),$path);
		return $path;
	}

	public static function toFSPath($path){
		//$path = str_replace(self::getWebFSPath(),self::getWebWSPath(),$path);

		return self::getWebFSPath().str_replace(ICE_WEB_WS_PATH,'',$path);
	}

	public static function getParams($filter= ''){
		$r = false;
		if(empty($filter) || !is_array($filter)){
			return $_POST;

		}


		foreach($_POST as $key =>$value){
			if(in_array($key,$filter)){
				$r[$key]=$value;
			}
		}
		return $r;
	}

	public static function getPOST($filter=''){
		return Env::getParams($filter);
	}

	public static function getGET($filter=''){
		$r = false;
		if(empty($filter) || !is_array($filter)){
			return $_GET;

		}


		foreach($_GET as $key =>$value){
			if(in_array($key,$filter)){
				$r[$key]=$value;
			}
		}
		return $r;
	}

	public static function getFSConfigPath(){
		return self::getFSPath()."/conf";
	}

	public static function path($pathes){
		$p= "";
		if(is_array($pathes)){
			foreach($pathes as $path){
				$p = self::catPath($p,$path);
			}
		}
		return $p;
	}

	public static function catPath($path,$path2,$absolute=true){
		//clean concatenation of pathes
		if($absolute  || (!empty($path) && !empty($path2))){
			if($path[strlen($path)-1] =='/' && $path2[0]=='/'){// if the first path ending with / or second starting with /
				$path2= substr($path2,1);
			}else if ($path[strlen($path)-1] !='/' && $path2[0]!='/'){ // else if any of them have a /
				$path .='/';
			}
		}
		return $path.$path2;
	}

	public static function getFQDN(){
		return $_SERVER['HTTP_HOST'];
	}

	public static function getServerPrefix(){
		if($_SERVER['HTTPS']=="on"){
			$prefix = 'https://';
		}else{
			$prefix='http://';
		}
		return $prefix.self::getFQDN();

	}



	public static function assertParams($assertion,$params){
		$result = true;
		foreach($assertion as $field=>$condition){
			if(!isset($params[$field])){
				throw new \Exception('field '.$field.' should be set');
			}
		}
		return $result;
	}
}
