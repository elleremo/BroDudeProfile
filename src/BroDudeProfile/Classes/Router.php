<?php

namespace BroDudeProfile\Classes;


use BroDudeProfile;

class Router {

	private $slug;

	public function __construct() {
		$this->slug = BroDudeProfile::$slug;

		add_action( 'init', [ $this, 'router_two' ] );

		add_action( "template_redirect", function () {
			$this->argsProcessing();
			if ( 'profile' == get_query_var( 'pagename' ) ) {
				$this->redirect();
			}
		}, 1 );

	}

	public function redirect() {
		$redirect = new Redirect();
		$redirect->redirect_profile();
		$redirect->not_exist_user();
		$redirect->self_profile();
	}

	public function router_two() {

		add_rewrite_tag( '%profile_string%', '([^&]+)' );

		add_rewrite_rule(
			"^({$this->slug})(.+)",
			'index.php?pagename=$matches[1]&profile_string=$matches[2]',
			'top'
		);
	}

	public function router() {

		add_rewrite_tag( '%tab_active%', '([^&]+)' );
		add_rewrite_tag( '%uid%', '([^&]+)' );

		add_rewrite_rule(
			"^({$this->slug})\/([A-z]*)\/([0-9]+)\/{0,}($|\/page\/?([0-9]{1,})\/?$)",
			'index.php?pagename=$matches[1]&tab_active=$matches[2]&uid=$matches[3]&paged=$matches[5]',
			'top'
		);

		if ( is_user_logged_in() ) {
			add_rewrite_rule(
				"^({$this->slug})\/([A-z]*)\/{0,}($|\/page\/?([0-9]{1,})\/?$)",
				'index.php?pagename=$matches[1]&tab_active=$matches[2]&paged=$matches[4]',
				'top'
			);
		}
	}

	public function argsProcessing() {

		if ( 'profile' == get_query_var( 'pagename' ) ) {

			$string = ltrim( get_query_var( 'profile_string' ), '/' );

			if ( is_user_logged_in() && empty( $string ) ) {
				$string = 'posts';
				set_query_var( 'tab_active', '/posts' );
			}

			$array = explode( '/', $string );

			if ( false !== strripos( $string, 'id-' ) ) {
				preg_match( "#\/id-([0-9]{1,})#", $string, $m );
				set_query_var( 'uid', intval( $m[1] ) );
			} else {
				set_query_var( 'uid', false );
			}
			if ( ! empty( $array ) ) {
				set_query_var( 'tab_active', $array[0] );
			}
		}

	}

}