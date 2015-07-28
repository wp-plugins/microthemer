<?php
/*
Plugin Name: Microthemer
Plugin URI: http://www.themeover.com/microthemer
Text Domain: tvr-microthemer
Domain Path: /languages
Description: Microthemer is a feature-rich visual design plugin for customizing the appearance of ANY WordPress Theme or Plugin Content (e.g. posts, pages, contact forms, headers, footers, sidebars) down to the smallest detail (unlike typical theme options). For CSS coders, Microthemer is a proficiency tool that allows them to rapidly restyle a WordPress theme or plugin. For non-coders, Microthemer's intuitive interface and "Double-click to Edit" feature opens the door to advanced theme and plugin customization.
Version: 4.1.3
Author: Themeover
Author URI: http://www.themeover.com
Text Domain: tvr-microthemer
*/

/*  Copyright 2012 by Sebastian Webb @ Themeover */

// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Please do not call this page directly.');
}

// define plugin variation
if (!defined('TVR_MICRO_VARIANT')) {
	define('TVR_MICRO_VARIANT', 'themer');
}

// define dev mode
if (!defined('TVR_DEV_MODE')) {
    define('TVR_DEV_MODE', false);
}

// define unique id for media query keys
if (!defined('UNQ_BASE')) {
	define('UNQ_BASE', uniqid());
}

// if already defined, tell user to disable microloader
else {
	add_action(
		'admin_notices',
		create_function(
			'',
			'echo \'<div id="message" class="error"><p><strong>'.wp_kses(__('Microthemer requires that you deactivate Microloader. Please do so.', 'tvr-microthemer'), array()).'</strong></p></div>\';'
		)
	);
	define('TVR_MICROBOTH', true);
}

