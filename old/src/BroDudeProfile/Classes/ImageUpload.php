<?php

namespace BroDudeProfile\Classes;


class ImageUpload {


	public function handle( $key, $post_data = [] ) {
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		return media_handle_upload( $key, 0, $post_data, [ 'test_form' => false ] );

	}

}