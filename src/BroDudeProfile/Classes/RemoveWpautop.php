<?php

namespace BroDudeProfile\Classes;


use BroDudeProfile;

class RemoveWpautop {


	public function __construct() {

		add_action(
			'template_redirect',
			function () {

				if ( get_query_var( 'pagename' ) === BroDudeProfile::$slug ) {
					remove_action( 'the_content', "wpautop", 50 );
				}
			},
			12
		);

	}

}