<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}

// gradient
if ( ($property == 'gradient_a' or 
$property == 'gradient_b' or 
$property == 'gradient_b_pos' or 
$property == 'gradient_c' or 
$property == 'gradient_angle') 
and !$array['styles']['gradient']['rendered'] ) {
	
	// !important is a bit different for css3 - only one "i" per line - do another check
	$sty['css_important'] = $this->tvr_css3_imp($section_name_slug, $css_selector_slug, $property_group_name, 'gradient_c', $con, $mq_key);
	//$sty['data'].= "$section_name_slug, $css_selector_slug, $property_group_name, 'gradient_c', $con, $mq_key - ".$sty['css_important'];
	
	// get Gradient A or default to other gradient
	if ( !empty($property_group_array['gradient_a'])) {
		$gradient_a = $property_group_array['gradient_a'];
	}
	else {
		// make same as B if set
		if ( !empty($property_group_array['gradient_b']) ) {
			$gradient_a = $property_group_array['gradient_b'];
		}
		// else make same as C
		elseif ( !empty($property_group_array['gradient_c']) ) {
			$gradient_a = $property_group_array['gradient_c'];
		}
	}
	
	// get Gradient B if set (it's optional so no default)
	if ( !empty($property_group_array['gradient_b'])) {
		$gradient_b = $property_group_array['gradient_b'];
		// get B Position if set
		if ( !empty($property_group_array['gradient_b_pos'])) {
			$gradient_b_pos = $property_group_array['gradient_b_pos'];
			// give gradient_b_pos px unit if not specified
			$gradient_b_pos.= $this->check_unit($property_group_name, 'gradient_b_pos', $gradient_b_pos);
		}
		else {
			$gradient_b_pos = '50%';
		}
		// create middle color-stop syntax for -webkit-gradient and others
		$wbkold_bstop = "color-stop($gradient_b_pos, $gradient_b), ";
		$new_bstop = "$gradient_b $gradient_b_pos, ";	
	}
	else {
		$wbkold_bstop = '';
		$new_bstop = '';
	}
	
	// get Gradient C or default to other gradient
	if ( !empty($property_group_array['gradient_c'])) {
		$gradient_c = $property_group_array['gradient_c'];
	}
	else {
		// make same as B if set
		if ( !empty($property_group_array['gradient_b'])) {
			$gradient_c = $property_group_array['gradient_b'];
		}
		// else make same as C
		elseif ( !empty($property_group_array['gradient_a']) ) {
			$gradient_c = $property_group_array['gradient_a'];
		}
	}
	
	// get Gradient Angle or default to vertical
	if (!empty($property_group_array['gradient_angle'])) {
		$gradient_angle = $property_group_array['gradient_angle'];
	}
	else {
		$gradient_angle = 'top to bottom';
	}
	// convert angle values to browser specific 
	switch ($gradient_angle) {
		case 'left to right':
			$wbkold_angle = 'left center, right center';
			$new_angle = '0deg';
			$non_prefix_angle = '90deg'; // non prefixed linear gradient has a different standard for angles (north starting) to prefixed versions
			break;
		case 'top left to bottom right':
			$wbkold_angle = 'left top, right bottom';
			$new_angle = '-45deg';
			$non_prefix_angle = '135deg';
			break;
		case 'top to bottom':
			$wbkold_angle = 'center top, center bottom';
			$new_angle = '-90deg';
			$non_prefix_angle = '180deg'; 
			break;
		case 'top right to bottom left':
			$wbkold_angle = 'right top, left bottom';
			$new_angle = '-135deg';
			$non_prefix_angle = '-135deg';
			break;
		case 'right to left':
			$wbkold_angle = 'right center, left center';
			$new_angle = '180deg';
			$non_prefix_angle = '-90deg';
			break;
		case 'bottom right to top left':
			$wbkold_angle = 'right bottom, left top';
			$new_angle = '135deg';
			$non_prefix_angle = '-45deg';
			break;
		case 'bottom to top':
			$wbkold_angle = 'center bottom, center top';
			$new_angle = '90deg';
			$non_prefix_angle = '0deg'; 
			break;
		case 'bottom left to top right':
			$wbkold_angle = 'left bottom, right top';
			$new_angle = '45deg';
			$non_prefix_angle = '45deg';
			break;
	} 
	
	// if the user didn't specify a background color, use Gradient A
	if (empty($array['styles']['background']['background_color'])) {
		$fallback_bg_color = "background-color: $gradient_a; /*non-CSS3 browsers will use this*/";
	}

    // check if bg-color property need to go in
    if (!empty($array['styles']['background']['background_color'])) {
        if ($array['styles']['background']['background_color'] != 'none' and
            $array['styles']['background']['background_color'] != '') {
            $user_bg_color = $array['styles']['background']['background_color']. ' ';
        }
        else {
            $user_bg_color = '';
        }
    }

	// check if bg image properties need to go in
	if (!empty($array['styles']['background']['background_image'])) {
		if ($array['styles']['background']['background_image'] != 'none') {
			$user_bg_image = "url(".$array['styles']['background']['background_image'].")";
		}
		else {
			$user_bg_image = "none";
		}
		if (!empty($array['styles']['background']['background_position'])) {
			$user_bg_image.= ' '.$array['styles']['background']['background_position'];
		}
		if (!empty($array['styles']['background']['background_repeat'])) {
			$user_bg_image.= ' '.$array['styles']['background']['background_repeat'];
		}
		$user_bg_image.= ', ';
	}
	
	// render the gradient
	$sty['data'].= "	{$tab}$fallback_bg_color
	{$tab}background: {$user_bg_color}{$user_bg_image}-webkit-gradient(linear, $wbkold_angle, from($gradient_a), {$wbkold_bstop}to($gradient_c)){$sty['css_important']};
	{$tab}background: {$user_bg_color}{$user_bg_image}-webkit-linear-gradient($new_angle, $gradient_a, {$new_bstop}$gradient_c){$sty['css_important']};
	{$tab}background: {$user_bg_color}{$user_bg_image}-moz-linear-gradient($new_angle, $gradient_a, {$new_bstop}$gradient_c){$sty['css_important']};
	{$tab}background: {$user_bg_color}{$user_bg_image}-ms-linear-gradient($new_angle, $gradient_a, {$new_bstop}$gradient_c){$sty['css_important']};
	{$tab}background: {$user_bg_color}{$user_bg_image}-o-linear-gradient($new_angle, $gradient_a, {$new_bstop}$gradient_c){$sty['css_important']};
	{$tab}background: {$user_bg_color}{$user_bg_image}linear-gradient($non_prefix_angle, $gradient_a, {$new_bstop}$gradient_c){$sty['css_important']};
	{$tab}-pie-background: {$user_bg_color}{$user_bg_image}linear-gradient($new_angle, $gradient_a, {$new_bstop}$gradient_c){$sty['css_important']};
";
	// record that this property group has been rendered
	$array['styles']['gradient']['rendered'] = true;
}

