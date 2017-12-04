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
use BroDudeProfile\Classes\Router;

class BroDudeProfile extends Wrap {
	public $version = '1.0.1';
	public static $textdomine;

	function __construct() {
		self::$textdomine = $this->setTextdomain();

//		new \BroDudeProfile\Classes\AjaxOut2();
//		new \BroDudeProfile\Classes\AjaxOut( 'boilerplate-ajax' );
//		new \BroDudeProfile\Classes\MyClass( $this );
		new \BroDudeProfile\Classes\Shortcode(
			'my_bro_profile',
			[
				'title'       => 'Boilerplate title',
				'description' => 'Boilerplate description'
			]
		);

		new Router();

	}

}

function BroDudeProfile__init() {
	new BroDudeProfile();
}

add_action( 'plugins_loaded', 'BroDudeProfile__init', 30 );
