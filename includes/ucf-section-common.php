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
		public static function display_section( $attr ) {
			$retval = '';
			$section = null;
			$class = '';
			$title = '';
			$section_id = '';

			if ( isset( $attr['slug'] ) ) {
				$section = self::get_section_by_slug( $attr['slug'] );
			}

			if ( isset( $attr['id'] ) ) {
				$section = get_post( $attr['id'] );
			}

			if  ( isset( $attr['class'] ) ) {
				$class = $attr['class'];
			}

			if ( isset( $attr['title'] ) ) {
				$title = $attr['title'];
			}

			if ( isset( $attr['section_id'] ) ) {
				$section_id = $attr['section_id'];
			}

			if ( $section ) {

				$before = self::ucf_section_display_before( $section, $class, $title, $section_id );
				if ( has_filter( 'ucf_section_display_before' ) ) {
					$before = apply_filters( 'ucf_section_display_before', $before, $section, $class, $title, $section_id );
				}

				$content = self::ucf_section_display( $section );
				if ( has_filter( 'ucf_section_display' ) ) {
					$content = apply_filters( 'ucf_section_display', $content, $section );
				}

				$after = self::ucf_section_display_after( $section );
				if ( has_filter( 'ucf_section_display_after' ) ) {
					$after = apply_filters( 'ucf_section_display_after', $after, $section );
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
		public static function ucf_section_display_before( $section, $class, $title, $section_id ) {
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
		public static function ucf_section_display( $section ) {
			ob_start();
		?>
			<?php echo apply_filters( 'the_content', $section->post_content ); ?>
		<?php
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
		public static function ucf_section_display_after( $section ) {
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

		/**
		 * Called by the wp_head filter
		 * Determines if a section has a stylesheet to print
		 * into the head of the document
		 * @author Jim Barnes
		 * @since 1.0.1
		 **/
		public static function get_section_stylesheets() {
			global $post;

			wp_enqueue_style( 'section-styles', plugins_url( 'static/css/empty.css', UCF_SECTION__PLUGIN_FILE ) );

			$styles_to_print = array();

			if ( has_shortcode( $post->post_content, 'ucf-section' ) ) {
				$pattern = get_shortcode_regex( array( 'ucf-section' ) );

				preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches );

				if ( preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches ) &&
					array_key_exists( 3, $matches ) ) {

					foreach( $matches[3] as $match ) {
						$args = shortcode_parse_atts( $match );

						$section = null;

						if ( isset( $args['slug'] ) ) {
							$section = self::get_section_by_slug( $args['slug'] );
						}

						if ( isset( $args['id'] ) ) {
							$section = get_post( $args['id'] );
						}

						if ( $section !== null ) {
							$stylesheet_id = get_post_meta( $section->ID, 'ucf_section_stylesheet', TRUE );

							if ( $stylesheet_id && ! key_exists( $styles_to_print, $stylesheet_id ) ) {
								$styles_to_print[$stylesheet_id] = $stylesheet_id;
							}
						}
					}
				}

				// Go ahead and print each stylesheet
				foreach( $styles_to_print as $id ) {
					self::print_stylesheet_to_head( $id );
				}
			}
		}

		/**
		 * Retrieves the stylesheet and prints it to head
		 * @author Jim Barnes
		 * @since 1.0.4
		 * @param int $stylesheet_id | The ID of the stylesheet to print
		 **/
		private static function print_stylesheet_to_head( $stylesheet_id=19961 ) {
			$style_filepath = get_attached_file( $stylesheet_id );
			$contents = file_get_contents( $style_filepath );

			wp_add_inline_style( 'section-styles', $contents );
		}
	}

	add_action( 'wp_enqueue_scripts', array( 'UCF_Section_Common', 'get_section_stylesheets' ) );
}

?>
