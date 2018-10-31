<?php
/*
Plugin Name: BroDude Profile
Plugin URI: http://alkoweb.ru
Author: Petrozavodsky
Author URI: http://alkoweb.ru
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( "includes/Autoloader.php" );

use BroDudeProfile\Autoloader;

new Autoloader( __FILE__, 'BroDudeProfile' );


use BroDudeProfile\Base\Wrap;
use BroDudeProfile\Classes\BodyClass;
use BroDudeProfile\Classes\Content;
use BroDudeProfile\Classes\DefaultAvatar;
use BroDudeProfile\Classes\FavoriteAdd;
use BroDudeProfile\Classes\Header;
use BroDudeProfile\Classes\NoTitle;
use BroDudeProfile\Classes\ProfileLinkFilter;
use BroDudeProfile\Classes\RemoveWpautop;
use BroDudeProfile\Classes\Router;
use BroDudeProfile\Classes\Settings;
use BroDudeProfile\Classes\SettingsSave;
use BroDudeProfile\Classes\Shortcode;
use BroDudeProfile\Classes\TitleMetaTag;
use BroDudeProfile\Classes\UserInfo;

class BroDudeProfile extends Wrap {
	public $version = '1.1.0';
	public static $textdomine;
	public static $slug = 'profile';
	public static $uid = false;
	public static $userinfo = false;

	function __construct() {
		self::$textdomine = $this->setTextdomain();
		$UserInfo         = new UserInfo();

		self::$uid      = $UserInfo->uid;
		self::$userinfo = $UserInfo->info;

		new Router();

		new FavoriteAdd();

		new RemoveWpautop();

		new Shortcode(
			'my_bro_profile',
			[
				'title' => 'Title',
			]
		);

		new NoTitle();


		add_action( 'template_redirect', function () {
			new Header();
			new Content();
			$settings = new Settings( 'BroDudeProfile__user-settings' );
			new SettingsSave();

			add_action( 'template_redirect', function () use ( $settings ) {
				$settings->add_js_css();
				new BodyClass();
			}, 90 );

		} );

		SettingsSave::ajax_init() ;

		new DefaultAvatar();

		new ProfileLinkFilter();


	}


	public static function flush_rule() {
		flush_rewrite_rules();
	}

}

register_deactivation_hook( __FILE__, [ 'BroDudeProfile', 'flush_rule' ] );
register_activation_hook( __FILE__, [ 'BroDudeProfile', 'flush_rule' ] );


function BroDudeProfile__init() {
	new BroDudeProfile();
}

add_action( 'plugins_loaded', 'BroDudeProfile__init', 21 );
