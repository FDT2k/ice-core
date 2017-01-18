<?php
namespace ICE\core\iface;


interface UserInterface {

	public function exists($username);
	function get_account_id();
}
