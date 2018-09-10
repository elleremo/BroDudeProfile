<?php

namespace BroDudeProfile\Classes;

class Content {
	private $uid = false;
	private $user_data = false;
	public $data = [];
	public $title = [
		'comments'  => 'Комментарии к постам:',
		'favorites' => 'Посты в избранном',
		'posts'     => 'Статьи автора',
		'edit'      => 'Настройки профиля'

	];
	public $allowed_tags = [
		'ul',
		'ol',
		'li',
		'em',
		'i',
		'p',
		'b',
		'strong',
	];

	private $per_page = 10;
	private $offset = 1;

	public function __construct() {
		$this->uid       = Permission::rewrite_uid();
		$this->user_data = get_userdata( Permission::rewrite_uid() );

		$this->offset = ( empty( get_query_var( 'paged' ) ) ? 1 : get_query_var( 'paged' ) );
		if ( Permission::is_my_profile() ) {
			$this->title['posts'] = "Мои статьи";
		}
		add_action( 'BroDudeProfile__shortcode-content', [ $this, 'content' ], 12 );

	}

	public function content() {
		?>
        <div class="profile__content--wrapper">
            <div class="profile__content">

                <div class="profile__content-title">
					<?php
					if ( array_key_exists( get_query_var( 'tab_active', 'comments' ), $this->title ) ):
						echo $this->title[ get_query_var( 'tab_active', 'comments' ) ];
					endif; ?>
                </div>

                <div class="profile__content-items">
					<?php $this->row( get_query_var( 'tab_active', 'comments' ) ); ?>
                </div>

            </div>
        </div>
		<?php
		$this->user_posts();

	}

	public function max_num_pages( $type ) {
		global $wpdb;
		$type = str_replace( 'user_', '', $type );

		if ( 'posts' === $type ) {
			return $wpdb->get_var( "
                SELECT count('ID') 
                FROM `{$wpdb->prefix}posts`
                WHERE `post_author` = {$this->uid} 
                AND `post_type` IN( 'post', 'advert_post') 
                AND `post_status` = 'publish'  
		    " );

		} elseif ( 'favorites' === $type ) {
			$favorites = new Favorites( $this->uid );
			if ( false == $favorites->favorites ) {
				return false;
			}
			$favorites_str = implode( ',', $favorites->favorites );

			return $wpdb->get_var( "
                SELECT count('ID')
                FROM `{$wpdb->prefix}posts`
                WHERE `ID` IN({$favorites_str})
                AND `post_type` IN( 'post', 'advert_post')
                AND `post_status` = 'publish'
		    " );

		}

	}

	public function row( $type ) {
		$method = "user_" . $type;
		if ( "edit" === $type ) {
			do_action( 'BroDudeProfile__user-settings', $this->uid );

		} else if ( method_exists( $this, $method ) ) {


			$max_num_pages = $this->max_num_pages( $method );
			if ( ! $max_num_pages ) {
				$max_num_pages = 0;
			}
			set_query_var( 'max_num_pages', $max_num_pages );
			set_query_var( 'posts_per_page', $this->per_page );
			$GLOBALS['wp_query']->max_num_pages = $max_num_pages;

			$posts = $this->$method();

			if ( false !== $posts ) {
				array_map(
					function ( $post ) {
						$this->row_html( $post );
					},
					$posts
				);
			} else {
				$this->empty_content();
			}

		}
	}

	public function empty_content() {
		?>
        <div class="profile__content-empty">
            Пока что ничего нет
        </div>
		<?php
	}

	public function row_html( $post ) {


		if ( 'WP_Comment' === get_class( $post ) ) {
			$p_post = get_post( $post->comment_post_ID );
			$title  = get_the_title( $p_post );
			$date   = $this->show_date( $post->comment_ID, get_class( $post ) );
			$text   = get_comment_text( $post->comment_ID );
			$link   = get_the_permalink( $p_post->ID );
		} else {
			$title = get_the_title( $post );
			$text  = get_the_excerpt( $post );
			$date  = $this->show_date( $post->ID, get_class( $post ) );
			$link  = get_the_permalink( $post->ID );
		}

		?>
        <a href="<?php echo $link; ?>" target="_blank" class="profile__content-item">
            <div class="profile__content-item-date">
				<?php echo $date; ?>
            </div>
            <div class="profile__content-item-content">

                <div class="profile__content-item-title">
					<?php echo $title; ?>
                </div>

                <div class="profile__content-item-text">
					<?php echo wp_kses( $text, $this->allowed_tags ); ?>
                </div>
            </div>
        </a>
		<?php
	}

	private function show_date( $post_id, $type ) {
		$date = '';

		if ( 'WP_Comment' === $type ) {
			$date .= get_comment_date( 'd.m.Y', $post_id );
			$date .= "<br>";
			$date .= get_comment_date( 'h:i', $post_id );
		} else {
			$date .= get_the_date( 'd.m.Y', $post_id );
			$date .= "<br>";
			$date .= get_the_date( 'h:i', $post_id );
		}


		return $date;
	}

	public function user_posts() {

		$posts = get_posts(
			[
				'author'         => $this->uid,
				'posts_per_page' => $this->per_page,
				'paged'          => $this->offset,
				'post_type'      => [ 'advert_post', 'post' ],
				'post_status'    => 'publish'
			]
		);

		if ( 1 > count( $posts ) ) {
			return false;
		}

		return $posts;
	}

	public function user_comments() {
		$coments = get_comments(
			[
				'user_id'  => $this->uid,
				'status'   => 'approve',
				'order_by' => 'comment_date',
				'number'   => $this->per_page,
				'paged'    => $this->offset,
			]
		);

		if ( 1 > count( $coments ) ) {
			return false;
		}

		return $coments;
	}

	public function user_favorites() {
		$favorites = new Favorites( $this->uid );

		if ( ! $favorites->favorites ) {
			return false;
		}

		$posts = get_posts(
			[
				'post__in'       => $favorites->favorites,
				'posts_per_page' => $this->per_page,
				'paged'          => $this->offset,
				'post_type'      => [ 'advert_post', 'post' ],
				'post_status'    => 'publish'

			]
		);

		if ( 1 > count( $posts ) ) {
			return false;
		}

		return $posts;
	}

}