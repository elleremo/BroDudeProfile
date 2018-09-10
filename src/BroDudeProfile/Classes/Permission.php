<?php

namespace BroDudeProfile\Classes;

use BroDudeProfile;

class Permission {


	public static function rewrite_uid() {
		$uid = get_query_var( 'uid', false );

		if ( false === $uid ) {
			return BroDudeProfile::$uid;
		}

		if ( false === get_user_by( 'ID', $uid ) ) {
			return false;
		}

		return (int) $uid;
	}

	public static function is_my_profile() {

		if( !empty(get_query_var('uid'))   ){
			return false;
		}


		if ( false !== BroDudeProfile::$uid ) {
			return true;
		}


		return false;
	}

}