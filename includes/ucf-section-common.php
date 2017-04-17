<?php
/**
 * Place common functions here.
 **/

if ( ! class_exists( 'UCF_Section_Common' ) ) {
	class UCF_Section_Common {

		public static function display_section( $attr ) {
			$output = ucf_section_display( $attr );
			return apply_filters( 'ucf_section_display', $output );
		}
	}
}

/**
* Returns the section HTML to be displayed on the page.
* @author RJ Bruneel
* @since 1.0.0
* @param $attr string | title of the section post type.
* @return String
**/
if ( ! function_exists( 'ucf_section_display' ) ) {
	function ucf_section_display( $attr ) {
		$post = get_page_by_title( $attr['title'], OBJECT, 'ucf_section' );
		return apply_filters( 'the_content', $post->post_content );
	}
}

?>