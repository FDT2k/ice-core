<?php
namespace FDT2k\ICE\CORE;



class TranslationBase  extends IObject{
	public $translations;

	function __construct($lang=null){
		Env::getLogger()->startLog('translations init');

		$c = Env::getConfig('i18n');
		//var_dump($c);
		if(isset($_REQUEST[$c->get('cookieName')])){
			$var = $_REQUEST[$c->get('cookieName')];
		}
		if(isset($_COOKIES[$c->get('cookieName')])){
			$cookie = $_COOKIES[$c->get('cookieName')];
		}
		// getting lang from cookie
		if(!empty($var) && $this->exists($var)){
			$lang = $var;
		}

		if( empty($lang) && !empty($cookie) && $this->exists($cookie)){
			$lang = $cookie;
		}

		if(empty($lang)){
			$lang = $_SESSION[$c->get('cookieName')];

		}

		if(empty($lang)){

			$lang = $c->get('defaultLanguage');

		}

		$this->lang= $lang;
		$this->init();
		if(empty($lang) || !$this->exists($lang)){
			$lang = $c->get('defaultLanguage');

			$this->lang= $lang;
			$this->init();

		}
		$_SESSION[$c->get('cookieName')]	=	$this->lang;
		$_COOKIES[$c->get('cookieName')]	=	$this->lang;
		$this->load();
		Env::getLogger()->endLog('translations init');
		$r =setLocale(LC_ALL,$this->getLocale($this->lang).'.UTF8');

		Env::getLogger()->log('current language:'.$this->lang);
		
	}

	function getLocale($lang){

		switch ($lang){
			case 'french':
				return 'fr_FR';
			break;
			case 'english':
				return 'en_US';
			break;
			case 'german':
				return 'de_DE';
			break;

		}
	}

	function init(){
	//	$this->siteFile = Env::catPath(Env::getFSPath(),Env::getConfig('i18n')->get('translationDirectory'));
		$this->path = Env::catPath($this->getTranslationDirectory(),$this->lang.".i18n.php");

		//$this->originpath = Env::catPath($this->getTranslationDirectory(),"origin.i18n.php");

		//var_dump($this->path);
	}
	function getTranslationDirectory(){
		return Env::catPath(Env::getFSPath(),Env::getConfig('i18n')->get('directory'));
	}


	function load(){
		@include($this->path);
	}

	function parse(){

	}

	function getTranslation($key){
		if(!isset($this->translations[$key])){
			$this->translations[$key] = $key;
			$this->writeTranslation($key);

		}
		$value =trim($this->translations[$key]);
		if(empty($value)){
			return $key;
		}
		return $this->translations[$key];
	}

	function exists($lang){
		if(file_exists(Env::catPath($this->getTranslationDirectory(),$lang.".i18n.php"))){
			return true;
		}
		return false;
	}

	function isCompiled($lang){

	}

	function compile($lang){

	}

	function writeTranslation($key,$value=""){

		if($handle = @fopen($this->path,"w")){
				@flock($handle,LOCK_EX);
				$file = "<?php\n\n/* self generated translation file, last generated on ".date("d.m.Y H:i:s")." (".time().") */\n";
				@fwrite($handle, $file . "\n\$this->translations = " . var_export($this->translations, true) . ";\n?>");
				@flock($handle,LOCK_UN);
				fclose($handle);
			}
		/*if(!empty($key)){
			if($handle = @fopen($this->path,'a+')){
				fwrite($handle,$key."\n-----/////-----\n".$value."\n#####/////#####\n");
				fclose($handle);
			}
			$this->buffer=$key."\n-----/////-----\n".$value."\n#####/////#####\n";
			$this->parse();
		}*/
	}


}
