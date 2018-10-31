<?php

namespace BroDudeProfile\Classes;

use BroDudeProfile;
use BroDudeProfile\Utils\ActivateShortcode;
use BroDudeProfile\Utils\Assets;

class Shortcode extends ActivateShortcode {
	use Assets;

	protected $js = false;
	protected $css = true;

	function init( $tag, $attrs ) {
		add_action( "template_redirect", function () use ( $tag ) {
			global $wp_query;
			if ( is_singular() && is_object( $wp_query ) && has_shortcode( $wp_query->post->post_content, $tag ) ) {
				$this->addCss( $tag, 'header' );
			}

		} );

	}

	function base( $attrs, $content, $tag ) {

		ob_start();
		?>

		<?php do_action( 'BroDudeProfile__shortcode-content' ); ?>

		<?php
		$res = ob_get_contents();
		ob_clean();


		return $res;
	}

}