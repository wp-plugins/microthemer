<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.');
}

// add media query stem for form fields if necessary
$sel_imp_array = array();
$styles = array();
if ($con == 'mq') {
    if (!empty($this->options['non_section']['m_query'][$key][$section_name][$css_selector]['styles'])){
        $styles = $this->options['non_section']['m_query'][$key][$section_name][$css_selector]['styles'];
    }
	$mq_stem = '[non_section][m_query]['.$key.']';
	$imp_key = '[m_query]['.$key.']';
    $mq_extr_class = '-'.$key;
    // get the important val
    if (!empty($this->options['non_section']['important']['m_query'][$key][$section_name][$css_selector][$property_group_name][$property])) {
        $important_val = $this->options['non_section']['important']['m_query'][$key][$section_name][$css_selector][$property_group_name][$property];
    } else {
        $important_val = '';
    }
    // save the general selector important array for querying if legacy values are discovered
    if (!empty($this->options['non_section']['important']['m_query'][$key][$section_name][$css_selector])){
        $sel_imp_array = $this->options['non_section']['important']['m_query'][$key][$section_name][$css_selector];
    }

} else {
    if (!empty($this->options[$section_name][$css_selector]['styles'])){
        $styles = $this->options[$section_name][$css_selector]['styles'];
    }
    $mq_stem = '';
    $imp_key = '';
    $mq_extr_class = '-all-devices';
    // get the important val
    if ( !empty($this->options['non_section']['important'][$section_name][$css_selector][$property_group_name][$property])) {
        $important_val = $this->options['non_section']['important'][$section_name][$css_selector][$property_group_name][$property];
    } else {
        $important_val = '';
    }
    // save the general selector important array for querying if legacy values are discovered
    if (!empty($this->options['non_section']['important'][$section_name][$css_selector])){
        $sel_imp_array = $this->options['non_section']['important'][$section_name][$css_selector];
    }

}


// check if legacy value for prop exists
$legacy_adjusted = $this->populate_from_legacy_if_exists($styles, $sel_imp_array, $property);
if ($legacy_adjusted['value']){
    $value = $legacy_adjusted['value'];
    $important_val = $legacy_adjusted['imp'];
}

/***
 * get variables from the config file
 */

// field class
$field_class = '';
if (!empty($this->propertyoptions[$property_group_name][$property]['field-class']) ) {
    $field_class = $this->propertyoptions[$property_group_name][$property]['field-class'];
}

// if combobox - replaces all select menus, for better styling and user flexibility
$combo_class = '';
$combo_arrow = '';
if (
    !empty($this->propertyoptions[$property_group_name][$property]['type']) and
    $this->propertyoptions[$property_group_name][$property]['type'] == 'combobox') {
    $combo_class = 'combobox';
    $combo_arrow = '<span class="combo-arrow"></span>';
}

// determine if the user has applied a value for this field, adjust comp class accordingly
$comp_class = 'comp-style cprop-' . str_replace('_', '-', $property);
if (!empty($value) or $value === '0') {
    $man_class = ' manual-val';
    $comp_class.= ' hidden';
} else {
    $man_class = '';
}

// check if input is eligable for autofill
if (!empty($this->propertyoptions[$property_group_name][$property]['rel'])) {
    $autofill_class = 'autofillable ';
    $autofill_rel = $this->propertyoptions[$property_group_name][$property]['rel'];
}
else {
    $autofill_class = '';
    $autofill_rel = '';
}

// input class
$input_class = '';
if (!empty($this->propertyoptions[$property_group_name][$property]['input-class']) ) {
    $input_class = $this->propertyoptions[$property_group_name][$property]['input-class'];
}


// input icon or just text
$text_label = '<span class="text-label css-info">'
    . $this->propertyoptions[$property_group_name][$property]['short_label'] . '</span>';
if (!empty($this->propertyoptions[$property_group_name][$property]['icon'])){
    $option_icon = '<span class="o-icon-wrap"><span class="tvr-icon option-icon option-icon-'.$property.' css-info"
    rel="program-docs" data-prop="'.str_replace('_', '-', $property).'"></span></span>';
} else {
    $option_icon = '';
}


