<?php

namespace BroDudeProfile\Classes;

use BroDudeProfile;

class ProfileLinkFilter {

	public function __construct() {
		add_filter( 'get_comment_author_url', [ $this, 'comment_url' ], 10, 3 );
	}

	public function comment_url( $url, $id, $comment ) {
		$user = get_user_by( 'email', $comment->comment_author_email );
		if ( $user ) {
			return site_url( "/" . BroDudeProfile::$slug . '/comments/' . $user->ID );
		}

		return $url;
	}

}