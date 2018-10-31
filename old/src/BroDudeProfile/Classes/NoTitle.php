<?php

namespace BroDudeProfile\Classes;


use BroDudeProfile;

class NoTitle {

	public function __construct() {

		add_action( 'template_redirect', [ $this, 'payload' ], 40 );

	}


	public function payload() {
		if ( get_query_var( 'pagename' ) === BroDudeProfile::$slug ) {
			set_query_var( 'no-title', true );
		}
	}

}