// only run plugin admin code on admin pages
if ( is_admin() ) {
	// admin class
	if (!class_exists('tvr_microthemer_admin')) {

		// define
		class tvr_microthemer_admin {

			var $version = '4.1.3';
            var $time = 0;
            // set this to true if version saved in DB is different, other actions may follow if new v
            var $new_version = false;
			var $minimum_wordpress = '3.6';
			var $users_wp_version = 0;
			var $page_prefix = '';
            var $optimisation_test = false;
			var $optionsName= 'microthemer_ui_settings';
			var $preferencesName = 'preferences_themer_loader';
			var $localizationDomain = "microthemer";
			var $globalmessage = array();
            var $ei = 0; // error index
			var $permissionshelp;
			var $microthemeruipage = 'tvr-microthemer.php';
			var $microthemespage = 'tvr-manage-micro-themes.php';
            var $managesinglepage = 'tvr-manage-single.php';
			var $preferencespage = 'tvr-microthemer-preferences.php';
            var $total_sections = 0;
            var $total_selectors = 0;
			var $sel_loop_count;
            var $sel_count = 0;
            var $sel_option_count = 0;
			var $trial = true;
            var $initial_options_html = array();
            var $imported_images = array();
            var $site_url = '';

			// @var array $pages Stores all the plugin pages in an array
			var $all_pages = array();
			// @var array $css_units Stores all the possible CSS units
			var $css_units = array();
            var $css_unit_sets = array();
            var $default_my_props = array();
			// @var array $options Stores the ui options for this plugin
			var $options = array();
            var $serialised_post = array();
            var $propertyoptions = array();
            var $property_option_groups = array();
            var $legacy_groups = array();
            var $mob_preview = array();
			// @var array $options Stores the "to be merged" options in
			var $to_be_merged = array();
			// @var array $preferences Stores the preferences for this plugin
			var $preferences = array();
			// @var array $file_structure Stores the micro theme dir file structure
			var $file_structure = array();
            // pollyfills
            var $pollyfills = array('pie'); // , boxsizing
            // temporarily keep track of the tabs that are available for the property group.
            // This saves additional processing at various stages
            var $current_pg_group_tabs = array();
			// default preferences set in constructor
			var $default_preferences = array();
            var $default_preferences_dont_reset = array();
            // edge mode fixed settings
            var $edge_mode = array();

			// default media queries
			var $unq_base = '';
			var $default_m_queries = array();
            var $mobile_first_mqs = array();
            var $mobile_first_semantic_mqs = array();
            var $mq_sets = array();
            var $comb_devs = array(); // for storing all-devs + MQs in one array
            // set default custom code options (todo make use of this array throughout the program)
            var $custom_code = array();

			// @var strings dir/url paths
			var $wp_content_url = '';
			var $wp_content_dir = '';
			var $wp_plugin_url = '';
			var $wp_plugin_dir = '';
			var $thispluginurl = '';
			var $thisplugindir = '';
			var $micro_root_dir = '';

			// control debug output here
			var $debug_merge = false;
			var $debug_save = false;
			var $debug_selective_export = false;
            var $show_me = ''; // for quickly printing vars in the top toolbar

			// Class Functions

			/**
			* PHP 4 Compatible Constructor
			function tvr_microthemer_admin(){$this->__construct();}
             **/

			/**
			* PHP 5 Constructor
			*/
			function __construct(){

				// Stop the plugin if below requirements
				// @taken from ngg gallery: http://wordpress.org/extend/plugins/nextgen-gallery/
				if ( (!$this->required_version()) or (!$this->check_memory_limit()) or defined('TVR_MICROBOTH') or !$this->trial_check() ) {
					return;
				}

                // translatable
                add_action('plugins_loaded', array(&$this, 'tvr_load_textdomain'));

				// add menu links (all WP admin pages need this)
				if (TVR_MICRO_VARIANT == 'themer') {
					add_action("admin_menu", array(&$this, "microthemer_dedicated_menu"));
                }
				else {
					add_action("admin_menu", array(&$this, "microloader_menu_link"));
				}

                // get the directory paths
                include dirname(__FILE__) .'/get-dir-paths.inc.php';

                /***
                limit the amount of code that runs on non-microthemer admin pages
                -- the main functions need to run for the sake of creating
                menu links to the plugin pages, but the code contained within is conditional.
                 ***/
                // save all plugin pages in an array for evaluation throughout the program
                $this->all_pages = array(
                    $this->microthemeruipage,
                    $this->microthemespage,
                    $this->managesinglepage,
                    $this->preferencespage
                );
                $page = isset($_GET['page']) ? $_GET['page'] : false;

                // use quick method of getting preferences at this stage (maybe shift code around another time)
                $this->preferences = get_option($this->preferencesName);

                // add shortcut to Microthemer if preference
                if (!empty($this->preferences['admin_bar_shortcut'])
                    and $this->preferences['admin_bar_shortcut'] == 1) {
                    add_action( 'admin_bar_menu', array(&$this, 'custom_toolbar_link'), 999999);
                }

				// only initialize on plugin admin pages
				if ( is_admin() and in_array($page, $this->all_pages) ) {

                    // save time for use with ensuring non-cached files
                    $this->time = time();

                    $this->permissionshelp = wp_kses(__('Please see this help article for changing directory and file permissions:', 'tvr-microthemer'), array()) . ' <a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a>.' . wp_kses(__('Tip: you may want to jump to the "Using an FTP Client" section of the article. But bear in mind that if your web hosting runs windows it may not be possible to adjust permissions using an FTP program. You may need to log into your hosting control panel, or request that your host adjust the permissions for you.', 'tvr-microthemer'), array());

                    // moved to constructor because __() can't be used on member declarations
                    $this->edge_mode = array(
                        'available' => false,
                        'edge_forum_url' => 'http://themeover.com/forum/topic/edge-mode-usability-testing-new-feature-preview/',
                        'cta' => __("Try out the new targeting options for the selector wizard. We've replaced the slider with hover and click functionality.", 'tvr-microthemer'),
                        'config' => array(
                            // 'slideless_wizard' => 1
                        ),
                        'active' => false // evaluated at top of ui page
                    );

                    $this->custom_code = array(
                        'hand_coded_css' => __('All Browsers', 'tvr-microthemer'),
                        'ie_css' => array(
                            'all' => __('All versions of IE', 'tvr-microthemer'),
                            'nine' => __('IE9 and below', 'tvr-microthemer'),
                            'eight' => __('IE8 and below', 'tvr-microthemer'),
                            'seven' => __('IE7 and below', 'tvr-microthemer')
                        )
                        // user_created = array()
                    );

                    // define CSS units sets
                    $this->css_unit_sets = array(
                        __('Simple pixels', 'tvr-microthemer'),
                        __('Recommended', 'tvr-microthemer'),
                    );

                    // define possible CSS units
                    $this->css_units = array(

                        // Absolute CSS units
                        'px'=> array(
                            'type' => 'absolute',
                            'desc' => __('pixels; 1px is equal to 1/96th of 1in.', 'tvr-microthemer')
                        ),
                        'px (implicit)'=> array(
                            'type' => 'absolute',
                            'desc' => __('Pixels are added by Microthemer behind the scenes when no unit is specified (this is the default).', 'tvr-microthemer')
                        ),
                        'pt'=> array(
                            'type' => 'absolute',
                            'desc' => __('points; 1pt is equal to 1/72nd of 1in', 'tvr-microthemer')
                        ),
                        'pc'=> array(
                            'type' => 'absolute',
                            'desc' => __('picas; 1pc is equal to 12pt', 'tvr-microthemer')
                        ),
                        'cm'=> array(
                            'type' => 'absolute',
                            'desc' => __('centimeters', 'tvr-microthemer')
                        ),
                        'mm'=> array(
                            'type' => 'absolute',
                            'desc' => __('millimeters', 'tvr-microthemer')
                        ),
                        'in'=> array(
                            'type' => 'absolute',
                            'desc' => __('inches', 'tvr-microthemer')
                        ),
                        // Relative units
                        '%'=> array(
                            'type' => 'relative',
                            'desc' => __('A percentage relative to another value, typically an enclosing element.', 'tvr-microthemer')
                        ),
                        'em'=> array(
                            'type' => 'relative',
                            'desc' => __('Relative to the font size of the element', 'tvr-microthemer')
                        ),
                        'rem'=> array(
                            'type' => 'relative',
                            'desc' => __('Relative to the font size of the root element', 'tvr-microthemer')
                        ),
                        'ex'=> array(
                            'type' => 'relative',
                            'desc' => __('Relative to the x-height of the element\'s font (i.e. the font\'s lowercase letter x).', 'tvr-microthemer')
                        ),
                        'ch'=> array(
                            'type' => 'relative',
                            'desc' => __('width of the "0" (ZERO, U+0030) glyph in the element\'s font', 'tvr-microthemer')
                        ),
                        // @link http://caniuse.com/#search=viewport
                        'vw' => array(
                            'type' => 'viewport relative',
                            'desc' => __('1% of viewport\'s width', 'tvr-microthemer'),
                            'not_supported_in' => array('IE8')
                        ),
                        'vh' => array(
                            'type' => 'viewport relative',
                            'desc' => __('1% viewport\'s height', 'tvr-microthemer'),
                            'not_supported_in' => array('IE8')
                        ),
                        'vmin' => array(
                            'type' => 'viewport relative',
                            'desc' => __('1% of viewport\'s smaller dimension', 'tvr-microthemer'),
                            'not_supported_in' => array('IE8')
                            // NOTE: in IE9 it's called 'vm'
                        ),
                        'vmax' => array(
                            'type' => 'viewport relative',
                            'desc' => __('1% of viewport\'s larger dimension', 'tvr-microthemer'),
                            'not_supported_in' => array('IE8', 'IE9', 'IE10', 'IE11')
                        )
                    );

                    // easy preview of common devices
                    $this->mob_preview = array(
                        array('(P) Apple iPhone 4', 320, 480),
                        array('(P) Apple iPhone 5', 320, 568),
                        array('(P) BlackBerry Z30', 360, 640),
                        array('(P) Google Nexus 5', 360, 640),
                        array('(P) Nokia N9', 360, 640),
                        array('(P) Samsung Gallaxy (All)', 360, 640),
                        array('(P) Apple iPhone 6', 375, 667),
                        array('(P) Google Nexus 4', 384, 640),
                        array('(P) LG Optimus L70', 384, 640),
                        array('(P) Apple iPhone 6 Plus', 414, 736),
                        // landscape (with some exceptions)
                        array('(L) Apple iPhone 4', 480, 320),
                        array('(L) Nokia Lumia 520', 533, 320),
                        array('(L) Apple iPhone 5', 568, 320),
                        array('(P) Google Nexus 7', 600, 960),
                        array('(P) BlackBerry PlayBook', 600, 1024),
                        array('(L) BlackBerry Z30', 640, 360),
                        array('(L) Google Nexus 5', 640, 360),
                        array('(L) Nokia N9', 640, 360),
                        array('(L) Samsung Gallaxy (All)', 640, 360),
                        array('(L) Google Nexus 4', 640, 384),
                        array('(L) LG Optimus L70', 640, 384),
                        array('(L) Apple iPhone 6', 667, 375),
                        array('(L) Apple iPhone 6 Plus', 736, 414),
                        array('(P) Apple iPad', 768, 1024),
                        array('(P) Google Nexus 10', 800, 1280),
                        array('(L) Google Nexus 7', 960, 600),
                        array('(L) BlackBerry PlayBook', 1024, 600),
                        array('(L) Apple iPad', 1024, 768),
                        array('(L) Google Nexus 10', 1280, 800)
                    );

                    // populate the default media queries
                    $this->unq_base = uniqid();
                    $this->default_m_queries = array(
                        $this->unq_base.'1' => array(
                            "label" => __("Large Desktop", "tvr-microthemer"),
                            "query" => "@media (min-width: 1200px)",
                            "min" => 1200,
                            "max" => 0),
                        $this->unq_base.'2' => array(
                            "label" => __("Desktop & Tablet", "tvr-microthemer"),
                            "query" => "@media (min-width: 768px) and (max-width: 979px)",
                            "min" => 768,
                            "max" => 979),
                        $this->unq_base.'3' => array(
                            "label" => __("Tablet & Phone", "tvr-microthemer"),
                            "query" => "@media (max-width: 767px)",
                            "min" => 0,
                            "max" => 767),
                        $this->unq_base.'4' => array(
                            "label" => __("Phone", "tvr-microthemer"),
                            "query" => "@media (max-width: 480px)",
                            "min" => 0,
                            "max" => 480)
                    );
                    // alternative mobile first media queries
                    $this->mobile_first_mqs = array(
                        $this->unq_base.'mf1' => array(
                            "label" => __("Tablet >", "tvr-microthemer"),
                            "query" => "@media (min-width: 767px)",
                            "min" => 767,
                            "max" => 0),
                        $this->unq_base.'mf2' => array(
                            "label" => __("Desktop >", "tvr-microthemer"),
                            "query" => "@media (min-width: 979px)",
                            "min" => 979,
                            "max" => 0),
                        $this->unq_base.'mf3' => array(
                            "label" => __("Large Desktop >", "tvr-microthemer"),
                            "query" => "@media (min-width: 1200px)",
                            "min" => 1200,
                            "max" => 0)
                    );
                    // semantically defined breakpoints
                    $this->mobile_first_semantic_mqs = array(
                        $this->unq_base.'mfs1' => array(
                            "label" => __("Narrow <", "tvr-microthemer"),
                            "query" => "@media (max-width: 480px)",
                            "min" => 0,
                            "max" => 480),
                        $this->unq_base.'mfs2' => array(
                            "label" => __("2 Col >", "tvr-microthemer"),
                            "query" => "@media (min-width: 767px)",
                            "min" => 767,
                            "max" => 0),
                        $this->unq_base.'mfs3' => array(
                            "label" => __("2 to 3 Col", "tvr-microthemer"),
                            "query" => "@media (min-width: 767px) and (max-width: 978px)",
                            "min" => 767,
                            "max" => 978),
                        $this->unq_base.'mfs4' => array(
                            "label" => __("3 Col >", "tvr-microthemer"),
                            "query" => "@media (min-width: 979px)",
                            "min" => 979,
                            "max" => 0),
                        $this->unq_base.'mfs5' => array(
                            "label" => __("3 Col to Wide", "tvr-microthemer"),
                            "query" => "@media (min-width: 979px) and (max-width: 1199px)",
                            "min" => 979,
                            "max" => 1199),
                        $this->unq_base.'mfs6' => array(
                            "label" => __("Wide >", "tvr-microthemer"),
                            "query" => "@media (min-width: 1200px)",
                            "min" => 1200,
                            "max" => 0)
                    );
                    $this->mq_sets['Desktop_first_device_MQs'] = $this->default_m_queries;
                    $this->mq_sets['Mobile_first_device_MQs'] = $this->mobile_first_mqs;
                    $this->mq_sets['Mobile_first_semantic_MQs'] = $this->mobile_first_semantic_mqs;

                    // define the default preferences here
                    $this->default_preferences = array(
                        "gzip" => 1,
                        "ie_notice" => 1,
                        "safe_mode_notice" => 1,
                        "css_important" => 1,
                        "pie_by_default" => 0,
                        //"admin_bar_main_ui" => 0, // adding
                        "admin_bar_preview" => 0,
                        "admin_bar_shortcut" => 1,
                        "top_level_shortcut" => 0,
                        "first_and_last" => 0,
                        "all_devices_default_width" => '',
                        "returned_ajax_msg" => '',
                        "returned_ajax_msg_seen" => 1,
                        "edge_mode" => 0,
                        "edge_config" => array(),
                        "tooltip_delay" => 500,
                        // "auto_capitalize" => 0 later, need to save folder name like selector (not just param)
                    );

                    // preferences that should not be reset if user resets global preferences (not actually an option yet)
                    $this->default_preferences_dont_reset = array(
                        "preview_url" => $this->home_url,
                        "previous_version" => $this->version,
                        "buyer_email" => '',
                        "buyer_validated" => false,
                        "active_theme" => 'customised',
                        "theme_in_focus" => '',
                        "last_viewed_selector" => '',
                        "mq_device_focus" => 'all-devices',
                        "pg_focus" => 'font',
                        "user_set_mq" => false,
                        "m_queries" => $this->default_m_queries,
                        "code_tabs" => $this->custom_code,
                        "my_props" => $this->default_my_props,
                        "show_code_editor" => 0,
                        "show_rulers" => 1,
                        "show_interface" => 1,
                        "initial_scale" => 0,
                        "show_adv_wizard" => 0,
                        "adv_wizard_tab" => 'refine-targeting',
                        "left_menu_down" => 1,

                        // I think I store true/false ie settings in preferences so that frontend script
                        // doesn't need to pull out all the options from the DB in order to enqueue the stylesheets.
                        // This will have an overhaul soon anyway.
                        "ie_css" => array('all' => '', 'nine' => '', 'eight' => '', 'seven' => ''),
                    );

                    // get the styles from the DB
                    $this->getOptions();

                    // get the css props
                    $this->getPropertyOptions();

                    // get/set the preferences
                    $this->getPreferences();

                    // create micro-themes dir/blank active-styles.css, copy pie if doesn't exist
                    $this->setup_micro_themes_dir();

                    // get the file structure - and create micro_root dir if it doesn't exist
                    $this->file_structure = $this->dir_loop($this->micro_root_dir);

                    // Write Microthemer version specific array data to JS file (can be static for each version).
                    // This can be done in dev mode only (also, some servers don't like creating JS files)
                    if (TVR_DEV_MODE){
                        $this->write_mt_version_specific_js();
                    }

                    // if this is a request for dynamic JS, return and die. (this is currently redundant)
                    if (isset($_GET['dynamic_mt_js'])){
                        include $this->thisplugindir . '/includes/js-dynamic.php';
                        die();
                    }

                    $ext_updater_file = dirname(__FILE__) .'/includes/plugin-updates/1.5/plugin-update-checker.php';
                    if ( TVR_MICRO_VARIANT == 'themer' and file_exists($ext_updater_file) ) {
                        require $ext_updater_file;
                        $MyUpdateChecker = new PluginUpdateChecker(
                            'http://themeover.com/wp-content/tvr-auto-update/meta-info.json?'.$this->time, // prevent cached file from loading
                            __FILE__,
                            'microthemer'
                        );
                    }

                    // we don't want the WP admin bar on any Microthemer pages
                    add_filter('show_admin_bar', '__return_false');

					// "Constants" setup
					// get value for safe mode
					if ( (gettype( ini_get('safe_mode') ) == 'string') ) {
						if ( ini_get('safe_mode') == 'off' ) {
							define('SAFE_MODE', FALSE);
						}
						else {
							define( 'SAFE_MODE', ini_get('safe_mode') );
						}
					}
					else {
						define( 'SAFE_MODE', ini_get('safe_mode') );
					}
					// add scripts and styles
					add_action( 'admin_init', array(&$this, 'add_css'));

					// only microthemer needs custom jQuery and gzipping
					if (TVR_MICRO_VARIANT == 'themer') {
						add_action('admin_init', array(&$this, 'add_js'));
						// enable gzipping on UI page if defined
						if ( $_GET['page'] == basename(__FILE__) and $this->preferences['gzip'] == 1) {
							if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
								ob_start("ob_gzhandler");
							else
								ob_start();
						}
					}

                    /* PAGE SPECIFIC PHP PROCESSING (that must come before page is rendered)  */
                    if ($page == $this->microthemeruipage or $page == $this->preferencespage){
                        // update preferences (because admin bar prefs can't be updated if called later)
                        add_action( 'admin_init', array(&$this, 'process_preferences_form'));
                    }

                }
			}

            // add a link to the WP Toolbar (this was copied from frontend class - use better method later)
            function custom_toolbar_link($wp_admin_bar) {
                if (!empty($this->preferences['top_level_shortcut'])
                    and $this->preferences['top_level_shortcut'] == 1){
                    $parent = false;
                } else {
                    $parent = 'site-name';
                }

                $args = array(
                    'id' => 'wp-mcr-shortcut',
                    'title' => 'Microthemer',
                    'parent' => $parent,
                    'href' => $this->wp_admin_url . 'admin.php?page=' . $this->microthemeruipage,
                    'meta' => array(
                        'class' => 'wp-mcr-shortcut',
                        'title' => __('Jump to the Microthemer interface', 'tvr-microthemer')
                    )
                );
                $wp_admin_bar->add_node($args);
            }

            // check if an url is valid
            function is_valid_url( $url ) {
                if ( '' != $url ) {
                    /* Using a HEAD request, we'll be able to know if the URL actually exists.
                     * the reason we're not using a GET request is because it might take (much) longer. */
                    $response = wp_remote_head( $url, array( 'timeout' => 3 ) );
                    /* We'll match these status codes against the HTTP response. */
                    $accepted_status_codes = array( 200, 301, 302 );

                    /* If no error occured and the status code matches one of the above, go on... */
                    if ( ! is_wp_error( $response ) &&
                        in_array( wp_remote_retrieve_response_code( $response ), $accepted_status_codes ) ) {
                        /* Target URL exists. Let's return the (working) URL */
                        return $url;
                    }
                    /* If we have reached this point, it means that either the HEAD request didn't work or that the URL
                     * doesn't exist. This is a fallback so we don't show the malformed URL */
                    return '';
                }
                return $url;
            }

            // set defaults for user's property preferences
            function set_my_props_defaults(){
                $pg_label = '';
                $update2 = false;
                foreach ($this->propertyoptions as $prop_group => $array){
                    foreach ($array as $prop => $meta) {
                        // get translated pg label
                        if ( !empty( $meta['pg_label'] ) ){
                            $pg_label = $meta['pg_label'];
                        }
                        // is it a new pg group? get ready for props
                        if (empty($this->preferences['my_props'][$prop_group])){
                            $update2 = true;
                            $this->preferences['my_props'][$prop_group] = array(
                                'pg_label' => $pg_label,
                                'pg_props' => array()
                            );
                        }
                        // set prop but only if undefined (first install or new prop added in update)
                        if (empty($this->preferences['my_props'][$prop_group]['pg_props'][$prop])){
                            $update2 = true;
                            $this->preferences['my_props'][$prop_group]['pg_props'][$prop]['label'] = $meta['label'];
                            // this suggested values feature will come later
                            $this->preferences['my_props'][$prop_group]['pg_props'][$prop]['sug_values'] = array();
                            // is there a default unit?
                            if ( !empty($meta['default_unit']) ){
                                $this->preferences['my_props'][$prop_group]['pg_props'][$prop]['default_unit'] =
                                    $meta['default_unit'][0]; // [0] implicit pixels
                            }
                        }
                        // ensure that the default unit doesn't get lost
                        if (!empty($meta['default_unit']) and
                            empty($this->preferences['my_props'][$prop_group]['pg_props'][$prop]['default_unit'])){
                            $update2 = true;
                            $this->preferences['my_props'][$prop_group]['pg_props'][$prop]['default_unit'] =
                                $meta['default_unit'][0]; // [0] implicit pixels
                        }
                    }
                }
                return $update2;
            }

            // load full set of suggested CSS units
            function update_my_props_array($config, $new_css_units){
                foreach ($this->preferences['my_props'] as $prop_group => $array){
                    foreach ($this->preferences['my_props'][$prop_group]['pg_props'] as $prop => $arr){
                        // skip props with no default unit
                        if (empty($this->preferences['my_props'][$prop_group]['pg_props'][$prop]['default_unit'])){
                            continue;
                        }
                        if ($config['mode'] == 'set'){
                            $new_unit = $this->propertyoptions[$prop_group][$prop]['default_unit'][$config['set_key']];
                        } elseif ($config['mode'] == 'post'){
                            if (!empty($new_css_units[$prop_group][$prop])){
                                $new_unit = $new_css_units[$prop_group][$prop];
                            }
                            // set all box model the same
                            $box_model_rel = false;
                            $first_in_group = false;
                            if (!empty($this->propertyoptions[$prop_group][$prop]['rel'])){
                                $box_model_rel = $this->propertyoptions[$prop_group][$prop]['rel'];
                            }
                            if (!empty($this->propertyoptions[$prop_group][$prop]['sub_label'])){
                                $first_in_group = $this->propertyoptions[$prop_group][$prop]['sub_label'];
                                $first_in_group_val = $new_unit;
                            }
                            if ($box_model_rel){
                                $new_unit = $first_in_group_val;
                            }
                        }
                        $this->preferences['my_props'][$prop_group]['pg_props'][$prop]['default_unit'] = $new_unit;
                    }
                }
                return $this->preferences['my_props'];
            }

            // ensure all preferences are defined
            function ensure_defined_preferences($full_preferences){
                $update = $update2 = false;
                foreach ($full_preferences as $key => $value){
                    if ( !isset($this->preferences[$key]) ){
                        $update = true;
                        $this->preferences[$key] = $value;
                    }
                }

                // new CSS props will be added over time and the default unit etc must be assigned.
                $update2 = $this->set_my_props_defaults();

                // save new defined prefs if necessary
                if ($update or $update2){
                    $this->savePreferences($this->preferences);
                }
            }


			// @taken from ngg gallery: http://wordpress.org/extend/plugins/nextgen-gallery/
			function required_version() {
				global $wp_version;
				$this->users_wp_version = $wp_version;
				// Check for WP version installation
				$wp_ok  =  version_compare($wp_version, $this->minimum_wordpress, '>=');
				// if requirements not met
				if ( ($wp_ok == FALSE) ) {
					add_action(
						'admin_notices',
						create_function(
							'',
							'echo \'<div id="message" class="error"><p><strong>' .
								wp_kses(__('Sorry, Microthemer only runs on WordPress version %s or above. Deactivate Microthemer to remove this message.', 'tvr-microthemer'), array()) .
							'</strong></p></div>\';'
						)
					);
					return false;
				}
				return true;
			}

			// @taken from ngg gallery: http://wordpress.org/extend/plugins/nextgen-gallery/
			function check_memory_limit() {
				// get the real memory limit before some increase it
				$this->memory_limit = (int) substr( ini_get('memory_limit'), 0, -1);
				// Wordpress requires 16MB to run
				if ( ($this->memory_limit != 0) and ($this->memory_limit < 16 ) ) {
					add_action(
						'admin_notices',
						create_function(
							'',
							'echo \'<div id="message" class="error"><p><strong>' .
								wp_kses(__('Sorry, Microthemer requires a memory limit of 16MB or higher to run. Your memory limit is less than this. Deactivate Microthemer to remove this message.', 'tvr-microthemer'), array()) .
							'</strong></p></div>\';'
						)
					);
					return false;
				}
				return true;
			}

			// check trial mode
			function trial_check() {
				if ( $this->trial === false ) {
					add_action(
						'admin_notices',
						create_function(
							'',
							'echo \'<div id="message" class="error"><p><strong>' .
								sprintf(wp_kses(__('Please %s to unlock the full program.', 'tvr-microthemer'), array()) . '<a target="_blank" href="http://themeover.com/microthemer/">' . wp_kses(__('purchase Microthemer', 'tvr-microthemer'), array()) . '</a>' ) .
							'</strong></p></div>\';'
						)
					);
					return false;
				}
				return true;
			}

			// Microthemer dedicated menu
			function microthemer_dedicated_menu() {
				add_menu_page(__('Microthemer UI', 'tvr-microthemer'), 'Microthemer', 'administrator', $this->microthemeruipage, array(&$this,'microthemer_ui_page'));
				add_submenu_page('options.php',
					__('Manage Design Packs', 'tvr-microthemer'),
					__('Manage Packs', 'tvr-microthemer'),
					'administrator', $this->microthemespage, array(&$this,'manage_micro_themes_page'));
				add_submenu_page('options.php',
					__('Manage Single Design Pack', 'tvr-microthemer'),
					__('Manage Single Pack', 'tvr-microthemer'),
					'administrator', $this->managesinglepage, array(&$this,'manage_single_page'));
				add_submenu_page($this->microthemeruipage, __('Microthemer Preferences', 'tvr-microthemer'),
					__('Preferences', 'tvr-microthemer'), 'administrator', $this->preferencespage, array(&$this,'microthemer_preferences_page'));
			}

			// Add Microloader menu link in appearance menu
			function microloader_menu_link() {
				add_theme_page('Microloader', 'Microloader', 'administrator', $this->microthemespage, array(&$this,'manage_micro_themes_page'));
			}

            // add support for translation
            function tvr_load_textdomain() {
                load_plugin_textdomain( 'tvr-microthemer', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
            }

			// add js
			function add_js() {
				if (TVR_MICRO_VARIANT == 'themer') {

					// register and enqueue plugin scripts
					wp_register_script(
                        'tvr_mcth_cssprops',
                        $this->thispluginurl.'js/version-specific.js?v='.$this->version
                    );

                    // params in JS url can cause probs so just print to top of UI for now.
                    // Use wp_localize_script method when we move on to optimisations: http://stackoverflow.com/questions/22614271/how-to-dynamically-generate-javascript-code-in-wordpress
                    /*wp_register_script( 'tvr_dynamic_js',
                        $this->wp_admin_url . 'admin.php?page=' .
                        $this->microthemeruipage.'&dynamic_mt_js=1&nocache='.$this->time);*/

                    wp_register_script( 'tvr_mcth_colorbox',
                        $this->thispluginurl.'js/colorbox/1.3.19/jquery.colorbox-min.js?v='.$this->version, 'jquery' );
                    wp_register_script( 'tvr_scrollbars',
                        $this->thispluginurl.'js/scroll/jquery.mCustomScrollbar.concat.min.js?v='.$this->version );
                        //$this->thispluginurl.'js/scroll/jquery.mCustomScrollbar.js?v='.$this->version );
					wp_register_script( 'tvr_sprintf',
                        $this->thispluginurl.'js/sprintf/sprintf.min.js?v='.$this->version, 'jquery' );

                    if (!$this->optimisation_test){
                        wp_enqueue_media(); // adds over 1000 lines of code to footer
                    }

					wp_enqueue_script( 'tvr_sprintf');

                    // enqueue jquery ui stuff
                    wp_enqueue_script( 'jquery-ui-core' );
                    wp_enqueue_script( 'jquery-ui-position', 'jquery');
                    wp_enqueue_script( 'jquery-ui-sortable', 'jquery');
					wp_enqueue_script( 'jquery-ui-slider', 'jquery');
                    wp_enqueue_script( 'jquery-ui-autocomplete', 'jquery');
                    wp_enqueue_script( 'jquery-ui-button', 'jquery');
                    wp_enqueue_script( 'jquery-ui-resizable', 'jquery');
                    wp_enqueue_script( 'jquery-ui-tooltip', 'jquery');

                    wp_enqueue_script( 'tvr_mcth_colorbox' );
					//wp_enqueue_script( 'tvr_mcth_tabs' );
                    wp_enqueue_script( 'tvr_scrollbars', 'jquery' );

					wp_enqueue_script( 'spectrum-colorpicker', $this->thispluginurl . 'js/colorpicker/spectrum.js?v=' . $this->version, array( 'jquery' ) );
					wp_enqueue_script( 'tvr_mcth_cssprops' );
                    wp_enqueue_script( 'tvr_dynamic_js' );

					// load js strings for translation
                    $js_i18n_main = $js_i18n_manage = array();
					include_once $this->thisplugindir . 'includes/js-i18n.inc.php';

					// load the main script
                    if ($_GET['page'] == $this->microthemeruipage){
                        if (!TVR_DEV_MODE){
                            wp_register_script( 'tvr_mcth_custom_ui',
                                $this->thispluginurl.'js/min/microthemer.js?v='.$this->version );
                        } else {
                            wp_register_script( 'tvr_mcth_custom_ui',
                                $this->thispluginurl.'js/tvr-microthemer.js?v='.$this->version );
                        }
						wp_localize_script( 'tvr_mcth_custom_ui', 'js_i18n_main', $js_i18n_main);
                        wp_enqueue_script( 'tvr_mcth_custom_ui');
                    }
                    // manage micro themes script
                    else {
                        if (!TVR_DEV_MODE) {
                            wp_register_script('tvr_mcth_custom_man',
                                $this->thispluginurl . 'js/min/manage-micro.js?v=' . $this->version);
                        } else {
                            wp_register_script( 'tvr_mcth_custom_man',
                                $this->thispluginurl.'js/tvr-manage-micro.js?v='.$this->version );
                        }

						wp_localize_script( 'tvr_mcth_custom_man', 'js_i18n_manage', $js_i18n_manage);
                        wp_enqueue_script( 'tvr_mcth_custom_man' );
                    }
				}
			}

			// add css
			function add_css() {
				// register
                wp_register_style('tvrGFonts', '//fonts.googleapis.com/css?family=Open+Sans:400,700,700italic,400italic');
                wp_enqueue_style( 'tvrGFonts');
				wp_register_style( 'tvr_mcth_styles', $this->thispluginurl.'css/styles.css?v='.$this->version );

                // enqueue
				if (TVR_MICRO_VARIANT == 'themer') {
					// color picker
					wp_enqueue_style( 'spectrum-colorpicker', $this->thispluginurl . 'js/colorpicker/spectrum.css?v=' . $this->version );
					// colorbox
					wp_register_style( 'tvr_mcth_colorbox_styles', $this->thispluginurl.'js/colorbox/1.3.19/colorbox.css?v='.$this->version );
					wp_enqueue_style( 'tvr_mcth_colorbox_styles' );
					// jquery ui styling
					wp_register_style( 'tvr_jqui_styles', $this->thispluginurl.'css/jquery-ui1.11.4.min.css?v='.$this->version );
					wp_enqueue_style( 'tvr_jqui_styles' );
				}

                // enqueue main stylesheet
                wp_enqueue_style( 'tvr_mcth_styles' );

                // preferences page doesn't want toolbar hack, so add to stylesheet conditionally
                if ($_GET['page'] != $this->preferencespage){
                    $custom_css = "
                        html, html.wp-toolbar {
                          padding-top:0
                        }";
                    wp_add_inline_style( 'tvr_mcth_styles', $custom_css );
                }
			}

			// build array for property/value input fields
			function getPropertyOptions() {
                $propertyOptions = array();
                $legacy_groups = array();
				include $this->thisplugindir . 'includes/property-options.inc.php';
				$this->propertyoptions = $propertyOptions;
                // populate $property_option_groups array
                foreach ($propertyOptions as $prop_group => $array){
                    foreach ($array as $prop => $meta) {
                        if ( !empty($meta['pg_label']) ){
                            $this->property_option_groups[$prop_group] = $meta['pg_label'];
                            break;
                        }
                    }
                }
                $this->legacy_groups = $legacy_groups;

			}

            // create static JS file for property options etc that relate to the current version of MT
            function write_mt_version_specific_js() {
                // Create new file if it doesn't already exist
                $js_file = $this->thisplugindir . 'js/version-specific.js';
                if (!$write_file = fopen($js_file, 'w')) {
                    $this->log(
                        wp_kses(__('Permission Error', 'tvr-microthemer'), array()), 'tvr-microthemer',
                    '<p>' . sprintf(wp_kses(__('WordPress does not have permission to create: %s', 'tvr-microthemer'), array()), $this->root_rel($js_file) . '. '.$this->permissionshelp ) . '</p>'
                    );
                }
                else {
                    // some CSS properties need adjustment for jQuery .css() call
                    $exceptions = array(
                        'font-family'  => 'google-font',
                        'list-style-image'  => 'list-style-image',
                        'text-shadow'  => array(
                            'text-shadow-color',
                            'text-shadow-x',
                            'text-shadow-y',
                            'text-shadow-blur'),
                        'box-shadow'  => array(
                            'box-shadow-color',
                            'box-shadow-x',
                            'box-shadow-y',
                            'box-shadow-blur'),
                        'background-img-full'  => array(
                            'background-image',
                            'gradient-angle',
                            'gradient-a',
                            'gradient-b',
                            'gradient-b-pos',
                            'gradient-c'
                            ),
                        'background-position' => 'background-position',
                        'background-position-custom' => array(
                            'background-position-x',
                            'background-position-y'
                        ),
                        'background-repeat' => 'background-repeat',
                        'background-attachment' => 'background-attachment',
                        'background-size' => 'background-size',
                        'background-clip' => 'background-clip',
                        'border-top-left-radius' => 'radius-top-left',
                        'border-top-right-radius' => 'radius-top-right',
                        'border-bottom-right-radius' => 'radius-bottom-right',
                        'border-bottom-left-radius' =>'radius-bottom-left',

                        'keys' => array(
                            'background-position-x' => array(
                                '0%' => 'left',
                                '100%' => 'right',
                                '50%' => 'center'
                            ),
                            'background-position-y' => array(
                                '0%' => 'top',
                                '100%' => 'bottom',
                                '50%' => 'center'
                            ),
                            'gradient-angle' => array(
                                '180deg' => 'top to bottom',
                                '0deg' => 'bottom to top',
                                '90deg' => 'left to right',
                                '-90deg' => 'right to left',
                                '135deg' => 'top left to bottom right',
                                '-45deg' => 'bottom right to top left',
                                '-135deg' => 'top right to bottom left',
                                '45deg' => 'bottom left to top right'
                            ),
                            // webkit has a different interpretation of the degrees - doh!
                            'webkit-gradient-angle' => array(
                                '-90deg' => 'top to bottom',
                                '90deg' => 'bottom to top',
                                '0deg' => 'left to right',
                                '180deg' => 'right to left',
                                '-45deg' => 'top left to bottom right',
                                '135deg' => 'bottom right to top left',
                                '-135deg' => 'top right to bottom left',
                                '45deg' => 'bottom left to top right'
                            )
                        )
                    );

                    $i = 0;
                    $data = '';
                    $done = array();
                    $input_props = array();
                    $combo = array();
                    $css_props = array();
                    foreach ($this->propertyoptions as $prop_group => $array) {
                        $j = 0;
                        foreach ($array as $prop => $junk) {
                            if (array_key_exists($prop, $exceptions)) {
                                $val = $exceptions[$prop];
                            } else {
                                $val = str_replace('_', '-', $prop);
                            }
                            $input_props[$prop_group][$prop] = $val;
                            if (empty($done[$val])) {
                                $css_props[$prop_group][] = $val;
                                $done[$val] = true;
                                ++$i;
                                ++$j;
                            }
                            // populate combobox array
                            if (
                                !empty($array[$prop]['select_options'])){
                                $combo[$prop] = $array[$prop]['select_options'];
                            }
                        }
                    }
                    // text/box-shadow need to be called as one
                    $css_props['shadow'][] = 'text-shadow';
                    $css_props['shadow'][] = 'box-shadow';
                    $css_props['background'][] = 'background-img-full'; // for storing full string (inc gradient)
                    $css_props['background'][] = 'extracted-gradient'; // for storing just gradient (for mixed-comp check)
                    $css_props['gradient'][] = 'background-image'; // gradient group needs this
                    $css_props['gradient'][] = 'background-img-full'; // for storing full string (inc gradient)
                    $css_props['gradient'][] = 'extracted-gradient';

                    // ready combo for MQ sets
                    $i = 0;
                    foreach ($this->mq_sets as $set => $junk){
                        if (++$i == 1){
                            $set.= ' (default)';
                        }
                        $mq_sets[] = str_replace('_', ' ', $set);
                    }
                    $combo['mq_sets'] = $mq_sets;

                    // ready combo for css_units
                    $combo['css_unit_sets'] = $this->css_unit_sets;
                    $combo['css_units'] = array_keys( $this->css_units );

                    // also write full css units array with descriptions
                    $data.= 'var TvrCSSUnits = ' . json_encode($this->css_units) . ';' . "\n\n";

                    // finish data string
                    $data.= 'var TvrPropExceptions = ' . json_encode($exceptions) . ';' . "\n\n";
                    $data.= 'var TvrInputProps = ' . json_encode($input_props) . ';' . "\n\n";
                    $data.= 'var TvrCSSProps = ' . json_encode($css_props) . ';' . "\n\n";
                    $data.= 'var TvrInputCompProps = {};' . "\n\n";
                    $data.= 'var TvrPGs = ' . json_encode($this->property_option_groups) . ';' . "\n\n";
                    // TvrCombo needs to be updated with dynamic JS file for suggested values based on user action
                    $data.= 'var TvrCombo = ' . json_encode($combo) . ';' . "\n\n";
                    $data.= 'var TvrMobPreview = ' . json_encode($this->mob_preview) . ';' . "\n\n";

                    // write and close file
                    fwrite($write_file, $data);
                    fclose($write_file);
                }

            }

			// @return array - Retrieve the plugin options from the database.
			function getOptions() {
				// default options (html layout sections only - no default selectors)
				if (!$theOptions = get_option($this->optionsName)) {
					$theOptions = array();
                    $theOptions['body'] = '';
                    $theOptions['header'] = '';
                    $theOptions['main_menu'] = '';
                    $theOptions['content'] = '';
                    $theOptions['pages'] = '';
                    $theOptions['posts_general'] = '';
                    $theOptions['post_single_view'] = '';
                    $theOptions['post_navigation'] = '';
                    $theOptions['comments'] = '';
                    $theOptions['leave_reply_form'] = '';
                    $theOptions['sidebar'] = '';
                    $theOptions['footer'] = '';
                    $theOptions['non_section']['hand_coded_css'] = '';

					// add_option rather than update_option (so autoload can be set to no)
					add_option($this->optionsName, $theOptions, '', 'no');
				}
				$this->options = $theOptions;
			}

			// @return array - Retrieve the plugin plugin preferences from the database.
			function getPreferences() {

                $full_preferences = array_merge($this->default_preferences, $this->default_preferences_dont_reset);

				// default preferences
				if (!$thePreferences = get_option($this->preferencesName)) {
					$thePreferences = $full_preferences;
					// add_option rather than update_option (so autoload can be set to no)
					add_option($this->preferencesName, $thePreferences, '', 'no');
				}

				$this->preferences = $thePreferences;

                // check if this is a new version of Microthemer
                if ($this->preferences['previous_version'] != $this->version){
                    $this->new_version = true;
                }

                // ensure preferences are defined (for when I add new preferences that upgrading users won't have)
                $this->ensure_defined_preferences($full_preferences);
			}

			// Save the preferences
			function savePreferences($pref_array) {
                global $wpdb;
				// get the full array of preferences
				$thePreferences = get_option($this->preferencesName);
				// update the preferences array with passed values
				foreach ($pref_array as $key => $value) {
					$thePreferences[$key] = $value;
				}
				// save in DB and go to relevant page
				update_option($this->preferencesName, $thePreferences);
				// update the global preferences array
				$this->preferences = $thePreferences;
				return true;
			}

            // common function for outputting yes/no radios
            function output_radio_input_lis($opts, $hidden = ''){

                foreach ($opts as $key => $array) {

					// ensure various vars are defined
					$array['label_no'] = ( !empty($array['label_no']) ) ? $array['label_no'] : '';
					$array['default'] = ( !empty($array['default']) ) ? $array['default'] : '';

                    // skip edge mode if not available
                    if ($key == 'edge_mode' and !$this->edge_mode['available']){
                        continue;
                    }

                    ?>
                    <li class="fake-radio-parent <?php echo $hidden; ?>">
                        <label title="<?php echo htmlentities($array['explain']); ?>"><?php echo $array['label']; ?>:</label>

                                <span class="yes-wrap p-option-wrap">
                                <input type='radio' autocomplete="off" class='radio'
                                       name='tvr_preferences[<?php echo $key; ?>]' value='1'
                                    <?php
                                    if ( !empty($this->preferences[$key]) or $array['default'] == 'yes' ) {
                                        echo 'checked="checked"';
                                        $on = 'on';
                                    } else {
                                        $on = '';
                                    }
                                    ?>
                                    />
                                    <span class="fake-radio <?php echo $on; ?>"></span>
									<span class="ef-label"><?php printf(wp_kses(__('Yes', 'tvr-microthemer'), array())); ?></span>
                                </span>
                                <span class="no-wrap p-wrap-wrap">
                                    <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[<?php echo $key; ?>]' value='0'
                                        <?php
                                        if ( empty($this->preferences[$key]) or $array['default'] == 'no' ) {
                                            echo 'checked="checked"';
                                            $on = 'on';
                                        } else {
                                            $on = '';
                                        }
                                        ?>
                                        />
                                        <span class="fake-radio <?php echo $on; ?>"></span>
										<span class="ef-label"><?php printf(wp_kses(__('No', 'tvr-microthemer'), array())); ?></span>
                                </span>
                    </li>
                <?php
                }
            }

            // common function for text inputs/combos
            function output_text_combo_lis($opts, $hidden = ''){
                foreach ($opts as $key => $array) {
                    $input_id = $input_class = $arrow_class = $class = $rel = $arrow = '';
                    $input_name = 'tvr_preferences['.$key.']';
                    $input_value = ( !empty($this->preferences[$key]) ) ? $this->preferences[$key] : '';

                    // does it need a custom id?
                    if (!empty($array['input_id'])){
                        $input_id = $array['input_id'];
                    }
                    // does it need a custom input class?
                    if (!empty($array['input_class'])){
                        $input_class = $array['input_class'];
                    }
                    // does it need a custom arrow class?
                    if (!empty($array['arrow_class'])){
                        $arrow_class = $array['arrow_class'];
                    }
                    // does it need a custom input name?
                    if (!empty($array['input_name'])){
                        $input_name = $array['input_name'];
                    }
                    // does it need a custom input value?
                    if (!empty($array['input_value'])){
                        $input_value = $array['input_value'];
                    }
                    // is it a combobox?
                    if (!empty($array['combobox'])){
                        $class = 'combobox';
                        $rel = 'rel="'.$array['combobox'].'"';
                        $arrow = '<span class="combo-arrow '.$arrow_class.'"></span>';
                    }
                    ?>
                    <li class="input-wrap <?php echo $hidden; ?>">
                        <label title="<?php echo htmlentities($array['explain']); ?>" class="text-label">
                            <?php echo $array['label']; ?>:</label>
                        <input type='text' autocomplete="off" name='<?php echo $input_name; ?>'
                               id="<?php echo $input_id; ?>"
                               class="<?php echo $class . ' ' . $input_class; ?>" <?php echo $rel; ?>
                               value='<?php echo esc_attr($input_value); ?>' />
                        <?php echo $arrow; ?>
                    </li>
                <?php
                }
            }

			// create revisions table if it doesn't exist
			function createRevsTable() {
				global $wpdb;
				$table_name = $wpdb->prefix . "micro_revisions";
				// only execut following code if table doesn't exist.
				// dbDelta function wouldn't overwrite table, but table version num shouldn't be updated with current plugin version if it already exists
				if( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
					if ( ! empty( $wpdb->charset ) )
						$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
					if ( ! empty( $wpdb->collate ) )
						$charset_collate .= " COLLATE $wpdb->collate";
					$sql = "CREATE TABLE $table_name (
					  id mediumint(9) NOT NULL AUTO_INCREMENT,
					  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					  user_action VARCHAR(55) DEFAULT '' NOT NULL,
					  data_size VARCHAR(10) DEFAULT '' NOT NULL,
					  settings longtext NOT NULL,
					  UNIQUE KEY id (id)
					  ) $charset_collate;";
					require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
					dbDelta($sql);
					// store the table version in the wp_options table (useful for upgrading the DB)
					add_option("micro_revisions_version", $this->version);
					return true;
				}
				else {
					return false;
				}
			}

			// Update the Revisions Table
			function updateRevisions($save_data, $user_action = '') {
				// create revisions table if it doesn't already exist
				if ($this->createRevsTable()) {
                    $this->log('','','error', 'revisions');
				}
				// add the revision to the table
				global $wpdb;
				$table_name = $wpdb->prefix . "micro_revisions";
				$serialized_data = serialize($save_data);
				$data_size = round(strlen($serialized_data)/1000).'KB';
				$rows_affected = $wpdb->insert( $table_name, array(
					'time' => current_time('mysql'),
					'user_action' => $user_action,
					'data_size' => $data_size,
					'settings' => $serialized_data )
				);
				// check if an old revision needs to be deleted
				$revs = $wpdb->get_results("select id from $table_name order by id asc");
				if ($wpdb->num_rows > 50) {
					$sql = "delete from $table_name where id = ".$wpdb->escape($revs[0]->id);
					$wpdb->query($sql);
				}
				return true;
			}

			// Get Revisions for displaying in table
			function getRevisions() {
				// get the full array of revisions
				global $wpdb;
				$table_name = $wpdb->prefix . "micro_revisions";
				$revs = $wpdb->get_results("select id, user_action, data_size, date_format(time, '%D %b %Y %H:%i') as datetime
				from $table_name order by id desc");
				$total_rows = $wpdb->num_rows;
				// if no revisions, explain
				if ($total_rows == 0) {
					return '<span id="revisions-table">' .
						wp_kses(__('No Revisions have been created yet. This will happen after your next save.', 'tvr-microthemer'), array()) .
					'</span>';
				}
				// if one revision, it's the same as the current settings, explain
				if ($total_rows == 1) {
					return '<span id="revisions-table">' .
						wp_kses(__('The only revision is a copy of your current settings.', 'tvr-microthemer'), array()) .
					'</span>';
				}
				// revisions exist so prepare table
				$restore_url = '&_wpnonce='.
				wp_create_nonce('tvr_restore_revision').'&action=restore_rev&tvr_rev=';
				$rev_table =
				'
				<table id="revisions-table">
            	<thead>
				<tr>
					<th>' . wp_kses(__('Revision', 'tvr-microthemer'), array()) . '</th>
                    <th>' . wp_kses(__('Date & Time', 'tvr-microthemer'), array()) . '</th>
                    <th>' . wp_kses(__('User Action', 'tvr-microthemer'), array()) . '</th>
					<th>' . wp_kses(__('Size', 'tvr-microthemer'), array()) . '</th>
                    <th>' . wp_kses(__('Restore', 'tvr-microthemer'), array()) . '</th>
                </tr>
				</thead>';
				$i = 0;
				foreach ($revs as $rev) {
					$rev_table.= '
					<tr>
						<td>'.$total_rows.' ';
						// explain which revision is the oldest
						if ($total_rows == 1) {
							$rev_table.= '(Oldest)';
						}
						$rev_table.='</td>
						<td>'.$rev->datetime.'</td>
						<td>'.$rev->user_action.'</td>
						<td>'.$rev->data_size.'</td>
						<td>';
						if ($i == 0) {
							$rev_table.= '(Current)';
						}
						else {
							$rev_table.='<span class="link restore-link" rel="'.$restore_url.$rev->id.'">' . wp_kses(__('Restore', 'tvr-microthemer'), array()) . '</span>';
						}
						$rev_table.='</td>
					</tr>';
					--$total_rows;
					++$i;
				}
				$rev_table.= '</table>';
				return $rev_table;
			}

			// Restore a revision
			function restoreRevision($rev_key) {
				// get the revision
				global $wpdb;
				$table_name = $wpdb->prefix . "micro_revisions";
				$rev = $wpdb->get_row("SELECT * FROM $table_name WHERE id = ".$wpdb->escape($rev_key));
				$rev->settings = unserialize($rev->settings);
				// restore to options DB field
				update_option($this->optionsName, $rev->settings);
				$this->options = get_option($this->optionsName); // this DB interaction doesn't seem necessary...
				return true;
			}

			// Save the UI styles to the database - create hybrid of settings from existing non-loaded styles and saved styles
			function saveUiOptions($theOptions){
				// create debug save file if specified at top of script
				if ($this->debug_save and !empty($this->preferences['theme_in_focus'])) {
					$debug_file = $this->micro_root_dir . $this->preferences['theme_in_focus'] . '/debug-save.txt';
					$write_file = fopen($debug_file, 'w');
					$data = '';
					$data.= "\n\n" . __('The new options', 'tvr-microthemer') . "\n\n";
					$data.= print_r($theOptions, true);
					$data.= "\n\n" . __('The existing options', 'tvr-microthemer') . "\n\n";
					$data.= print_r($this->options, true);
				}
				// loop through all the state trackers
				if (is_array($theOptions['non_section']['view_state'])) {
					foreach($theOptions['non_section']['view_state'] as $section_name => $array) {
						// loop through the selector trackers
						if (is_array($array)) {
							foreach ( $array as $css_selector => $view_state) {
                                // if the selector options haven't been pulled into the UI, use existing
								if ($css_selector != 'this' and $view_state == 0) {
									$theOptions[$section_name][$css_selector] = $this->options[$section_name][$css_selector];
									// media query values will also need to be pulled from existing settings
									if (!empty($this->options['non_section']['m_query']) and
									is_array($this->options['non_section']['m_query'])) {
										foreach ($this->options['non_section']['m_query'] as $m_key => $array) {
											if (!empty($array[$section_name][$css_selector])
                                                and is_array($array[$section_name][$css_selector])) {
												$theOptions['non_section']['m_query'][$m_key][$section_name][$css_selector] = $array[$section_name][$css_selector];
											}
										}
									}
								}


							}
						}
					}
				}
				if ($this->debug_save) {
					$data.= "\n\n" . __('The hybrid options', 'tvr-microthemer') . "\n\n";
					$data.= print_r($theOptions, true);
					fwrite($write_file, $data);
					fclose($write_file);
				}
				update_option($this->optionsName, $theOptions);
				$this->options = get_option($this->optionsName);

				return true;
			}

			// Resest the options.
			function resetUiOptions(){
				delete_option($this->optionsName);
				$this->getOptions(); // reset the defaults
				$pref_array = array();
				$pref_array['active_theme'] = 'customised';
				$pref_array['theme_in_focus'] = '';
				$this->savePreferences($pref_array);
				return true;
			}

			// clear the style definitions - leaving all the sections and selectors intact
			function clearUiOptions() {
				if (is_array($this->options['non_section']['view_state'])) {
					foreach($this->options['non_section']['view_state'] as $section_name => $array) {
						// loop through the selector trackers
						if (is_array($array)) {
							foreach ( $array as $css_selector => $view_state) {
								if ($css_selector == 'this') { continue; }
								// reset styles array to defaults
                                foreach ($this->property_option_groups as $group => $junk){
                                    $option_groups[$group] = '';
                                }
								$this->options[$section_name][$css_selector]['styles'] = $option_groups;
							}
						}
					}
				}
                // clear the custom code
                $this->options['non_section']['hand_coded_css'] = '';
                $this->options['non_section']['ie_css']['all'] = '';
                $this->options['non_section']['ie_css']['nine'] = '';
                $this->options['non_section']['ie_css']['eight'] = '';
                $this->options['non_section']['ie_css']['seven'] = '';
                // clear all media query settings
                $this->options['non_section']['m_query'] = array();

				// update the options in the DB
				update_option($this->optionsName, $this->options);
				$this->options = get_option($this->optionsName); // necessary?
				return true;
			}

            function log($short, $long, $type = 'error', $preset = false, $vars = array()){
                // some errors are the same, reuse the text
                if ($preset) {
                    if ($preset == 'revisions'){
                        $this->globalmessage[++$this->ei]['short'] = __('Revision log update failed.', 'tvr-microthemer');
                        $this->globalmessage[$this->ei]['type'] = 'error';
                        $this->globalmessage[$this->ei]['long'] = '<p>' . wp_kses(__('Adding your latest save to the revisions table failed.', 'tvr-microthemer'), array()) . '</p>';
                    } elseif ($preset == 'json-decode'){
                        $this->globalmessage[++$this->ei]['short'] = __('Decode json error', 'tvr-microthemer');
                        $this->globalmessage[$this->ei]['type'] = 'error';
                        $this->globalmessage[$this->ei]['long'] = '<p>' . sprintf(wp_kses(__('WordPress was not able to convert %s into a usable format.', 'tvr-microthemer'), array()), $this->root_rel($vars['json_file']) ) . '</p>';
                    }

                } else {
                    $this->globalmessage[++$this->ei]['short'] = $short;
                    $this->globalmessage[$this->ei]['type'] = $type;
                    $this->globalmessage[$this->ei]['long'] = $long;
                }
            }

            // save ajax-generated global msg in db for showing on next page load
            function cache_global_msg(){
                $pref_array = array();
                $pref_array['returned_ajax_msg'] = $this->globalmessage;
                $pref_array['returned_ajax_msg_seen'] = 0;
                $this->savePreferences($pref_array);
            }

            // display the logs
            function display_log(){
                // if the page is reloading after an ajax request, we may have unseen status messages to show - merge the two
                if (!empty($this->preferences['returned_ajax_msg']) and !$this->preferences['returned_ajax_msg_seen']){
                    $cached_global = $this->preferences['returned_ajax_msg'];
                    if (is_array($this->globalmessage)){
                        $this->globalmessage = array_merge($this->globalmessage, $cached_global);
                    } else {
                        $this->globalmessage = $cached_global;
                    }
                    // clear the cached message as it is beign shown
                    $pref_array['returned_ajax_msg'] = '';
                    $pref_array['returned_ajax_msg_seen'] = 1;
                    $this->savePreferences($pref_array);
                }
                if (is_array($this->globalmessage)){
                    // sort the logs by type
                    $logs = array();
                    foreach ($this->globalmessage as $key => $log){
                       $logs[$log['type']][] = $log;
                    }
                    $html = '
                    <ul class="logs">';
                    // Note: "dev-notice" type tracks successes that don't need to be shown to the user
                    $types = array('error', 'warning', 'notice');
                    // output logs in order
                    $i = 0;
                    foreach ( $types as $type){
                        if (!empty($logs[$type])and is_array($logs[$type])){
                            foreach ($logs[$type] as $key => $log){
                                $html.= $this->display_log_item($type, $log, $key);
                            }
                        }
                    }
                    $html.= '</ul>';
                    return $html;
                }
            }

            // display log item - used as template so need as function to keep html consistent
            function display_log_item($type, $log, $key, $id = ''){
                $html = '
                <li '.$id.' class="tvr-'.$type.' tvr-message row-'.($key+1).'">
                    <span class="short">'.$log['short'].'</span>
                    <div class="long">'.$log['long'].'</div>
                </li>';
                return $html;
            }

            // circumvent max_input_vars by passing one serialised input that can be unpacked with this function
            function my_parse_str($string, &$result) {
                if($string==='') return false;
                $result = array();
                // find the pairs "name=value"
                $pairs = explode('&', $string);
                foreach ($pairs as $pair) {
                    // use the original parse_str() on each element
                    parse_str($pair, $params);
                    $k=key($params);
                    if(!isset($result[$k])) {
                        $result+=$params;
                    }
                    else {
                        if (is_array($result[$k])){
                            //echo '<pre>key:'. $k . "\n";
                            //echo 'params:';
                            //print_r($params);
                            //$result[$k]+=$params[$k];
                            $result[$k] = $this->array_merge_recursive_distinct($result[$k], $params[$k]);
                            // 'result:';
                            //print_r($result);
                            //echo '</pre>';
                        }
                    } //
                    //else $result[$k]+=$params[$k];
                }
                return true;
            }

            // better recursive array merge function listed on the function's PHP page
            function array_merge_recursive_distinct ( array &$array1, array &$array2 ){
                $merged = $array1;
                foreach ( $array2 as $key => &$value )
                {
                    if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
                    {
                        $merged [$key] = $this->array_merge_recursive_distinct ( $merged [$key], $value );
                    }
                    else
                    {
                        $merged [$key] = $value;
                    }
                }

                return $merged;
            }

            // process preferences form
            function process_preferences_form(){
                if (isset($_POST['tvr_save_preferences_submit'])) {
                    check_admin_referer('tvr_preferences_submit');
                    $pref_array = $_POST['tvr_preferences'];
                    // CSS units need saving in a different way (as my_props is more than just css units)
                    $pref_array = $this->update_default_css_units($pref_array);
                    if ($this->savePreferences($pref_array)) {
                        $this->log(
                            wp_kses(__('Preferences saved', 'tvr-microthemer'), array()), 'tvr-microthemer',
                            '<p>' . wp_kses(__('Your Microthemer preferences have been successfully updated.', 'tvr-microthemer'), array()) . '</p>',
                            'notice'
                        );
                        // the admin bar shortcut needs to be applied here else it will only show on next page load
                        if (!empty($this->preferences['admin_bar_shortcut']) and
                            $this->preferences['admin_bar_shortcut'] == 1) {
                            add_action( 'admin_bar_menu', array(&$this, 'custom_toolbar_link'), 999999);
                        } else {
                            remove_action( 'admin_bar_menu', array(&$this, 'custom_toolbar_link'), 999999 );
                        }
                    }
                }
            }

            // update the preferences array with the new units when the user saves the preferences
            function update_default_css_units($pref_array){
                // cache the posted css units
                $new_css_units = $pref_array['new_css_units'];
                // then discard as junk
                unset($pref_array['new_css_units']);
                $config['mode'] = 'post';
                // is it a set?
                if ( !empty($pref_array['load_css_unit_sets']) ){
                   $config['mode'] = 'set';
                   $config['set_key'] = array_search($pref_array['load_css_unit_sets'], $this->css_unit_sets);
                   $new_css_units = 'set';
                   // we don't want the set preference to be remembered or it will wipe any custom settings
                   $pref_array['load_css_unit_sets'] = '';
                }
                // update the existing my_props array
                $pref_array['my_props'] = $this->update_my_props_array($config, $new_css_units);
                return $pref_array;
            }

            // process posted zip file (do this on manage and single hence wrapped in a funciton )
            function process_uploaded_zip() {
                if (isset($_POST['tvr_upload_micro_submit'])) {
                    check_admin_referer('tvr_upload_micro_submit');
                    if ($_FILES['upload_micro']['error'] == 0) {
                        $this->handle_zip_package();
                    }
                    // there was an error - save in global message
                    else {
                        $this->log_file_upload_error($_FILES['upload_micro']['error']);
                    }
                }
            }

			// Microthemer UI page
			function microthemer_ui_page() {
				// only run code if it's the ui page
				if ( isset($_GET['page']) and $_GET['page'] == $this->microthemeruipage ) {

                    // simple ajax operations that can be executed from any page, pointing to ui page
                    if (isset($_GET['mcth_simple_ajax'])) {
                        check_admin_referer('mcth_simple_ajax');
                        // ajax - update preview url after page navigation
                        if (isset($_GET['tvr_microthemer_ui_preview_url'])) {
                            $preview_url = strip_tags(rawurldecode($_GET['tvr_microthemer_ui_preview_url']));
                            $pref_array = array();
                            $pref_array['preview_url'] = $preview_url;
                            $this->savePreferences($pref_array);
                            // kill the program - this action is always requested via ajax. no message necessary
                            die();
                        }
                        // wizard shows advanced options
                        if (isset($_GET['show_adv_wizard'])) {
                            $pref_array = array();
                            $pref_array['show_adv_wizard'] = intval($_GET['show_adv_wizard']);
                            $this->savePreferences($pref_array);
                            // kill the program - this action is always requested via ajax. no message necessary
                            die();
                        }
                        // code editor focus
                        if (isset($_GET['show_code_editor'])) {
                            $pref_array = array();
                            $pref_array['show_code_editor'] = intval($_GET['show_code_editor']);
                            $this->savePreferences($pref_array);
                            // kill the program - this action is always requested via ajax. no message necessary
                            die();
                        }
                        // ruler show/hide
                        if (isset($_GET['show_rulers'])) {
                            $pref_array = array();
                            $pref_array['show_rulers'] = intval($_GET['show_rulers']);
                            $this->savePreferences($pref_array);
                            // kill the program - this action is always requested via ajax. no message necessary
                            die();
                        }
                        // show/hide whole interface
                        if (isset($_GET['show_interface'])) {
                            $pref_array = array();
                            $pref_array['show_interface'] = intval($_GET['show_interface']);
                            $this->savePreferences($pref_array);
                            // kill the program - this action is always requested via ajax. no message necessary
                            die();
                        }
                        // active MQ tab
                        if (isset($_GET['mq_device_focus'])) {
                            $pref_array = array();
                            $pref_array['mq_device_focus'] = htmlentities($_GET['mq_device_focus']);
                            $this->savePreferences($pref_array);
                            // kill the program - this action is always requested via ajax. no message necessary
                            die();
                        }
                        // active property group
                        if (isset($_GET['pg_focus'])) {
                            $pref_array = array();
                            $pref_array['pg_focus'] = htmlentities($_GET['pg_focus']);
                            $this->savePreferences($pref_array);
                            // kill the program - this action is always requested via ajax. no message necessary
                            die();
                        }
                        // remember selector wizard tab
                        if (isset($_GET['adv_wizard_tab'])) {
                            $pref_array = array();
                            $pref_array['adv_wizard_tab'] = htmlentities($_GET['adv_wizard_tab']);
                            $this->savePreferences($pref_array);
                            // kill the program - this action is always requested via ajax. no message necessary
                            die();
                        }
                        // left menu being up or down
                        if (isset($_GET['left_menu_down'])) {
                            $pref_array = array();
                            $pref_array['left_menu_down'] = intval($_GET['left_menu_down']);
                            $this->savePreferences($pref_array);
                            // kill the program - this action is always requested via ajax. no message necessary
                            die();
                        }
                        // last viewed selector
                        if (isset($_GET['last_viewed_selector'])) {
                            $pref_array = array();
                            $pref_array['last_viewed_selector'] = htmlentities($_GET['last_viewed_selector']);
                            $this->savePreferences($pref_array);
                            // kill the program - this action is always requested via ajax. no message necessary
                            die();
                        }


                        // download pack
                        if (!empty($_GET['action']) and
                            $_GET['action'] == 'tvr_download_pack') {
                            if ( !empty($_GET['dir_name'])) {
                                // first of all, copy any images from the media library
                                $pack = $_GET['dir_name'];
                                $dir = $this->micro_root_dir . $pack;
                                $json_config_file =  $dir . '/config.json';
                                if ($library_images = $this->get_linked_library_images($json_config_file)){
                                    foreach($library_images as $key => $root_rel_path){
                                        $orig = rtrim(ABSPATH,"/"). $root_rel_path;
                                        $img_paths[] = $new = $dir . '/' . basename($root_rel_path);
                                        if (!copy($orig, $new)){
                                            $this->log(
                                                wp_kses(__('Library image not downloaded', 'tvr-microthemer'), array()), 'tvr-microthemer',
                                                '<p>' . sprintf(wp_kses(__('%s could not be copied to the zip download file', 'tvr-microthemer'), array()), $root_rel_path) . '</p>',
                                                'warning'
                                            );
                                            $download_status = 0;
                                        }
                                    }
                                }

                                // now zip the contents
                                if (
                                    $this->create_zip(
                                    $this->micro_root_dir,
                                    $pack,
                                    $this->thisplugindir.'zip-exports/')
                                    ){
                                    $download_status = 1;
                                }  else {
                                    $download_status = 0;
                                }
                            }
                            else {
                                $download_status = 0;
                            }
                            // delete any media library images temporarily copied to the directory
                            if ($library_images){
                                foreach ($img_paths as $key => $path){
                                    if (!unlink($path)){
                                        $this->log(
                                            wp_kses(__('Temporary image could not be deleted.', 'tvr-microthemer'), array()), 'tvr-microthemer',
                                            '<p>' . sprintf(wp_kses(__('%s was temporarily copied to your theme pack before download but could not be deleted after the download operation finished.', 'tvr-microthemer'), array()), $this->root_rel($root_rel_path) ) . '</p>',
                                            'warning'
                                        );
                                    }
                                }
                            }
                            echo '
                            <div id="microthemer-notice">'
                                . $this->display_log() . '
						        <span id="download-status" rel="'.$download_status.'"></span>
						    </div>';
                            die();
                        }

                        // delete pack
                        if (!empty($_GET['action']) and
                            $_GET['action'] == 'tvr_delete_micro_theme') {
                            if (!empty($_GET['dir_name']) and $this->tvr_delete_micro_theme($_GET['dir_name'])){
                                $delete_status = 1;
                            } else {
                                $delete_status = 0;
                            }
                            echo '
                            <div id="microthemer-notice">'
                                . $this->display_log() . '
						        <span id="delete-status" rel="'.$delete_status.'"></span>
						    </div>';
                            die();
                        }
                    }

					// if it's a save request
                    if( isset($_POST['action']) and $_POST['action'] == 'tvr_microthemer_ui_serialised') {
						$user_action = wp_kses(__('Save', 'tvr-microthemer'), array());
                        check_admin_referer('tvr_microthemer_ui_serialised');
                        $circumvent_max_input_vars = true;
                        if ($circumvent_max_input_vars){
                            $this->my_parse_str($_POST['tvr_serialized_data'], $this->serialised_post);
                        } else {
                            parse_str($_POST['tvr_serialized_data'], $this->serialised_post); // for debugging
                        }

                        $debug = false;
                        if ($debug){
                            echo '<pre>';
                            print_r($this->serialised_post);
                            echo '</pre>';
                        }


                        //echo ini_get('max_input_vars').'<pre>serialised:'. "\n";
						// save settings in DB
						if ( $this->saveUiOptions($this->serialised_post['tvr_mcth'])) {
                            $saveOk = wp_kses(__('Settings Saved', 'tvr-microthemer'), array());
                            // this should probably be added after the export operation works fine
                            if ($this->serialised_post['export_to_pack'] == 1) {
                                $saveOk.= wp_kses(__(' & Exported', 'tvr-microthemer'), array());
                            }
                            $this->log(
                                $saveOk,
                                '<p>' . wp_kses(__('The UI interface settings were successfully saved.', 'tvr-microthemer'), array()) . '</p>',
                                'notice'
                            );
						}
						else {
                            $this->log(
                                wp_kses(__('Settings failed to save', 'tvr-microthemer'), array()), 'tvr-microthemer',
                                '<p>' . wp_kses(__('Saving your setting to the database failed.', 'tvr-microthemer'), array()) . '</p>'
                            );
						}
						// check if settings need to be exported to a design pack
						if ($this->serialised_post['export_to_pack'] == 1) {
							$theme = htmlentities($this->serialised_post['export_pack_name']);
                            if ($this->serialised_post['new_pack'] == 1){
								$context = 'new';
                                $do_option_insert = true;
							}
							// function return sanitised theme name
							$theme = $this->update_json_file($theme, $context);
							// save new sanitised theme in span for updating select menu via jQuery
							if ($do_option_insert) {
								$new_select_option = $theme;
							}
							else {
								$new_select_option = '';
							}
							$user_action.= sprintf(wp_kses(__(' & Export to %s', 'tvr-microthemer'), array()), '<i>'. $this->readable_name($theme). '</i>');
						}
						// else its a standard save of custom settings
						else {
							$theme = 'customised';
							$user_action.= wp_kses(__(' (regular)', 'tvr-microthemer'), array());
						}
						// update active-styles.css
						$this->update_active_styles($theme);
						// update the revisions DB field
						if (!$this->updateRevisions($this->options, $user_action)) {
                            $this->log('','','error', 'revisions');
						}
						// if the user is approaching their max_input_vars limit, warn them
						$max_input_vars = ini_get('max_input_vars');
						if ( $max_input_vars ) {
							$num_input_vars = count($_POST, COUNT_RECURSIVE);
							// warn is 75% of max is bein used
							if ( $num_input_vars >= ($max_input_vars*.5) ) {
                                $this->log(
                                    wp_kses(__('Data limit near', 'tvr-microthemer'), array()), 'tvr-microthemer',
                                    '<p>' .
									wp_kses(
										printf( __( '<b>Warning:</b> you are approaching a data sending limit "max_input_vars" set by your server. Please save your settings and then reload the interface to solve this problem. Read more about this issue <a %s>here.</a>', 'tvr-microthemer'),
											'target="_blank" href="http://themeover.com/avoiding-save-errors-after-editing-lots-of-selectors-by-using-the-speed-up-button/"'),
										array(
											'a' => array( 'href' => array(), 'target' => array() ),
											'b' => array(),
										)
									)
									. '</p>',
                                    'warning'
                                );
							}
						}

                        // pass info for the program as a dev log

						// return the globalmessage and then kill the program - this action is always requested via ajax
						echo '<div id="microthemer-notice">' . $this->display_log() . '

						<div class="script-feedback">
                            <span id="sanit-export-name">'.$new_select_option.'</span>
                            <span id="google-url-to-refresh">'.$this->preferences['g_url'].'</span>
						</div>
						</div>';
						die();
					}

					// ajax - load selectors and/or selector options
					if ( isset($_GET['action']) and $_GET['action'] == 'tvr_microthemer_ui_load_styles') {
						check_admin_referer('tvr_microthemer_ui_load_styles');
						$section_name = strip_tags($_GET['tvr_load_section']);
						$css_selector = strip_tags($_GET['tvr_load_selector']);
                        $array = $this->options[$section_name][$css_selector];
                        echo '<div id="tmp-wrap">';
                        echo $this->all_option_groups_html($section_name, $css_selector, $array);
						echo '</div>';
						// kill the program - this action is always requested via ajax. no message necessary
						die();
					}

					// if it's an import request
					if ( isset($_POST['import_from_pack_name']) and !empty($_POST['import_from_pack_name']) ) {
						check_admin_referer('tvr_import_from_pack');
						$theme_name = sanitize_file_name(sanitize_title(htmlentities($_POST['import_from_pack_name'])));
						$json_file = $this->micro_root_dir . $theme_name . '/config.json';
						$context = $_POST['tvr_import_method'];
                        // import any background images that may need moving to the media library and update json
                        $this->import_pack_images_to_library($json_file, $theme_name);
						// load the json file
                        $this->load_json_file($json_file, $theme_name, $context);
                        // update the revisions DB field
                        $user_action = wp_kses( sprintf( __(' Import (%1$s) from %2$s',
                                'tvr-microthemer'), $context, '<i>'.
                            $this->readable_name($theme_name). '</i>' ), array('i' =>
                            array()) );

						if (!$this->updateRevisions($this->options, $user_action)) {
                            $this->log('','','error', 'revisions');
						}
                        // save last message in database so that it can be displayed on page reload (just once)
                        $this->cache_global_msg();
                        die();
					}

					// if it's a reset request
					elseif( isset($_GET['action']) and $_GET['action'] == 'tvr_ui_reset'){
						check_admin_referer('tvr_microthemer_ui_reset');
						if ($this->resetUiOptions()) {
							$this->update_active_styles('customised');
                            $this->log(
                                wp_kses(__('Folders were reset', 'tvr-microthemer'), array()),
                                '<p>' . wp_kses(__('The default empty folders have been reset.', 'tvr-microthemer'), array()) . '</p>',
                                'notice'
                            );
							// update the revisions DB field
							if (!$this->updateRevisions($this->options, 'Settings Reset')) {
                                $this->log(
                                    wp_kses(__('Revision failed to save', 'tvr-microthemer'), array()),
                                    '<p>' . wp_kses(__('The revisions table could not be updated.', 'tvr-microthemer'), array()) . '</p>',
                                    'notice'
                                );
							}
						}
                        // save last message in database so that it can be displayed on page reload (just once)
                        $this->cache_global_msg();
                        die();
					}

					// if it's a clear styles request
					elseif(isset($_GET['action']) and $_GET['action'] == 'tvr_clear_styles'){
						check_admin_referer('tvr_microthemer_clear_styles');
						if ($this->clearUiOptions()) {
							$this->update_active_styles('customised');
                            $this->log(
                                wp_kses(__('Styles were cleared', 'tvr-microthemer'), array()), 'tvr-microthemer',
                                '<p>' . wp_kses(__("All styles were cleared, but your folders and selectors remain fully intact.", "tvr-microthemer"), array()) . '</p>',
                                'notice'
                            );
							// update the revisions DB field
							if (!$this->updateRevisions($this->options, 'All Styles Cleared')) {
                                $this->log('', '', 'error', 'revisions');
							}
						}
                        // save last message in database so that it can be displayed on page reload (just once)
                        $this->cache_global_msg();
                        die();
					}

					// if it's an email error report request
					elseif(isset($_GET['action']) and $_GET['action'] == 'tvr_error_email'){
						check_admin_referer('tvr_microthemer_ui_load_styles');
						$body = "*** MICROTHEMER ERROR REPORT | ".date('d/m/Y h:i:s a', $this->time)." *** \n\n";
						$body .= "PHP ERROR \n" . stripslashes($_POST['tvr_php_error']) .  "\n\n";
						$body .= "BROWSER INFO \n" . stripslashes($_POST['tvr_browser_info']) .  "\n\n";
						$body .= "SERIALISED POSTED DATA \n" . stripslashes($_POST['tvr_serialised_data']) . "\n\n";
						// An error can occur EITHER when saving to DB OR creating the active-styles.css
						// The php error line number will reveal this. If the latter is true, the DB data contains the posted data too (FYI)
						$body .= "SERIALISED DATA IN DB \n" . serialize($this->options). "\n\n";
						// write file to error-reports dir
						$file_path = 'error-reports/error-'.date('Y-m-d').'.txt';
						$error_file = $this->thisplugindir . $file_path;
						$write_file = fopen($error_file, 'w');
						fwrite($write_file, $body);
						fclose($write_file);
						// Determine from email address. Try to use validated customer email. Don't contact if not Microthemer customer.
						if ( !empty($this->preferences['buyer_email']) ) {
							$from_email = $this->preferences['buyer_email'];
							$body .= "MICROTHEMER CUSTOMER EMAIL \n" . $from_email;
						}
						else {
							$from_email = get_option('admin_email');
						}
						// Try to send email (won't work on localhost)
						$subject = 'Microthemer Error Report | ' . date('d/m/Y', $this->time);
						$to = 'support@themeover.com';
						$from = "Microthemer User <$from_email>";
						$headers = "From: $from";
						if(@mail($to,$subject,$body,$headers)) {
                            $this->log(
                                wp_kses(__('Email successfully sent', 'tvr-microthemer'), array()),
                                '<p>' . wp_kses(__('Your error report was successfully emailed to Themeover. Thanks, this really does help.', 'tvr-microthemer'), array()) . '</p>',
                                'notice'
                            );
						}
						else {
							$error_url = $this->thispluginurl . $file_path;
                            $this->log(
                                wp_kses(__('Report email failed', 'tvr-microthemer'), array()),
                                '<p>' . wp_kses(__('Your error report email failed to send (are you on localhost?)', 'tvr-microthemer'), array()) . '</p>
								<p>' .
								wp_kses(
									sprintf(
										__('Please email <a %1$s>this report</a> to %2$s', 'tvr-microthemer'),
										'target="_blank" href="' .$error_url . '"',
										'<a href="mailto:support@themeover.com">support@themeover.com</a>'
									),
									array( 'a' => array( 'href' => array(), 'target' => array() ) )
								)
								. '</p>'
                            );
						}
                        echo '
                        <div id="microthemer-notice">'. $this->display_log() . '</div>';
                        die();
					}

					// if it's a restore revision request
					if(isset($_GET['action']) and $_GET['action'] == 'restore_rev'){
						check_admin_referer('tvr_restore_revision');
						$rev_key = $_GET['tvr_rev'];
						if ($this->restoreRevision($rev_key)) {
                            $this->log(
                                wp_kses(__('Settings successfully restored', 'tvr-microthemer'), array()),
                                '<p>' . wp_kses(__('Your settings were successfully restored from a previous save.', 'tvr-microthemer'), array()) . '</p>',
                                'notice'
                            );
							$this->update_active_styles('customised');
							// update the revisions DB field
							if (!$this->updateRevisions($this->options, __('Previous Settings Restored', 'tvr-microthemer'))) {
                                $this->log('','','error', 'revisions');
							}
						}
						else {
                            $this->log(
                                wp_kses(__('Settings restore failed', 'tvr-microthemer'), array()),
                                '<p>' . wp_kses(__('Data could not be restored from a previous save.', 'tvr-microthemer'), array()) . '</p>'
                            );
						}
                        // save last message in database so that it can be displayed on page reload (just once)
                        $this->cache_global_msg();
                        die();
					}

					// if it's a get revision ajax request
					elseif(isset($_GET['action']) and $_GET['action'] == 'get_revisions'){
						check_admin_referer('tvr_get_revisions'); // general ui nonce
						echo '<div id="tmp-wrap">' . $this->getRevisions() . '</div>'; // outputs table
						die();
					}


                    /* PREFERENCES FUNCTIONS MOVED TO MAIN UI */

                    // update the MQs
                    if (isset($_POST['tvr_update_media_queries_submit'])){
                        check_admin_referer('tvr_media_queries_form');
                        // get the initial scale and default width for the "All Devices" tab
                        $pref_array['initial_scale'] = $_POST['tvr_preferences']['initial_scale'];
                        $pref_array['all_devices_default_width'] = $_POST['tvr_preferences']['all_devices_default_width'];
                        // reset default media queries if all empty
                        $action = '';
                        if (empty($_POST['tvr_preferences']['m_queries'])) {
                            $pref_array['m_queries'] = $this->default_m_queries;
                            $action = 'reset';
                        } else {
                            $pref_array['m_queries'] = $_POST['tvr_preferences']['m_queries'];
                            // check the media query min/max values
                            foreach($pref_array['m_queries'] as $key => $mq_array) {
                                $m_conditions = array('min', 'max');
                                foreach ($m_conditions as $condition){
                                    $matches = $this->get_screen_size($mq_array['query'], $condition);
                                    $pref_array['m_queries'][$key][$condition] = 0;
                                    if ($matches){
                                        $pref_array['m_queries'][$key][$condition] = intval($matches[1]);
                                        $pref_array['m_queries'][$key][$condition.'_unit'] = $matches[2];
                                    }
                                }
                            }
                            $action = 'update';
                        }
                        // are we merging/overwriting with a new media query set
                        if (!empty($_POST['tvr_preferences']['load_mq_set'])){
                            print_r($this->mq_sets);
                            $action = 'load_set';
                            $new_set = str_replace(' ', '_', $_POST['tvr_preferences']['load_mq_set']);
                            $new_set = str_replace('_(default)', '', $new_set);
                            $new_mq_set = $this->mq_sets[$new_set];
                            if (!empty($_POST['tvr_preferences']['overwrite_existing_mqs'])){
                                $pref_array['m_queries'] = $new_mq_set;
                                $load_action = 'replaced';
                            } else {
                                $pref_array['m_queries'] = array_merge($pref_array['m_queries'], $new_mq_set);
                                $load_action = 'was merged with';
                            }

                        }
                        // save and preset message
                        if ($this->savePreferences($pref_array)) {
                            switch ($action) {
                                case 'reset':
                                    $this->log(
                                        wp_kses(__('Media queries were reset', 'tvr-microthemer'), array()),
                                        '<p>' . wp_kses(__('The default media queries were successfully reset.', 'tvr-microthemer'), array()) . '</p>',
                                        'notice'
                                    );
                                    break;
                                case 'update':
                                    $this->log(
                                        wp_kses(__('Media queries were updated', 'tvr-microthemer'), array()),
                                        '<p>' . wp_kses(__('Your media queries were successfully updated.', 'tvr-microthemer'), array()) . '</p>',
                                        'notice'
                                    );
                                    break;
                                case 'load_set':
                                    $this->log(
                                        wp_kses(__('Media query set loaded', 'tvr-microthemer'), array()),
                                        '<p>' . sprintf( wp_kses(__('A new media query set %1$s your existing media queries: %2$s', 'tvr-microthemer'), array()), $load_action, htmlentities($_POST['tvr_preferences']['load_mq_set']) ) . '</p>',
                                        'notice'
                                    );
                                    break;
                            }

                        }
                    }

                    // validate email
                    if (isset($_POST['tvr_validate_submit'])) {
                        $params = 'email='.$_POST['tvr_preferences']['buyer_email'].'&domain='.$this->home_url;
                        $validation = wp_remote_fopen('http://themeover.com/wp-content/tvr-auto-update/validate.php?'.$params);
                        if ($validation) {
                            $_POST['tvr_preferences']['buyer_validated'] = 1;
                            $this->trial = 0;
                            if (!$this->preferences['buyer_validated']) { // not already validated
                                $this->log(
                                    wp_kses(__('Full program unlocked!', 'tvr-microthemer'), array()),
                                    '<p>' . wp_kses(__('Your email address has been successfully validated. Microthemer\'s full program features have been unlocked!', 'tvr-microthemer'), array() ) . '</p>',
                                    'notice'
                                );
                            } else {
                                $this->log(
                                    wp_kses(__('Already validated', 'tvr-microthemer'), array()),
                                    '<p>' . wp_kses(__('Your email address has already been validated. The full program is currently active.', 'tvr-microthemer'), array()) . '</p>',
                                    'notice'
                                );
                            }
                        }
                        else {
                            //=chris please do checks on why validation failed here and report to user
                            $_POST['tvr_preferences']['buyer_validated'] = 0;
                            $this->trial = true;
                            $this->log(
                                wp_kses(__('Validation failed', 'tvr-microthemer'), array()),
                                '<p>' . wp_kses(__('Your email address could not be validated. Please try the following:', 'tvr-microthemer'), array()) . '</p>
								<ul>' .
									'<li>' . wp_kses(
										sprintf(
											__('Make sure you are entering your <b>PayPal email address</b> (which may be different from your themeover.com member email address you registered with, or the email address you provided when downloading the free trial). The correct email address to unlock the program will be shown on <a %s>My Downloads</a>', 'tvr-microthemer' ),
											'target="_blank" href="http://rebrand.themeover.com/login/"' ),
										array( 'a' => array( 'href' => array(), 'target' => array() ), 'b' => array() )
									) . '</li>' .
									'<li>' . wp_kses(
										sprintf( __('If you purchased Microthemer from <b>CodeCanyon</b>, please send us a "Validate my email" message via the contact form on the right hand side of <a %s>this page</a> (you will need to ensure that you are logged in to CodeCanyon).', 'tvr-microthemer'), 'target="_blank" href="http://codecanyon.net/user/themeover"' ),
										array( 'a' => array( 'href' => array(), 'target' => array() ), 'b' => array() )
									) . '</li>' .
									'<li>' . wp_kses(
										sprintf( __('The connection to Themeover\'s server may have failed due to an intermittent network error. <span %s>Resubmitting your email one more time</span> may do the trick.', 'tvr-microthemer'), 'class="link show-dialog" rel="unlock-microthemer"' ),
										array( 'span' => array() ) )
									. '</li>' .
									'<li>' . wp_kses(__('Try temporarily <b>disabling any plugins</b> that may be blocking Microthemer\'s connection to the valid users database on themeover.com. You can re-enable them after you unlock Microthemer. WP Security plugins sometimes block Microthemer.', 'tvr-microthemer'), array( 'b' => array() )) . '</li>' .
									'<li>' . wp_kses(__('Security settings on your server may block all outgoing PHP connections to domains not on a trusted whitelist (e.g. sites that are not wordpress.org). Ask your web host about unblocking themeover.com - even if they just do it temporarily to allow you to activate the plugin.', 'tvr-microthemer'), array()) . '</li>'
                                . '</ul>
								<p>' .
								wp_kses(
									sprintf('If none of the above solve your problem please send WP login details for the site you\'re working on using <a %1$s>this secure contact form</a>. Please remember to include the URL to your website along with the username and password. If you\'d prefer not to provide login details please send us an email via the <a %1$s>contact form</a> to discuss alternative measures.',
										'target="_blank" href="https://themeover.com/support/contact/"'),
									array( 'a' => array( 'href' => array(), 'target' => array() ) )
								)
								. '</p>'
							);
                        }
                        $pref_array = $_POST['tvr_preferences'];
                        if (!$this->savePreferences($pref_array)) {
                            $this->log(
                                wp_kses(__('Unlock status not saved', 'tvr-microthemer'), array()),
                                '<p>' . wp_kses(__('Your validation status could not be saved. The program may need to be unlocked again.', 'tvr-microthemer'), array()) . '</p>'
                            );
                        }
                    }

                    // reset default preferences
                    if (isset($_POST['tvr_preferences_reset'])) {
                        check_admin_referer('tvr_preferences_reset');
                        $pref_array = $this->default_preferences;
                        if ($this->savePreferences($pref_array)) {
                            $this->log(
                                wp_kses(__('Preferences were reset', 'tvr-microthemer'), array()),
                                '<p>' . wp_kses(__('The default program preferences were reset.', 'tvr-microthemer'), array()) . '</p>',
                                'notice'
                            );
                        }
                    }

					// include user interface (microthemer only)
					if (TVR_MICRO_VARIANT == 'themer') {
						include $this->thisplugindir . 'includes/tvr-microthemer-ui.php';
					}

				}
			}



			// Manage Micro Themes page
			function manage_micro_themes_page() {
				// only run code if it's the manage themes page
				if ( $_GET['page'] == $this->microthemespage ) {

                    // handle zip upload
                    $this->process_uploaded_zip();

					// notify that design pack was successfully deleted (operation done via ajax on single pack page)
                    if (!empty($_GET['action']) and $_GET['action'] == 'tvr_delete_ok') {
                        check_admin_referer('tvr_delete_ok');
                        $this->log(
                            wp_kses(__('Design pack deleted', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('The design pack was successfully deleted.', 'tvr-microthemer'), array()) . '</p>',
                            'notice'
                        );
                    }

					// create new micro theme
					if (isset($_POST['tvr_create_micro_submit'])) {
						check_admin_referer('tvr_create_micro_submit');
						$micro_name = esc_attr( $_POST['micro_name']);
						if ( !empty($micro_name) ) {
							$this->create_micro_theme($micro_name, 'create', '');
						}
						else {
                            $this->log(
                                wp_kses(__('Please specify a name', 'tvr-microthemer'), array()),
                                '<p>' . wp_kses(__('You didn\'t enter anything in the "Name" field. Please try again.', 'tvr-microthemer'), array()) . '</p>'
                            );


						}
					}

					// handle edit micro selection
					if (isset($_POST['tvr_edit_micro_submit'])) {
						check_admin_referer('tvr_edit_micro_submit');
						$pref_array = array();
						$pref_array['theme_in_focus'] = $_POST['preferences']['theme_in_focus'];
						$this->savePreferences($pref_array);
					}

					// activate theme
					if (
                        !empty($_GET['action']) and
                        $_GET['action'] == 'tvr_activate_micro_theme') {
						check_admin_referer('tvr_activate_micro_theme');
						$theme_name = $this->preferences['theme_in_focus'];
						$json_file = $this->micro_root_dir . $theme_name . '/config.json';
						$this->load_json_file($json_file, $theme_name);
						// update the revisions DB field
						$user_action = sprintf( wp_kses(__('%s Activated', 'tvr-microthemer'), array()),  '<i>' . $this->readable_name($theme_name) . '</i>' );
						if (!$this->updateRevisions($this->options, $user_action)) {
                            $this->log('', '', 'error', 'revisions');
						}
					}
					// deactivate theme
					if (
                        !empty($_GET['action']) and
                        $_GET['action'] == 'tvr_deactivate_micro_theme') {
						check_admin_referer('tvr_deactivate_micro_theme');
						$pref_array = array();
						$pref_array['active_theme'] = '';
						if ($this->savePreferences($pref_array)) {
                            $this->log(
                                wp_kses(__('Item deactivated', 'tvr-microthemer'), array()),
								'<p>' .
								sprintf(
									wp_kses(__('%s was deactivated.', 'tvr-microthemer'), array()),
									'<i>'.$this->readable_name($this->preferences['theme_in_focus']).'</i>' )
								. '</p>',
                                'notice'
                            );
						}
					}

					// include manage micro interface (both loader and themer plugins need this)
					include $this->thisplugindir . 'includes/tvr-manage-micro-themes.php';
				}
			}

            // Manage single page
            function manage_single_page() {
                // only run code on preferences page
                if( $_GET['page'] == $this->managesinglepage ) {

                    // handle zip upload
                    $this->process_uploaded_zip();

                    // update meta.txt
                    if (isset($_POST['tvr_edit_meta_submit'])) {
                        check_admin_referer('tvr_edit_meta_submit');
                        $this->update_meta_file($this->micro_root_dir . $this->preferences['theme_in_focus'] . '/meta.txt');
                    }

                    // update readme.txt
                    if (isset($_POST['tvr_edit_readme_submit'])) {
                        check_admin_referer('tvr_edit_readme_submit');
                        $this->update_readme_file($this->micro_root_dir . $this->preferences['theme_in_focus'] . '/readme.txt');
                    }

                    // upload a file
                    if (isset($_POST['tvr_upload_file_submit'])) {
                        check_admin_referer('tvr_upload_file_submit');
                        $this->handle_file_upload();
                    }

                    // delete a file
                    if (
                        !empty($_GET['action']) and
                        $_GET['action'] == 'tvr_delete_micro_file') {
                        check_admin_referer('tvr_delete_micro_file');
                        $root_rel_path = $this->root_rel($_GET['file'], false, true);
                        $delete_ok = true;
                        // remove the file from the media library
                        if ($_GET['location'] == 'library'){
                            global $wpdb;
                            $img_path = $_GET['file'];
                            // We need to get the images meta ID.
                            $query = "SELECT ID FROM wp_posts where guid = '" . esc_url($img_path)
                                . "' AND post_type = 'attachment'";
                            $results = $wpdb->get_results($query);
                            // And delete it
                            foreach ( $results as $row ) {
                                //delete the image and also delete the attachment from the Media Library.
                                if ( false === wp_delete_attachment( $row->ID )) {
                                    $delete_ok = false;
                                }
                            }
                        }
                        // regular delete of pack file
                        else {
                            if ( !unlink(ABSPATH . $root_rel_path) ) {
                                $delete_ok = false;
                            } else {
                                // remove from file_structure array
                                $file = basename($root_rel_path);
                                if (!$this->is_screenshot($file)){
                                    $key = $file;
                                } else {
                                    $key = 'screenshot';
                                    // delete the screenshot-small too
                                    $thumb = str_replace('screenshot', 'screenshot-small', $root_rel_path);
                                    if (is_file(ABSPATH . $thumb)){
                                        unlink(ABSPATH . $thumb);
                                        unset($this->file_structure[$this->preferences['theme_in_focus']][basename($thumb)]);
                                    }
                                }
                                unset($this->file_structure[$this->preferences['theme_in_focus']][$key]);
                            }
                        }
                        if ($delete_ok){
                            $this->log(
                                wp_kses(__('File deleted', 'tvr-microthemer'), array()),
                                '<p>' . sprintf( wp_kses(__('%s was successfully deleted.', 'tvr-microthemer'), array()), htmlentities($root_rel_path) ) . '</p>',
                                'notice'
                            );
                            // update paths in json file
                            $json_config_file = $this->micro_root_dir . $this->preferences['theme_in_focus'] . '/config.json';
                            $this->replace_json_paths($json_config_file, array($root_rel_path => ''));
                        } else {
                            $this->log(
                                wp_kses(__('File delete failed', 'tvr-microthemer'), array()),
                                '<p>' . sprintf( wp_kses(__('%s was not deleted.', 'tvr-microthemer'), array()), htmlentities($root_rel_path) ) . '</p>'
                            );
                        }
                    }


                    // include preferences interface (only microthemer)
                    if (TVR_MICRO_VARIANT == 'themer') {
                        include $this->thisplugindir . 'includes/tvr-manage-single.php';
                    }

                }
            }

			// Preferences page
			function microthemer_preferences_page() {
				// only run code on preferences page
				if( $_GET['page'] == $this->preferencespage ) {



					// include preferences interface (only microthemer)
					if (TVR_MICRO_VARIANT == 'themer') {
                        // this is a separate include because it needs to have separate page for changing gzip
                        $page_context = $this->preferencespage;
                        echo '
                        <div id="tvr" class="wrap tvr-wrap">
                            <div id="pref-standalone">
                            <div id="full-logs">
                                '.$this->display_log().'
                            </div>';
                            include $this->thisplugindir . 'includes/tvr-microthemer-preferences.php';
                            echo '
                            </div>
                        </div>';
					}
				}
			}

			/* add run if admin page condition...? */

			/***
			Generic Functions
			***/

			// get min/max media query screen size
			function get_screen_size($q, $minmax) {
				$pattern = "/$minmax-width:\s*([0-9\.]+)\s*(px|em|rem)/";
				if (preg_match($pattern, $q, $matches)) {
                    //echo print_r($matches);
					return $matches;
				} else {
					return 0;
				}
			}

			// show plugin menu
			function plugin_menu() {
				?>
                <div id='plugin-menu' class="fixed-subsection">
                <h3>Plugin Menu</h3>
                <div class='menu-option-wrap'>
				<a id="tvr-item-1" href='admin.php?page=<?php echo $this->microthemeruipage;?>' title="<?php _e('Go to Microthemer UI Page', 'tvr-microthemer') ?>">UI</a>
				<a id="tvr-item-2" href='admin.php?page=<?php echo $this->microthemespage;?>' title="<?php _e('Go to Manage Micro Themes Page', 'tvr-microthemer') ?>">Manage</a>
				<a id="tvr-item-3" href='admin.php?page=<?php echo $this->preferencespage;?>' title="<?php _e('Go to Microthemer Preferences Page', 'tvr-microthemer') ?>">Options</a>
            	</div>
                </div>
                <?php
			}

			// show need help videos
			function need_help_notice() {
				if ($this->preferences['need_help'] == '1' and TVR_MICRO_VARIANT != 'loader') {
					?>
					<p class='need-help'><b><?php printf(wp_kses(__('Need Help?', 'tvr-microthemer'), array())); ?></b>
					<?php printf(
						wp_kses(__('Browse Our <span %1$s>Video Guides</span> and <span %2$s>Tutorials</span> or <span %3$s>Search Our Forum</span>', 'tvr-microthemer'),
							array( 'span' => array() )),
						'class="help-trigger" rel="' . $this->thispluginurl.'includes/help-videos.php',
						'class="help-trigger" rel="' . $this->thispluginurl.'includes/tutorials.php',
						'class="help-trigger" rel="' . $this->thispluginurl.'includes/search-forum.php'
					); ?></p>
				<?php
				}
			}

            /* Simple function to check for the browser
            For checking chrome faster notice and FF bug if $.browser is deprecated soon
            http://php.net/manual/en/function.get-browser.php */
            function check_browser(){
                $u_agent = $_SERVER['HTTP_USER_AGENT'];
                $ub = 'unknown-browser';
                if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
                    $ub = "MSIE";
                }
                elseif(preg_match('/Firefox/i',$u_agent)){
                    $ub = "Firefox";
                }
                elseif(preg_match('/Chrome/i',$u_agent)){
                    $ub = "Chrome";
                }
                elseif(preg_match('/Safari/i',$u_agent)){
                    $ub = "Safari";
                }
                elseif(preg_match('/Opera/i',$u_agent)){
                    $ub = "Opera";
                }
                elseif(preg_match('/Netscape/i',$u_agent)){
                    $ub = "Netscape";
                }
                return $ub;
            }

			// ie notice
			function ie_notice() {
				// display ie message unless disabled
                //global $is_IE;
				if ($this->preferences['ie_notice'] == '1' and $this->check_browser() != 'Chrome') {
                    $this->log(
                        wp_kses(__('Chrome Is Faster', 'tvr-microthemer'), array()),
						'<p>' .
						sprintf(
							wp_kses(__('We\'ve noticed that Microthemer runs considerably faster in Chrome that other browsers. Actions like switching tabs, property groups, and accessing preferences are instant in Chrome but can incur a half second delay on other browsers. Speed improvements will be a major focus in our next phase of development. But for now, you can avoid these issues simply by using Microthemer with %1$s.', 'tvr-microthemer'), array()),
							'<a target="_blank" href="http://www.google.com/intl/' . _x('en-US', 'Chrome URL slug: https://www.google.com/intl/en-US/chrome/browser/welcome.html', 'tvr-microthemer') . '/chrome/browser/welcome.html">Google Chrome</a>'
						)
						. '</p><p>' .
						wp_kses(__('<b>Note</b>: Web browsers do not conflict with each other, you can install as many as you want on your computer at any one time. But if you love your current browser you can turn this message off on the preferences page.', 'tvr-microthemer'), array( 'b' => array() ))
					. '</p>',
                        'warning'
                    );
				}
			}

			// tell them to get validated
			function validate_reminder() {
				if (!$this->preferences['buyer_validated'] and TVR_MICRO_VARIANT == 'themer') {
					?>
					<div id='validate-reminder' class="error">
					<p><b><?php printf(wp_kses(__("IMPORTANT - Free Trial Mode is Active", "tvr-microthemer"), array() ) ); ?></b><br /> <br />
						<?php wp_kses(
							printf( __('Please <a %s>validate your purchase to unlock the full program</a>.', 'tvr-microthemer'), 
								'href="admin.php?page=tvr-microthemer-preferences.php#validate"' ),
								array( 'a' => array( 'href' => array() ) ) ); ?>
						<br />
						<?php printf(
							wp_kses(__('The Free Trial limits you to editing or creating 3 Sections and 9 Selectors (3 per Section).', 'tvr-microthemer'), array())
						); ?></p>
					<p><?php wp_kses(
						printf( __('Purchase a <a %1$s>Standard</a> ($45) or <a %1$s>Developer</a> ($90) License Now!', 'tvr-microthemer'), 
							'target="_blank" href="http://themeover.com/microthemer/"'),
							array( 'a' => array( 'href' => array(), 'target' => array() ) ) ); ?></p>
					<p><?php wp_kses(
						printf( __('<b>This Plugin is Supported!</b> Themeover provides the <a %s>best forum support</a> you\'ll get any where (and it\'s free of course)',
								'tvr-microthemer'),
							'target="_blank" href="http://themeover.com/forum/"' ),
							array( 'a' => array( 'href' => array(), 'target' => array() ), 'b' => array() ) ); ?></p>
                    </div>
				<?php
				}
			}

			// show server info
			function server_info() {
				global $wpdb;
				// get MySQL version
				$sql_version = $wpdb->get_var("SELECT VERSION() AS version");
				// evaluate PHP safe mode
				if(ini_get('safe_mode')) {
					$safe_mode = 'On';
				}
				else {
					$safe_mode = 'Off';
				}
				?>
                &nbsp;Operating System:<br />&nbsp;<b><?php echo PHP_OS; ?> (<?php echo (PHP_INT_SIZE * 8) ?> Bit)</b><br />

                &nbsp;MySQL Version:<br />&nbsp;<b><?php echo $sql_version; ?></b><br />
                &nbsp;PHP Version:<br />&nbsp;<b><?php echo PHP_VERSION; ?></b><br />
                &nbsp;PHP Safe Mode:<br />&nbsp;<b><?php echo $safe_mode; ?></b><br />
                <?php
			}

            // get all-devs and the MQS into a single simple array
            function combined_devices(){
                $comb_devs['all-devices'] = array(
                    'label' => wp_kses(__('All Devices', 'tvr-microthemer'), array()),
                    'query' => __('General CSS that will apply to all devices', 'tvr-microthemer'),
                    'min' => 0,
                    'max' => 0
                );
                foreach ($this->preferences['m_queries'] as $key => $m_query) {
                    $comb_devs[$key] = $m_query;
                }
                return $comb_devs;
            }

			// get micro-theme dir file structure
			function dir_loop($dir_name) {
                if (empty($this->file_structure)) {
                    $this->file_structure = array();
                }
				// check for micro-themes folder, create if doesn't already exist
				if ( !is_dir($dir_name) ) {
					if ( !wp_mkdir_p($dir_name) ) {
                        $this->log(
                            wp_kses(__('/micro-themes folder error', 'tvr-microthemer'), array()),
							'<p>' .
							sprintf(
								wp_kses(__('WordPress was not able to create the %1$s directory. ', 'tvr-microthemer'), array()),
								$this->root_rel($dir_name)
							) . $this->permissionshelp . '</p>'
                        );
                        return false;
					}
				}
				if ($handle = opendir($dir_name)) {
					$count = 0;
					 /* This is the correct way to loop over the directory. */
					 while (false !== ($file = readdir($handle))) {
						if ($file != '.' and $file != '..') {
							$file = htmlentities($file); // just in case
							if ($this->is_acceptable($file) or !preg_match('/\./',$file)) {
								 // if it's a directory
								 if (!preg_match('/\./',$file) ) {
									  $this->file_structure[$file] = '';
									  $next_dir = $dir_name . $file . '/';
									  // loop through the contents of the micro theme
									  $this->dir_loop($next_dir);
								 }
								 // it's a normal file
								 else {
									  $just_dir = str_replace($this->micro_root_dir, '', $dir_name);
									  $just_dir = str_replace('/', '', $just_dir);
                                      if ($this->is_screenshot($file)){
                                          $this->file_structure[$just_dir]['screenshot'] = $file;
                                      } else {
                                          $this->file_structure[$just_dir][$file] = $file;
                                      }

									  ++$count;
								  }
							}
						}
				 	}
					closedir($handle);
				}
				if (is_array($this->file_structure)) {
					ksort($this->file_structure);
				}
				return $this->file_structure;
			}

			// display abs file path relative to the root dir (for notifications)
			function root_rel($path, $markup = true, $url = false) {
				if ($markup == true) {
					$rel_path = '<b><i>/' . str_replace(ABSPATH, '', $path) . '</i></b>';
				}
				else {
					$rel_path = str_replace(ABSPATH, '', $path);
				}
                // root relative url (works on mixed ssl sites and if WP is in a subfolder of the doc root - getenv())
                if ($url){
                    $rel_path = substr(getenv("SCRIPT_NAME"), 0, -19) . str_replace($this->site_url, '', $path);
                }
				return $rel_path;
			}

			// get extension
			function get_extension($file) {
				$ext = strtolower(substr($file, strrpos($file, '.') + 1));
				return $ext;
			}

			// make server folder readable
			function readable_name($name) {
				$readable_name = str_replace('_', ' ', $name);
				$readable_name = ucwords(str_replace('-', ' ', $readable_name));
				return $readable_name;
			}

			// check if the file is an image
			function is_image($file) {
				$ext = $this->get_extension($file);
				if ($ext == 'jpg' or
				$ext == 'jpeg' or
				$ext == 'png' or
				$ext == 'gif'
				) {
					return true;
				}
				else {
					return false;
				}
			}

			// check if it's a screenshot image
			function is_screenshot($file) {
				if(strpos($file, 'screenshot.', 0) !== false) {
					return true;
				}
				else {
					return false;
				}
			}

			// check a multi-dimentional array for a value
			function in_2dim_array($elem, $array, $target_key){
				foreach ($array as $current_mq_key => $val) {
				   if ($val[$target_key] == $elem) {
					   return $current_mq_key;
				   }
			   }
			   return false;
    		}

			//check if the file is acceptable
			function is_acceptable($file) {
				$ext = $this->get_extension($file);
				if ($ext == 'jpg' or
				$ext == 'jpeg' or
				$ext == 'png' or
				$ext == 'gif' or
				$ext == 'txt' or
				$ext == 'json' or
				$ext == 'psd' or
				$ext == 'ai'
				) {
					return true;
				}
				else {
					return false;
				}
			}


			// get list of acceptable file types
			function get_acceptable() {
				$acceptable = array (
					'jpg',
					'jpeg',
					'png',
					'gif',
					'txt',
					'json',
					'psd',
					'ai');
					return	$acceptable;
			}

			// rename dir if dir with same name exists in same location
			function rename_if_required($dir_path, $name) {
				if ( is_dir($dir_path . $name ) ) {
					$suffix = 1;
					do {
						$alt_name = substr ($name, 0, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
						$dir_check = is_dir($dir_path . $alt_name);
						$suffix++;
					} while ( $dir_check );
					return $alt_name;
				}
				else {
					return false;
				}
			}

			// rename the to-be-merged section
			function get_alt_section_name($section_name, $orig_settings, $new_settings) {
				$suffix = 2;
				do {
					$alt_name = substr ($section_name, 0, 200 - ( strlen( $suffix ) + 1 ) ) . "_$suffix";
					$context = 'alt-check';
					$conflict = $this->is_name_conflict($alt_name, $orig_settings, $new_settings, $context);
					$suffix++;
				} while ( $conflict );
				return $alt_name;
			}

			// check if the section name exists in the orig_settings or the new_settings (possible after name modification)
			function is_name_conflict($alt_name, $orig_settings, $new_settings, $context='') {
				if ( ( isset($orig_settings[$alt_name]) // conflicts with orig settings or
				or ($context == 'alt-check' and isset($new_settings[$alt_name]) )) // conflicts with new settings (and is an alt name)
				and $alt_name != 'non_section' // and is a section
				) {
					return true; // conflict
				}
				else {
					return false; // no name conflict
				}
			}

			/***
			Microthemer UI Functions
			***/

            // ui dialog html (start)
            function start_dialog($id, $heading, $class = '', $tabs = array() ) {
                if ($id != 'manage-design-packs' and $id != 'program-docs'){
                    $scrollable = 'scrollable-area';
                } else {
                    $scrollable = '';
                }
                if ( !empty( $tabs ) ) {
                    $class.= ' has-mt-tabs';
                }
                $html = '<div id="'.$id.'-dialog" class="tvr-dialog '.$class.' hidden">
                <div class="dialog-main">
                    <div class="dialog-inner">
                        <div class="heading dialog-header">
                            <span class="dialog-icon"></span>
                            <span class="text">'.$heading.'</span>
                            <span class="tvr-icon close-icon close-dialog"></span>
						</div>';

				// If there are any tabs, the content is preceded by a tab menu
				if ( !empty( $tabs ) ) {
					$html.='
                    <div class="dialog-tabs query-tabs">
                        <input class="dummy-for-now" type="hidden"
                       name="tvr_mcth[non_section][dummy]"
                       value="" />';
                    // maybe add functionality to remember pref tab at a later date.
					for ($i = 0; $i < count($tabs); $i++) {
						$html .= '
                        <span class="' . ($i == 0 ? 'active' : '' )
                        . ' mt-tab dialog-tab dialog-tab-'.$i.'" rel="'.''.$i.'">
                            ' . $tabs[$i] . '
                        </span>';
					}
					$html .= '
					</div>';
				}
				$html .= '<div class="dialog-content '.$scrollable.'">';
                return $html;
            }

            function dialog_button($button_text, $type, $class){
                if ($type == 'span'){
                    $button = '<span class="tvr-button dialog-button '.$class.'">'.$button_text.'</span>';
                } else {
                    $button = '<input name="tvr_'.strtolower(str_replace(' ', '_', $button_text)).'_submit"
                   type="submit" value="'.$button_text.'"
                   class="tvr-button dialog-button" />';
                }

                return $button;
            }

            // ui dialog html (end)
            function end_dialog($button_text, $type = 'span', $class = '') {
                $button = $this->dialog_button($button_text, $type, $class);
                $html = '

                            </div>
                            <div class="dialog-footer">
                            '.$this->display_left_menu_icons(). $button. '
                            </div>
                        </div>
                    </div>
                </div>';
                return $html;
            }

            // render ajax loaders for dynamic insertion
            function hidden_ajax_loaders(){
                ?>
                <div id="a-loaders">
                    <img id="loading-gif-template" class="ajax-loader small" src="<?php echo $this->thispluginurl; ?>/images/ajax-loader-green.gif" />
                    <img id="loading-gif-template-wbg" class="ajax-loader small" src="<?php echo $this->thispluginurl; ?>/images/ajax-loader-wbg.gif" />
                    <img id="loading-gif-template-mgbg" class="ajax-loader small" src="<?php echo $this->thispluginurl; ?>/images/ajax-loader-mgbg.gif" />
                </div>
                <?php
            }

            // output header
            function manage_packs_header($page){
                ?>
                <ul class="pack-manage-options">
                    <li class="upload">
                        <form name='upload_micro_form' id="upload-micro-form" method="post" enctype="multipart/form-data"
                              action="<?php echo 'admin.php?page='. $page;?>" >
                            <?php wp_nonce_field('tvr_upload_micro_submit'); ?>
                            <input id="upload_pack_input" type="file" name="upload_micro"  />
                            <input class="tvr-button upload-pack" type="submit" name="tvr_upload_micro_submit"
								value="+ Upload New" title="<?php _e('Upload a new design pack', 'tvr-microthemer'); ?>" />
                        </form>
                    </li>
                    <!--<li>
                        <a class="tvr-button" target="_blank" title="Submit one of your design packs
                for sale/downlaod on themeover.com"
                           href="https://themeover.com/sell-micro-themes/submit-micro-theme/">
                            Submit To Marketplace
                        </a>
                    </li>
                    <li>
                        <a class="tvr-button" target="_blank" title="Browse Themeover's marketplace of design packs for various
                WordPress themes and plugins"
                           href="http://themeover.com/theme-packs/">Browse Marketplace</a>
                    </li>-->
                </ul>
                <?php
            }

            // output meta spans and logs tmpl for manage pages
            function manage_packs_meta(){
                ?>
                <span id="ajaxUrl" rel="<?php echo $this->site_url.'/wp-admin/admin.php?page='.$this->microthemeruipage.'&mcth_simple_ajax=1&_wpnonce='.wp_create_nonce('mcth_simple_ajax') ?>"></span>
                <span id="delete-ok" rel='admin.php?page=<?php echo $this->microthemespage;?>&action=tvr_delete_ok&_wpnonce=<?php echo wp_create_nonce('tvr_delete_ok'); ?>'></span>
                <span id="zip-folder" rel="<?php echo $this->thispluginurl.'zip-exports/'; ?>"></span>
                <?php

                echo $this->display_log_item('error', array('short'=> '', 'long'=> ''), 0, 'id="log-item-template"');
            }

            function pack_pagination($page, $total_pages, $total_packs, $start, $end) {
                ?>
                <ul class="tvr-pagination">
                    <?php
                    $i = $total_pages;
                    while ($i >= 1){
                        echo '
                        <li class="page-item">';
                        if ($i == $page) {
                            echo '<span>'.$i.'</span>';
                        } else {
                            echo '<a href="admin.php?page='. $this->microthemespage . '&packs_page='.$i.'">'.$i.'</a>';
                        }
                        echo '
                        </li>';
                        --$i;
                    }
					echo '<li class="displaying-x">' .
						sprintf(wp_kses(__('Displaying %1$s - %2$s of %3$s', 'tvr-microthemer'), array()), $start, $end, $total_packs) . '</li>';

                    if (!empty($this->preferences['theme_in_focus']) and $total_packs > 0){
                        $url = 'admin.php?page=' . $this->managesinglepage . '&design_pack=' . $this->preferences['theme_in_focus'];
                        $name = $this->readable_name($this->preferences['theme_in_focus']);
                        ?>
                        <li class="last-modified" rel="<?php echo $this->preferences['theme_in_focus']; ?>">
                            Last modified design pack: <a title="Edit <?php echo $name ; ?>"
                               href="<?php echo $url; ?>"><?php echo $name ; ?>
                            </a>
                        </li>
                        <?php
                    }
                   ?>
                </ul>
                <?php
            }

            function display_left_menu_icons() {

                if ($this->preferences['buyer_validated']){
                    $unlock_class = 'unlocked';
                    $unlock_title = __('Validate license using a different email address', 'tvr-microthemer');
                } else {
                    $unlock_class = '';
                    $unlock_title = __('Enter your PayPal email (or the email listed in My Downloads) to unlock Microthemer', 'tvr-microthemer');
                }

				// set 'on' buttons
                $code_editor_class = $this->preferences['show_code_editor'] ? ' on' : '';
                $ruler_class = $this->preferences['show_rulers'] ? ' on' : '';


                //
                $html = '
                    <div class="unlock-microthemer '.$unlock_class.' v-left-button show-dialog" rel="unlock-microthemer" title="'.$unlock_title.'"></div>



                    <div class="save-interface v-left-button" title="' . __("Manually save UI settings (Ctrl+S)", "tvr-microthemer") . '"></div>

                    <div id="toggle-highlighting" class="v-left-button"
                    title="'. __("Toggle highlighting", "tvr-microthemer").'"></div>

                    <div id="toggle-rulers" class="toggle-rulers v-left-button '.$ruler_class.'"
                        title="'.__('Toggle rulers on/off', 'tvr-microthemer').'"></div>

                    <div class="ruler-tools v-left-button tvr-popright-wrap">

                        <div class="tvr-popright">
                            <div class="popright-sub">
                                <div id="remove-guides" class="remove-guides v-left-button"
                        title="'.__('Remove all guides', 'tvr-microthemer').'"></div>
                            </div>
                        </div>
                    </div>


                    <div class="code-tools v-left-button tvr-popright-wrap">

                        <div id="edit-css-code" class="edit-css-code v-left-button new-icon-group '.$code_editor_class.'"
                        title="'.__('Code editor view', 'tvr-microthemer').'"></div>

                        <div class="tvr-popright">
                            <div class="popright-sub">
                                <div class="display-css-code v-left-button show-dialog" rel="display-css-code" title="' . __("View the CSS code Microthemer generates", "tvr-microthemer") . '"></div>
                            </div>
                        </div>
                    </div>


                    <div class="preferences-tools v-left-button tvr-popright-wrap">

                        <div class="display-preferences v-left-button show-dialog" rel="display-preferences" title="' . __("Set your global Microthemer preferences", "tvr-microthemer") . '"></div>

                        <div class="tvr-popright">
                            <div class="popright-sub">

                                <div class="edit-media-queries v-left-button show-dialog" rel="edit-media-queries"
                    title="' . __("Edit Media Queries", "tvr-microthemer") . '"></div>

                            </div>
                        </div>
                    </div>


                    <div class="pack-tools v-left-button tvr-popright-wrap">

                        <div class="manage-design-packs v-left-button show-dialog new-icon-group" rel="manage-design-packs" title="' . __("Install & Manage your design packages", "tvr-microthemer") . '"></div>

                        <div class="tvr-popright">
                            <div class="popright-sub">

                                <div class="import-from-pack v-left-button show-dialog" rel="import-from-pack" title="' . __("Import settings from a design pack", "tvr-microthemer") . '"></div>

                    <div class="export-to-pack v-left-button show-dialog" rel="export-to-pack" title="' . __("Export your work as a design pack", "tvr-microthemer") . '"></div>

                            </div>
                        </div>
                    </div>


                    <!--<div class="display-share v-left-button show-dialog" rel="display-share" title="' . __("Spread the word about Microthemer", "tvr-microthemer") . '"></div>-->




                    <div class="reset-tools v-left-button tvr-popright-wrap">

                        <div class="display-revisions v-left-button show-dialog new-icon-group" rel="display-revisions" title="' . __("Restore settings from a previous save point", "tvr-microthemer") . '"></div>

                        <div class="tvr-popright">
                            <div class="popright-sub">
                                <div id="ui-reset" class="v-left-button folder-reset"
                                title="' . __("Reset the interface to the default empty folders", "tvr-microthemer") . '"></div>
                                <div id="clear-styles" class="v-left-button styles-reset"
                                title="' . __("Clear all styles, but leave folders and selectors intact", "tvr-microthemer") . '"></div>
                            </div>
                        </div>
                    </div>


                    <div class="program-docs v-left-button show-dialog new-icon-group" rel="program-docs"
                    title="' . __("Learn how to use Microthemer", "tvr-microthemer") . '"></div>

                    <div class="toggle-full-screen v-left-button" rel="toggle-full-screen"
                    title="' . __("Full screen mode", "tvr-microthemer") . '"></div>

                    <a class="back-to-wordpress v-left-button" title="' . __("Return to WordPress dashboard", "tvr-microthemer") . '"
                    href="'.$this->wp_blog_admin_url.'"></a>


                ';
                return $html;
                /*
                 <div class="integration v-left-button show-dialog tvr-popright-wrap" rel="integration"
                    title="View Microthemer\'s integration options">
                        <div class="tvr-popright integration-right">
                            <div class="popright-sub integration-sub">
                                <div id="toggle-wp-touch" class="v-left-button toggle-wp-touch"
                                title="' . __("Enable/Disable WPTouch mode", "tvr-microthemer") . '">WPTouch</div>
                                <div id="toggle-beaver" class="v-left-button toggle-beaver"
                                title="' . __("Enable/Disable Builder Beaver mode", "tvr-microthemer") . '">Builder Beaver</div>
                            </div>
                        </div>
                    </div>
                 */
            }

			// Resolve property/value input fields
			function resolve_input_fields($section_name, $css_selector, $property_group_array, $property_group_name, $property, $value,
			$con = 'reg', $key=1) {
                $html = '';
                // don't display legacy properties or the image display field
                if (!$this->is_legacy_prop($property_group_name, $property) and !strpos($property, 'img_display') ){
                    include $this->thisplugindir . 'includes/resolve-input-fields.inc.php';
                }
                return $html;
			}

            // menu section html
            function menu_section_html($section_name, $array) {

                $section_name = esc_attr($section_name); //=esc
                if (!empty($this->option['non_section']['display_name'][$section_name])) {
                    // new system that doesn't restrict section name format
                    $display_section_name = $this->option['non_section']['display_name'][$section_name];
                } else {
                    $display_section_name = ucwords(str_replace('_', ' ', $section_name));
                }
                $selector_count_state = $this->selector_count_state($array);
                // generate html code for sections in this loop to save having to do a 2nd loop later
                $this->initial_options_html[$this->total_sections] = $this->section_html($section_name, $array);

                ?>
                <li id="<?php echo 'strk-'.$section_name; ?>" class="section-tag strk strk-sec">
                    <input type="hidden" class="register-section" name="tvr_mcth[<?php echo $section_name; ?>]" value="" />
                    <input type="hidden" class="section-display-name"
                           name="tvr_mcth[non_section][display_name][<?php echo $section_name;?>]"
                           value="<?php echo $display_section_name;?>" />
                    <div class="sec-row item-row">
					<span class="menu-arrow folder-menu-arrow tvr-icon"></span>
					<span class="folder-icon tvr-icon sortable-icon" title="<?php _e("Reorder folder", "tvr-microthemer"); ?>"></span>
					<span class="section-name item-name"
                          <?php /*title="<?php _e("Folder", "tvr-microthemer"); ?>" */ ?>
                        >
                        <span class="name-text selector-count-state"
                            rel="<?php echo $selector_count_state; ?>"><?php echo $display_section_name; ?></span>
                            <?php
                            if ($selector_count_state > 0) {
                                echo ' <span class="folder-count-wrap count-wrap">(<span class="folder-state-count state-count">'.$selector_count_state.'</span>)</span>';
                            }
                            // update global $total_selectors count
                            $this->total_selectors = $this->total_selectors + $selector_count_state;
                            ?>
                        </span>
                        <span class="edit-section-form hidden">
                            <input type='text' class='rename-input' name='rename_section'
                                   value='<?php echo ucwords(str_replace('_', ' ', $section_name));  ?>' />
								   <span class='rename-button tvr-button' title="<?php _e("Rename folder", "tvr-microthemer"); ?>"><?php printf(wp_kses(__('Rename', 'tvr-microthemer'), array())); ?></span>
								   <span class='cancel-rename-section cancel link' title="<?php _e("Cancel rename", "tvr-microthemer"); ?>"><?php printf(wp_kses(__('Cancel', 'tvr-microthemer'), array())); ?></span>
                        </span>
                        <span class="manage-section-icons manage-icons">

						<span class="delete-section tvr-icon delete-icon" title="<?php _e("Delete Folder", "tvr-microthemer"); ?>"></span>
							<span class="copy-section tvr-icon copy-icon" title="<?php _e("Copy Folder", "tvr-microthemer"); ?>"></span>
							<span class="toggle-rename-section tvr-icon edit-icon" title="<?php _e("Rename Folder", "tvr-microthemer"); ?>"></span>
							<span class="reveal-add-selector tvr-icon add-icon" title="<?php _e("Add selector to this folder", "tvr-microthemer"); ?>"></span>
                        </span>
                    </div>
                    <?php if (!$this->optimisation_test){
                        ?>
                        <ul class="selector-sub">
                            <li class="add-selector-list-item">
                                <div class="sel-row item-row">
                                    <?php
                                    $tip = wp_kses(__('Non-coders should use the selector wizard instead of using these form fields. Just double-click something on your site!', 'tvr-microthemer'), array());
                                    if (!$this->optimisation_test){
                                        $this->selector_add_modify_form('add', $tip);
                                    }
                                    ?>
                                </div>

                            </li>
                            <?php
                            // loop over CSS selectors if they exist
                            if ( is_array($array) ) {
                                $sel_loop_count = 0;
                                foreach ( $array as $css_selector => $array) {
                                    ++$sel_loop_count;
                                    ++$this->sel_count;
                                    // selector list item
                                    $this->menu_selector_html($section_name, $css_selector, $array, $sel_loop_count);
                                }
                            }
                            ?>

                        </ul>
                        <?php
                    }
                    ?>

                </li>
            <?php
            }



            // menu single selector html
            function menu_selector_html($section_name, $css_selector, $array, $sel_loop_count) {

                $sel_class = '';

                // determine which style groups are active
                $style_count_state = 0;
                foreach ($this->propertyoptions as $property_group_name => $junk) {
                    if ($this->pg_has_values_inc_legacy_inc_mq($section_name, $css_selector, $array, $property_group_name)) {
                        ++$style_count_state;
                    }
                }

                // disable selectors locked by trial (all sels will be editable even in free trial in future)
                if (!$this->preferences['buyer_validated'] and $this->sel_count > 9 ) {
                    $sel_class.= 'trial-disabled'; // visually signals disabled and, prevents editing
                }

                // should feather be displayed?
                if ($style_count_state > 0 ) {
                    $sel_class.= ' hasValues';
                }

                // can't recall why I went down this route of storing label and code in piped single value.
                if (is_array($array) and !empty($array['label'])){
                    $labelCss = explode('|', $array['label']);
                    // convert my custom quote escaping in recognised html encoded single/double quotes
                    $selector_title = esc_attr(str_replace('cus-', '&', $labelCss[1]));
                } else {
                    $labelCss = array('', '');
                    $array['label'] = '';
                    $selector_title = '';
                }

                ?>
                <li id="<?php echo 'strk-'.$section_name.'-'.$css_selector; ?>" class="selector-tag strk strk-sel <?php echo $sel_class; ?>">

                    <input type='hidden' class='register-selector' name='tvr_mcth[<?php echo $section_name; ?>][<?php echo $css_selector; ?>]' value='' />
                    <input type='hidden'
                           class='view-state-input selector-tracker' name='tvr_mcth[non_section][view_state][<?php echo $section_name;?>][<?php echo $css_selector;?>]' rel='<?php echo $labelCss[0];?>' value='0' />
                    <input type='hidden' class='selector-label' name='tvr_mcth[<?php echo $section_name; ?>][<?php echo $css_selector; ?>][label]' value='<?php echo $array['label']; ?>' />
                    <div class="sel-row item-row">
					    <span class="tvr-icon selector-icon sortable-icon" title="<?php _e("Reorder or move selector into a different folder", "tvr-microthemer"); ?>"></span>
                        <span class="tvr-icon feather-icon"></span>
                        <span class="selector-name item-name change-selector" title="<?php echo $selector_title; ?>">
                        <span class="name-text style-count-state change-selector"
                              rel="<?php echo $style_count_state; ?>"><?php echo $labelCss[0]; ?></span>
                            <?php
                            /* FEATHER SYSTEM SUPERSEDES
                            if ($style_count_state > 0) {
                                echo ' <span class="count-wrap change-selector">(<span class="state-count change-selector">'.$style_count_state.'</span>)</span>';
                            }
                            */
                            ?>
                        </span>
                        <span class="manage-selector-icons manage-icons">
						<span class="delete-selector tvr-icon delete-icon" title="<?php _e("Delete Selector", "tvr-microthemer"); ?>"></span>
							<span class="copy-selector tvr-icon copy-icon" title="<?php _e("Copy Selector", "tvr-microthemer"); ?>"></span>
							<span class="toggle-modify-selector tvr-icon edit-icon" title="<?php _e("Edit Selector", "tvr-microthemer"); ?>"></span>
                        </span>
                        <?php
                        $tip = wp_kses(__('Give your selector a better descriptive name and/or modify the CSS selector code (if you know CSS)', 'tvr-microthemer'), array());
                        if (!$this->optimisation_test){
                            $this->selector_add_modify_form('edit', $tip, $labelCss, $section_name, $css_selector);
                        }
                        ?>
                    </div>
                </li>
            <?php
            }

            // add/modify selector form
            function selector_add_modify_form($con, $tip, $labelCss = '', $section_name ='', $css_selector = '') {
                $display = ucwords($con);
                if (is_array($labelCss)) {
                    $display_selector_name = esc_attr($labelCss[0]);
                    // convert my custom quote escaping in recognised html encoded single/double quotes
                    $selector_css = esc_attr(str_replace('cus-', '&', $labelCss[1]));
                } else {
                    $display_selector_name = '';
                    $selector_css = '';
                }
                ?>
                <div class='<?php echo $con; ?>-selector-form float-form hidden'>
                    <p class="tip">

                        <span><?php echo $tip; ?></span>

                    </p>
                    <p>
						<label><?php printf(wp_kses(__('Descriptive Name:', 'tvr-microthemer'), array())); ?></label>
                        <input type='text' class='selector-name-input' name='<?php echo $con; ?>_selector[label]' value='<?php echo $display_selector_name; ?>' />
                    </p>
                    <p>
                       	<label><?php printf(wp_kses(__('CSS Selector Code:', 'tvr-microthemer'), array())); ?></label>
                        <textarea class='selector-css-textarea' name='<?php echo $con; ?>_selector[css]'><?php echo $selector_css; ?></textarea>
                    </p>
                    <p>

                        <span class="pollyfills">
                            <?php
                            foreach ($this->pollyfills as $polly){
                                if ($this->preferences[$polly.'_by_default'] != 1){
                                    // get the polyfill val
                                    if (!empty($this->options[$section_name][$css_selector][$polly])) {
                                        $input = '<input class="'.$polly.'-tracker" type="hidden"
                    name="tvr_mcth['.$section_name.']['.$css_selector.']['.$polly.']" value="1" />';
                                        $state = 'on';
                                        $class = 'active';
                                    } else {
                                        $input = '';
                                        $state = 'off';
                                        $class = '';
                                    }
                                    $extra_explain = '';
                                    if ($polly == 'pie'){
                                        $extra_explain = __('Note: also adds a "position" value of "relative" unless you explicitly set a value for position (pie needs the target element or parent element to have a non-static position value in order to work).', 'tvr-microthemer');
                                    }
                                    echo $input;
                                    ?>
                                    <span class="<?php echo $polly; ?>-toggle tvr-icon input-icon-toggle <?php echo $class; ?>"
                                          data-input-type="<?php echo $polly; ?>"
                                          title='<?php printf(__('Click to turn the %1$s pollyfill on or off for this selector. %2$s', 'tvr-microthemer'), $polly, $extra_explain); ?>'
                                          rel="<?php echo $state; ?>"></span>
                                <?php
                                }
                            }
                            ?>
                        </span>
                        <span class='<?php echo $con; ?>-selector tvr-button'
							  title="<?php printf(__("selector", "tvr-microthemer"), $display); ?>"><?php printf(wp_kses(__('%s Selector', 'tvr-microthemer'), array()), $display); ?></span>
							  <span class="cancel-<?php echo $con; ?>-selector cancel link" title="<?php printf(__('Cancel %s Selector', 'tvr-microthemer'), $display); ?>"><?php printf(wp_kses(__('Cancel', 'tvr-microthemer'), array())); ?></span>
                    </p>
                </div>
            <?php
            }

            // section html
            function section_html($section_name, $array) {
                $html = '';
                $html.= '
                <li id="opts-'.$section_name.'" class="section-wrap section-tag">
                    '.$this->all_selectors_html($section_name, $array).'
                </li>';
                return $html;
            }

			function all_selectors_html($section_name, $array, $force_load = 0) {
                $html = '';
                $html.= '<ul class="opts-selectors-sub">';
                    // loop the CSS selectors if they exist
                    if ( is_array($array) ) {
                        $this->sel_loop_count = 0; // reset count of selector in section
                        foreach ( $array as $css_selector => $sel_array) {
                            ++$this->sel_loop_count;
                            $html.= $this->single_selector_html($section_name, $css_selector, $sel_array, $force_load);
					}
				}
                $html.= '</ul>';
                return $html;
            }

            // selector html
            function single_selector_html($section_name, $css_selector, $array, $force_load = 0) {
                ++$this->sel_option_count;
                $html = '';
                $css_selector = esc_attr($css_selector); //=esc
                // disable sections locked by trial
                if (!$this->preferences['buyer_validated'] and $this->sel_option_count > 9) {
                    $trial_disabled = 'trial-disabled';
                } else {
                    $trial_disabled = '';
                }
                $html.= '<li id="opts-'.$section_name.'-'.$css_selector.'"
                class="selector-tag selector-wrap '.$trial_disabled.'">';
                // only load style options if we need to force load
                if ($force_load == 1) {
                    $html.= $this->all_option_groups_html($section_name, $css_selector, $array);
                }
                $html.= '</li>';
                return $html;
            }

			// determine the number of selectors in the array
			function selector_count_state($array) {
				$selector_count_state = count($array);
				$selector_count_recursive = count($array, COUNT_RECURSIVE);
				// if the 2 values are the same, the $selector_count_state variable refers to an empty value
				if ($selector_count_state == $selector_count_recursive) {
					$selector_count_state = 0;
				}
				return $selector_count_state;
			}


			// display property group icons and options
			function all_option_groups_html($section_name, $css_selector, $array){

                // get the last viewed property group
                $pg_focus = ( !empty($array['pg_focus']) ) ? $array['pg_focus'] : '';

				// display pg icons
                $html = '
                <ul class="styling-option-icons">';

                    // store the last active pg so we can return to it
                    $html.= '<input class="pg-focus" type="hidden"
                        name="tvr_mcth['.$section_name.']['.$css_selector.'][pg_focus]"
                    value="'.$pg_focus.'" />';

                    // display the pg icons
                    foreach ($this->propertyoptions as $property_group_name => $junk) {

                         /* LEAVE THIS FOR JS
                          *  check if the property group should be "on" (indicating styles have been added)
                        $class = ( $this->pg_has_values_inc_legacy_inc_mq(
                            $section_name,
                            $css_selector,
                            $array,
                            $property_group_name) ) ? 'on hasValues' : '';
                        */

                        $class = '';

                         // check if the property group should be "active" (in focus)
                         if ($property_group_name == $pg_focus) {
                             //$class.= ' active'; // don't do this while we trial the continuity system
                         }

                         // icon
                         $html.='
                         <li class="pg-icon pg-icon-'.$property_group_name.' '.$class.'"
                         rel="'.$property_group_name.'" title="'.$this->property_option_groups[$property_group_name].'">
                         </li>';
                     }

                $html.='
                </ul>';

                // display actual fields
                $html.='
                <ul class="styling-options">';

                    // do all-device and MQ fields
                    foreach ($this->propertyoptions as $property_group_name => $junk) {
                         $html.= $this->single_option_group_html(
                             $section_name,
                             $css_selector,
                             $array,
                             $property_group_name,
                             $pg_focus);
                    }

                    $html.= '
                </ul>';

                return $html;
			}

            // if a pg group has loaded but no values were added we don't want to load it into the dom
            // note: style_config may be redundant now. Needs proper inspection.
            function pg_has_values($array){
                if (empty($array) or !is_array($array)){
                    return false;
                }
                $no_values = true;
                foreach ($array as $key => $value){
                    // must allow zero values!
                   if ( !empty($value) or $value === 0 or $value === '0'){
                       $no_values = false;
                       break;
                   }
                }
                if ($no_values){
                    return false;
                } else {
                    return true;
                }
            }

            // are legacy values present for pg group?
            function has_legacy_values($styles, $property_group_name){
                $legacy_values = false;
                if (!empty($this->legacy_groups[$property_group_name]) and is_array($this->legacy_groups[$property_group_name])){
                    foreach ($this->legacy_groups[$property_group_name] as $leg_group => $array){
                        // check if the pg has values and they are specifically ones have have moved to this pg
                        if ( !empty($styles[$leg_group]) and
                            $this->pg_has_values($styles[$leg_group]) and
                            $this->props_moved_to_this_pg($styles[$leg_group], $array)){
                            $legacy_values = $styles[$leg_group];
                            break;
                        }
                    }
                }
                return $legacy_values;
            }

            function pg_has_values_inc_legacy($array, $property_group_name){
                $styles_found = false;
                if (!empty($array['styles'][$property_group_name]) and $this->pg_has_values($array['styles'][$property_group_name])) {
                    $styles_found['cur_leg'] = 'current';
                } elseif (!empty($array['styles']) and $this->has_legacy_values($array['styles'], $property_group_name)){
                    $styles_found['cur_leg'] = 'legacy';
                }
                return $styles_found;
            }

            // look for any values in property group, including legacy values - and optionally, media query values
            function pg_has_values_inc_legacy_inc_mq($section_name, $css_selector, $array, $property_group_name){

                $styles_found = false;

                // first just look for values in all devices (most likely)
                if ($styles_found = $this->pg_has_values_inc_legacy($array, $property_group_name)) {
                    $styles_found['dev_mq'] = 'all-devices';
                    return $styles_found;
                } else {
                    // look for media query tabs with values too
                    if (!empty($this->options['non_section']['active_queries']) and
                        is_array($this->options['non_section']['active_queries'])) {
                        foreach ($this->options['non_section']['active_queries'] as $mq_key => $junk) { // here
                            // set new $array['styles']
                            if (!empty($this->options['non_section']['m_query'][$mq_key][$section_name][$css_selector])){
                                $array = $this->options['non_section']['m_query'][$mq_key][$section_name][$css_selector];
                                if ($styles_found = $this->pg_has_values_inc_legacy($array, $property_group_name)) {
                                    $styles_found['dev_mq'] = 'mq';
                                    break;
                                }
                            }
                        }
                    }
                    return $styles_found;
                }
            }


            // ensure that specific legacy props have have moved to this pg
            function props_moved_to_this_pg($leg_group_styles, $array){
                // loop through legacy props to see if style values exist
                if (is_array($array)){
                    foreach ($array as $legacy_prop => $legacy_prop_legend_key){
                        if (!empty($leg_group_styles[$legacy_prop])){
                            return true;
                        }
                    }
                }
                return false;
            }

            // determine if options property is legacy or not
            function is_legacy_prop($property_group_name, $property){
                $legacy = false;
                foreach ($this->legacy_groups as $new_group => $array){
                    foreach ($array as $leg_group => $arr2){
                        foreach ($arr2 as $leg_prop => $legacy_prop_legend_key) {
                            if ($property_group_name == $leg_group and $property == $leg_prop) {
                                $legacy = $new_group; // return new group for legacy property
                                break;
                            }
                        }
                    }
                }
                return $legacy;
            }

            // function to get legacy value (inc !important) if it exists
            function populate_from_legacy_if_exists($styles, $sel_imp_array, $prop){
                $target_leg_prop = false;
                $legacy_adjusted['value'] = false;
                $legacy_adjusted['imp'] = '';
                foreach ($this->legacy_groups as $pg => $leg_group_array){
                    foreach ($leg_group_array as $leg_group => $leg_prop_array){
                        // look for prop in value: 1 = same as key
                        foreach ($leg_prop_array as $leg_prop => $legend_key){
                            // prop may have legacy values
                            if ( ($prop == $leg_prop and $legend_key) == 1 or $prop == $legend_key){
                                $target_leg_prop = $leg_prop;

                            } elseif (is_array($legend_key)){
                                // loop through array
                                if (in_array($prop, $legend_key)){
                                    $target_leg_prop = $leg_prop;
                                }
                            }
                            // if the property had a previous location, check for a value
                            if ($target_leg_prop){
                                if (!empty($styles[$leg_group][$target_leg_prop])){
                                    $legacy_adjusted['value'] = $styles[$leg_group][$target_leg_prop];
                                    if (!empty($sel_imp_array[$leg_group][$target_leg_prop])){
                                        $legacy_adjusted['imp'] = $sel_imp_array[$leg_group][$target_leg_prop];
                                    }
                                    break 3; // break out of all loops
                                }
                            }
                        }
                    }
                }
                return $legacy_adjusted;
            }


            // output all the options for a given property group
            function single_option_group_html(
                $section_name,
                $css_selector,
                $array,
                $property_group_name,
                $pg_focus){

                // check if the property group should be "active" (in focus)
                $pg_show_class = ( $property_group_name == $pg_focus ) ? 'show' : '';

                // main pg wrapper
                $html ='
                <li id="opts-'.$section_name.'-'.$css_selector.'-'.$property_group_name.'"
                         class="group-tag group-tag-'.$property_group_name.' hidden '.$pg_show_class.'">';

                    // output all devices and MQ fields
                    $html.= $this->single_device_fields(
                            $section_name,
                            $css_selector,
                            $array,
                            $property_group_name,
                            $pg_show_class);

                $html.= '
                </li>';

                return $html;
            }

            // function for outputing all devices and MQs without repeating code,
            // should create combined array globally really so this can be done everywhere
            function single_device_fields(
                $section_name,
                $css_selector,
                $array,
                $property_group_name,
                $pg_show_class){

                $html = '';

                // output all fields
                foreach ($this->combined_devices() as $key => $m_query){

                    $property_group_array = false;
                    $con = 'reg';

                    // get array val if MQ
                    if ($key != 'all-devices'){
                        $con = 'mq';
                        $array = false;
                        if (!empty($this->options['non_section']['m_query'][$key][$section_name][$css_selector])) {
                            $array = $this->options['non_section']['m_query'][$key][$section_name][$css_selector];
                        }
                    }

                    // need to check for existing styles (inc legacy)
                    if ( $array and $styles_found = $this->pg_has_values_inc_legacy(
                        $array,
                        $property_group_name) ) {

                        // if there are current styles for the all devices tab, retrieve them
                        if ($styles_found['cur_leg'] == 'current'){
                            $property_group_array = $array['styles'][$property_group_name];
                        }

                        // if only legacy values exist set empty array so inputs are displayed
                        else {
                            $property_group_array = array();
                        }
                    }

                    // show fields even if no values if tab is current
                    if ( !$property_group_array and $this->preferences['mq_device_focus'] == $key and $pg_show_class){
                        $property_group_array = array();
                    }

                    // output fields if needed
                    if ( is_array( $property_group_array ) ) {

                        // visible if tab is active
                        $show_class = ( $this->preferences['mq_device_focus'] == $key ) ? 'show' : '';

                        // this is contained in a separate function because the li always needs to exist
                        // as a wrapper for the tmpl div
                        $html.= $this->single_option_fields($section_name,
                            $css_selector,
                            $property_group_array,
                            $property_group_name,
                            $show_class,
                            false,
                            $key,
                            $con);

                    }
                }

                return $html;
            }

            // the options fields part of the property group (which can be added as templates)
            function single_option_fields(
                $section_name,
                $css_selector,
                $property_group_array,
                $property_group_name,
                $show_class,
                $template = false,
                $key = 'all-devices',
                $con = 'reg'){

                // is this template HTML?
                $id = ( $template ) ? 'id="option-group-template-'.$property_group_name. '"' : '';

                // do all-devices fields
                $html = '
                <div '.$id.' class="property-fields hidden property-'.$property_group_name.'
                property-fields-'. $key . ' ' . $show_class.'">
                    ';

                    // merge to allow for new properties added to property-options.inc.php (array with values must come 2nd)
                    $property_group_array = array_merge($this->propertyoptions[$property_group_name], $property_group_array);

                    // output individual property fields
                    foreach ($property_group_array as $property => $value) {

                        // filter prop
                        $property = esc_attr($property);

                        /* if a new CSS property has been added with array_merge(), $value will be something like:
                        Array ( [label] => Left [default_unit] => px [icon] => position_left )
                        - so just set to nothing if it's an array
                        */
                        $value = ( !is_array($value) ) ? esc_attr($value) : '';

                        // format input fields
                        $html.= $this->resolve_input_fields($section_name, $css_selector, $property_group_array, $property_group_name, $property, $value, $con, $key);
                    }

                    $html.= '
                </div>';

                return $html;
            }


            // check for legacy device_focus values (e.g. padding/margin for padding_margin)
            function device_focus_inc_legacy($section_name, $css_selector, $property_group_name, $property_group_array){
                $device_array = array();
                $device_key = '';
                if (!empty($this->options[$section_name][$css_selector]['device_focus'])){
                    $device_array = $this->options[$section_name][$css_selector]['device_focus'];
                }
                $dtab = 'all-devices';
                // check for regular device tab focus
                if (!empty($device_array[$property_group_name])){
                    $device_key = $device_array[$property_group_name];
                }
                // check for legacy pg name
                elseif (!empty($this->legacy_groups[$property_group_name])){
                    foreach($this->legacy_groups[$property_group_name] as $leg_pg => $junk){
                        if (!empty($device_array[$leg_pg])){
                            $device_key = $device_array[$leg_pg];
                            break;
                        }
                    }
                }
                // check if the tab in focus actually has any field values
                if (!empty($this->current_pg_group_tabs[$device_key])){
                    $dtab = $device_key;
                } else {
                    // fallback to a tab that does have values
                    foreach ($this->current_pg_group_tabs as $key){
                        $dtab = $key;
                        break;
                    }
                }
                return $dtab;
            }

            // media query option in "Edit media queries" - also used for template
            function edit_mq_row(
                $m_query = array('label' => '', 'query' => ''),
                $key = 'key',
                $i = 0,
                $template = true){
                    $li_class = 'mq-row-'.$i;
                    if ($template){
                        $li_class = 'm-query-tpl';
                    }
                    ?>
                    <li class="mq-row <?php echo $li_class; ?>">
                        <span class="del-m-query tvr-icon delete-icon" title="<?php _e("Delete this media query", "tvr-microthemer"); ?>"></span>
                        <div class="mq-edit-wrap mq-label-wrap">
                            <label title="<?php printf(wp_kses(__('Give your media query a descriptive name', 'tvr-microthemer'),
                                array())); ?>"><?php printf(wp_kses(__('Label:', 'tvr-microthemer'), array())); ?></label>
                            <input class="m-label" type="text" name="tvr_preferences[m_queries][<?php echo $key; ?>][label]"
                                   value="<?php echo esc_attr($m_query['label']); ?>" />
                        </div>
                        <div class="mq-edit-wrap mq-query-wrap">
                            <label title="<?php _e("Set the media query condition", "tvr-microthemer"); ?>">
                                <?php printf(wp_kses(__('Media Query:', 'tvr-microthemer'), array())); ?></label>
                            <input class="m-code" type="text"
                                   name="tvr_preferences[m_queries][<?php echo $key; ?>][query]"
                                   value="<?php echo esc_attr($m_query['query']); ?>" />
                        </div>
                        <div class="mq-edit-wrap mq-checkbox-wrap">
                            <label title="<?php _e("Hide this tab in the interface if you don't need it right now and you'd like to keep the number of tabs in the interface manageably low", "tvr-microthemer"); ?>">
                                <?php printf(wp_kses(__('Hide Tab In Interface', 'tvr-microthemer'), array())); ?>:</label>
                            <?php
                            $checked = '';
                            $on = '';
                            if ( !empty($this->preferences['m_queries'][$key]['hide']) ){
                                $checked = 'cecked="checked"';
                                $on = 'on';
                            }
                            ?>
                            <input type="checkbox" name="tvr_preferences[m_queries][<?php echo $key; ?>][hide]"
                                   value="1" <?php echo $checked; ?> />
                            <span class="fake-checkbox <?php echo $on ?>"></span>
                            <span class="ef-label">
                                <?php printf(wp_kses(__('Yes (no settings will be lost)', 'tvr-microthemer'), array())); ?>
                            </span>
                        </div>
                    </li>
                <?php
            }

            // The new UI always shows the MQ tabs.
            // This happens even when no selectors are showing, so a different approach is needed
            function global_media_query_tabs(){

                $html = '
                <div class="query-tabs">

                    <span class="edit-mq show-dialog"
                    title="' . __('Edit media queries', 'tvr-microthemer') . '" rel="edit-media-queries">
                    </span>

                    <input class="device-focus" type="hidden"
                    name="tvr_mcth[non_section][device_focus]"
                    value="'.$this->preferences['mq_device_focus'].'" />';

                    // display tabs
                    foreach ($this->combined_devices() as $key => $m_query){

                        // don't show if hidden by the user
                        if ( !empty($m_query['hide']) ) continue;

                        // should the tab be active?
                        $class = ($this->preferences['mq_device_focus'] == $key) ? 'active' : '';

                        $html.= '<span class="mt-tab mq-tab mq-tab-'.$key.' '.$class.'" rel="'.$key.'"
                        title="'.$m_query['query'].'">'.$m_query['label'].'</span>';
                    }


                    $html.= '<div class="clear"></div>

                </div>';

                return $html;
            }


			/* output media query tabs
			function media_query_tabs($section_name, $css_selector, $property_group_name,
                                      $property_group_array = false, $template = false) {

                // we're not currently allowing the mq tabs to be added as a tmpl so this is redundant
                if ($template){
                    $id = 'id="mt-tab-template"';
                } else {
                    $id = '';
                }
                $human_pg_name = ucwords(str_replace('_', ' & ', $property_group_name));
                $html = '';

                $html.='<div '.$id.' class="query-tabs">
                <span class="edit-mq show-dialog" title="' . __('Edit media queries', 'tvr-microthemer') . '" rel="edit-media-queries"></span>
                <span class="add-mq" title="' . sprintf(__('Manage tabs for %1$s', 'tvr-microthemer'), $human_pg_name) . '">
                <span class="tvr-icon add-mq-icon"></span>
                '.$this->media_query_checkboxes($section_name, $css_selector, $property_group_name, $property_group_array).'
                </span>';
                // get the configuration of the device tab
                $device_tab = $this->device_focus_inc_legacy($section_name, $css_selector, $property_group_name, $property_group_array);

				// should the tab be visible (if the pg group has no data it should never be visible)
                $has_styles = '';
				if (is_array($property_group_array)) {
					$has_styles = 'has-styles';
				}
				else {
					//$has_styles = $this->fix_for_mq_update($section_name, $css_selector, $property_group_name, $main_label_show_class);
				}
                if ($device_tab == 'all-devices') {
                    $all_tab_active = 'active';
                } else {
                    $all_tab_active = '';
                }
                $html.='<input class="device-focus" type="hidden"
                name="tvr_mcth['.$section_name.']['.$css_selector.'][device_focus]['.$property_group_name.']"
                value="'.$device_tab.'" />
                <span class="mt-tab mt-tab-all-devices '.$has_styles. ' '.$all_tab_active.'"
				rel="all-devices" title="' . __('General CSS that will apply to all devices', 'tvr-microthemer') . '">' . wp_kses(__('All Devices', 'tvr-microthemer'), array()) . '</span>';
				foreach ($this->preferences['m_queries'] as $key => $m_query) {
					// should the tab be visible
                    //$array = $this->options['non_section']['m_query'][$key][$section_name][$css_selector];
                    //$legacy_values = $this->has_legacy_values($array['styles'], $property_group_name);

					if (
                        //$this->pg_has_values_inc_legacy($array, $property_group_name)
                        !empty($this->current_pg_group_tabs[$key])
                    ) {
						$has_styles = 'has-styles';
                        //$html.= 'hello111 <pre>'. print_r($array) . '</pre>' . $property_group_name . $this->pg_has_values_inc_legacy($array, $property_group_name);
					}
					else {
						$has_styles = '';
					}
					// should the tab be active
					if ($device_tab == $key) {
						$class = 'active';
					} else {
						$class = '';
					}
                    $html.= '<span class="mt-tab mt-tab-'.$key.' '.$class.' '.$has_styles.'" rel="'.$key.'"
					title="'.$m_query['query'].'">'.$m_query['label'].'</span>';
				}
                $html.='<div class="clear"></div>
                </div>';
                return $html;
			}*/

			/* output the media query checkboxes
			function media_query_checkboxes($section_name, $css_selector,
                                            $property_group_name, $property_group_array) {
				// this is the first time we need to check which tabs are active, save the config in a temp variable for later reference
                $this->current_pg_group_tabs = array(); //reset

                $html = '';
                if (is_array($property_group_array)
                    //or $this->fix_for_mq_update($section_name, $css_selector, $property_group_name, $main_label_show_class)
                ) {
                    $checked = 'checked="checked"';
                    $property_group_state = 'on';
                    $this->current_pg_group_tabs['all-devices'] = 'all-devices';
                }
                else {
                    $checked='';
                    $property_group_state = '';
                }
				// All devices checkbox
                $html.='<span class="mq-options">
                <span class="mq-button mqb-all-devices '.$property_group_state.'" title="' . __('Styles will apply to all screen sizes', 'tvr-microthemer') . '">';

                    $html.='<input class="style-config checkbox all-toggle" type="checkbox" autocomplete="off"
                    value="'.$property_group_name.'"
                    name="tvr_mcth['. $section_name.']['.$css_selector.'][all_devices]['.$property_group_name.']"
                    '.$checked.' />
                    <span class="mq-remove tvr-icon" rel="all-devices|All Devices" title="' . __('Remove tab', 'tvr-microthemer') . '"></span>
                    <span class="mq-clear tvr-icon" rel="all-devices|All Devices" title="' . __('Clear tab styles', 'tvr-microthemer') . '"></span>
                    <span class="mq-add tvr-icon" rel="all-devices|All Devices" title="' . __('Add tab', 'tvr-microthemer') . '"></span>
					<span class="mq-button-text mq-add" rel="all-devices|All Devices" title="' . __('Styles will apply to all screen sizes', 'tvr-microthemer') . '">' .
						wp_kses(__('All Devices', 'tvr-microthemer'), array()) . '</span>

                </span>';
				// Media query checkboxes
                foreach ($this->preferences['m_queries'] as $key => $m_query) {
                    // may need to register the group like the regular checkbox (but if no bugs, may be much more efficient not to)
                    // check if the properties group is checked
                    if (
                    !empty($this->options['non_section']['m_query'][$key][$section_name][$css_selector]) and
                        $this->pg_has_values_inc_legacy($this->options['non_section']['m_query'][$key][$section_name][$css_selector],$property_group_name)) {
                        $property_group_state = 'on';
                        $this->current_pg_group_tabs[$key] = $key;
                    }
                    else {
                        $property_group_state = '';
                    }
                    $html.= '<span class="mq-button mqb-'.$key.' '.$property_group_state.'" title="'.$m_query['query'].'">
                    <span class="mq-remove mq-specific tvr-icon" rel="'.$key.'|'.$m_query['label'].'" title="' . __('Remove tab', 'tvr-microthemer') . '"></span>
                    <span class="mq-clear mq-specific tvr-icon" rel="'.$key.'|'.$m_query['label'].'" title="' . __('Clear tab styles', 'tvr-microthemer') . '"></span>
                    <span class="mq-add mq-specific tvr-icon" rel="'.$key.'|'.$m_query['label'].'" title="' . __('Add tab', 'tvr-microthemer') . '"></span>
                    <span class="mq-button-text mq-specific mq-add"  rel="'.$key.'|'.$m_query['label'].'" title="'.$m_query['query'].'">'.$m_query['label'].'</span>

                    </span>';

                }
                // remove all option
                $html.= '
                <span class="mq-button remove-all" title="' . sprintf(__('Remove all "%1$s" styling options for this selector', 'tvr-microthemer'), ucwords($property_group_name)) . '">
                    <span class="mq-remove-all tvr-icon delete-icon"></span>
					<span class="mq-button-text mq-remove-all link">' . wp_kses(__('Remove all tabs', 'tvr-microthemer'), array()) . '</span>
                </span>';

                $html.='</span>';
                return $html;
			} */

			// output the media query form fields (or empty container divs)
			function media_query_fields($section_name, $css_selector, $property_group_name) {

                // Note this function copies a lot of code from single_option_group_html() and single_option_fields()
                // perhaps it could be done more efficiently...

                $html = '';

                // loop through each MQ to check for existing styles (inc legacy)
				foreach ($this->preferences['m_queries'] as $key => $m_query) {

                     $property_group_array = false;

                     if (!empty($this->options['non_section']['m_query'][$key][$section_name][$css_selector])){
                         $array = $this->options['non_section']['m_query'][$key][$section_name][$css_selector];

                         if ($styles_found = $this->pg_has_values_inc_legacy($array, $property_group_name)) {

                             // if there are current styles for the MQ, retrieve them
                             if ($styles_found['cur_leg'] == 'current'){
                                 $property_group_array = $this->options['non_section']['m_query'][$key][$section_name][$css_selector]['styles'][$property_group_name];
                             }
                             // if legacy values exist, set pg as empty array so inputs are displayed
                             else {
                                 $property_group_array = array();
                             }
                         }
                     }

                     // show fields even if no values if tab is current
                     if ( !$property_group_array and $this->preferences['mq_device_focus'] == $key ){
                         $property_group_array = array();
                     }

                     // output MQ fields if needed
                     if ( is_array( $property_group_array ) ) {

                         // visible if all-devices tab is active
                         $show_class = ( $this->preferences['mq_device_focus'] == $key ) ? 'show' : '';

                         $html.= '
                         <div class="property-fields hidden property-'.$property_group_name
                             . ' property-fields-'.$key . ' ' .$show_class.'">';

                         // merge to allow for new properties added to property-options.inc.php (array with values must come 2nd)
                         $property_group_array = array_merge($this->propertyoptions[$property_group_name], $property_group_array);

                         foreach ($property_group_array as $property => $value) {

                             // filter prop
                             $property = esc_attr($property);

                             /* if a new CSS property has been added with array_merge(), $value will be something like:
                             Array ( [label] => Left [default_unit] => px [icon] => position_left )
                             - so just set to nothing if it's an array
                             */
                             $value = ( !is_array($value) ) ? esc_attr($value) : '';

                             // format input fields
                             $html.= $this->resolve_input_fields($section_name, $css_selector, $property_group_array,
                                 $property_group_name, $property, $value, 'mq', $key);
                         }

                         $html.= '
                         </div><!-- end property-fields -->';
                     } // ends foreach property
				}
                return $html;
            }

			// check if need to default to px
			function check_unit($property_group_name, $property, $value) {
				if (!empty($this->preferences['my_props'][$property_group_name]['pg_props'][$property]['default_unit'])
                    and
                    $this->preferences['my_props'][$property_group_name]['pg_props'][$property]['default_unit'] == 'px (implicit)' and
				is_numeric($value) and
                //strpos('%', $value) === false and
				$value != 0) {
					$unit = 'px';
				}
				/*elseif (!empty($this->propertyoptions[$property_group_name][$property]['default_unit']) and
				$this->propertyoptions[$property_group_name][$property]['default_unit'] == '%' and
				is_numeric($value) and
				$value != 0) {
					$unit = '%';
				}*/
				else {
					$unit = '';
				}
				return $unit;
			}

			// check if !important should be used for CSS3 line
			function tvr_css3_imp($section_name_slug, $css_selector_slug, $property_group_name, $prop, $con, $mq_key) {
				if ($this->preferences['css_important'] != '1') {
					if ($con == 'mq') {
						$important_val = $this->options['non_section']['important']['m_query'][$mq_key][$section_name_slug][$css_selector_slug][$property_group_name][$prop];
					} else {
						$important_val = $this->options['non_section']['important'][$section_name_slug][$css_selector_slug][$property_group_name][$prop];
					}
					if ($important_val == '1') {
						$css_important = ' !important';
					}
					else {
						$css_important = '';
					}
				} else {
					$css_important = ' !important';
				}
				return $css_important;
			}


			function convert_ui_data($ui_data, $sty, $con, $key = '1') {
				if ($con == 'mq') {
					$mq_key = $key;
					$mq_label = $this->preferences['m_queries'][$key]['label'];
					$mq_query = $this->preferences['m_queries'][$key]['query'];
					$tab = "\t";
					$sec_breaks = "";
					$sty['data'].= "



/* $mq_label
************************************************************************/
$mq_query {
";
				} else {
					$tab = "";
					$sec_breaks = "";
				}

				// loop through the sections
				foreach ( $ui_data as $section_name => $array) {
					// don't output extranious data
					if ($section_name == 'non_section') {
						continue;
					}
					$section_name_slug = $section_name;
					$section_name = ucwords(str_replace('_', ' ', $section_name));
					// determine if there are any selectors
					$selector_count_state = count($array);
					$selector_count_recursive = count($array, COUNT_RECURSIVE);
					// if the 2 values are the same, the $selector_count_state variable refers to an empty value
					if ($selector_count_state == $selector_count_recursive) {
						$selector_count_state = 0;
					}
					if ($selector_count_state > 0) {
						$sty['data'].= $sec_breaks."
$tab/* =$section_name
$tab-------------------------------------------------------------- */
";
					}
					// loop the CSS selectors if they exist
					if ( is_array($array) ) {

						foreach ( $array as $css_selector => $array) {
							// check if selector array contains property values, if not, break
							$empty = true;
                            if (empty($array['styles'])) {
                              continue;
                            }
							// if a selector is malformed, $array['styles'] will be a string instead of an array
							if (!empty($array['styles']) and !is_array($array['styles'])) {
								$css_sel_name = ucwords(str_replace('_', ' ', $css_selector));
                                $this->log(
                                    wp_kses(__('Malformed error', 'tvr-microthemer'), array()),
                                    '<p>'.$section_name. ' &raquo; '.$css_sel_name.'</p>
									<p>' .
									sprintf(
										wp_kses(__('To fix this issue, please delete the malformed \'%1$s\' selector (which may not be displaying a name) in the \'%2$s\' folder', 'tvr-microthemer'), array()),
										$css_sel_name, $section_name
									) . '</p>' .
									wp_kses(__('Array is: ', 'tvr-microthemer'), array()) . $array['styles']
                                );
								continue; // so script doesn't cause fatal error
							}
							foreach ($sty['prop_key_array'] as $key => $junk) {
								if ( isset($array['styles'][$key]) and $array['styles'][$key] != '' ) {
									$empty = false;
								}
							}

							// sort out the css selector - need to get css label/code from regular ui array
							$css_selector_slug = $css_selector;
							if ($con == 'mq') {
								$array['label'] = $this->options[$section_name_slug][$css_selector_slug]['label'];
							}
							$label_array = explode('|', $array['label']);
							$css_label =  ucwords(str_replace('_', ' ', $label_array[0]));
							$css_selector =  $label_array[1];

							// output comma'd selectors on different lines
							$css_selector = str_replace(", ", ",\n", $css_selector);
							// convert custom escaped single & double quotes back to normal ( [type="submit"] )
							$css_selector = str_replace('cus-quot;', '"', $css_selector);
							$css_selector = str_replace('cus-#039;', '\'', $css_selector);
							$count_styles = count($array);

							// check for use of curly braces
							$curly = strpos($css_selector, '{');

							// if all property values are blank, the array is completely empty
							if ($empty and $curly === false) { continue; }

							// adjust selector if curly braces are present
							if ($curly!== false) {
								$curly_array = explode("{", $css_selector);
								$css_selector = $curly_array[0];
								// save custom styles in an array for later output
								$cusStyles = explode(";", str_replace('}', '', $curly_array[1]) );
							} else {
								$cusStyles = false;
							}

							// media_query_buttons

							// if there are styles or the user has entered hand-coded styles
							if ( ($con != 'mq' and $count_styles > 2) or ($con != 'mq' and $curly!== false ) or ($con == 'mq' and $count_styles > 1) ) {

								$sty['data'].= $tab."/* $css_label */
$tab$css_selector {
";

								// loop the groups of properties for the selector
                                $pie_relevant = false;
                                $box_sizing_relevant = false;
								if ( is_array( $array['styles'] ) ) {
									foreach ( $array['styles'] as $property_group_name => $property_group_array ) {
										if ( is_array( $property_group_array ) ) {
											// reset $xy_done
											$xy_done = false;
											foreach ($property_group_array as $property => $value) {
                                                // we don't want the display_img field in the stylesheet
                                                if ($property == 'list_style_imageimg_display' or $property == 'background_imageimg_display'){
                                                    continue;
                                                }


												// get the appropriate value for !important
												if (empty($this->options['non_section']['important'])) {
													$important_val = 0;
												} else {
													if ($con == 'mq') {
														$important_val = $this->options['non_section']['important']['m_query'][$mq_key][$section_name_slug][$css_selector_slug][$property_group_name][$property];
													} else {
														$important_val = $this->options['non_section']['important'][$section_name_slug][$css_selector_slug][$property_group_name][$property];
													}
												}

												if ( $value != '' ) {
													// check if value needs px extension
													$unit = $this->check_unit($property_group_name, $property, $value);
													// convert custom escaped single & double quotes back to normal (font-family)
													//$value = str_replace('cus-quot;', '"', $value);
													//$value = str_replace('cus-#039;', '\'', $value);
                                                    $value = stripslashes($value);
													// check if !important should be added
													if ($this->preferences['css_important'] != '1') { // don't modify if globally turned on
														if ($important_val == '1'
														or (($property == 'background_position_x' or $property == 'background_position_y')
														and $important_val == '1' )) {
															$sty['css_important'] = ' !important';
														}
														else {
															$sty['css_important'] = '';
														}
													}
                                                    if ($property == 'box_sizing'){
                                                        $box_sizing_relevant = true;
                                                    }
													// if css3 property
													if (in_array($property, $sty['css3'])) {
														include $this->thisplugindir . 'includes/resolve-css3.inc.php';
													}
													else {
														$property = str_replace('_', '-', $property);
														// exception for images
														if ( ($property == 'background-image'
                                                                or $property == 'list-style-image') and $value != 'none') {
															$sty['data'].= $tab."	$property: url(\""
                                                                .$this->root_rel($value, false, true)."\"){$sty['css_important']};
";
														}
														// exception for custom background image position
														elseif ($property == 'background-position' and $value == 'custom') {
															$sty['data'].= ""; // do nothing
														}
														// exception for font family wit Google selected
														elseif ($property == 'font-family' and $value == 'Google Font...') {
															$sty['data'].= ""; // do nothing
														}
														// exception for google font
														elseif ($property == 'google-font' and $value) {
															$sty['g_fonts_used'] = true;
															// separate variant from font name
															$fv = explode(" (", $value);
															$f_name = $fv[0];
															$f_var = str_replace(' ', '', $fv[1]);
															$f_var = str_replace(')', '', $f_var);
															// save unique fonts in array for building Google CSS URL
															$url_font_value = str_replace(' ', '+', $f_name);
															if ($sty['g_fonts'][$url_font_value][$f_var] != 1) {
																$sty['g_fonts'][$url_font_value][$f_var] = 1;
															}
															$sty['data'].= $tab."	font-family: '$f_name'{$sty['css_important']};
";
														}
														// exception for custom bg x/y coordinates
														elseif ($property == 'background-position-x' or $property == 'background-position-y') {
															if ($property_group_array['background_position'] != 'custom') {
																$sty['data'].= ""; // do nothing
															}
															// resolve custom x/y coordinates
															else {
																if ($xy_done) {
																	$sty['data'].= ""; // do nothing (x had a value so we did it then)
																}
																else {
																	// check if !important was specified next to dropdown menu
																	$pos_x_value = $property_group_array['background_position_x'];
																	$pos_y_value = $property_group_array['background_position_y'];
																	// resolve x value and unit
																	if ( $pos_x_value != '' ) {
																		$pos_x_unit = $this->check_unit($property_group_name, 'background_position_x',
																		$pos_x_value);
																		$pos_x = $pos_x_value . $pos_x_unit;
																	}
																	else {
																		$pos_x = '0'; // default to 0
																	}
																	// resolve y value and unit
																	if ( $pos_y_value != '' ) {
																		$pos_y_unit = $this->check_unit($property_group_name, 'background_position_y',
																		$pos_y_value);
																		$pos_y = $pos_y_value . $pos_y_unit;
																	}
																	else {
																		$pos_y = '0'; // default to 0
																	}
																	$sty['data'].= $tab."	background-position:$pos_x $pos_y{$sty['css_important']};
";
																	$xy_done = true;
																}
															}
														}
														// default method
														else {
															$sty['data'].= $tab."	$property: $value{$unit}{$sty['css_important']};
";
														}
														// if opacity property, add cross-browser rules too.
														if ($property == 'opacity') {
															$percent = $value*100;
															$sty['data'].= $tab.'	-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity='.$percent.')"'.$sty['css_important'].';
'.$tab.'        filter: alpha(opacity='.$percent.')'.$sty['css_important'].';
'.$tab.'        -moz-opacity:'.$value.$sty['css_important'].';
'.$tab.'        -khtml-opacity: '.$value.$sty['css_important'].';
';
														}
													}
												}
											}
										}
									}
								}
								// determine if CSS3 PIE needs calling (don't add to body as all other pie will break)
								if ($pie_relevant
								and $css_selector != 'body'
								and $css_selector != 'html'
								and ($this->preferences['pie_by_default'] == 1 or $array['pie'] == 1)
                                ) {
									$sty['data'].= $tab."	behavior: url(".$sty['pie'].");
";
									// auto-apply position:relative if prefered and position hasn't been explicitly defined
									if ( empty($array['styles']['position']['position']) ) {
										$sty['data'].= $tab."	position: relative; /* " .
											_x('Because CSS3 PIE is enabled. It requires this to work.', 'CSS comment', 'tvr-microthemer') . " */
";
									}
								}
                                /* determine if box-sizing pollyfill neeeds calling
                                if ($box_sizing_relevant and ($this->preferences['boxsizing_by_default'] == 1 or $array['boxsizing'] == 1)) {
                                    $sty['data'].= $tab."	*behavior: url(".$sty['boxsizing'].");
";

                                }*/

								// output the custom styles if they exist
								if ($cusStyles and is_array($cusStyles)) {
									$sty['data'].= "	/* " . _x('Selector Custom CSS', 'CSS comment', 'tvr-microthemer') . " */
";
									foreach ($cusStyles as $rule) {
										$clean_rule = trim($rule);
										if ( !empty($clean_rule) ) {
											$sty['data'].= "	$clean_rule;
";
										}
									}

								}

								$sty['data'].= "$tab}
";
							}
						}
					}
				}
				// return the modified $sty array
				if ($con == 'mq') {
					$sty['data'].= "}";
				}
				return $sty;
			}



			// update active-styles.css
			function update_active_styles($activated_from, $context = '') {
				// get path to ative-styles.css
				$act_styles = $this->micro_root_dir.'active-styles.css';

                // check for micro-themes folder and create if doesn't exist
                $this->setup_micro_themes_dir();

				// check if it's writable
				if ( is_writable($act_styles) )  {
					 // stylesheet building code needs to wrapped up in a function as it needs to run twice (again for media queries)
					 // store values in a object that can be passed and returned to function
					$sty['data'] = '/*** Micro'.TVR_MICRO_VARIANT.' Styles ***/' . "\n";
					// for later comparison
					$sty['g_fonts'] = array();
					$sty['g_fonts_used'] = false;
					$sty['prop_key_array'] = $this->property_option_groups;
					// determine if !important should be added to styles
					if ($this->preferences['css_important'] == '1') {
						$sty['css_important'] = ' !important';
					}
					else {
						$sty['css_important'] = '';
					}
					// get path to PIE.php in case it needs to be called
					// $sty['pie'] = str_replace($site_url, '', $this->thispluginurl) . 'pie/PIE.php'; // call pie relative to root so works on SSL pages
					// $sty['pie'].= $this->thispluginurl.'pie/PIE.php'; // commented out coz it fails on ssl pages (if not full site ssl)
					// this will work on ssl pages & localhost with sites in sub directory of document root
                    $polly_path = substr(getenv("SCRIPT_NAME"), 0, -19) . str_replace($this->site_url, '', $this->micro_root_url);
					$sty['pie'] = $polly_path . 'PIE.php';
                    //$sty['boxsizing'] = $polly_path . 'boxsizing.php';
					// css3 properties
					$sty['css3'] = array(
                        'gradient_a',
                        'gradient_b',
                        'gradient_b_pos',
                        'gradient_c',
                        'gradient_angle',
                        'border_top_left_radius',
                        'border_top_right_radius',
                        'border_bottom_right_radius',
                        'border_bottom_left_radius',
                        'box_shadow_color',
                        'box_shadow_x',
                        'box_shadow_y',
                        'box_shadow_blur',
                        'text_shadow_color',
                        'text_shadow_x',
                        'text_shadow_y',
                        'text_shadow_blur' );
					// check if hand coded have been set - output before other css
					if ( trim($this->options['non_section']['hand_coded_css'] != '' ) ) {
					$sty['data'].= '
/* =' . _x('Hand Coded CSS', 'CSS comment', 'tvr-microthemer') . '
-------------------------------------------------------------- */
'.stripslashes($this->options['non_section']['hand_coded_css']) . '
';
					}
					// convert ui data to regular css output
					$sty = $this->convert_ui_data($this->options, $sty, 'regular');
					// convert ui data to media query css output
					if (!empty($this->options['non_section']['m_query']) and is_array($this->options['non_section']['m_query'])) {
						foreach ($this->preferences['m_queries'] as $key => $m_query) {
                            // process media query if it has been in use at all
                            if ( is_array($this->options['non_section']['m_query'][$key])){
                                $sty = $this->convert_ui_data($this->options['non_section']['m_query'][$key], $sty, 'mq', $key);
                            }
						}
					}
					// the file will be created if it doesn't exist. otherwise it is overwritten.
					$write_file = fopen($act_styles, 'w');
					// if write is unsuccessful for some reason
					if (!fwrite($write_file, $sty['data'])) {
                        $this->log(
                            wp_kses(__('Write stylesheet error', 'tvr-microthemer'), array()),
                            '<p>' . sprintf( wp_kses(__('Writing to %s failed. ', 'tvr-microthemer'), array()), $this->root_rel($act_styles) ) . $this->permissionshelp.'</p>'
                        );
					}
					fclose($write_file);
					}
					// no write access
					else {
                        $this->log(
                            wp_kses(__('Write stylesheet error', 'tvr-microthemer'), array()),
							'<p>' . wp_kses(__('WordPress does not have "write" permission	for: ', 'tvr-microthemer'), array())
							. $this->root_rel($act_styles) . '. '.$this->permissionshelp.'</p>'
                        );
					}
					// write to the ie specific stysheets if user defined
					$this->update_ie_sheets();

					// update the preferences value for active theme - custom/theme name
					$pref_array = array();
					$g_ie_array = array();
					// build google font url
					if (!empty($sty['g_fonts_used'])) {
						$g_url = '//fonts.googleapis.com/css?family=';
						$first = true;

						foreach ($sty['g_fonts'] as $url_font_value => $v_array) {
							if ($first) {
								$first = false;
							} else {
								$g_url.='|';
							}
							$g_url.= $url_font_value;
							// add any variations to string
							$v_first = true;
							$v_string = '';
							foreach ($v_array as $f_var => $val) {
								if ( empty($f_var) ) {
									continue;
								}
								if ($v_first) {
									$v_string.= ':';
									$v_first = false;
								} else {
									$v_string.=',';
								}
								$v_string.= $f_var.'';
								$g_ie_array[] = $url_font_value . ':' . $f_var;
							}
							$g_url.= $v_string;

						}
					} else {
						$g_url = '';
					}
					$pref_array['g_fonts_used'] = $sty['g_fonts_used'];
					$pref_array['g_url'] = $g_url;
					$pref_array['g_ie_array'] = $g_ie_array;

					if ($activated_from != 'customised' and $context != 'Merge') {
						$pref_array['theme_in_focus'] = $activated_from;
						$pref_array['active_theme'] = $activated_from;

					}
					if ($context == 'Merge' or $activated_from == 'customised') {
						$pref_array['active_theme'] = 'customised'; // a merge means a new custom configuration
					}
					if ($this->savePreferences($pref_array) and $activated_from != 'customised') {
                        $this->log(
                            wp_kses(__('Design pack activated', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('The design pack was successfully activated.', 'tvr-microthemer'), array()) . '</p>',
                            'dev-notice'
                        );
					}

			}

			// update ie specific stylesheets
			function update_ie_sheets() {
				if ( !empty($this->options['non_section']['ie_css']) and
				is_array($this->options['non_section']['ie_css']) ) {
					foreach ($this->options['non_section']['ie_css'] as $key => $val) {
						// if has custom styles
						$trim_val = trim($val);
						if (!empty($trim_val)) {
							$pref_array['ie_css'][$key] = true;
							$stylesheet = $this->micro_root_dir.'ie-'.$key.'.css';
							// Create new file if it doesn't already exist
							if (!file_exists($stylesheet)) {
								if (!$write_file = fopen($stylesheet, 'w')) {
                                    $this->log(
                                        wp_kses(__('Create IE stylesheet error', 'tvr-microthemer'), array()),
										'<p>' . wp_kses(__('WordPress does not have permission to create: ', 'tvr-microthemer'), array())
										. $this->root_rel($stylesheet) . '. '.$this->permissionshelp.'</p>'
                                    );
								}
								else {
									fclose($write_file);
								}
							}
							// writable
							if ( is_writable($stylesheet) )  {
								// the file will be created if it doesn't exist. otherwise it is overwritten.
								$write_file = fopen($stylesheet, 'w');
								// if write is unsuccessful for some reason
								if (!fwrite($write_file, stripslashes($val))) {
                                    $this->log(
                                        wp_kses(__('Write IE stylesheet error', 'tvr-microthemer'), array()),
                                        '<p>' . sprintf( wp_kses(__('Writing to %s failed. ', 'tvr-microthemer'), array()), $this->root_rel($stylesheet) )
                                        .$this->permissionshelp.'</p>'
                                    );
								}
								fclose($write_file);
							}
							// isn't writable
							else {
                                $this->log(
                                    wp_kses(__('Write IE stylesheet error', 'tvr-microthemer'), array()),
									'<p>' . wp_kses(__('WordPress does not have "write" permission for: ', 'tvr-microthemer'), array())
									. $this->root_rel($stylesheet) . '. '.$this->permissionshelp.'</p>'
                                );
							}
						}
						// no value for stylesheet specified
						else {
							$pref_array['ie_css'][$key] = false;
						}
					}
					// update the preferences so that the stylesheets are called in the <head>
					$this->savePreferences($pref_array);
				}
			}


			// write settings to .json file
			function update_json_file($theme, $context = '', $secondary_content = '') {
                $theme = sanitize_file_name(sanitize_title($theme));
				// create micro theme of 'new' has been requested
				if ($context == 'new') {
					// Check for micro theme with same name now so that config.json is created in the right dir
					$name = $theme;
					if ( is_dir($this->micro_root_dir . $name ) ) {
						$suffix = 1;
						do {
							$alt_name = substr ($name, 0, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
							$dir_check = is_dir($this->micro_root_dir . $alt_name );
							$suffix++;
						} while ( $dir_check );
						$name = $alt_name;
					}
					$theme = $name;
					$this->create_micro_theme($theme, 'export', '');
				}
				// Create new file if it doesn't already exist
				$json_file = $this->micro_root_dir.$theme.'/config.json';
				if (!file_exists($json_file)) {
					if (!$write_file = fopen($json_file, 'w')) { // this creates a blank file for writing
                        $this->log(
                            wp_kses(__('Create json error', 'tvr-microthemer'), array()),
							'<p>' . wp_kses(__('WordPress does not have permission to create: ', 'tvr-microthemer'), array())
							. $this->root_rel($json_file) . '. '.$this->permissionshelp.'</p>'
                        );
					}
					$task = 'created';
				}
				else {
					$task = 'updated';
				}
				// check if it's writable (can skip this bit if a new blank micro theme has been created from the Manage page)
				if ($context != 'create-blank') {
					if ( is_writable($json_file) )  {
						// tap into WordPress native JSON functions
						if( !class_exists('Moxiecode_JSON')) {
							require_once($this->thisplugindir . 'includes/class-json.php');
						}
						$json_object = new Moxiecode_JSON();
						// Always a selective export now

                        // copy full options to var for editing
                        $json_data = $this->options;
                        // loop through full options
                        foreach ($this->options as $section_name => $array) {
                            // if the section wasn't selected, remove it from json data var (along with the view_state var)
                            if (!array_key_exists($section_name, $this->serialised_post['export_sections'])
                                and $section_name != 'non_section') {
                                unset($json_data[$section_name]);
                                unset($json_data['non_section']['view_state'][$section_name]);

                            }
                        }
                        // set handcoded css to nothing if not marked for export
                        if ( empty($this->serialised_post['export_sections']['hand_coded_css'])) {
                            $json_data['non_section']['hand_coded_css'] = '';
                            echo wp_kses(_x('unset: hand-coded', 'Verb', 'tvr-microthemer'), array()) . '<br />';
                        } else {
                            echo '<b>don\'t</b> unset: hand-coded<br />';
                        }
                        // ie too
                        foreach ($this->preferences['ie_css'] as $key => $value) {
                            if ( empty($this->serialised_post['export_sections']['ie_css'][$key])) {
                                $json_data['non_section']['ie_css'][$key] = '';
                                echo wp_kses(_x('unset: ', 'Verb', 'tvr-microthemer'), array()) . $key. '<br />';
                            } else {
                                echo wp_kses(__('<b>don\'t</b> unset: ', 'tvr-microthemer'), array( 'b' => array())) . $key. '<br />';
                            }
                        }
                        // create debug selective export file if specified at top of script
                        if ($this->debug_selective_export) {
                            $data = '';
                            $debug_file = $this->micro_root_dir . $this->preferences['theme_in_focus'] . '/debug-selective-export.txt';
                            $write_file = fopen($debug_file, 'w');
                            $data.= __('The Selectively Exported Options', 'tvr-microthemer') . "\n\n";
                            $data.= print_r($json_data, true);
                            $data.= "\n\n" . __('The Full Options', 'tvr-microthemer') . "\n\n";
                            $data.= print_r($this->options, true);
                            fwrite($write_file, $data);
                            fclose($write_file);
                        }

						// write data to json file
						if ($data = $json_object->encode($json_data)) {
							// the file will be created if it doesn't exist. otherwise it is overwritten.
							$write_file = fopen($json_file, 'w');
							fwrite($write_file, $data);
							fclose($write_file);
						}
						else {
                            $this->log(
                                wp_kses(__('Encode json error', 'tvr-microthemer'), array()),
                                '<p>' . wp_kses(__('WordPress failed to convert your settings into json.', 'tvr-microthemer'), array()) . '</p>'
                            );
						}
					}
					else {
                        $this->log(
                            wp_kses(__('Write json error', 'tvr-microthemer'), array()),
							'<p>' . wp_kses(__('WordPress does not have "write" permission	for: ', 'tvr-microthemer'), array())
							. $this->root_rel($json_file) . '. '.$this->permissionshelp.'</p>'
                        );
					}
				}
				return $theme; // sanitised theme name
			}

            // load .json file
            function load_json_file($json_file, $theme_name, $context = '') {
                $json_error = false;
                // check that config.json exists
                if (file_exists($json_file)) {
                    // attempt to read config.json
                    if (!$read_file = fopen($json_file, 'r')) {
                        $this->log(
                            wp_kses(__('Read json error', 'tvr-microthemer'), array()),
							'<p>' . sprintf( wp_kses(__('WordPress was not able to read %s. ', 'tvr-microthemer'), array()), $this->root_rel($json_file) )
							. '. '.$this->permissionshelp.'</p>'
                        );
                    }
                    else {
                        // get the data
                        $data = fread($read_file, filesize($json_file));
                        fclose($read_file);
                    }
                    // tap into WordPress native JSON functions
                    if( !class_exists('Moxiecode_JSON')) {
                        require_once($this->thisplugindir . 'includes/class-json.php');
                    }
                    $json_object = new Moxiecode_JSON();
                    // convert to array
                    if (!$json_array = $json_object->decode($data)) {
                        $this->log('', '', 'error', 'json-decode', array('json_file', $json_file));
                        $json_error = true;
                    }

                    // Unitless css values may need to be auto-adjusted
                    $filtered_json = $this->filter_json_css_units($json_array);
                    // do for each MQ too
                    if (!empty($filtered_json['non_section']['m_query']) and
                        is_array($filtered_json['non_section']['m_query'])) {
                        foreach ($filtered_json['non_section']['m_query'] as $m_key => $array) {
                            $filtered_json['non_section']['m_query'][$m_key] = $this->filter_json_css_units($array);
                        }
                    }
                    // check what import method the user specified
                    if ($context == 'Overwrite' or empty($context)) {
                        // attempt to decode json into an array
                        if (!$this->options = $filtered_json) {
                            $this->log('', '', 'error', 'json-decode', array('json_file', $json_file));
                            $json_error = true;
                        }
                    }
                    elseif ($context == 'Merge') {
                        // attempt to decode json into an array
                        if (!$this->to_be_merged = $filtered_json) {
                            $this->log('', '', 'error', 'json-decode', array('json_file', $json_file));
                            $json_error = true;
                        }
                        // json success, merge new settings with current
                        else {
                            $this->options = $this->merge($this->options, $this->to_be_merged);
                        }
                    }
                    // json decode was successful
                    if (!$json_error) {
                        $this->log(
                            wp_kses(__('Settings were imported', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('The design pack settings were successfully imported.', 'tvr-microthemer'), array()) . '</p>',
                            'notice'
                        );
                        // check for new mqs
                        $pref_array['m_queries'] = $this->preferences['m_queries'];
                        // check for new media queries in the import
                        $mq_analysis = $this->analyse_mqs(
                            $this->options['non_section']['active_queries'],
                            $pref_array['m_queries']
                        );
                        if ($mq_analysis['new']) {
                            // merge the new queries
                            $pref_array['m_queries'] = array_merge($pref_array['m_queries'], $mq_analysis['new']);

                            $this->log(
                                wp_kses(__('Media queries added', 'tvr-microthemer'), array()),
								'<p>' . wp_kses(__('The design pack you imported contained media queries that are different from the ones used in your current setup. In order for the design pack settings to function correctly, these additional media queries have been imported into your workspace.',
								'tvr-microthemer'), array()) . '</p>
								<p>' . sprintf( wp_kses(__('Please <span %s>review (and possibly rename) the imported media queries</span>. Note: imported queries are marked with "(imp)", which you can remove from the label name once you\'ve reviewed them.', 'tvr-microthemer'), array( 'span' => array() )),
									'class="link show-dialog" rel="edit-media-queries"' ) . ' </p>',
                                'warning'
                            );
                        }
                        if ($this->debug_merge) {
                            $debug_file = $this->micro_root_dir . $this->preferences['theme_in_focus'] . '/debug-merge-post.txt';
                            $write_file = fopen($debug_file, 'w');
                            $data = '';
                            $data.= "\n\nThe merged and MQ replaced options\n\n";
                            $data.= print_r($this->options, true);
                            fwrite($write_file, $data);
                            fclose($write_file);
                        }


                        // save settings in db
                        $this->saveUiOptions($this->options);
                        // update active-styles.css
                        $this->update_active_styles($theme_name, $context); // pass the context so the function doesn't update theme_in_focus
                        // Only update theme_in_focus if it's not a merge
                        if ($context != 'Merge') {
                            $pref_array['theme_in_focus'] = $theme_name;
                        }
                        // update the preferences if not merge or media queries need importing
                        if ($context != 'Merge' or $mq_analysis['new']) {
                            $this->savePreferences($pref_array);
                        }
                    }
                }
                // the config file doesn't exist
                else {
                    $this->log(
                        wp_kses(__('Config file missing', 'tvr-microthemer'), array()),
						'<p>' . sprintf(
							wp_kses(__('%1$s settings file could not be loaded because it doesn\'t exist in %2$s', 'tvr-microthemer'), array()),
							$this->root_rel($json_file),
							'<i>'.$this->readable_name($theme_name).'</i>.' ) . '</p>'
                    );
                }
            }

            // ensure mq keys in pref array and options match
            //- NOTE A SIMPLER SOLUTION WOULD BE TO CONVERT ARRAY INTO STRING AND THEN DO str_replace (may have side effects though)
            function replace_mq_keys($student_key, $role_model_key, $options) {
                $old_new_mq_map[$student_key] = $role_model_key;
                // replace the relevant array keys - unset() doesn't work on $this-> so slightly convoluted solution used
                $cons = array('active_queries', 'm_query');
                $updated_array = array();
                foreach ($cons as $stub => $context) {
                    unset($updated_array);
                    if (is_array($options['non_section'][$context])) {
                        foreach ($options['non_section'][$context] as $cur_key => $array) {
                            if ($cur_key == $student_key) {
                                $key = $role_model_key;
                            } else {
                                $key = $cur_key;
                            }
                            $updated_array[$key] = $array;
                        }
                        $options['non_section'][$context] = $updated_array; // reassign main array with updated keys array
                    }
                }
                // and also the !important media query keys
                $updated_array = array();
                if (!empty($options['non_section']['important']['m_query']) and
                    is_array($options['non_section']['important']['m_query'])) {
                    foreach ($options['non_section']['important']['m_query'] as $cur_key => $array) {
                        if ($cur_key == $student_key) {
                            $key = $role_model_key;
                        } else {
                            $key = $cur_key;
                        }
                        $updated_array[$key] = $array;
                    }
                    $options['non_section']['important']['m_query'] = $updated_array; // reassign main array with updated keys array
                }
                // annoyingly, I also need to do a replace on device_focus key values for all selectors
                foreach($options as $section_name => $array) {
                    if ($section_name == 'non_section') { continue; }
                    // loop through the selectors
                    if (is_array($array)) {
                        foreach ($array as $css_selector => $sub_array) {
                            if (is_array($sub_array['device_focus'])) {
                                foreach ( $sub_array['device_focus'] as $prop_group => $value) {
                                    // replace the value if it is an old key
                                    if (!empty($old_new_mq_map[$value])) {
                                        $options[$section_name][$css_selector]['device_focus'][$prop_group] = $old_new_mq_map[$value];
                                    }
                                }
                            }
                        }
                    }
                }
                return $options;
            }


            // merge the new settings with the current settings
            function merge($orig_settings, $new_settings) {
                // create debug merge file if set at top of script
                if ($this->debug_merge) {
                    $debug_file = $this->micro_root_dir . $this->preferences['theme_in_focus'] . '/debug-merge.txt';
                    $write_file = fopen($debug_file, 'w');
                    $data = '';
                    $data.= "\n\n" . __('The current options', 'tvr-microthemer') . "\n\n";
                    $data.= print_r($orig_settings, true);
                }
                if (is_array($new_settings)) {
                    // check if search needs to be done on important and m_query arrays
                    $mq_arr = $imp_arr = false;
                    if (!empty($new_settings['non_section']['m_query']) and is_array($new_settings['non_section']['m_query'])){
                        $mq_arr = $new_settings['non_section']['m_query'];
                    }
                    if (!empty($new_settings['non_section']['important']) and is_array($new_settings['non_section']['important'])){
                        $imp_arr = $new_settings['non_section']['important'];
                    }

                    // loop through new sections to check for section name conflicts
                    foreach($new_settings as $section_name => $array) {
                        // if a name conflict exists
                        if ( $this->is_name_conflict($section_name, $orig_settings, $new_settings, 'first-check') ) {
                            // create a non-conflicting new name
                            $alt_name = $this->get_alt_section_name($section_name, $orig_settings, $new_settings);
                            // rename the to-be-merged section and the corresponding non_section extras
                            $new_settings[$alt_name] = $new_settings[$section_name];
                            unset($new_settings[$section_name]);
                            $new_settings['non_section']['view_state'][$alt_name] = $new_settings['non_section']['view_state'][$section_name];
                            unset($new_settings['non_section']['view_state'][$section_name]);
                            // also rename all the corresponding [m_query] folder names (ouch)
                            if ($mq_arr){
                                foreach ($mq_arr as $mq_key => $arr){
                                    foreach ($arr as $orig_sec => $arr){
                                        // if the folder name exists in the m_query array, replace
                                        if ($section_name == $orig_sec){
                                            $new_settings['non_section']['m_query'][$mq_key][$alt_name] = $new_settings['non_section']['m_query'][$mq_key][$section_name];
                                            unset($new_settings['non_section']['m_query'][$mq_key][$section_name]);
                                        }
                                    }
                                }
                            }
                            // and the [important] folder names (double ouch)
                            if ($imp_arr){
                                foreach ($imp_arr as $orig_sec => $arr){
                                    // if it's MQ important values
                                    if ($orig_sec == 'm_query'){
                                        foreach ($imp_arr['m_query'] as $mq_key => $arr){
                                            foreach ($arr as $orig_sec => $arr){
                                                // if the folder name exists in the m_query array, replace
                                                if ($section_name == $orig_sec){
                                                    $new_settings['non_section']['important']['m_query'][$mq_key][$alt_name] = $new_settings['non_section']['important']['m_query'][$mq_key][$section_name];
                                                    unset($new_settings['non_section']['important']['m_query'][$mq_key][$section_name]);
                                                }
                                            }
                                        }
                                    } else {
                                        // regular important value
                                        $new_settings['non_section']['important'][$alt_name] = $new_settings['non_section']['important'][$section_name];
                                        unset($new_settings['non_section']['important'][$section_name]);
                                    }
                                }
                            }
                        }
                    }

                    // check if the import contains the same media queries but with different keys
                    $mq_analysis = $this->analyse_mqs(
                        $new_settings['non_section']['active_queries'],
                        $orig_settings['non_section']['active_queries']
                    );
                    // if so normalise (set the keys the same).
                    if ($mq_analysis['replacements_needed']){
                        foreach ($mq_analysis['replacements'] as $student_key => $role_model_key){
                            $new_settings = $this->replace_mq_keys($student_key, $role_model_key, $new_settings);
                        }
                    }

                    if ($this->debug_merge) {
                        $data .= "\n\n" . wp_kses(__('The MQ Analysis', 'tvr-microthemer'), array()) . "\n\n";
                        $data .= print_r($mq_analysis, true);
                        $data .= "\n\n" . wp_kses(__('The to merge options', 'tvr-microthemer'), array()) . "\n\n";
                        $data .= print_r($new_settings, true);
                    }

                    // now that we've checked for and corrected possible name conflicts, merge the arrays (recursively to avoid overwriting)
                    $merged_settings = $this->array_merge_recursive_distinct($orig_settings, $new_settings);

                    // the hand-coded CSS of the imported settings needs to be appended to the original
                    foreach ($this->custom_code as $key => $val){
                        // if regular main custom css
                        if ($key == 'hand_coded_css'){
                            $new_code = trim($new_settings['non_section'][$key]);
                            if (!empty($new_code)) {
                                $merged_settings['non_section'][$key] =
                                    $orig_settings['non_section'][$key]
                                    . "\n\n/* " . _x('CSS from design pack imported with \'Merge\'', 'CSS comment', 'tvr-microthemer') . " */\n"
                                    . $new_settings['non_section'][$key];
                            } else {
                                // the imported pack has no custom code so keep the original
                                $merged_settings['non_section'][$key] = $orig_settings['non_section'][$key];
                            }
                        }
                        // if ie css
                        elseif ($key == 'ie_css'){
                            foreach ($val as $key2 => $val2){
                                $new_code = trim($new_settings['non_section'][$key][$key2]);
                                if (!empty($new_code)) {
                                    $merged_settings['non_section'][$key][$key2] =
                                        $orig_settings['non_section'][$key][$key2]
                                    	. "\n\n/* " . _x('CSS from design pack imported with \'Merge\'', 'CSS comment', 'tvr-microthemer') . " */\n"
                                        . $new_settings['non_section'][$key][$key2];
                                }   else {
                                    // the imported pack has no custom code so keep the original
                                    $merged_settings['non_section'][$key][$key2] = $orig_settings['non_section'][$key][$key2];
                                }
                            }
                        }
                    }
                }
                if ($this->debug_merge) {
                    $data.= "\n\n" . __('The Merged options', 'tvr-microthemer') . "\n\n";
                    $data.= print_r($merged_settings, true);
                    fwrite($write_file, $data);
                    fclose($write_file);
                }
                return $merged_settings;
            }

            // get an array of current mq keys paired with replacements - compare against 'role model' to base current array on
            function analyse_mqs($student_mqs, $role_model_mqs){
                $mq_analysis['new'] = false;
                $mq_analysis['replacements_needed'] = false;
                $i = 0;
                if (!empty($student_mqs) and is_array($student_mqs)) {
                    foreach ($student_mqs as $student_key => $student_array){
                        $replacement_key = $this->in_2dim_array($student_array['query'], $role_model_mqs, 'query');
                        // if new media query
                        if ( !$replacement_key ) {
                            // ensure key is unique by using unique base from last page load
                            $new_key = $this->unq_base.++$i;
                            $mq_analysis['new'][$new_key]['label'] = $student_array['query']. ' (imp)';
                            $mq_analysis['new'][$new_key]['query'] = $student_array['query'];
                        }
                        // else store replacement key
                        else {
                            if ($replacement_key != $student_key){
                                $mq_analysis['replacements_needed'] = true;
                                $mq_analysis['replacements'][$student_key] = $replacement_key;
                            }
                        }
                    }
                }
                return $mq_analysis;
            }


			/***
			Manage Micro Theme Functions
			***/

            function setup_micro_themes_dir(){
                if ( !is_dir($this->micro_root_dir) ) {
                    if ( !wp_mkdir_p( $this->micro_root_dir, 0755 ) ) {
                        $this->log(
                            wp_kses(__('/micro-themes create error', 'tvr-microthemer'), array()),
							'<p>' . sprintf(
								wp_kses(__('WordPress was not able to create the %s directory. ', 'tvr-microthemer'), array()),
								$this->root_rel($this->micro_root_dir)
							) . $this->permissionshelp . '</p>'
                        );
                        return true; // error is true
                    }
                }
                // copy pie over
                $error = $this->copy_pie();
                if ($error){
                    return true;// error is true
                }
                // also create blank active-styles else 404 before user adds styles
                return $this->maybe_create_stylesheet($this->micro_root_dir.'active-styles.css');
            }

            // create active-styles if it doesn't already exist
            function maybe_create_stylesheet($act_styles){
                $error = false;
                if (!file_exists($act_styles)) {
                    if (!$write_file = fopen($act_styles, 'w')) {
                        $this->log(
                            wp_kses(__('Create stylesheet error', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('WordPress does not have permission to create: ', 'tvr-microthemer'), array()) .
                            $this->root_rel($act_styles) . '. '.$this->permissionshelp.'</p>'
                        );
                        $error = true;
                    }
                    else {
                        fclose($write_file);
                    }
                }
                return $error;
            }

            // copy pie files so Microthemer styles can still be used following uninstall
            function copy_pie(){
                $error = false;
                $pie_files = array('PIE.php', 'PIE.htc');
                foreach($pie_files as $file){
                    $orig = $this->thisplugindir . '/pie/' . $file;
                    $new = $this->micro_root_dir . $file;
                    if (file_exists($new)){
                        continue;
                    }
                    if (!copy($orig, $new)){
                        $this->log(
                            wp_kses(__('CSS3 PIE not copied', 'tvr-microthemer'), array()),
							'<p>' . sprintf(
								wp_kses(__('CSS3 PIE (%s) could not be copied to correct location. This is needed to support gradients, rounded corners and box-shadow in old versions of Internet Explorer.', 'tvr-microthemer'), array()),
								$file
							) . '</p>',
                            'error'
                        );
                        $error = true;
                    }
                }
                return $error;
            }

			// create micro theme
			function create_micro_theme($micro_name, $action, $temp_zipfile) {
				// sanitize dir name
				$name = sanitize_file_name( $micro_name  );
                $error = false;
				// extra bit need for zip uploads (removes .zip)
				if ($action == 'unzip') {
					$name = substr($name, 0, -4);
				}
				// check for micro-themes folder and create if doesn't exist
				$error = $this->setup_micro_themes_dir();
				// check if the micro-themes folder is writable
				if ( !is_writeable( $this->micro_root_dir ) ) {
                    $this->log(
                        wp_kses(__('/micro-themes write error', 'tvr-microthemer'), array()),
						'<p>' . sprintf(
							wp_kses(__('The directory %s is not writable. ', 'tvr-microthemer'), array()),
							$this->root_rel($this->micro_root_dir)
						) . $this->permissionshelp . '</p>'
                    );
					$error = true;
				}
				// Check for micro theme with same name
				if ($alt_name = $this->rename_if_required($this->micro_root_dir, $name)) {
					$name = $alt_name; // $alt_name is false if no rename was required
				}
				// abs path
				$this_micro_abs = $this->micro_root_dir . $name;
				// Create new micro theme folder
				if ( !wp_mkdir_p ( $this_micro_abs ) ) {
                    $this->log(
                        wp_kses(__('design pack create error', 'tvr-microthemer'), array()),
						'<p>' . sprintf(
							wp_kses(__('WordPress was not able to create the %s directory.', 'tvr-microthemer'), array()), $this->root_rel($this_micro_abs)
						). '</p>'
                    );
					$error = true;
				}
				// Check folder permission
				if ( !is_writeable( $this_micro_abs ) ) {
                    $this->log(
                        wp_kses(__('design pack write error', 'tvr-microthemer'), array()),
						'<p>' . sprintf(
							wp_kses(__('The directory %s is not writable. ', 'tvr-microthemer'), array()), $this->root_rel($this_micro_abs)
						) . $this->permissionshelp . '</p>'
                    );
					$error = true;
				}
				if (SAFE_MODE and $this->preferences['safe_mode_notice'] == '1') {
                    $this->log(
                        wp_kses(__('Safe-mode is on', 'tvr-microthemer'), array()),
						'<p>' . wp_kses(__('The PHP server setting "Safe-Mode" is on.', 'tvr-microthemer'), array())
						. '</p><p>' . sprintf(
							wp_kses(__('<b>This isn\'t necessarily a problem. But if the design pack "%1$s" hasn\'t been created</b>, please create the directory %2$s manually and give it permission code 777. ', 'tvr-microthemer'),
								array( 'b' => array() )
							),
							$this->readable_name($name), $this->root_rel($this_micro_abs) 
						) . $this->permissionshelp
					. '</p>',
                        'warning'
                    );
					$error = true;
				}
				// unzip if required
				if ($action == 'unzip') {
                    // extract the files
					$this->extract_files($this_micro_abs, $temp_zipfile);
                    // get the final name of the design pack from the meta file
                    $name = $this->rename_from_meta($this_micro_abs . '/meta.txt', $name);
                    if ($name){
                        // import bg images to media library and update paths if any are found
                        $json_config_file = $this->micro_root_dir . $name . '/config.json';
                        $this->import_pack_images_to_library($json_config_file, $name);
                    }
                    // add the dir to the file structure array
                    $this->dir_loop($this->micro_root_dir . $name);
				}
				// if creating blank shell or exporting UI settings, need to create meta.txt and readme.txt
				if ($action == 'create' or $action == 'export') {
					// set the theme name value
					$_POST['theme_meta']['Name'] = $this->readable_name($name);
					$this->update_meta_file($this_micro_abs . '/meta.txt');
					$this->update_readme_file($this_micro_abs . '/readme.txt');
					if ($action == 'create') {
						$this->update_json_file($name, 'create-blank');
					}
				}
				// update the theme_in_focus value in the preferences table
				$pref_array['theme_in_focus'] = $name;
				if (!$this->savePreferences($pref_array)) {
					// not much cause for returning an error
				}
				// if still no error, the action worked
				if ($error != true) {
					if ($action == 'create') {
                        $this->log(
                            wp_kses(__('Design pack created', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('The design pack directory was successfully created on the server.', 'tvr-microthemer'), array()) . '</p>',
                            'notice'
                        );
					}
					if ($action == 'unzip') {
                        $this->log(
                            wp_kses(__('Design pack installed', 'tvr-microthemer'), array()),
							'<p>' . sprintf(
								wp_kses(__('The design pack was successfully uploaded and extracted. You can import it into your Microthemer workspace any time using the <span %s>import option</span>', 'tvr-microthemer'),
								array( 'span' => array() )),
								'class="show-parent-dialog link" rel="import-from-pack"'
							) . '<span id="update-packs-list" rel="'.$this->readable_name($name).'"></span>.</p>',
                            'notice'
                        );
					}
					if ($action == 'export') {
                        $this->log(
                            wp_kses(__('Settings exported', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('Your settings were successfully exported as a design pack directory on the server.', 'tvr-microthemer'), array()) . '</p>',
                            'notice'
                        );
					}
				}
			}

            // rename zip form meta.txt name value
            function rename_from_meta($meta_file, $name){
                $orig_name = $name;
                if (is_file($meta_file) and is_readable($meta_file)) {
                    $meta_info = $this->read_meta_file($meta_file);
                    $name = strtolower(sanitize_file_name( $meta_info['Name'] ));
                    // rename the directory if it doesn't already have the correct name
                    if ($orig_name != $name){
                        if ($alt_name = $this->rename_if_required($this->micro_root_dir, $name)) {
                            $name = $alt_name; // $alt_name is false if no rename was required
                        }
                        rename($this->micro_root_dir . $orig_name, $this->micro_root_dir . $name);
                    }
                    return $name;
                } else {
                    // no meta file error
                    $this->log(
                        wp_kses(__('Missing meta file', 'tvr-microthemer'), array()),
						'<p>' . sprintf(
							wp_kses(__('The zip file doesn\'t contain a necessary %s file or it could not be read.', 'tvr-microthemer'), array()),
							$this->root_rel($meta_file)
						) . '</p>'
                    );
                    return false;
                }
            }

            // read the data from a file into a string
            function get_file_data($file){
                if (!is_file($file)){
                    $this->log(
                        wp_kses(__('File doesn\'t exist', 'tvr-microthemer'), array()),
						'<p>' . sprintf(
							wp_kses(__('%s does not exist on the server.', 'tvr-microthemer'), array()),
							$this->root_rel($file)
						) . '</p>'
                    );
                    return false;
                }
                if (!is_readable($file)){
                    $this->log(
                        wp_kses(__('File not readable', 'tvr-microthemer'), array()),
						'<p>' . sprintf(
							wp_kses(__(' %s could not be read.', 'tvr-microthemer'), array()),
							$this->root_rel($file)
						) . '</p>'
                        . $this->permissionshelp
                    );
                    return false;
                }
                $fh = fopen($file, 'r');
                $data = fread($fh, filesize($file));
                fclose($fh);
                return $data;
            }

            // get image paths from the config.json file
            function get_image_paths($data){
                $img_array = array();
                // also need to add list_style_image & border_image_src
                preg_match_all('/"(background_image|list_style_image|border_image_src)":"([^none][A-Za-z0-9 _\-\.\\/&\(\)\[\]!\{\}\?:=]+)"/',
                    $data,
                    $img_array,
                    PREG_PATTERN_ORDER);
                    /*echo '<pre>';
                        print_r($img_array);
                       echo '</pre>';*/
                // ensure $img_array only contains unique images
                foreach ($img_array[2] as $key => $config_img_path) {
                    // if it's no unique, remove
                    if (!empty($already_got[$config_img_path])){
                        unset($img_array[2][$key]);
                    }
                    $already_got[$config_img_path] = 1;
                }
                if (count($img_array[2]) > 0) {
                    return $img_array;
                } else {
                    return false;
                }
            }

            // get media library images linked to from the config.json file
            function get_linked_library_images($json_config_file){
                // get config data
                if (!$data = $this->get_file_data($json_config_file)) {
                    return false;
                }
                // get images from the config file that should be imported
                if (!$img_array = $this->get_image_paths($data)) {
                    return false;
                }
                // loop through the image array, remove any images not in the media library
                foreach ($img_array[2] as $key => $config_img_path) {
                    // has uploads path and doens't also exist in pack dir (yet to be moved) - may be an unnecessary check
                    if (strpos($config_img_path, '/uploads/')!== false and !is_file($this->micro_root_dir . $config_img_path)){
                        $library_images[] = $config_img_path;
                    }
                }
                if (is_array($library_images)){
                    return $library_images;
                } else {
                    return false;
                }

            }

            // import images in a design pack to the media library and update image paths in config.json
            function import_pack_images_to_library($json_config_file, $name){
                // reset imported images
                $this->imported_images = array();
                // get config data
                if (!$data = $this->get_file_data($json_config_file)) {
                    return false;
                }
                // get images from the config file that should be imported
                if (!$img_array = $this->get_image_paths($data)) {
                    return false;
                }
                // loop through the image array
                foreach ($img_array[2] as $key => $config_img_path) {
                    $just_image_name = basename($config_img_path);
                    $pack_image = $this->micro_root_dir . $name . '/' . $just_image_name;
                    // import the file to the media library if it exists
                    if (file_exists($pack_image)) {
                        $this->imported_images[$just_image_name]['orig_config_path'] = $config_img_path;
                        $id = $this->import_image_to_library($pack_image, $just_image_name);
                        // report wp error if problem
                        if ( $id === 0 or is_wp_error($id) ) {
                            if (is_wp_error($id)){
                                $wp_error = '<p>'. $id->get_error_message() . '</p>';
                            } else {
                                $wp_error = '';
                            }
                            $this->log(
                                wp_kses(__('Move to media library failed', 'tvr-microthemer'), array()),
								'<p>' . sprintf(
									wp_kses(__('%s was not imported due to an error.', 'tvr-microthemer'), array()),
									$this->root_rel($pack_image)
								) . '</p>'
                                . $wp_error
                            );
                        }
                    }
                }
                // first report successfully moved images
                $moved_list =
                    '<ul>';
                $moved = false;
                foreach ($this->imported_images as $just_image_name => $array){
                    if (!empty($array['success'])){
                        $moved_list.= '
                                    <li>
                                        '.$just_image_name.'
                                    </li>';
                        $moved = true;
                        // also update the json data string
                        $replacements[$array['orig_config_path']] = $array['new_config_path'];
                    }
                }
                $moved_list.=
                    '</ul>';
                if ($moved){
                    $this->log(
                        wp_kses(__('Images transferred to media library', 'tvr-microthemer'), array()),
                        '<p>' . wp_kses(__('The following images were transferred from the design pack to your WordPress media library:', 'tvr-microthemer'), array()) . '</p>'
                        . $moved_list,
                        'notice'
                    );
                    // update paths in json file
                    $this->replace_json_paths($json_config_file, $replacements, $data);
                }
            }

            // update paths in json file
            function replace_json_paths($json_config_file, $replacements, $data = false){
                if (!$data){
                    if (!$data = $this->get_file_data($json_config_file)) {
                        return false;
                    }
                }
                // replace paths in string
                $replacement_occurred = false;
                foreach ($replacements as $orig => $new){
                    if (strpos($data, $orig) !== false){
                        $replacement_occurred = true;
                        $data = str_replace($orig, $new, $data);
                    }
                }
                if (!$replacement_occurred){
                    return false;
                }
                // update the config.json image paths for images successfully moved to the library
                if (is_writable($json_config_file)) {
                    if ($write_file = fopen($json_config_file, 'w')) {
                        if (fwrite($write_file, $data)) {
                            fclose($write_file);
                            $this->log(
                                wp_kses(__('Images paths updated', 'tvr-microthemer'), array()),
                                '<p>' . wp_kses(__('Images paths were successfully updated to reflect the new location or deletion of an image(s).', 'tvr-microthemer'), array()) . '</p>',
                                'notice'
                            );
                        }
                        else {
                            $this->log(
                                wp_kses(__('Image paths failed to update.', 'tvr-microthemer'), array()),
								'<p>' . wp_kses(__('Images paths could not be updated to reflect the new location of the images transferred to your media library. This happened because Microthemer could not rewrite the config.json file.', 'tvr-microthemer'), array()) . '</p>' . $this->permissionshelp
                            );
                        }
                    }
                }
            }

            // Unitless css values need to be auto-adjusted to explicit pixels if the user's preference
            // for the prop is not 'px (implicit)' and the value is a unitless number
            function filter_json_css_units($data, $context = 'reg'){
                $filtered_json = $data;
                foreach ($filtered_json as $section_name => $array){
                    if ($section_name == 'non_section') {
                        continue;
                    }
                    if (is_array($array)) {
                        foreach ($array as $css_selector => $arr) {
                            if ( is_array( $arr['styles'] ) ) {
                                foreach ($arr['styles'] as $prop_group => $arr2) {
                                    if (is_array($arr2)) {
                                        foreach ($arr2 as $prop => $value) {
                                            // we're finally at property, does it need explicit px added?
                                            if (!empty($this->preferences['my_props'][$prop_group]['pg_props'][$prop]['default_unit'])){
                                                $default_unit = $this->preferences['my_props'][$prop_group]['pg_props'][$prop]['default_unit'];
                                            } else {
                                                continue;
                                            }
                                            // it has a default, is it something other than px (implicit)
                                            if ($default_unit == 'px (implicit)'){
                                                continue;
                                            }
                                            // if the value is a unitless number apply px as the user doesn't have implicit pixels set
                                            if (is_numeric($value) and $value != 0){
                                                $filtered_json[$section_name][$css_selector]['styles'][$prop_group][$prop] = $value . 'px';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                return $filtered_json;
            }

            //Handle an individual file import.
            function import_image_to_library($file, $just_image_name, $post_id = 0, $import_date = false) {
                set_time_limit(60);
                // Initially, Base it on the -current- time.
                $time = current_time('mysql', 1);
                // A writable uploads dir will pass this test. Again, there's no point overriding this one.
                if ( ! ( ( $uploads = wp_upload_dir($time) ) && false === $uploads['error'] ) ) {
                    $this->log(
                        wp_kses(__('Uploads folder error', 'tvr-microthemer'), array()),
                        $uploads['error']
                    );
                    return 0;
                }

                $wp_filetype = wp_check_filetype( $file, null );
                $type = $ext = false;
                extract( $wp_filetype );
                if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) ) {
                    $this->log(
                        wp_kses(__('Wrong file type', 'tvr-microthemer'), array()),
                        '<p>' . wp_kses(__('Sorry, this file type is not permitted for security reasons.', 'tvr-microthemer'), array()) . '</p>'
                    );
                    return 0;
                }

                //Is the file already in the uploads folder?
                if ( preg_match('|^' . preg_quote(str_replace('\\', '/', $uploads['basedir'])) . '(.*)$|i', $file, $mat) ) {
                    $filename = basename($file);
                    $new_file = $file;

                    $url = $uploads['baseurl'] . $mat[1];

                    $attachment = get_posts(array( 'post_type' => 'attachment', 'meta_key' => '_wp_attached_file', 'meta_value' => ltrim($mat[1], '/') ));
                    if ( !empty($attachment) ) {
                        $this->log(
                            wp_kses(__('Image already in library', 'tvr-microthemer'), array()),
							'<p>' . sprintf(
								wp_kses(__('%s already exists in the WordPress media library and was therefore not moved', 'tvr-microthemer'), array()),
								$filename
							) . '</p>',
                            'warning'
                        );
                        return 0;
                    }
                    //OK, Its in the uploads folder, But NOT in WordPress's media library.
                } else {
                    $filename = wp_unique_filename( $uploads['path'], basename($file));

                    // copy the file to the uploads dir
                    $new_file = $uploads['path'] . '/' . $filename;
                    if ( false === @rename( $file, $new_file ) ) {
                        $this->log(
                            wp_kses(__('Move to library failed', 'tvr-microthemer'), array()),
							'<p>' . sprintf(
								wp_kses(__('%1$s could not be moved to %2$s', 'tvr-microthemer'), array()),
								$filename,
								$uploads['path']
							) . '</p>',
                            'warning'
                        );
                        return 0;
                    }


                    // Set correct file permissions
                    $stat = stat( dirname( $new_file ));
                    $perms = $stat['mode'] & 0000666;
                    @ chmod( $new_file, $perms );
                    // Compute the URL
                    $url = $uploads['url'] . '/' . $filename;
                }

                //Apply upload filters
                $return = apply_filters( 'wp_handle_upload', array( 'file' => $new_file, 'url' => $url, 'type' => $type ) );
                $new_file = $return['file'];
                $url = $return['url'];
                $type = $return['type'];

                $title = preg_replace('!\.[^.]+$!', '', basename($new_file));
                $content = '';

                // update the array for replacing paths in config.json
                $this->imported_images[$just_image_name]['success'] = true;
                $this->imported_images[$just_image_name]['new_config_path'] = $this->root_rel($url, false, true);

                // use image exif/iptc data for title and caption defaults if possible
                if ( $image_meta = @wp_read_image_metadata($new_file) ) {
                    //if ( '' != trim($image_meta['title']) )
                        //$title = trim($image_meta['title']);
                    if ( '' != trim($image_meta['caption']) )
                        $content = trim($image_meta['caption']);
                }

                //=sebcus the title should reflect a possible file rename e.g. image1 - happens above ^
                //$title = str_replace('.'.$ext, '', $filename);

                if ( $time ) {
                    $post_date_gmt = $time;
                    $post_date = $time;
                } else {
                    $post_date = current_time('mysql');
                    $post_date_gmt = current_time('mysql', 1);
                }

                // Construct the attachment array
                $attachment = array(
                    'post_mime_type' => $type,
                    'guid' => $url,
                    'post_parent' => $post_id,
                    'post_title' => $title,
                    'post_name' => $title,
                    'post_content' => $content,
                    'post_date' => $post_date,
                    'post_date_gmt' => $post_date_gmt
                );

                $attachment = apply_filters('afs-import_details', $attachment, $file, $post_id, $import_date);

                //Win32 fix:
                $new_file = str_replace( strtolower(str_replace('\\', '/', $uploads['basedir'])), $uploads['basedir'], $new_file);

                // Save the data
                $id = wp_insert_attachment($attachment, $new_file, $post_id);
                if ( !is_wp_error($id) ) {
                    $data = wp_generate_attachment_metadata( $id, $new_file );
                    wp_update_attachment_metadata( $id, $data );
                }
                //update_post_meta( $id, '_wp_attached_file', $uploads['subdir'] . '/' . $filename );

                return $id;
            }

            // handle zip package
            function handle_zip_package() {
                $temp_zipfile = $_FILES['upload_micro']['tmp_name'];
                $filename = $_FILES['upload_micro']['name']; // it won't be this name for long
                // Chrome return a empty content-type : http://code.google.com/p/chromium/issues/detail?id=6800
                if ( !preg_match('/chrome/i', $_SERVER['HTTP_USER_AGENT']) ) {
                    // check if file is a zip file
                    if ( !preg_match('/(zip|download|octet-stream)/i', $_FILES['upload_micro']['type']) ) {
                        @unlink($temp_zipfile); // del temp file
                        $this->log(
                            wp_kses(__('Faulty zip file', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('The uploaded file was faulty or was not a zip file.', 'tvr-microthemer'), array()) . '</p>
						<p>' . wp_kses(__('The server recognised this file type: ', 'tvr-microthemer'), array()) . $_FILES['upload_micro']['type'].'</p>'
                        );
                    }
                }
                $this->create_micro_theme($filename, 'unzip', $temp_zipfile);
            }

			// handle zip extraction
			function extract_files($dir, $file) {
				// tap into native WP zip handling
				if( !class_exists('PclZip')) {
					require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
				}
				$archive = new PclZip($file);
				// extract all files in one folder - the callback functions
				// (tvr_microthemer_getOnlyValid)
				// have to be external to this class
				if ($archive->extract(PCLZIP_OPT_PATH, $dir, PCLZIP_OPT_REMOVE_ALL_PATH,
										PCLZIP_CB_PRE_EXTRACT, 'tvr_micro'.TVR_MICRO_VARIANT.'_getOnlyValid') == 0) {
                    $this->log(
                        wp_kses(__('Extract zip error', 'tvr-microthemer'), array()),
                        '<p>' . wp_kses(__('Error : ', 'tvr-microthemer'), array()) . $archive->errorInfo(true).'</p>'
                    );
				}
			}

			// handle zip archiving
			function create_zip($path_to_dir, $dir_name, $zip_store) {
				$error = false;
                if( !class_exists('PclZip')) {
					require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
				}
				// check if the /zip-exports dir is writable first
				if (is_writable($zip_store)) {
					$archive = new PclZip($zip_store.$dir_name.'.zip');
					// create zip
					$v_list = $archive->create($path_to_dir.$dir_name,
										 PCLZIP_OPT_REMOVE_PATH, $path_to_dir);
					if ($v_list == 0) {
                        $error = true;
                        $this->log(
                            wp_kses(__('Create zip error', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('Error : ', 'tvr-microthemer'), array()) . $archive->errorInfo(true).'</p>'
                        );
					}
					else {
                        $this->log(
                            wp_kses(__('Zip package created', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('Zip package successfully created.', 'tvr-microthemer'), array()) .
							'<a href="'.$this->thispluginurl.'zip-exports/'.$dir_name.'.zip">' .
							wp_kses(__('Download zip file', 'tvr-microthemer'), array()) . '</a>
						</p>',
                            'notice'
                        );
					}
				}
				else {
                    $error = true;
                    $this->log(
                        wp_kses(__('Zip store error', 'tvr-microthemer'), array()),
						'<p>' . sprintf(
							wp_kses(__('The directory %s is not writable. ', 'tvr-microthemer'), array()),
							$this->root_rel($zip_store)
						) . $this->permissionshelp . '</p>'
                    );
				}
                // verdict
                if ($error){
                    return false;
                } else {
                    return true;
                }
			}

			// read meta data from file
			function read_meta_file($meta_file) {
				// create default meta.txt file if it doesn't exist
				if (!is_file($meta_file)) {
					$_POST['theme_meta']['Name'] = $this->readable_name($this->preferences['theme_in_focus']);
					$this->update_meta_file($this->micro_root_dir . $this->preferences['theme_in_focus'].'/meta.txt');
				}
				if (is_file($meta_file)) {
					// check if it's readable
					if ( is_readable($meta_file) )  {
						//disable wptexturize
						remove_filter('get_theme_data', 'wptexturize');
						return $this->flx_get_theme_data( $meta_file );
					}
					else {
						$abs_meta_path = $this->micro_root_dir . $this->preferences['theme_in_focus'].'/meta.txt';

                        $this->log(
                            wp_kses(__('Read meta.txt error', 'tvr-microthemer'), array()),
							'<p>' . wp_kses(__('WordPress does not have permission to read: ', 'tvr-microthemer'), array()) .
							$this->root_rel($abs_meta_path) . '. '.$this->permissionshelp.'</p>'
                        );
						return false;
					}
				}
			}

			// read readme.txt data from file
			function read_readme_file($readme_file) {
				// create default readme file if it doesn't exist
				if (!is_file($readme_file)) {
					$this->update_readme_file($this->micro_root_dir . $this->preferences['theme_in_focus'].'/readme.txt');
				}
				if (is_file($readme_file)) {
					// check if it's readable
					if ( is_readable($readme_file) )  {
						$fh = fopen($readme_file, 'r');
                        $length = filesize($readme_file);
                        if ($length == 0) {
                            $length = 1;
                        }
						$data = fread($fh, $length);
						fclose($fh);
						return $data;
					}
					else {
						$abs_readme_path = $this->micro_root_dir . $this->preferences['theme_in_focus'].'/readme.txt';
                        $this->log(
                            wp_kses(__('Read readme.txt error', 'tvr-microthemer'), array()),
							'<p>' . wp_kses(__('WordPress does not have permission to read: ', 'tvr-microthemer'), array()) .
							$this->root_rel($abs_readme_path) . '. '.$this->permissionshelp.'</p>'
                        );
						return false;
					}
				}
			}

			// adapted WordPress function for reading and formattings a template file
			function flx_get_theme_data( $theme_file ) {
				$default_headers = array(
					'Name' => 'Theme Name',
                    'PackType' => 'Pack Type',
					'URI' => 'Theme URI',
					'Description' => 'Description',
					'Author' => 'Author',
					'AuthorURI' => 'Author URI',
					'Version' => 'Version',
					'Template' => 'Template',
					'Status' => 'Status',
					'Tags' => 'Tags'
					);
				// define allowed tags
				$themes_allowed_tags = array(
					'a' => array(
						'href' => array(),'title' => array()
						),
					'abbr' => array(
						'title' => array()
						),
					'acronym' => array(
						'title' => array()
						),
					'code' => array(),
					'em' => array(),
					'strong' => array()
				);
				// get_file_data() - WP 2.8 compatibility function created for this
				$theme_data = get_file_data( $theme_file, $default_headers, 'theme' );
				$theme_data['Name'] = $theme_data['Title'] = wp_kses( $theme_data['Name'], $themes_allowed_tags );
                $theme_data['PackType'] = wp_kses( $theme_data['PackType'], $themes_allowed_tags );
				$theme_data['URI'] = esc_url( $theme_data['URI'] );
				$theme_data['Description'] = wp_kses( $theme_data['Description'], $themes_allowed_tags );
				$theme_data['AuthorURI'] = esc_url( $theme_data['AuthorURI'] );
				$theme_data['Template'] = wp_kses( $theme_data['Template'], $themes_allowed_tags );
				$theme_data['Version'] = wp_kses( $theme_data['Version'], $themes_allowed_tags );
				if ( empty($theme_data['Status']) )
					$theme_data['Status'] = 'publish';
				else
					$theme_data['Status'] = wp_kses( $theme_data['Status'], $themes_allowed_tags );

				if ( empty($theme_data['Tags']) )
					$theme_data['Tags'] = array();
				else
					$theme_data['Tags'] = array_map( 'trim', explode( ',', wp_kses( $theme_data['Tags'], array() ) ) );

				if ( empty($theme_data['Author']) ) {
					$theme_data['Author'] = $theme_data['AuthorName'] = __('Anonymous');
				} else {
					$theme_data['AuthorName'] = wp_kses( $theme_data['Author'], $themes_allowed_tags );
					if ( empty( $theme_data['AuthorURI'] ) ) {
						$theme_data['Author'] = $theme_data['AuthorName'];
					} else {
						$theme_data['Author'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $theme_data['AuthorURI'], __( 'Visit author homepage' ), $theme_data['AuthorName'] );
					}
				}
				return $theme_data;
			}

			// delete theme
			function tvr_delete_micro_theme($dir_name) {
                $error = false;
				// loop through files if they exist
				if (is_array($this->file_structure[$dir_name])) {
					foreach ($this->file_structure[$dir_name] as $dir => $file) {
						if (!unlink($this->micro_root_dir . $dir_name.'/'.$file)) {
                            $this->log(
                                wp_kses(__('File delete error', 'tvr-microthemer'), array()),
                                '<p>' . wp_kses(__('Unable to delete: ', 'tvr-microthemer'), array()) .
                                $this->root_rel($this->micro_root_dir .
                                    $dir_name.'/'.$file) . '</p>'
                            );
                            $error = true;
						}
					}
				}
				if ($error != true) {
                    $this->log(
                        'Files successfully deleted',
						'<p>' . sprintf(
							wp_kses(__('All files within %s were successfully deleted.', 'tvr-microthemer'), array()),
							$this->readable_name($dir_name)
						) . '</p>',
                        'dev-notice'
                    );
					// attempt to delete empty directory
					if (!rmdir($this->micro_root_dir . $dir_name)) {
                        $this->log(
                            wp_kses(__('Delete directory error', 'tvr-microthemer'), array()),
							'<p>' . sprintf(
								wp_kses(__('The empty directory: %s could not be deleted.', 'tvr-microthemer'), array()),
								$this->readable_name($dir_name)
							) . '</p>'
                        );
                        $error = true;
					}
					else {
                        $this->log(
                            wp_kses(__('Directory successfully deleted', 'tvr-microthemer'), array()),
                            '<p>' . sprintf(
								wp_kses(__('%s was successfully deleted.', 'tvr-microthemer'), array()),
								$this->readable_name($dir_name)
							) . '</p>',
                            'notice'
                        );

						// reset the theme_in_focus value in the preferences table
						$pref_array['theme_in_focus'] = '';
						if (!$this->savePreferences($pref_array)) {
							// not much cause for a message
						}
                        if ($error){
                            return false;
                        } else {
                            return true;
                        }
					}
				}
			}

			// update the meta file
			function update_meta_file($meta_file) {
                // check if the micro theme dir needs to be renamed
                if (isset($_POST['prev_micro_name']) and ($_POST['prev_micro_name'] != $_POST['theme_meta']['Name'])) {
                    $orig_name = $this->micro_root_dir . $this->preferences['theme_in_focus'];
                    $new_theme_in_focus = sanitize_file_name(sanitize_title($_POST['theme_meta']['Name']));
                    // need to do unique dir check here too
                    // Check for micro theme with same name
                    if ($alt_name = $this->rename_if_required($this->micro_root_dir, $new_theme_in_focus)) {
                        $new_theme_in_focus = $alt_name;
                        // The dir had to be automatically renamed so update the visible name
                        $_POST['theme_meta']['Name'] = $this->readable_name($new_theme_in_focus);
                    }
                    $new_name = $this->micro_root_dir . $new_theme_in_focus;
                    // if the directory is writable
                    if (is_writable($orig_name)) {
                        if (rename($orig_name, $new_name)) {
                            // if rename is successful...

                            // the meta file will have a different location now
                            $meta_file = str_replace($this->preferences['theme_in_focus'], $new_theme_in_focus, $meta_file);

                            // update the files array directory key
                            $cache = $this->file_structure[$this->preferences['theme_in_focus']];
                            $this->file_structure[$new_theme_in_focus] = $cache;
                            unset($this->file_structure[$this->preferences['theme_in_focus']]);

                            // update the value in the preferences table
                            $pref_array = array();
                            $pref_array['theme_in_focus'] = $new_theme_in_focus;
                            if ($this->savePreferences($pref_array)) {
                                $this->log(
                                    wp_kses(__('Design pack renamed', 'tvr-microthemer'), array()),
                                    '<p>' . wp_kses(__('The design pack directory was successfully renamed on the server.', 'tvr-microthemer'), array()) . '</p>',
                                    'notice'
                                );
                            }
                        }
                        else {
                            $this->log(
                                wp_kses(__('Directory rename error', 'tvr-microthemer'), array()),
								'<p>' . sprintf(
									wp_kses(__('The directory, %s could not be renamed for some reason.', 'tvr-microthemer'), array()),
									$this->root_rel($orig_name)
								) . '</p>'
                            );
                        }
                    }
                    else {
                        $this->log(
                            wp_kses(__('Directory rename error', 'tvr-microthemer'), array()),
							'<p>' . sprintf(
								wp_kses(__('WordPress does not have permission to rename the directory %1$s to match your new theme name "%2$s". ', 'tvr-microthemer'), array()),
								$this->root_rel($orig_name),
								htmlentities($this->readable_name($_POST['theme_meta']['Name']))
								) . $this->permissionshelp.'.</p>'
                        );
                    }
                }


				// Create new file if it doesn't already exist
				if (!file_exists($meta_file)) {
					if (!$write_file = fopen($meta_file, 'w')) {
                        $this->log(
                            sprintf( wp_kses(__('Create %s error', 'tvr-microthemer'), array()), 'meta.txt'),
                            '<p>' . sprintf(wp_kses(__('WordPress does not have permission to create: %s', 'tvr-microthemer'), array()), $this->root_rel($meta_file) . '. '.$this->permissionshelp ) . '</p>'
                        );
					}
					else {
						fclose($write_file);
					}
					$task = 'created';
					// set post variables if undefined (might be following initial export)

					if (!isset($_POST['theme_meta']['Description'])) {

						global $user_identity;
						get_currentuserinfo();
						/* get the user's website (fallback on site_url() if null)
						$user_info = get_userdata($user_ID);
						if ($user_info->user_url != '') {
							$author_uri = $user_info->user_url;
						}
						else {
							$author_uri = site_url();
						}*/
						// get parent theme name and version
						//$theme_data = wp_get_theme(get_stylesheet_uri());
						// $template = $theme_data['Name'] . ' ' . $theme_data['Version'];
						//$template = $theme_data['Name'];
                        $_POST['theme_meta']['Description'] = "";
                        $_POST['theme_meta']['PackType'] ='';
                        $_POST['theme_meta']['Author'] = $user_identity;
						$_POST['theme_meta']['AuthorURI'] = '';
						// $_POST['theme_meta']['Template'] = get_current_theme();
						$_POST['theme_meta']['Template'] = '';
						$_POST['theme_meta']['Version'] = '1.0';
						$_POST['theme_meta']['Tags'] = '';

					}
				}
				else {
					$task = 'updated';
				}



				// check if it's writable - // need to remove carriage returns
				if ( is_writable($meta_file) )  {

                    /*
                    note: if DateCreated is missing the pack was made before june 12.
                    This may or may not be useful information.
                    */

                    //removed Theme URI: '.strip_tags(stripslashes($_POST['theme_meta']['URI'])).'

					$data = '/*
Theme Name: '.strip_tags(stripslashes($_POST['theme_meta']['Name'])).'
Pack Type: '.strip_tags(stripslashes($_POST['theme_meta']['PackType'])).'
Description: '.strip_tags(stripslashes(str_replace(array("\n", "\r"), array(" ", ""), $_POST['theme_meta']['Description']))).'
Author: '.strip_tags(stripslashes($_POST['theme_meta']['Author'])).'
Author URI: '.strip_tags(stripslashes($_POST['theme_meta']['AuthorURI'])).'
Template: '.strip_tags(stripslashes($_POST['theme_meta']['Template'])).'
Version: '.strip_tags(stripslashes($_POST['theme_meta']['Version'])).'
Tags: '.strip_tags(stripslashes($_POST['theme_meta']['Tags'])).'
DateCreated: '.date('Y-m-d').'
*/';

					 // the file will be created if it doesn't exist. otherwise it is overwritten.
					 $write_file = fopen($meta_file, 'w');
					 fwrite($write_file, $data);
					 fclose($write_file);
					 // success message
                    $this->log(
                        'meta.txt '.$task,
                        '<p>' . sprintf( wp_kses(__('The %1$s file for the design pack was %2$s', 'tvr-microthemer'), array()), 'meta.txt', $task ) . '</p>',
                        'dev-notice'
                    );
				}
				else {
                    $this->log(
                        sprintf( wp_kses(__('Write %s error', 'tvr-microthemer'), array()), 'meta.txt'),
						'<p>' . wp_kses(__('WordPress does not have "write" permission for: ', 'tvr-microthemer'), array()) .
						$this->root_rel($meta_file) . '. '.$this->permissionshelp.'</p>'
                    );
				}

			}

			// update the readme file
			function update_readme_file($readme_file) {
				// Create new file if it doesn't already exist
				if (!file_exists($readme_file)) {
					if (!$write_file = fopen($readme_file, 'w')) {
                        $this->log(
                        	sprintf( wp_kses(__('Create %s error', 'tvr-microthemer'), array()), 'readme.txt'),
							'<p>' . sprintf(
							wp_kses(__('WordPress does not have permission to create: %s', 'tvr-microthemer'), array()),
							$this->root_rel($readme_file) . '. '.$this->permissionshelp
						) . '</p>'
                        );
					}
					else {
						fclose($write_file);
					}
					$task = 'created';
					// set post variable if undefined (might be defined if theme dir has been
					// created manually and then user is submitting readme info for the first time)
					if (!isset($_POST['tvr_theme_readme'])) {
                        $_POST['tvr_theme_readme'] = '';
					}
				}
				else {
					$task = 'updated';
				}
				// check if it's writable
				if ( is_writable($readme_file) )  {
					$data = stripslashes($_POST['tvr_theme_readme']); // don't use striptags so html code can be added
					 // the file will be created if it doesn't exist. otherwise it is overwritten.
					 $write_file = fopen($readme_file, 'w');
					 fwrite($write_file, $data);
					 fclose($write_file);
					 // success message
                     $this->log(
                        'readme.txt '.$task,
						'<p>' . sprintf(
							wp_kses(__('The %1$s file for the design pack was %2$s', 'tvr-microthemer'), array()),
							'readme.txt', $task
						) . '</p>',
                        'dev-notice'
                     );
				}
				else {
                    $this->log(
                        sprintf( wp_kses(__('Write %s error', 'tvr-microthemer'), array()), 'readme.txt'),
						'<p>' . wp_kses(__('WordPress does not have "write" permission for: ', 'tvr-microthemer'), array()) .
						$this->root_rel($readme_file) . '. '.$this->permissionshelp.'</p>'
                    );
				}
			}

			// handle file upload
			function handle_file_upload() {
				// if no error
				if ($_FILES['upload_file']['error'] == 0) {
					$file = $_FILES['upload_file']['name'];
					// check if the file has a valid extension
					if ($this->is_acceptable($file)) {
						$dest_dir = $this->micro_root_dir .  $this->preferences['theme_in_focus'].'/';
						// check if the directory is writable
						if (is_writeable($dest_dir) ) {
							// copy file if safe
							if (is_uploaded_file($_FILES['upload_file']['tmp_name'])
							and copy($_FILES['upload_file']['tmp_name'], $dest_dir . $file)) {
                                $this->log(
                                    wp_kses(__('File successfully uploaded', 'tvr-microthemer'), array()),
									'<p>' . sprintf(
										wp_kses(__('<b>%s</b> was successfully uploaded.', 'tvr-microthemer'), array( 'b' => array() )),
										htmlentities($file)
									) . '</p>',
                                    'notice'
                                );
                                // update the file_structure array
                                $this->file_structure[$this->preferences['theme_in_focus']][$file] = $file;

                                // resize file if it's a screeshot
                                if ($this->is_screenshot($file)) {
                                    $img_full_path = $dest_dir . $file;
                                    // get the screenshot size, resize if too big
                                    list($width, $height) = getimagesize($img_full_path);
                                    if ($width > 896 or $height > 513){
                                        $this->wp_resize(
                                            $img_full_path,
                                            896,
                                            513,
                                            $img_full_path);
                                    }
                                    // now do thumbnail
                                    $thumbnail = $dest_dir . 'screenshot-small.'. $this->get_extension($file);
                                    $root_rel_thumb = $this->root_rel($thumbnail);
                                    if (!$final_dimensions = $this->wp_resize(
                                        $img_full_path,
                                        145,
                                        83,
                                        $thumbnail)) {
                                        $this->log(
                                            wp_kses(__('Screenshot thumbnail error', 'tvr-microthemer'), array()),
											'<p>' . sprintf(
												wp_kses(__('Could not resize <b>%s</b> to thumbnail proportions.', 'tvr-microthemer'), array( 'b' => array() )), $root_rel_thumb
											) . $img_full_path .
											wp_kses(__(' thumb: ', 'tvr-microthemer'), array()).$thumbnail.'</p>'
                                        );
                                    }
                                    else {
                                        // update the file_structure array
                                        $file = basename($thumbnail);
                                        $this->file_structure[$this->preferences['theme_in_focus']][$file] = $file;
                                        $this->log(
                                            wp_kses(__('Screenshot thumbnail successfully created', 'tvr-microthemer'), array()),
											'<p>' . sprintf(
												wp_kses(__('<b>%s</b> was successfully created.', 'tvr-microthemer'), array()),
												$root_rel_thumb
											) . '</p>',
                                            'notice'
                                        );
                                    }
                                }


							}
						}
						// it's not writable
						else {
                            $this->log(
                                wp_kses(__('Write to directory error', 'tvr-microthemer'), array()),
								'<p>'. wp_kses(__('WordPress does not have "Write" permission to the directory: ', 'tvr-microthemer'), array()) .
								$this->root_rel($dest_dir) . '. '.$this->permissionshelp.'.</p>'
                            );
						}
					}
					else {
                        $this->log(
                            wp_kses(__('Invalid file type', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('You have uploaded a file type that is not allowed.', 'tvr-microthemer'), array()) . '</p>'
                        );

					}
				}
				// there was an error - save in global message
				else {
					$this->log_file_upload_error($_FILES['upload_file']['error']);
				}
			}

            // log file upload problem
            function log_file_upload_error($error){
                switch ($error) {
                    case 1:
                        $this->log(
                            wp_kses(__('File upload limit reached', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('The file you uploaded exceeded your "upload_max_filesize" limit. This is a PHP setting on your server.', 'tvr-microthemer'), array()) . '</p>'
                        );
                        break;
                    case 2:
                        $this->log(
                            wp_kses(__('File size too big', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('The file you uploaded exceeded your "max_file_size" limit. This is a PHP setting on your server.', 'tvr-microthemer'), array()) . '</p>'
                        );
                        break;
                    case 3:
                        $this->log(
                            wp_kses(__('Partial upload', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('The file you uploaded only partially uploaded.', 'tvr-microthemer'), array()) . '</p>'
                        );
                        break;
                    case 4:
                        $this->log(
                            wp_kses(__('No file uploaded', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('No file was detected for upload.', 'tvr-microthemer'), array()) . '</p>'
                        );
                        break;
                }
            }

            // resize image using wordpress functions
            function wp_resize($path, $w, $h, $dest, $crop = true){
                $image = wp_get_image_editor( $path );
                if ( ! is_wp_error( $image ) ) {
                    $image->resize( $w, $h, $crop );
                    $image->save( $dest );
                    return true;
                } else {
                    return false;
                }
            }

			// resize image
			function resize($img, $max_width, $max_height, $newfilename) {
				//Check if GD extension is loaded
				if (!extension_loaded('gd') && !extension_loaded('gd2')) {
                    $this->log(
                        wp_kses(__('GD not loaded', 'tvr-microthemer'), array()),
                        '<p>' . wp_kses(__('The PHP extension GD is not loaded.', 'tvr-microthemer'), array()) . '</p>'
                    );
					return false;
				}
				//Get Image size info
				$imgInfo = getimagesize($img);
				switch ($imgInfo[2]) {
					case 1: $im = imagecreatefromgif($img); break;
					case 2: $im = imagecreatefromjpeg($img);  break;
					case 3: $im = imagecreatefrompng($img); break;
					default:
                        $this->log(
                            wp_kses(__('File type error', 'tvr-microthemer'), array()),
                            '<p>' . wp_kses(__('Unsuported file type. Are you sure you uploaded an image?', 'tvr-microthemer'), array()) . '</p>'
                        );

					return false; break;
				}
				// orig dimensions
				$width = $imgInfo[0];
				$height = $imgInfo[1];
				// set proportional max_width and max_height if one or the other isn't specified
				if ( empty($max_width)) {
					$max_width = round($width/($height/$max_height));
				}
				if ( empty($max_height)) {
					$max_height = round($height/($width/$max_width));
				}
				// abort if user tries to enlarge a pic
				if (($max_width > $width) or ($max_height > $height)) {
                    $this->log(
                        wp_kses(__('Dimensions too big', 'tvr-microthemer'), array()),
						'<p>' . sprintf(
							wp_kses(__('The resize dimensions you specified (%1$s x %2$s) are bigger than the original image (%3$s x %4$s). This is not allowed.', 'tvr-microthemer'), array()),
							$max_width, $max_height, $width, $height
						) . '</p>'
                    );
					return false;
				}

				// proportional resizing
				$x_ratio = $max_width / $width;
				$y_ratio = $max_height / $height;
				if (($width <= $max_width) && ($height <= $max_height)) {
					$tn_width = $width;
					$tn_height = $height;
				}
				else if (($x_ratio * $height) < $max_height) {
					$tn_height = ceil($x_ratio * $height);
					$tn_width = $max_width;
				}
				else {
					$tn_width = ceil($y_ratio * $width);
					$tn_height = $max_height;
				}
				// for compatibility
				$nWidth = $tn_width;
				$nHeight = $tn_height;
				$final_dimensions['w'] = $nWidth;
				$final_dimensions['h'] = $nHeight;
				$newImg = imagecreatetruecolor($nWidth, $nHeight);
				/* Check if this image is PNG or GIF, then set if Transparent*/
				if(($imgInfo[2] == 1) or ($imgInfo[2]==3)) {
					imagealphablending($newImg, false);
					imagesavealpha($newImg,true);
					$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
					imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
				}
				imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);
				// Generate the file, and rename it to $newfilename
				switch ($imgInfo[2]) {
					case 1: imagegif($newImg,$newfilename); break;
					case 2: imagejpeg($newImg,$newfilename);  break;
					case 3: imagepng($newImg,$newfilename); break;
					default:
                    $this->log(
                        wp_kses(__('Image resize failed', 'tvr-microthemer'), array()),
                        '<p>' . wp_kses(__('Your image could not be resized.', 'tvr-microthemer'), array()) . '</p>'
                    );
                    return false;
                    break;
				}
				return $final_dimensions;
			}
			// next function


		} // End Class
	} // End if class exists statement

	/***
	PCLZIP only seems to accept non Class member functions, hence the following functions appear here
	***/

	// check file types (microthemer)
	if (!function_exists('tvr_microthemer_getOnlyValid')) {
		function tvr_microthemer_getOnlyValid($p_event, &$p_header)	{
			// avoid null byte hack (THX to Dominic Szablewski)
			if ( strpos($p_header['filename'], chr(0) ) !== false )
				$p_header['filename'] = substr ( $p_header['filename'], 0, strpos($p_header['filename'], chr(0) ));
			$info = pathinfo($p_header['filename']);
			// check for extension
			$ext = array('jpeg', 'jpg', 'png', 'gif', 'txt', 'json', 'psd', 'ai');
			if ( in_array( strtolower($info['extension']), $ext) ) {
				// For MAC skip the ".image" files
				if ($info['basename']{0} ==  '.' )
					return 0;
				else
					return 1;
			}
			// ----- all other files are skipped
			else {
			  return 0;
			}
		}
	}

	if (!function_exists('tvr_microloader_getOnlyValid')) {
		// check file types (microloader)
		function tvr_microloader_getOnlyValid($p_event, &$p_header)	{
			// avoid null byte hack (THX to Dominic Szablewski)
			if ( strpos($p_header['filename'], chr(0) ) !== false )
				$p_header['filename'] = substr ( $p_header['filename'], 0, strpos($p_header['filename'], chr(0) ));
			$info = pathinfo($p_header['filename']);
			// check for extension
			$ext = array('jpeg', 'jpg', 'png', 'gif', 'txt', 'json', 'psd', 'ai');
			if ( in_array( strtolower($info['extension']), $ext) ) {
				// For MAC skip the ".image" files
				if ($info['basename']{0} ==  '.' )
					return 0;
				else
					return 1;
			}
			// ----- all other files are skipped
			else {
			  return 0;
			}
		}
	}
	// PCLZIP_CB_POST_EXTRACT is an option too: http://www.phpconcept.net/pclzip/user-guide/49

	/***
	WP 2.7 COMPATIBILITY FUNCTIONS
	***/

	// escape html data for html attribute fields
	if (!function_exists('esc_attr')) {
		function esc_attr($text) {
			return attribute_escape($text);
		}
	}

	// escape url
	if (!function_exists('esc_url')) {
		function esc_url($text) {
			return clean_url($text);
		}
	}

	// Strip close comment and close php tags from file headers used by WP
	if (!function_exists('_cleanup_header_comment')) {
		function _cleanup_header_comment($str) {
			return trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $str));
		}
	}

	// get url of site
	if (!function_exists('home_url')) {
		function home_url() {
			return site_url();
		}
	}

	/***
	WP 2.7, 2.8 COMPATIBILITY FUNCTIONS
	***/

	// get data from meta.txt
	if (!function_exists('get_file_data')) {
		function get_file_data( $file, $default_headers, $context = '' ) {
			$fp = fopen( $file, 'r' );
			// Pull only the first 8kiB of the file in.
			$file_data = fread( $fp, 8192 );
			// PHP will close file handle, but we are good citizens.
			fclose( $fp );
			if ( !empty($context) ) {
				$extra_headers = apply_filters( "extra_{$context}_headers", array() );
				$extra_headers = array_flip( $extra_headers );
				foreach( $extra_headers as $key=>$value ) {
					$extra_headers[$key] = $key;
				}
				$all_headers = array_merge( $extra_headers, (array) $default_headers );
			} else {
				$all_headers = $default_headers;
			}
			foreach ( $all_headers as $field => $regex ) {
				preg_match( '/^[ \t\/*#]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, ${$field});
				if ( !empty( ${$field} ) )
					${$field} = _cleanup_header_comment( ${$field}[1] );
				else
					${$field} = '';
			}
			$file_data = compact( array_keys( $all_headers ) );
			return $file_data;
		}
	}

	/***
	INSTANTIATE THE ADMIN CLASS
	***/
	if (class_exists('tvr_microthemer_admin')) {
		$tvr_microthemer_admin_var = new tvr_microthemer_admin();
	}

} // ends 'if is_admin()' condition

// frontend code - insert active-styles.css in head section if active
if (!is_admin()) {
	// admin class
	if (!class_exists('tvr_microthemer_frontend')) {
		// define
		class tvr_microthemer_frontend {

			// @var string The preferences string name for this plugin
            var $time = 0;
			var $preferencesName = 'preferences_themer_loader';
			// @var array $preferences Stores the ui options for this plugin
			var $preferences = array();
			var $version = '4.1.3';
            var $microthemeruipage = 'tvr-microthemer.php';

			/**
			* PHP 4 Compatible Constructor

			function tvr_microthemer_frontend(){$this->__construct();}
             * */
			/**
			* PHP 5 Constructor
			*/

			function __construct(){

                $this->time = time();

				// check that styles are active
				$this->preferences = get_option($this->preferencesName);

				// get path variables
				include dirname(__FILE__) .'/get-dir-paths.inc.php';

				// add active-styles.css (if not preview)
				if (!isset($_GET['tvr_micro'])) {
					add_action( 'wp_print_styles', array(&$this, 'add_css'), 999999);
				}
				// else add the preview css
				else {
					add_action( 'wp_print_styles', array(&$this, 'add_preview_css'), 999999);
				}

                // add shortcut to Microthemer
                if (!empty($this->preferences['admin_bar_shortcut']) and $this->preferences['admin_bar_shortcut'] == 1) {
                    add_action( 'admin_bar_menu', array(&$this, 'custom_toolbar_link'), 999999);
                }

				// add viewport = 1 if set in preferences
				if ($this->preferences['initial_scale'] == 1) {
					add_action( 'wp_head', array(&$this, 'viewport_meta') );
				}
				// add meta_tag if logged in - else undefined iframeUrl variable creates break error
				add_action( 'wp_head', array(&$this, 'add_meta_tag'));
				// add firebug overlay script
				add_action( 'wp_enqueue_scripts', array(&$this, 'add_js'));
				// insert dynamic classes to menus if preferenced
				if (!function_exists('add_first_and_last')) {
					if ($this->preferences['first_and_last'] != '0') {
						add_filter('wp_nav_menu', array(&$this, 'add_first_and_last'));
					}
				}
				// filter the HTML just before it's sent to the browser - no need for now.
				/*if (false) {
					add_action('get_header', array(&$this, 'tvr_head_buffer_start'));
					add_action('wp_head', array(&$this, 'tvr_head_buffer_end'));
				}*/

			} // end constructor

			// remove parent style.css and replace with microthemer reset.css
			function tvr_head_buffer_callback($buffer) {
			  // modify buffer here, and then return the updated code
			  $buffer = str_replace(get_stylesheet_uri(), $this->thispluginurl.'css/frontend/reset.css', $buffer);
			  return $buffer;
			}
			// start buffer
			function tvr_head_buffer_start() {
				ob_start(array(&$this, "tvr_head_buffer_callback"));
			}
			// end buffer
			function tvr_head_buffer_end() {
				ob_end_flush();
			}

            // add a link to the WP Toolbar
            function custom_toolbar_link($wp_admin_bar) {
                if (!empty($this->preferences['top_level_shortcut'])
                    and $this->preferences['top_level_shortcut'] == 1){
                    $parent = false;
                } else {
                    $parent = 'site-name';
                }

                $args = array(
                    'id' => 'wp-mcr-shortcut',
                    'title' => 'Microthemer',
                    'parent' => $parent,
                    'href' => $this->wp_admin_url . 'admin.php?page=' . $this->microthemeruipage,
                    'meta' => array(
                        'class' => 'wp-mcr-shortcut',
                        'title' => __('Jump to the Microthemer interface', 'tvr-microthemer')
                    )
                );
                $wp_admin_bar->add_node($args);
            }

			// determine dependent style sheets - sometimes a theme includes style.css AFTER active-styles.css (e.g. classipress)
			function dep_stylesheets() {
				/*
				// redundant, but kept as an example in case this method is ever needed
				$cur_theme = strtolower(get_current_theme());
				switch ($cur_theme) {
					case "classipress":
						$deps = array('at-main', 'at-color');
						break;
					default:
						$deps = false;
				}
				return $deps;
				*/
				return false;
			}


			// add stylesheet function
			function add_css() {
				// if it's a preview don't cache the css file
				if (is_user_logged_in()) {
					$append = '?nocache=' . $this->time;
				} else {
                    $append = '';
                }
				if ( !empty($this->preferences['active_theme']) ) {
					// register css - check theme name so relevant dependencies can be added
					$deps = $this->dep_stylesheets();

					// check if Google Fonts stylesheet needs to be called
					if (!empty($this->preferences['g_fonts_used'])) {
                        // add fonts subset url param if defined
                        if (!empty($this->preferences['gfont_subset'])) {
                            $this->preferences['g_url'] = $this->preferences['g_url'] . $this->preferences['gfont_subset'];
                        }
						wp_register_style( 'micro'.TVR_MICRO_VARIANT.'_g_font', $this->preferences['g_url'], false );
						wp_enqueue_style( 'micro'.TVR_MICRO_VARIANT.'_g_font' );
					}

                    // ensure active-styles.css exists before including to avoid 404
                    //if (file_exists($this->micro_root_dir.'active-styles.css')) {
                        wp_register_style( 'micro'.TVR_MICRO_VARIANT, $this->micro_root_url.'active-styles.css' . $append, $deps );
                        wp_enqueue_style( 'micro'.TVR_MICRO_VARIANT );
                    //}

					// check if ie-specific stylesheets need to be called
					global $is_IE;
					if ( $is_IE ) {
						global $wp_styles;
						// all IE versions
						if ($this->preferences['ie_css']['all']) {
							wp_register_style( 'tvr_ie', $this->micro_root_url.'ie-all.css'.$append);
							wp_enqueue_style( 'tvr_ie' );
						}
						// IE9 and below
						if ($this->preferences['ie_css']['nine']) {
							wp_register_style( 'tvr_ie9', $this->micro_root_url.'ie-nine.css'.$append);
							wp_enqueue_style( 'tvr_ie9' );
							$wp_styles->add_data('tvr_ie9','conditional','lte IE 9'); // method for enqueuing condionally
						}
						// IE8 and below
						if ($this->preferences['ie_css']['eight']) {
							wp_register_style( 'tvr_ie8', $this->micro_root_url.'ie-eight.css'.$append);
							wp_enqueue_style( 'tvr_ie8' );
							$wp_styles->add_data('tvr_ie8','conditional','lte IE 8'); // method for enqueuing condionally
						}
						// IE7 and below
						if ($this->preferences['ie_css']['seven']) {
							wp_register_style( 'tvr_ie7', $this->micro_root_url.'ie-seven.css'.$append);
							wp_enqueue_style( 'tvr_ie7' );
							$wp_styles->add_data('tvr_ie7','conditional','lte IE 7'); // method for enqueuing condionally
						}
					}

				}
				// only include firebug style overlay css if user is logged in
				if (is_user_logged_in() and TVR_MICRO_VARIANT == 'themer') {
					// register
					wp_register_style( 'micro'.TVR_MICRO_VARIANT.'-overlay-css', $this->thispluginurl.'js/overlay/overlay.css?v='.$this->version );
					// enqueue
					wp_enqueue_style( 'micro'.TVR_MICRO_VARIANT.'-overlay-css');
				}
			}

			// add preview css
			function add_preview_css() {
				if (is_user_logged_in()) {
					$append = '?nocache=' . $this->time;
				}
				$deps = $this->dep_stylesheets();
				wp_register_style( 'micro_theme_preview', $this->micro_root_url.intval($_GET['tvr_micro']).'.css'.$append, $deps );
				wp_enqueue_style( 'micro_theme_preview' );
			}

			// add viewport intial scale = 1
			function viewport_meta() {
				?>
                <!-- Microthemer viewport setting -->
				<meta name="viewport" content="width=device-width, initial-scale=1"/>
				<?php
			}

			// add meta iframe-url tracker (for remembering the preview page)
			function add_meta_tag() {
				if ( is_user_logged_in() ) {
					if (!function_exists('currentPageURL')) {
						function currentPageURL() {
							$curpageURL = 'http';
							if (!empty($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {$curpageURL.= "s";}
							$curpageURL.= "://";
							if ($_SERVER["SERVER_PORT"] != "80") {
							$curpageURL.= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
							} else {
							$curpageURL.= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
							}
							return $curpageURL;
						}
					}
                    ?><meta name='iframe-url' id='iframe-url' content='<?php echo rawurlencode(currentPageURL());?>' /><meta name='mt-show-admin-bar' id='mt-show-admin-bar' content='<?php echo $this->preferences['admin_bar_preview'];?>' /><?php
				}
			}

			// add firebug style overlay js if user is logged in
			function add_js() {
				if ( is_user_logged_in() and TVR_MICRO_VARIANT == 'themer') {
                    // testing only - swap default jQuery with 2.x for future proofing
                    /*
                    $jq2 = false;
                    if ($jq2){
                        wp_deregister_script('jquery');
                        wp_register_script('jquery', ($this->thispluginurl.'js/jq2.js'));
                    }*/

					// load js strings for translation
                    $js_i18n_overlay = array();
					include_once $this->thisplugindir . 'includes/js-i18n.inc.php';

					wp_enqueue_script( 'jquery' );

                    wp_register_script( 'tvr_sprintf',
                        $this->thispluginurl.'js/sprintf/sprintf.min.js?v='.$this->version, 'jquery' );
                    wp_enqueue_script( 'tvr_sprintf');

                    if (!TVR_DEV_MODE) {
                        wp_register_script( 'tvr_mcth_overlay',
                            $this->thispluginurl.'js/min/jquery.overlay.js?v='.$this->version, array('jquery') );
                    } else {
                        wp_register_script( 'tvr_mcth_overlay',
                            $this->thispluginurl.'js/jquery.overlay.js?v='.$this->version, array('jquery') );
                    }
					wp_localize_script( 'tvr_mcth_overlay', 'js_i18n_overlay', $js_i18n_overlay);
					wp_enqueue_script( 'tvr_mcth_overlay' );
				}
			}

			// add first and last classes to menus
			function add_first_and_last( $items ) {
				$position = strrpos($items, 'class="menu-item', -1);
				$items=substr_replace($items, 'menu-item-last ', $position+7, 0);
				$position = strpos($items, 'class="menu-item');
				$items=substr_replace($items, 'menu-item-first ', $position+7, 0);
				return $items;
			}

		} // end class
	} // end 'if(!class_exists)'

	// instantiate the frontend class
	if (class_exists('tvr_microthemer_frontend')) {
		$tvr_microthemer_frontend_var = new tvr_microthemer_frontend();
	}

} // end 'is_admin()'

?>
