<?php

namespace BroDudeProfile\Classes;


use BroDudeProfile;

class Header {
	private $uid = false;
	private $user_data = false;
	public $menu = [];

	public function __construct() {
		$this->uid       = Permission::rewrite_uid();
		$this->user_data = get_userdata( Permission::rewrite_uid() );
		$this->menu      = [
			"comments"  => [
				'anchor' => 'Комментарии',
				'link'   => site_url( BroDudeProfile::$slug . "/comments/" )
			],
			"posts"     => [
				'anchor' => 'Публикации',
				'link'   => site_url( BroDudeProfile::$slug . "/posts/" )
			],
			'favorites' => [
				'anchor' => 'Избранное',
				'link'   => site_url( BroDudeProfile::$slug . "/favorites/" )
			]
		];

		if ( ! Permission::is_my_profile() ) {
			unset( $this->menu['favorites'] );

			$this->menu = array_map( function ( $elem ) {
				$elem['link'] = $elem['link'] .'id-'. get_query_var( 'uid' );

				return $elem;
			}, $this->menu );
		}


		add_action( 'BroDudeProfile__shortcode-content', [ $this, 'content' ], 10 );


	}

	public function content() {
		?>

        <div class="profile__header">

            <div class="profile__header-left">

                <div class="profile__header-left-edit">
					<?php $this->button_edit(); ?>
                </div>

                <div class="profile__header-avatar">
					<?php echo get_avatar( $this->uid,  100 ); ?>
                </div>

            </div>

            <div class="profile__header-right">
                <div class="profile__header-right-top">

                    <div class="profile__header-name-wrap">

                        <div class="profile__header-name">
							<?php echo $this->user_data->display_name; ?>
                        </div>

                        <div class="profile__header-grade">
							<?php echo $this->grade(); ?>
                        </div>

                    </div>

                    <div class="profile__header-register-data">
						<?php echo $this->register_date( 'На сайте с ' ); ?>
                    </div>


                </div>

                <div class="profile__header-right-bottom <?php echo( Permission::is_my_profile() ? 'my-profile' : '' ); ?>">
					<?php $this->menu(); ?>
					<?php $this->button_edit(); ?>
                </div>
            </div>
        </div>
		<?php
	}

	public function button_edit() {
		if ( Permission::is_my_profile() ) {
			$url = site_url( BroDudeProfile::$slug . "/edit/" );
			echo "<a href='{$url}' class='profile__edit-link'>Настройки</a>";
		}
	}

	public function menu() {
		global $wpdb;

		if ( 0 < count( $this->menu ) ):
			?>
            <ul class="header__menu">
				<?php
				foreach ( $this->menu as $key => $val ) :
					$class = ( $key === get_query_var( 'tab_active', 'comments' ) ) ? "active" : "";
					?>
                    <li class="header__menu-item <?php echo $key . " " . $class; ?>">
                        <a class="header__menu-link" href="<?php echo $val['link']; ?>">

                            <span class="text">

							<?php
							if ( "favorites" == $key ) {
								echo "<span class='star'> </span>";
							}
							?>

							<?php echo $val['anchor']; ?>

							<?php
							if ( "posts" == $key ) {
								$count = count_user_posts( $this->user_data->ID, [ 'post', 'advert_post' ], true );

								if ( 0 < $count ) {
									printf( "(%s)", $count );
								}
							} elseif ( "comments" == $key ) {
								$count_comments = $wpdb->get_var(
									"SELECT COUNT(comment_ID) FROM  {$wpdb->comments} WHERE user_id ='{$this->uid}' "
								);
								if ( 0 < $count_comments ) {
									printf( "(%s)", $count_comments );
								}
							} elseif ( "favorites" == $key ) {

								$favorites       = new Favorites( get_current_user_id() );
								$favorites_count = $favorites->count();
								if ( 0 < $favorites_count ) {
									printf( "(%s)", $favorites_count );
								}
							}
							?>

                            </span>

                        </a>
                    </li>
				<?php endforeach; ?>
            </ul>
		<?php
		endif;
	}

	public function register_date( $prefix = '', $suffix = '' ) {

		if ( user_can( $this->uid, 'publish_pages' ) ) {
			return "{$prefix} незапамятных времен {$suffix}";
		}

		return $prefix . mysql2date( 'd F Y ', $this->user_data->user_registered ) . $suffix;

	}

	private function role_is() {
		if ( user_can( $this->uid, 'edit_posts' ) and ! user_can( $this->uid, 'update_core' ) ) {
			return 'author';
		}

		return 'user';
	}

	public function grade() {
		$text = 'Читатель';
		if ( $this->role_is() === 'author' ) {
			$text = 'Автор';
		}

		return $text;
	}
}