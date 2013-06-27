<?php

// This file is part of the Carrington Core Platform for WordPress
// http://crowdfavorite.com/wordpress/carrington-core/
//
// Copyright (c) 2008-2012 Crowd Favorite, Ltd. All rights reserved.
// http://crowdfavorite.com
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

/**
 * Get a Carrington Framework option, load the default otherwise
 *
 * @param string $name Name of the option to retrieve
 * @return mixed Value of the option
 * 
**/
function cfct_get_option($name) {
	$defaults = array(
		cfct_option_name('login_link_enabled') => 'yes',
		cfct_option_name('copyright') => sprintf(__('Copyright &copy; %s &nbsp;&middot;&nbsp; %s', 'carrington'), date('Y'), get_bloginfo('name')),
		cfct_option_name('credit') => 'yes',
		cfct_option_name('lightbox') => 'yes',
		cfct_option_name('header_image') => 0,
	);
	$name = cfct_option_name($name);
	
	$defaults = apply_filters('cfct_option_defaults', $defaults);
	$value = get_option($name);
	
	
	// We want to check for defaults registered using the prefixed and unprefixed versions of the option name.
	if ($value === false) {
		$prefix = cfct_get_option_prefix();
		$basname = substr($name, strlen($prefix) + 1, -1);
		
		if (isset($defaults[$name])) {
			$value = $defaults[$name];
		}
		else if (isset($basename) && isset($defaults[$basename])) {
			$value = $defaults[$basename];
		}
	}
	if ($name == cfct_option_name('copyright')) {
		$value = str_replace('%Y', date('Y'), $value);
	}

	return apply_filters('cfct_get_option_value', $value, $name);
}

/**
 * Custom formatting for strings
 * 
 * @param string $str A string to be formatted
 * @return string Formatted string
 *  
**/
function cfct_basic_content_formatting($str) {
	$str = wptexturize($str);
	$str = convert_smilies($str);
	$str = convert_chars($str);
	$str = wpautop($str);
	return $str;
}

/**
 * Generate markup for login/logout links
 * 
 * @param string $redirect URL to redirect after the login or logout
 * @param string $before Markup to display before
 * @param string $after Markup to display after
 * @return string Generated login/logout Markup
 */ 
function cfct_get_loginout($redirect = '', $before = '', $after = '') {
	if (cfct_get_option('login_link_enabled') != 'no') {
		return $before . wp_loginout($redirect, false) . $after;
	}
} 

/**
 * Recursively merges two arrays down overwriting values if keys match.
 * 
 * @param array $array_1 Array to merge into
 * @param array $array_2 Array in which values are merged from
 * 
 * @return array Merged array
 */ 
function cfct_array_merge_recursive($array_1, $array_2) {
	foreach ($array_2 as $key => $value) {
		if (isset($array_1[$key]) && is_array($array_1[$key]) && is_array($value)) {
			$array_1[$key] = cfct_array_merge_recursive($array_1[$key], $value);
		}
		else {
			$array_1[$key] = $value;
		}
	}
	
	return $array_1;
}

/**
 * Returns the options prefix
 */ 
function cfct_get_option_prefix() {
	return apply_filters('cfct_option_prefix', 'cfct');
}

/**
 * Prefix options names
 */ 
function cfct_option_name($name) {
	$prefix = cfct_get_option_prefix();
	// If its already prefixed, we don't need to do it again.
	if (strpos($name, $prefix.'_') !== 0) {
		return $prefix.'_'.$name;
	}
	else {
		return $name;
	}
}

/**
 * Loads about text from Carrington options for display in the sidebar
 * 
 * @return string Markup for the about text
 * 
**/
function cfct_about_text() {
	$about_text = cfct_get_option('about_text');
	if (!empty($about_text)) {
		$about_text = cfct_basic_content_formatting($about_text);
	}
	else {
		global $post, $wp_query;
		$orig_post = $post;
		isset($wp_query->query_vars['page']) ? $page = $wp_query->query_vars['page'] : $page = null;
// temporary - resetting below
		$wp_query->query_vars['page'] = null;
		remove_filter('the_excerpt', 'st_add_widget');
		$about_query = new WP_Query('pagename=about');
		while ($about_query->have_posts()) {
			$about_query->the_post();
			$about_text = get_the_excerpt().sprintf(__('<a class="more" href="%s">more &rarr;</a>', 'carrington'), get_permalink());
		}
		$wp_query->query_vars['page'] = $page;
		if (!empty($orig_post)) {
			$post = $orig_post;
			setup_postdata($post);
		}
	}
	if (function_exists('st_add_widget')) {
		add_filter('the_excerpt', 'st_add_widget');
	}
	return $about_text;
}

/**
 * Gets custom colors to be used with a themes
 * 
 * @return string Custom color
 * 
**/
function cfct_get_custom_colors($type = 'option') {
	global $cfct_color_options;
	$colors = array();
	foreach ($cfct_color_options as $option => $value) {
		switch ($type) {
			case 'preview':
				!empty($_GET[$option]) ? $colors[$option] = strip_tags(stripslashes($_GET[$option])) : $colors[$option] = '';
				break;
			case 'option':
			default:
				$colors[$option] = cfct_get_option($option);
				break;
		}
	}
	return $colors;
}