// construct important toggle
// check status of !important - only create dom element if positive result (optimisation purposes)
if ($important_val == 1) {
    $important_toggle = '<input class="important-tracker" type="hidden"
        name="tvr_mcth[non_section][important]'.$imp_key.'['.$section_name.']['.$css_selector.']['.$property_group_name.']['.$property.']" value="1" />';
    $rel = 'on';
    $class = 'active';
}
else {
    $important_toggle = '';
    $rel = 'off';
    $class = '';
}
// don't show i on all css3 props and text shadow
if (
empty($this->propertyoptions[$property_group_name][$property]['hide imp'])) {
    $important_toggle.= '<span class="important-toggle input-icon-toggle '.$class.' imp-'.$con.'" data-input-type="important"
        title="Add/remove !important declaration" rel="'.$rel.'">i</span>';
} else {
    $important_toggle.= '<span class="imp-placeholder">i</span>';
}

/** Deal with property exceptions */
$extra_icon = '';
$input_name_adj = '';
$image_field = false;
// add image insert button for bg image
if ($property == 'background_image' or $property == 'list_style_image') {
    $extra_icon = ' <span class="tvr-icon tvr-image-upload"></span>';
    $input_name_adj = 'img_display';
    $image_field = true;
}
// strip font-family custom quotes for legacy reasons
if ($property == 'font_family') {
    $value = str_replace('cus-#039;', '&quot;', $value);
}
// allow user to edit their google fonts with a link
if ($property == 'google_font') {
    $extra_icon = '<span class="g-font tvr-icon" title="Edit Google Font"></span>';
    // hide if background positon isn't set to custom
    if ($property_group_array['font_family'] != 'Google Font...') {
        $field_class.= ' hidden';
    }
}


/***
 * output the form fields
 */

// check if it's a new sub group
if (!empty($this->propertyoptions[$property_group_name][$property]['sub_label'])){
    $html.= '
    <div class="field-wrap sub-label sub-label-'.$property.'">'.
        $this->propertyoptions[$property_group_name][$property]['sub_label'].':
    </div>';
}

$html.= '<div id="opts-'.$section_name.'-'.$css_selector.'-'.$property_group_name.'-'.$property. $mq_extr_class. '"
 class="property-tag field-wrap tvr-clearfix '.$man_class . ' '.$field_class . ' field-'.$property_group_name.'-'.$property.'">
    <label>';

    $html.= '<span class="option-label"
    data-simple-tt="'.$this->propertyoptions[$property_group_name][$property]['label'].'"
    title="'.$this->propertyoptions[$property_group_name][$property]['label'].'">'
        . $option_icon . $text_label . '</span>
        </label>'
        . $important_toggle

    . '<span class="input-wrap">';


    if ($image_field){
        $html.= '
                <span class="tvr-icon clear-bg-image delete-icon"></span>
                <input type="hidden"
                class="property-input image-url-store '.$input_class . '"
                name="tvr_mcth'.$mq_stem.'['.$section_name.']['.$css_selector.'][styles]['.$property_group_name.']['. $property.']" value="'.$value.'" />';
        // now only show filename
        $value = basename($value);
    }

    // render combobox
    $html.= '
            <input type="text" autocomplete="off" rel="'.$property.'" data-autofill="'.$autofill_rel.'"
            class="property-input '.$combo_class.' '.$input_class . ' ' . $autofill_class . ' ' . $input_name_adj .'"
            name="tvr_mcth'.$mq_stem.'['.$section_name.']['.$css_selector.'][styles]['.$property_group_name.']['. $property.$input_name_adj.']" value="'.stripslashes($value).'" />

        '.$combo_arrow;

    $html.= $extra_icon;

    $html.= '<span class="'.$comp_class.'"></span>

    </span>'; // end input wrap

    $html.= '
    <span class="comp-mixed-table">
		<h5 title="' . __('Hover over the rows to highlight the individual elements on the page', 'tvr-microthemer') . '">' . wp_kses(__('Page Analysis', 'tvr-microthemer'), array()) . '</h5>
        <table>
            <thead>
            <tr>
                <th><i>n</i></th>
				<th>' . wp_kses(__('Value', 'tvr-microthemer'), array()) . '</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </span>



</div><!-- end field-wrap -->';

if (
!empty($this->propertyoptions[$property_group_name][$property]['linebreak']) and
$this->propertyoptions[$property_group_name][$property]['linebreak'] == '1' ) {
    $html.= '<div class="clear"></div>';
}

