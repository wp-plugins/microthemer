<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    die('Please do not call this page directly.');
}
// define('WINABSPATH', str_replace("\\", "/", ABSPATH) );  // required for Windows & XAMPP

/* discussion of when to use site_url vs home_url: http://premium.wpmudev.org/forums/topic/when-referencing-content-your-plugins-seem-to-use-site_url
See also: http://codex.wordpress.org/Giving_WordPress_Its_Own_Directory
- Use site_url for referencing actual files paths (so currently correct for most of paths below)
- Use home_url for loading the homepage (site preview should use this)
- considerations: multi-site, SSL
*/

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

// get main site URLS and microthemer vars
$site_url = untrailingslashit(site_url()). '/';
$https_site_url = untrailingslashit(str_replace('http://' , 'https://', site_url())). '/';
$microthemer_dir = plugin_dir_path( __FILE__ ); // with trailing slash
$microthemer_name = dirname(plugin_basename(__FILE__));

// adjust content url for ssl if necessary
if (version_compare(get_bloginfo( 'version') , '3.0' , '<') && is_ssl()) {
    $wp_content_url = str_replace($site_url, $https_site_url, content_url());
}
else {
    $wp_content_url = content_url();
}
// allow for SSL on certain pages too
$wp_content_url = (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') ? $wp_content_url : str_replace($site_url, $https_site_url, $wp_content_url);

// determine content dir
$wp_content_dir = str_replace($site_url, ABSPATH, $wp_content_url);
$wp_content_dir = str_replace($https_site_url, ABSPATH, $wp_content_dir); // in case ssl
// THEORY: this technique might be unreliable if site url isn't the first stub of $wp_content_url somehow...
if (strpos($wp_content_dir, 'http') !== false){
    $wp_content_dir = WP_CONTENT_DIR; // WP advise against this but it's better than the dir path being an URL.
}

// normalise presence/absence of trailing slash - user may define wp-content/ (with slash)
$wp_content_url = untrailingslashit($wp_content_url);
$wp_content_dir = untrailingslashit($wp_content_dir);

// get plugins url and directory
$wp_plugin_url = plugins_url();

// try to get directory by string replacing the site url with ABSPATH
$wp_plugin_dir = str_replace($site_url, ABSPATH, $wp_plugin_url);
$wp_plugin_dir = str_replace($https_site_url, ABSPATH, $wp_plugin_dir);
// THEORY: this technique might be unreliable if site url isn't the first stub of $wp_plugin_url somehow...
if (strpos($wp_plugin_dir, 'http') !== false){
    $wp_plugin_dir = str_replace( $microthemer_name . '/', '', $microthemer_dir );
}

// add variables to class
$this->wp_content_url = $wp_content_url;
$this->wp_content_dir = $wp_content_dir;
$this->wp_plugin_url = $wp_plugin_url;
$this->wp_plugin_dir = $wp_plugin_dir;
$this->thispluginurl = $wp_plugin_url .'/' . $microthemer_name.'/';
$this->thisplugindir = $microthemer_dir;

// for creating root relative paths (good for mixed ssl)
$this->site_url = untrailingslashit($site_url);
$this->home_url = home_url();

// save the admin dashboard url
$this->wp_admin_url = network_admin_url();
$this->wp_blog_admin_url = admin_url(); // A contribution from Abland: http://themeover.com/forum/topic/microthemer-3-0-what-did-we-get-right-what-should-we-improve/?view=all

/* Another Abland contribution: http://themeover.com/forum/topic/settings-options-in-admin/ */
global $wp_version;
global $blog_id;
if ($wp_version >= 3 and is_multisite()) {
    $filename = $wp_content_dir . "/blogs.dir/";
    if(file_exists($filename)){
        if ($blog_id == '1') {
            $this->micro_root_dir = $wp_content_dir . '/blogs.dir/micro-themes/';
            $this->micro_root_url = $wp_content_url . '/blogs.dir/micro-themes/';
        } else {
            $this->micro_root_dir = $wp_content_dir . '/blogs.dir/' . $blog_id . '/micro-themes/';
            $this->micro_root_url = $wp_content_url . '/blogs.dir/' . $blog_id . '/micro-themes/';
        }
    } else {
        if ($blog_id == '1') {
            $this->micro_root_dir = $wp_content_dir . '/uploads/sites/micro-themes/';
            $this->micro_root_url = $wp_content_url . '/uploads/sites/micro-themes/';
        } else {
            $this->micro_root_dir = $wp_content_dir . '/uploads/sites/' . $blog_id . '/micro-themes/';
            $this->micro_root_url = $wp_content_url . '/uploads/sites/' . $blog_id . '/micro-themes/';
        }
    }
} else {
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

// quick path debug
$tvr_debug_paths = false;
if ($tvr_debug_paths){
    echo '$wp_content_url: ' .  $this->wp_content_url . '<br />';
    echo '$wp_content_dir: ' .  $this->wp_content_dir . '<br />';
    echo '$wp_plugin_url: ' .  $this->wp_plugin_url . '<br />';
    echo '$wp_plugin_dir: ' .  $this->wp_plugin_dir . '<br />';
    echo '$thispluginurl: ' .  $this->thispluginurl . '<br />';
    echo '$thisplugindir: ' .  $this->thisplugindir . '<br />';
    echo '$site_url: ' .  $this->site_url . '<br />';
    echo '$home_url: ' .  $this->home_url . '<br />';
    echo '$wp_admin_url: ' .  $this->wp_admin_url . '<br />';
    echo '$wp_blog_admin_url: ' .  $this->wp_blog_admin_url . '<br />';
    echo '$micro_root_url: ' .  $this->micro_root_url . '<br />';
    echo '$micro_root_dir: ' .  $this->micro_root_dir . '<br />';
}
?>
