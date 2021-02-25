<?php
/**
 * Registers the section shortcode
 * @author RJ Bruneel
 * @since 1.0.0
 **/

if ( ! class_exists( 'UCF_Section_Shortcode' ) ) {
	class UCF_Section_Shortcode {
		public static function shortcode( $atts ) {
			$atts = shortcode_atts( array(
				'slug'            => null,
				'id'              => null,
				'class'           => '',
				'title'           => '',
				'section_id'      => '',
				'random_from_tag' => ''
			), $atts );

			if ( isset( $atts['slug'] ) || isset( $atts['id'] ) || !empty( $atts['random_from_tag'] ) ) {
				return UCF_Section_Common::display_section( $atts );
			}

			return '';
		}
	}
	add_shortcode( 'ucf-section', array( 'UCF_Section_Shortcode', 'shortcode' ) );
}
?>
