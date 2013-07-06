<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}
// get the file structure for use throughout the UI
$file_structure = $this->dir_loop($this->micro_root_dir);

?>

<!-- bg image gallery -->
<div id='bg-image-links'>
<?php
$count = 0;
foreach ($this->filtered_images as $dir => $array) {
	foreach ($array as $file) {
		$dir = esc_attr($dir);
		$file = esc_attr($file);
		$bg_url = $this->micro_root_url . $dir . '/' . $file;
		if ($count == 0) {
			$this->first_bg_url = $bg_url;
			$id = 'id="first-bg-url"';
		}
		else {
			$id = '';
		}
		echo '<a href="'.$bg_url.'" '.$id.' rel="bg-image-ui">'.$bg_url.'</a>';
		++$count;
	}
}
?>
</div>

<?php // output the user interface ?>

<div class='wrap tvr-ui-wrap'>
	<div id='tvr-ui'>
		
        <h2>Microthemer UI</h2>
        
        
        <span id="ui-nonce"><?php echo wp_create_nonce('tvr_microthemer_ui_load_styles'); ?></span>
        
        <?php
	
		// show help message
		$this->need_help_notice();
		// show validation reminder
		$this->validate_reminder();
		// ie notice
		$this->ie_notice();
		?>
        
        <div id="stan-vis-tabs">
        	<span id="view-stan">Standard View</span>
        	<a href="#" id="load-visual">Visual View</a>
            <div class="clear"></div>
         </div>
        
        <span id='scroll-pref' rel='<?php echo $this->preferences['auto_scroll']; ?>'></span>
        <span id='auto-visual-pref' rel='<?php echo $this->preferences['load_visual']; ?>'></span>
        <span id='plugin-url' rel='<?php echo $this->thispluginurl; ?>'></span>
        <span id='plugin-trial' rel='<?php echo $this->preferences['buyer_validated']; ?>'></span>
        
        
        <div id="add-new-section">
            <p id='ie-has'><a href='#' class='reveal-add-section prominent-action'>Add New Section</a></p> 
            <?php $this->add_new_section(); ?> 
        </div>            
      
        <form method="post" name="tvr_microthemer_ui_save" id="tvr_microthemer_ui_save" autocomplete="off">
        <?php wp_nonce_field('tvr_microthemer_ui_save'); ?>
        <div class='section-fields' id='section-fields'>
        <?php
        // loop through the sections to create the interface
		$sec_loop_count = 0;
        foreach ( $this->options as $section_name => $array) {
			++$sec_loop_count;
            $section_name = esc_attr($section_name); //=esc
            // if non_section continue
            if ($section_name == 'non_section') {
                continue;
            }
			// disable sections locked by trial
			if (!$this->preferences['buyer_validated'] and $sec_loop_count > 2) {
				$trial_disabled = 'trial-disabled';	
			} else {
				$trial_disabled = 'not-disabled';	
			}
            ?>
            <div id='section-<?php echo $section_name;?>' class='section-wrap <?php echo $trial_disabled; ?>'>
            <h2 class="main-options-heading another-class">
            <?php 
            echo ucwords(str_replace('_', ' ', $section_name)); 
            ?>
            <input type="checkbox" class="export-section" name="export_sections[<?php echo $section_name; ?>]" 
            value="1" rel="off" title="Selectively export this section" /></h2>
            <input type='hidden' class='register-section' name='tvr_mcth[<?php echo $section_name; ?>]' value='' />
            <?php
            $view_state = $this->options['non_section']['view_state'][$section_name]['this'];
			// display section management options
			$this->manage_section($section_name, $array, $view_state);
            ?>
            <div id='<?php echo $section_name; ?>-selectors' class='hidden-options view-state-<?php echo $view_state;?>'>
            <?php 
			// display the section selectors if previously saved open
			if ($view_state == 2) {
				$this->section_selectors($section_name, $array); 
			}
			?>
            </div><!-- end (h2) hidden-options -->
            </div><!-- end section-wrap -->
            <?php
        } // ends foreach for all options
		?>
		</div><!-- end section-fields -->
		<?php $section_count_state = (count($this->options)-1); // subtract 1 for non_section ?>
		<span id="section-count-state" class='section-count-state' rel='<?php echo $section_count_state; ?>'></span>
		<?php if ( $section_count_state > 1 ) { ?>
		<a href='#' id="sections-sortable-button" class='sections-sortable prominent-action'>Enable Sortable Sections</a>
		<?php 
		}
		?>
        
        <!-- keep track of total sections & selectors -->
        <div id="ui-totals-count">
            <p><span>Total Sections:</span><span id="total-sec-count"><?php echo $section_count_state; ?></span></p>
            <p><span>Total Selectors:</span><span id="total-sel-count"><?php echo $this->total_selectors; ?></span></p>
        </div>
        
		<div id='hand-css'>
            <input type="checkbox" class="export-section" name="export_sections[hand_coded_css]" 
                value="1" rel="off" title="Selectively export the hand-coded CSS" />
            <a href='#' class='hand-css-reveal'>Edit Hand-Coded CSS</a>
			<div id='hand-css-area'>
				<textarea id='hand-css-textarea' name='tvr_mcth[non_section][hand_coded_css]' autocomplete="off"><?php 
				//=esc	
				echo htmlentities(stripslashes($this->options['non_section']['hand_coded_css'])); ?></textarea>
			</div>
		</div>
        
        <div id='revisions'>
            <a href='#' rel='<?php echo 'admin.php?page=' . $this->microthemeruipage . 
			'&_wpnonce='.wp_create_nonce('tvr_get_revisions'). '&action=get_revisions'; ?>' id='reveal-revisions' class='reveal-revisions'>Restore Settings From Previous Saves</a>
			<div id='revision-area'></div>
		</div>
		
        <ul id='view-states'>
			<?php
			// every section and selector has a tracker input
			// also use this loop to build section + selector visual view dropdown menus
			// for controlling the display of the sel menu
			$vmenu_sections = '<select name="vmenu-sections" id="vmenu-sections" class="vmenu-sections" 
			title="Change Section" autocomplete="off">
			<optgroup label="Section in Focus">'; 
			// almost exactly the same as orig iframe menu
			$vmenu_selectors = '<select name="vmenu-selectors" id="vmenu-selectors" class="vmenu-selectors" 
			title="Change Selector" autocomplete="off">'; 
			
			$sec_loop_count = 0;
			$sel_total_loop_count = 0;
			foreach ( $this->options as $section_name => $array) {
				++$sec_loop_count;
				$section_name = esc_attr($section_name); //=esc
				$selector_count_state = $this->selector_count_state($array); 
				// if non_section continue
				if ($section_name == 'non_section') {
					continue;
				}
				echo '<li id="strk-'.$section_name.'" class="strk">';
				$section_view_state = $this->options['non_section']['view_state'][$section_name]['this'];
				if ($section_view_state == '' or $section_view_state == 1) { 
					$section_view_state = 0; 
				}
				?>
                <input type='hidden' id='view-state-<?php echo $section_name;?>-this' rel='' title=''
                class='view-state-input section-tracker' name='tvr_mcth[non_section][view_state][<?php echo $section_name;?>][this]' value='<?php echo $section_view_state; ?>' />
                <?php
				// disable sections locked by trial
				if (!$this->preferences['buyer_validated'] and $sec_loop_count > 2) {
					$trial_disabled = 'disabled';	
				} else {
					$trial_disabled = '';	
				}
				// add to section vmenu
				$display_section_name = ucwords(str_replace('_', ' ', $section_name));
				$vmenu_sections.= '<option id="vmenu-'.$section_name .'" class="vmenu-sec-option" '.$trial_disabled.'>'.$display_section_name;
				if ($selector_count_state > 0) {
					 $vmenu_sections.= ' ('.$selector_count_state.')';
				}
				$vmenu_sections.= '</option>';
				// add to selectors vmenu (which is the same as the original iframe menu, but is built with php)
				$vmenu_selectors.= '<optgroup label="'.$display_section_name.'" rel="'.$section_name.'">';
				// add hidden option that doesn't get copied to display menu but allows empty sections to have parent optgroup that gets copied to display 
				$vmenu_selectors.= '<option id="vmenu-'.$section_name .'-this" class="vmenu-sel-option hidden" 
				title=""></option>';
				// loop the CSS selectors if they exist
				if ( is_array($array) ) {
					$sel_loop_count = 0;
					foreach ( $array as $css_selector => $array) {
						++$sel_loop_count;
						$selector_view_state = $this->options['non_section']['view_state'][$section_name][$css_selector];
						// default to 0 and if styles were hidden on last save, they don't load so set tracker to 0
						if ($selector_view_state == '' or $selector_view_state == 1) { 
							$selector_view_state = 0; 
						}
						$labelCss = explode('|', $array['label'])
						?>
						<input type='hidden' id='view-state-<?php echo $section_name;?>-<?php echo $css_selector;?>' 
						class='view-state-input selector-tracker' name='tvr_mcth[non_section][view_state][<?php echo $section_name;?>][<?php echo $css_selector;?>]' rel='<?php echo $labelCss[0];?>' title='<?php echo $labelCss[1];?>' value='<?php echo $selector_view_state; ?>' />
						<?php
						// add to selectors vmenu
						// determine which style groups are active
                        $style_count_state = 0;
                        if ( is_array($array['style_config']) ) {
                            foreach ( $array['style_config'] as $key => $value) {
                                if ($key == $value) {
                                    ++$style_count_state;
                                }
                            }
                        } 
						// disable selectors locked by trial
						if (!$this->preferences['buyer_validated'] and ($sel_loop_count > 3 or $sel_total_loop_count > 5) ) {
							$trial_disabled = 'disabled';	
						} else {
							++$sel_total_loop_count;
							$trial_disabled = '';	
						}
						// add selector vmenu item
						$vmenu_selectors.= '<option id="vmenu-'.$section_name .'-'.$css_selector .'" class="vmenu-sel-option" 
						title="'.$labelCss[1].'" '.$trial_disabled.'>'.$labelCss[0];
						if ($style_count_state > 0) {
							 $vmenu_selectors.= ' ('.$style_count_state.')';
						}
						$vmenu_selectors.= '</option>';
					}
				}
				$vmenu_selectors.= '</optgroup>';
				echo '</li>';
			}
			
			// close vmenus
			$vmenu_sections.= '</optgroup></select>';
			$vmenu_selectors.= '</select>';
			?>
    	</ul>
        </form>
    </div><!-- end tvr-ui -->
    
    <?php // echo 'here' . $vmenu_selectors; ?>
     
    <div id='fixed-menu'>
    
    <img id='loading-gif' src='<?php echo $this->thispluginurl;?>images/loading.gif' />
    <?php
	// display any message that might appear after a page reload (e.g. settings successfully imported)
	if ($this->globalmessage != '') {
		// $page_reload_message =  '<div id="microthemer-notice">' . $this->globalmessage . '</div>'; 
		$page_reload_message =  $this->globalmessage . 
		'<p>
		<a id="hide-notification" href="#">Hide Message</a>
		</p>';
		$style_def = 'style="display:block;"';
	}
	else {
		$page_reload_message = '';
		$style_def = '';
	}
	?>
    <div id='ajax-message' <?php echo $style_def; ?>><?php echo $page_reload_message; ?></div>
    	<div id='fixed-menu-inner'>
            <div class="save-options fixed-subsection">
                <h3>Save Settings</h3>
                <div class='menu-option-wrap'>
                	<form method="post" name="tvr_microthemer_ui_save2" id="tvr_microthemer_ui_save2" autocomplete="off">
                    <?php wp_nonce_field('tvr_microthemer_ui_save2'); ?>
                    <div class='menu-input-wrap'>
                    <input type='checkbox' class='checkbox export-checkbox' name='export_to' value='1' autocomplete="off" /> 
                    Export to Micro Theme
                    </div>
                    <div class='export-option menu-input-wrap'>
                        <select name='export_to_theme' class='export-select' autocomplete="off" 
                        title='<?php echo $this->readable_name($this->preferences['theme_in_focus']);?>'>
                            <option value=''>Select Theme</option>
                            <option value='new'>New</option>
                            <?php
							foreach ($file_structure as $dir => $array) {
								// determine checked
								if ( $this->preferences['theme_in_focus'] == $dir) {
									$selected = 'selected="selected"';
								}
								else {
									$selected = '';
								}
								// under some conditions a blank value can crop up. Not sure what the cause is but this fixes problem.
								if ($dir != '') {
									echo '<option value="'.$dir.'" '.$selected.'>'.$this->readable_name($dir).'</option>';
								}
							}
							?>
                        </select>
                        <div id='export-new-name'>
                        Name:<br />
                        <input type='text' name='export_new_name' value='' maxlength='19' />
                        </div>
                        <div class='only-selected-wrap'>
                        <input type='checkbox' id='only-export-selected' class='checkbox only-export-selected' 
                        name='only_export_selected' value='1' autocomplete="off" disabled="disabled" /> Only Export Selected 
                        </div>
                    </div>
                    <input id="ui-save" name="save" type="submit" value="Save Settings" title="Save Settings (Keyboard Shortcut: Ctrl+S)" 
                    class="button-primary ui-save" rel='<?php 
					echo 'admin.php?page=' . $this->microthemeruipage; ?>' />
                    <input type="hidden" name="action" value="tvr_microthemer_ui_save" />
                    </form>
                 </div>
            </div>
            
            <div class="import-micro fixed-subsection">
            	<h3>Import Settings</h3>
                <form method="post" id="microthemer_ui_settings_import" action="admin.php?page=<?php echo $this->microthemeruipage; ?>" autocomplete="off">
                <div class='menu-option-wrap'>
                <div class='menu-input-wrap'>
                    <?php wp_nonce_field('tvr_import_from_theme'); ?>
                    <select name='tvr_import_from_theme' autocomplete="off" class="import-select">
                            <option value=''>Select Theme</option>
                            <?php
							foreach ($file_structure as $dir => $array) {
								// if theme contains .json file
								if (file_exists($this->micro_root_dir . $dir . '/config.json')) {
									// under some conditions a blank value can crop up. Not sure what the cause is but this fixes problem.
									if ($dir != '') {
										echo '<option value="'.$dir.'">'.$this->readable_name($dir).'</option>';
									}
								}
							}
							?>
                     </select> 
                </div>
                <div id="overwrite-merge" class="menu-input-wrap">
                     &nbsp;<input name="tvr_import_method" type="radio" value="Overwrite" id='ui-import-overwrite' 
                     class="radio ui-import-method" /> Overwrite
                     &nbsp; <input name="tvr_import_method" type="radio" value="Merge" id='ui-import-merge' 
                     class="radio ui-import-method" /> Merge
                     </div>
                     <input name="import_submit" type="submit" value="Import Settings" id='ui-import' class="button-primary ui-import" />  
                </div>  
                </form>
            </div>
            
            <div class="preview-options fixed-subsection">
                <h3>View Site</h3>
                <div class='menu-option-wrap'>
                	<form method="post" autocomplete="off">
                    <?php wp_nonce_field('flexify-ui-options-preview'); ?>
                    <div class='menu-input-wrap previewurl-wrap'>
                        <input id='previewurl' type='text' name='preview_url' autocomplete="off" 
                        title='<?php echo $this->preferences['preview_url']; ?>' 
                        value='<?php
						$site_url = site_url();
						$strpos = strpos($this->preferences['preview_url'], $site_url);
						if ($this->preferences['preview_url'] != '' and ($strpos === 0)) {
							echo esc_attr($this->preferences['preview_url']); 
						}
						else {
							echo site_url();
						}
						?>' /> 
                    </div>
                    <span id='previewStatus'></span>
                    <div class='menu-input-wrap'>
                    <a target='_blank' id='previewcss' class='previewcss'  
                    href='<?php echo $this->micro_root_url.'active-styles.css';?>' title="View the CSS code Microthemer generates">View CSS</a>
                    <input id='save-ui-first' type='checkbox' class='checkbox' name='save_ui_first' 
                    checked='checked' value='1' autocomplete="off" title="Save your current settings before viewing CSS" /> <span title="Save your current settings before viewing CSS">Save First</span>
                    
                    </div>
                    <input id='ajaxUrl' type="hidden" value="<?php 
					$preview_url = trailingslashit(site_url()).'wp-admin/admin.php?page='.$this->microthemeruipage.'&_wpnonce='.wp_create_nonce('tvr_microthemer_ui_preview_url');
					//=esc
					echo esc_url($preview_url); ?>" />
                    <?php /*
                    <input id='previewbutton' type="button" value="View Site" class="button-primary" rel="<?php echo site_url(); ?>" />   
					*/
					?>                 
                    </form>
            	</div>
            </div>
            
            <div class="reset-options fixed-subsection">
               <a href="<?php echo trailingslashit(site_url()).'wp-admin/admin.php?page='.$this->microthemeruipage.'&_wpnonce='.wp_create_nonce('tvr_microthemer_ui_reset');?>&action=tvr_ui_reset" id="ui-reset" class="reset ui-reset" 
              title="Reset UI to default empty sections">Reset</a>
              
              <a href="<?php echo trailingslashit(site_url()).'wp-admin/admin.php?page='.$this->microthemeruipage.'&_wpnonce='.wp_create_nonce('tvr_microthemer_clear_styles');?>&action=tvr_clear_styles" id="clear-styles" class="clear-styles" 
              title="Clear all style definitions - sections and selectors will remain intact">Clear</a>
              
              
              <a class="speed-up" id='speed-up' href='#' title="The more selectors you edit, the more data is loaded. This will collapse all open sections/selectors, save your settings, reload the interface, and speed things up (if things have slowed down).">SpeedUp</a>
          </div>
            
            <?php $this->plugin_menu(); ?>
            
            <div id="toggle-fixed-menu" class="showing" title="Open/Collapse Side Options"></div>
             
        </div><!-- fixed-menu-inner -->
      
      	
        
  	</div><!-- fixed-menu -->
    
    <!-- html templates -->
    <form action='#' name='dummy'>
	<?php 
    // define template for section ?>
    <div id='section-template' class='template section-template section-wrap'>
        <h2 class="main-options-heading"><input class="export-section" type="checkbox" title="Selectively export this section" rel="off" value="1" name="export_sections[selector_section]"></h2>
        <input type='hidden' class='register-section' name='tvr_mcth[selector_section]' value='' />
        <?php $this->manage_section('selector_section', $array); ?>
        <div id='selector_section-selectors' class='hidden-options'>
        	<div class="add-sel-wrap">
            	<a href='#' class='reveal-add-selector prominent-action'>Add New Selector</a>
                <?php $this->add_new_selectors('selector_section'); ?>
            </div>
            <div class='selector-fields'>
            </div><!-- ends selector-fields  -->
        </div><!-- end (h2) hidden-options -->
    </div><!-- end section-wrap -->
    
    <?php // define template for new selector ?>
    <div id='selector-template' class='selector-wrap template selector-template'>
        <h3 class='sub-options-heading'></h3>
        <input type='hidden' class='register-selector' name='tvr_mcth[selector_section][selector_css]' value='' />
         <?php $this->manage_selector('selector_section', 'selector_css', '', ''); ?>
        <div class='hidden-options'>
            <input type='hidden' class='selector-label' name='' value='' />
            <?php // loop through property groups
            foreach ($this->propertyoptions as $property_group_name => $array) {
                ?>
                <div class="row">
                    <div class='main-label'>
                    <input type='hidden' class='register-group' 
                    name='tvr_mcth[selector_section][selector_css][styles][<?php echo $property_group_name; ?>]' value='' />
                    <input type='checkbox' class='style-config checkbox' 
                    name='tvr_mcth[selector_section][selector_css][style_config][<?php echo $property_group_name; ?>]' 
                    value='<?php echo $property_group_name; ?>' />
                    <?php 
					if ($property_group_name != 'forecolor') {
						 echo ucwords($property_group_name); 
					}
					else {
						echo 'Color';
					}
					?>:
                    <span class='property-group-state' rel='off'></span>
                    
                    <?php 
					// if CSS3 - include option for turning pie off (on by default)
					if ($property_group_name == 'CSS3') {
						?>
                        <div class='pie-switch hide'>
                        <?php echo '<a class="snippet" title="click for info" 
						href="'.$this->thispluginurl.'includes/css-reference.php?snippet=pie">CSS3 PIE:</a>'; ?>
                        <input type='radio' autocomplete="off" class='radio' name="tvr_mcth[selector_section][selector_css][pie]" 
                        value='1' checked="checked" /> On<br />
                         <input type='radio' autocomplete="off" class='radio' name="tvr_mcth[selector_section][selector_css][pie]" 
                         value='0' /> Off
                        </div>
                        <?php	
					}
					?>
                    
                    </div>
                    <div id='group-selector_section-selector_css-<?php echo $property_group_name;?>' 
                    class='tvr-ui-right'></div>
                </div><!-- end row -->
                <?php
            }
            ?>  
        </div><!-- end (h3) hidden-options -->
    </div><!-- end selector-template -->
    
    <?php // define property group templates
    foreach ($this->propertyoptions as $property_group_name => $property_group_array) {
        ?>
        <div id='property-template-<?php echo $property_group_name; ?>' class='property-fields template property-template-<?php echo $property_group_name; ?> property-<?php 
        echo $property_group_name; ?>'>	
        <?php 
        // loop through each property in a group (always an array)
        foreach ($property_group_array as $property => $value) {
            // format input fields
            $this->resolve_input_fields('selector_section', 'selector_css', $property_group_array, $property_group_name, $property, '');
        } // ends foreach property
        ?>
        </div><!-- end property-fields -->
        <?php
    }
	?>
    </form>
    
    <?php 
    // define template for selector wizard ?>
    <div id="intelli-wrap-template" class="template" title="Selector Wizard">
    	<!--<h3>Selector Wizard</h3>-->
        <div class="refine-target">
        	<div class="refine-target-controls">
                <span class="tvr-prev-sibling" title="Move to Previous Sibling Element"></span>
                <div class="updown-wrap">
                    <span class="tvr-parent" title="Move to Parent Element"></span>
                    <span class="tvr-child" title="Move to Child Element"></span>
                </div>
                <span class="tvr-next-sibling" title="Move to Next Sibling Element"></span>
            </div>
            <div class="refine-target-html"><p><b>Element HTML: </b><br /><span class="display-html"></span></p></div> 
        </div>
    	<select></select>
    </div>
    <!-- end html templates -->
     
    <?php 
	/*** *** Build Visual View *** ***/
	?>
    <div id="visual-view" <?php if (!$this->preferences['load_visual']) { echo 'style="display:none;"'; } ?> >
    	
        <div id="v-top-controls">
        	<div id="status-board"></div>
        </div>
        
        <div id="v-frontend" title="Double-click an element to create editing options for it">
        	<?php
			// resolve iframe url
			$site_url = site_url();
			$strpos = strpos($this->preferences['preview_url'], $site_url);
			if ($this->preferences['preview_url'] != '' and ($strpos === 0)) {
				$iframe_url = esc_attr($this->preferences['preview_url']); 
			}
			else {
				$iframe_url = site_url();
			}
			// maybe use src="includes/holding.html" with an animated GIF saying "Loading Website Frontend"
			?>
        	<iframe id="viframe" frameborder="0" name="viframe"  
            rel="<?php echo $iframe_url; ?>" src="<?php echo $this->thispluginurl; ?>includes/place-holder2.html"></iframe>
        </div>
  		

        <div id="v-bottom-controls">
        	<div id="progress-indicator" class="v-bottom-button" title="Nothing in Progress">
            </div>
            <div id="scroll-buttons" class="v-bottom-button">
            	<span id="vb-focus-prev" title="Go To Previous Selector"></span>
                <span id="vb-focus-next" title="Go To Next Selector"></span>
            </div>
            <div id="section-filter" class="v-bottom-button v-drop-down">
            	<?php echo $vmenu_sections; ?>
                <span id="vb-add-section" title="Add New Section"></span>
            </div>
            <div id="selector-filter" class="v-bottom-button v-drop-down">
            	<?php echo $vmenu_selectors; ?>
                <!-- hiding select menu options/optgroups does not work across browsers - cloning optgroup into display menu is work around -->
                <select name="vmenu-selectors-display" id="vmenu-selectors-display" class="vmenu-selectors-display" title="Change Selector"></select>
                <span id="vb-add-selector" title="Add New Selector to Section in Focus"></span>
            </div>
            <div id="toggle-editing" class="v-bottom-button on" title="Show/Hide Editing Options"></div>
            <div id="toggle-highlighting" class="v-bottom-button on" title="Show/Hide Highlighting"></div>
            <div id="tvr-popup-menu" class="v-bottom-button" title="More Options">
            	<div id="tvr-popup-wrap">
                    <div id="toggle-sortable-sections" class="v-bottom-button" title="Sort/Manage Sections"></div>
                    <div id="toggle-revisions" class="v-bottom-button" title="Restore from Previous 20 Saves"></div>
                    
                    <div id="toggle-sortable-selectors" class="v-bottom-button" title="Sort/Manage Selectors"></div>
                    <div id="toggle-raw-css" class="v-bottom-button" title="Enter Raw CSS Code"></div>
                    
                    <div id="toggle-auto-save" class="v-bottom-button" rel="<?php echo esc_attr($this->preferences['auto_save']); ?>" 
                    title="Enable/Disable Auto-save"></div>
                    <div id="show-tutorials" class="v-bottom-button help-trigger"
                     rel="<?php echo $this->thispluginurl.'includes/help-videos.php'; ?>" title="Display In-program Docs"></div>
                    
                </div>
            </div>
           <div id="close-visual" class="v-bottom-button" title="Close Visual View"> </div>  
            
        </div>
        
        <div id="v-side-controls">
        
        </div>
	
    </div>
    
</div><!-- end wrap -->