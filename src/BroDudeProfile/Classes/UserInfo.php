<?php

namespace BroDudeProfile\Classes;


class UserInfo {

	public $uid = false;
	public $info = false;

	public function __construct() {
		$this->check_user_type();
	}

	public function check_user_type() {
		if ( is_user_logged_in() ) {
			$this->info = wp_get_current_user();
			$this->uid  = $this->info->ID;
		}
	}

}