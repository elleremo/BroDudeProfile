<?php

namespace BroDudeProfile\Classes;

use BroDudeProfile;

class Redirect {

	private $slug = false;

	/**
	 * Redirect constructor.
	 */
	public function __construct() {
		$this->slug = BroDudeProfile::$slug;
	}

	public function self_profile() {

		if ( get_query_var( 'name' ) === $this->slug ) {
			if ( BroDudeProfile::$uid === Permission::rewrite_uid() ) {
				if ( false !== get_query_var( 'uid', false ) ) {
					wp_safe_redirect(
						site_url(
							get_query_var( 'pagename' ) . "/" . get_query_var( 'tab_active', '' )
						)
					);
					die;
				}
			}
		}
	}

	public function not_exist_user() {
		if ( get_query_var( 'name' ) === $this->slug && false === Permission::rewrite_uid() ) {
			wp_safe_redirect( site_url( '/404' ), 302 );
			die;
		}

	}

	public function redirect_profile() {
		if ( false === get_query_var( 'uid' ) ) {
			if ( get_query_var( 'name' ) === $this->slug && ! is_user_logged_in() ) {
				wp_safe_redirect(
					add_query_arg(
						[
							'redirect_to' => urlencode( site_url( "/{$this->slug}" ) )
						],
						site_url( '/wp-login.php' )
					)
				);
				die;
			}
		}
	}

}