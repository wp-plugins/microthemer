<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}
?>
<div class='field-wrap 
	<?php
	// give input a common css class for jQuery traversing
	$property_input_class = 'property-input';
	
	// if the input is wider than normal add a span class
	if (isset($this->propertyoptions[$property_group_name][$property]['span'])) {
		echo ' input-span-'.$this->propertyoptions[$property_group_name][$property]['span'];
	}
	// give last-field class if last
	if ($this->propertyoptions[$property_group_name][$property]['pos'] == 'last') {
		echo ' last-field';
	}
	if ($property == 'background_position_x' or $property == 'background_position_y') {
		echo ' bg-position-custom';
		// hide if background positon isn't set to custom
		if ($property_group_array['background_position'] != 'custom') {
			echo ' bg-position-hide';
		}
	}
	?>'>      
    <label><?php
	// provide colorbox image slider for bg images
	if ($this->propertyoptions[$property_group_name][$property]['bg_image'] == 1) {
		if ($value != '') {
			$bg_url = $this->micro_root_url . $value;
		}
		else {
			$bg_url = $this->first_bg_url;
		}
		echo '<a href="#" class="view-bg-images" rel="'.$this->micro_root_url.'" alt="'.$bg_url.'"  title="'.$value.'">view images&nbsp;</a>';
	}
	?>
	
	<?php
    if (isset($this->propertyoptions[$property_group_name][$property]['icon'])) {
		?><img src='<?php echo $this->thispluginurl.'images/'.$this->propertyoptions[$property_group_name][$property]['icon']; ?>.gif'width='20' height='20' /><?php	
	}
	echo '<a class="snippet" title="click for info" href="'.$this->thispluginurl.'includes/css-reference.php?snippet='.$property.'&prop_group='.$property_group_name.'">' . $this->propertyoptions[$property_group_name][$property]['label'] . '</a>';
	?>
    
    </label>

    <br />
    
    <?php
	if ($property != 'background_position_x' and $property != 'background_position_y') {
		// check status of !important - only create dom element if positive result (optimisation purposes)
		if ($this->options['non_section']['important'][$section_name][$css_selector][$property_group_name][$property] == 1) {
			echo '<input class="important-tracker" type="hidden" 
			name="tvr_mcth[non_section][important]['.$section_name.']['.$css_selector.']['.$property_group_name.']['.$property.']" value="1" />';
			$class_rel = 'on';	
		}
		else {
			$class_rel = 'off';	
		}
		
		// don't show i on all css3 props
		if ($property_group_name != 'CSS3' 
		or $property == 'gradient_c'
		or $property == 'radius_bottom_left'
		or $property == 'box_shadow_blur') { 
			?>
			<span class="important-toggle tvr-toggle-<?php echo $class_rel; ?>" title="Click to add/remove !important declaration" rel="<?php echo $class_rel; ?>">i</span>
			<?php
		}
	}
	 
	
	// if select
	if ($this->propertyoptions[$property_group_name][$property]['type'] == 'select') {
		?>
        <select class='<?php echo $property_input_class; 
		// add bg-image class if appropriate
		if ($this->propertyoptions[$property_group_name][$property]['bg_image'] == 1) {
			echo ' bg-image-select';
		}
		// bg-position-select class
		elseif ($property == 'background_position') {
			echo ' bg-position-select';
		}
		?>' autocomplete="off" 
        name="tvr_mcth[<?php echo $section_name; ?>][<?php echo $css_selector;?>][styles][<?php echo $property_group_name;?>][<?php echo $property; ?>]">
        <option value=''></option>
        <?php
		// dynamic background image list 
		if ($this->propertyoptions[$property_group_name][$property]['bg_image'] == 1) {
			// add none option
			echo '<option value="none"';
			if ($value == 'none') {
				echo ' selected="selected"';
			}
			echo '>none</option>';
			$count = 0;
			foreach ($this->filtered_images as $dir => $array) {
				// sort the background images alphabetically
				sort($array);
				foreach ($array as $file) {
					$dir = esc_attr($dir);
					$optgroup = $this->readable_name($dir);
					$file = esc_attr($file);
					$menu_value = $dir . '/' . $file;
					// determine need for theme header
					if ($prev_dir != $dir) {
						if ($count > 0) { 
							echo '</optgroup>'; 
						}
						echo "<optgroup label='$optgroup'>";
					}
					// determine selected
					if ($value == $menu_value) {
						$selected = 'selected="selected"';
					}
					else {
						$selected = '';
					}
					// output option
					echo "<option value='$menu_value' class='bg-image-option' $selected>$file</option>";
					++$count;
					$prev_dir = $dir;
				}
			}
			echo '</optgroup>';
		}
		// normal property options list
		else {
			foreach ($this->propertyoptions[$property_group_name][$property]['select_options'] as $dropdown_value) {
				// determine checked
				if ($value == $dropdown_value) {
					$checked = 'selected="selected"';
				}
				else {
					$checked = '';
				}
				
				?>
				<option value="<?php echo $dropdown_value; ?>" <?php echo $checked; 
				// display font on screen if font-family
				if ($property == 'font_family') {
					echo 'style="font-size:12px;font-family:'.str_replace('cus-#039;', '\'', $dropdown_value).'"';
				}
				?>>
				<?php echo str_replace('cus-#039;', '', $dropdown_value); ?></option>
				<?php 
			}
		}
		?>
        </select>
        <?php
	}
	
	// if text (default)
	else {
		
		// check if input is eligable for autofill
		if ($this->propertyoptions[$property_group_name][$property]['rel'] != '') {
			$autofill_class = 'autofillable ';
			$autofill_rel = 'rel="'.$this->propertyoptions[$property_group_name][$property]['rel'].'"';
		}
		else {
			$autofill_class = '';
			$autofill_rel = '';
		}
		?>
        <input type="text" autocomplete="off" <?php echo $autofill_rel; ?> 
        class='<?php echo $autofill_class . $property_input_class; 
        // check if color picker needed
        if ( $this->propertyoptions[$property_group_name][$property]['picker'] == '1' ) {
            echo ' color';
        }
        
        ?>' 
        name="tvr_mcth[<?php echo $section_name; ?>][<?php echo $css_selector; ?>][styles][<?php echo $property_group_name; ?>][<?php echo $property; ?>]" value="<?php echo $value; ?>" /> 
        <?php	
        }
	?>
    
</div><!-- end field-wrap -->
<?php if ( $this->propertyoptions[$property_group_name][$property]['linebreak'] == '1' ) {
		echo '<div class="clear"></div>';
	}
?>