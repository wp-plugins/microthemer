<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}
$propertyOptions = array();
// font
$propertyOptions['font']['font_weight'] = array('label' => 'Font Weight', 'type' => 'select', 
	'select_options' => array("normal","bold"));
$propertyOptions['font']['font_style'] = array('label' => 'Font Style', 'type' => 'select', 
	'select_options' => array("normal","italic","oblique"));
$propertyOptions['font']['font_variant'] = array('label' => 'Font Variant', 'type' => 'select', 
	'select_options' => array("normal","small-caps"));
$propertyOptions['font']['font_size'] = array('label' => 'Font Size', 'default_unit' => 'px', 'pos' => 'last');
$propertyOptions['font']['font_family'] = array('label' => 'Font Family', 'type' => 'select', 'span' => '2', 
	'select_options' => array(
	"Google Font...",
	"Arial",
	"cus-#039;Book Antiquacus-#039;",
	"cus-#039;Bookman Old Stylecus-#039;",
	"cus-#039;Arial Blackcus-#039;",
	"Charcoal",
	"cus-#039;Comic Sans MScus-#039;",
	"cursive",
	"Courier",
	"cus-#039;Courier Newcus-#039;",
	"Gadget",
	"Garamond",
	"Geneva",
	"Georgia",
	"Helvetica",
	"Impact",
	"cus-#039;Lucida Consolecus-#039;",
	"cus-#039;Lucida Grandecus-#039;",
	"cus-#039;Lucida Sans Unicodecus-#039;",
	"Monaco",
	"monospace",
	"cus-#039;MS Sans Serifcus-#039;",
	"cus-#039;MS Serifcus-#039;",
	"cus-#039;New Yorkcus-#039;",
	"Palatino",
	"cus-#039;Palatino Linotypecus-#039;",
	"sans-serif",
	"serif",
	"Symbol",
	"Tahoma",
	"cus-#039;Times New Romancus-#039;",
	"Times",
	"cus-#039;Trebuchet MScus-#039;",
	"Verdana",
	"Webdings",
	"Wingdings",
	"cus-#039;Zapf Dingbatscus-#039;"
	));
$propertyOptions['font']['google_font'] = array('label' => 'Google Font', 'span' => '2', 'pos' => 'last');	
	
// text
$propertyOptions['text']['line_height'] = array('label' => 'Line Height');
$propertyOptions['text']['text_align'] = array('label' => 'Alignment', 'type' => 'select', 
	'select_options' => array("left","right","center","justify"));
$propertyOptions['text']['text_decoration'] = array('label' => 'Decoration','type' => 'select', 
	'select_options' => array("underline","line-through","none"));
$propertyOptions['text']['text_transform'] = array('label' => 'Transform','type' => 'select', 
	'select_options' => array("capitalize","uppercase","lowercase","none"), 'pos' => 'last', 'linebreak' => 1);
$propertyOptions['text']['text_indent'] = array('label' => 'Indent', 'default_unit' => 'px');
$propertyOptions['text']['letter_spacing'] = array('label' => 'Letter Spacing', 'default_unit' => 'px');
$propertyOptions['text']['word_spacing'] = array('label' => 'Word Spacing', 'default_unit' => 'px');
$propertyOptions['text']['list_style'] = array('label' => 'List Style','type' => 'select', 
	'select_options' => array(
	'circle', 
	'disc',
	'square', 
	'armenian', 
	'decimal', 
	'decimal-leading-zero', 
	'georgian', 
	'lower-alpha', 
	'lower-greek', 
	'lower-latin', 
	'lower-roman', 
	'upper-alpha', 
	'upper-latin', 
	'upper-roman', 
	'none'
	), 'pos' => 'last');
// text shadow
$propertyOptions['text']['text_shadow_color'] = array('label' => 'Shadow Color', 'icon' => 'shadow', 'picker' => '1');
$propertyOptions['text']['text_shadow_x'] = array('label' => 'Shadow x-offset', 'default_unit' => 'px', 'icon' => 'x_offset');
$propertyOptions['text']['text_shadow_y'] = array('label' => 'Shadow y-offset', 'default_unit' => 'px', 'icon' => 'y_offset');
$propertyOptions['text']['text_shadow_blur'] = array('label' => 'Shadow Blur', 'default_unit' => 'px', 'icon' => 'shadow', 'pos' => 'last');
// forecolor
$propertyOptions['forecolor']['color'] = array('label' => 'Color', 'picker' => '1');
// background
$propertyOptions['background']['background_color'] = array('label' => 'Color', 'picker' => '1');
$propertyOptions['background']['background_image'] = array('label' => 'Image', 'type' => 'select', 'span' => '3', 'bg_image' => '1', 'pos' => 'last', 'linebreak' => 1);
$propertyOptions['background']['background_position'] = array('label' => 'Position','type' => 'select', 
	'select_options' => array(
	'left top',
	'left center',
	'left bottom',
	'right top',
	'right center',
	'right bottom',
	'center top',
	'center center',
	'center bottom',
	'custom'
	));
