<?php

// this file is redundant now

/*** BORDER ***/
// get the legacy border val
if ($property_group_name == 'border' and
    !empty($styles['border']['border_style'])){
    $border_style_val = $styles['border']['border_style'];
} else {
    $border_style_val = false;
}
// 4 values for border style should use legacy border-style if it was set
if (!empty($border_style_val) and (
        $property == 'border_top_style' or
        $property == 'border_right_style' or
        $property == 'border_bottom_style' or
        $property == 'border_left_style'
    )) {
    $value = $border_style_val;
}
// get legacy values for border radius
$radius_map = array(
    'radius_top_left' => 'border_top_left_radius',
    'radius_top_right' => 'border_top_right_radius',
    'radius_bottom_right' => 'border_bottom_right_radius',
    'radius_bottom_left' => 'border_bottom_left_radius'
);
if ($property_group_name == 'border' and strpos($property, 'radius') !== false){
    foreach ($radius_map as $old => $new){
        if ($property == $new){
            if(!empty($styles['CSS3'][$old])){
                $value = $styles['CSS3'][$old];
            }
        }
    }
}

/*** COLOR ***/
// get legacy value for forecolor
if ($property_group_name == 'font' and
    $property == 'color' and
    !empty($styles['forecolor']['color'])){
    $value = $styles['forecolor']['color'];
}

/*** TEXT VALUES ***/
// get legacy value for list style type
if ($property == 'list_style_type' and
    !empty($styles['text']['list_style'])){
    $value = $styles['text']['list_style'];
}
// get legacy value for line height
if ($property == 'line_height' and
    !empty($styles['text']['line_height'])){
    $value = $styles['text']['line_height'];
}
// get legacy value for text-decoration
if ($property == 'text_decoration' and
    !empty($styles['text']['text_decoration'])){
    $value = $styles['text']['text_decoration'];
}

/*** SHADOW ***/
// get legacy values for text/box Shadow
if ($property_group_name == 'shadow'){
    foreach ($this->propertyoptions['shadow'] as $key => $val){
        if ($property == $key){
            if (strpos($property, 'text') !== false){
                $group = 'text';
            } else {
                $group = 'CSS3';
            }
            if(!empty($styles[$group][$key])){
                $value = $styles[$group][$key];
            }
        }
    }
}

/*** BACKGROUND ***/
// get legacy custom values if they exist
if ($property == 'background_position' and
    !empty($styles['background']['background_position_x'])
    or !empty($styles['background']['background_position_y'])){
    $value = $styles['background']['background_position_x'] . ' ' .$styles['background']['background_position_y'];
}

/*** PADDING/MARGIN ***/
// get legacy values for padding/margin
if ($property_group_name == 'padding_margin'){
    foreach ($this->propertyoptions['padding_margin'] as $key => $val){
        if ($property == $key){
            if (strpos($property, 'padding') !== false){
                $group = 'padding';
            } else {
                $group = 'margin';
            }
            if(!empty($styles[$group][$key])){
                $value = $styles[$group][$key];
            }
        }
    }
}

/*** POSITION/BEHAVIOUR ***/
// get legacy values for float/clear
if ($property == 'float' and
    !empty($styles['behaviour']['float'])){
    $value = $styles['behaviour']['float'];
}
if ($property == 'clear' and
    !empty($styles['behaviour']['clear'])){
    $value = $styles['behaviour']['clear'];
}

/*** GRADIENT ***/
// get legacy values for border radius
if ($property_group_name == 'gradient'){
    if(!empty($styles['CSS3'][$property])){
        $value = $styles['CSS3'][$property];
    }
}

?>