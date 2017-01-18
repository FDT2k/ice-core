<?php
namespace FDT2k\ICE\CORE;

class Session implements \SessionHandlerInterface{
	public  $session;
	public  function init(){
		$this->resetPrefix();

	}

	public  function resetPrefix(){
		$this->session = &$_SESSION;
	}

	public  function usePrefix($prefix){
		$this->session= &$this->session[$prefix];
	}

	public  function clear(){
		$this->session= null;
	}

	public function set($name,$value){
		$this->session[$name] = $value;
	}

	public function get($name){
		return $this->session[$name];
	}

	public function getSessionID(){
		return session_id();
	}



	public function open($savePath, $sessionName)
    {
    	$this->db = Env::getDatabase();
        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }

        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
    	$sql = "select datas from php_sessions where sess_id=".$this->db->convertString($id);
    	//var_dump($sql);
    	$rs = $this->db->executeQuery($sql);
    	$result = false;
    	$this->exists = false;
    	if($rs){
    		$result = $rs->fetchAssoc();
    		if($result){
    			$result = $result['datas'];
    			$this->exists = true;
    		}
    	}

    	return $result;
     //   return (string)@file_get_contents("$this->savePath/sess_$id");
    }

    public function write($id, $data)
    {
    	if(!$this->exists){
    		$sql = "insert INTO php_sessions (sess_id,datas,created_on,last_used_on,ip) VALUES(".$this->db->convertString($id).", ".$this->db->convertString($data).", '".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."')";
    		//var_dump($sql);
    	}else{
    		$sql = "update php_sessions set datas=".$this->db->convertString($data).", last_used_on='".date('Y-m-d H:i:s')."' where sess_id=".$this->db->convertString($id);
    	}
    	return $this->db->executeUpdate($sql);
    }

    public function destroy($id)
    {
    	return $this->db->executeUpdate("delete from php_sessions where id = ".$this->db->convertString($id));

    }

    public function gc($maxlifetime)
    {
    	//var_dump($maxlifetime);
    	$date = date('Y-m-d H:i:s', strtotime("NOW -$maxlifetime seconds"));
    	$this->db->executeUpdate("delete from php_sessions where last_used_on < ".$this->db->convertString($date));
    	return true;
    }
}
