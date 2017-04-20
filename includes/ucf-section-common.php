<?php
/**
 * Place common functions here.
 **/

if ( ! class_exists( 'UCF_Section_Common' ) ) {
	class UCF_Section_Common {

		public static function display_section( $attr ) {
			$section = self::get_section_by_slug( $attr['slug'] );
			
			if ( $section ) {
				$output = apply_filters( 'the_content', $section->post_content );
				return apply_filters( 'ucf_section_display', $output, $section->ID );
			}

			return '';
		}

		/**
		 * Returns a section based on slug.
		 **/
		public static function get_section_by_slug( $slug ) {
			$args = array(
				'post_type'   => 'ucf_section',
				'post_name'   => $slug,
				'numberposts' => 1,
			);

			$posts = get_posts( $args );

			if ( count( $posts ) > 0 ) {
				return $posts[0];
			}

			return null;
		}
	}
}

?>