// border radius
if ( ($property == 'radius_top_left' or 
$property == 'radius_top_right' or 
$property == 'radius_bottom_right' or 
$property == 'radius_bottom_left') and
!$array['styles']['radius']['rendered'] ) {
	
	// !important is a bit different for css3 - only one "i" per line - do another check
	$sty['css_important'] = $this->tvr_css3_imp($section_name_slug, $css_selector_slug, $property_group_name, 'radius_bottom_left', $con, $mq_key);

	// top left
	if (!empty($property_group_array['radius_top_left'])) {
		$radius_top_left = $property_group_array['radius_top_left'];
	}
	else {
		$radius_top_left = 0;
	}
	$radius_top_left.= $this->check_unit($property_group_name, 'radius_top_left', $radius_top_left);
	// top right
	if (!empty($property_group_array['radius_top_right'])) {
		$radius_top_right = $property_group_array['radius_top_right'];
	}
	else {
		$radius_top_right = 0;
	}
	$radius_top_right.= $this->check_unit($property_group_name, 'radius_top_right', $radius_top_right);
	// bottom right
	if (!empty($property_group_array['radius_bottom_right'])) {
		$radius_bottom_right = $property_group_array['radius_bottom_right'];
	}
	else {
		$radius_bottom_right = 0;
	}
	$radius_bottom_right.= $this->check_unit($property_group_name, 'radius_bottom_right', $radius_bottom_right);
	// bottom left
	if (!empty($property_group_array['radius_bottom_left'])) {
		$radius_bottom_left = $property_group_array['radius_bottom_left'];
	}
	else {
		$radius_bottom_left = 0;
	}
	$radius_bottom_left.= $this->check_unit($property_group_name, 'radius_bottom_left', $radius_bottom_left);
	
	$sty['data'].= $tab."	-webkit-border-radius: $radius_top_left $radius_top_right $radius_bottom_right $radius_bottom_left{$sty['css_important']};
	{$tab}-moz-border-radius: $radius_top_left $radius_top_right $radius_bottom_right $radius_bottom_left{$sty['css_important']};
	{$tab}border-radius: $radius_top_left $radius_top_right $radius_bottom_right $radius_bottom_left{$sty['css_important']};
";
	// record that this property group has been rendered
	$array['styles']['radius']['rendered'] = true;
}

