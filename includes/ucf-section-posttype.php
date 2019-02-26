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
					'singular'    => 'Section',
					'plural'      => 'Sections',
					'text_domain' => 'ucf_section'
				)
			);
			register_post_type( 'ucf_section', self::args( $labels ) );
			add_action( 'add_meta_boxes', array( 'UCF_Section_PostType', 'register_metabox' ) );
			add_action( 'save_post', array( 'UCF_Section_PostType', 'save_metabox' ) );
		}

		/**
		 * Adds the UCF Section Assets metabox
		 * @author Jim Barnes
		 * @since 1.0.4
		 **/
		public static function register_metabox() {
			add_meta_box(
				'ucf_section_metabox',
				'UCF Section Fields',
				array( 'UCF_Section_PostType', 'register_fields' ),
				'ucf_section',
				'normal',
				'low'
			);
		}

		/**
		 * The markup callback for the UCF Section Assets metabox
		 * @author Jim Barnes
		 * @since 1.0.4
		 * @param $post WP_Post | The current post object
		 * @return string | The function output is echoed
		 **/
		public static function register_fields( $post ) {
			wp_nonce_field( 'ucf_section_nonce_save', 'ucf_section_nonce' );
			$upload_link = esc_url( get_upload_iframe_src( 'media', $post->ID ) );

			$stylesheet     = get_post_meta( $post->ID, 'ucf_section_stylesheet', TRUE );
			$stylesheet_url = wp_get_attachment_url( $stylesheet );
			$javascript     = get_post_meta( $post->ID, 'ucf_section_javascript', TRUE );
			$javascript_url = wp_get_attachment_url( $javascript );

				// Existing asset IDs are invalid if the attachment URL can't be retrieved
				// (e.g. if the attachment was deleted)
			if ( ! $stylesheet_url ) {
				$stylesheet = null;
			}

			if ( ! $javascript_url ) {
				$javascript = null;
			}
?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><strong>Custom Stylesheet</strong></th>
						<td>
							<div class="css-preview meta-file-wrap <?php if ( ! $stylesheet ) { echo 'hidden'; }?>">
								<span class="dashicons dashicons-media-code"></span>
								<span id="ucf_section_css_filename"><?php if ( $stylesheet_url ) { echo basename( $stylesheet_url ); }?></span>
							</div>
							<p class="hide-if-no-js">
								<a class="css-upload meta-file-upload <?php if ( $stylesheet ) { echo 'hidden'; }?>" href="<?php echo $upload_link; ?>">
									Add File
								</a>
								<a class="css-remove meta-file-upload <?php if ( !$stylesheet ) { echo 'hidden'; }?>" href="#">
									Remove File
								</a>
							</p>
							<input class="meta-file-field" id="ucf_section_stylesheet" name="ucf_section_stylesheet" type="hidden" value="<?php if ( $stylesheet ) { echo htmlentities( $stylesheet ); } ?>">
						</td>
					</tr>
					<tr>
						<th><strong>Custom JavaScript</strong></th>
						<td>
							<div class="js-preview meta-file-wrap <?php if ( ! $javascript ) { echo 'hidden'; }?>">
								<span class="dashicons dashicons-media-code"></span>
								<span id="ucf_section_js_filename"><?php if ( $javascript_url ) { echo basename( $javascript_url ); } ?></span>
							</div>
							<p class="hide-if-no-js">
								<a class="js-upload meta-file-upload <?php if ( $javascript ) { echo 'hidden'; }?>" href="<?php echo $upload_link; ?>">
									Add File
								</a>
								<a class="js-remove meta-file-upload <?php if ( !$javascript ) { echo 'hidden'; }?>" href="#">
									Remove File
								</a>
							</p>

							<input class="meta-file-field" id="ucf_section_javascript" name="ucf_section_javascript" type="hidden" value="<?php if ( $javascript ) { echo htmlentities( $javascript ); } ?>">
						<td>
					</tr>
				</tbody>
			</table>
<?php
		}

		/**
		 * Enqueue admin assets
		 * @author Jim Barnes
		 * @since 1.0.4
		 **/
		public static function enqueue_admin_assets( $hook ) {
			global $post;

			if ( $hook === 'post-new.php' || $hook === 'post.php' ) {
				if ( 'ucf_section' === $post->post_type ) {
					wp_enqueue_script(
						'ucf_section_admin_script',
						plugins_url( 'static/js/ucf-section-admin.min.js', UCF_SECTION__PLUGIN_FILE ),
						array( 'jquery' ),
						null,
						true
					);
				}
			}
		}

		/**
		 * Saves the data from the metabox
		 * @author Jim Barnes
		 * @since 1.0.4
		 **/
		public static function save_metabox( $post_id ) {
			$post_type = get_post_type( $post_id );

			// If not a ucf_section post, return.
			if ( $post_type !== 'ucf_section' ) return;

			// If the nonce is not present or is invalid return.
			if (
				! isset( $_POST['ucf_section_nonce'] )
				|| ! wp_verify_nonce( $_POST['ucf_section_nonce'], 'ucf_section_nonce_save' )
			) return;

			$stylesheet = isset( $_POST['ucf_section_stylesheet'] ) ? intval( $_POST['ucf_section_stylesheet'] ) : null;
			$javascript = isset( $_POST['ucf_section_javascript'] ) ? intval( $_POST['ucf_section_javascript'] ) : null;

			if ( ! add_post_meta( $post_id, 'ucf_section_stylesheet', $stylesheet, true ) ) {
				update_post_meta( $post_id, 'ucf_section_stylesheet', $stylesheet );
			}

			if ( ! add_post_meta( $post_id, 'ucf_section_javascript', $javascript, true ) ) {
				update_post_meta( $post_id, 'ucf_section_javascript', $javascript );
			}
		}

		/**
		 * Returns an array of labels for the custom post type.
		 * @author RJ Bruneel
		 * @since 1.0.0
		 * @param $singular string | The singular form for the CPT labels.
		 * @param $plural string | The plural form for the CPT labels.
		 * @param $text_domain string | The text domain.
		 * @return Array
		 **/
		public static function labels( $singular, $plural, $text_domain ) {
			return array(
				'name'                  => _x( $plural, 'Post Type General Name', $text_domain ),
				'singular_name'         => _x( $singular, 'Post Type Singular Name', $text_domain ),
				'menu_name'             => __( $plural, $text_domain ),
				'name_admin_bar'        => __( $singular, $text_domain ),
				'archives'              => __( $plural . ' Archives', $text_domain ),
				'parent_item_colon'     => __( 'Parent ' . $singular . ':', $text_domain ),
				'all_items'             => __( 'All ' . $plural, $text_domain ),
				'add_new_item'          => __( 'Add New ' . $singular, $text_domain ),
				'add_new'               => __( 'Add New', $text_domain ),
				'new_item'              => __( 'New ' . $singular, $text_domain ),
				'edit_item'             => __( 'Edit ' . $singular, $text_domain ),
				'update_item'           => __( 'Update ' . $singular, $text_domain ),
				'view_item'             => __( 'View ' . $singular, $text_domain ),
				'search_items'          => __( 'Search ' . $plural, $text_domain ),
				'not_found'             => __( 'Not found', $text_domain ),
				'not_found_in_trash'    => __( 'Not found in Trash', $text_domain ),
				'featured_image'        => __( 'Featured Image', $text_domain ),
				'set_featured_image'    => __( 'Set featured image', $text_domain ),
				'remove_featured_image' => __( 'Remove featured image', $text_domain ),
				'use_featured_image'    => __( 'Use as featured image', $text_domain ),
				'insert_into_item'      => __( 'Insert into ' . $singular, $text_domain ),
				'uploaded_to_this_item' => __( 'Uploaded to this ' . $singular, $text_domain ),
				'items_list'            => __( $plural . ' list', $text_domain ),
				'items_list_navigation' => __( $plural . ' list navigation', $text_domain ),
				'filter_items_list'     => __( 'Filter ' . $plural . ' list', $text_domain ),
			);
		}

		/**
		 * Returns the argument array for the section custom post type
		 * @author RJ Bruneel
		 * @since 1.0.0
		 * @param $labels Array | An array of labels
		 * @return Array
		 **/
		public static function args( $labels ) {
			$singular = $labels['singular'];
			$plural = $labels['plural'];
			$text_domain = $labels['text_domain'];

			$args = array(
				'label'                 => __( 'Section', 'ucf_section' ),
				'description'           => __( 'Sections', 'ucf_section' ),
				'labels'                => self::labels( $singular, $plural, $text_domain ),
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
				'has_archive'           => false,
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'capability_type'       => 'post',
			);
			$args = apply_filters( 'ucf_section_post_type_args', $args );
			return $args;
		}

		/**
		 * Returns an array of taxonomies to associate with the post type
		 * @author RJ Bruneel
		 * @since 1.0.0
		 * @return Array
		 **/
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

	/** Register the post type on init */
	add_action( 'init', array( 'UCF_Section_PostType', 'register' ), 10, 0 );
	add_action( 'admin_enqueue_scripts', array( 'UCF_Section_PostType', 'enqueue_admin_assets' ), 99, 1 );
}
?>