$propertyOptions['background']['background_repeat'] = array('label' => 'Repeat','type' => 'select', 
	'select_options' => array(
	'repeat',
	'repeat-x',
	'repeat-y',
	'no-repeat'
	));
$propertyOptions['background']['background_attachment'] = array('label' => 'Attachment','type' => 'select',  
	'select_options' => array(
	'scroll',
	'fixed'
	),'pos' => 'last', 'linebreak' => 1);
$propertyOptions['background']['background_position_x'] = array('label' => 'Position Left',  'default_unit' => 'px', 'icon' => 'position_left');
$propertyOptions['background']['background_position_y'] = array('label' => 'Position Top',  'default_unit' => 'px', 'icon' => 'position_top', 'pos' => 'last', 'linebreak' => 1);
// dimensions
$propertyOptions['dimensions']['width'] = array('label' => 'Width', 'default_unit' => 'px');
$propertyOptions['dimensions']['height'] = array('label' => 'Height', 'default_unit' => 'px', 'linebreak' => 1);
$propertyOptions['dimensions']['min_width'] = array('label' => 'Min Width', 'default_unit' => 'px');
$propertyOptions['dimensions']['min_height'] = array('label' => 'Min Height', 'default_unit' => 'px', 'linebreak' => 1);
$propertyOptions['dimensions']['max_width'] = array('label' => 'Max Width', 'default_unit' => 'px');
$propertyOptions['dimensions']['max_height'] = array('label' => 'Max Height', 'default_unit' => 'px', 'pos' => 'last', 'linebreak' => 1);
// padding
$propertyOptions['padding']['padding_top'] = array('label' => 'Padding Top', 'default_unit' => 'px', 'icon' => 'padding_top', 'rel' => 'paddding');
$propertyOptions['padding']['padding_right'] = array('label' => 'Padding Right', 'default_unit' => 'px', 'icon' => 'padding_right', 'rel' => 'paddding');
$propertyOptions['padding']['padding_bottom'] = array('label' => 'Padding Bottom', 'default_unit' => 'px', 'icon' => 'padding_bottom', 'rel' => 'paddding');
$propertyOptions['padding']['padding_left'] = array('label' => 'Padding Left', 'default_unit' => 'px', 'icon' => 'padding_left', 'rel' => 'paddding', 'pos' => 'last');
// margin
$propertyOptions['margin']['margin_top'] = array('label' => 'Margin Top', 'default_unit' => 'px', 'icon' => 'margin_top', 'rel' => 'margin');
$propertyOptions['margin']['margin_right'] = array('label' => 'Margin Right', 'default_unit' => 'px', 'icon' => 'margin_right', 'rel' => 'margin');
$propertyOptions['margin']['margin_bottom'] = array('label' => 'Margin Bottom', 'default_unit' => 'px', 'icon' => 'margin_bottom', 'rel' => 'margin');
$propertyOptions['margin']['margin_left'] = array('label' => 'Margin Left', 'default_unit' => 'px', 'icon' => 'margin_left', 'rel' => 'margin', 'pos' => 'last');
// border
$propertyOptions['border']['border_top_color'] = array('label' => 'Top Color', 'default_unit' => 'px', 'icon' => 'top', 'rel' => 'border_color', 'picker' => '1');
$propertyOptions['border']['border_right_color'] = array('label' => 'Right Color', 'default_unit' => 'px', 'icon' => 'right', 'rel' => 'border_color', 'picker' => '1');
$propertyOptions['border']['border_bottom_color'] = array('label' => 'Bottom Color', 'default_unit' => 'px', 'icon' => 'bottom', 'rel' => 'border_color', 'picker' => '1');
$propertyOptions['border']['border_left_color'] = array('label' => 'Left Color', 'default_unit' => 'px', 'icon' => 'left', 'rel' => 'border_color', 'picker' => '1', 'pos' => 'last');
$propertyOptions['border']['border_top_width'] = array('label' => 'Top Width', 'default_unit' => 'px', 'icon' => 'top', 'rel' => 'border_width');
$propertyOptions['border']['border_right_width'] = array('label' => 'Right Width', 'default_unit' => 'px', 'icon' => 'right', 'rel' => 'border_width');
$propertyOptions['border']['border_bottom_width'] = array('label' => 'Bottom Width', 'default_unit' => 'px', 'icon' => 'bottom', 'rel' => 'border_width');
$propertyOptions['border']['border_left_width'] = array('label' => 'Left Width', 'default_unit' => 'px', 'icon' => 'left', 'rel' => 'border_width', 'pos' => 'last');
$propertyOptions['border']['border_style'] = array('label' => 'Border Style', 'type' => 'select',
	'select_options' => array(
	'hidden',
	'dotted',
	'dashed',
	'solid',
	'double',
	'groove',
	'ridge',
	'inset',
	'outset',
	'none'
	));
