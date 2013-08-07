<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}
/*** 
pre content php 
***/
// get micro-themes dir file structure - unset $this->file_structure so it can be populated with fresh scan
unset($this->file_structure);
$file_structure = $this->dir_loop($this->micro_root_dir);
?>

<div class='wrap'>
	<div id='tvr-preferences'>
		<?php 
		// display message
		if ($this->globalmessage != '') {
			echo '<div id="microthemer-notice">' . $this->globalmessage . '</div>'; 
		}
		?>
       <h2>Microthemer Preferences</h2>
       
       <?php
	   // show help message
	   $this->need_help_notice();
	   // show validation reminder
	   $this->validate_reminder();
	   // ie notice
		$this->ie_notice();
	   ?>
                 
       <form name='preferences_form' method="post" class='float-form' autocomplete="off"
       action="admin.php?page=<?php echo $this->preferencespage;?>" >
       		<?php wp_nonce_field('tvr_preferences_submit'); ?>
            <h3>General</h3>
            <p style="display:none;"><label>Currently Active Theme:</label>
            <select name="tvr_preferences[active_theme]" autocomplete="off">
            	<option value=''>Nothing Active</option>
                <option value='customised'
                <?php 
				if ($this->preferences['active_theme'] == 'customised') { 
					echo 'selected="selected"';
				} 
				?>
                >Custom Configuration</option>
            	<?php
				if (is_array($file_structure)) {
					foreach ($file_structure as $dir => $array) {
						// highlight if filtered
						if ($this->preferences['active_theme'] == $dir) {
							$selected = 'selected="selected"';
						}
						else {
							$selected = '';
						}
						if ($dir != '') {
							echo "<option value='$dir' $selected>".$this->readable_name($dir)."</option>";
						}
					}
				}
				?>
            </select>
            <input type='hidden' name='prev_active_theme' value='<?php echo $this->preferences['active_theme'];?>' />
            </p>
            
            <p><label>Show links to help videos etc: </label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[need_help]' value='1'
            <?php
			if ($this->preferences['need_help'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> Yes
             </p>
             <p><label>&nbsp;</label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[need_help]' value='0' 
            <?php
			if ($this->preferences['need_help'] == '0') {
				echo 'checked="checked"';
			}
			?>
             /> No
            </p>
            
             <p><label>Disable WP Theme's style.css: </label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[disable_parent_css]' value='1'
            <?php
			if ($this->preferences['disable_parent_css'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> Yes
             </p>
             <p><label>&nbsp;</label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[disable_parent_css]' value='0' 
            <?php
			if ($this->preferences['disable_parent_css'] == '0') {
				echo 'checked="checked"';
			}
			?>
             /> No
            </p>
            
            <p><label>WordPress Frontend Admin Bar: </label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[admin_bar]' value='1'
            <?php
			if ($this->preferences['admin_bar'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> Show
             </p>
             <p><label>&nbsp;</label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[admin_bar]' value='0' 
            <?php
			if ($this->preferences['admin_bar'] == '0') {
				echo 'checked="checked"';
			}
			?>
             /> Don't Show
            </p>
            
            <p><label>jQuery Source: </label>
            <?php 
			/* WORDPRESS WOULDN'T ALLOW THIS FEATURE
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[jquery_source]' value='plugin'
            <?php
			if ($this->preferences['jquery_source'] == 'plugin') {
				echo 'checked="checked"';
			}
			?>
             /> Load plugin version of jQuery
             </p>
             <p><label>&nbsp;</label>
			 */
			 ?>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[jquery_source]' value='native' 
            <?php
			// if ($this->preferences['jquery_source'] == 'native') {
				echo 'checked="checked"';
			// }
			?>
             /> WordPress (fixed option as of version 2.2.2)
            </p>
                        
            <p><label>PHP Safe Mode Warning: </label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[safe_mode_notice]' value='1'
            <?php
			if ($this->preferences['safe_mode_notice'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> Show
             </p>
             <p><label>&nbsp;</label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[safe_mode_notice]' value='0' 
            <?php
			if ($this->preferences['safe_mode_notice'] == '0') {
				echo 'checked="checked"';
			}
			?>
             /> Don't Show
            </p>
            
            <p><label>Add "first" and "last" classes to Menus: </label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[first_and_last]' value='1'
            <?php
			if ($this->preferences['first_and_last'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> Yes (only works with Custom Menus)
             </p>
             <p><label>&nbsp;</label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[first_and_last]' value='0' 
            <?php
			if ($this->preferences['first_and_last'] == '0') {
				echo 'checked="checked"';
			}
			?>
             /> No
            </p>
           
            <br />
            <h3>Microthemer UI</h3>
            
            <p><label>Auto-load Visual View: </label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[load_visual]' value='1' 
             <?php
			if ($this->preferences['load_visual'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> Yes
            </p>
            <p><label>&nbsp;</label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[load_visual]' value='0' 
             <?php
			if ($this->preferences['load_visual'] == '0') {
				echo 'checked="checked"';
			}
			?>
             /> No
            </p>
            
            <p><label>Gzip Microthemer UI Page: </label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[gzip]' value='1' 
             <?php
			if ($this->preferences['gzip'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> Yes
            </p>
            <p><label>&nbsp;</label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[gzip]' value='0' 
             <?php
			if ($this->preferences['gzip'] == '0') {
				echo 'checked="checked"';
			}
			?>
             /> No
            </p>
            <p><label>Auto-add <b>!important</b> to CSS Styles: </label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[css_important]' value='1' 
             <?php
			if ($this->preferences['css_important'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> Yes
            </p>
            <p><label>&nbsp;</label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[css_important]' value='0' 
             <?php
			if ($this->preferences['css_important'] == '0') {
				echo 'checked="checked"';
			}
			?>
             /> No
            </p>
            <p><label>View Site URL: </label>
            <input type='text' autocomplete="off" name='tvr_preferences[preview_url]' 
            value='<?php echo esc_attr($this->preferences['preview_url']); ?>' />
            </p>
            
            <p><label>Filter Background Image List:<br /><br />
            <span class='tipbit'>Hold Control and Click or <br />Hold Shift and Click to <br />Select Multiple Items</span></label>
            <select multiple='multiple' name="tvr_preferences[image_filter][]" size='15' class='multiple' autocomplete="off">
            	<option value='' 
                <?php 
				if ($this->preferences['image_filter'][0] == '') {
					echo 'selected="selected"';
				}
				?>
                >Don't Filter Out Any Themes</option>
            	<?php
				if (is_array($file_structure)) {
					foreach ($file_structure as $dir => $array) {
						// highlight if filtered
						$array_key = array_search($dir, $this->preferences['image_filter']);
						if ($array_key != false or $array_key === 0) {
							$selected = 'selected="selected"';
						}
						else {
							$selected = '';
						}
						if ($dir != '') {
							echo "<option value='$dir' $selected>Exclude: ".$this->readable_name($dir)."</option>";	
						}
					}
				}
				?>
            </select>
            </p>
            
            <p><label>When CSS3 Styles Are Defined</label>
            <input type='radio' class='radio' name='tvr_preferences[auto_relative]' value='1' 
             <?php
			if ($this->preferences['auto_relative'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> always add <i>position:relative</i> to the element in question.
            </p>
            <p id='auto-relative-exp'>
            This is a fixed setting. But worth notifying you about here so that you can override it on individual elements. To stop Microthemer from automatically adding <i>position:relative</i> to an element that you have applied CSS3 styles to, simply set <i>position:static</i> on it (or set CSS3 PIE to off).
            </p>
            
            <p><label>Internet Explorer Notice: </label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[ie_notice]' value='1'
            <?php
			if ($this->preferences['ie_notice'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> Show
             </p>
             <p> <label>&nbsp;</label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[ie_notice]' value='0' 
            <?php
			if ($this->preferences['ie_notice'] == '0') {
				echo 'checked="checked"';
			}
			?>
             /> Don't Show
            </p>
            
           <p><label>Auto-scroll to Selector after editing: </label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[auto_scroll]' value='1'
            <?php
			if ($this->preferences['auto_scroll'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> Enable (happens when you close the Visual View)
             </p>
             <p><label>&nbsp;</label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[auto_scroll]' value='0' 
            <?php
			if ($this->preferences['auto_scroll'] == '0') {
				echo 'checked="checked"';
			}
			?>
             /> Disable
            </p> 
            
            <p><label>Enable Auto-save by default: </label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[auto_save]' value='1'
            <?php
			if ($this->preferences['auto_save'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> Enable
             </p>
             <p><label>&nbsp;</label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[auto_save]' value='0' 
            <?php
			if ($this->preferences['auto_save'] == '0') {
				echo 'checked="checked"';
			}
			?>
             /> Disable
            </p> 
            
            <p><label>Editing Options Transparency: </label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[trans_editing]' value='1'
            <?php
			if ($this->preferences['trans_editing'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> Enable transparency when mouse cursor moves away from options
             </p>
             <p><label>&nbsp;</label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[trans_editing]' value='0' 
            <?php
			if ($this->preferences['trans_editing'] == '0') {
				echo 'checked="checked"';
			}
			?>
             /> Disable transparency when mouse cursor moves away from options
            </p> 
            
            <p><label>Selector Wizard Transparency: </label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[trans_wizard]' value='1'
            <?php
			if ($this->preferences['trans_wizard'] == '1') {
				echo 'checked="checked"';
			}
			?>
             /> Enable transparency when mouse cursor moves off Selector Wizard
             </p>
             <p><label>&nbsp;</label>
            <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[trans_wizard]' value='0' 
            <?php
			if ($this->preferences['trans_wizard'] == '0') {
				echo 'checked="checked"';
			}
			?>
             /> Disable transparency when mouse cursor moves off Selector Wizard
            </p>
            
            <br />
            <p><input name="tvr_preferences_submit" type="submit" value="Save Preferences" class="button-primary submit" /></p>
                  
                  
             </form>
             
             
            
            <?php 
			if ($this->preferences['buyer_email'] != 'mojo' and $this->preferences['buyer_email'] != 'inky') {
				?>
                
                <form name='tvr_validate_form' method="post" class='float-form' autocomplete="off"
       action="admin.php?page=<?php echo $this->preferencespage;?>" >
                <a name='validate'></a>
                <br />
                <h3>Disable Free Trial Mode - Unlock The Full Program!</h3>
                <p>To disable Free Trial Mode and unlock the full program, please enter the email address that Themeover sent your Microthemer download link to. If you purchased Microthemer from CodeCanyon, please send us a "Validate my email" message via the contact form on the right hand side of <a target='_blank' href='http://codecanyon.net/user/themeover '>this page</a> (you will need to log in to CodeCanyon first).</p>
                <p><label>Your Email Address: </label>
                <input type='text' autocomplete="off" name='tvr_preferences[buyer_email]' 
                value='<?php echo esc_attr($this->preferences['buyer_email']); ?>' />
                </p>
                <p><label>Manually Check for Updates Now: </label>
                <input type='checkbox' autocomplete="off" class='checkbox' name='tvr_validate_one_off[manual_update]' value='1' /> Yes
                 </p>
                <p><b>Note:</b> Themeover will record your domain name when you submit your email address for license verification purposes. Microthemer can be used on multiple domains, but you must own the domains - unless you purchase a Developer License, which allows you to use Microthemer on Client Websites.
                <a target='_blank' href='http://themeover.com/microthemer/'>Purchase a Standard or Developer License for Microthemer</a>.</p>
                
                <input name="tvr_validate_submit" type="submit" value="Validate Purchase" class="button-primary submit" />
                
                </form>
                <?php
			}
			?>
          
        
            
   </div><!-- tvr-preferences -->
   
   <div id='preferences-menu-wrap'>
   <div id='fixed-menu'>
    	<div id='fixed-menu-inner'>      
           <div>
               <h3>Save Preferences</h3>
               <div class='menu-option-wrap'>
                  
               </div>
           </div>
           
           <div>
               <h3>Server Settings</h3>
               <div id='tvr-server-settings' class='menu-option-wrap'>
                  <?php $this->server_info(); ?>
               </div>
           </div>
           
           <?php $this->plugin_menu(); ?> 
      
    </div><!-- fixed-menu-inner -->
    
    <!-- reset preferences -->
        <form method="post">
            <?php wp_nonce_field('tvr_preferences_reset'); ?>
            <p class="submit reset-options">
              <input name="tvr_preferences_reset" type="submit" value="Reset Preferences" class='reset prominent-action' />
            </p>
        </form>
    
   </div>
   <!-- fixed-menu-inner -->
   </div>
   <!-- preferences-menu-wrap -->
   
</div><!-- wrap -->