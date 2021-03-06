<?php
/*
Plugin Name: Comment Info Detector
Plugin URI: http://hieudt.info/wp-plugins/comment-info-detector
Description: Enables you to detect your commenter's info and show their country flag, web browser, operating system automatically.
Version: 1.0.4
Author: HieuDT
Author URI: http://hieudt.info
  
Copyright 2008  HieuDT  (email : mr.hieudt@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Especially thanks to Priyadi Iman Nurcahyo (http://priyadi.net/) and Omry Yadan (http://firestats.cc/)

*/

if (!defined('WP_CONTENT_DIR')) {
	define( 'WP_CONTENT_DIR', ABSPATH.'wp-content');
}
if (!defined('WP_CONTENT_URL')) {
	define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
}
if (!defined('WP_PLUGIN_DIR')) {
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
}
if (!defined('WP_PLUGIN_URL')) {
	define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
}

function CID_init() {
	$CID_options = array();
	$CID_options['flag_icons_url'] = WP_PLUGIN_URL . "/comment-info-detector/flags";
	$CID_options['flag_template'] = '<span class="country-flag"><img src="%IMAGE_BASE%/%COUNTRY_CODE%.png" title="%COUNTRY_NAME%" alt="%COUNTRY_NAME%" /> %COUNTRY_NAME%</span> ';
	$CID_options['WB_OS_icons_url'] = WP_PLUGIN_URL . "/comment-info-detector/browsers";
	$CID_options['WB_OS_template'] = '<span class="WB-OS"><img src="%IMAGE_BASE%/%BROWSER_CODE%.png" title="%BROWSER_NAME%" alt="%BROWSER_NAME%" /> %BROWSER_NAME% %BROWSER_VERSION% <img src="%IMAGE_BASE%/%OS_CODE%.png" title="%OS_NAME%" alt="%OS_NAME%" /> %OS_NAME% %OS_VERSION%</span>';
	$CID_options['auto_display_flag'] = 0;
	$CID_options['auto_display_WB_OS'] = 0;	
	add_option('CID_options', $CID_options, 'Comment Info Detector Options');
}
add_action('activate_comment-info-detector/comment-info-detector.php', 'CID_init');

function CID_options_page() {
	add_options_page('Comment Info Detector Options', 'Comment Info Detector', 10, 'comment-info-detector/options.php');
}
add_action('admin_menu', 'CID_options_page');

function CID_css() {
	echo "\n".'<!-- Generated By Comment Info Detector 1.0.4 - http://hieudt.info/wp-plugins/comment-info-detector -->'."\n";
	if(@file_exists(TEMPLATEPATH.'/comment-info-detector.css')) {
		echo '<link rel="stylesheet" href="'.get_stylesheet_directory_uri().'/comment-info-detector.css" type="text/css" media="screen" />'."\n";	
	} else {
		echo '<link rel="stylesheet" href="'.WP_PLUGIN_URL.'/comment-info-detector/comment-info-detector.css" type="text/css" media="screen" />'."\n";
	}
}
add_action('wp_head', 'CID_css');

$CID_options = get_option('CID_options');

require(dirname(__FILE__).'/country.php');
require(dirname(__FILE__).'/browser.php');

if ($CID_options['auto_display_flag'] == 1 || ($CID_options['auto_display_flag'] == 2 && !is_admin()))
	add_filter('get_comment_author_link','CID_auto_display_flag'); 

if ($CID_options['auto_display_WB_OS'] == 1 || ($CID_options['auto_display_WB_OS'] == 2 && !is_admin()))
	add_filter('get_comment_author_link','CID_auto_display_WB_OS');
	
function CID_auto_display_flag($link) {
	global $comment;
	return $link . ' ' . CID_get_flag_without_template($comment->comment_author_IP,true,false,'','');
}

function CID_auto_display_WB_OS($link) {
	global $comment;
	return $link . ' ' . CID_browser_string_without_template($comment->comment_agent,true,false,'','');
}
?>