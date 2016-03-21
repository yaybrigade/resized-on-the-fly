=== Resized On The Fly ===
Contributors: Roman Jaster, Yay Brigade
Tags: Utility, Images, Resizing
Requires at least: 3.5.0
Tested up to: 4.4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides function resized_on_the_fly() to make it easier to resize image in WordPress. 

== Description ==

Provides function resized_on_the_fly() to make it easier to resize image in WordPress. 

This plugin has no admin UI whatsoever. It solely makes the function resized_on_the_fly() available for advanced use in templates.

= Compatibility =

Relies on Syamil MJ's Aqua Resizer, which is included.

== Installation ==

1. Copy the `resized-on-the-fly` folder into your `wp-content/plugins` folder
2. Activate the `Resized On The Fly` plugin via the plugins admin page
3. Function resized_on_the_fly() is now available for use in template files

== Changelog ==

= 2.6.1 =
(3/21/2016)
* Removed an aq_resizer() error message that was logged when images were too small to be resized

= 2.6.0 =
(3/2/2016)
* Add support for transparent svg placeholder (for responsive images)

= 2.5.0 =
(10/7/2015)
* Add support for lazyload

= 2.4.0 =
(9/29/2015)
* Add support for itemprop="image"

= 2.3.0 =
(9/7/2015)
* Initial WordPress plugin Release
* Clean up code a bit

= 2.2.0 =
(1/20/2015)
* ignore gif images because their animation is lost after resizing 

= 2.1.0 =
(11/26/2014)
* enable crop functionality for responsive images 

= 2.0.0 =
(11/25/2014)
* includes responsive images option