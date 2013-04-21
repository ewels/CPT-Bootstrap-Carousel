<?php
/*
Plugin Name: CPT Bootstrap Carousel
Plugin URI: http://www.tallphil.co.uk/bootstrap-carousel
Description: A custom post type for choosing images and content which outputs <a href="http://twitter.github.io/bootstrap/javascript.html#carousel" target="_blank">Bootstrap Carousel</a> from a shortcode. Requires Bootstrap javascript and CSS to be loaded separately.
Version: 1.0
Author: Phil Ewels
Author URI: http://phil.ewels.co.uk
License: GPLv2
*/

// Custom Post Type Setup
add_action( 'init', 'cptbc_post_type' );
function cptbc_post_type() {
	$labels = array(
		'name' => 'Carousel Images',
		'singular_name' => 'Carousel Image',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Carousel Image',
		'edit_item' => 'Edit Carousel Image',
		'new_item' => 'New Carousel Image',
		'view_item' => 'View Carousel Image',
		'search_items' => 'Search Carousel Images',
		'not_found' =>  'No Carousel Image',
		'not_found_in_trash' => 'No Carousel Images found in Trash', 
		'parent_item_colon' => '',
		'menu_name' => 'Carousel'
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


// Add theme support for featured images if not already present
// http://wordpress.stackexchange.com/questions/23839/using-add-theme-support-inside-a-plugin
function cptbc_addFeaturedImageSupport() {
	$supportedTypes = get_theme_support( 'post-thumbnails' );
	if( $supportedTypes === false )
		add_theme_support( 'post-thumbnails', array( 'cptbc' ) );               
	elseif( is_array( $supportedTypes ) ) {
		$supportedTypes[0][] = 'cptbc';
		add_theme_support( 'post-thumbnails', $supportedTypes[0] );
	}
}
add_action( 'after_setup_theme', 'cptbc_addFeaturedImageSupport');

// FRONT END

// Shortcode
function cptbc_shortcode($atts) { 
	$atts = (array) $atts;
	return cptbc_frontend($atts);
}
add_shortcode('image-carousel', 'cptbc_shortcode');

// Display latest WftC
function cptbc_frontend($atts){
	$id = rand(0, 999); // use a random ID so that the CSS IDs work with multiple on one page
	$args = array( 'post_type' => 'cptbc', 'orderby' => 'menu_order', 'order' => 'ASC');
	$loop = new WP_Query( $args );
	$images = array();
	while ( $loop->have_posts() ) {
		$loop->the_post();
		if ( '' != get_the_post_thumbnail() ) {
			$title = get_the_title();
			$content = get_the_excerpt();
			$image = get_the_post_thumbnail( get_the_ID(), 'full' );
			$images[] = array('title' => $title, 'content' => $content, 'image' => $image);
		}
	}
	if(count($images) > 0){
		ob_start();
		?>
		<div id="cptbc_<?php echo $id; ?>" class="carousel slide">
			<ol class="carousel-indicators">
			<?php foreach ($images as $key => $image) { ?>
				<li data-target="#cptbc_<?php echo $id; ?>" data-slide-to="<?php echo $key; ?>" <?php echo $key == 0 ? 'class="active"' : ''; ?>></li>
			<?php } ?>
			</ol>
			<div class="carousel-inner">
			<?php foreach ($images as $key => $image) { ?>
				<div class="item <?php echo $key == 0 ? 'active' : ''; ?>">
					<?php echo $image['image']; ?>
					<div class="carousel-caption">
						<h4><?php echo $image['title']; ?></h4>
						<p><?php echo $image['content']; ?></p>
					</div>
				</div>
			<?php } ?>
			</div>
			<a class="left carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="prev">‹</a>
			<a class="right carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="next">›</a>
		</div>
<?php }
	$output = ob_get_contents();
	ob_end_clean();
	
	// Restore original Post Data
	wp_reset_postdata();	
	
	return $output;
}

// Call the carousel in javascript, else it won't start scrolling on its own
function cptbc_footer_js() {
?>
<script type="text/javascript">
	jQuery(function(){
		jQuery('.carousel').carousel()
	});
</script>
<?php
}
add_action('wp_footer', 'cptbc_footer_js');

?>