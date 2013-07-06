<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}
// define('WINABSPATH', str_replace("\\", "/", ABSPATH) );  // required for Windows & XAMPP

// check ssl
if ( ! function_exists( 'is_ssl' ) ) {
function is_ssl() {
if ( isset($_SERVER['HTTPS']) ) {
if ( 'on' == strtolower($_SERVER['HTTPS']) )
 return true;
if ( '1' == $_SERVER['HTTPS'] )
 return true;
} elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
return true;
}
return false;
}
}
// adjust content url for ssl if necessary
if (version_compare(get_bloginfo( 'version') , '3.0' , '<') && is_ssl()) {
$wp_content_url = (defined('WP_CONTENT_URL')) ? WP_CONTENT_URL : str_replace('http://' , 'https://' , get_option( 'siteurl')) . '/wp-content';
} 
else {
$wp_content_url = (defined('WP_CONTENT_URL')) ? WP_CONTENT_URL : get_option('siteurl') . '/wp-content';
}
// allow for SSL on certain pages too
$wp_content_url = (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') ? $wp_content_url : str_replace("http://", "https://", $wp_content_url);

// determine content dir
$wp_content_dir = (defined('WP_CONTENT_DIR')) ? WP_CONTENT_DIR : ABSPATH . 'wp-content';
// normalise presence/absence of trailing slash - user may define wp-content/ (with slash)
$wp_content_url = untrailingslashit($wp_content_url);
$wp_content_dir = untrailingslashit($wp_content_dir);

$wp_plugin_url = $wp_content_url . '/plugins/';
$wp_plugin_dir = $wp_content_dir . '/plugins/';
$wpmu_plugin_url = $wp_content_url . '/mu-plugins/';
$wpmu_plugin_dir = $wp_content_dir . '/mu-plugins/';
// add variables to class
$this->wp_content_url = $wp_content_url;
$this->wp_content_dir = $wp_content_dir;
$this->wp_plugin_url = $wp_plugin_url;
$this->wp_plugin_dir = $wp_plugin_dir;
$this->thispluginurl = $wp_plugin_url . dirname(plugin_basename(__FILE__)).'/';
$this->thisplugindir = $wp_plugin_dir . dirname(plugin_basename(__FILE__)).'/';

/* Abland contribution: http://themeover.com/forum/topic/settings-options-in-admin/ */
global $wp_version;	
if ($wp_version >= 3 and is_multisite()) {
	global $blog_id;
	$this->micro_root_dir = $wp_content_dir . '/blogs.dir/' . $blog_id . '/micro-themes/';
	$this->micro_root_url = $wp_content_url . '/blogs.dir/' . $blog_id . '/micro-themes/';
} 
else {
	$this->micro_root_dir = $wp_content_dir . '/micro-themes/';		
	$this->micro_root_url = $wp_content_url . '/micro-themes/';
}

// set page prefix for form actions and plugin links
if (TVR_MICRO_VARIANT == 'themer') {
	$this->page_prefix = 'admin';
}
else {
	$this->page_prefix = 'themes';
}
?>