<?php
/*
Plugin Name: UCF Section
Description: Provides a shortcode, functions, and default styles for displaying Sections.
Version: 1.1.2
Author: UCF Web Communications
License: GPL3
GitHub Plugin URI: /UCF/UCF-Section-Plugin
*/


if ( ! defined( 'WPINC' ) ) {
	die;
}

add_filter( 'the_content', array( 'UCF_Section_Common', 'format_shortcode_output' ), 10, 1 );

add_action( 'plugins_loaded', function() {

	define( 'UCF_SECTION__PLUGIN_FILE', __FILE__ );

	require_once 'includes/ucf-section-common.php';
	require_once 'shortcodes/ucf-section-shortcode.php';
	require_once 'includes/ucf-section-posttype.php';

} );

?>
