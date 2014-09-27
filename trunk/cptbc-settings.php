<?php
/*****************************************************
* CPT Bootstrap Carousel
* http://www.tallphil.co.uk/bootstrap-carousel/
* ----------------------------------------------------
* cptbc-settings.php
* Code to handle the Settings page
******************************************************/

///////////////////
// SETTINGS PAGE
///////////////////

// Set up settings defaults
register_activation_hook(__FILE__, 'cptbc_set_options');
function cptbc_set_options (){
	$defaults = array(
		'interval' => '5000',
		'showcaption' => 'true',
		'showcontrols' => 'true',
		'customprev' => '',
		'customnext' => '',
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'category' => '',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
		'before_caption' => '<p>',
		'after_caption' => '</p>',
		'image_size' => 'full',
		'id' => '',
		'twbs' => '3',
		'use_background_images' => '0',
		'background_images_height' => '500',
        'use_javascript_animation' => '1'
	);
	add_option('cptbc_settings', $defaults);
}
// Clean up on uninstall
register_activation_hook(__FILE__, 'cptbc_deactivate');
function cptbc_deactivate(){
	delete_option('cptbc_settings');
}


// Render the settings page
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
		<h2>CPT Bootstrap Carousel <?php _e('Settings', 'cpt-bootstrap-carousel'); ?></h2>
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
				'customprev', // ID
				__('Custom prev button class', 'cpt-bootstrap-carousel'), // Title
				array( $this, 'customprev_callback' ), // Callback
				'cpt-bootstrap-carousel', // Page
				'cptbc_settings_options' // Section
		);
		
		add_settings_field(
				'customnext', // ID
				__('Custom next button class', 'cpt-bootstrap-carousel'), // Title
				array( $this, 'customnext_callback' ), // Callback
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
		
		add_settings_field(
				'before_title', // ID
				__('HTML before title', 'cpt-bootstrap-carousel'), // Title
				array( $this, 'before_title_callback' ), // Callback
				'cpt-bootstrap-carousel', // Page
				'cptbc_settings_options' // Section
		);
		
		add_settings_field(
				'after_title', // ID
				__('HTML after title', 'cpt-bootstrap-carousel'), // Title
				array( $this, 'after_title_callback' ), // Callback
				'cpt-bootstrap-carousel', // Page
				'cptbc_settings_options' // Section
		);
		
		add_settings_field(
				'before_caption', // ID
				__('HTML before caption text', 'cpt-bootstrap-carousel'), // Title
				array( $this, 'before_caption_callback' ), // Callback
				'cpt-bootstrap-carousel', // Page
				'cptbc_settings_options' // Section
		);
		
		add_settings_field(
				'after_caption', // ID
				__('HTML after caption text', 'cpt-bootstrap-carousel'), // Title
				array( $this, 'after_caption_callback' ), // Callback
				'cpt-bootstrap-carousel', // Page
				'cptbc_settings_options' // Section
		);
		
		add_settings_field(
				'image_size', // ID
				__('Image Size', 'cpt-bootstrap-carousel'), // Title 
				array( $this, 'image_size_callback' ), // Callback
				'cpt-bootstrap-carousel', // Page
				'cptbc_settings_options' // Section		   
		);
		
		add_settings_field(
				'use_background_images', // ID
				__('Use background images?', 'cpt-bootstrap-carousel'), // Title 
				array( $this, 'use_background_images_callback' ), // Callback
				'cpt-bootstrap-carousel', // Page
				'cptbc_settings_options' // Section		   
		);
		
		add_settings_field(
				'background_images_height', // ID
				__('Height if using bkgrnd images (px)', 'cpt-bootstrap-carousel'), // Title
				array( $this, 'background_images_height_callback' ), // Callback
				'cpt-bootstrap-carousel', // Page
				'cptbc_settings_options' // Section
		);
        
		add_settings_field(
				'use_javascript_animation', // ID
				__('Use Javascript to animate carousel?', 'cpt-bootstrap-carousel'), // Title 
				array( $this, 'use_javascript_animation_callback' ), // Callback
				'cpt-bootstrap-carousel', // Page
				'cptbc_settings_options' // Section		   
		);
			 
	}
			
	// Sanitize each setting field as needed -  @param array $input Contains all settings fields as array keys
	public function sanitize( $input ) {
		$new_input = array();
		foreach($input as $key => $var){
			if($key == 'twbs' || $key == 'interval' || $key == 'background_images_height'){
				$new_input[$key] = absint( $input[$key] );
				if($key == 'interval' && $new_input[$key] == 0){
					$new_input[$key] = 5000;
				}
			} else if ($key == 'before_title' || $key == 'after_title' || $key == 'before_caption' || $key == 'after_caption'){
				$new_input[$key] = $input[$key]; // Don't sanitise these, meant to be html!
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
			$cptbc_showcontrols_c = '';
		} else if(isset( $this->options['showcontrols'] ) && $this->options['showcontrols'] == 'true'){
			$cptbc_showcontrols_t = ' selected="selected"';
			$cptbc_showcontrols_f = '';
			$cptbc_showcontrols_c = '';
		} else if(isset( $this->options['showcontrols'] ) && $this->options['showcontrols'] == 'custom'){
			$cptbc_showcontrols_t = '';
			$cptbc_showcontrols_f = '';
			$cptbc_showcontrols_c = ' selected="selected"';
		}
		print '<select id="showcontrols" name="cptbc_settings[showcontrols]">
			<option value="true"'.$cptbc_showcontrols_t.'>'.__('Show', 'cpt-bootstrap-carousel').'</option>
			<option value="false"'.$cptbc_showcontrols_f.'>'.__('Hide', 'cpt-bootstrap-carousel').'</option>
			<option value="custom"'.$cptbc_showcontrols_c.'>'.__('Custom', 'cpt-bootstrap-carousel').'</option>
		</select>';
	}
	
	public function customnext_callback() {
			printf('<input type="text" id="customnext" name="cptbc_settings[customnext]" value="%s" size="6" />',
					isset( $this->options['customnext'] ) ? esc_attr( $this->options['customnext']) : '');
	}
	
	public function customprev_callback() {
			printf('<input type="text" id="customprev" name="cptbc_settings[customprev]" value="%s" size="6" />',
					isset( $this->options['customprev'] ) ? esc_attr( $this->options['customprev']) : '');
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
			print '<option value="'.$cat->name.'"';
			if(isset( $this->options['category'] ) && $this->options['category'] == $cat->name){
				print ' selected="selected"';
			}
			print ">".$cat->name."</option>";
		}
		print '</select>';
	}
	
	public function before_title_callback() {
			printf('<input type="text" id="before_title" name="cptbc_settings[before_title]" value="%s" size="6" />',
					isset( $this->options['before_title'] ) ? esc_attr( $this->options['before_title']) : '<h4>');
	}
	
	public function after_title_callback() {
			printf('<input type="text" id="after_title" name="cptbc_settings[after_title]" value="%s" size="6" />',
					isset( $this->options['after_title'] ) ? esc_attr( $this->options['after_title']) : '</h4>');
	}
	
	public function before_caption_callback() {
			printf('<input type="text" id="before_caption" name="cptbc_settings[before_caption]" value="%s" size="6" />',
					isset( $this->options['before_caption'] ) ? esc_attr( $this->options['before_caption']) : '<p>');
	}
	
	public function after_caption_callback() {
			printf('<input type="text" id="after_caption" name="cptbc_settings[after_caption]" value="%s" size="6" />',
					isset( $this->options['after_caption'] ) ? esc_attr( $this->options['after_caption']) : '</p>');
	}
	
	public function image_size_callback() {
		$image_sizes = get_intermediate_image_sizes();
		print '<select id="image_size" name="cptbc_settings[image_size]">
			<option value="full"';
			if(isset( $this->options['image_size'] ) && $this->options['image_size'] == 'full'){
				print ' selected="selected"';
			}
			echo '>Full (default)</option>';
		foreach($image_sizes as $size){
			print '<option value="'.$size.'"';
			if(isset( $this->options['image_size'] ) && $this->options['image_size'] == $size){
				print ' selected="selected"';
			}
			print ">".ucfirst($size)."</option>";
		}
		print '</select>';
	}
	
	public function use_background_images_callback() {
		print '<select id="use_background_images" name="cptbc_settings[use_background_images]">';
		print '<option value="0"';
		if(isset( $this->options['use_background_images'] ) && $this->options['use_background_images'] == 0){
			print ' selected="selected"';
		}
		echo '>No (default)</option>';
		print '<option value="1"';
		if(isset( $this->options['use_background_images'] ) && $this->options['use_background_images'] == 1){
			print ' selected="selected"';
		}
		echo '>Yes</option>';
		print '</select>';
	}
	
	public function background_images_height_callback() {
		printf('<input type="text" id="background_images_height" name="cptbc_settings[background_images_height]" value="%s" size="6" />',
				isset( $this->options['background_images_height'] ) ? esc_attr( $this->options['background_images_height']) : '500px');
	}
    
	public function use_javascript_animation_callback() {
		print '<select id="use_javascript_animation" name="cptbc_settings[use_javascript_animation]">';
		print '<option value="1"';
		if(isset( $this->options['use_javascript_animation'] ) && $this->options['use_javascript_animation'] == 1){
			print ' selected="selected"';
		}
		echo '>Yes (default)</option>';
		print '<option value="0"';
		if(isset( $this->options['use_javascript_animation'] ) && $this->options['use_javascript_animation'] == 0){
			print ' selected="selected"';
		}
		echo '>No</option>';
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

?>
