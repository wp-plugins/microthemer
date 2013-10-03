<?php
/*
Plugin Name: Microthemer
Plugin URI: http://www.themeover.com/microthemer
Description: Microthemer is a feature-rich visual design plugin for customizing the appearance of ANY WordPress Theme or Plugin Content (e.g. contact forms) down to the smallest detail (unlike typical Theme Options). For CSS coders, Microthemer is a proficiency tool that allows them to rapidly restyle a WordPress Theme. For non-coders, Microthemer's intuitive interface and "Double-click to Edit" feature opens the door to advanced Theme customization.
Version: 2.4.4
Author: Themeover
Author URI: http://www.themeover.com
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
			'echo \'<div id="message" class="error"><p><strong>Microthemer requires that you deactivate Microloader. Please do so.</strong></p></div>\';'
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
	
			var $version = '2.4.4';
			var $minimum_wordpress = '3.2.1';
			var $users_wp_version = 0;
			var $page_prefix = '';
			var $optionsName= 'microthemer_ui_settings';
			var $preferencesName = 'preferences_themer_loader';
			var $localizationDomain = "microthemer";
			var $globalmessage = '';
			var $permissionshelp = 'Please see this help article for changing directory and file permissions: <a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a>';
			var $microthemeruipage = 'tvr-microthemer.php';
			var $microthemespage = 'tvr-manage-micro-themes.php';
			var $preferencespage = 'tvr-microthemer-preferences.php';
			var $total_selectors = 0;
			var $sel_loop_count;
			var $trial = true;
			
			// @var array $pages Stores all the plugin pages in an array
			var $all_pages = array();
			// @var array $options Stores the ui options for this plugin
			var $options = array();
			// @var array $options Stores the "to be merged" options in
			var $to_be_merged = array();
			// @var array $preferences Stores the preferences for this plugin
			var $preferences = array();
			// @var array $file_structure Stores the micro theme dir file structure
			var $file_structure = array();
			// @var array $filtered_images stores a list of user-filtered background images
			var $filtered_images = array();
			// set defualt preferences
			var $default_preferences = array(
				"admin_bar" => 1,
    			"jquery_source" => "native",
				"gzip" => 1,
				"auto_relative" => 1,
				"ie_notice" => 1,
				"auto_scroll" => 1,
				"auto_save" => 0,
				"load_visual" => 0,
				"need_help" => 1,
				"safe_mode_notice" => 1,
				"image_filter" => array(),
				"disable_parent_css" => 0,
				"css_important" => 1,
				"first_and_last" => 1,
				"trans_editing" => 0,
				"trans_wizard" => 0,
				"initial_scale" => 1
			);
			// default media queries
			var $unq_base = '';
			var $default_m_queries = array();

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
			
			
			// Class Functions
			
			/**
			* PHP 4 Compatible Constructor
			*/
			function tvr_microthemer_admin(){$this->__construct();}
			/**
			* PHP 5 Constructor
			*/        
			function __construct(){
				
				// Stop the plugin if below requirements
				// @taken from ngg gallery: http://wordpress.org/extend/plugins/nextgen-gallery/
				if ( (!$this->required_version()) or (!$this->check_memory_limit()) or defined('TVR_MICROBOTH') or !$this->trial_check() ) {
					return;
				}
				
				
				// add menu links (all WP admin pages need this)
				if (TVR_MICRO_VARIANT == 'themer') {
					add_action("admin_menu", array(&$this,"microthemer_dedicated_menu"));
				}
				else {
					add_action("admin_menu", array(&$this,"microloader_menu_link"));
				}  
				
				// populate the default media queries
				$this->unq_base = uniqid();
				$this->default_m_queries = array(
					$this->unq_base.'1' => array(
						"label" => "Large Desktop",
						"query" => "@media (min-width: 1200px)",
						"min" => 1200,
						"max" => 0),
					$this->unq_base.'2' => array(
						"label" => "Desktop & Tablet",
						"query" => "@media (min-width: 768px) and (max-width: 979px)",
						"min" => 768,
						"max" => 979),
					$this->unq_base.'3' => array(
						"label" => "Tablet & Phone",
						"query" => "@media (max-width: 767px)",
						"min" => 0,
						"max" => 767),
					$this->unq_base.'4' => array(
						"label" => "Phone",
						"query" => "@media (max-width: 480px)",
						"min" => 0,
						"max" => 480)	
				);
				
				// get the preferences here for the sake of the validator (all users get automatic updates now)
				$this->getPreferences();
				
				// if no media queries yet, assign default
				if ( !$this->preferences['user_set_mq'] and $_POST['tvr_preferences']['user_set_mq'] != 1 ) {
					$pref_array['m_queries'] = $this->default_m_queries;
					$this->savePreferences($pref_array);
				}
				
				$ext_updater_file = dirname(__FILE__) .'/includes/plugin-updates/plugin-update-checker.php';
				if ( TVR_MICRO_VARIANT == 'themer' and file_exists($ext_updater_file) ) {
					require $ext_updater_file;
					$MyUpdateChecker = new PluginUpdateChecker(
						'http://themeover.com/wp-content/tvr-auto-update/meta-info.json?'.time(), // prevent cached file from loading
						__FILE__,
						'microthemer'
					);
				} 
				
				/*** 
				limit the amount of code that runs on non-microthemer admin pages
				-- the main functions need to run for the sake of creating 
				menu links to the plugin pages, but the code contained within is conditional.
				***/
				// save all plugin pages in an array for evaluation throughout the program
				$this->all_pages = array(
					$this->microthemeruipage,
					$this->microthemespage,
					$this->preferencespage
					);
				// only initilize on plugin admin pages 
				if ( is_admin() and in_array($_GET['page'], $this->all_pages) ) {
					/* Language Setup - only english for now
					$locale = get_locale();
					$mo = dirname(__FILE__) . "/languages/" . $this->localizationDomain . "-".$locale.".mo";
					load_textdomain($this->localizationDomain, $mo);
					*/
					
					// Initialize the options
					include dirname(__FILE__) .'/get-dir-paths.inc.php';
					// only microthemer needs ui options
					if (TVR_MICRO_VARIANT == 'themer') {
						$this->getOptions();
					}
					$this->getPropertyOptions();
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
							'echo \'<div id="message" class="error"><p><strong>Sorry, Microthemer only runs on WordPress version '.$this->minimum_wordpress.' or above. Deactivate Microthemer to remove this message.</strong></p></div>\';'
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
							'echo \'<div id="message" class="error"><p><strong>Sorry, Microthemer requires a memory limit of 16MB or higher to run. Your memory limit is less than this. Deactivate Microthemer to remove this message.</strong></p></div>\';'
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
							'echo \'<div id="message" class="error"><p><strong>Please <a target="_blank" href="http://themeover.com/microthemer/">purchase Microthemer</a> to unlock the full program.</strong></p></div>\';'
						)
					);
					return false;
				}
				return true;
			}
			
			// Microthemer dedicated menu
			function microthemer_dedicated_menu() {
				add_menu_page('Microthemer UI', 'Microthemer', 'administrator', $this->microthemeruipage, array(&$this,'microthemer_ui_page'));
				add_submenu_page($this->microthemeruipage, 'Manage Micro Themes', 'Manage Themes', 'administrator', $this->microthemespage, array(&$this,'manage_micro_themes_page'));
				add_submenu_page($this->microthemeruipage, 'Microthemer Preferences', 'Preferences', 'administrator', $this->preferencespage, array(&$this,'microthemer_preferences_page'));	
			}
			
			// Add Microloader menu link in appearance menu
			function microloader_menu_link() {
				add_theme_page('Microloader', 'Microloader', 'administrator', $this->microthemespage, array(&$this,'manage_micro_themes_page'));
			}
			
			// add js
			function add_js() {
				if (TVR_MICRO_VARIANT == 'themer') {
					/* WORDPRESS WOULDN'T ALLOW THIS FEATURE
					if ($this->preferences['jquery_source'] == 'plugin') {
						global $concatenate_scripts;
						$concatenate_scripts = false;
						wp_deregister_script( 'jquery' );
						wp_register_script( 'jquery', $this->thispluginurl.'js/jquery1.8.3.js?v='.$this->version );
						wp_enqueue_script( 'jquery' );	
					}*/
					
					// register and enqueue plugin scripts
					wp_register_script( 'tvr_mcth_jscolor', $this->thispluginurl.'js/mcthmr_jscolor/mcthmr_jscolor.js?v='.$this->version );
					//wp_register_script( 'tvr_mcth_jqueryui', $this->thispluginurl.'js/jquery-ui-1.10.2.custom.min.js?v='.$this->version, 'jquery' );
					wp_register_script( 'tvr_mcth_colorbox', $this->thispluginurl.'js/colorbox/1.3.19/jquery.colorbox-min.js?v='.$this->version, 'jquery' );
					wp_register_script( 'tvr_mcth_tabs', $this->thispluginurl.'js/enable-tabs.js?v='.$this->version, 'jquery' ); 
					wp_enqueue_script( 'tvr_mcth_jscolor' );
					//wp_enqueue_script( 'tvr_mcth_jqueryui' );
					wp_enqueue_script( 'jquery-ui-core' );
					wp_enqueue_script( 'jquery-ui-sortable' );
					
					wp_enqueue_script( 'tvr_mcth_colorbox' );
					wp_enqueue_script( 'tvr_mcth_tabs' );
					// load relevant plugin page script 
					if ( $_GET['page'] == $this->microthemeruipage) {		
						wp_register_script( 'tvr_mcth_custom_ui', $this->thispluginurl.'js/min/microthemer.js?v='.$this->version ); 
						//wp_register_script( 'tvr_mcth_custom_ui', $this->thispluginurl.'js/tvr-microthemer.js?v='.$this->version ); 
						wp_enqueue_script( 'tvr_mcth_custom_ui' );	
					}
					// manage micro themes script
					else {
						wp_register_script( 'tvr_mcth_custom_man', $this->thispluginurl.'js/min/manage-micro.js?v='.$this->version ); 
						//wp_register_script( 'tvr_mcth_custom_man', $this->thispluginurl.'js/tvr-manage-micro.js?v='.$this->version ); 
						wp_enqueue_script( 'tvr_mcth_custom_man' );	
					}	
				}
			}
					
			// add css
			function add_css() {
				// register
				wp_register_style( 'tvr_mcth_styles', $this->thispluginurl.'css/styles.css?v='.$this->version ); 
				// enqueue
				wp_enqueue_style( 'tvr_mcth_styles' );
				if (TVR_MICRO_VARIANT == 'themer') {
					wp_register_style( 'tvr_mcth_colorbox_styles', $this->thispluginurl.'js/colorbox/1.3.19/colorbox.css?v='.$this->version ); 
					wp_enqueue_style( 'tvr_mcth_colorbox_styles' );
				}
				// add IE only stylesheet if WordPress detects IE
				global $is_IE;
				global $wp_styles;
				if ( $is_IE ) {
					// all IE versions
					wp_register_style( 'tvr_ie', $this->thispluginurl.'css/ie.css?v='.$this->version);
					wp_enqueue_style( 'tvr_ie' );
					// IE8 only
					wp_register_style( 'tvr_ie8', $this->thispluginurl.'css/ie8.css?v='.$this->version);
					wp_enqueue_style( 'tvr_ie8' );
					$wp_styles->add_data('tvr_ie8','conditional','(lte IE 8)'); // method for enqueuing condionally
					// IE7 only
					wp_register_style( 'tvr_ie7', $this->thispluginurl.'css/ie7.css?v='.$this->version);
					wp_enqueue_style( 'tvr_ie7' );
					$wp_styles->add_data('tvr_ie7','conditional','(lte IE 7)'); // method for enqueuing condionally
				}
			}
			
			// build array for property/value input fields
			function getPropertyOptions() {
				include $this->thisplugindir . 'includes/property-options.inc.php';
				$this->propertyoptions = $propertyOptions;
			}
			
			// @return array - Retrieve the plugin options from the database.
			function getOptions() {
				// default options (html layout sections only - no default selectors)
				if (!$theOptions = get_option($this->optionsName)) {
					$theOptions = array(); 
					if ($this->preferences['buyer_validated']) {
						$theOptions['main_body'] = '';			
						$theOptions['header'] = '';
						$theOptions['main_menu'] = '';
						$theOptions['content'] = '';
						$theOptions['pages'] = '';
						$theOptions['posts'] = '';
						$theOptions['post_single_view'] = '';
						$theOptions['post_navigation'] = '';
						$theOptions['comments'] = '';
						$theOptions['leave_reply_form'] = '';
						$theOptions['main_widget_areas'] = '';
						$theOptions['search_site'] = '';
						$theOptions['footer'] = '';
						$theOptions['footer_widget_areas'] = '';
						$theOptions['contact_form_7'] = '';
						$theOptions['non_section']['hand_coded_css'] = '';
						// register view states (otherwise first selector doesn't load iframe highlighting properly)
						$theOptions['non_section']['view_state']['main_body']['this'] = '0';
						$theOptions['non_section']['view_state']['header']['this'] = '0';
						$theOptions['non_section']['view_state']['main_menu']['this'] = '0';
						$theOptions['non_section']['view_state']['content']['this'] = '0';
						$theOptions['non_section']['view_state']['pages']['this'] = '0';
						$theOptions['non_section']['view_state']['posts']['this'] = '0';
						$theOptions['non_section']['view_state']['post_single_view']['this'] = '0';
						$theOptions['non_section']['view_state']['post_navigation']['this'] = '0';
						$theOptions['non_section']['view_state']['comments']['this'] = '0';
						$theOptions['non_section']['view_state']['leave_reply_form']['this'] = '0';
						$theOptions['non_section']['view_state']['main_widget_areas']['this'] = '0';
						$theOptions['non_section']['view_state']['search_site']['this'] = '0';
						$theOptions['non_section']['view_state']['footer']['this'] = '0';
						$theOptions['non_section']['view_state']['footer_widget_areas']['this'] = '0';
						$theOptions['non_section']['view_state']['contact_form_7']['this'] = '0';
					} else {
						$theOptions['free_trial_example_section'] = '';
						$theOptions['non_section']['hand_coded_css'] = '';
						$theOptions['non_section']['view_state']['free_trial_example_section']['this'] = '0';
					}
					
					// add_option rather than update_option (so autoload can be set to no)
					add_option($this->optionsName, $theOptions, '', 'no');
				}
				$this->options = $theOptions;  
			}
			
			// @return array - Retrieve the plugin plugin preferences from the database.
			function getPreferences() {
				// default preferences
				if (!$thePreferences = get_option($this->preferencesName)) {
					$thePreferences = $this->default_preferences;
					$thePreferences['buyer_email'] = '';
					$thePreferences['buyer_validated'] = false;
					$thePreferences['active_theme'] = 'customised';
					$thePreferences['theme_in_focus'] = '';
					$thePreferences['preview_url'] = site_url();
					$thePreferences['user_set_mq'] = false;
					// add_option rather than update_option (so autoload can be set to no)
					add_option($this->preferencesName, $thePreferences, '', 'no');
				}
				$this->preferences = $thePreferences;  
			}
			
			// Save the preferences
			function savePreferences($pref_array) { 
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
					$this->globalmessage.= '<p>Revisions Table Created.</p>';
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
				if ($wpdb->num_rows > 20) {
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
					return '<span id="revisions-table">No Revisions have been created yet. This will happen after your next save.</span>'; 
				}
				// if one revision, it's the same as the current settings, explain
				if ($total_rows == 1) {
					return '<span id="revisions-table">The only revision is a copy of your current settings.</span>'; 
				}
				// revisions exist so prepare table
				$restore_url = 'admin.php?page=' . $this->microthemeruipage . '&_wpnonce='.
				wp_create_nonce('tvr_restore_revision').'&action=restore_rev&tvr_rev=';
				$rev_table = 
				'
				<table id="revisions-table" class="widefat">
            	<thead>
				<tr>
					<th>Revision</th>
                    <th>Date & Time</th>
                    <th>User Action</th>
					<th>Size</th>
                    <th>Restore</th>
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
							$rev_table.='<a class="restore-link" href="'.$restore_url.$rev->id.'">Restore</a>';
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
				if ($this->debug_save) {
					$debug_file = $this->micro_root_dir . $this->preferences['theme_in_focus'] . '/debug-save.txt';
					$write_file = fopen($debug_file, 'w'); 
					$data = '';
					$data.= "\n\nThe new options\n\n";;
					$data.= print_r($theOptions, true);
					$data.= "\n\nThe existing options\n\n";;
					$data.= print_r($this->options, true);
				}
				// loop through all the state trackers
				if (is_array($theOptions['non_section']['view_state'])) {
					foreach($theOptions['non_section']['view_state'] as $section_name => $array) {
						// loop through the selector trackers
						if (is_array($array)) {
							foreach ( $array as $css_selector => $view_state) { 
								// check if section selectors were loaded
								if ($css_selector == 'this' and $view_state == 0) { // section
									$theOptions[$section_name] = $this->options[$section_name];
								}
								if ($css_selector != 'this' and $view_state == 0) { // selector
									$theOptions[$section_name][$css_selector] = $this->options[$section_name][$css_selector];
								}
							}
						}
					}
				}
				if ($this->debug_save) {
					$data.= "\n\nThe hybrid options\n\n";;
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
								$option_groups = array(
								'font' => '',
								'text' => '',
								'forecolor' => '',
								'background' => '',
								'dimensions' => '',
								'padding' => '',
								'margin' => '',
								'border' => '',
								'behaviour' => '',
								'position' => '',
								'CSS3' => '');
								$this->options[$section_name][$css_selector]['styles'] = $option_groups;
								// set style_config to empty array
								$this->options[$section_name][$css_selector]['style_config'] = $option_groups;
								// set pie to 1 (default is on)
								$this->options[$section_name][$css_selector]['pie'] = 1;
								// don't bother adjusting view states (auto closing sec/sels) - unless it seems more natural...
							}
						}
					}
				}
				// update the options in the DB
				update_option($this->optionsName, $this->options);
				$this->options = get_option($this->optionsName); // necessary?
				return true;
			}
					
			// Microthemer UI page
			function microthemer_ui_page() { 
				// only run code if it's the ui page
				if ( $_GET['page'] == $this->microthemeruipage ) {
					
					// if it's a save request
					if($_POST['action'] == 'tvr_microthemer_ui_save') {
						
						$user_action = 'Save';
						
						// 2 forms are submitted (invalid <form> markeup with 1 form was causing a bug in ie8)
						// the 2nd form triggers this $_POST['action'] condition, so that's the one we verify
						// temporarily disable this wp_nonce check when troubleshooting an error report
						check_admin_referer('tvr_microthemer_ui_save2'); 
						// save settings in DB
						if ($this->saveUiOptions($_POST['tvr_mcth'])) {
							$this->globalmessage.= '<p>Settings Saved.</p>';
						}
						else {
							$this->globalmessage.= '<p>Settings NOT Saved.</p>';
						}
						// check if settings need to be exported to a micro theme
						if (isset($_POST['export_to'])) {
							$theme = htmlentities($_POST['export_to_theme']);
							if ($theme == 'new') {
								$context = 'new';
								if ( !empty($_POST['export_new_name']) ) {
									$theme = htmlentities($_POST['export_new_name']);
									$do_option_insert = true;
								}
							}
							// function return sanitised theme name
							$theme = $this->update_json_file($theme, $context);
							// save new sanitised theme in span for updating select menu via jQuery
							if ($do_option_insert) {
								$new_select_option = 'yes|'.$theme .'|'.htmlentities($_POST['export_new_name']);
							}
							else {
								$new_select_option = 'no';
							}
							$user_action.= ' & Export to <i>'. $this->readable_name($theme). '</i>';
						}
						// else its a standard save of custom settings
						else {
							$theme = 'customised';
							$user_action.= ' (regular)';
						}
						// update active-styles.css
						$this->update_active_styles($theme);
						// update the revisions DB field
						if (!$this->updateRevisions($this->options, $user_action)) {
							$this->globalmessage.= '<p>Revision log could not be updated.</p>';
						}
						// return the globalmessage and then kill the program - this action is always requested via ajax
						echo '<div id="microthemer-notice">' . $this->globalmessage . '
						<span id="sanit-export-name">'.$new_select_option.'</span></div>'; 
						die();	
					}
					
					// ajax - load selectors and/or selector options
					if ($_GET['action'] == 'tvr_microthemer_ui_load_styles') {
						check_admin_referer('tvr_microthemer_ui_load_styles');
						// call dir_loop to populate global class properties (for bg image list)
						$file_structure = $this->dir_loop($this->micro_root_dir);
						$section_name = strip_tags($_GET['tvr_load_section']);
						$css_selector = strip_tags($_GET['tvr_load_selector']);
						$force_load = strip_tags($_GET['tvr_force_selectors']);
						$tvr_load_type = strip_tags($_GET['tvr_load_type']);
						echo '<div id="tmp-wrap">';
						if ($tvr_load_type == 'section') {
							// get the section array to pass to the function
							$array = $this->options[$section_name];
							$this->section_selectors($section_name, $array, $force_load);
						}
						elseif ($tvr_load_type == 'selector') {
							// get the selector array to pass to the function
							$array = $this->options[$section_name][$css_selector];
							$this->selector_option_groups($section_name, $css_selector, $array);
						}
						echo '</div>';
						// kill the program - this action is always requested via ajax. no message necessary
						die();
					}
					
					// ajax - update preview url
					if (isset($_GET['tvr_microthemer_ui_preview_url'])) {
						check_admin_referer('tvr_microthemer_ui_preview_url');
						$preview_url = strip_tags($_GET['tvr_microthemer_ui_preview_url']);
						$pref_array = array();
						$pref_array['preview_url'] = $preview_url;
						$this->savePreferences($pref_array);
						// kill the program - this action is always requested via ajax. no message necessary
						die();
					}
					
					// if it's an import request
					if ( isset($_POST['tvr_import_from_theme']) and !empty($_POST['tvr_import_from_theme']) ) {
						check_admin_referer('tvr_import_from_theme');
						$theme_name = htmlentities($_POST['tvr_import_from_theme']);
						$json_file = $this->micro_root_dir . $theme_name . '/config.json';
						$context = $_POST['tvr_import_method'];
						$this->load_json_file($json_file, $theme_name, $context);
						// update the revisions DB field
						$user_action = ' Import ('.$context.') from <i>'. $this->readable_name($theme_name). '</i>';
						if (!$this->updateRevisions($this->options, $user_action)) {
							$this->globalmessage.= '<p>Revision log could not be updated.</p>';
						}
					}
					
					// if it's a reset request
					elseif($_GET['action'] == 'tvr_ui_reset'){
						check_admin_referer('tvr_microthemer_ui_reset');                                          
						if ($this->resetUiOptions()) {
							$this->update_active_styles('customised');
							$this->globalmessage.= '<p>Settings were reset.</p>';
							// update the revisions DB field
							if (!$this->updateRevisions($this->options, 'Settings Reset')) {
								$this->globalmessage.= '<p>Revision log could not be updated.</p>';
							}
						}
						
					}
					
					// if it's a clear styles request
					elseif($_GET['action'] == 'tvr_clear_styles'){
						check_admin_referer('tvr_microthemer_clear_styles');                                    
						if ($this->clearUiOptions()) {
							$this->update_active_styles('customised');
							$this->globalmessage.= '<p>Styles were cleared.</p>';
							// update the revisions DB field
							if (!$this->updateRevisions($this->options, 'All Styles Cleared')) {
								$this->globalmessage.= '<p>Revision log could not be updated.</p>';
							}
						}
					}
					
					// if it's an email error report request
					elseif($_GET['action'] == 'tvr_error_email'){
						check_admin_referer('tvr_microthemer_ui_load_styles');                                 
						$body = "*** MICROTHEMER ERROR REPORT | ".date('d/m/Y h:i:s a', time())." *** \n\n";
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
						$subject = 'Microthemer Error Report | ' . date('d/m/Y', time());
						$to = 'support@themeover.com';
						$from = "Microthemer User <$from_email>";
						$headers = "From: $from";
						if(@mail($to,$subject,$body,$headers)) {	
							echo '<div id="microthemer-notice"><p><b>Email Sent.</b> Thanks! <br />(this really does help)</p></div>';
							die();	
						}
						else {
							$error_url = $this->thispluginurl . $file_path;
							echo '<div id="microthemer-notice"><p><b>Email Failed.</b><br /> (are you on localhost?)</p>
							<p>Please email <a target="_blank" href="'.$error_url.'">this report</a> to 
							<a href="mailto:support@themeover.com">support@themeover.com</a></p></div>';
							die();	
						}
					}
					
					// if it's a restore revision request
					if($_GET['action'] == 'restore_rev'){
						check_admin_referer('tvr_restore_revision'); 
						$rev_key = $_GET['tvr_rev'];
						if ($this->restoreRevision($rev_key)) {
							$this->globalmessage.= '<p>Previous Save data successfully restored.</p>';
							$this->update_active_styles('customised');
							// update the revisions DB field
							if (!$this->updateRevisions($this->options, 'Previous Settings Restored')) {
								$this->globalmessage.= '<p>Revision log could not be updated.</p>';
							}
						}
						else {
							$this->globalmessage.= '<p>Error: Save data could not be restored.</p>';
						}
						
					}
					
					// if it's a get revision ajax request
					elseif($_GET['action'] == 'get_revisions'){
						check_admin_referer('tvr_get_revisions'); // general ui nonce
						echo '<div id="tmp-wrap">' . $this->getRevisions() . '</div>'; // outputs table
						die();
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
					
					// create new micro theme
					if (isset($_POST['tvr_create_micro_submit'])) {
						check_admin_referer('tvr_create_micro_submit');
						$micro_name = esc_attr( $_POST['micro_name']);
						if ( !empty($micro_name) ) {
							$this->create_micro_theme($micro_name, 'create', '');
						}
						else {
							$this->globalmessage.= '<p>You didn\'t enter anything in the "Name" field. Please try again.</p>';
						}
					}
					
					// handle zip upload
					if (isset($_POST['tvr_upload_micro_submit'])) {
						check_admin_referer('tvr_upload_micro_submit');
						if ($_FILES['upload_micro']['error'] == 0) {
							$this->handle_zip_package();
						}
						else {
							$this->globalmessage.= '<p>Upload failed!</p>';
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
					if ($_GET['action'] == 'tvr_activate_micro_theme') {
						check_admin_referer('tvr_activate_micro_theme');
						$theme_name = $this->preferences['theme_in_focus'];
						$json_file = $this->micro_root_dir . $theme_name . '/config.json';
						$this->load_json_file($json_file, $theme_name);
						// update the revisions DB field
						$user_action = '<i>' . $this->readable_name($theme_name) . '</i> Activated';
						if (!$this->updateRevisions($this->options, $user_action)) {
							$this->globalmessage.= '<p>Revision log could not be updated.</p>';
						}
					}
					// deactivate theme
					if ($_GET['action'] == 'tvr_deactivate_micro_theme') {
						check_admin_referer('tvr_deactivate_micro_theme');
						$pref_array = array();
						$pref_array['active_theme'] = '';
						if ($this->savePreferences($pref_array)) {
							$this->globalmessage.= '<p><i>'.$this->readable_name($this->preferences['theme_in_focus']).'</i> was deactivated.</p>';
						}
					}
					
					// delete a theme
					if ($_GET['action'] == 'tvr_delete_micro_theme') {
						check_admin_referer('tvr_delete_micro_theme');
						$this->tvr_delete_micro_theme();
					}
					
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
					if ($_GET['action'] == 'tvr_delete_micro_file') {
						check_admin_referer('tvr_delete_micro_file');
						if (unlink($this->micro_root_dir . $this->preferences['theme_in_focus'] . '/'. $_GET['file'])) {
							$this->globalmessage.= '<p>'.htmlentities($_GET['file']).' was successfully deleted.</p>';
						}
					}
					
					// handle zip export
					if ($_GET['action'] == 'tvr_export_zip_package') {
						check_admin_referer('tvr_export_zip_package');
						$this->create_zip($this->micro_root_dir, $this->preferences['theme_in_focus'], $this->thisplugindir.'zip-exports/');
					}
					
					// handle ext img copy/correction
					if ($_GET['action'] == 'tvr_copy_micro_images') {
						check_admin_referer('tvr_copy_micro_images');
						$json_file = $this->micro_root_dir . $this->preferences['theme_in_focus'] . '/config.json';
						$this->check_image_paths($json_file, $this->preferences['theme_in_focus'], 'correct');
					}
					
					// include manage micro interface (both loader and themer plugins need this)
					include $this->thisplugindir . 'includes/tvr-manage-micro-themes.php';
				}
			}
			
			// Preferences page
			function microthemer_preferences_page() {
				// only run code on preferences page 
				if( $_GET['page'] == $this->preferencespage ) {
					
					// update preferences 
					if (isset($_POST['tvr_preferences_submit'])) {
						check_admin_referer('tvr_preferences_submit');
						$pref_array = $_POST['tvr_preferences'];
						// reset default media queries if all empty
						if (empty($_POST['tvr_preferences']['m_queries'])) {
							$pref_array['m_queries'] = $this->default_m_queries;
							$this->globalmessage.= '<p>Default media queries were reset.</p>';
						} else {
							// check the media query min/max values
							foreach($pref_array['m_queries'] as $key => $mq_array) {
								$pref_array['m_queries'][$key]['min'] = $this->get_screen_size($mq_array['query'], 'min');
								$pref_array['m_queries'][$key]['max'] = $this->get_screen_size($mq_array['query'], 'max');
							}
						}
						if ($this->savePreferences($pref_array)) {
							$this->globalmessage.= '<p>Preferences saved.</p>';
						}
						// if the active_theme has been changed, invoke the relevant functions - redundant code
						if (!empty($_POST['tvr_preferences']['active_theme'])
						and $_POST['tvr_preferences']['active_theme'] != $_POST['prev_active_theme']) {
							// if custom, just update active styles
							if ($_POST['tvr_preferences']['active_theme'] == 'customised') {
								$this->update_active_styles('customised');
							}
							// if from theme, load settings from json (which also updates active styles)
							else {
								$theme_name = htmlentities($_POST['tvr_preferences']['active_theme']);
								$json_file = $this->micro_root_dir . $theme_name . '/config.json';
								$this->load_json_file($json_file, $theme_name);
							}
						}
					}
					
					// validate email
					if (isset($_POST['tvr_validate_submit'])) {	
						$params = 'email='.$_POST['tvr_preferences']['buyer_email'].'&domain='.home_url();
						$validation = wp_remote_fopen('http://themeover.com/wp-content/tvr-auto-update/validate.php?'.$params);
						if ($validation) {
							$_POST['tvr_preferences']['buyer_validated'] = 1;
							$this->trial = 0;
							if (!$this->preferences['buyer_validated']) { // not already validated
								$this->globalmessage.= '<p><b>Your email address has been successfully validated.</b> 
								Microthemer\'s full program features have been unlocked!</p>
								<p><b>Tip</b>: from now on, if you reset the Microthemer UI the default (and more useful) Sections will load 
								instead of the "Free Trial Example Section".</p>';
							} else {
								$this->globalmessage.= '<p>Your email address has already been validated. The full program is active.</p>';
							}
						}
						else {
							$_POST['tvr_preferences']['buyer_validated'] = 0;
							$this->trial = true;
							$this->globalmessage.= '<p>Your email address could not be validated.</p>';
						}
						$pref_array = $_POST['tvr_preferences'];
						if (!$this->savePreferences($pref_array)) {
							$this->globalmessage.= '<p>Your validation status could not be saved</p>';
						}
					}
					
					// reset default preferences
					if (isset($_POST['tvr_preferences_reset'])) {
						check_admin_referer('tvr_preferences_reset');
						$pref_array = $this->default_preferences;
						$pref_array['m_queries'] = $this->default_m_queries;
						if ($this->savePreferences($pref_array)) {
							$this->globalmessage.= '<p>The default preferences were reset.</p>';
						}
					}
					
					// include preferences interface (only microthemer)
					if (TVR_MICRO_VARIANT == 'themer') {
						include $this->thisplugindir . 'includes/tvr-microthemer-preferences.php';
					}
					
				}
			}
			
			/* add run if admin page condition...? */
			
			/*** 
			Generic Functions 
			***/
			
			// get min/max media query screen size
			function get_screen_size($q, $minmax) {
				$pattern = "/$minmax-width:\s*([0-9]+)\s*px/";
				if (preg_match($pattern, $q, $matches)) {
					return $matches[1];
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
                <a id="tvr-item-1" href='admin.php?page=<?php echo $this->microthemeruipage;?>' title="Go to Microthemer UI Page">UI</a>
                <a id="tvr-item-2" href='admin.php?page=<?php echo $this->microthemespage;?>' title="Go to Manage Micro Themes Page">Manage</a> 
                <a id="tvr-item-3" href='admin.php?page=<?php echo $this->preferencespage;?>' title="Go to Microthemer Preferences Page">Options</a>
            	</div>
                </div>
                <?php
			}
			
			// show need help videos
			function need_help_notice() {
				if ($this->preferences['need_help'] == '1' and TVR_MICRO_VARIANT != 'loader') {
					?>
					<p class='need-help'><b>Need Help?</b> Browse Our <span class="help-trigger" 
                    rel="<?php echo $this->thispluginurl.'includes/help-videos.php'; ?>">Video Guides</span> and 
                    <span class="help-trigger" rel="<?php echo $this->thispluginurl.'includes/tutorials.php'; ?>">Tutorials</span> 
                    or <span class="help-trigger" rel="<?php echo $this->thispluginurl.'includes/search-forum.php'; ?>">Search Our Forum</span></p>
				<?php
				}
			}
			
			// ie notice
			function ie_notice() {
				// display ie message unless dissabled
				if ($this->preferences['ie_notice'] == '1') {
					?>
					<div id='ie-browser'><p><b>Notice:</b> Microthemer has been designed for modern browsers. You are using Internet Explorer. Even the latest version of Internet Explorer is slow and clunky when faced with demanding applications like this one. For a noticeably faster experience, please download the lastest version of <a target='_blank' title="Download The World's Best Internet Browser" 
					href='http://www.mozilla.com/en-US/firefox/new/'>Firefox</a> or <a href='http://www.google.com/chrome/intl/en-GB/landing_tv.html'>Google Chrome</a>. <b>Note</b>: Web browsers do not conflict with each other, you can install as many as you want on your computer at any one time.</p></div>
				<?php
				}
			}
			
			// tell them to get validated
			function validate_reminder() {
				if (!$this->preferences['buyer_validated'] and TVR_MICRO_VARIANT == 'themer') {
					?>
					<div id='validate-reminder' class="error">
                    	<p><b>IMPORTANT - Free Trial Mode is Active</b><br /> <br /> 
                        Please <a href="admin.php?page=tvr-microthemer-preferences.php#validate">validate your purchase to unlock the full program</a>.
                        <br /> 
                        The Free Trial limits you to editing or creating 3 Sections and 9 Selectors (3 per Section).</p>
                        <p>Purchase a <a target="_blank" href="http://themeover.com/microthemer/">Standard</a> ($45) or 
                       <a target="_blank" href="http://themeover.com/microthemer/">Developer</a> ($90) License Now!</p>
                       <p><b>This Plugin is Supported!</b> Themeover provides the <a target="_blank" href="http://themeover.com/forum/">best forum support</a> you'll get any where (and it's free of course)</p>
                        
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
			
			// get micro-theme dir file structure
			function dir_loop($dir_name) {
				// check for micro-themes folder, create if doesn't already exist
				if ( !is_dir($dir_name) ) {
					if ( !wp_mkdir_p($dir_name) ) {
						$this->globalmessage.= '<p>WordPress was not able to create 
						the ' . $this->root_rel($dir_name) . ' directory. ' . $this->permissionshelp . '</p>';
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
									  $this->file_structure[$just_dir][$file] = $file; 
									  // save file and dir in filtered_images array if the criteria is met
									  $array_key = array_search($just_dir, $this->preferences['image_filter']);
									  if ( $this->is_image($file) and (!$array_key and $array_key !== 0) and !$this->is_screenshot($file) ) {
									  	$this->filtered_images[$just_dir][$file] = $file;
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
			function root_rel($path, $markup = true) {
				if ($markup == true) {
					$rel_path = '<b><i>/' . str_replace(ABSPATH, '', $path) . '</i></b>';
				}
				else {
					$rel_path = str_replace(ABSPATH, '', $path);
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
			
			// Resolve property/value input fields
			function resolve_input_fields($section_name, $css_selector, $property_group_array, $property_group_name, $property, $value, 
			$con = 'reg', $key=1) {
				include $this->thisplugindir . 'includes/resolve-input-fields.inc.php';	
			}

			// Add options for adding section
			function add_new_section() {
				?>
				<div class='new-section'>
					<label>Section Name:</label> 
					<input type='text' class='new-section-input' name='new_section[name]' value='' />
					<input type='button' class='new-section-add prominent-action' name='new_selector[add]' value='Add' />
				</div>
				<?php
			}
			
			// manage section
			function manage_section($section_name, $array, $view_state = 1) {
				?>
				 <div class='manage-x'>
							<div class='view-selectors'>
								<a href='#' class='edit-selectors'><?php
								// determine approriate text for view state 
								if ($view_state == 1 or $view_state == 0) {
									echo 'Edit Selectors';
								}
								else {
									echo 'Hide Selectors';
								}
								?></a> 
								<?php 
								// determine number of selectors
								$selector_count_state = $this->selector_count_state($array); 
								if ($selector_count_state != 0) {
									echo '<span class="count-wrap">(<span class="state-count">'.$selector_count_state.'</span>)</span>';
								}
								// update global $total_selectors count (for internal use only)
								$this->total_selectors = $this->total_selectors + $selector_count_state;
								?>
								<span class='selector-count-state' rel='<?php echo $selector_count_state; ?>'></span>
							</div>
							<div class='edit-section'>
								<a href='#' class='rename-section'>Rename Section</a> | 
								<a href='#' class='delete-section' rel='<?php echo $section_name;?>'>Delete</a>
								<div class='rename'>
									<label>Section Name:</label> 
									<input type='text' class='rename-input' name='rename_section[<?php echo $section_name; ?>]' value='<?php echo ucwords(str_replace('_', ' ', $section_name));  ?>' />
									<input type='button' class='rename-button' name='rename_button[<?php echo $section_name; ?>]'  id='<?php echo $section_name; ?>-rename' value='Rename' />
								</div>
							</div>
						</div>
						<?php
			}
			
			function section_selectors($section_name, $array, $force_load = 0) {
				?>
                <div class="add-sel-wrap">
                    <a href='#' class='reveal-add-selector prominent-action'>Add New Selector</a>
                    <?php $this->add_new_selectors($section_name); ?>
                </div>
				<div class='selector-fields'>
				<?php
				// store intial array before overwriting it in foreach
				$selector_array = $array;
				// loop the CSS selectors if they exist
				if ( is_array($array) ) {
					$this->sel_loop_count = 0; // reset
					foreach ( $array as $css_selector => $array) {
						++$this->sel_loop_count;
						$css_selector = esc_attr($css_selector); //=esc
						$label_array = explode('|', $array['label']); 
						$label_array[0] = esc_attr($label_array[0]); //=esc
						// disable sections locked by trial
						if (!$this->preferences['buyer_validated'] and $this->sel_loop_count > 3) {
							$trial_disabled = 'trial-disabled';	
						} else {
							$trial_disabled = '';	
						}
						?>
						<div id='selector-<?php echo $section_name.'-'.$css_selector; ?>' class='selector-wrap <?php echo $trial_disabled; ?>'>
						<?php
						// convert my custom quote escaping in recognised html encoded single/double quotes
						$selector_title = esc_attr(str_replace('cus-', '&', $label_array[1])); //=esc
						?>
						<h3 class='sub-options-heading' title="<?php echo $selector_title; ?>"><?php 
						echo ucwords(str_replace('_', ' ', $label_array[0])); 
						?></h3>
						<input type='hidden' class='register-selector' name='tvr_mcth[<?php echo $section_name; ?>][<?php echo $css_selector; ?>]' value='' />
						<?php
						// check view state for styles
						$view_state = $this->options['non_section']['view_state'][$section_name][$css_selector];
						// output selector management options
						$this->manage_selector($section_name, $css_selector, $label_array, $array, $view_state); 
						?>
						<div class='hidden-options clearfix view-state-<?php echo $view_state;?>'>
						<?php
						// only load style options if left open on last save (or need to force load for section rename)
						if ($view_state == 2 or $force_load == 1) {
							$this->selector_option_groups($section_name, $css_selector, $array);
						}
						echo '
						</div><!-- end (h3) hidden-options -->
						</div><!-- end selector-wrap -->';
					} // ends ' foreach for css selectors' 
				} // ends 'if css selector array'
				echo '
				</div><!-- ends selector-fields  -->';
				$selector_count_state = $this->selector_count_state($selector_array); 
				if ($selector_count_state > 1) {
				?>
				<a href='#' rel='<?php echo $section_name;?>' 
				class='selectors-sortable prominent-action'>Enable Sortable Selectors</a>
				<?php
				}
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
			
			// Add options for adding new CSS selectors
			function add_new_selectors($section_name) {
				?>
				<div class='new-css-selectors'>
                	<div class='new-css-selector-wrap'>
					<label>CSS Selector: &nbsp;</label>
                    <textarea class='selector-css' name='new_selector[<?php echo $section_name; ?>][css]'></textarea>
                    </div>
                    <div class='new-css-label-wrap'>
					<label>Label: &nbsp;</label><input type='text' class='selector-label' name='new_selector[<?php echo $section_name; ?>][label]' value='' />
					<input type='button' id='<?php echo $section_name; ?>-add' class='add-selector prominent-action' name='new_selector[<?php echo $section_name; ?>][add]' value='Add' title="Add Selector to current Section" />
                    </div>
				</div>
				<?php
			}
			
			// manage selectors
			function manage_selector($section_name, $css_selector, $label_array, $array, $view_state = 1) {
				?>
				<div class='manage-x'>
                    <div class='view-styles'>
                        <?php 
                        // determine which style groups are active
                        $style_count_state = 0;
                        if ( !empty($array['style_config']) and is_array($array['style_config']) ) {
                            foreach ( $array['style_config'] as $key => $value) {
                                if ($key == $value) {
                                    ++$style_count_state;
                                }
                            }
                        }
                        ?>
                        <a href='#' class='edit-styles'><?php
						// determine approriate text for view state 
						if ($view_state == 0 or $view_state == 1) {
							echo 'Edit Styles';
						}
						else {
							echo 'Hide Styles';
						}
						?></a> 
                        <?php 
                        if ($style_count_state != 0) {
                            echo '<span class="count-wrap">(<span class="state-count">'.$style_count_state.'</span>)</span>';
                        }
                        ?>
                        <span class='style-count-state' rel='<?php echo $style_count_state; ?>'></span>
                    </div>
                    <div class='edit-selector'>
                        <a href='#' class='modify-selector'>Modify Selector</a> | 
                        <a href='#' class='delete-selector' rel='<?php echo $section_name.'|'.$css_selector;?>'>Delete</a>
                    </div>
                    <div class='modify'>
                            <?php
                            $selector_name_human = $label_array[0];
                            $selector_name_human = esc_attr($selector_name_human); //=esc
							// convert my custom quote escaping in recognised html encoded single/double quotes
							$selector_css = esc_attr(str_replace('cus-', '&', $label_array[1])); //=esc
                            ?>
                            <div class='css-selector-wrap'>
                            <label>CSS Selector: &nbsp;</label> 
                            <?php
							// help author meet WordPress CSS selector coding standards
							// $selector_css = str_replace(", ", ",\n", $selector_css);
							?>
                            <textarea class='modify-input-css' name='modify_selector[css]' 
                            title="<?php echo $selector_css; ?>"><?php echo $selector_css; ?></textarea>
                            </div>
                            <div class='css-label-wrap'>
                            <label>Label:  &nbsp;</label> 
                            <input type='text' class='modify-input-label' name='modify_selector[label]' 
                            value='<?php echo $selector_name_human; ?>' />
                            <input type='button' class='modify-button prominent-action' name='anyname'  id='<?php echo $section_name; ?>-modify-<?php echo $css_selector; ?>' value='Update' />
                            </div>
                    </div>
                </div>
                <?php
			}
			
			// display selector styling options
			function selector_option_groups($section_name, $css_selector, $array) {
				 ?>
                 <input type='hidden' class='selector-label' name='tvr_mcth[<?php echo $section_name; ?>][<?php echo $css_selector; ?>][label]' value='<?php echo $array['label']; ?>' />
                    <?php
                    // loop through the groups of properties for the selector
                    if ( is_array( $array['styles'] ) ) {
                        foreach ( $array['styles'] as $property_group_name => $property_group_array ) { 
                        $property_group_name = esc_attr($property_group_name); //=esc
                        ?>
                        <div class="row clearfix">
                            <?php
                            // check if the properties group is checked
                            if ($array['style_config'][$property_group_name] == $property_group_name) {
                                $checked = 'checked="checked"';
                                $property_group_state = 'on';
								$show_class = 'show';
                            }
                            else {
                                $checked='';
                                $property_group_state = 'off';
								$show_class = '';
                            }
							$main_label_show_class = $show_class;
                            ?>
                            <div class='main-label'>
                            <input type='hidden' class='register-group' name='tvr_mcth[<?php echo $section_name; ?>][<?php echo $css_selector; ?>][styles][<?php echo $property_group_name; ?>]' value='' />
                            <input type='checkbox' class='style-config checkbox main-checkbox' name='tvr_mcth[<?php echo $section_name; ?>][<?php echo $css_selector; ?>][style_config][<?php echo $property_group_name; ?>]' value='<?php echo $property_group_name; ?>' <?php echo $checked; ?> autocomplete="off" 
                             /> <?php 
							if ($property_group_name != 'forecolor') {
								 echo ucwords($property_group_name); 
							}
							else {
								echo 'Color';
							}
							?>:
                            <span class='property-group-state' rel='<?php echo $property_group_state; ?>'></span>
                            
                            <?php 
							 
							// if CSS3 - include option for turning pie off (on by default)
							if ($property_group_name == 'CSS3') {
								?>
								<div class='pie-switch<?php if ($property_group_state == 'off') { echo ' hide'; } ?>'>
								<?php echo '<a class="snippet" title="click for info" 
								href="'.$this->thispluginurl.'includes/css-reference.php?snippet=pie&prop_group=pie">CSS3 PIE:</a>'; ?>
								<input type='radio' autocomplete="off" class='radio' 
                                name="tvr_mcth[<?php echo $section_name; ?>][<?php echo $css_selector; ?>][pie]" 
                                value='1' 
                                <?php
								// check if pie swtich is on (default to on)
								if ($array['pie'] == '1' or !isset($array['pie'])) {
									echo 'checked="checked"';
								}
								?>
                                 /> On<br />
								
                                <input type='radio' autocomplete="off" class='radio' 
                                name="tvr_mcth[<?php echo $section_name; ?>][<?php echo $css_selector; ?>][pie]" 
                                value='0' 
                                <?php
								// check if pie swtich is off 
								if ($array['pie'] == '0') {
									echo 'checked="checked"';
								}
								?>
                                /> Off
								</div>
								<?php	
							}
							?>
                            
                            </div>
                            <div id='group-<?php echo $section_name.'-'.$css_selector.'-'.$property_group_name;?>' 
                            class='tvr-ui-right <?php echo $show_class; ?>'>
								<?php 
								
                                $this->media_query_tabs($section_name, $css_selector, $property_group_name, $main_label_show_class);
                                // loop through each properties in a group if set
                                if ( is_array( $property_group_array ) ) {
                                   // determine which tab to show
								   $device_tab = $this->options[$section_name][$css_selector]['device_focus'][$property_group_name];
								   if ( empty($device_tab) or $device_tab == 'all-devices') {
									   $show_class = 'show';
									} else {
										$show_class = '';
									}
							    ?><div class='property-fields property-<?php echo $property_group_name; ?> 
                                property-mq-all-devices <?php echo $show_class; ?> '><?php
								// merge to allow for new properties added to property-options.inc.php (array with values must come 2nd)
								$property_group_array = array_merge($this->propertyoptions[$property_group_name], $property_group_array);
                                foreach ($property_group_array as $property => $value) {
									$property = esc_attr($property); //=esc	
									$value = esc_attr($value); //=esc
									/* if a new CSS property has been added with array_merge(), $value will be something like:
									"Array ( [label] => Left [default_unit] => px [icon] => position_left )"  - so just set to nothing if it's an array
									*/
									if ($value == 'Array') { // esc_attr() must convert array to "Array" string
										$value = '';
									}
									// format input fields
									$this->resolve_input_fields($section_name, $css_selector, $property_group_array, $property_group_name, $property, $value);	
                                }
                                echo '</div><!-- end property-fields -->';
                            } // ends foreach property
                            // output any media query settings
							$this->media_query_fields($section_name, $css_selector, $property_group_name);
							echo '
                            </div><!-- end tvr-ui-right -->
                         </div><!-- end row -->';
                        } // ends foreach styles
                    } // ends if $array['styles'] is array
			}
			
			// on upgrading, the all-devices won't be checked even if only the main is checked. 
			// Solution = check if any mqs are checked. If the main label is checked but nothing else is, this is an identifying symptom
			function fix_for_mq_update($section_name, $css_selector, $property_group_name, $main_label_show_class) {
				if ($main_label_show_class == 'show') {
					$any_checked = false;
					foreach ($this->preferences['m_queries'] as $key => $m_query) {
						if ($this->options['non_section']['m_query'][$key][$section_name][$css_selector]['style_config'][$property_group_name] 
						== $property_group_name) {
							$any_checked = true;
						}
					}
					if (!$any_checked) {
						$show_class = 'show';
					}
					else {
						$show_class = '';
					}
				} 
				else {
					$show_class = '';
				}
				return $show_class;
			}
			
			
			// output media query tabs
			function media_query_tabs($section_name, $css_selector, $property_group_name, $main_label_show_class = '') {
				?>
                <div class="query-tabs">
                <span class="add-mq" title="Add a Media Query Tab">
                +
                <?php $this->media_query_checkboxes($section_name, $css_selector, $property_group_name, $main_label_show_class); ?>
                </span>
				
				<?php
				// save the configuration of the device tab
				$device_tab = $this->options[$section_name][$css_selector]['device_focus'][$property_group_name];
				if ( empty($device_tab)) {
					$device_tab = 'all-devices';
				}
				// should the tab be visible
				if ($this->options[$section_name][$css_selector]['all_devices'][$property_group_name] == $property_group_name) {
					$show_class = 'show';
				}
				else {
					$show_class = $this->fix_for_mq_update($section_name, $css_selector, $property_group_name, $main_label_show_class);
				}
				?>
                <input class="device-focus" type="hidden" 
                name="tvr_mcth[<?php echo $section_name; ?>][<?php echo $css_selector; ?>][device_focus][<?php echo $property_group_name; ?>]"
                value="<?php echo $device_tab; ?>" />
                <span class="mq-tab tab-mq-all-devices <?php echo $show_class. ' '; if ($device_tab == 'all-devices') { echo 'active'; } ?>" 
                rel="all-devices" title="General CSS that will apply to all devices">All Devices</span>
                <?php
				foreach ($this->preferences['m_queries'] as $key => $m_query) {
					// should the tab be visible
					if ($this->options['non_section']['m_query'][$key][$section_name][$css_selector]['style_config'][$property_group_name] 
                    == $property_group_name) {
						$show_class = 'show';
					}
					else {
						$show_class = '';
					}
					// should the tab be active
					if ($device_tab == $key) {
						$class = 'active';
					} else {
						$class = '';
					}
					echo '<span class="mq-tab tab-mq-'.$key.' '.$class.' '.$show_class.'" rel="'.$key.'" 
					title="Target '.$m_query['label'].' Screens">'.$m_query['label'].'</span>';
				}
				
				?>
                
                
                <div class="clear"></div>
                </div>
                <?php
			}
			
			// output the media query checkboxes
			function media_query_checkboxes($section_name, $css_selector, $property_group_name, $main_label_show_class) {
				
				// All devices checkbox
				?>
                <span class="mq-options">
                
                <span class="mq-button" title="No Media Query">
					<?php
                    if ($this->options[$section_name][$css_selector]['all_devices'][$property_group_name] == $property_group_name or 
					$this->fix_for_mq_update($section_name, $css_selector, $property_group_name, $main_label_show_class == 'show') 
					) {
                        $checked = 'checked="checked"';
                        $property_group_state = 'on';
                    }
                    else {
                        $checked='';
                        $property_group_state = 'off';
                    }
                    ?>
                    <input class="style-config checkbox all-toggle" type="checkbox" autocomplete="off"  value="<?php echo $property_group_name; ?>" 
                    name="tvr_mcth[<?php echo $section_name; ?>][<?php echo $css_selector; ?>][all_devices][<?php echo $property_group_name; ?>]" 
                    <?php echo $checked; ?> > All Devices
                    <span class="property-group-state" rel="<?php echo $property_group_state; ?>"></span>
                </span>
                
                <?php
				// Media query checkboxes
                foreach ($this->preferences['m_queries'] as $key => $m_query) {
                    // may need to register the group like the regular checkbox (but if no bugs, may be much more efficient not to)
                    // check if the properties group is checked
                    if ($this->options['non_section']['m_query'][$key][$section_name][$css_selector]['style_config'][$property_group_name] 
                    == $property_group_name) {
                        $checked = 'checked="checked"';
                        $property_group_state = 'on';
                    }
                    else {
                        $checked='';
                        $property_group_state = 'off';
                    }
                    echo '<span class="mq-button mqb-'.$key.'" title="'.$m_query['query'].'"> 
                    <input class="checkbox mq-toggle style-config" type="checkbox" name="tvr_mcth[non_section][m_query]['.
                    $key.']['.$section_name.']['.$css_selector.'][style_config]['.$property_group_name.']" 
                    value="'.$property_group_name.'" '.$checked.' rel="'.$key.'|'.$m_query['label'].'" />
                    '.$m_query['label'].'
                    <span class="property-group-state" rel="'.$property_group_state.'"></span>
                    
                    </span>'; 
                }
                ?>
                </span> 
   			<?php
			}
			
			// output the media query form fields (or empty container divs)
			function media_query_fields($section_name, $css_selector, $property_group_name) {
				// output 
				 foreach ($this->preferences['m_queries'] as $key => $m_query) {
					if ($this->options['non_section']['m_query'][$key][$section_name][$css_selector]['style_config'][$property_group_name] 
                    == $property_group_name) {
						// loop through each properties in a group if set
						$property_group_array = $this->options['non_section']['m_query'][$key][$section_name][$css_selector]['styles'][$property_group_name];
						if ( is_array( $property_group_array ) ) {
							// determine if the tab should be showing
							$device_tab = $this->options[$section_name][$css_selector]['device_focus'][$property_group_name];
							if ($device_tab == $key) {
								$show_class = 'show';
							} else {
								$show_class = '';
							}
							?><div class='property-fields property-<?php echo $property_group_name; ?> 
                            property-mq-<?php echo $key . ' ' .$show_class; ?>'><?php
							// merge to allow for new properties added to property-options.inc.php (array with values must come 2nd)
							$property_group_array = array_merge($this->propertyoptions[$property_group_name], $property_group_array);
							foreach ($property_group_array as $property => $value) {
								$property = esc_attr($property); //=esc	
								$value = esc_attr($value); //=esc
								if ($value == 'Array') { // esc_attr() must convert array to "Array" string
									$value = '';
								}
								// format input fields
								$this->resolve_input_fields($section_name, $css_selector, $property_group_array, 
								$property_group_name, $property, $value, 'mq', $key);	
							}
							echo '</div><!-- end property-fields -->';
						} // ends foreach property
					}
				}
            }
			
			// check if need to default to px or %
			function check_unit($property_group_name, $property, $value) {
				if ($this->propertyoptions[$property_group_name][$property]['default_unit'] == 'px' and 
				is_numeric($value) and
				$value != 0) {
					$unit = 'px';
				}
				elseif ($this->propertyoptions[$property_group_name][$property]['default_unit'] == '%' and 
				is_numeric($value) and
				$value != 0) {
					$unit = '%';
				}
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
					$sec_breaks = "\n\n";
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
							// if a selector is malformed, $array['styles'] will be a string instead of an array
							if (!is_array($array['styles'])) {
								$css_sel_name = ucwords(str_replace('_', ' ', $css_selector));
								$this->globalmessage.= '<p><b id="malformed" title="To fix, delete the malformed \''.$css_sel_name.'\' selector (which may be blank) in the \''.$section_name. '\' section">Malformed Error:</b> 
								<i>'.$section_name. ' > '.$css_sel_name.'</i></p>';
								continue; // so script doesn't cause fatal error
							}
							foreach ($sty['prop_key_array'] as $key) {
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
								if ( is_array( $array['styles'] ) ) {
									foreach ( $array['styles'] as $property_group_name => $property_group_array ) {
										if ( is_array( $property_group_array ) ) {
											// reset $xy_done
											$xy_done = false;
											foreach ($property_group_array as $property => $value) {
												// get the appropriate value for !important
												if ($con == 'mq') {
													$important_val = $this->options['non_section']['important']['m_query'][$mq_key][$section_name_slug][$css_selector_slug][$property_group_name][$property];
												} else {
													$important_val = $this->options['non_section']['important'][$section_name_slug][$css_selector_slug][$property_group_name][$property];
												}
												
												if ( $value != '' ) {
													// check if value needs px extension
													$unit = $this->check_unit($property_group_name, $property, $value);
													// convert custom escaped single & double quotes back to normal (font-family)
													$value = str_replace('cus-quot;', '"', $value);
													$value = str_replace('cus-#039;', '\'', $value);
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
													// if css3 property
													if (in_array($property, $sty['css3'])) {
														include $this->thisplugindir . 'includes/resolve-css3.inc.php';
													}
													else {
														$property = str_replace('_', '-', $property);
														// exception for images
														if ($property == 'background-image' and $value != 'none') {
															$sty['data'].= $tab."	$property: url(".$this->micro_root_url."$value){$sty['css_important']};
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
														elseif ($property == 'google-font') {
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
'.$tab.'filter: alpha(opacity='.$percent.')'.$sty['css_important'].';
'.$tab.'-moz-opacity:'.$value.$sty['css_important'].';
'.$tab.'-khtml-opacity: '.$value.$sty['css_important'].';
';
														}
													}
												}
											}
										}
									}
								}
								// determine if CSS3 PIE needs calling (don't add to body as all other pie will break)
								if ($array['style_config']['CSS3'] == 'CSS3' 
								and $css_selector != 'body'
								and $css_selector != 'html'
								and $array['pie'] != '0') {
									$sty['data'].= $tab."	behavior: url(".$sty['pie'].");									
";
									// auto-apply position:relative if prefered and position hasn't been explicitly defined
									if ($this->preferences['auto_relative'] == 1 
									and empty($array['styles']['position']['position'])) {
										$sty['data'].= $tab."	position: relative;									
";
									}
								}
								
								// output the custom styles if they exist
								if ($cusStyles and is_array($cusStyles)) {
									$sty['data'].= "	/* Selector Custom CSS */
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
				// Create new file if it doesn't already exist
				if (!file_exists($act_styles)) {
					if (!$write_file = fopen($act_styles, 'w')) {
						$this->globalmessage.= '<p>WordPress does not have permission to 
						create: ' . $this->root_rel($act_styles) . '. '.$this->permissionshelp.'</p>';
					}
					else {
						fclose($write_file);
					}
					$task = 'created';
				}
				else {
					$task = 'updated';
				}	
				// check if it's writable
				if ( is_writable($act_styles) )  { 
					 // stylesheet building code needs to wrapped up in a function as it needs to run twice (again for media queries)
					 // store values in a object that can be passed and returned to function
					$sty['data'] = '/*** Micro'.TVR_MICRO_VARIANT.' Styles ***/';
					// for later comparison
					$sty['g_fonts'] = array();
					$sty['g_fonts_used'] = false;
					$sty['prop_key_array'] = array('font', 'text', 'forecolor', 'background', 'dimensions', 
					'padding', 'margin', 'border', 'behaviour', 'position', 'CSS3');
					// determine if !important should be added to styles
					if ($this->preferences['css_important'] == '1') {
						$sty['css_important'] = ' !important';
					}
					else {
						$sty['css_important'] = '';
					}
					// get path to PIE.php in case it needs to be called
					$site_url = site_url();
					// $sty['pie'] = str_replace($site_url, '', $this->thispluginurl) . 'pie/PIE.php'; // call pie relative to root so works on SSL pages
					// $sty['pie'].= $this->thispluginurl.'pie/PIE.php'; // commented out coz it fails on ssl pages (if not full site ssl)
					// this will work on ssl pages & localhost with sites in sub directory of document root 
					$sty['pie'] = substr(getenv("SCRIPT_NAME"), 0, -19) . str_replace($site_url, '', $this->thispluginurl) . 'pie/PIE.php'; 
					// css3 properties
					$sty['css3'] = array('gradient_a', 'gradient_b', 'gradient_b_pos', 'gradient_c', 'gradient_angle','radius_top_left', 'radius_top_right', 'radius_bottom_right', 'radius_bottom_left', 'box_shadow_color', 'box_shadow_x', 'box_shadow_y', 'box_shadow_blur', 'text_shadow_color', 'text_shadow_x', 'text_shadow_y', 'text_shadow_blur' );
					// check if hand coded have been set - output before other css
					if ( trim($this->options['non_section']['hand_coded_css'] != '' ) ) {
					$sty['data'].= '
	
	
/* =Hand Coded CSS
-------------------------------------------------------------- */
					
'.stripslashes($this->options['non_section']['hand_coded_css']);	
					}
					// convert ui data to regualr css output
					$sty = $this->convert_ui_data($this->options, $sty, 'regular');
					// convert ui data to media query css output
					if (is_array($this->options['non_section']['m_query'])) {
						foreach ($this->options['non_section']['m_query'] as $key => $ui_data) {
							$sty = $this->convert_ui_data($ui_data, $sty, 'mq', $key);
						}
					}
					// the file will be created if it doesn't exist. otherwise it is overwritten.
					$write_file = fopen($act_styles, 'w'); 
					// if write is unsuccessful for some reason
					if (!fwrite($write_file, $sty['data'])) {
						$this->globalmessage.= '<p>Writing to ' . $this->root_rel($act_styles) . ' failed for some reason.</p>';
					}
					fclose($write_file);
					}
					// no write access
					else {
						$this->globalmessage.= '<p>WordPress does not have "write" permission 
						for: ' . $this->root_rel($act_styles) . '. '.$this->permissionshelp.'</p>';
							
					}
					// update the preferences value for active theme - custom/theme name
					$pref_array = array();
					// build google font url
					if ($sty['g_fonts_used']) {
						$g_url = '//fonts.googleapis.com/css?family=';
						$first = true;
						$g_ie_array = array();
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
						$this->globalmessage.= '<p>Theme activated.</p>';
					}
					
			}
			
			// write settings to .json file
			function update_json_file($theme, $context = '', $secondary_content = '') {
				// create micro theme of 'new' has been requested
				if ($context == 'new') {
					if ($theme == 'new') {
						$theme.= ' '.date("d M Y");
					}
					$theme = sanitize_file_name(sanitize_title($theme));
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
						$this->globalmessage.= '<p>WordPress does not have permission to 
						create: ' . $this->root_rel($json_file) . '. '.$this->permissionshelp.'</p>';
					}
					else {
						// if the user is creating a new micro theme from the Manage Themes page, add default sections to config.json
						if ($context == 'create-blank') {
							$data = '{"main_body":"","header":"","main_menu":"","content":"","pages":"","posts":"","post_single_view":"","post_navigation":"","comments":"","leave_reply_form":"","main_widget_areas":"","search_site":"","footer":"","footer_widget_areas":"","contact_form_7":"","non_section":{"hand_coded_css":"","view_state":{"main_body":{"this":"0"},"header":{"this":"0"},"main_menu":{"this":"0"},"content":{"this":"0"},"pages":{"this":"0"},"posts":{"this":"0"},"post_single_view":{"this":"0"},"post_navigation":{"this":"0"},"comments":{"this":"0"},"leave_reply_form":{"this":"0"},"main_widget_areas":{"this":"0"},"search_site":{"this":"0"},"footer":{"this":"0"},"footer_widget_areas":{"this":"0"},"contact_form_7":{"this":"0"}}}}';
							fwrite($write_file, $data);
						}
						fclose($write_file);
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
							require_once(ABSPATH . 'wp-includes/js/tinymce/plugins/spellchecker/classes/utils/JSON.php');
						}
						$json_object = new Moxiecode_JSON();
						// if it's a selective export, only write selected sections to json file
						if ( !empty($_POST['only_export_selected']) ) {
							// copy full options to var for editing
							$json_data = $this->options;
							// loop through full options
							foreach ($this->options as $section_name => $array) {
								// if the section wasn't selected, remove it from json data var (along with the view_state var)
								if (!array_key_exists($section_name, $_POST['export_sections']) and $section_name != 'non_section') {
									unset($json_data[$section_name]);
									unset($json_data['non_section']['view_state'][$section_name]);
								}
							}
							// set handcoded css to nothing if not marked for export
							if ( empty($_POST['export_sections']['hand_coded_css'])) {
								$json_data['non_section']['hand_coded_css'] = '';
							}
							// create debug selective export file if specified at top of script
							if ($this->debug_selective_export) {
								$debug_file = $this->micro_root_dir . $this->preferences['theme_in_focus'] . '/debug-selective-export.txt';
								$write_file = fopen($debug_file, 'w'); 
								$data.= "The Selectively Exported Options\n\n";;
								$data.= print_r($json_data, true);
								$data.= "\n\nThe Full Options\n\n";;
								$data.= print_r($this->options, true);
								fwrite($write_file, $data);
								fclose($write_file);
							}
						}
						// default to writing all options to json file
						else {
							$json_data = $this->options;
						}
						// write data to json file
						if ($data = $json_object->encode($json_data)) {
							// the file will be created if it doesn't exist. otherwise it is overwritten.
							$write_file = fopen($json_file, 'w'); 
							fwrite($write_file, $data);
							fclose($write_file);
						}
						else {
							$this->globalmessage.= '<p>WordPress failed to convert your settings into json.</p>';
						}
					}
					else {
						$this->globalmessage.= '<p>WordPress does not have "write" permission 
						for: ' . $this->root_rel($json_file) . '. '.$this->permissionshelp.'</p>';	
					}
				}
				return $theme; // sanitised theme name
			}
					
			// load .json file
			function load_json_file($json_file, $theme_name, $context = '') {
				// check that config.json exists
				if (file_exists($json_file)) {
					// attempt to read config.json
					if (!$read_file = fopen($json_file, 'r')) {
						$this->globalmessage.= '<p>WordPress was not able to read 
						' . $this->root_rel($json_file) . '. '.$this->permissionshelp.'</p>';
					}
					else {
						// get the data
						$data = fread($read_file, filesize($json_file));
						fclose($read_file);
					}
					// tap into WordPress native JSON functions
					if( !class_exists('Moxiecode_JSON')) {
						require_once(ABSPATH . 'wp-includes/js/tinymce/plugins/spellchecker/classes/utils/JSON.php');
					}
					$json_object = new Moxiecode_JSON();
					// check what import method the user specified
					if ($context == 'Overwrite' or empty($context)) {
						// attempt to decode json into an array
						if (!$this->options = $json_object->decode($data)) {
							$this->globalmessage.= '<p>WordPress was not able to convert ' . $this->root_rel($json_file) . 
							' into a usable format.</p>';
							$json_error = true;
						}
					}
					elseif ($context == 'Merge') {
						// attempt to decode json into an array
						if (!$this->to_be_merged = $json_object->decode($data)) {
							$this->globalmessage.= '<p>WordPress was not able to convert ' . $this->root_rel($json_file) . 
							' into a usable format.</p>';
							$json_error = true;
						}
						// json success, merge new settings with current
						else {
							$this->options = $this->merge($this->options, $this->to_be_merged);
						}
					}
					// json decode was successful
					if (!$json_error) {
						$this->globalmessage.= '<p>Settings successfully imported.</p>';
						// check for new mqs
						$mqs_imported = false;
						$pref_array['m_queries'] = $this->preferences['m_queries'];
						if (is_array($this->options['non_section']['active_queries'])) {
							$i = 0;
							foreach ($this->options['non_section']['active_queries'] as $options_mq_key => $mq_array) {
								// add the new media query if not currently in use
								$pref_mq_key = $this->in_2dim_array($mq_array['query'], $pref_array['m_queries'], 'query');
								if ( !$pref_mq_key ) { // new media query
									// ensure key is unique by using unique base from last page load
									$pref_mq_key = $this->unq_base.++$i;
									$pref_array['m_queries'][$pref_mq_key]['label'] = $mq_array['label']. ' (imp)';
									$pref_array['m_queries'][$pref_mq_key]['query'] = $mq_array['query'];
									$mqs_imported = true;
								}
								// If the media query was imported or not, the mq key in the options needs to match the key in the pref array
								$this->replace_options_mq_key($options_mq_key, $pref_mq_key);	
							}
						}
						if ($mqs_imported) {
							$this->globalmessage.= '<p><b>New media queries were imported</b>. 
							<a href="admin.php?page='.$this->preferencespage.'#m_queries"
							 title="Review (and relabel) imported media queries">Review them here</a></p>
							<p>Note: imported queries are marked with "(imp)", which you can remove from the label name once you\'ve reviewed them.</p>';
						}
						// save settings in db
						$this->saveUiOptions($this->options);
						// update active-styles.css
						$this->update_active_styles($theme_name, $context); // pass the context so the function doesn't update theme_in_focus
						// Only update theme_in_focus if it's not a merge
						if ($context != 'Merge') {
							$pref_array['theme_in_focus'] = $theme_name; 	
						}
						if ($mqs_imported) {
							
						}
						// update the preferences if not merge or media queries need importing
						if ($context != 'Merge' or $mqs_imported) {
							$this->savePreferences($pref_array);
						}
					}
				}
				// the config file doesn't exist
				else {
					$this->globalmessage.= '<p>' . $this->root_rel($json_file) . ' settings file could not be loaded because 
					it doesn\'t exist in <i>'.$this->readable_name($theme_name).'</i>.</p>';
				}
				
			}
			
			// ensure mq keys in pref array and options match
			function replace_options_mq_key($options_mq_key, $pref_mq_key) {
				$cons = array('active_queries', 'm_query');
				// replace the relevant array keys - unset() doesn't work on $this-> so slightly convaluted solution used
				$updated_array = array();
				foreach ($cons as $stub => $context) {
					unset($updated_array);
					if (is_array($this->options['non_section'][$context])) {
						foreach ($this->options['non_section'][$context] as $cur_key => $array) {
							if ($cur_key == $options_mq_key) {
								$key = $pref_mq_key;
							} else {
								$key = $cur_key;
							}
							$updated_array[$key] = $array;
						}
						$this->options['non_section'][$context] = $updated_array; // reassign main array with updated keys array 
					}
				}
			}
			
			
			// merge the new settings with the current settings
			function merge($orig_settings, $new_settings) {
				// create debug merge file if set at top of script
				if ($this->debug_merge) {
					$debug_file = $this->micro_root_dir . $this->preferences['theme_in_focus'] . '/debug-merge.txt';
					$write_file = fopen($debug_file, 'w'); 
					$data = '';
					$data.= "\n\nThe to merge options\n\n";
					$data.= print_r($new_settings, true);
					$data.= "\n\nThe current options\n\n";
					$data.= print_r($orig_settings, true);
				}
				if (is_array($new_settings)) {
					// loop through new sections to check for section name conflicts
					foreach($new_settings as $section_name => $array) {
						// if a name conflict exists
						if ( $this->is_name_conflict($section_name, $orig_settings, $new_settings, 'first-check') ) {
							// create a non-conflicting new name
							$alt_name = $this->get_alt_section_name($section_name, $orig_settings, $new_settings);
							// rename the to-be-merged section and the corresponding view_state tracker
							$new_settings[$alt_name] = $new_settings[$section_name];
							$new_settings['non_section']['view_state'][$alt_name] = $new_settings['non_section']['view_state'][$section_name];
							unset($new_settings[$section_name]);
							unset($new_settings['non_section']['view_state'][$section_name]);
						}
					}
					// now that we've checked for and corrected possible name conflicts, merge the arrays
					$merged_settings = array_merge($orig_settings, $new_settings);
					// view states need to be merged seperately else the new settings overwrite the original
					$merged_settings['non_section']['view_state'] = 
					array_merge($orig_settings['non_section']['view_state'], $new_settings['non_section']['view_state']);
					// the Hand-coded CSS of the imported settings needs to be appended to the original
					$merged_settings['non_section']['hand_coded_css'] =  
					$orig_settings['non_section']['hand_coded_css'] . "\n\n/*** CSS From Micro Theme Imported with 'Merge' ***/\n\n" 
					. $new_settings['non_section']['hand_coded_css'];
				}
				if ($this->debug_merge) {
					$data.= "\n\nThe Merged options\n\n";
					$data.= print_r($merged_settings, true);
					fwrite($write_file, $data);
					fclose($write_file);
				}
				return $merged_settings;
			}


			/*** 
			Manage Micro Theme Functions 
			***/
			
			
			
			// create micro theme
			function create_micro_theme($micro_name, $action, $temp_zipfile) {
				// sanitize dir name
				$name = sanitize_file_name( $micro_name  );
				// extra bit need for zip uploads (removes .zip)
				if ($action == 'unzip') {
					$name = substr($name, 0, -4); 
				}
				$content_dir = $this->preferences['content_dir'];
				// check for micro-themes folder
				if ( !is_dir($this->micro_root_dir) ) {
					if ( !wp_mkdir_p( $this->micro_root_dir ) ) {
						$this->globalmessage.= '<p>WordPress was not able to create 
						the ' . $this->root_rel($this->micro_root_dir) . ' directory. ' . $this->permissionshelp . '</p>';
						$error = true;
					}
				}
				// check if the micro-themes folder is writable
				if ( !is_writeable( $this->micro_root_dir ) ) {
					$this->globalmessage .= '<p>The directory ' . $this->root_rel($this->micro_root_dir) . ' is not writable. 
					You need to change its permissions. ' . $this->permissionshelp . '</p>';
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
				  $this->globalmessage.= '<p>WordPress was not able to create 
						the ' . $this->root_rel($this_micro_abs) . ' directory.</p>';
						$error = true;
				}
				// Check folder permission
				if ( !is_writeable( $this_micro_abs ) ) {
					$this->globalmessage.= '<p>The directory ' . $this->root_rel($this_micro_abs) . ' is not writable. 
					You need to change its permissions. ' . $this->permissionshelp . '</p>';
					$error = true;
				}
				if (SAFE_MODE and $this->preferences['safe_mode_notice'] == '1') {
					$this->globalmessage.= '<p>The PHP server setting "Safe-Mode" is on.</p><p><b>This isn\'t necessarily 
					a problem. But if the micro theme "' . $this->readable_name($name) . '" hasn\'t been created</b>, 
					please create the directory ' . $this->root_rel($this_micro_abs) . ' manually and gives it permission 
					code 777.' . $this->permissionshelp . '</p>';
					$error = true;
				}
				// unzip if required
				if ($action == 'unzip') {
					$this->extract_files($this_micro_abs, $temp_zipfile);
					/* If the user installs a purchased micro theme, it will create micro-themes/$product_id/
					A symptom of this will be a bg image url_path and/or theme name mismatch with the dir name 
					(that isn't due to an auto-rename to prevent overwrite). 
					So, if we determine that the dir_name really doesn't fit with the url paths or theme name: auto-correct.
					*/
					// if dir rename wasn't essential to prevent conflict...
					if (!$alt_name) {
						// first check config.json for bg url paths
						$json_config_file = $this_micro_abs . '/config.json';
						if (is_file($json_config_file)) {
							// check if it's readable
							if ( is_readable($json_config_file) )  {
								$fh = fopen($json_config_file, 'r');
								$data = fread($fh, filesize($json_config_file));
								fclose($fh);
								// check for bg image path as first port of call
								preg_match_all('/"background_image":"([^none][A-Za-z0-9 _\-\.\\/&\(\)\[\]!\{\}\?:=]+)"/', 
								$data, 
								$img_array, 
								PREG_PATTERN_ORDER);
								
								$orig_name = $this_micro_abs;

								// if there are images, check paths
								if (count($img_array[1]) > 0) {
									$always_same = true;
									$i = 0;
									$ext_img_array = array();
									foreach ($img_array[1] as $key => $dir_img) {
										$dir_img_array = explode('/', $dir_img);
										$dir = $dir_img_array[0];
										// if not all paths are the same, url paths are a bad indicator
										if ($i != 0 and ($dir != $prev_dir)) {
											$always_same = false;
										}
										$prev_dir = $dir;
										++$i;
									}
									// if all bg_url paths are the same, bg url paths are a good indicator
									if ($always_same) {
										
										// rename dir to fit image paths if there is a mismatch
										if ($orig_name != $this->micro_root_dir . $dir) {
											// need to protect against overwrites here too 
											if ($alt_name = $this->rename_if_required($this->micro_root_dir, $dir)) {
												$dir = $alt_name; // $alt_name is false if no rename was required 
											}
											$new_name = $this->micro_root_dir . $dir;
											$name = $dir; // update $name for subsequent processing
											// rename
											rename($orig_name, $new_name);
										}
									}
								}
								// else bg urls can't be used to validate, check the theme name in the meta.txt file
								if (count($img_array[1]) == 0 or !$always_same) {
									$meta_info = $this->read_meta_file($this_micro_abs . '/meta.txt');
									$dir = strtolower(sanitize_file_name($meta_info['Name']));
									// rename dir to fit image paths if there is a mismatch
									if ($orig_name != $this->micro_root_dir . $dir) {
										// need to protect against overwrites here too 
										if ($alt_name = $this->rename_if_required($this->micro_root_dir, $dir)) {
											$dir = $alt_name; // $alt_name is false if no rename was required 
										}
										$new_name = $this->micro_root_dir . $dir;
										$name = $dir; // update $name for subsequent processing
										// rename
										rename($orig_name, $new_name);
									}
								}	
							}
						}
					}
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
					$this->globalmessage.= '<p>The newly created theme is not currently being viewed.</p>';
				}
				// if still no message, the action worked
				if ($error != true) {
					if ($action == 'create') {
						$this->globalmessage.= '<p>Micro Theme folder successfully created.</p>';
					}
					if ($action == 'unzip') {
						$this->globalmessage.= '<p>The Micro Theme zip package was successfully uploaded and extracted.</p>';
					}
					if ($action == 'export') {
						$this->globalmessage.= '<p>Settings Exported.</p>';
					}
				}
			}
		
			// handle zip package
			function handle_zip_package() {
				$temp_zipfile = $_FILES['upload_micro']['tmp_name'];
				$filename = $_FILES['upload_micro']['name']; 
				// Chrome return a empty content-type : http://code.google.com/p/chromium/issues/detail?id=6800
				if ( !preg_match('/chrome/i', $_SERVER['HTTP_USER_AGENT']) ) {
					// check if file is a zip file
					if ( !preg_match('/(zip|download|octet-stream)/i', $_FILES['upload_micro']['type']) ) {
						@unlink($temp_zipfile); // del temp file
						$this->globalmessage.= '<p>The uploaded file was faulty or was not a zip file.<br /><br />
						The server recognised this file type:'.$_FILES['upload_micro']['type'].'</p>';
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
					$this->globalmessage.= '<p>Error : ' . $archive->errorInfo(true).'</p>';
				}
			}
			
			// handle zip archiving
			function create_zip($path_to_dir, $dir_name, $zip_store) {
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
						$this->globalmessage.= '<p>Error : ' . $archive->errorInfo(true).'</p>';
					}
					else {
						$this->globalmessage.= '<p>Zip package successfully created. 
						<a href="'.$this->thispluginurl.'zip-exports/'.$dir_name.'.zip">Download Zipped Theme</a> 
						</p>';
					}
				}
				else {
					$this->globalmessage.= '<p>The directory ' . $this->root_rel($zip_store) . ' is not writable. 
					You need to change its permissions so that it is writable. ' . $this->permissionshelp . '</p>';
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
						$this->globalmessage.= '<p>WordPress does not have permission to 
						read: ' . $this->root_rel($abs_meta_path) . '. '.$this->permissionshelp.'</p>';
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
						$data = fread($fh, filesize($readme_file));
						fclose($fh);
						return $data; 
					}
					else {
						$abs_readme_path = $this->micro_root_dir . $this->preferences['theme_in_focus'].'/readme.txt';
						$this->globalmessage.= '<p>WordPress does not have permission to 
						read: ' . $this->root_rel($abs_readme_path) . '. '.$this->permissionshelp.'</p>';
						return false;
					}
				}
			}
			
			// adapted WordPress function for reading and formattings a template file
			function flx_get_theme_data( $theme_file ) {
				$default_headers = array(
					'Name' => 'Theme Name',
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
			function tvr_delete_micro_theme() {
				// must delete files first - dir needs to be empty for rmdir() to work
				$file_structure = $this->dir_loop($this->micro_root_dir);
				// loop through files if they exist
				if (is_array($file_structure[$this->preferences['theme_in_focus']])) {
					foreach ($file_structure[$this->preferences['theme_in_focus']] as $dir => $file) {
						if (!unlink($this->micro_root_dir . $this->preferences['theme_in_focus'].'/'.$file)) {
							$this->globalmessage.= '<p>Unable to delete: ' . 
							$this->root_rel($this->micro_root_dir . $this->preferences['theme_in_focus'].'/'.$file) . '</p>';
							$error = true;
						}
					}	
				}
				if ($error != true) {
					$this->globalmessage.= '<p>Any files within ' . 
					$this->readable_name($this->preferences['theme_in_focus']) . ' were successfully deleted.</p>';
					// attempt to delete empty directory
					if (!rmdir($this->micro_root_dir . $this->preferences['theme_in_focus'])) {
						$this->globalmessage.= '<p>The empty directory: ' . 
						$this->readable_name($this->preferences['theme_in_focus']) . ' could not be deleted.</p>';
					}
					else {
						$this->globalmessage.= '<p>' . 
						$this->readable_name($this->preferences['theme_in_focus']) . ' was successfully deleted.</p>';
						// reset the theme_in_focus value in the preferences table
						$pref_array['theme_in_focus'] = '';
						if (!$this->savePreferences($pref_array)) {
							$this->globalmessage.= '<p>An error occured when unsetting the deleted theme as the current theme in view. Selecting a new theme from the drop down menu at the top right of the page should solve this issue.</p>';
						}
					}
				}
			} 
		
			// update the meta file
			function update_meta_file($meta_file) {	
				// Create new file if it doesn't already exist
				if (!file_exists($meta_file)) {
					if (!$write_file = fopen($meta_file, 'w')) {
						$this->globalmessage.= '<p>WordPress does not have permission to create: ' . $this->root_rel($meta_file) . '. '.$this->permissionshelp.'</p>';
					}
					else {
						fclose($write_file);
					}
					$task = 'created';
					// set post variables if undefined (might be defined if theme dir has been 
					// created manually and then user is submitting meta info for the first time)
					if (!isset($_POST['theme_meta']['Description'])) {
						$_POST['theme_meta']['Description'] = "Click the 'Edit Meta Info' Button below to add a description (to replace this text) and other meta info for this Micro Theme.";
						global $user_identity, $user_ID;
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
						$theme_data = get_theme_data(get_stylesheet_uri());	
						// $template = $theme_data['Name'] . ' ' . $theme_data['Version'];
						$template = $theme_data['Name'];
						$_POST['theme_meta']['Author'] = $user_identity;
						$_POST['theme_meta']['AuthorURI'] = ''; 
						// $_POST['theme_meta']['Template'] = get_current_theme();
						$_POST['theme_meta']['Template'] = $template;
						$_POST['theme_meta']['Version'] = '1.0';
						$_POST['theme_meta']['Tags'] = strtolower($_POST['theme_meta']['Name']).' micro theme, customise '.strtolower($template
						).' '.$theme_data['Version'].', wordpress theme, wordpress themes 3.0, micro themes, microthemer';
					}
				}
				else {
					$task = 'updated';
				}
					
				// check if it's writable
				if ( is_writable($meta_file) )  {
					$data = '/*
Theme Name: '.strip_tags(stripslashes($_POST['theme_meta']['Name'])).'
Theme URI: '.strip_tags(stripslashes($_POST['theme_meta']['URI'])).'
Description: '.strip_tags(stripslashes($_POST['theme_meta']['Description'])).'
Author: '.strip_tags(stripslashes($_POST['theme_meta']['Author'])).'
Author URI: '.strip_tags(stripslashes($_POST['theme_meta']['AuthorURI'])).'
Template: '.strip_tags(stripslashes($_POST['theme_meta']['Template'])).'
Version: '.strip_tags(stripslashes($_POST['theme_meta']['Version'])).'
Tags: '.strip_tags(stripslashes($_POST['theme_meta']['Tags'])).'
*/';
					 // the file will be created if it doesn't exist. otherwise it is overwritten.
					 $write_file = fopen($meta_file, 'w'); 
					 fwrite($write_file, $data);
					 fclose($write_file);
					 // success message
					 $this->globalmessage.= '<p>Meta Info '.$task.'</p>';
				}
				else {
					$this->globalmessage.= '<p>WordPress does not have "write" permission for: ' . $this->root_rel($meta_file) . '. '.$this->permissionshelp.'</p>';	
				}
				// check if the micro theme dir needs to be renamed
				if (isset($_POST['prev_micro_name']) and ($_POST['prev_micro_name'] != $_POST['theme_meta']['Name'])) {
					$orig_name = $this->micro_root_dir . $this->preferences['theme_in_focus'];
					$new_theme_in_focus = sanitize_file_name(sanitize_title($_POST['theme_meta']['Name']));
					$new_name = $this->micro_root_dir . $new_theme_in_focus;
					// if the directory is writable
					if (is_writable($orig_name)) {
						if (rename($orig_name, $new_name)) {
							// if rename is successful, update the value in the preferences table
							$pref_array = array();
							$pref_array['theme_in_focus'] = $new_theme_in_focus;
							if ($this->savePreferences($pref_array)) {
								$this->globalmessage.= '<p>Theme directory successfully renamed on server.</p>';
							}
						}
						else {
							$this->globalmessage.= '<p>The directory, ' . $this->root_rel($orig_name) . ' is writable, but WordPress could not rename it for some reason. Sorry we can\'t be more specific!</p>';
						}
					}
					else {
						$this->globalmessage.= '<p>WordPress does not have permission to rename the 
						directory ' . $this->root_rel($orig_name) . ' to match your new 
						theme name "'.htmlentities($this->readable_name($_POST['theme_meta']['Name'])).'". '.$this->permissionshelp.'.</p>';
					}
				}		
			}
			
			// update the readme file
			function update_readme_file($readme_file) {	
				// Create new file if it doesn't already exist
				if (!file_exists($readme_file)) {
					if (!$write_file = fopen($readme_file, 'w')) {
						$this->globalmessage.= '<p>WordPress does not have permission to create: ' 
						. $this->root_rel($readme_file) . '. '.$this->permissionshelp.'</p>';
					}
					else {
						fclose($write_file);
					}
					$task = 'created';
					// set post variable if undefined (might be defined if theme dir has been 
					// created manually and then user is submitting readme info for the first time)
					if (!isset($_POST['tvr_theme_readme'])) {
						// set $_POST['tvr_theme_readme'] with default text
						include $this->thisplugindir . 'includes/default-readme.inc.php';
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
					 $this->globalmessage.= '<p>readme.txt '.$task.'</p>';
				}
				else {
					$this->globalmessage.= '<p>WordPress does not have "write" permission 
					for: ' . $this->root_rel($readme_file) . '. '.$this->permissionshelp.'</p>';	
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
							  $this->globalmessage.= '<p><b><i>'.htmlentities($file).'</i></b> was successfully uploaded.</p>';
							  // resize file if it's an image and user specifies resize dimensions
							  if ( !empty($_POST['tvr_upload_file_resize']) and $this->is_image($file)) {
								  // if users specifies new dimesions resize
								  if ( !empty($_POST['tvr_upload_file_width']) or !empty($_POST['tvr_upload_file_height']) ) {
									  $img_full_path = $dest_dir . $file;
									  if (!$final_dimensions = $this->resize($img_full_path, 
									  intval($_POST['tvr_upload_file_width']), 
									  intval($_POST['tvr_upload_file_height']), $img_full_path)) {
										  $this->globalmessage.= '<p>Could not resize <b><i>'.htmlentities($file).'</i></b>.</p>'; 
									  }
									  else {
										  $this->globalmessage.= '<p><b><i>'.htmlentities($file).'</i></b> 
										  was successfully resized to 
										  '.$final_dimensions['w'].' x '.$final_dimensions['h'].'.</p>'; 
									  }
								  }
								  // no dimensions specified
								  else {
									  $this->globalmessage.= '<p>Your image was not resized because you didn\'t specify 
									  any dimensions for width or height. Tip - you only have to specify width OR height. 
									  The image will always be resized proportionally.</p>';
								  }
							  }
							}
						}
						// it's not writable
						else {
							$this->globalmessage.= '<p>WordPress does not have "Write" permission to the 
							directory: ' . $this->root_rel($dest_dir) . '. '.$this->permissionshelp.'.</p>';
						}
					}
					else {
						$this->globalmessage.= '<p>Invalid file type.</p>';
					}
				}
				// there was an error - save in gloabl message
				else {
					switch ($_FILES['upload_file']['error']) {
						case 1:  $this->globalmessage.= 'File exceeded upload_max_filesize';  break;
						case 2:  $this->globalmessage.= 'File exceeded max_file_size';  break;
						case 3:  $this->globalmessage.= 'File only partially uploaded';  break;
						case 4:  $this->globalmessage.= 'No file uploaded';  break;
					}
				}	
			}
			
			// resize image
			function resize($img, $max_width, $max_height, $newfilename) {
				//Check if GD extension is loaded
				if (!extension_loaded('gd') && !extension_loaded('gd2')) {
					$this->globalmessage.= '<p>The PHP extension GD is not loaded.</p>';
					return false;
				}
				//Get Image size info
				$imgInfo = getimagesize($img);
				switch ($imgInfo[2]) {
					case 1: $im = imagecreatefromgif($img); break;
					case 2: $im = imagecreatefromjpeg($img);  break;
					case 3: $im = imagecreatefrompng($img); break;
					default:  $this->globalmessage.= '<p>Unsuported file type. Are you sure you uploaded an image?</p>';  
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
					$this->globalmessage.= "<p>The resize dimensions you specified ($max_width x $max_height) are bigger 
					than the original image ($width x $height). This is not allowed.</p>";  
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
					default:  $this->globalmessage.= '<p>Operation failed.</p>'; return false; break;
				}
				return $final_dimensions;
			}
			
			// check for links to images in other micro themes
			function check_image_paths($json_file, $theme_name, $action) {
				if (file_exists($json_file)) {
					// attempt to read config.json
					if (!$read_file = fopen($json_file, 'r')) {
						$this->globalmessage.= '<p>WordPress was not able to read ' . $this->root_rel($json_file) . '. '.$this->permissionshelp.'</p>';
					}
					else {
						// get the data
						$data = fread($read_file, filesize($json_file));
						fclose($read_file);
					}
					// look for non-local image paths
					preg_match_all('/"background_image":"([^none][A-Za-z0-9 _\-\.\\/&\(\)\[\]!\{\}\?:=]+)"/', 
					$data, 
					$img_array, 
					PREG_PATTERN_ORDER);
					// if there are images, check paths
					
					if (count($img_array) > 0) {
						$i = 0;
						$ext_img_array = array();
						foreach ($img_array[1] as $key => $dir_img) {
							$dir_img_array = explode('/', $dir_img);
							$dir = $dir_img_array[0];
							// if there are external image paths, save in array
							if ($dir != $this->preferences['theme_in_focus'] and !in_array($dir_img, $ext_img_array)) {
								$ext_img_array[$i] = $dir_img;
								++$i;
							}
						}
						
						// if there are external images
						if ($i > 0) {
							// show warning if necessary
							if ($action == 'notify') {
								$this->globalmessage.= '<p><b>Note:</b> this theme links to the following external images:</p>
								<ol>';
								foreach ($ext_img_array as $dir_img) {
									$this->globalmessage.= '<li>' . $dir_img . '</li>';
								}
								$this->globalmessage.= '
								</ol>
								<p><b>Recomended Action:</b> <a href="admin.php?page='.$this->microthemespage.'&action=tvr_copy_micro_images&_wpnonce='.wp_create_nonce('tvr_copy_micro_images').'">Copy Images To Theme and/or Auto Update Image Paths</a></p>';
								return true;  // there are external images
							}
							// else copy images & correct image paths 
							elseif ($action == 'correct') {
								$theme_dir = $this->micro_root_dir . $theme;
								// if target dir is writable
								if (is_writable($theme_dir)) {
									foreach ($ext_img_array as $dir_img) {
										$dir_img_array = explode('/', $dir_img);
										$dir = $dir_img_array[0];
										$img = $dir_img_array[1];
										$new_dir_img = str_replace($dir, $theme_name, $dir_img);
										// attempt to copy image
										$ext_theme_dir = $this->micro_root_dir . $dir;
										$ext_img_path = $this->micro_root_dir . $dir . '/' . $img;
										$new_img_path = $this->micro_root_dir . $new_dir_img;
										// check if original dir exists (it won't following a rename)
										if (is_dir($ext_theme_dir)) {
											// if orig dir is readable
											if (is_readable($ext_theme_dir)) {
												// if the file exists - copy it and update img path
												if (file_exists($ext_img_path)) {
													// try to copy
													if (copy($ext_img_path, $new_img_path)) {
														$data = str_replace($dir_img, $new_dir_img, $data);
														$write = true;
													}
													// copy failed
													else {
														$this->globalmessage.= "<p>
														<i>$dir_img</i> could not be copied to <i>$new_dir_img</i></p>";
														$copy_error = true;
													}
												}
												// the file doesn't exist
												else {
													// check if it's just the file path that needs updating
													if (file_exists($new_img_path)) {
														// just update path (no need to copy)
														$data = str_replace($dir_img, $new_dir_img, $data);
														$copy_error = true;
														$write = true;
													}
													// the image doesn't in the current theme
													else {
														$this->globalmessage.= "<p>
														<i>$dir_img</i> could not be copied to <i>$new_dir_img</i> because the 
														image doesn't exist in that directory. ".$this->permissionshelp."</p>";
														$copy_error = true;
													}
												}
											}
											// original dir isn't readable
											else {
												$copy_error = true;
												$this->globalmessage.= "<p>
												<i>$dir</i> isn't writable. ".$this->permissionshelp."</p>";
											}
										}
										// else original dir doesn't exist anymore
										else {
											$copy_error = true;
											// check if it's just the file path that needs updating (e.g. with a dir rename)
											if (file_exists($new_img_path)) {
												// just update path (no need to copy)
												$data = str_replace($dir_img, $new_dir_img, $data);
												$write = true;
											}
											// the image doesn't exist in the current theme
											else {
												$this->globalmessage.= "<p>
												<i>$dir_img</i> could not be copied to <i>$new_dir_img</i> because the 
												directory doesn't exist anymore. ".$this->permissionshelp."</p>";
											}
										}
									}
									// if all copies worked out, notify
									if ($copy_error != true) {
										$this->globalmessage.= "<p>All images were successfully copied</p>";
									}
									// overwrite existing config.json if required
									if (is_writable($json_file) and $write = true) {
										if ($write_file = fopen($json_file, 'w')) {
											if (fwrite($write_file, $data)) {
												fclose($write_file);
												$this->globalmessage.= "<p>Images paths were successfully updated</p>
												<p><b>Note:</b> you will need to re-import this theme for the changes 
												to show in the Microthemer UI.</p>";
											}
											else {
												$write_error = true;
											}
										}
									}
									// json.config isn't writable
									else {
										$this->globalmessage.= "<p>
										" . $this->root_rel($json_file) . " isn't writable. ".$this->permissionshelp."</p>";
									}
								}
								// target dir isn't writable
								else {
									$this->globalmessage.= "<p>
									" . $this->root_rel($this->micro_root_dir . $theme) . " isn't writable. ".$this->permissionshelp."</p>";
								}
								return true; // there are external images
							}
						}
					}	
				}
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
			var $preferencesName = 'preferences_themer_loader';
			// @var array $preferences Stores the ui options for this plugin
			var $preferences = array();
			
			/**
			* PHP 4 Compatible Constructor
			*/
			function tvr_microthemer_frontend(){$this->__construct();}
			/**
			* PHP 5 Constructor
			*/ 
			       
			function __construct(){
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
				
				// remove wp admin bar from frontend preview if prefered
				if ($this->preferences['admin_bar'] == 0 and TVR_MICRO_VARIANT == 'themer') {
					add_action( 'show_admin_bar', '__return_false' );
				}
				// add viewport = 1 if set in preferences
				if ($this->preferences['initial_scale'] !== '0') {
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
				// replace active theme styles.css with microthemer reset.css (if user specifies)
				if ($this->preferences['disable_parent_css'] == 1) {
					add_action('get_header', array(&$this, 'tvr_head_buffer_start'));
					add_action('wp_head', array(&$this, 'tvr_head_buffer_end'));
				}
				
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
					$append = '?nocache=' . time();
				}
				if ( !empty($this->preferences['active_theme']) ) {
					// register css - check theme name so relevant dependecies can be added
					$deps = $this->dep_stylesheets();
					
					// check if Google Fonts stylesheet needs to be called
					if ($this->preferences['g_fonts_used']) {
						wp_register_style( 'micro'.TVR_MICRO_VARIANT.'_g_font', $this->preferences['g_url'], false ); 
						global $is_IE;
						// enqueue
						wp_enqueue_style( 'micro'.TVR_MICRO_VARIANT.'_g_font' );
						// register ie conditional google fonts for faux fix: (doesn't work)
						// http://www.smashingmagazine.com/2012/07/11/avoiding-faux-weights-styles-google-web-fonts/
						/*if ( $is_IE ) {
							global $wp_styles;
							$k = 0;
							foreach ($this->preferences['g_ie_array'] as $font_and_var) {
								// echo $font_and_var;
								// IE8 and below
								wp_register_style( 'g_font_ie-'.$k, '//fonts.googleapis.com/css?family='.$font_and_var, false);
								wp_enqueue_style( 'g_font_ie-'.$k );
								// $wp_styles->add_data('g_font_ie-'.$k, 'conditional', '(lte IE 8)'); // ie8 or lower
								++$k;
							}
						}*/
						
					}
					
					wp_register_style( 'micro'.TVR_MICRO_VARIANT, $this->micro_root_url.'active-styles.css'.$append, $deps ); 
					// enqueue
					wp_enqueue_style( 'micro'.TVR_MICRO_VARIANT );	
					
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
					$append = '?nocache=' . time();
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
							if ($_SERVER["HTTPS"] == "on") {$curpageURL.= "s";}
							$curpageURL.= "://";
							if ($_SERVER["SERVER_PORT"] != "80") {
							$curpageURL.= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
							} else {
							$curpageURL.= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
							}
							return $curpageURL;
						}
					}
					?><meta name='iframe-url' id='iframe-url' content='<?php echo currentPageURL();?>' /><?php
				} 
			}
			
			// add firebug style overlay js if user is logged in
			function add_js() {
				if ( is_user_logged_in() and TVR_MICRO_VARIANT == 'themer') {
					wp_enqueue_script( 'jquery' );
					wp_register_script( 'tvr_mcth_overlay', $this->thispluginurl.'js/overlay/jquery.overlay.js?v='.$this->version, 
					array('jquery') ); // this last param enqueues jquery even if it wasn't enqueued above (FYI)
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