CPT Bootstrap Carousel
======================

A custom post type for choosing images and content which outputs Bootstrap Image Carousel (slider) from the `[image-carousel]` shortcode.

The plugin assumes that you're already using Bootstrap, so you need to load the Bootstrap javascript and CSS separately.

* [Download Twitter Bootstrap](http://twitter.github.io/bootstrap/index.html)
* [Bootstrap WordPress Theme](http://320press.com/wpbs/)
* [Bootstrap CDN](http://www.bootstrapcdn.com/) _(hotlink CSS and javascript files)_
* [Bootstrap Carousel in action](http://twitter.github.io/bootstrap/examples/carousel.html)

I may consider adding an option to load the Bootstrap files in the future if there is demand. Let me know if you'd like it!

Also hosted on the [WordPress Plugins Directory](http://wordpress.org/support/view/plugin-reviews/cpt-bootstrap-carousel).

Installation
------------

1. Upload the `cpt-bootstrap-carousel` folder to the `/wp-content/plugins/` directory
1. Activate the `cpt-bootstrap-carousel` plugin through the 'Plugins' menu in WordPress
1. Make sure that your theme is loading the [Twitter Bootstrap](http://www.getbootstrap.com) CSS and Carousel javascript
1. Place the `[image-carousel]` shortcode in a Page or Post
1. Create new items in the `Carousel` post type, uploading a Featured Image for each.
	1. *Optional:* You can hyperlink each image by entering the desired url `Image Link URL` admin metabox when adding a new carousel image.

Shortcode Options
-----------------
You can specify how long the carousel pauses for, and whether to display captions and the controls using optional
shortcode attributes:

1. `interval` _(default 5000)_
    * Length of time for the caption to pause on each image. Time in milliseconds.
1. `showcaption` _(default true)_
    * Whether to display the text caption on each image or not. `true` or `false`.
1. `showcontrols` _(default true)_
    * Whether to display the control arrows or not. `true` or `false`.
1. `orderby` and `order` _(default `menu_order` `ASC`)_
	* What order to display the posts in. Uses [WP_Query terms](http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters).
1. `category` _(default all)_
	* Filter carousel items by a comma separated list of carousel category slugs.
		
For example, to display a carousel with images from the `global` and `home` categories, with no captions, no controls and pausing for eight seconds with images in a random order, use the following:
`[image-carousel interval="8000" showcaption="false" showcontrols="false" orderby="rand" category="global,home"]`


Frequently Asked Questions
--------------------------

**Nothing is showing up at all**

1. Is the plugin installed and activated?
1. Have you added any items in the `Carousel` post type?
1. Have you placed the `[image-carousel]` shortcode in your page?

**My images are showing but they're all over the place**

1. Is your theme loading the Bootstrap CSS and Javascript? _(look for `bootstrap.css` in the source HTML)_

**The carousel makes the content jump each time it changes**

1. You need to make sure that each image is the same height. You can do this by setting an `Aspect ratio` in the `Edit Image` section of the WordPress Media Library and cropping your images.

Changelog
---------
* __1.3__
	* Added support for carousel categories, using filtering with the `category` shortcode
	* Added shortcode attribute to specify ordering of images
		* This means that images can now be in a random order
	* Admin thumbnail images now link to the edit page
* __1.2__
	* Featured images are now shown in the admin list view
		* Note: This update creates a new thumbnail size. I recommend using the [Regenerate Thumbnails](http://wordpress.org/plugins/regenerate-thumbnails/) WordPress plugin to regenerate all of your image thumbnails.
	* Added new admin metabox for image url (written by @tallphil, based on code contributed by @atnon)
* __1.1__
    * Added shortcode attributes (code contributed by @joshgerdes)
* __1.0__
	* Initial release