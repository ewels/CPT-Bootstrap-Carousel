<?php
/*
Plugin Name: CPT Bootstrap Carousel
Plugin URI: http://www.tallphil.co.uk/bootstrap-carousel/
Description: A custom post type for choosing images and content which outputs <a href="http://twitter.github.io/bootstrap/javascript.html#carousel" target="_blank">Bootstrap Carousel</a> from a shortcode. Requires Bootstrap javascript and CSS to be loaded separately.
Version: 1.2
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

// Add column in admin list view to show featured image
// http://wp.tutsplus.com/tutorials/creative-coding/add-a-custom-column-in-posts-and-custom-post-types-admin-screen/
function cptbc_get_featured_image($post_ID) {  
	$post_thumbnail_id = get_post_thumbnail_id($post_ID);  
	if ($post_thumbnail_id) {  
		$post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'featured_preview');  
		return $post_thumbnail_img[0];  
	}  
}
function cptbc_columns_head($defaults) {  
	$defaults['featured_image'] = 'Featured Image';  
	return $defaults;  
}  
function cptbc_columns_content($column_name, $post_ID) {  
	if ($column_name == 'featured_image') {  
		$post_featured_image = cptbc_get_featured_image($post_ID);  
		if ($post_featured_image) {  
			echo '<img src="' . $post_featured_image . '" width="70%" />';  
		}  
	}  
}
add_filter('manage_posts_columns', 'cptbc_columns_head');  
add_action('manage_posts_custom_column', 'cptbc_columns_content', 10, 2);

// Extra admin field for image URL
function cptbc_image_url(){
  global $post;
  $custom = get_post_custom($post->ID);
  $cptbc_image_url = isset($custom['cptbc_image_url']) ?  $custom['cptbc_image_url'][0] : '';
  $cptbc_image_url_openblank = isset($custom['cptbc_image_url_openblank']) ?  $custom['cptbc_image_url_openblank'][0] : '0';
  ?>
  <label>Image URL:</label>
  <input name="cptbc_image_url" value="<?php echo $cptbc_image_url; ?>" /> <br />
  <small><em>(optional - leave blank for no link)</em></small><br /><br />
  <label><input type="checkbox" name="cptbc_image_url_openblank" <?php if($cptbc_image_url_openblank == 1){ echo ' checked="checked"'; } ?> value="1" /> Open link in new window?</label>
  <?php
}
function cptbc_admin_init_custpost(){
	add_meta_box("cptbc_image_url", "Image Link URL", "cptbc_image_url", "cptbc", "side", "low");
}
add_action("add_meta_boxes", "cptbc_admin_init_custpost");
function cptbc_mb_save_details(){
	global $post;
	if (isset($_POST["cptbc_image_url"])) {
		update_post_meta($post->ID, "cptbc_image_url", esc_url($_POST["cptbc_image_url"]));
		update_post_meta($post->ID, "cptbc_image_url_openblank", $_POST["cptbc_image_url_openblank"]);
	}
}
add_action('save_post', 'cptbc_mb_save_details');



// FRONT END

// Shortcode
function cptbc_shortcode($atts, $content = null) {
	// Set default shortcode attributes
	$defaults = array(
		'interval' => '5000',
		'showcaption' => 'true',
		'showcontrols' => 'true'
	);

	// Parse incomming $atts into an array and merge it with $defaults
	$atts = shortcode_atts($defaults, $atts);

	return cptbc_frontend($atts);
}
add_shortcode('image-carousel', 'cptbc_shortcode');

// Display carousel
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
			$url = get_post_meta(get_the_ID(), 'cptbc_image_url');
			$url_openblank = get_post_meta(get_the_ID(), 'cptbc_image_url_openblank');
			$images[] = array('title' => $title, 'content' => $content, 'image' => $image, 'url' => esc_url($url[0]), 'url_openblank' => $url_openblank[0] == "1" ? true : false);
		}
	}
	if(count($images) > 0){
		ob_start();
		?>
		<div id="cptbc_<?php echo $id; ?>" class="carousel slide">
			<ol class="carousel-indicators">
			<?php foreach ($images as $key => $image) { ?>
				<li data-target="#cptbc_<?php echo $id; ?>" data-slide-to="<?php echo $key; ?>" data-interval="<?php echo $atts['interval']; ?>" <?php echo $key == 0 ? 'class="active"' : ''; ?>></li>
			<?php } ?>
			</ol>
			<div class="carousel-inner">
			<?php foreach ($images as $key => $image) { ?>
				<div class="item <?php echo $key == 0 ? 'active' : ''; ?>">
					<?php if($image['url']) {
						echo '<a href="'.$image['url'].'"';
						if($image['url_openblank']) {
							echo ' target="_blank"';
						}
						echo '>';
					}
					echo $image['image'];
					if($image['url']) { echo '</a>'; }?>
					<?php if($atts['showcaption'] === 'true') { ?>
						<div class="carousel-caption">
							<h4><?php echo $image['title']; ?></h4>
							<p><?php echo $image['content']; ?></p>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
			</div>
			<?php if($atts['showcontrols'] === 'true') { ?>
				<a class="left carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="prev">‹</a>
				<a class="right carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="next">›</a>
			<?php } ?>
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
