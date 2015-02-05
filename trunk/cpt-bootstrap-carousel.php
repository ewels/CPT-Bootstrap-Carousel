<?php
/*
Plugin Name: CPT Bootstrap Carousel
Plugin URI: http://www.tallphil.co.uk/bootstrap-carousel/
Description: A custom post type for choosing images and content which outputs <a href="http://getbootstrap.com/javascript/#carousel" target="_blank">Bootstrap Carousel</a> from a shortcode. Requires Bootstrap javascript and CSS to be loaded separately.
Version: 1.9
Author: Phil Ewels
Author URI: http://phil.ewels.co.uk
Text Domain: cpt-bootstrap-carousel
License: GPLv2
*/

// Initialise - load in translations
function cptbc_loadtranslations () {
	$plugin_dir = basename(dirname(__FILE__)).'/languages';
	load_plugin_textdomain( 'cpt-bootstrap-carousel', false, $plugin_dir );
}
add_action('plugins_loaded', 'cptbc_loadtranslations');

////////////////////////////
// Custom Post Type Setup
////////////////////////////
add_action( 'init', 'cptbc_post_type' );
function cptbc_post_type() {
	$labels = array(
		'name' => __('Carousel Images', 'cpt-bootstrap-carousel'),
		'singular_name' => __('Carousel Image', 'cpt-bootstrap-carousel'),
		'add_new' => __('Add New', 'cpt-bootstrap-carousel'),
		'add_new_item' => __('Add New Carousel Image', 'cpt-bootstrap-carousel'),
		'edit_item' => __('Edit Carousel Image', 'cpt-bootstrap-carousel'),
		'new_item' => __('New Carousel Image', 'cpt-bootstrap-carousel'),
		'view_item' => __('View Carousel Image', 'cpt-bootstrap-carousel'),
		'search_items' => __('Search Carousel Images', 'cpt-bootstrap-carousel'),
		'not_found' => __('No Carousel Image', 'cpt-bootstrap-carousel'),
		'not_found_in_trash' => __('No Carousel Images found in Trash', 'cpt-bootstrap-carousel'),
		'parent_item_colon' => '',
		'menu_name' => __('Carousel', 'cpt-bootstrap-carousel')
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'page',
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_position' => 21,
		'supports' => array('title','excerpt','thumbnail', 'page-attributes')
	); 
	register_post_type('cptbc', $args);
}
// Create a taxonomy for the carousel post type
function cptbc_taxonomies () {
	$args = array('hierarchical' => true);
	register_taxonomy( 'carousel_category', 'cptbc', $args );
}
add_action( 'init', 'cptbc_taxonomies', 0 );


// Add theme support for featured images if not already present
// http://wordpress.stackexchange.com/questions/23839/using-add-theme-support-inside-a-plugin
function cptbc_addFeaturedImageSupport() {
	$supportedTypes = get_theme_support( 'post-thumbnails' );
	if( $supportedTypes === false ) {
		add_theme_support( 'post-thumbnails', array( 'cptbc' ) );	  
		add_image_size('featured_preview', 100, 55, true);
	} elseif( is_array( $supportedTypes ) ) {
		$supportedTypes[0][] = 'cptbc';
		add_theme_support( 'post-thumbnails', $supportedTypes[0] );
		add_image_size('featured_preview', 100, 55, true);
	}
}
add_action( 'after_setup_theme', 'cptbc_addFeaturedImageSupport');

// Load in the pages doing everything else!
require_once('cptbc-admin.php');
require_once('cptbc-settings.php');
require_once('cptbc-frontend.php');

