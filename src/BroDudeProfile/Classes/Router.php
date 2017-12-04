<?php

namespace BroDudeProfile\Classes;


class Router {

	public static $slug = 'profile';

	public function __construct() {
		add_action( 'init', [ $this, 'router' ] );

		add_action( "template_redirect", function () {
			get_query_var( 'active_tab' );
		} );
	}

	public function router() {
		$slug = self::$slug;

		add_rewrite_tag( '%active_tab%', '([^&]+)' );
		add_rewrite_rule( "^({$slug})/([^/]*)/?", 'index.php?pagename=$matches[1]&active_tab=$matches[2]', 'top' );
	}

}