=== Resized On The Fly ===
Contributors: Roman Jaster, Yay Brigade
Tags: Utility, Images, Resizing
Requires at least: 3.5.0
Tested up to: 6.1.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

=======================
Current Version: 2.11.2
=======================

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

= 2.11.2 =
(7/25/2023)
* Changed "c_fit" to "c_limit" for cloudinary option (to prevent from upscaling small images)

= 2.11.1 =
(6/30/2023)
* Fixed PHP 8.0 errors related to gif files (undefined variable)

= 2.11 =
(5/26/2023)
* Cloudinary support for responsive images
* Added "flush_transient" option

= 2.10 =
(3/20/2023)
* Added Cloudinary support for single images

= 2.9.1 =
(3/3/2023)
* Fixed PHP 8.0 errors

= 2.9 =
(1/18/2020)
* Added "add_height_width_attr" option

= 2.8 =
(3/8/2019)
* Added "alt_fallback" option

= 2.7 =
(8/4/2016)
* Performance improvement - image size is saved as a wp transient 
  (so that it does not have to be checked repeatedly via PHP)

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