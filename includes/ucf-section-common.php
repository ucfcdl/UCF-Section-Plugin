<?php
/**
 * Place common functions here.
 **/

if ( ! class_exists( 'UCF_Section_Common' ) ) {
	class UCF_Section_Common {

		/**
		 * Displays the output of the section.
		 *
		 * @author R.J. Bruneel
		 * @since 1.0.0
		 *
		 * @param $attr Array | An array of attributes.
		 *
		 * @return string | The output of the section content.
		 **/
		public static function display_section( $atts, $content ) {
			$retval = '';
			$class = '';
			$title = '';
			$section_id = '';


			if ( isset( $atts['slug'] ) ) {
				$post_object = self::get_section_by_slug( $atts['slug'] );
				$content = ( $post_object ) ? $post_object->post_content : '';
			}

			if ( isset( $atts['id'] ) ) {
				$post_object = get_post( $atts['id'] );
				$content = ( $post_object ) ? $post_object->post_content : '';
			}

			if  ( isset( $atts['class'] ) ) {
				$class = $atts['class'];
			}

			if ( isset( $atts['title'] ) ) {
				$title = $atts['title'];
			}

			if ( isset( $atts['section_id'] ) ) {
				$section_id = $atts['section_id'];
			}

			if ( $content ) {

				$before = self::ucf_section_display_before( $content, $class, $title, $section_id );
				if ( has_filter( 'ucf_section_display_before' ) ) {
					$before = apply_filters( 'ucf_section_display_before', $before, $content, $class, $title, $section_id );
				}

				$content = self::ucf_section_display( $content, $class, $title, $section_id );
				if ( has_filter( 'ucf_section_display' ) ) {
					$content = apply_filters( 'ucf_section_display', $content, $class, $title, $section_id );
				}

				$after = self::ucf_section_display_after( $content );
				if ( has_filter( 'ucf_section_display_after' ) ) {
					$after = apply_filters( 'ucf_section_display_after', $after, $content );
				}

				$retval = $before . $content . $after;
			}

			return $retval;
		}

		/**
		 * Prepends the section content with a section tag.
		 * Use the `ucf_section_display_before` filter
		 * hook to override or modify this output.
		 *
		 * @author Jim Barnes
		 * @since 1.0.0
		 *
		 * @param $section WP_Post object | The section
		 * @param $class string | The string of css classes
		 * @param $title string | The title to display in the section menu
		 * @param $section_id string | the id to assign to the section
		 *
		 * @return string | The html to be appended to output.
		 **/
		public static function ucf_section_display_before( $content, $class, $title, $section_id ) {
			$class = ! empty( $class ) ? ' class="' . $class . '"' : '';
			$title = ! empty( $title ) ? ' data-section-link-title="' . $title . '"' : '';
			$id = ! empty( $section_id ) ? ' id="' . $section_id . '"' : '';

			ob_start();
		?>
			<section<?php echo $id; ?><?php echo $class; ?>>
		<?php
			return ob_get_clean();
		}

		/**
		 * Outputs the content of the section.
		 * Use the `ucf_section_display` filter
		 * hook to override or modify this output.
		 *
		 * @author Jim Barnes
		 * @since 1.0.0
		 *
		 * @param $section WP_Post object | The section
		 *
		 * @return string | The html to be appended to output.
		 **/
		public static function ucf_section_display( $content ) {
			ob_start();
			echo $content;
			return ob_get_clean();
		}

		/**
		 * Outputs the content of the section.
		 * Use the `ucf_section_display_after` filter
		 * hook to override or modify this output.
		 *
		 * @author Jim Barnes
		 * @since 1.0.0
		 *
		 * @param $section WP_Post object | The section
		 *
		 * @return string | The html to be appended to output.
		 **/
		public static function ucf_section_display_after( $content ) {
			ob_start();
		?>
			</section>
		<?php
			return ob_get_clean();
		}

		/**
		 * Returns a section based on slug.
		 *
		 * @author Jim Barnes
		 * @since 1.0.0
		 *
		 * @param $slug string | The slug of the post to find
		 *
		 * @return WP_POST|null | The WP_Post object found.
		 **/
		public static function get_section_by_slug( $slug ) {
			$args = array(
				'post_type'   => 'ucf_section',
				'name'        => $slug,
				'numberposts' => 1,
			);

			$posts = get_posts( $args );

			if ( count( $posts ) > 0 ) {
				return $posts[0];
			}

			return null;
		}

		/**
		 * Replaces paragraph tags around the section shortcode
		 * @author Jim Barnes
		 * @since 1.0.0
		 * @param $content string | The content being filtered
		 * @return string | The formatted content
		 **/
		public static function format_shortcode_output( $content ) {
			$block = 'ucf-section';

			$rep = preg_replace( "/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content );
			$rep = preg_replace( "/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", "[/$2]", $rep );
			return $rep;
		}
	}
}

?>
