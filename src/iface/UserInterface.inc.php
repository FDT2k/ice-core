<?php
namespace \FDT2k\ICE\CORE\iface;


interface UserInterface {

	public function exists($username);
	function get_account_id();
}
