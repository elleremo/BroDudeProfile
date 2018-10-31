<?php


namespace BroDudeProfile\Classes;


use BroDudeProfile\Utils\Assets;

class FavoriteAdd {
	use Assets;

	public static $uid = false;
	public $favorite_user_meta_field = 'wpfp_favorites';
	public static $added_html = "
		<div class='single__content-header-favorite-icon added'></div>
        <div class='single__content-header-favorite-text added'  data-text='в избранном' >
        в избранном
        </div>";

	public function __construct() {
		if ( is_user_logged_in() ) {
			self::$uid = get_current_user_id();
			add_action( 'wp_ajax_add_favorite', [ $this, 'action' ] );

			add_filter( 'BroDude__theme-favorite-text', function ( $html, $post_id ) {
				if ( false === $this->post_favorite_in( $post_id ) ) {
					return $html;
				}

				return self::$added_html;
			}, 10, 2 );

			$this->addJs( 'favorite_add', 'footer' );

		} else {
			$this->addJs( 'favorite_add_no_priv', 'footer' );
		}

	}

	public function action() {
		$request = $_REQUEST;

		$request = array_map( 'trim', $request );

		if ( is_numeric( $request['post_id'] ) && $this->post_exist_and_puslish( $request['post_id'] )  ) {
			$maessage = $this->update_user_favorite_meta( strval( $request['post_id'] ) );

			$html = self::$added_html;

			if ( true !== $this->post_favorite_in( $request['post_id'] ) ) {
				$html = str_replace( [ 'в избранном', 'added' ], [ 'в избранное', '' ], $html );
			}

			echo wp_send_json_success( [
				'post_id'     => intval( $request['post_id'] ),
				'result_text' => $maessage['text'],
				'action'      => $maessage['action'],
				'html'        => $html
			] );
		}
		echo wp_send_json_error( [
			'post_id'     => intval( $request['post_id'] ),
			'result_text' => $maessage['text'],
			'action'      => $maessage['action'],
		] );


	}

	public function update_user_favorite_meta( $post_id ) {
		$message = [
			'text'   => '',
			'action' => ''
		];

		$post_id = intval( $post_id );
		$tmp_arr = get_user_meta( self::$uid, $this->favorite_user_meta_field, true );
		$tmp_arr = $this->get_clean_favorite_array( $tmp_arr );

		if ( in_array( $post_id, $tmp_arr ) ) {
			$key = array_search( $post_id, $tmp_arr, true );
			unset( $tmp_arr[ $key ] );
			update_user_meta( self::$uid, $this->favorite_user_meta_field, $tmp_arr );
			$message['action'] = 'delete';
			$message['text']   = 'в избранное';
		} else {
			$tmp_arr[] = $post_id;
			$tmp_arr   = $this->get_clean_favorite_array( $tmp_arr );
			update_user_meta( self::$uid, $this->favorite_user_meta_field, $tmp_arr );
			$message['action'] = 'added';
			$message['text']   = 'в избранном';
		}

		return $message;

	}

	public function post_favorite_in( $post_id ) {
		$meta = get_user_meta( self::$uid, $this->favorite_user_meta_field, true );

		if ( ! is_array( $meta ) ) {
			return false;
		}

		return in_array( $post_id, $meta );
	}

	public function get_clean_favorite_array( $tmp_arr ) {
		if ( is_array( $tmp_arr ) ) {
			$tmp_arr = array_flip( $tmp_arr );
			$tmp_arr = array_unique( $tmp_arr );
			$tmp_arr = array_flip( $tmp_arr );
		}

		return $tmp_arr;
	}


	private function post_exist_and_puslish( $post_is ) {
		global $wpdb;
		$o = $wpdb->get_var( "
			SELECT `ID` FROM 
			`{$wpdb->prefix}posts` WHERE 
			`ID` = '{$post_is}' AND 
			`post_type` IN ('post','advert_post') AND 
			`post_status` = 'publish'"
		);
		if ( ! empty( $o ) ) {
			return true;
		}

		return false;
	}

}