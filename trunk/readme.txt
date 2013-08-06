=== CPT Bootstrap Carousel ===
Contributors: tallphil, joshgerdes, atnon
Donate Link: http://phil.ewels.co.uk
Tags: carousel, slider, image, bootstrap
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A custom post type for choosing images and content which outputs Bootstrap Image Carousel (slider) from the [image-carousel] shortcode.

== Description ==

A custom post type for choosing images and content which outputs a [carousel](http://getbootstrap.com/javascript/#carousel) from [Twitter Bootstrap](http://www.getbootstrap.com) using the shortcode `[image-carousel]`. 

The plugin assumes that you're already using Bootstrap, so you need to load the Bootstrap javascript and CSS separately.

* [Download Twitter Bootstrap](http://getbootstrap.com/)
* [Bootstrap WordPress Theme](http://320press.com/wpbs/)
* [Bootstrap CDN](http://www.bootstrapcdn.com/) _(hotlink CSS and javascript files)_
* [Bootstrap Carousel in action](http://getbootstrap.com/javascript/#carousel)

The carousel can be tweaked using shortcode attributes, eg: `[image-carousel interval="8000" showcaption="false" showcontrols="false"]`

I may consider adding an option to load the Bootstrap files in the future if there is demand. Let me know if you'd like it!

If you'd like to contribute to this plugin, you can find it [hosted on GitHub](https://github.com/tallphil/cpt-bootstrap-carousel).

== Installation ==

1. Upload the `cpt-bootstrap-carousel` folder to the `/wp-content/plugins/` directory
1. Activate the `cpt-bootstrap-carousel` plugin through the 'Plugins' menu in WordPress
1. Make sure that your theme is loading the [Twitter Bootstrap](http://www.getbootstrap.com) CSS and Carousel javascript
1. Place the `[image-carousel]` shortcode in a Page or Post
1. Create new items in the `Carousel` post type, uploading a Featured Image for each.
	1. *Optional:* You can hyperlink each image by entering the desired url `Image Link URL` admin metabox when adding a new carousel image.

= Shortcode Options =
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
1. `twbs` _(default 2)_
	* Output markup for Twitter Bootstrap Version 2 or 3.

For example, to display a carousel with no captions or controls, use the following:
`[image-carousel showcaption="false" showcontrols="false"]`

To display a carousel which pauses for 8 seconds and shows images in a random order:
`[image-carousel interval="8000" orderby="rand"]`

To display a carousel with images from the `global` and `home` categories, with markup for Twitter Bootstrap v3.x:
`[image-carousel category="global,home" twbs="3"]`


== Frequently Asked Questions ==

= How do I insert the carousel? =

First of all, install and activate the plugin. Go to 'Carousel' in the WordPress admin pages and add some images. Then, insert the carousel using the `[image-carousel]` into the body of any page.

= Can I customise the way it looks? =

The carousel shortcode has a number of attributes that you can use to customise the output. These are described on the [Installation](http://wordpress.org/plugins/cpt-bootstrap-carousel/installation/) page.

= Can I insert the carousel into a WordPress template instead of a page? =

Absolutely - you just need to use the [do_shortcode](http://codex.wordpress.org/Function_Reference/do_shortcode) WordPress function. For example:
`<?php echo do_shortcode('[image-carousel]'); ?>`

= Can I change the order that the images display in? =

You can specify the order that the carousel displays images by using the `orderby` and `order` shortcode attributes. You can use any terms described for the [WP_Query orderby terms](http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters), such as random, by date, by title and by menu order.

= Can I have different carousels with different images on the same site? =

Yes - create a few categories and add your specific images to a specific category. Then, when inserting the shortcode into the page, specify which category you want it to display images from using the `category` shortcode attribute.

= Help! Nothing is showing up at all =

1. Is the plugin installed and activated?
1. Have you added any items in the `Carousel` post type?
1. Have you placed the `[image-carousel]` shortcode in your page?

Try writing the shortcode using the 'Text' editor instead of the 'Visual' editor, as the visual editor can sometimes add extra unwanted markup.

= My images are showing but they're all over the place =

1. Is your theme loading the Bootstrap CSS and Javascript? _(look for `bootstrap.css` in the source HTML)_

= The carousel makes the content jump each time it changes =

1. You need to make sure that each image is the same height. You can do this by setting an `Aspect ratio` in the `Edit Image` section of the WordPress Media Library and cropping your images.

== Screenshots ==

1. Admin list interface showing Carousel images and titles.
2. Admin image interface showing optional title and caption (Excerpt) fields, along with Category, order, image and URL
3. Example output. Requires Bootstrap CSS and Javascript to be loaded (see documentation).

== Changelog ==

= 1.3 =
* Added support for carousel categories, using filtering with the `category` shortcode
* Added `orderby` shortcode attribute to specify ordering of images
	* This means that images can now be in a random order
* Added `twbs` shortcode attribute to allow the output of Twitter Bootstrap v3 markup
* Added WordPress directory screenshots
* Admin thumbnail images now link to the edit page

= 1.2 =
* Featured images are now shown in the admin list view
* Added new admin metabox for image url (written by @tallphil, based on code contributed by @atnon)

= 1.1 =
* Added shortcode attributes (code contributed by @joshgerdes)

= 1.0 =
* Initial release

 == Upgrade Notice ==

= 1.3 =
* A bunch of requested features have been added - mainly random ordering and categories. See the changelog for more details.

= 1.2 =
* Featured images are now shown in the admin list view.
** Note: This update creates a new thumbnail size. I recommend using the [Regenerate Thumbnails](http://wordpress.org/plugins/regenerate-thumbnails/) WordPress plugin to regenerate all of your image thumbnails.
* Added a new admin metabox to give each image a link (optional).

= 1.1 =
* Added shortcode attribute functionality for tweaking of carousel options.