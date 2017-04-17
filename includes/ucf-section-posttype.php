<?php
/**
 * Handles the registration of the section custom post type.
 * @author RJ Bruneel
 * @since 1.0.0
 **/
if ( ! class_exists( 'UCF_Section_PostType' ) ) {
	class UCF_Section_PostType {
		/**
		 * Registers the custom post type.
		 * @author RJ Bruneel
		 * @since 1.0.0
		 **/
		public static function register() {
			$labels = apply_filters(
				'ucf_section_labels',
				array(
					'singular'  => 'Section',
					'plural'    => 'Sections',
					'post_type' => 'ucf_section'
				)
			);
			register_post_type( 'ucf_section', self::args( $labels ) );
			add_action( 'add_meta_boxes', array( 'UCF_Section_PostType', 'register_metabox' ) );
			add_action( 'save_post', array( 'UCF_Section_PostType', 'save_metabox' ) );
		}


		/**
		 * Adds a metabox to the section custom post type.
		 * @author RJ Bruneel
		 * @since 1.0.0
		 **/
		public static function register_metabox() {
			add_meta_box(
				'ucf_section_metabox',
				'Section Details',
				array( 'UCF_Section_PostType', 'register_metafields' ),
				'ucf_section',
				'normal',
				'high'
			);
		}


		/**
		 * Adds metafields to the metabox
		 * @author RJ Bruneel
		 * @since 1.0.0
		 * @param $post WP_POST object
		 **/
		public static function register_metafields( $post ) {
			wp_nonce_field( 'ucf_section_nonce_save', 'ucf_section_nonce' );
			$header = get_post_meta( $post->ID, 'ucf_section_header', TRUE );
?>
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label class="block" for="ucf_section_header"><strong>Header</strong></label>
						</th>
						<td>
							<p class="description">(Optional) Large header displayed at the top of the section.</p>
							<input type="text" id="ucf_section_header" name="ucf_section_header" class="regular-text" <?php echo ( ! empty( $header ) ) ? 'value="' . $header . '"' : ''; ?>>
						</td>
					</tr>
				</tbody>
			</table>
<?php
		}

		/**
		 * Handles saving the data in the metabox
		 * @author RJ Bruneel
		 * @since 1.0.0
		 * @param $post_id WP_POST post id
		 **/
		public static function save_metabox( $post_id ) {
			$post_type = get_post_type( $post_id );
			// If this isn't a section, return.
			if ( 'ucf_section' !== $post_type ) return;
			if ( isset( $_POST['ucf_section_header'] ) ) {
				// Ensure field is valid.
				$header = sanitize_text_field( $_POST['ucf_section_header'] );
				if ( $header ) {
					update_post_meta( $post_id, 'ucf_section_header', $header );
				}
			}
		}

		/**
		 * Returns an array of labels for the custom post type.
		 * @author RJ Bruneel
		 * @since 1.0.0
		 * @param $singular string | The singular form for the CPT labels.
		 * @param $plural string | The plural form for the CPT labels.
		 * @param $post_type string | The post type name.
		 * @return Array
		 **/
		public static function labels( $singular, $plural, $post_type ) {
			return array(
				'name'                  => _x( $plural, 'Post Type General Name', $post_type ),
				'singular_name'         => _x( $singular, 'Post Type Singular Name', $post_type ),
				'menu_name'             => __( $plural, $post_type ),
				'name_admin_bar'        => __( $singular, $post_type ),
				'archives'              => __( $plural . ' Archives', $post_type ),
				'parent_item_colon'     => __( 'Parent ' . $singular . ':', $post_type ),
				'all_items'             => __( 'All ' . $plural, $post_type ),
				'add_new_item'          => __( 'Add New ' . $singular, $post_type ),
				'add_new'               => __( 'Add New', $post_type ),
				'new_item'              => __( 'New ' . $singular, $post_type ),
				'edit_item'             => __( 'Edit ' . $singular, $post_type ),
				'update_item'           => __( 'Update ' . $singular, $post_type ),
				'view_item'             => __( 'View ' . $singular, $post_type ),
				'search_items'          => __( 'Search ' . $plural, $post_type ),
				'not_found'             => __( 'Not found', $post_type ),
				'not_found_in_trash'    => __( 'Not found in Trash', $post_type ),
				'featured_image'        => __( 'Featured Image', $post_type ),
				'set_featured_image'    => __( 'Set featured image', $post_type ),
				'remove_featured_image' => __( 'Remove featured image', $post_type ),
				'use_featured_image'    => __( 'Use as featured image', $post_type ),
				'insert_into_item'      => __( 'Insert into ' . $singular, $post_type ),
				'uploaded_to_this_item' => __( 'Uploaded to this ' . $singular, $post_type ),
				'items_list'            => __( $plural . ' list', $post_type ),
				'items_list_navigation' => __( $plural . ' list navigation', $post_type ),
				'filter_items_list'     => __( 'Filter ' . $plural . ' list', $post_type ),
			);
		}

		public static function args() {
			$args = array(
				'label'                 => __( 'Section', 'ucf_section' ),
				'description'           => __( 'Sections', 'ucf_section' ),
				'labels'                => self::labels( $singular, $plural, $post_type ),
				'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', ),
				'taxonomies'            => self::taxonomies(),
				'hierarchical'          => false,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 5,
				'menu_icon'             => 'dashicons-welcome-widgets-menus',
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => true,
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'capability_type'       => 'post',
			);
			$args = apply_filters( 'ucf_section_post_type_args', $args );
			return $args;
		}
		public static function taxonomies() {
			$retval = array();
			$retval = apply_filters( 'ucf_section_taxonomies', $retval );

			foreach( $retval as $taxonomy ) {
				if ( ! taxonomy_exists( $taxonomy ) ) {
					unset( $retval[$taxonomy] );
				}
			}
			return $retval;
		}
	}
    add_action( 'init', array( 'UCF_Section_PostType', 'register' ), 10, 0 );
}
?>