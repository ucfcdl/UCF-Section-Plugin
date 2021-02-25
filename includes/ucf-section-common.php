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
			global $wp_query;

			$post = $wp_query->get_queried_object();

			if ( $post !== null && get_class( $post ) !== 'WP_Post' ) {
				$post = null;
			}

			$retval = '';
			$section = null;
			$class = array( 'ucf-section' );
			$title = '';
			$section_id = '';

			if ( isset( $attr['slug'] ) ) {
				if ( $post ) {
					$section = isset( $post->sections['posts'][$attr['slug']] ) ? $post->sections['posts'][$attr['slug']] : null;
				}

				if ( ! $section ) {
					$section = self::get_section_by_slug( $attr['slug'] );
				}
			}

			if ( isset( $attr['id'] ) ) {
				if ( $post ) {
					$section = isset( $post->sections['posts'][$attr['id']] ) ? $post->sections['posts'][$attr['id']] : null;
				}

				if ( ! $section ) {
					$section = get_post( $attr['id'] );
				}
			}

			if ( !empty( $attr['random_from_tag'] ) ) {
				if ( $post ) {
					$section = isset( $post->sections['posts'][$attr['random_from_tag']] ) ? $post->sections['posts'][$attr['random_from_tag']] : null;
				}

				if ( ! $section ) {
					$section = self::get_random_section( $attr['random_from_tag'] );
				}
			}

			if ( $section ) {

				$class[] = 'ucf-section-' . $section->post_name;
				if ( isset( $attr['class'] ) ) {
					$class = array_unique( array_merge( $class, explode( ' ', $attr['class'] ) ) );
				}
				$class = implode( ' ', $class );

				if ( isset( $attr['title'] ) && ! empty( $attr['title'] ) ) {
					$title = $attr['title'];
				} else {
					$pattern = '/<h(\d)([\s\w":\d\-=]*)>(.*)<\/h\1>/';
					$matches = array();

					preg_match( $pattern, $section->post_content, $matches );

					if ( $matches ) {
						$title = trim( esc_attr( strip_tags( $matches[3] ) ) );
					}
				}

				if ( isset( $attr['section_id'] ) ) {
					$section_id = $attr['section_id'];
				}

				$before = '';
				if ( has_filter( 'ucf_section_display_before' ) ) {
					$before = apply_filters( 'ucf_section_display_before', $before, $section, $class, $title, $section_id );
				} else {
					$before = self::ucf_section_display_before( $section, $class, $title, $section_id );
				}

				$content = '';
				if ( has_filter( 'ucf_section_display' ) ) {
					$content = apply_filters( 'ucf_section_display', $content, $section );
				} else {
					$content = self::ucf_section_display( $section );
				}

				$after = '';
				if ( has_filter( 'ucf_section_display_after' ) ) {
					$after = apply_filters( 'ucf_section_display_after', $after, $section );
				} else {
					$after = self::ucf_section_display_after( $section );
				}

				$retval = $before . $content . $after;

			}

			return wp_make_content_images_responsive( $retval );
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
			$class = ' class="' . $class . '"';

			$title_markup =
				! empty( $title )
				? ' data-section-link-title="' . $title . '" aria-label="' . $title . '"'
				: '';

			$id_markup =
				! empty( $section_id )
				? ' id="' . $section_id . '"'
				: '';

			ob_start();
		?>
			<section<?php echo $id_markup; ?><?php echo $class; ?><?php echo $title_markup; ?>>
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
		 * Returns all the sections found within the current $post's content
		 * @author Jo Dickson
		 * @since 1.0.4
		 * @return array | Array of section WP_Post objects
		 **/
		public static function get_post_sections( $object ) {
			$sections = array();

			if ( !$object || is_admin() ) { return $sections; } // Abort if $post is not set

			if ( has_filter( 'get_post_sections' ) ) {
				$sections = apply_filters( 'get_post_sections', $sections, $object );
			}

			if ( $object->post_type === 'ucf_section' ) {
				$sections[$object->post_name] = $object;
			}

			if ( get_class( $object ) === 'WP_Post' ) {
				$sections = self::get_post_sections_from_content( $object->post_content, $sections );
			}

			return $sections;
		}

		/**
		 * Helper function that finds the sections
		 * within a chunk of content.
		 * @author Jim Barnes
		 * @since 1.0.15
		 * @param string $content The content to parse
		 * @param array $sections The sections already found
		 * @return array The sections
		 */
		public static function get_post_sections_from_content( $content, $sections = array() ) {
			if ( has_shortcode( $content, 'ucf-section' ) ) {
				$pattern = get_shortcode_regex( array( 'ucf-section' ) );

				if ( preg_match_all( '/' . $pattern . '/s', $content, $matches ) &&
					array_key_exists( 3, $matches ) ) {

					foreach( $matches[3] as $match ) {
						$args = shortcode_parse_atts( $match );

						$section = null;
						$match = null;

						if ( isset( $args['slug'] ) ) {
							$section = self::get_section_by_slug( $args['slug'] );
							$match = $args['slug'];
						}

						if ( isset( $args['id'] ) ) {
							$section = get_post( $args['id'] );
							$match = $args['id'];
						}

						if ( isset( $args['random_from_tag'] ) ) {
							$section = self::get_random_section( $args['random_from_tag'] );
							$match = $args['random_from_tag'];
						}

						if ( $section !== null ) {
							$sections[$match] = $section;
						}

					}
				}
			}

			return $sections;
		}

		/**
		 * Returns all the inline styles to print for all sections found within
		 * the current $post's content
		 * @author Jo Dickson
		 * @since 1.0.4
		 * @return array | array of styles; keys correspond to attachment IDs, values consist of stylesheet file contents
		 **/
		public static function get_post_section_styles( $sections ) {
			$styles_to_print = array();

			if ( $sections ) {
				foreach ( $sections as $section ) {
					$stylesheet_id = get_post_meta( $section->ID, 'ucf_section_stylesheet', TRUE );
					$style_filepath = get_attached_file( $stylesheet_id );
					$style_contents = '';
					if ( $style_filepath ) {
						$style_contents = file_get_contents( $style_filepath );
					}

					if ( $stylesheet_id && $style_contents && ! key_exists( $stylesheet_id, $styles_to_print ) ) {
						$styles_to_print[$stylesheet_id] = $style_contents;
					}
				}
			}
			return $styles_to_print;
		}

		/**
		 * Returns all the inline scripts to print for all sections found
		 * within the current $post's content
		 * @author Jo Dickson
		 * @since 1.0.4
		 * @return array | array of scripts; keys correspond to attachment IDs, values consist of javascript file contents
		 **/
		public static function get_post_section_javascript( $sections ) {
			$scripts_to_print = array();

			if ( $sections ) {
				foreach ( $sections as $section ) {
					$javascript_id = get_post_meta( $section->ID, 'ucf_section_javascript', TRUE );
					$javascript_filepath = get_attached_file( $javascript_id );
					$javascript_contents = '';
					if ( $javascript_filepath ) {
						$javascript_contents = file_get_contents( $javascript_filepath );
					}

					if ( $javascript_id && $javascript_contents && ! key_exists( $javascript_id, $scripts_to_print ) ) {
						$scripts_to_print[$javascript_id] = $javascript_contents;
					}
				}
			}
			return $scripts_to_print;
		}

		/**
		 * To be called by wp_head. Prints all relevant section styles for the
		 * current $post
		 * @author Jo Dickson
		 * @since 1.0.4
		 * @return void
		 **/
		public static function add_inline_section_styles() {
			global $wp_query;

			$object = $wp_query->get_queried_object();

			if ( ! $object ) return;

			$styles_to_print = $object->sections['styles'];

			if ( $styles_to_print ) {
				foreach ( $styles_to_print as $stylesheet_id => $styles ) {
					echo '<style id="section-css-' . $stylesheet_id . '">' . $styles . '</style>';
				}
			}
		}

		/**
		 * To be called by wp_footer. Prints all relevant section scripts for
		 * the current $post
		 * @author Jo Dickson
		 * @since 1.0.4
		 * @return void
		 **/
		public static function add_inline_section_javascript() {
			global $wp_query;

			$object = $wp_query->get_queried_object();

			if ( ! $object ) return;

			$scripts_to_print = $object->sections['scripts'];

			if ( $scripts_to_print ) {
				foreach ( $scripts_to_print as $javascript_id => $script ) {
					echo '<script id="section-js-' . $javascript_id . '">' . $script . '</script>';
				}
			}
		}

		/**
		 * Adds media library support for css and js files.
		 * @author Cadie Brown
		 * @since 1.0.5
		 * @param array $mimes | Current array of mime types
		 * @return array | Updated array of mime types
		 **/
		public static function add_custom_mimes( $mimes ) {
			$mimes['css'] = 'text/css';
			$mimes['js'] = 'application/javascript';
			return $mimes;
		}

		/**
		 * Filter for adding post data
		 * @author Jim Barnes
		 * @since 1.0.12
		 * @param WP_Post $post The post object
		 */
		public static function add_sections_to_post() {
			global $wp_query;

			$object = $wp_query->get_queried_object();

			if ( ! $object ) return;

			$sections = self::get_post_sections( $object );

			$object->sections = array(
				'posts' => $sections,
				'styles' => array(),
				'scripts' => array()
			);

			if ( count( $object->sections['posts'] ) < 1 ) return $object;

			$object->sections['styles'] = self::get_post_section_styles( $object->sections['posts'] );
			$object->sections['scripts'] = self::get_post_section_javascript( $object->sections['posts'] );
		}

		/**
		 * Returns a randomly selected section from a
		 * given tag.
		 *
		 * @author Cadie Stockman
		 * @since 1.1.0
		 *
		 * @param $tag string | The tag to choose a section from
		 *
		 * @return WP_POST|null | The WP_Post object found.
		 **/
		public static function get_random_section( $tag ) {
			$args = array(
				'post_type'   => 'ucf_section',
				'numberposts' => 1,
				'orderby'     => 'rand',
				'tag'         => $tag,
			);

			$posts = get_posts( $args );

			if ( count( $posts ) > 0 ) {
				return $posts[0];
			}

			return null;
		}

	}

	add_action( 'wp_head', array( 'UCF_Section_Common', 'add_inline_section_styles' ), 99 );
	add_action( 'wp_footer', array( 'UCF_Section_Common', 'add_inline_section_javascript' ), 99 );
	add_filter( 'upload_mimes', array( 'UCF_Section_Common', 'add_custom_mimes' ) );
	add_filter( 'wp', array( 'UCF_Section_Common', 'add_sections_to_post' ), 99, 1 );
}

?>
