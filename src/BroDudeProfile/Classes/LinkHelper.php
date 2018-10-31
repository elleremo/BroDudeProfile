<?php

namespace BroDudeProfile\Classes;


class LinkHelper {

	public function __construct() {
		 add_filter( 'BroDude__theme-profile-link', [ $this, 'helper' ], 10, 2 );
	}

	public function helper( $link, $uid ) {

		return site_url( "/profile/posts/id-{$uid}/" );
	}

}