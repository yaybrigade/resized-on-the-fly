<?php
/*
Plugin Name: Resized On The Fly
Plugin URI: https://github.com/yaybrigade/resized-on-the-fly
GitHub Plugin URI: https://github.com/yaybrigade/resized-on-the-fly
Description: Provides function resized_on_the_fly() for WordPress templates to make it easier to resize image.
Version: 2.9
Author: Roman Jaster, Yay Brigade
Author URI: yaybrigade.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


/*  ********************************************
	Image Resize Function 
	* Returns <img> html for resized image 
	* Images sizes are created on the fly (using Aqua Resizer)
	
	* Call: function resized_on_the_fly($image, $options_array)
	
	* $image:

	  - image id [int]
	  - or image array [array] (as provide by ACF)
	
	* $options_array:
	
	  Single image options:
	  - $width [int]
	  - $height [int]
	  - $crop [boolean]
	  - $upscale [boolean] (upscale works when both height and width are specified and crop is true)
	  - $return ['img', 'url']
	  
	  Responsive images options:
	  - $srcset [int, int, ...]
  	  - $sizes [string]
	  - $crop [boolean]
	  	if $crop is true then $width and $height will be used to get the crop ratio for the images
		  - $width [int]		
		  - $height [int]
	  - $upscale [boolean]
	  - $transparent_placeholder [boolean] (uses base64 transparent svg as a placeholder for src attribute)
	  !!!
	  Note: Responsive images will always return an img tag	 ($return will be ignored)
	  
	  Options for both:
	  - $alt [string] (overwrites any alt info for image)
	  - $alt_fallback [string] (used if no alt info is found for image)
	  - $add_classes [string]  
	  - $itemprop [boolean] (add itemprop="image")
	  - $lazyload [boolean] (for single image: uses data-scr instead of src | for responsive images: uses data-srcset instead of srcset)

	
*/


// Require Aqua-Resizer
// Thanks to Syamil MJ (https://github.com/syamilmj/Aqua-Resizer)
// I'm using a forked version (https://github.com/yaybrigade/Aqua-Resizer)
require 'Aqua-Resizer-master/aq_resizer.php'; 


/**
 * Main Function
 */
