<?php
/*
Plugin Name: Advanced Custom Fields: Separation
Plugin URI: https://github.com/yaybrigade/resized-on-the-fly
GitHub Plugin URI: https://github.com/yaybrigade/resized-on-the-fly
Description: Provides function resized_on_the_fly() for WordPress templates to make it easier to resize image.
Version: 2.3.0
Author: Roman Jaster, Yay Brigade
Author URI: yaybrigade.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


/*  ********************************************
	Image Resize Function 
	* Returns <img> html for resized image (using Aqua Resizer)
	* Images sizes are created on the fly
	
	* Features:
	
	  Single image:
	  - width [int]
	  - height [int]
	  - crop [true/false]
	  - upscale [true/false]
	  - return ['img', 'url']
	  - alt [string]
	  - add_classes [string]
	  
	  Responsive images:
	  - srcset [int, int, ...]
  	  - sizes [string]
	  - crop [true/false]
	  	if crop is true then width and height will be used to get the crop ratio for the images
		  - width [int]		
		  - height [int]
	  - upscale [true/false]
	  - alt [string]
	  - add_classes [string]  
	  !!!
	  Note: Responsive images will always return an img tag	  
	
*/

require 'Aqua-Resizer-master/aq_resizer.php'; // Thanks to Syamil MJ (https://github.com/syamilmj/Aqua-Resizer)

function endsWith($haystack, $needle) {
	// search forward starting from end minus needle length characters
	return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE;
}
function resized_on_the_fly($image_id_or_array, $options_array) {
	
	// Get options from array
	if ( ! $width = $options_array['width'] ) $width = false;
	if ( ! $height = $options_array['height'] ) $height = false;
	if ( ! $crop = $options_array['crop'] ) $crop = false;
	if ( ! $alt = $options_array['alt'] ) $alt = false;
	if ( ! $add_classes = $options_array['add_classes'] ) $add_classes = '';
	if ( ! $upscale = $options_array['upscale'] ) $upscale = false; // $upscale=true works for aqua_resize (although it is not documented) when both height and width are specified
	if ( ! $return = $options_array['return'] ) $return = 'img';
	if ( ! $srcset = $options_array['srcset'] ) $srcset = false;
	if ( ! $sizes = $options_array['sizes'] ) $sizes = '';
	
	
	// Get the image id
	if ( 'array' == gettype($image_id_or_array) ){
		$image_id = $image_id_or_array['id']; // get id from array
	} else {
		$image_id = $image_id_or_array; // id was passed
	}
	
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
	
	
	if ($srcset  && !$isGif):
		// ***
		// Responsive Images 
		//    (always ignore gif images)
		
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
				$smallest_url = $new_url; // save the smallest url for use with img tag later (as fallback for src property)
			endif; 
			
			$srcset_string .= $new_url . ' ' . $this_width . 'w';
		
		endforeach;
		
		$img_html = "<img src=\"$smallest_url\"  srcset=\"$srcset_string\"  sizes=\"$sizes\"  alt=\"$alt\"  class=\"$add_classes\" />";
		return $img_html;

	else:
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
			// Output image
			$img_html = "<img src=\"$new_url\" alt=\"$alt\" class=\"$add_classes\" />";
			return $img_html;
		endif; 

	endif; // if ($srcset)
};