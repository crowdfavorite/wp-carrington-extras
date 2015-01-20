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

// 	ini_set('display_errors', '1');
// 	ini_set('error_reporting', E_ALL);

define('CFCT_EXTRAS_VERSION', '3.5');

// Path to Carrington Core parent directory (usually the theme).
if (!defined('CFCT_PATH')) {
	define('CFCT_PATH', trailingslashit(TEMPLATEPATH));
}

if (!defined('CFCT_DEBUG')) {
	define('CFCT_DEBUG', false);
}

include_once(CFCT_PATH.'carrington-extras/admin.php');
include_once(CFCT_PATH.'carrington-extras/ajax-load.php');
include_once(CFCT_PATH.'carrington-extras/attachment.php');
include_once(CFCT_PATH.'carrington-extras/utility.php');

load_theme_textdomain('carrington-extras');

/**
 * Loads header code from Carrington Options
 *
**/
function cfct_wp_head() {
	echo cfct_get_option('wp_head');
}
add_action('wp_head', 'cfct_wp_head');

/**
 * Loads footer code from Carrington Options
 *
**/
function cfct_wp_footer() {
	echo cfct_get_option('wp_footer');
}
add_action('wp_footer', 'cfct_wp_footer');
