<?php

namespace BroDudeProfile\Classes;


class Favorites {
	public static $favorite_user_meta_field = 'wpfp_favorites';

	public $favorites = false;


	public function __construct( $uid ) {

		$this->favorites = $this->favorites( $uid );

	}

	public function favorites( $user_id ) {
		$field = self::$favorite_user_meta_field;

		$meta = get_user_meta( $user_id, $field, true );
		
		if ( empty( $meta ) ) {
			return false;
		}

		if ( is_array( $meta ) && 0 < count( $meta ) ) {
			return array_unique( array_values( $meta ) );
		}

		return false;
	}

	public function count() {

		$posts = $this->favorites;


		if ( false !== $posts ) {
			return count( $posts );
		}

		return 0;

	}
}