<?php
namespace ICE\core\service;
use \ICE\Env as Env;

class LDAPAuthenticationService extends AuthenticationService {

	public function	__construct(){
		$this->setLogger(Env::getLogger('ldap'));
		$this->setServer(Env::getConfig('ldap')->get('ldapServer'));
		$this->setPort(Env::getConfig('ldap')->get('ldapPort'));
		$this->setBaseDN(Env::getConfig('ldap')->get('baseDN'));
		$this->setProtocol(Env::getConfig('ldap')->get('ldapProtocol'));

		$uri = $this->getProtocol()."://".$this->getServer().":".$this->getPort();
		$this->getLogger()->log("Connecting to ldap ".$uri);
		$this->setHandle(ldap_connect($uri));

		if(!$this->getHandle()){
		//	var_dump($this->getHandle());
			$this->getLogger()->log("Connecting to ldap FAILED:".$uri);
			$this->setError("Cannot connect to ldap server",4001);
		}

		if($model = Env::getConfig("auth")->get('user_model')){
			$this->model = new $model();
			if(!$this->model instanceOf \ICE\core\iface\UserInterface){
				throw new \ICE\core\Exception("error, assigned model ".$model." should implements \ICE\core\iface\UserInterface",0,"");
			}
		}

		ldap_set_option($this->getHandle(), LDAP_OPT_PROTOCOL_VERSION, 3) ;

	}

	public function authenticate($login,$password,$opts=array()){
		$str=$this->getBaseDN();
		$str = "cn=".$login.",".$str;

		$result = @ldap_bind($this->getHandle(), $str, $password);
		if($result){
			$this->setUserDN($str);
			if($this->model->exists(array('email'=>$login))){
				$user = $this->model->select(array('email'=>$login));
				$user_id = $user[$this->model->getEntity()->onePKName()];
			}else{
				if(!$user_id = $this->model->insert(array('email'=>$login))){
					throw new \ICE\core\Exception("couldnt create reference user in database");
				}


				//var_dump($this->model->getLastQuery());
			}
		//	var_dump($user_id);
			Env::getUserSessionService()->create_session(array('uid'=>$user_id,'login'=>$login));
		}

		return $result;
	}

	public function register($login,$password){

	}

	public function is_logged(){
		return Env::getUserSessionService()->getToken()!="";
	}

	public function fetch_groups($groupdn){
		$ad = $this->getHandle();
		$groupdn = $groupdn.",".$this->getBaseDN();
		$userdn = $this->getUserDN();
		$attributes = array('memberof');
		$result = ldap_read($ad, $userdn, '(objectclass=*)');

		if ($result === FALSE) { return FALSE; };
		$entries = ldap_get_entries($ad, $result);
var_dump($entries);
		if ($entries['count'] <= 0) { return FALSE; };
		if (empty($entries[0]['memberof'])) { return FALSE; } else {
			 for ($i = 0; $i < $entries[0]['memberof']['count']; $i++) {
					 if ($entries[0]['memberof'][$i] == $groupdn) { return TRUE; }
					 elseif (checkGroupEx($ad, $entries[0]['memberof'][$i], $groupdn)) { return TRUE; };
			 };
		};
		return FALSE;
	}

}
