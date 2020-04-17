<?php
/*
Plugin Name: CPT Bootstrap Carousel
Plugin URI: http://www.tallphil.co.uk/bootstrap-carousel/
Description: A custom post type for choosing images and content which outputs <a href="http://getbootstrap.com/javascript/#carousel" target="_blank">Bootstrap Carousel</a> from a shortcode. Requires Bootstrap javascript and CSS to be loaded separately.
Version: 1.11dev
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


// Load in the pages doing everything else!
require_once('src/cptbc-init.php');
require_once('src/cptbc-admin.php');
require_once('src/cptbc-settings.php');
require_once('src/cptbc-frontend.php');
