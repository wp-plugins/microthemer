<?php 
$docs_title = "CSS Property and Value Reference";
$main_class = 'css-ref';
$h1 = "CSS Reference";
include 'docs-header.inc.php'; 
?>

<?php 
if (isset($_GET['snippet'])) {
	$label = htmlentities($_GET['snippet']);
	$prop_group = htmlentities($_GET['prop_group']);
}
else {
	$label = 'show contents';
}

if ($label != 'pie') { ?>
<p class="w3-attr">Much information in this CSS reference has been gathered from the <a target='_blank' href='http://www.w3schools.com/cssref/default.asp'>W3 Schools CSS Reference.</a></p>

<?php } ?>

<?php
$ref = array();
/*** Font ***/
// font weight
$ref['font']['font_weight'] = array(
	'title' => 'Font Weight', 
	'ref_desc' => "The font-weight property sets how thick or thin characters in text should be displayed.",
	'ref_values' => array(
		"normal" => "Defines normal characters. This is default",
		"bold" => "Defines thick characters"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_font_weight.asp');
// font style
$ref['font']['font_style'] = array(
	'title' => 'Font Style', 
	'ref_desc' => "The font-style property specifies the font style for text.",
	'ref_values' => array(
		"normal" => "The browser displays a normal font style. This is default",
		"italic" => "The browser displays an italic font style",
		"oblique" => "The browser displays an oblique font style"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_font_font-style.asp');
// font variant
$ref['font']['font_variant'] = array(
	'title' => 'Font Variant', 
	'ref_desc' => "The font-variant property specifies whether or not text should be displayed in a small-caps font.",
	'ref_values' => array(
		"normal" => "The browser displays a normal font. This is default",
		"small-caps" => "The browser displays a small-caps font"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_font_font-variant.asp');
// font size
$ref['font']['font_size'] = array(
	'title' => 'Font Size', 
	'ref_desc' => "The font-size property sets the size of a font.",
	'ref_values' => array(
		"(numeric)" => "e.g. '12' would set the font size to 12 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '1.2em' in the Font Size field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_font_font-size.asp');
// font family
$ref['font']['font_family'] = array(
	'title' => 'Font Family', 
	'ref_desc' => "The font-family property specifies the font for an element.",
	'ref_values' => array(
		"font name" => "The name of the font e.g. 'Arial'"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_font_font-family.asp');
// Google Font
$ref['font']['google_font'] = array(
	'title' => 'Google Web Fonts', 
	'ref_desc' => "Choose from hundreds of free, open-source fonts optimized for the web. Google Fonts is a service Google offers free of charge that allows web designers to use a wide variety of openly licensed fonts on their web pages. Prior to font service like Google Web Fonts, web designers were limited to using a small selection of 'Web Safe' fonts that were guaranteed to look the same on different computers (e.g. Macs and PCs). Microthemer makes use of Google's Web Fonts API to make adding Google Fonts really easy. Just click the 'Change Font' link, and then the 'Use This Font' link next to the name of the font. For efficiency, Microthemer only loads the Google Fonts you've specified on the frontend of your site.",
	'ref_values' => array(
		"font name" => "The name of the font e.g. 'Oswald'"), 
	'w3s' => 'http://www.google.com/fonts/');
/*** Text ***/
// line height
$ref['text']['line_height'] = array(
	'title' => 'Line Height', 
	'ref_desc' => "The line-height property specifies the line height.",
	'ref_values' => array(
		"(numeric)" => "e.g. '18' would set the line height to 18 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '1.8em' in the Line Height field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_dim_line-height.asp');
// text alignment
$ref['text']['text_align'] = array(
	'title' => 'Text Alignment', 
	'ref_desc' => "The text-align property specifies the horizontal alignment of text in an element.",
	'ref_values' => array(
		"left" => "Aligns the text to the left. This is default",
		"right" => "Aligns the text to the right",
		"center" => "Centers the text",
		"justify" => "Stretches the lines so that each line has equal width (like in newspapers and magazines)"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_text_text-align.asp');
// text decoration
$ref['text']['text_decoration'] = array(
	'title' => 'Text Decoration', 
	'ref_desc' => "The text-decoration property specifies the decoration added to text.",
	'ref_values' => array(
		"underline" => "Defines a line below the text",
		"line-through" => "Defines a line through the text",
		"none" => "	Defines normal text. This is default"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_text_text-decoration.asp');
// text transform
$ref['text']['text_transform'] = array(
	'title' => 'Text Transform', 
	'ref_desc' => "The text-transform property controls the capitalization of text.",
	'ref_values' => array(
		"capitalise" => "Transforms the first character of each word to uppercase",
		"uppercase" => "Transforms all characters to uppercase",
		"lowercase" => "Transforms all characters to lowercase",
		"none" => "No capitalization. The text renders as it is. This is default"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_text_text-transform.asp');
// text indent
$ref['text']['text_indent'] = array(
	'title' => 'Text Indent', 
	'ref_desc' => "The text-indent property specifies the indentation of the first line in a text-block.",
	'ref_values' => array(
		"(numeric)" => "e.g. '80' would set the text indent to 80 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '15%' in the Text Indent field."),
	'w3s' => 'http://www.w3schools.com/cssref/pr_text_text-indent.asp');
// letter spacing
$ref['text']['letter_spacing'] = array(
	'title' => 'Letter Spacing', 
	'ref_desc' => "The letter-spacing property increases or decreases the space between characters in text.",
	'ref_values' => array(
		"(numeric)" => "e.g. '2' would set the letter spacing to 2 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified)."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_text_text-indent.asp');
// word spacing
$ref['text']['word_spacing'] = array(
	'title' => 'Word Spacing', 
	'ref_desc' => "The word-spacing property increases or decreases the white space between words.",
	'ref_values' => array(
		"(numeric)" => "e.g. '10' would set the font size to 10 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '1em' in the Word Spacing field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_text_word-spacing.asp');
// list style
$ref['text']['list_style'] = array(
	'title' => 'List Style', 
	'ref_desc' => "The list-style shorthand property sets all the list properties in one declaration. Microthemer only presents the <i>list-style-type</i> property values however because the others are rarely useful. 'list-style-type' specifies the type of list-item marker in a list. Circle, disc and square can be applied to unordered lists (&#60;ul&#62;), commonly referred to as bulleted lists. All the rest can be applied to ordered lists (	&#60;ol&#62;), such as those with numbers at the start.",
	'ref_values' => array(
		"circle" => "The marker is a circle",
		"disc" => "The marker is a filled circle. This is default",
		"square" => "The marker is a square",
		"armenian" => "The marker is traditional Armenian numbering",
		"decimal" => "	The marker is a number",
		"decimal-leading-zero" => "	The marker is a number padded by initial zeros (01, 02, 03, etc.)",
		"georgian" => "The marker is traditional Georgian numbering (an, ban, gan, etc.)",
		"lower-alpha" => "The marker is lower-alpha (a, b, c, d, e, etc.)",
		"lower-greek" => "The marker is lower-greek (alpha, beta, gamma, etc.)",
		"lower-latin" => "The marker is lower-latin (a, b, c, d, e, etc.)",
		"lower-roman" => "The marker is lower-roman (i, ii, iii, iv, v, etc.)",
		"upper-alpha" => "The marker is upper-alpha (A, B, C, D, E, etc.) ",
		"upper-latin" => "upper-latin	The marker is upper-latin (A, B, C, D, E, etc.)",
		"upper-roman" => "The marker is upper-roman (I, II, III, IV, V, etc.)",
		"none" => ""), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_list-style.asp');
// text shadow color
$ref['text']['text_shadow_color'] = array(
	'title' => 'Text Shadow Color', 
	'ref_desc' => "The color of the text shadow.",
	'ref_values' => array(
		"(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the 'Text Shadow Color' text field to reveal the color picker."), 
	'w3s' => '');	
// text shadow x offset
$ref['text']['text_shadow_x'] = array(
	'title' => 'Text Shadow X-offset', 
	'ref_desc' => "The position of the horizontal shadow. Negative values are allowed",
	'ref_values' => array(
		"(numeric)" => "e.g. '1' would create a 1 pixel shadow to the right of the text (Microthemer automatically adds the 'px' unit if a unit isn't specified)."), 
	'w3s' => '');
// text shadow y offset
$ref['text']['text_shadow_y'] = array(
	'title' => 'Text Shadow Y-offset', 
	'ref_desc' => "The position of the vertical shadow. Negative values are allowed",
	'ref_values' => array(
		"(numeric)" => "e.g. '-1' would create a 1 pixel shadow above the element (Microthemer automatically adds the 'px' unit if a unit isn't specified)."), 
	'w3s' => '');
// text shadow blur
$ref['text']['text_shadow_blur'] = array(
	'title' => 'Text Shadow Blur', 
	'ref_desc' => "The blur distance. If you define a black text shadow and a blur of 3px, their will be a 3 pixel blur area where the shadow fades evenly from black to transparent.",
	'ref_values' => array(
		"(numeric)" => "e.g. '4' would create a 4 pixel text blur."), 
	'w3s' => '');
/*** Color ***/
// color
$ref['font']['color'] = array(
	'title' => 'Foreground Color', 
	'ref_desc' => "The color property specifies the color of text.",
	'ref_values' => array(
		"(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Color text field to reveal the color picker."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_text_color.asp');
/*** Background ***/
// background color
$ref['background']['background_color'] = array(
	'title' => 'Background Color', 
	'ref_desc' => "The background-color property sets the background color of an element.",
	'ref_values' => array(
		"(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Color text field to reveal the color picker."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_background-color.asp');
// background image
$ref['background']['background_image'] = array(
	'title' => 'Background Image', 
	'ref_desc' => "The background-image property sets the background image for an element.",
	'ref_values' => array(
		"(image)" => "Microthemer lists all the images contained within micro themes in a dropdown menu. You can also click the 'view images' link at the top right of the images menu to browse the images visually. Tip: the image slidehow will start from the image selected in the menu and will iterate through in the same order."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_background-image.asp');
// background position
$ref['background']['background_position'] = array(
	'title' => 'Background Position', 
	'ref_desc' => "The background-position property sets the starting position of a background image.",
	'ref_values' => array(
		"left top" => "The left and top edges of the background image are flush againt the left and top edges of the element. This is default",
		"left center" => "The left edge of the background image is flush againt the left edge of the element and is vertically centered",
		"left bottom" => "The left and bottom edges of the background image are flush againt the left and bottom edges of the element",
		"right top" => "The right and top edges of the background image are flush againt the right and top edges of the element",
		"right center" => "The right edge of the background image is flush againt the right edge of the element and is vertically centered",
		"right bottom" => "The right and bottom edges of the background image are flush againt the right and bottom edges of the element",
		"center top" => "The top edge of the background image is flush againt the top edge of the element and is horizontally centered",
		"center center" => "The center of the background image is aligned with the center of the element",
		"center bottom" => "The bottom edge of the background image is flush againt the bottom edge of the element and is horizontally centered"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_background-position.asp');
// background repeat
$ref['background']['background_repeat'] = array(
	'title' => 'Background Repeat', 
	'ref_desc' => "The background-repeat property sets if/how a background image will be repeated.",
	'ref_values' => array(
		"repeat" => "The background image will be repeated both vertically and horizontally. This is default",
		"repeat-x" => "The background image will be repeated only horizontally",
		"repeat-y" => "The background image will be repeated only vertically",
		"no-repeat" => "The background-image will not be repeated"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_background-repeat.asp');
// background position x
$ref['background']['background_position_x'] = array(
	'title' => 'Horizontal Background Position', 
	'ref_desc' => "The horizontal (x-axis) position of the background image. This property sets the left edge of a background image to a unit to the 
	left or right of the left edge of the element the background image is being applied to. Negative values are allowed.</p>
	<p><b>Note</b>: the way a background image moves on screen when you apply a positive 'Position Left' value may seem counterintuitive. It moves right on the screen when given a positive value because the browser increases the distance between the left edge of the element and the left edge of the background image. If in doubt, just look at the directional icon <img src='../images/position_left.gif' />. The icon depicts the direction the element will move on the page as you increase the value.",
	'ref_values' => array(
		"(numeric)" => "e.g. '30' would set the width to 30 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '25%' in the Position Left field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_background-position.asp');
// background position y
$ref['background']['background_position_y'] = array(
	'title' => 'Vertical Background Position', 
	'ref_desc' => "The vertical (y-axis) position of the background image. This property sets the top edge of a background image to a unit above or below the top edge of the element the background image is being applied to. Negative values are allowed.</p>
	<p><b>Note</b>: the way a background image moves on screen when you apply a positive 'Position Top' value may seem counterintuitive. It moves down on the screen when given a positive value because the browser increases the distance between the top edge of the element and the top edge of the background image. If in doubt, just look at the directional icon <img src='../images/position_top.gif' />. The icon depicts the direction the element will move on the page as you increase the value.",
	'ref_values' => array(
		"(numeric)" => "e.g. '30' would set the width to 30 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '25%' in the Position Left field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_background-position.asp');
// background attachement
$ref['background']['background_attachment'] = array(
	'title' => 'Background Attachment', 
	'ref_desc' => "The background-attachment property sets whether a background image is fixed or scrolls with the rest of the page",
	'ref_values' => array(
		"scroll" => "The background image scrolls with the rest of the page. This is default",
		"fixed" => "The background image is fixed"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_background-attachment.asp');
/*** Dimensions ***/
// width
$ref['dimensions']['width'] = array(
	'title' => 'Width', 
	'ref_desc' => "The width property sets the width of an element. <b>Note:</b> The total width of an element is 
	width + padding + borders + margins. But there is an exception to this rule. If the width hasn't been set for an element it defaults to the width of it's parent element (incidently, defining the value 'auto' is the same as not setting a width).  Applying padding, margin and border values when width is 'auto' or hasn't been defined, causes the browser to decrease the value it calculates for width (otherwise the element would be too big for it's parent element - which is what happens if you enter a value of '100%' for width and then add margins, padding or borders).",
	'ref_values' => array(
		"(numeric)" => "e.g. '400' would set the width to 400 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '50%' in the Width field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_dim_width.asp');
// height
$ref['dimensions']['height'] = array(
	'title' => 'Height', 
	'ref_desc' => "The height property sets the height of an element. <b>Note:</b> The total height of an element is 
	height + padding + borders + margins.",
	'ref_values' => array(
		"(numeric)" => "e.g. '200' would set the height to 400 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '10em' in the Height field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_dim_height.asp');
// min-width
$ref['dimensions']['min_width'] = array(
	'title' => 'Minimum Width', 
	'ref_desc' => "The min-width property sets the minimum width of an element. Note: The min-width property does not include padding, borders, or margins",
	'ref_values' => array(
		"(numeric)" => "e.g. '400' would set the minimun width to 400 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '50%' in the Min Width field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_dim_min-width.asp');
// min-height
$ref['dimensions']['min_height'] = array(
	'title' => 'Minimum Height', 
	'ref_desc' => "The min-height property sets the minimum height of an element. Note: The min-height property does not include padding, borders, or margins",
	'ref_values' => array(
		"(numeric)" => "e.g. '400' would set the minimun height to 400 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified)."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_dim_min-width.asp');
// max-width
$ref['dimensions']['max_width'] = array(
	'title' => 'Maximum Width', 
	'ref_desc' => "The max-width property sets the maximum width of an element. Note: The max-width property does not include padding, borders, or margins",
	'ref_values' => array(
		"(numeric)" => "e.g. '400' would set the maximun width to 400 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '50%' in the Max Width field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_dim_max-width.asp');
// max-height
$ref['dimensions']['max_height'] = array(
	'title' => 'Maximum Height', 
	'ref_desc' => "The max-height property sets the maximum height of an element. Note: The max-height property does not include padding, borders, or margins",
	'ref_values' => array(
		"(numeric)" => "e.g. '400' would set the maximun height to 400 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified)."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_dim_max-width.asp');		
/*** Padding ***/
// padding top
$ref['padding']['padding_top'] = array(
	'title' => 'Padding Top', 
	'ref_desc' => "The padding-top property sets the top padding (space) of an element. The space is created <i>inside</i> the element's border.",
	'ref_values' => array(
		"(numeric)" => "e.g. '15' would set the top padding for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Top Padding field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_padding-top.asp');
// padding right
$ref['padding']['padding_right'] = array(
	'title' => 'Padding Right', 
	'ref_desc' => "The padding-right property sets the right padding (space) of an element. The space is created <i>inside</i> the element's border.",
	'ref_values' => array(
		"(numeric)" => "e.g. '15' would set the right padding for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Right Padding field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_padding-right.asp');
// padding bottom
$ref['padding']['padding_bottom'] = array(
	'title' => 'Padding Bottom', 
	'ref_desc' => "The padding-bottom property sets the bottom padding (space) of an element. The space is created <i>inside</i> the element's border.",
	'ref_values' => array(
		"(numeric)" => "e.g. '15' would set the bottom padding for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Bottom Padding field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_padding-bottom.asp');
// padding left
$ref['padding']['padding_left'] = array(
	'title' => 'Padding Left', 
	'ref_desc' => "The padding-left property sets the left padding (space) of an element. The space is created <i>inside</i> the element's border.",
	'ref_values' => array(
		"(numeric)" => "e.g. '15' would set the left padding for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Left Padding field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_padding-left.asp');
/*** Margin ***/	
// margin top
$ref['margin']['margin_top'] = array(
	'title' => 'Margin Top', 
	'ref_desc' => "The margin-top property sets the top margin (space) of an element. The space is created <i>outside</i> the element's border.",
	'ref_values' => array(
		"(numeric)" => "e.g. '15' would set the top margin for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Top Margin field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_margin-top.asp');
// margin right
$ref['margin']['margin_right'] = array(
	'title' => 'Margin Right', 
	'ref_desc' => "The margin-right property sets the right margin (space) of an element. The space is created <i>outside</i> the element's border.",
	'ref_values' => array(
		"(numeric)" => "e.g. '15' would set the right margin for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Right Margin field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_margin-right.asp');
// margin bottom
$ref['margin']['margin_bottom'] = array(
	'title' => 'Margin Bottom', 
	'ref_desc' => "The margin-bottom property sets the bottom margin (space) of an element. The space is created <i>outside</i> the element's border.",
	'ref_values' => array(
		"(numeric)" => "e.g. '15' would set the bottom margin for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Bottom Margin field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_margin-bottom.asp');
// margin left
$ref['margin']['margin_left'] = array(
	'title' => 'Margin Left', 
	'ref_desc' => "The margin-left property sets the left margin (space) of an element. The space is created <i>outside</i> the element's border.",
	'ref_values' => array(
		"(numeric)" => "e.g. '15' would set the left margin for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Left Margin field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_margin-left.asp');
/*** Border ***/	
// border top color
$ref['border']['border_top_color'] = array(
	'title' => 'Border Top Color', 
	'ref_desc' => "The border-top-color property sets the top border color of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.",
	'ref_values' => array(
		"(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Top Border Color text field to reveal the color picker."),
	'w3s' => 'http://www.w3schools.com/cssref/pr_border-top_color.asp');
// border right color
$ref['border']['border_right_color'] = array(
	'title' => 'Border Right Color', 
	'ref_desc' => "The border-right-color property sets the right border color of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.",
	'ref_values' => array(
		"(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Right Border Color text field to reveal the color picker."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_border-right_color.asp');
// border bottom color
$ref['border']['border_bottom_color'] = array(
	'title' => 'Border Bottom Color', 
	'ref_desc' => "The border-bottom-color property sets the bottom border color of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.",
	'ref_values' => array(
		"(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Bottom Border Color text field to reveal the color picker."),
	'w3s' => 'http://www.w3schools.com/cssref/pr_border-bottom_color.asp');
// border left color
$ref['border']['border_left_color'] = array(
	'title' => 'Border Left Color', 
	'ref_desc' => "The border-left-color property sets the left border color of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.",
	'ref_values' => array(
		"(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Left Border Color text field to reveal the color picker."),
	'w3s' => 'http://www.w3schools.com/cssref/pr_border-left_color.asp');
// border top width
$ref['border']['border_top_width'] = array(
	'title' => 'Border Top Width', 
	'ref_desc' => "The border-top-width property sets the top border width of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.",
	'ref_values' => array(
		"(numeric)" => "e.g. '15' would set the top border width for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Top Border Width field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_border-top_width.asp');
// border right width
$ref['border']['border_right_width'] = array(
	'title' => 'Border Right Width', 
	'ref_desc' => "The border-right-width property sets the right border width of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.",
	'ref_values' => array(
		"(numeric)" => "e.g. '15' would set the right border for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Right Border Width field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_border-right_width.asp');
// border bottom width
$ref['border']['border_bottom_width'] = array(
	'title' => 'Border Bottom Width', 
	'ref_desc' => "The border-bottom-width property sets the bottom border width of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.",
	'ref_values' => array(
		"(numeric)" => "e.g. '15' would set the bottom border for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Bottom Border Width field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_border-bottom_width.asp');
// border left width
$ref['border']['border_left_width'] = array(
	'title' => 'Border Left Width', 
	'ref_desc' => "The border-left-width property sets the left border width of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.",
	'ref_values' => array(
		"(numeric)" => "e.g. '15' would set the left border for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Left Border Width field."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_border-left_width.asp');
// border style
$ref['border']['border_style'] = array(
	'title' => 'Border Style', 
	'ref_desc' => "The border-style property sets the style of an element's four borders.",
	'ref_values' => array(
		"hidden" => "The same as 'none', except in border conflict resolution for table elements",
		"dotted" => "Specifies a dotted border",
		"dashed" => "Specifies a dashed border",
		"solid" => "Specifies a solidborder",
		"double" => "Specifies a double border",
		"groove" => "Specifies a 3D grooved border. The effect depends on the border-color value",
		"ridge" => "Specifies a 3D ridged border. The effect depends on the border-color value!",
		"inset" => "Specifies a 3D inset border. The effect depends on the border-color value",
		"outset" => "Specifies a 3D outset border. The effect depends on the border-color value",
		"none" => "Specifies no border"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_background-position.asp');	
/*** Behaviour ***/
// display
$ref['behaviour']['display'] = array(
	'title' => 'Display', 
	'ref_desc' => "The display property specifies the type of box an element should generate.",
	'ref_values' => array(
		"block" => "The element will generate a block box (a line break before and after the element)",
		"inline" => "The element will generate an inline box (no line break before or after the element)",
		"inline-block" => "The element will generate a block box, laid out as an inline box",
		"none" => "The element doesn't appear on the page at all"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_class_display.asp');
// float
$ref['behaviour']['float'] = array(
	'title' => 'Float', 
	'ref_desc' => "The float property specifies whether or not an element should float.",
	'ref_values' => array(
		"left" => "The element floats to the left",
		"right" => "The element floats to the right",
		"none" => "The element is not floated. This is the default."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_class_float.asp');
// clear
$ref['behaviour']['clear'] = array(
	'title' => 'Clear', 
	'ref_desc' => "The clear property specifies which sides of an element where other floating elements are not allowed.",
	'ref_values' => array(
		"left" => "No floating elements allowed on the left side",
		"right" => "No floating elements allowed on the right side",
		"both" => "No floating elements allowed on the left or the right side",
		"none" => "Default. Allows floating elements on both sides"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_class_clear.asp');
// overflow
$ref['behaviour']['overflow'] = array(
	'title' => 'Overflow', 
	'ref_desc' => "The overflow property specifies what happens if content overflows an element's box.",
	'ref_values' => array(
		"visible" => "The overflow is not clipped. It renders outside the element's box. This is default",
		"scroll" => "The overflow is clipped, but a scroll-bar is added to see the rest of the content",
		"auto" => "If overflow is clipped, a scroll-bar should be added to see the rest of the content",
		"hidden" => "The overflow is clipped, and the rest of the content will be invisible"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_pos_overflow.asp');	
// visibility
$ref['behaviour']['visibility'] = array(
	'title' => 'Visibility', 
	'ref_desc' => "The visibility property specifies whether or not an element is visible. But unlike setting 'display' to 'none', if you set 'visibility' to 'hidden' the hidden element will still take up the same space on the page - it just won't be visible.",
	'ref_values' => array(
		"visible" => "The element is visible. This is default",
		"hidden" => "The element is invisible (but still takes up space)",
		"collapse" => "Only for table elements. collapse removes a row or column, but it does not affect the table layout. The space taken up by the row or column will be available for other content. If collapse is used on other elements, it renders as 'hidden'"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_class_visibility.asp');
// Cursor
$ref['behaviour']['cursor'] = array(
	'title' => 'Cursor', 
	'ref_desc' => "The cursor property specifies the type of cursor to be displayed when pointing on an element.",
	'ref_values' => array(
		"auto" => "	Default. The browser sets a cursor",
		"crosshair" => "The cursor render as a crosshair",
		"default" => "The default cursor",
		"e-resize" => "The cursor indicates that an edge of a box is to be moved right (east)",
		"help" => "The cursor indicates that help is available",
		"move" => "The cursor indicates something that should be moved",
		"n-resize" => "The cursor indicates that an edge of a box is to be moved up (north)",
		"ne-resize" => "The cursor indicates that an edge of a box is to be moved up and right (north/east)",
		"nw-resize" => "The cursor indicates that an edge of a box is to be moved up and left (north/west)",
		"pointer" => "The cursor render as a pointer",
		"progress" => "	The cursor indicates that the program is busy (in progress)",
		"s-resize" => "The cursor indicates that an edge of a box is to be moved down (south)",
		"se-resize" => "The cursor indicates that an edge of a box is to be moved down and right (south/east)",
		"sw-resize" => "The cursor indicates that an edge of a box is to be moved down and left (south/west)",
		"text" => "The cursor indicates text",
		"w-resize" => "The cursor indicates that an edge of a box is to be moved left (west)",
		"wait" => "The cursor indicates that the program is busy"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_class_visibility.asp');
// opacity
$ref['behaviour']['opacity'] = array(
	'title' => 'Opacity', 
	'ref_desc' => "The opacity property sets the opacity level for an element. You can enter any numeric value between 0 and 1 (e.g. 0.25 or 0.9)",
	'ref_values' => array(
		"(decimal 0 - 1)" => "e.g. '0.5' would set the opacity to 50% (half transparent)."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_border-top_width.asp');		
/*** Position ***/		
// position
$ref['position']['position'] = array(
	'title' => 'Position', 
	'ref_desc' => "The position property is used to position an element.",
	'ref_values' => array(
		"absolute" => "Generates an absolutely positioned element, positioned relative to the first parent element that has a position other than static. The element's position is specified with the 'left', 'top', 'right', and 'bottom' properties",
		"relative" => "	Generates a relatively positioned element, positioned relative to its normal position. The element's position is specified with the 'left', 'top', 'right', and 'bottom' properties",
		"fixed" => "Generates an absolutely positioned element, positioned relative to the browser window. The element's position is specified with the 'left', 'top', 'right', and 'bottom' properties",
		"static" => "Default. No position, the element occurs in the normal flow (ignores any top, bottom, left, right, or z-index declarations)"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_class_position.asp');		
// z-index
$ref['position']['z_index'] = array(
	'title' => 'Z-index', 
	'ref_desc' => "The z-index property specifies the stack order of an element. An element with greater stack order is always in front of an element with a lower stack order. Note: z-index only works on positioned elements (position:absolute, position:relative, or position:fixed).",
	'ref_values' => array(
		"(numeric)" => "If you had 2 absolutely positioned elements that overlapped and you gave element A a z-index value of 5 and element B a z-index value of 10, element B would show in front of element A."), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_pos_z-index.asp');	
// top
$ref['position']['top'] = array(
	'title' => 'Top', 
	'ref_desc' => "For absolutely positioned elements, the top property sets the top edge of an element to a unit above/below the top edge of its containing element. For relatively positioned elements, the top property sets the top edge of an element to a unit above/below its normal position. Negative values are allowed.</p>
	<p><b>Note</b>: the way an element moves on screen when you apply a positive 'top' value may seem counterintuitive. It moves down on the screen when given a positive value because the browser increases the distance between the top of the element and some reference point. If in doubt, just look at the directional icon <img src='../images/position_top.gif' />. The icon depicts the direction the element will move on the page as you increase the value for 'top'.",
	'ref_values' => array(
		"(numeric)" => "e.g. '75' would move an element 75 pixels below the top edge of its parent element (if the element is absolutely positioned), or 75px below it's normal position (if the element is relatively positioned)"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_pos_top.asp');
// right
$ref['position']['right'] = array(
	'title' => 'Right', 
	'ref_desc' => "For absolutely positioned elements, the right property sets the right edge of an element to a unit to the left or right of the right edge of its containing element. For relatively positioned elements, the right property sets the right edge of an element to a unit to the left or right of its normal position. Negative values are allowed.</p>
	<p><b>Note</b>: the way an element moves on screen when you apply a positive 'right' value may seem counterintuitive. It moves left on the screen when given a positive value because the browser increases the distance between the right of the element and some reference point. If in doubt, just look at the directional icon <img src='../images/position_right.gif' />. The icon depicts the direction the element will move on the page as you increase the value for 'right'.",
	'ref_values' => array(
		"(numeric)" => "e.g. '20' would move an element 20 pixels to the left of the right edge of its parent element (if the element is absolutely positioned), or 20px to the left of it's normal position (if the element is relatively positioned)"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_pos_right.asp');
// bottom
$ref['position']['bottom'] = array(
	'title' => 'Bottom', 
	'ref_desc' => "For absolutely positioned elements, the bottom property sets the bottom edge of an element to a unit above/below the bottom edge of its containing element. For relatively positioned elements, the bottom property sets the bottom edge of an element to a unit above/below its normal position. Negative values are allowed.</p>
	<p><b>Note</b>: the way an element moves on screen when you apply a positive 'bottom' value may seem counterintuitive. It moves up on the screen when given a positive value because the browser increases the distance between the bottom of the element and some reference point. If in doubt, just look at the directional icon <img src='../images/position_bottom.gif' />. The icon depicts the direction the element will move on the page as you increase the value for 'bottom'.",
	'ref_values' => array(
		"(numeric)" => "e.g. '75' would move an element 75 pixels below the bottom edge of its parent element (if the element is absolutely positioned), or 75px below it's normal position (if the element is relatively positioned)"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_pos_bottom.asp');
// left
$ref['position']['left'] = array(
	'title' => 'Left', 
	'ref_desc' => "For absolutely positioned elements, the left property sets the left edge of an element to a unit to the 
	left or right of the left edge of its containing element. For relatively positioned elements, the left property sets the left edge of an element to a unit to the left or right of its normal position. Negative values are allowed.</p>
	<p><b>Note</b>: the way an element moves on screen when you apply a positive 'left' value may seem counterintuitive. It moves right on the screen when given a positive value because the browser increases the distance between the left of the element and some reference point. If in doubt, just look at the directional icon <img src='../images/position_left.gif' />. The icon depicts the direction the element will move on the page as you increase the value for 'left'.",
	'ref_values' => array(
		"(numeric)" => "e.g. '20' would move an element 20 pixels to the right of the left edge of its parent element (if the element is absolutely positioned), or 20px to the right of it's normal position (if the element is relatively positioned)"), 
	'w3s' => 'http://www.w3schools.com/cssref/pr_pos_left.asp');
/*** CSS3 ***/
// Gradient A
$ref['CSS3']['gradient_a'] = array(
	'title' => 'Gradient A', 
	'ref_desc' => "One of the colors in the linear gradient. The color you specify here will gradually blend into the color you specify for Gradient B if you specify one. If you don't specify a color for Gradient B, Gradient A will blend into Gradient C.",
	'ref_values' => array(
		"(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the 'Gradient A' text field to reveal the color picker."), 
	'w3s' => '');
// Gradient B
$ref['CSS3']['gradient_b'] = array(
	'title' => 'Gradient B (optional)', 
	'ref_desc' => "One of the colors in the linear gradient. The color you specify here will gradually blend into Gradient A and C. <b>Gradient B is optional</b>. If you just want to blend 2 colors, you only need to specify a value for Grandient A and C. If you do use it, there is an additional option for Gradient B: 'B Position (optional)'. Click the label for this option for details.",
	'ref_values' => array(
		"(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the 'Gradient B' text field to reveal the color picker."), 
	'w3s' => '');
// B Position (optional)
$ref['CSS3']['gradient_b_pos'] = array(
	'title' => 'B Position (optional)', 
	'ref_desc' => "The position of Gradient B in relation to Gradient A. Gradient B is the middle color of the gradient, but it doesn't have to appear exactly in between Gradient A and C. If you were to specify a 'B Position' of 10% (for instance) Gradient B would begin almost immediately after Gradient A. If you were to specify a 'B Position' of 90%, Gradient B wouldn't begin until just before Gradient C. If you leave this setting blank, or specify a value of 50% Gradient B, will be placed exactly between Gradient A and C.",
	'ref_values' => array(
		"(numeric)" => "For consistency, the default unit is pixels. So if you enter 25 it will default to 25px. But it's more common to specify gradient postions in percentages. So you might put 15% (specifiying the unit so it doesn't default to pixels)"), 
	'w3s' => '');
// Gradient C
$ref['CSS3']['gradient_c'] = array(
	'title' => 'Gradient C', 
	'ref_desc' => "One of the colors in the linear gradient. The color you specify here will gradually blend into the color you specify for Gradient B if you specify one. If you don't specify a color for Gradient B, Gradient C will blend into Gradient A.",
	'ref_values' => array(
		"(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the 'Gradient C' text field to reveal the color picker."), 
	'w3s' => '');
// Gradient Angle
$ref['CSS3']['gradient_angle'] = array(
	'title' => 'Gradient Angle', 
	'ref_desc' => "The angel of the gradient. You have 8 options which cover all possible horizontal, vertical and perfectly diagonal variations. Note: old versions of Safari and Chrome (< Chrome 10 and < Safari 5.1) don't properly support diagonal gradients.",
	'ref_values' => array(
		"top to bottom" => "Gradient A blending from top to bottom into Gradient B or C.",
		"bottom to top" => "Gradient A blending from bottom to top into Gradient B or C.",
		"left to right" => "Gradient A blending from left to right into Gradient B or C.",
		"right to left" => "Gradient A blending from right to left into Gradient B or C.",
		"top left to bottom right" => "Gradient A blending from top left to bottom right into Gradient B or C.",
		"bottom right to top left" => "Gradient A blending from bottom right to top left into Gradient B or C.",
		"top right to bottom left" => "Gradient A blending from top right to bottom left into Gradient B or C.",
		"bottom left to top right" => "Gradient A blending from bottom left to top right into Gradient B or C."
		), 
	'w3s' => '');
// border radius top left
$ref['CSS3']['radius_top_left'] = array(
	'title' => 'Top Left Border Radius', 
	'ref_desc' => "The top left radius property defines the shape of the border of the top-left corner. A higher value creates a more rounded curve.",
	'ref_values' => array(
		"(numeric)" => "e.g. '5' would set the top left border radius for an element to 5 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%'."), 
	'w3s' => 'http://www.w3schools.com/cssref/css3_pr_border-top-left-radius.asp');	
	
// border radius top right
$ref['CSS3']['radius_top_right'] = array(
	'title' => 'Top Right Border Radius', 
	'ref_desc' => "The top right radius property defines the shape of the border of the top-right corner. A higher value creates a more rounded curve.",
	'ref_values' => array(
		"(numeric)" => "e.g. '5' would set the top right border radius for an element to 5 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%'."), 
	'w3s' => 'http://www.w3schools.com/cssref/css3_pr_border-top-right-radius.asp');
// border radius bottom left
$ref['CSS3']['radius_bottom_left'] = array(
	'title' => 'Bottom Left Border Radius', 
	'ref_desc' => "The bottom left radius property defines the shape of the border of the bottom-left corner. A higher value creates a more rounded curve.",
	'ref_values' => array(
		"(numeric)" => "e.g. '5' would set the bottom left border radius for an element to 5 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%'."), 
	'w3s' => 'http://www.w3schools.com/cssref/css3_pr_border-bottom-left-radius.asp');	
// border radius bottom right
$ref['CSS3']['radius_bottom_right'] = array(
	'title' => 'Bottom Right Border Radius', 
	'ref_desc' => "The bottom right radius property defines the shape of the border of the bottom-right corner. A higher value creates a more rounded curve.",
	'ref_values' => array(
		"(numeric)" => "e.g. '5' would set the bottom right border radius for an element to 5 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%'."), 
	'w3s' => 'http://www.w3schools.com/cssref/css3_pr_border-bottom-right-radius.asp');
// box shadow color
$ref['CSS3']['box_shadow_color'] = array(
	'title' => 'Box Shadow Color', 
	'ref_desc' => "The color of the shadow. Choosing a darker version of the parent element's background color produces the most natural effect.",
	'ref_values' => array(
		"(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Color text field to reveal the color picker."), 
	'w3s' => '');	
// box shadow x offset
$ref['CSS3']['box_shadow_x'] = array(
	'title' => 'Box Shadow X-offset', 
	'ref_desc' => "The position of the horizontal shadow. Negative values are allowed",
	'ref_values' => array(
		"(numeric)" => "e.g. '15' would create a 15 pixel shadow to the right of the element (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '2%'."), 
	'w3s' => '');
// box shadow y offset
$ref['CSS3']['box_shadow_y'] = array(
	'title' => 'Box Shadow Y-offset', 
	'ref_desc' => "The position of the vertical shadow. Negative values are allowed",
	'ref_values' => array(
		"(numeric)" => "e.g. '-10' would create a 10 pixel shadow above the element (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '-2%'."), 
	'w3s' => '');
// box shadow blur
$ref['CSS3']['box_shadow_blur'] = array(
	'title' => 'Box Shadow Blur', 
	'ref_desc' => "The blur distance. If you defined a black shadow and a blur of 10px, their would be a 10 pixel blur area where the shadow faded evenly from black to transparent at every extremity.",
	'ref_values' => array(
		"(numeric)" => "e.g. '10' would create a 10 pixel blur at the edge of the element's shadow (Microthemer automatically adds the 'px' unit if a unit isn't specified. Other commonly used units include 'em' and '%'. So you could enter '5%'.)"), 
	'w3s' => '');
	
	
// function for showing all CSS properties
function show_css_index($ref) {
	// output all help snippets
	$i = 1;
	foreach ($ref as $property_group => $prop_array) {
		?>
        <ul id="<?php echo $property_group; ?>" class="css-index <?php if ($i&1) { echo 'odd'; } ?>">
        <li class="list-heading"><?php echo ucwords($property_group); ?></li>
        <?php
		foreach ($prop_array as $property_id => $array) {
			?>
            <li class="property-item"><a href="css-reference.php?snippet=<?php echo $property_id; ?>&prop_group=<?php echo $property_group; ?>">
			<?php echo $array['title']; ?></a></li><?php
		}
		++$i;
		?>
        </ul>
        <?php
	}
	?>
    <ul class="css-index">
    	<li class="pie-link"><a href="css-reference.php?snippet=pie&prop_group=pie">CSS3 PIE</a></li>
     </ul>
    <?php
}

// if a label hasn't been passed
if (!isset($_GET['snippet'])) {
	?>
   <div class='content-box'>
   	<h3>CSS Properties</h3>
    <div class="inner-box">
    <?php show_css_index($ref); ?>
    </div>
    </div>
    <?php
}

// else just get the snippet
else {
	
	// if pie explanation
	if ($label == 'pie') {
		?>
		<div id='<?php echo $label; ?>' class='snippet content-box'>
			<h3>CSS3 PIE by Jason Johnston</h3>
            <div class="inner-box">
			<p>Microthemer comes pre-integrated with CSS3 PIE which is enabled by default. CSS3 PIE makes Internet Explorer 6-9 render CSS3 properties like Gradients, Border Radius and Box Shadow correctly. We recommend that you visit 
            the <a href="http://css3pie.com/" target="_balnk">CSS3 PIE site</a> to learn more about it.</p>
            <p>One of the main drawbacks with PIE is that it is very frequently necessary to give elements a "position" value of "relative" when also assigning CSS3 properties to them. To make this less tedious, Microthemer automatically applies "position:relative" when you set CSS3 properties.</p> <p>You can still use PIE and turn off the automatic "position:relative" by simply setting the "position" value to something (e.g. "static", "absolute" or "fixed"). However, sometimes it's better to just not use PIE and allow corners to be square or backgrounds to be solid colors in Internet Explorer versions that don't support CSS3 properties. For this reason, we give you the option to disable PIE on individual selectors.</p>
            <p><b>Some Known PIE Shortcomings</b></p>
            
            <ul>
             <li>PIE does not currently work when applied to the "Body" Selector</li>
             <li>Element types that cannot accept children (e.g. "Input" and "Image") will fail or throw errors if you apply styles that use relative length units such as em or ex. Stick to using px units for these elements (Microthemer automatically applies "px" if no unit is set - apart from line-height as it is a valid (and useful) not to include a unit).</li>
             <li>There is another work around that avoids the "position:relative" fix mentioned above. You can make the ancestor element position:relative and give it a z-index. An ancester element is an element that contains another element. With WordPress, the "Post" element is the often an ancester of the "Meta Info Wrapper" element. So you could make PIE render gradients properly on the "Meta Info Wrapper" without giving the Meta Info Wrapper "position:relative" by explicitly setting "position:static" on the "Meta Info Wrapper" and then giving the "Post" element "position:relative" and a z-index value of 50 (for instance).</li>
           </ul>
           <br />
           <p><b>Donate to PIE:</b> PIE is Free for everyone. Please consider <a href="http://css3pie.com/" target="_balnk">donating to the PIE project</a> if it has helped you.</p>
           
           <h4>CSS Properties</h4>
            <?php show_css_index($ref); ?>
            
        </div>
		</div>
		<?php
	}
	// else property explanation
	else {
		?>
		<div id='<?php echo $label; ?>' class='snippet content-box'>
			<h3><?php echo $ref[$prop_group][$label]['title']; ?></h3>
            <div class="inner-box">
			<p><?php echo $ref[$prop_group][$label]['ref_desc']; ?></p>
			<table>
			 <thead>
			 <tr class='heading'>
			  <th>Value</th><th>Description</th>
			 </tr>
             </thead>
             <tbody>
			 <?php
			 $i = 1;
             if (is_array($ref[$prop_group][$label]['ref_values'])){
                 foreach ($ref[$prop_group][$label]['ref_values'] as $value => $desc) {
                     ?>
                     <tr <?php if ($i&1) { echo 'class="odd"'; } ?>>
                         <td class="value"><?php echo $value; ?></td><td><?php echo $desc; ?></td>
                     </tr>
                     <?php
                     ++$i;
                 }
             } else {
                 ?>
                 <tr class="odd">
                     <td class="value" colspan="2">Documentation for this CSS property is on the way!</td>
                 </tr>
                 <?php
             }
			 ?>
             </tbody>
			</table>
            <br />
            <h4>Other CSS Properties</h4>
            <?php show_css_index($ref); ?>
            <br />
			<?php
			if ( !empty($ref[$prop_group][$label]['w3s']) ) {
				?>
				<p>Detailed information at <a target='_blank' href='<?php echo $ref[$prop_group][$label]['w3s']; ?>'>W3 Schools</a></p>
				<?php
			}
			?>
            </div>
		</div>
		<?php
	}
}

include 'docs-footer.inc.php'; ?>

</body>
</html>
