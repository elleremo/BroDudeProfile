<?php

namespace BroDudeProfile\Classes;

use BroDudeProfile;

class BodyClass {

	public function __construct() {
		add_filter( 'body_class', function ( $classes ) {

			if ( get_query_var( 'pagename' ) === BroDudeProfile::$slug ) {
				unset($classes[array_search('singular', $classes)]);

				$classes[] = 'no-content-change';
				$classes[] = 'profile-page';

				if ( get_query_var( 'tab_active', 'comments' ) ) {
					$classes[] = 'profile-tab-' . get_query_var( 'tab_active', 'comments' );
				}

			}

			return $classes;
		} );


	}

}