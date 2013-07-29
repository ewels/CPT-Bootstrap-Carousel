=== CPT Bootstrap Carousel ===
Contributors: tallphil, joshgerdes, atnon
Donate Link: http://phil.ewels.co.uk
Tags: carousel, slider, image, bootstrap
Requires at least: 3.0.1
Tested up to: 3.5.1
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A custom post type for choosing images and content which outputs Bootstrap Image Carousel (slider) from the [image-carousel] shortcode.

== Description ==

A custom post type for choosing images and content which outputs a [carousel](http://twitter.github.io/bootstrap/javascript.html#carousel) from [Twitter Bootstrap](http://www.getbootstrap.com) using the shortcode `[image-carousel]`. 

The plugin assumes that you're already using Bootstrap, so you need to load the Bootstrap javascript and CSS separately.

* [Download Twitter Bootstrap](http://twitter.github.io/bootstrap/index.html)
* [Bootstrap WordPress Theme](http://320press.com/wpbs/)
* [Bootstrap CDN](http://www.bootstrapcdn.com/) _(hotlink CSS and javascript files)_
* [Bootstrap Carousel in action](http://twitter.github.io/bootstrap/examples/carousel.html)

The carousel can be tweaked using shortcode attributes, eg: `[image-carousel interval="8000" showcaption="false" showcontrols="false"]`

I may consider adding an option to load the Bootstrap files in the future if there is demand. Let me know if you'd like it!

If you'd like to contribute to this plugin, you can find it [hosted on GitHub](https://github.com/tallphil/cpt-bootstrap-carousel).

== Installation ==

1. Upload the `cpt-bootstrap-carousel` folder to the `/wp-content/plugins/` directory
1. Activate the `cpt-bootstrap-carousel` plugin through the 'Plugins' menu in WordPress
1. Make sure that your theme is loading the [Twitter Bootstrap](http://www.getbootstrap.com) CSS and Carousel javascript
1. Place the `[image-carousel]` shortcode in a Page or Post
1. Create new items in the `Carousel` post type, uploading a Featured Image for each.

= Shortcode Options =
You can specify how long the carousel pauses for, and whether to display captions and the controls using optional
shortcode attributes:

1. `interval` _(default 5000)_
    * Length of time for the caption to pause on each image. Time in milliseconds.
1. `showcaption` _(default true)_
    * Whether to display the text caption on each image or not. `true` or `false`.
1. `showcontrols` _(default true)_
    * Whether to display the control arrows or not. `true` or `false`.

For example, to display the carousel with no captions, no controls and pausing for eight seconds, use the following:
`[image-carousel interval="8000" showcaption="false" showcontrols="false"]`

= Other Options = 
You may assign an url link to an image by adding the custom field `url` and entering the wanted url as key when adding a new carousel image.

== Frequently Asked Questions ==

= Nothing is showing up at all =

1. Is the plugin installed and activated?
1. Have you added any items in the `Carousel` post type?
1. Have you placed the `[image-carousel]` shortcode in your page?

= My images are showing but they're all over the place  =

1. Is your theme loading the Bootstrap CSS and Javascript? _(look for `bootstrap.css` in the source HTML)_

= The carousel makes the content jump each time it changes =

1. You need to make sure that each image is the same height. You can do this by setting an `Aspect ratio` in the `Edit Image` section of the WordPress Media Library and cropping your images.

== Changelog ==

= 1.1 =
* Added shortcode attributes (code contributed by @joshgerdes)

= 1.0 =
* Initial release

 == Upgrade Notice ==

= 1.1 =
* Added shortcode attribute functionality for tweaking of carousel options.