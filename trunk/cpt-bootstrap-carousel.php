<?php
/*
Plugin Name: CPT Bootstrap Carousel
Plugin URI: http://www.tallphil.co.uk/bootstrap-carousel/
Description: A custom post type for choosing images and content which outputs <a href="http://twitter.github.io/bootstrap/javascript.html#carousel" target="_blank">Bootstrap Carousel</a> from a shortcode. Requires Bootstrap javascript and CSS to be loaded separately.
Version: 1.5
Author: Phil Ewels
Author URI: http://phil.ewels.co.uk
Text Domain: cpt-bootstrap-carousel
License: GPLv2
*/

// Initialise - load in translations
function cptbc_loadtranslations () {
	$plugin_dir = basename(dirname(__FILE__));
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
	$defaults['featured_image'] = __('Featured Image', 'cpt-bootstrap-carousel');  
	return $defaults;  
}  
function cptbc_columns_content($column_name, $post_ID) {  
	if ($column_name == 'featured_image') {  
		$post_featured_image = cptbc_get_featured_image($post_ID);  
		if ($post_featured_image) {  
			echo '<a href="'.get_edit_post_link($post_ID).'"><img src="' . $post_featured_image . '" /></a>';  
		}  
	}  
}
add_filter('manage_cptbc_posts_columns', 'cptbc_columns_head');  
add_action('manage_cptbc_posts_custom_column', 'cptbc_columns_content', 10, 2);

