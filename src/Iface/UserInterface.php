<?php
namespace FDT2k\Noctis\Core\Iface;


interface UserInterface {

	public function exists($username);
	function get_account_id();
	function check_login($user,$password);
	function load_user($id);
}