// behaviour
$propertyOptions['behaviour']['display'] = array('label' => 'Display','type' => 'select', 
	'select_options' => array(
	'block',
	'inline',
	'inline-block',
	'none'
	));
$propertyOptions['behaviour']['float'] = array('label' => 'Float','type' => 'select', 
	'select_options' => array(
	'left',
	'right',
	'none'
	));
$propertyOptions['behaviour']['clear'] = array('label' => 'Clear','type' => 'select', 
	'select_options' => array(
	'left',
	'right',
	'both',
	'none'
	));
$propertyOptions['behaviour']['overflow'] = array('label' => 'Overflow','type' => 'select', 
	'select_options' => array(
	'visible',
	'scroll',
	'auto',
	'hidden'
	), 'pos' => 'last');
$propertyOptions['behaviour']['visibility'] = array('label' => 'Visibility','type' => 'select', 
	'select_options' => array(
	'visible',
	'hidden',
	'collapse'
	));
	
$propertyOptions['behaviour']['cursor'] = array('label' => 'Cursor','type' => 'select', 
	'select_options' => array(
	'auto',
	'crosshair',
	'default',
	'e-resize',
	'help',
	'move',
	'n-resize',
	'ne-resize',
	'nw-resize',
	'pointer',
	'progress',
	's-resize',
	'se-resize',
	'sw-resize',
	'text',
	'w-resize',
	'wait'
	));
$propertyOptions['behaviour']['opacity'] = array('label' => 'Opacity', 'linebreak' => 1);
// position
$propertyOptions['position']['position'] = array('label' => 'Position','type' => 'select', 
	'select_options' => array(
	'absolute',
	'relative',
	'fixed',
	'static'
	));
$propertyOptions['position']['z_index'] = array('label' => 'Z-index', 'linebreak' => 1);
$propertyOptions['position']['top'] = array('label' => 'Top', 'default_unit' => 'px', 'icon' => 'position_top');
$propertyOptions['position']['bottom'] = array('label' => 'Bottom', 'default_unit' => 'px', 'icon' => 'position_bottom');
$propertyOptions['position']['left'] = array('label' => 'Left', 'default_unit' => 'px', 'icon' => 'position_left');
$propertyOptions['position']['right'] = array('label' => 'Right', 'default_unit' => 'px', 'icon' => 'position_right', 'pos' => 'last');
// CSS3
// gradient
$propertyOptions['CSS3']['gradient_a'] = array('label' => 'Gradient A', 'picker' => '1');
$propertyOptions['CSS3']['gradient_b'] = array('label' => 'Gradient B (optional)', 'picker' => '1');
$propertyOptions['CSS3']['gradient_b_pos'] = array('label' => 'B Position (optional)', 'default_unit' => 'px');
$propertyOptions['CSS3']['gradient_c'] = array('label' => 'Gradient C', 'picker' => '1', 'linebreak' => 1, 'pos' => 'last');
$propertyOptions['CSS3']['gradient_angle'] = array('label' => 'Gradient Angle', 'linebreak' => 1, 'pos' => 'last', 'type' => 'select', 
	"select_options" => array(
	"top to bottom",
	"bottom to top",
	"left to right",
	"right to left",
	"top left to bottom right",
	"bottom right to top left",
	"top right to bottom left",
	"bottom left to top right"
	));
// note - pie only supports the shorthand border radius
$propertyOptions['CSS3']['radius_top_left'] = array('label' => 'TL Radius', 'default_unit' => 'px', 'icon' => 'radius_top_left', 'rel' => 'border_radius');
$propertyOptions['CSS3']['radius_top_right'] = array('label' => 'TR Radius', 'default_unit' => 'px', 'icon' => 'radius_top_right', 'rel' => 'border_radius');
$propertyOptions['CSS3']['radius_bottom_right'] = array('label' => 'BR Radius', 'default_unit' => 'px', 'icon' => 'radius_bottom_right', 'rel' => 'border_radius');
$propertyOptions['CSS3']['radius_bottom_left'] = array('label' => 'BL Radius', 'default_unit' => 'px', 'icon' => 'radius_bottom_left', 'rel' => 'border_radius', 'pos' => 'last');
// shadow
$propertyOptions['CSS3']['box_shadow_color'] = array('label' => 'Shadow Color', 'icon' => 'shadow', 'picker' => '1');
$propertyOptions['CSS3']['box_shadow_x'] = array('label' => 'Shadow x-offset', 'default_unit' => 'px', 'icon' => 'x_offset');
$propertyOptions['CSS3']['box_shadow_y'] = array('label' => 'Shadow y-offset', 'default_unit' => 'px', 'icon' => 'y_offset');
$propertyOptions['CSS3']['box_shadow_blur'] = array('label' => 'Shadow Blur', 'default_unit' => 'px', 'icon' => 'shadow', 'pos' => 'last');

/*

*/
?>