// Extra admin field for image URL
function cptbc_image_url(){
  global $post;
  $custom = get_post_custom($post->ID);
  $cptbc_image_url = isset($custom['cptbc_image_url']) ?  $custom['cptbc_image_url'][0] : '';
  $cptbc_image_url_openblank = isset($custom['cptbc_image_url_openblank']) ?  $custom['cptbc_image_url_openblank'][0] : '0';
  ?>
  <label><?php _e('Image URL', 'cpt-bootstrap-carousel'); ?>:</label>
  <input name="cptbc_image_url" value="<?php echo $cptbc_image_url; ?>" /> <br />
  <small><em><?php _e('(optional - leave blank for no link)', 'cpt-bootstrap-carousel'); ?></em></small><br /><br />
  <label><input type="checkbox" name="cptbc_image_url_openblank" <?php if($cptbc_image_url_openblank == 1){ echo ' checked="checked"'; } ?> value="1" /> <?php _e('Open link in new window?', 'cpt-bootstrap-carousel'); ?></label>
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

// Set up settings defaults
register_activation_hook(__FILE__, 'cptbc_set_options');
function cptbc_set_options (){
	$defaults = array(
		'interval' => '5000',
		'showcaption' => 'true',
		'showcontrols' => 'true',
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'category' => '',
		'id' => '',
		'twbs' => '2'
	);
	add_option('cptbc_settings', $defaults);
}
// Clean up on uninstall
register_activation_hook(__FILE__, 'cptbc_deactivate');
function cptbc_deactivate(){
	delete_option('cptbc_settings');
}


///////////////////
// SETTINGS PAGE
///////////////////
class cptbc_settings_page {
	// Holds the values to be used in the fields callbacks
	private $options;
    	
	// Start up
	public function __construct() {
	    add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
	    add_action( 'admin_init', array( $this, 'page_init' ) );
	}
    	
	// Add settings page
	public function add_plugin_page() {
		add_submenu_page('edit.php?post_type=cptbc', __('Settings', 'cpt-bootstrap-carousel'), __('Settings', 'cpt-bootstrap-carousel'), 'manage_options', 'cpt-bootstrap-carousel', array($this,'create_admin_page'));
	}
    	
	// Options page callback
	public function create_admin_page() {
	    // Set class property
	    $this->options = get_option( 'cptbc_settings' );
		if(!$this->options){
			cptbc_set_options ();
			$this->options = get_option( 'cptbc_settings' );
		}
	    ?>
	    <div class="wrap">
			<?php screen_icon('edit');?> <h2>CPT Bootstrap Carousel <?php _e('Settings', 'cpt-bootstrap-carousel'); ?></h2>
			<p><?php printf(__('You can set the default behaviour of your carousels here. All of these settings can be overridden by using %s shortcode attributes %s.', 'cpt-bootstrap-carousel'),'<a href="http://wordpress.org/plugins/cpt-bootstrap-carousel/" target="_blank">', '</a>'); ?></p>
		         
	        <form method="post" action="options.php">
	        <?php
	            settings_fields( 'cptbc_settings' );   
	            do_settings_sections( 'cpt-bootstrap-carousel' );
	            submit_button(); 
	        ?>
	        </form>
	    </div>
	    <?php
	}
    	
	// Register and add settings
	public function page_init() {        
	    register_setting(
	        'cptbc_settings', // Option group
	        'cptbc_settings', // Option name
	        array( $this, 'sanitize' ) // Sanitize
	    );
    	
	    add_settings_section(
	        'cptbc_settings_options', // ID
	        '', // Title - nothing to say here.
	        array( $this, 'cptbc_settings_options_header' ), // Callback
	        'cpt-bootstrap-carousel' // Page
	    );  
    	
	    add_settings_field(
	        'twbs', // ID
	        __('Twitter Bootstrap Version', 'cpt-bootstrap-carousel'), // Title 
	        array( $this, 'twbs_callback' ), // Callback
	        'cpt-bootstrap-carousel', // Page
	        'cptbc_settings_options' // Section           
	    );
    	
	    add_settings_field(
	        'interval', // ID
	        __('Slide Interval (milliseconds)', 'cpt-bootstrap-carousel'), // Title
	        array( $this, 'interval_callback' ), // Callback
	        'cpt-bootstrap-carousel', // Page
	        'cptbc_settings_options' // Section
	    );
		
	    add_settings_field(
	        'showcaption', // ID
	        __('Show Slide Captions?', 'cpt-bootstrap-carousel'), // Title 
	        array( $this, 'showcaption_callback' ), // Callback
	        'cpt-bootstrap-carousel', // Page
	        'cptbc_settings_options' // Section           
	    );
		
	    add_settings_field(
	        'showcontrols', // ID
	        __('Show Slide Controls?', 'cpt-bootstrap-carousel'), // Title 
	        array( $this, 'showcontrols_callback' ), // Callback
	        'cpt-bootstrap-carousel', // Page
	        'cptbc_settings_options' // Section           
	    );
		
	    add_settings_field(
	        'orderby', // ID
	        __('Order Slides By', 'cpt-bootstrap-carousel'), // Title 
	        array( $this, 'orderby_callback' ), // Callback
	        'cpt-bootstrap-carousel', // Page
	        'cptbc_settings_options' // Section           
	    );
		
	    add_settings_field(
	        'order', // ID
	        __('Ordering Direction', 'cpt-bootstrap-carousel'), // Title 
	        array( $this, 'order_callback' ), // Callback
	        'cpt-bootstrap-carousel', // Page
	        'cptbc_settings_options' // Section           
	    );
		
	    add_settings_field(
	        'category', // ID
	        __('Restrict to Category', 'cpt-bootstrap-carousel'), // Title 
	        array( $this, 'category_callback' ), // Callback
	        'cpt-bootstrap-carousel', // Page
	        'cptbc_settings_options' // Section           
	    );
		   
	}
    	
	// Sanitize each setting field as needed -  @param array $input Contains all settings fields as array keys
	public function sanitize( $input ) {
	    $new_input = array();
		foreach($input as $key => $var){
			if($key == 'twbs' || $key == 'interval'){
				$new_input[$key] = absint( $input[$key] );
				if($key == 'interval' && $new_input[$key] == 0){
					$new_input[$key] = 5000;
				}
			} else {
				$new_input[$key] = sanitize_text_field( $input[$key] );
			}
		}
	    return $new_input;
	}
    	
	// Print the Section text
	public function cptbc_settings_options_header() {
	    // nothing to say here.
	}
    	
	public function twbs_callback() {
		if(isset( $this->options['twbs'] ) && $this->options['twbs'] == '3'){
			$cptbc_twbs3 = ' selected="selected"';
			$cptbc_twbs2 = '';
		} else {
			$cptbc_twbs3 = '';
			$cptbc_twbs2 = ' selected="selected"';
		}
		print '<select id="twbs" name="cptbc_settings[twbs]">
			<option value="2"'.$cptbc_twbs2.'>2.x</option>
			<option value="3"'.$cptbc_twbs3.'>3.x</option>
		</select>';
	}
	
	public function interval_callback() {
	    printf('<input type="text" id="interval" name="cptbc_settings[interval]" value="%s" size="6" />',
	        isset( $this->options['interval'] ) ? esc_attr( $this->options['interval']) : '');
	}
	
	public function showcaption_callback() {
		if(isset( $this->options['showcaption'] ) && $this->options['showcaption'] == 'false'){
			$cptbc_showcaption_t = '';
			$cptbc_showcaption_f = ' selected="selected"';
		} else {
			$cptbc_showcaption_t = ' selected="selected"';
			$cptbc_showcaption_f = '';
		}
		print '<select id="showcaption" name="cptbc_settings[showcaption]">
			<option value="true"'.$cptbc_showcaption_t.'>'.__('Show', 'cpt-bootstrap-carousel').'</option>
			<option value="false"'.$cptbc_showcaption_f.'>'.__('Hide', 'cpt-bootstrap-carousel').'</option>
		</select>';
	}
	
	public function showcontrols_callback() {
		if(isset( $this->options['showcontrols'] ) && $this->options['showcontrols'] == 'false'){
			$cptbc_showcontrols_t = '';
			$cptbc_showcontrols_f = ' selected="selected"';
		} else {
			$cptbc_showcontrols_t = ' selected="selected"';
			$cptbc_showcontrols_f = '';
		}
		print '<select id="showcontrols" name="cptbc_settings[showcontrols]">
			<option value="true"'.$cptbc_showcontrols_t.'>'.__('Show', 'cpt-bootstrap-carousel').'</option>
			<option value="false"'.$cptbc_showcontrols_f.'>'.__('Hide', 'cpt-bootstrap-carousel').'</option>
		</select>';
	}
	
	public function orderby_callback() {
		$orderby_options = array (
			'menu_order' => __('Menu order, as set in Carousel overview page', 'cpt-bootstrap-carousel'),
			'date' => __('Date slide was published', 'cpt-bootstrap-carousel'),
			'rand' => __('Random ordering', 'cpt-bootstrap-carousel'),
			'title' => __('Slide title', 'cpt-bootstrap-carousel')			
		);
		print '<select id="orderby" name="cptbc_settings[orderby]">';
		foreach($orderby_options as $val => $option){
			print '<option value="'.$val.'"';
			if(isset( $this->options['orderby'] ) && $this->options['orderby'] == $val){
				print ' selected="selected"';
			}
			print ">$option</option>";
		}
		print '</select>';
	}
	
	public function order_callback() {
		if(isset( $this->options['order'] ) && $this->options['order'] == 'DESC'){
			$cptbc_showcontrols_a = '';
			$cptbc_showcontrols_d = ' selected="selected"';
		} else {
			$cptbc_showcontrols_a = ' selected="selected"';
			$cptbc_showcontrols_d = '';
		}
		print '<select id="order" name="cptbc_settings[order]">
			<option value="ASC"'.$cptbc_showcontrols_a.'>'.__('Ascending', 'cpt-bootstrap-carousel').'</option>
			<option value="DESC"'.$cptbc_showcontrols_d.'>'.__('Decending', 'cpt-bootstrap-carousel').'</option>
		</select>';
	}
	
	public function category_callback() {
		$cats = get_terms('carousel_category');
		print '<select id="orderby" name="cptbc_settings[category]">
			<option value="">'.__('All Categories', 'cpt-bootstrap-carousel').'</option>';
		foreach($cats as $cat){
			print '<option value="'.$cat->term_id.'"';
			if(isset( $this->options['category'] ) && $this->options['category'] == $cat->term_id){
				print ' selected="selected"';
			}
			print ">".$cat->name."</option>";
		}
		print '</select>';
	}
    	
	
}

if( is_admin() ){
    $cptbc_settings_page = new cptbc_settings_page();
}

// Add settings link on plugin page
function cptbc_settings_link ($links) { 
	$settings_link = '<a href="edit.php?post_type=cptbc&page=cpt-bootstrap-carousel">'.__('Settings', 'cpt-bootstrap-carousel').'</a>'; 
	array_unshift($links, $settings_link); 
	return $links; 
}
$cptbc_plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$cptbc_plugin", 'cptbc_settings_link' );






///////////////////
// FRONT END
///////////////////

// Shortcode
function cptbc_shortcode($atts, $content = null) {
    // Set default shortcode attributes
	$options = get_option( 'cptbc_settings' );
	if(!$options){
		cptbc_set_options ();
		$options = get_option( 'cptbc_settings' );
	}
	$options['id'] = '';

	// Parse incomming $atts into an array and merge it with $defaults
	$atts = shortcode_atts($options, $atts);

	return cptbc_frontend($atts);
}
add_shortcode('image-carousel', 'cptbc_shortcode');

// Display carousel
function cptbc_frontend($atts){
	$id = rand(0, 999); // use a random ID so that the CSS IDs work with multiple on one page
	$args = array( 'post_type' => 'cptbc', 'posts_per_page' => '-1', 'orderby' => $atts['orderby'], 'order' => $atts['order']);
	if($atts['category'] != ''){
		$args['carousel_category'] = $atts['category'];
	}
	if($atts['id'] != ''){
		$args['p'] = $atts['id'];
	}
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
			<?php if($atts['showcontrols'] === 'true' && $atts['twbs'] == '3') { ?>
				<a class="left carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="prev"><span class="icon-prev"></span></a>
				<a class="right carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="next"><span class="icon-next"></span></a>
			<?php } else if($atts['showcontrols'] === 'true'){ ?>
				<a class="left carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="prev">‹</a>
				<a class="right carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="next">›</a>
			<?php } ?>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#cptbc_<?php echo $id; ?>').carousel({
					interval: <?php echo $atts['interval']; ?>
				});
			});
		</script>
<?php }
	$output = ob_get_contents();
	ob_end_clean();
	
	// Restore original Post Data
	wp_reset_postdata();	
	
	return $output;
}

?>