function resized_on_the_fly($image, $options_array) {

	// Get options from array
	if ( ! $width = $options_array['width'] ) $width = false;
	if ( ! $height = $options_array['height'] ) $height = false;
	if ( ! $crop = $options_array['crop'] ) $crop = false;
	if ( ! $alt = $options_array['alt'] ) $alt = false;	
	if ( ! $alt_fallback = $options_array['alt_fallback'] ) $alt_fallback = false;
	if ( ! $add_classes = $options_array['add_classes'] ) $add_classes = '';
	if ( ! $upscale = $options_array['upscale'] ) $upscale = false; 
	if ( ! $return = $options_array['return'] ) $return = 'img';
	if ( ! $srcset = $options_array['srcset'] ) $srcset = false;
	if ( ! $sizes = $options_array['sizes'] ) $sizes = '';
	if ( ! $itemprop = $options_array['itemprop'] ) $itemprop = false;
	if ( ! $lazyload = $options_array['lazyload'] ) $lazyload = false;
	if ( ! $transparent_placeholder = $options_array['transparent_placeholder'] ) $transparent_placeholder = false;
	if ( ! $add_height_width_attr = $options_array['add_height_width_attr'] ) $add_height_width_attr = false;
	
	// Get the image url

	if ( 'array' == gettype($image) ):
		$image_id = $image['id']; // get id from array
	else:
		$image_id = $image; // id was passed
	endif;

	// Get image object
	$original = wp_get_attachment_image_src( $image_id, 'full' );

	// Get url
	if ($original):
		$original_url = $original[0]; 
	else:
		return false; // abort if the original image doesn't even exist
	endif;
		
	
	// Is this a gif?
	// Gif images will be ignored because their animated functionality would be lost
	$isGif = false;
	if ( endsWith($original_url, ".gif") ) {
		$isGif = true;
	}
	
	// Get ALT from WordPress if it was not supplied to function
	if ( ! $alt ) {
		$attachment = get_post( $image_id ); 
		$alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
	}
	// If still no ALT, then use the alt_fallback if supplied
	if ( ! $alt && $alt_fallback ) {
		$alt = $alt_fallback;
	}

	// itemprop="image"
	$itemprop_html = ''; 
	if ( $itemprop ) {
		$itemprop_html = ' itemprop="image" '; 
	}
			
	if ($srcset && !$isGif) {
		// ***
		// Responsive Images 
		// (always ignore gif images)
		
		$image_sizes = explode(",", $srcset);
		
		$srcset_string = '';
		
		foreach ($image_sizes as $this_width):
		
			if ($crop):
				$this_height = round ( $this_width * ($height/$width) );  // Calculate new height based on height/width ratio
			else:
				$this_height = false;
			endif;
		
			$new_url = aq_resize( $original_url, $this_width, $this_height, $crop, $single=true, $upscale );	

			if ( false == $new_url ) $new_url = $original_url; // If image cannot be created use the original image url
			
			if ($srcset_string):
				$srcset_string .= ','; // add comma if there is already a value
			else:
				// First time this loop runs...
				
				// save the smallest url for use with img tag later (as fallback for src property)
				$smallest_url = $new_url; 
				
				// or use a transparent placeholder
				if ($transparent_placeholder):
		
					$image_size = get_actual_image_size($image_id, $new_url);
		
					if ($image_size) {
						$w = $image_size[0]; // actual image width
						$h = $image_size[1]; // actual image height
						$smallest_url = "data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg' viewBox%3D'0 0 $w $h'%2F%3E"; // transparent svg with width and height
					}
				endif;
			endif; 
			
			$srcset_string .= $new_url . ' ' . $this_width . 'w';
		
		endforeach;
		
		// Check for lazyload
		$srcset_attribute ="srcset";
		if ($lazyload) {
			$srcset_attribute ="data-srcset";
		}

		// Check if to include height and width attributes
		$height_width_html = '';
		if ($add_height_width_attr) {
			$image_size = get_actual_image_size($image_id, $new_url);
			if ($image_size) {
				$w = $image_size[0]; // actual image width
				$h = $image_size[1]; // actual image height
				$height_width_html = "height=\"$h\" width=\"$w\"";
			}
		}
		
		$img_html = "<img src=\"$smallest_url\"  $srcset_attribute=\"$srcset_string\"  sizes=\"$sizes\"  alt=\"$alt\"  class=\"$add_classes\"  $itemprop_html  $height_width_html />";
		return $img_html;

	} else {
		// ***
		// Or just return one image
	
		// Get resized image from Aqua Resizer (but don't do this for gif images)
		if (!$isGif) {
			$new_url = aq_resize( $original_url, $width, $height, $crop, $single=true, $upscale );	
		}
		if ( false == $new_url ) $new_url = $original_url; // If image cannot be created use the original image url
		
		if ('url' == $return):
			// Return URL only
			return $new_url;
		else:
			// Output <img> tag
			
			// Check for lazyload
			$src_attribute ="src";
			if ($lazyload) {
				$src_attribute ="data-src";
			}
			
			// Check if to include height and width attributes
			$height_width_html = '';
			if ($add_height_width_attr) {
				$image_size = get_actual_image_size($image_id, $new_url);
				if ($image_size) {
					$w = $image_size[0]; // actual image width
					$h = $image_size[1]; // actual image height
					$height_width_html = "height=\"$h\" width=\"$w\"";
				}
			}
		
			$img_html = "<img $src_attribute=\"$new_url\" alt=\"$alt\" class=\"$add_classes\"  $itemprop_html $height_width_html />";
			return $img_html;
		endif; 

	} // if ($srcset)
};



/**
 * Flush out the transients when image is updated via WP
 */
function rotf_imagesize_transient_flusher($post_ID) {
	delete_transient( 'rotf_imagesize_' . $post_ID);
}
add_action( 'edit_attachment', 'rotf_imagesize_transient_flusher' );

/**
 * Helper Functions
 */

// endsWith()
function endsWith($haystack, $needle) {
	// search forward starting from end minus needle length characters
	return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE;
}

// get_actual_image_size()
function get_actual_image_size($image_id, $new_url) {
	// get actual image size -- from transient or by looking at the image
	if ( false === ( $image_size = get_transient( 'rotf_imagesize_' . $image_id  ) ) ) {
		$image_size = getimagesize($new_url); 
		set_transient( 'rotf_imagesize_' . $image_id, $image_size);
	}
	return $image_size;
}