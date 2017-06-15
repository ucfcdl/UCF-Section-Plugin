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
				'has_archive'           => true,
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
}
?>