// box shadow
if ( ($property == 'box_shadow_color' or
$property == 'box_shadow_x' or
$property == 'box_shadow_y' or
$property == 'box_shadow_blur') and
!$array['styles']['box_shadow']['rendered']) {
	
	// !important is a bit different for css3 - only one "i" per line - do another check
	$sty['css_important'] = $this->tvr_css3_imp($section_name_slug, $css_selector_slug, $property_group_name, 'box_shadow_blur', $con, $mq_key);

	// shadow color
	if (!empty($property_group_array['box_shadow_color'])) {
		$box_shadow_color = $property_group_array['box_shadow_color'];
	}
	else {
		$box_shadow_color = '#CCCCCC';
	}
	// x-offset
	if (!empty($property_group_array['box_shadow_x'])) {
		$box_shadow_x = $property_group_array['box_shadow_x'];
	}
	else {
		$box_shadow_x = 0;
	}
	$box_shadow_x.= $this->check_unit($property_group_name, 'box_shadow_x', $box_shadow_x);
	// y-offset
	if (!empty($property_group_array['box_shadow_y'])) {
		$box_shadow_y = $property_group_array['box_shadow_y'];
	}
	else {
		$box_shadow_y = 0;
	}
	$box_shadow_y.= $this->check_unit($property_group_name, 'box_shadow_y', $box_shadow_y);
	// blur
	if (!empty($property_group_array['box_shadow_blur'])) {
		$box_shadow_blur = $property_group_array['box_shadow_blur'];
	}
	else {
		$box_shadow_blur = 0;
	}
	$box_shadow_blur.= $this->check_unit($property_group_name, 'box_shadow_blur', $box_shadow_blur);
	
	$sty['data'].= $tab."	-webkit-box-shadow: $box_shadow_color $box_shadow_x $box_shadow_y $box_shadow_blur{$sty['css_important']};
	{$tab}-moz-box-shadow: $box_shadow_color $box_shadow_x $box_shadow_y $box_shadow_blur{$sty['css_important']};
	{$tab}box-shadow: $box_shadow_color $box_shadow_x $box_shadow_y $box_shadow_blur{$sty['css_important']};
";
 // record that this property group has been rendered
	$array['styles']['box_shadow']['rendered'] = true;
	
}


// text shadow
if ( ($property == 'text_shadow_color' or
$property == 'text_shadow_x' or
$property == 'text_shadow_y' or
$property == 'text_shadow_blur') and
!$array['styles']['text_shadow']['rendered']) {
	
	// !important is a bit different for css3 - only one "i" per line - do another check
	$sty['css_important'] = $this->tvr_css3_imp($section_name_slug, $css_selector_slug, $property_group_name, 'text_shadow_blur', $con, $mq_key);
	
	// shadow color
	if (!empty($property_group_array['text_shadow_color'])) {
		$text_shadow_color = $property_group_array['text_shadow_color'];
	}
	else {
		$text_shadow_color = '#CCCCCC';
	}
	// x-offset
	if (!empty($property_group_array['text_shadow_x'])) {
		$text_shadow_x = $property_group_array['text_shadow_x'];
	}
	else {
		$text_shadow_x = 0;
	}
	$text_shadow_x.= $this->check_unit($property_group_name, 'text_shadow_x', $text_shadow_x);
	// y-offset
	if (!empty($property_group_array['text_shadow_y'])) {
		$text_shadow_y = $property_group_array['text_shadow_y'];
	}
	else {
		$text_shadow_y = 0;
	}
	$text_shadow_y.= $this->check_unit($property_group_name, 'text_shadow_y', $text_shadow_y);
	// blur
	if (!empty($property_group_array['text_shadow_blur'])) {
		$text_shadow_blur = $property_group_array['text_shadow_blur'];
	}
	else {
		$text_shadow_blur = 0;
	}
	$text_shadow_blur.= $this->check_unit($property_group_name, 'text_shadow_blur', $text_shadow_blur);

    // allow for disabling text-shadow with "none" in color field
    if ($text_shadow_color == 'none'){
        $resolved_text_shadow = 'none';
    } else {
        $resolved_text_shadow = "$text_shadow_color $text_shadow_x $text_shadow_y $text_shadow_blur";
    }
	
	$sty['data'].= $tab."	text-shadow: $resolved_text_shadow{$sty['css_important']};
";
 // record that this property group has been rendered
	$array['styles']['text_shadow']['rendered'] = true;
}

?>