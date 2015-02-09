<?php
/*****************************************************
* CPT Bootstrap Carousel
* http://www.tallphil.co.uk/bootstrap-carousel/
* ----------------------------------------------------
* cptbc-frontend.php
* Code to handle front-end rendering of the carousel
******************************************************/

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

	// Build the attributes
	$id = rand(0, 999); // use a random ID so that the CSS IDs work with multiple on one page
	$args = array(
		'post_type' => 'cptbc',
		'posts_per_page' => '-1',
		'orderby' => $atts['orderby'],
		'order' => $atts['order']
	);
	if($atts['category'] != ''){
		$args['carousel_category'] = $atts['category'];
	}
	if(!isset($atts['before_title'])) $atts['before_title'] = '<h4>';
	if(!isset($atts['after_title'])) $atts['after_title'] = '</h4>';
	if(!isset($atts['before_caption'])) $atts['before_caption'] = '<p>';
	if(!isset($atts['after_caption'])) $atts['after_caption'] = '</p>';
	if(!isset($atts['image_size'])) $atts['image_size'] = 'full';
	if(!isset($atts['use_background_images'])) $atts['use_background_images'] = '0';
	if(!isset($atts['use_javascript_animation'])) $atts['use_javascript_animation'] = '1';
    if(!isset($atts['select_background_images_style_size'])) $atts['select_background_images_style_size'] = 'cover';
	if($atts['id'] != ''){
		$args['p'] = $atts['id'];
	}

	// Collect the carousel content. Needs printing in two loops later (bullets and content)
	$loop = new WP_Query( $args );
	$images = array();
	$output = '';
	while ( $loop->have_posts() ) {
		$loop->the_post();
		if ( '' != get_the_post_thumbnail(get_the_ID(), $atts['image_size']) ) {
			$post_id = get_the_ID();
			$title = get_the_title();
			$content = get_the_excerpt();
			$image = get_the_post_thumbnail( get_the_ID(), $atts['image_size'] );
			$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), $atts['image_size']);
			$image_src = $image_src[0];
			$url = get_post_meta(get_the_ID(), 'cptbc_image_url', true);
			$url_openblank = get_post_meta(get_the_ID(), 'cptbc_image_url_openblank', true);
			$link_text = get_post_meta(get_the_ID(), 'cptbc_image_link_text', true);
			$images[] = array('post_id' => $post_id, 'title' => $title, 'content' => $content, 'image' => $image, 'img_src' => $image_src, 'url' => esc_url($url), 'url_openblank' => $url_openblank == "1" ? true : false, 'link_text' => $link_text);
		}
	}

	// Check we actually have something to show
	if(count($images) > 0){
		ob_start();
		?>
		<div id="cptbc_<?php echo $id; ?>" class="carousel slide" <?php if($atts['use_javascript_animation'] == '0'){ echo ' data-ride="carousel"'; } ?> data-interval="<?php echo $atts['interval']; ?>">
			
			<?php // First content - the carousel indicators
			if( count( $images ) > 1 ){ ?>
				<ol class="carousel-indicators">
				<?php foreach ($images as $key => $image) { ?>
					<li data-target="#cptbc_<?php echo $id; ?>" data-slide-to="<?php echo $key; ?>" <?php echo $key == 0 ? 'class="active"' : ''; ?>></li>
				<?php } ?>
				</ol>
			<?php } ?>

			<div class="carousel-inner">
			<?php
			// Carousel Content
			foreach ($images as $key => $image) {

				// Build anchor link so it can be reused
				$linkstart = '';
				$linkend = '';
				if($image['url'] && $atts['link_button'] == 0) {
					$linkstart = '<a href="'.$image['url'].'"';
					if($image['url_openblank']) {
						$linkstart .= ' target="_blank"';
					}
					$linkstart .= '>';
					$linkend = '</a>';
				} ?>

				<div class="item <?php echo $key == 0 ? 'active' : ''; ?>" id="<?php echo $image['post_id']; ?>" <?php if($atts['use_background_images'] == 1){ echo ' style="height: '.$atts['background_images_height'].'px; background: url(\''.$image['img_src'].'\') no-repeat center center ; -webkit-background-size: ' . $atts['select_background_images_style_size'] . '; -moz-background-size: ' . $atts['select_background_images_style_size'] . '; -o-background-size: ' . $atts['select_background_images_style_size'] . '; background-size: ' . $atts['select_background_images_style_size'] . ';"'; } ?>>
					<?php
					// Regular behaviour - display image with link around it
					if($atts['use_background_images'] == 0){
						echo $linkstart.$image['image'].$linkend;
					// Backgorund images mode - need block level link inside carousel link if we have a linl
					} else if($image['url'] && $atts['link_button'] == 0) {
						echo '<a href="'.$image['url'].'"';
						if($image['url_openblank']) {
							$linkstart .= ' target="_blank"';
						}
						echo ' style="display:block; width:100%; height:100%;">&nbsp;</a>';
					} 
					// The Caption div
					if(($atts['showcaption'] === 'true' && strlen($image['title']) > 0 && strlen($image['content']) > 0) || ($image['url'] && $atts['link_button'] == 1))  {
						echo '<div class="carousel-caption">';
						// Title
						if(strlen($image['title']) > 0){
							echo $atts['before_title'].$linkstart.$image['title'].$linkend.$atts['after_title'];
						}
						// Caption
						if(strlen($image['content']) > 0){
							echo $atts['before_caption'].$linkstart.$image['content'].$linkend.$atts['after_caption'];
						}
						// Link Button
						if($image['url'] && $atts['link_button'] == 1){ 
							if(isset($atts['link_button_before'])) echo $atts['link_button_before'];
							$target = '';
							if($image['url_openblank']) {
								$target = ' target="_blank"';
							}
							echo '<a href="'.$image['url'].'" '.$target.' class="'.$atts['link_button_class'].'">';
							if(isset($image['link_text']) && strlen($image['link_text']) > 0) {
								echo $image['link_text'];
							} else {
								echo $atts['link_button_text'];
							}
							echo '</a>';
							if(isset($atts['link_button_after'])) echo $atts['link_button_after'];
						}
						echo '</div>';
					} ?>
				</div>
			<?php } ?>
			</div>

			<?php // Previous / Next controls
			if( count( $images ) > 1 ){
				if($atts['showcontrols'] === 'true' && $atts['twbs'] == '3') { ?>
					<a class="left carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
					<a class="right carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
				<?php } else if($atts['showcontrols'] === 'true'){ ?>
					<a class="left carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="prev">‹</a>
					<a class="right carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="next">›</a>
				<?php } else if($atts['showcontrols'] === 'custom' && $atts['twbs'] == '3' &&  $atts['customprev'] != '' &&  $atts['customnext'] != ''){ ?>
					<a class="left carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="prev"><span class="<?php echo $atts['customprev'] ?> icon-prev"></span></a>
					<a class="right carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="next"><span class="<?php echo $atts['customnext'] ?> icon-next"></span></a>
				<?php }
			} ?>

		</div>

        <?php // Javascript animation fallback
        if($atts['use_javascript_animation'] == '1'){ ?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#cptbc_<?php echo $id; ?>').carousel({
					interval: <?php echo $atts['interval']; ?>
				});
			});
		</script>
        <?php }

        // Collect the output
		$output = ob_get_contents();
		ob_end_clean();
	}
	
	// Restore original Post Data
	wp_reset_postdata();  
	
	return $output;
}

