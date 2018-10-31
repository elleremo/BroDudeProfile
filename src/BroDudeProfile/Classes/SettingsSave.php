<?php

namespace BroDudeProfile\Classes;


use BroDudeProfile\Utils\Assets;

class SettingsSave {
	use Assets;

	public static $action_name = 'bd_profile';
	public static $ajax_url;
	public static $user_avatar_meta_field = 'my_bro_avatar_url';

	public function __construct() {

		self::$ajax_url = add_query_arg(
			[
				'action' => self::$action_name
			],
			admin_url( '/admin-ajax.php' )
		);

		$this->add_js_css();

	}

	public function add_js_css() {
	    if(isset($_GET['edit'])){
	        set_query_var('tab_active', 'edit');
        }

		if ( 'edit' === get_query_var( 'tab_active', false ) ) {
			$this->addJs( "ProfileSettingsScript" );
		}
	}

	public static function ajax_init() {
		$action = SettingsSave::$action_name;
		add_action( "wp_ajax_{$action}", [ __CLASS__, 'ajax_action' ] );
	}

	public static function ajax_action() {
		$post = $_REQUEST;
		$file = $_FILES;

		unset( $post['action'] );
		unset( $post['user_login'] );

		if ( wp_verify_nonce( $post['_wpnonce'], SettingsSave::$action_name ) ) {


			if ( is_array( $file ) && array_key_exists( 'avatar', $file ) ) {

				if ( ! empty( $file['avatar']['tmp_name'] ) ) {
					$uploader = new ImageUpload();
					$image_id = $uploader->handle(
						'avatar',
						[
							'post_excerpt' => 'uid_' . get_current_user_id()
						]
					);


					if ( is_wp_error( $image_id ) ) {
						wp_send_json_error( [
							'post' => $post,
							'html' => self::alert( $image_id->get_error_message(), 'error' )
						] );
					}

					$image = wp_get_attachment_image_src( $image_id, 'full' );

					update_user_meta(
						get_current_user_id(),
						self::$user_avatar_meta_field,
						[
							'full_url'  => $image[0],
							'name'      => basename( $image[0] ),
							'avatar_id' => $image_id,
							'type'      => 'new'
						]
					);

				}
			}

			if ( array_key_exists( 'reed_progress', $post ) ) {
				update_user_meta(
					get_current_user_id(),
					'reed_progress',
					true
				);
			} else {
				delete_user_meta(
					get_current_user_id(),
					'reed_progress'
				);
			}

			if ( array_key_exists( 'wooden_bg', $post ) ) {
				update_user_meta(
					get_current_user_id(),
					'wooden_bg',
					true
				);
			} else {
				delete_user_meta(
					get_current_user_id(),
					'wooden_bg'
				);
			}



			$out = self::update_user_meta( $post );

			if ( is_wp_error( $out ) ) {
				wp_send_json_error( [
					'post' => $post,
					'html' => self::alert( $out->get_error_message(), 'error' )
				] );

			} else {
				wp_send_json_success( [
					'post' => $post,
					'html' => self::alert( "Настройки сохранены" )
				] );

			}

		}
	}

	public static function alert( $message, $type = 'success' ) {

		return "<div class='profile__alert-wrap {$type}'><div class='profile__alert {$type}'> {$message} </div></div>";
	}

	public static function update_user_meta( $array ) {

		$array = array_filter(
			$array,
			function ( $v, $k ) {
				if ( empty( $v ) ) {
					return false;
				}
				if ( in_array( $k, [ self::$user_avatar_meta_field, 'user_login', 'first_name', 'last_name', 'user_pas' ] ) ) {
					return true;
				}

				return false;
			},
			ARRAY_FILTER_USE_BOTH
		);

		$array = array_map( 'trim', $array );

		$array['ID'] = get_current_user_id();


		return wp_update_user( $array );

	}

}