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
	$options['pause'] = 'hover'; // used for data-pause flag
	$options['fill'] = 'false';  // full height carousel from div position y
	$options['fill_min'] = '400'; // the minimum carousel size

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
		$args['tax_query'][] = array(
			'taxonomy'  => 'carousel_category',
			'field'     => 'slug',
			'terms'     => $atts['category'],
			'operator'  => 'IN'
		);
	}
	if(!isset($atts['before_title'])) $atts['before_title'] = '<h4>';
	if(!isset($atts['after_title'])) $atts['after_title'] = '</h4>';
	if(!isset($atts['before_caption'])) $atts['before_caption'] = '<p>';
	if(!isset($atts['after_caption'])) $atts['after_caption'] = '</p>';
	if(!isset($atts['image_size'])) $atts['image_size'] = 'full';
	if(!isset($atts['use_background_images'])) $atts['use_background_images'] = '0';
	if(!isset($atts['use_javascript_animation'])) $atts['use_javascript_animation'] = '1';
    if(!isset($atts['select_background_images_style_size'])) $atts['select_background_images_style_size'] = 'cover';
    if(!isset($atts['media_image_size'])) $atts['media_image_size'] = 'none'; // the image-size for @media(max-width)
    if(!isset($atts['media_max_width'])) $atts['media_max_width'] = '992'; // the max-width for @media

	if($atts['id'] != ''){
		$args['p'] = $atts['id'];
	}

	// Collect the carousel content. Needs printing in two loops later (bullets and content)
	$loop = new WP_Query( $args );
	$images = array();
	$output = '';
	$images_css_media = array();

	// we need an internal style for @media
	$output_css = '<style type="text/css">';
	$output_css_media = '@media (max-width: ' . $atts['media_max_width'] . 'px) {';
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

			// generate the style
			if($atts['use_background_images'] == 1) {
				// for image-size
				$output_css .= '#cptbc-item-' . $post_id . '{ height: ' . $atts['background_images_height'] . 'px; background: url(\'' . $image_src . '\') no-repeat center center ; -webkit-background-size: ' . $atts['select_background_images_style_size'] . '; -moz-background-size: ' . $atts['select_background_images_style_size'] . '; -o-background-size: ' . $atts['select_background_images_style_size'] . '; background-size: ' . $atts['select_background_images_style_size'] . '; }';
			

				if ($atts['media_image_size'] != 'none' && '' != get_the_post_thumbnail(get_the_ID(), $atts['media_image_size']) ) {	
					// for media_image_size		
					$image = get_the_post_thumbnail( get_the_ID(), $atts['media_image_size'] );
					$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), $atts['media_image_size']);
					$image_src = $image_src[0];
					$url = get_post_meta(get_the_ID(), 'cptbc_image_url', true);
					$url_openblank = get_post_meta(get_the_ID(), 'cptbc_image_url_openblank', true);
					$link_text = get_post_meta(get_the_ID(), 'cptbc_image_link_text', true);
					$images_css_media[] = array('post_id' => $post_id, 'title' => $title, 'content' => $content, 'image' => $image, 'img_src' => $image_src, 'url' => esc_url($url), 'url_openblank' => $url_openblank == "1" ? true : false, 'link_text' => $link_text);
					
					$output_css_media .= '#cptbc-item-' . $post_id. '{ height: ' . $atts['background_images_height'] . 'px; background: url(\'' . $image_src . '\') no-repeat center center ; -webkit-background-size: ' . $atts['select_background_images_style_size'] . '; -moz-background-size: ' . $atts['select_background_images_style_size'] . '; -o-background-size: ' . $atts['select_background_images_style_size'] . '; background-size: ' . $atts['select_background_images_style_size'] . '; }';
				}
			}
		}
	}

	// Check we actually have something to show
	if(count($images) > 0){
		ob_start();
		?>
		<div id="cptbc_<?php echo $id; ?>" class="carousel slide" <?php if($atts['use_javascript_animation'] == '0'){ echo ' data-ride="carousel"'; } ?> data-interval="<?php echo $atts['interval']; ?>" data-pause="<?php echo $atts['pause']; ?>">
			
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
				
				if( !isset($atts['link_button']) ) {
					$atts['link_button'] = 0;
				}
				
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

				<div class="<?php echo $atts['twbs'] == '4' ? 'carousel-' : ''; ?>item <?php echo $key == 0 ? 'active' : ''; ?>" id="cptbc-item-<?php echo $image['post_id']; ?>">
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
					if(($atts['showtitle'] === 'true' && strlen($image['title']) > 0) || ($atts['showcaption'] === 'true' && strlen($image['content']) > 0) || ($image['url'] && $atts['link_button'] == 1))  {
						echo '<div class="carousel-caption">';
						// Title
						if($atts['showtitle'] === 'true' && strlen($image['title']) > 0){
							echo $atts['before_title'].$linkstart.$image['title'].$linkend.$atts['after_title'];
						}
						// Caption
						if($atts['showcaption'] === 'true' && strlen($image['content']) > 0){
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
				if($atts['showcontrols'] === 'true' && ($atts['twbs'] == '3' || $atts['twbs'] == '4')) { ?>
					<a class="left carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
					<a class="right carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
				<?php } else if($atts['showcontrols'] === 'true'){ ?>
					<a class="left carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="prev">‹</a>
					<a class="right carousel-control" href="#cptbc_<?php echo $id; ?>" data-slide="next">›</a>
				<?php } else if($atts['showcontrols'] === 'custom' && ($atts['twbs'] == '3' || $atts['twbs'] == '4') &&  $atts['customprev'] != '' &&  $atts['customnext'] != ''){ ?>
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

        // Javascript fill page carousel
        if ($atts['fill'] == 'true')
        {
        	?>
        	<script>
				jQuery(window).resize(function() {
					carouselToFullpage<?php echo $id; ?>();
				});

				function carouselToFullpage<?php echo $id; ?>()
				{	
					var restHeight = jQuery(window).height() - jQuery('#cptbc_<?php echo $id; ?>').offset().top;

					restHeight = restHeight < <?php echo $atts['fill_min']; ?> ? <?php echo $atts['fill_min']; ?> : restHeight;

					var items = jQuery("[id^=cptbc-item-]")
					for (var i = 0; i < items.length; i++)
					{
						jQuery(items[i]).css("height", restHeight+"px");
					}
				}
				jQuery(document).ready(carouselToFullpage<?php echo $id; ?>);
			</script>
        	<?php
        }

        // Collect the output
		$output = ob_get_contents();
		ob_end_clean();

		// build all together
		$output_css = $output_css . $output_css_media . '}</style>';
		$output = $output_css . $output;

	} else {
		$output = '<!-- CPT Bootstrap Carousel - no images found for #cptbc_'.$id.' -->';
	}

	// Restore original Post Data
	wp_reset_postdata();  
	
	return $output;
}

