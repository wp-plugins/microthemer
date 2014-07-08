<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.');
}

// border-style was removed for separate 4 values
if ($property == 'border_style') {
    //return
}

// add media query stem for form fields if necessary
if ($con == 'mq') {
	$mq_stem = '[non_section][m_query]['.$key.']';
	$imp_key = '[m_query]['.$key.']';
    if ($property_group_name == 'border'){
        $border_style_val = $this->options['non_section']['important']['m_query'][$key][$section_name][$css_selector]['styles']['border']['border_style'];
    } else {
        $border_style_val = false;
    }

    if (!empty($this->options['non_section']['important']['m_query'][$key][$section_name][$css_selector][$property_group_name][$property])) {
        $important_val = $this->options['non_section']['important']['m_query'][$key][$section_name][$css_selector][$property_group_name][$property];
    } else {
        $important_val = '';
    }
} else {
    $mq_stem = '';
    $imp_key = '';
    if ($property_group_name == 'border'){
        $border_style_val = $this->options[$section_name][$css_selector]['styles']['border']['border_style'];

    } else {
        $border_style_val = false;
    }
    if ( !empty($this->options['non_section']['important'][$section_name][$css_selector][$property_group_name][$property])) {
        $important_val = $this->options['non_section']['important'][$section_name][$css_selector][$property_group_name][$property];
    } else {
        $important_val = '';
    }
}

// 4 values for border style should use legacy border-style if it was set
/*if (!empty($border_style_val) and (
        $property == 'border_top_style' or
        $property == 'border_right_style' or
        $property == 'border_bottom_style' or
        $property == 'border_left_style'
    )) {
    $value = $border_style_val;
}*/

if (!empty($value) or $value === '0') {
    $man_class = ' manual-val';
} else {
    $man_class = '';
}
?>
<div class='field-wrap clearfix <?php echo $man_class; ?>
	<?php
	// give input a common css class for jQuery traversing
	$property_input_class = 'property-input';

	// if the input is wider than normal add a span class
	if (isset($this->propertyoptions[$property_group_name][$property]['span'])) {
		echo ' input-span-'.$this->propertyoptions[$property_group_name][$property]['span'];
	}
	// give last-field class if last
	if (
        !empty($this->propertyoptions[$property_group_name][$property]['pos']) and
        $this->propertyoptions[$property_group_name][$property]['pos'] == 'last') {
		echo ' last-field';
	}
	if ($property == 'background_position_x' or $property == 'background_position_y') {
		echo ' bg-position-custom';
		// hide if background positon isn't set to custom
		if ($property_group_array['background_position'] != 'custom') {
			echo ' bg-position-hide';
		}
	}
	if ($property == 'google_font') {
		echo ' tvr-font-custom';
		// hide if background positon isn't set to custom
		if ($property_group_array['font_family'] != 'Google Font...') {
			echo ' google-font-hide';
		}
	}
	?>'>      
    <label><?php
	// provide colorbox image slider for bg images
	if (
        !empty($this->propertyoptions[$property_group_name][$property]['bg_image']) and
        $this->propertyoptions[$property_group_name][$property]['bg_image'] == 1) {
		if ( !empty($value)) {
			$bg_url = $this->micro_root_url . $value;
		}
		else {
            if (!empty($this->first_bg_url)) {
                $bg_url = $this->first_bg_url;
            } else {
                $bg_url = '';
            }

		}
		echo '<a href="#" class="view-bg-images" rel="'.$this->micro_root_url.'" alt="'.$bg_url.'"  title="'.$value.'">view images&nbsp;</a>';
	}
	// allow user to edit their google font
	if ($property == 'google_font') {
		echo '<a href="'.$this->thispluginurl.'includes/fonts-api.php" class="snippet g-font"  title="Edit Google Font">Change Font</a>';
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
		if ($important_val == 1) {
			echo '<input class="important-tracker" type="hidden" 
			name="tvr_mcth[non_section][important]'.$imp_key.'['.$section_name.']['.$css_selector.']['.$property_group_name.']['.$property.']" value="1" />';
			$class_rel = 'on';	
		}
		else {
			$class_rel = 'off';	
		}
		// don't show i on all css3 props and text shadow
		if (
            empty($this->propertyoptions[$property_group_name][$property]['hide imp'])) {
			?>
			<span class="important-toggle tvr-toggle-<?php echo $class_rel; ?> imp-<?php echo $con; ?> " title="Click to add/remove !important declaration" rel="<?php echo $class_rel; ?>">i</span>
			<?php
		}
	}
	 
	
	// if select
	if (
        !empty($this->propertyoptions[$property_group_name][$property]['type']) and
        $this->propertyoptions[$property_group_name][$property]['type'] == 'select') {
		?>
        <select class='<?php echo $property_input_class; 
		// add bg-image class if appropriate
		if (
            !empty($this->propertyoptions[$property_group_name][$property]['bg_image']) and
            $this->propertyoptions[$property_group_name][$property]['bg_image'] == 1) {
			echo ' bg-image-select';
		}
		// bg-position-select class
		elseif ($property == 'background_position') {
			echo ' bg-position-select';
		}
		// bg-position-select class
		elseif ($property == 'font_family') {
			echo ' tvr-font-select';
		}
		?>' autocomplete="off" 
        name="tvr_mcth<?php echo $mq_stem; ?>[<?php echo $section_name; ?>][<?php echo $css_selector;?>][styles][<?php echo $property_group_name;?>][<?php echo $property; ?>]">
        <option value=''></option>
        <?php
		// dynamic background image list 
		if (
            !empty($this->propertyoptions[$property_group_name][$property]['bg_image']) and
            $this->propertyoptions[$property_group_name][$property]['bg_image'] == 1) {
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
                $prev_dir = '';
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
		if (!empty($this->propertyoptions[$property_group_name][$property]['rel'])) {
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
        if (
            !empty($this->propertyoptions[$property_group_name][$property]['picker']) and
            $this->propertyoptions[$property_group_name][$property]['picker'] == '1' ) {
            echo ' color';
        }
        
        ?>' 
        name="tvr_mcth<?php echo $mq_stem; ?>[<?php echo $section_name; ?>][<?php echo $css_selector; ?>][styles][<?php echo $property_group_name; ?>][<?php echo $property; ?>]" value="<?php echo $value; ?>" /> 
        <?php	
        }

    $comp_class = 'comp-style cprop-' . str_replace('_', '-', $property);
    if (!empty($value) or $value === '0') {
       $comp_class.= ' hidden';
    }
    ?>
    <span class="<?php echo $comp_class; ?>"></span>
    <span class="comp-mixed-table">
        <h5 title="Hover over the rows to highlight the individual elements on the page">Page Analysis</h5>
        <table>
            <thead>
            <tr>
                <th><i>n</i></th>
                <th>Value</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </span>
</div><!-- end field-wrap -->
<?php if (
    !empty($this->propertyoptions[$property_group_name][$property]['linebreak']) and
    $this->propertyoptions[$property_group_name][$property]['linebreak'] == '1' ) {
		echo '<div class="clear"></div>';
	}

?>
