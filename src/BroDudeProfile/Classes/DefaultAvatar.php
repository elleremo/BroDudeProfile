<?php

namespace BroDudeProfile\Classes;


use BroDudeProfile\Utils\Assets;

class DefaultAvatar {
	use Assets;

	public static $avatar_size = 'image_50x50';
	public static $avatar_size_full = 'image_100x100';
	public static $old_avatars_url;
	public static $old_avatars_dir;
	public static $user_avatar_meta_field = 'my_bro_avatar_url';

	public function __construct() {
		self::set_old_avatar_patch();
		add_filter( 'avatar_defaults', [ $this, 'avatar_default' ] );
		add_filter( 'get_avatar', [ $this, 'user_exist_avatar' ], 30, 6 );
	}

	public function add_image_size() {
		add_image_size( self::$avatar_size, 50, 50, [ 'center', 'center' ] );
		add_image_size( self::$avatar_size_full, 50, 50, [ 'center', 'center' ] );
	}

	public function avatar_default( $avatar_defaults ) {
		$avatar                     = $this->url . '/public/images/avatar.jpg';
		$avatar_defaults[ $avatar ] = "Для неимущих";

		return $avatar_defaults;
	}


	public static function set_old_avatar_patch() {
		$upload_dir            = wp_upload_dir();
		self::$old_avatars_url = $upload_dir['baseurl'] . '/avatars/';
		self::$old_avatars_dir = $upload_dir['basedir'] . '/avatars/';
	}

	public function user_exist_avatar( $avatar, $id_or_email, $size, $default, $alt, $args ) {

		if ( $id_or_email instanceof \WP_Comment ) {
			$id_or_email = $id_or_email->comment_author_email;
		}

		if ( is_email( $id_or_email ) ) {
			$user_data = get_user_by( 'email', $id_or_email );

			if ( false == $user_data ) {
				return $avatar;
			}

			$id_or_email = $user_data->ID;

		}

		$user_id = intval( $id_or_email );
		$url     = get_user_meta( $user_id, self::$user_avatar_meta_field, true );


		if ( is_array( $url ) && array_key_exists( 'avatar_id', $url ) && array_key_exists( 'full_url', $url ) ) {

			$avatar_size = 'image_50x50';

			if ( 100 == $size ) {
				$avatar_size = 'image_100x100';
			}

			$image_data = wp_get_attachment_image_src( $url['avatar_id'], $avatar_size );

			return sprintf(
				"<img alt='%s' src='%s' srcset='%s' sizes='%s' class='%s' height='%d' width='%d' %s />",
				esc_attr( $args['alt'] ),
				esc_url( array_shift( $image_data ) ),
				wp_get_attachment_image_srcset( $url['avatar_id'], $avatar_size ),
				wp_get_attachment_image_sizes( $url['avatar_id'], $avatar_size ),
				'avatar avatar-50 photo author-avatar-img',
				(int) $args['height'],
				(int) $args['width'],
				$args['extra_attr']
			);

		}

		if ( empty( $url ) || false == $url ) {
			$exist_file_name = $this->user_avatar_avatar_exists( $user_id );

			if ( false !== $exist_file_name ) {
				$url = self::$old_avatars_url . $user_id . "/" . $exist_file_name;
			}

		} else if ( is_array( $url ) && array_key_exists( 'full_url', $url ) ) {
			$url = $url['full_url'];
		}

		if ( ! empty( $url ) ) {
			return sprintf(
				"<img alt='%s' src='%s'  class='%s' height='%d' width='%d' %s/>",
				esc_attr( $args['alt'] ),
				esc_url( $url ),
//				esc_url( $url2x ) . ' 2x',
				'avatar avatar-50 photo author-avatar-img',
				(int) $args['height'],
				(int) $args['width'],
				$args['extra_attr']
			);
		}


		return $avatar;
	}


	public static function user_avatar_avatar_exists( $id ) {

		$out               = false;
		$avatar_folder_dir = self::$old_avatars_dir . "{$id}";
		if ( is_dir( $avatar_folder_dir ) && $av_dir = opendir( $avatar_folder_dir ) ) {
			$avatar_files = array();
			while ( false !== ( $avatar_file = readdir( $av_dir ) ) ) {
				if ( 2 < strlen( $avatar_file ) ) {
					$avatar_files[] = $avatar_file;
				}
			}
			if ( 0 < count( $avatar_files ) ) {
				if ( is_array( $avatar_files ) ):
					foreach ( $avatar_files as $key => $value ) {
						if ( strpos( $value, "-bpfull" ) ) {
							$out = $value;
							apply_filters( 'my_bro_avatar_find_in_dir', $out, $id, 'old' );
						}
					}
				endif;
			}
			closedir( $av_dir );
		}

		return $out;
	}

}