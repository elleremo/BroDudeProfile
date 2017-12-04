<?php

namespace BroDudeProfile\Classes;


class Redirect {


	public static function redirect_profile() {
		$login_page  = home_url( '/login/' );
		$page_viewed = basename( $_SERVER['REQUEST_URI'] );

		if ( $page_viewed == "profile" && $_SERVER['REQUEST_METHOD'] == 'GET' ) {
			if ( ! is_user_logged_in() ) {
				wp_redirect( $login_page, 307 );
				exit;
			}
		}
	}

}