<?php

namespace BroDudeProfile\Classes;

use BroDudeProfile\Utils\ActivateShortcode;
use BroDudeProfile\Utils\Assets;

class Shortcode extends ActivateShortcode {
	use Assets;

	protected $js = false;
	protected $css = true;

	function init( $tag, $attrs ) {
		add_action( "template_redirect", function () use ( $tag ) {
			global $wp_query;
			if ( is_singular() && has_shortcode( $wp_query->post->post_content, $tag ) ) {
				$this->addCss( $tag );
			}
		} );
	}

	function base( $attrs, $content, $tag ) {
		ob_start();
		?>

		<?php
		$res = ob_get_contents();
		ob_clean();
		return $res;
	}

}