<?php
// Stop direct call
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    die('Please do not call this page directly.');
}
$propertyOptions = array();
// font
$propertyOptions['font']['font_family'] = array(
    'short_label' => 'Family',
    'label' => 'Font Family',
    'type' => 'combobox',
    'input-class' => 'tvr-font-select size-big',
    'select_options' => array(
		'',
        'Google Font...',
        'Arial',
        '"Book Antiqua"',
        '"Bookman Old Style"',
        '"Arial Black"',
        'Charcoal',
        '"Comic Sans MS"',
        'cursive',
        'Courier',
        '"Courier New"',
        'Gadget',
        'Garamond',
        'Geneva',
        'Georgia',
        'Helvetica',
        'Impact',
        '"Lucida Console"',
        '"Lucida Grande"',
        '"Lucida Sans Unicode"',
        'Monaco',
        'monospace',
        '"MS Sans Serif"',
        '"MS Serif"',
        '"New York"',
        'Palatino',
        '"Palatino Linotype"',
        'sans-serif',
        'serif',
        'Symbol',
        'Tahoma',
        '"Times New Roman"',
        'Times',
        '"Trebuchet MS"',
        'Verdana',
        'Webdings',
        'Wingdings',
        '"Zapf Dingbats"'
    ),
    'icon' => '33, 14',
    // ref
    'ref_desc' => "<p>The font-family property specifies the type of font for an element. You can specify just one font such as Arial, but it\'s also commonplace to specify multiple fonts separated by commas e.g. \"Book Antiqua\", Garamond, serif (hence the name font-family). The browser will try to load the first font, in this case \"Book Antiqua\". If the user doesn't have that font installed on their computer the second font will be loaded (e.g. Garamond) or the third - and so on.  Note that if a font has spaces in the name in needs to be wrapped in quotes as has been done with \"Book Antiqua\".</p>",
    'ref_values' => array(
        "font name" => "The name of the font e.g. 'Arial'"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_font_font-family.asp');

$propertyOptions['font']['google_font'] = array(
    'short_label' => 'Google Font',
    'label' => 'Google Font Family',
    'field-class' => 'tvr-font-custom',
    'input-class' => 'size-big',
    'icon' => '30',
    // ref
    'ref_desc' => "<p>Choose from hundreds of free, open-source fonts optimized for the web. Google Fonts is a service Google offers free of charge that allows web designers to use a wide variety of openly licensed fonts on their web pages. Prior to font services like Google Web Fonts, web designers were limited to using a small selection of 'web safe' fonts that were very likely to be installed on any computer. Now we can use Google fonts that may not be installed on a user's computer (because they are downloaded from Google). Microthemer makes use of Google's Web Fonts API to make adding Google Fonts really easy. Just select 'Google Fonts...' from the font-family menu, and then click the 'Use This Font' link next to the name of the font. For efficiency, Microthemer only loads the Google Fonts you've specified in your Microthemer workspace settings. This automation makes experimenting with different Google fonts hassle free.</p>",
    'ref_values' => array(
        "font name" => "The name of the font e.g. 'Oswald'. Note Microthemer includes the variation e.g. (normal) or (italic 700) in brackets so that Microthemer can download the correct font file (and only that font file) from Google."),
    'w3s' => 'http://www.google.com/fonts/'
);
$propertyOptions['font']['color'] = array(
    'short_label' => 'Color',
    'label' => 'Color',
    'input-class' => 'color',
    'icon' => '26',
    // ref
    'ref_desc' => "<p>The color property specifies the color of text. The reason it wasn't named \"text-color\" is a mystery.</p>",
    'ref_values' => array(
        "(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Color text field to reveal the color picker."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_text_color.asp'
);
$propertyOptions['font']['font_size'] = array(
    'short_label' => 'Size',
    'label' => 'Font Size',
    'field-class' => 'icon-size-0',
    'input-class' => 'size-0',
    'default_unit' => 'px',
    'icon' => '21',
    // ref
    'ref_desc' => "<p>As you might imagine, the font-size property sets the font-size of text.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '12' would set the font size to 12 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '1.2em' in the Font Size field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_font_font-size.asp'
);
$propertyOptions['font']['line_height'] = array(
    'short_label' => 'Line Height',
    'label' => 'Line Height',
    'input-class' => 'size-0',
    'icon' => '22',
    // ref
    'ref_desc' => "<p>The line-height property specifies the line height of an element. A line of text will be vertically centered within the specified height.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '1.5' would set the line height to 18 pixels if the font-size for the element was set to 12px (12 x 1.5).
        Note that with line-height it is valid and often advisable not to specify a unit value, thus allowing the browser to calculate line-height based on font-size. Microthemer therefore does not automatically add the 'px' unit if no unit is specified."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_dim_line-height.asp'
);
$propertyOptions['font']['font_weight'] = array(
    'short_label' => 'Weight',
    'label' => 'Font Weight',
    'field-class' => 'icon-size-0a',
    'input-class' => 'size-2',
    'type' => 'combobox',
    'select_options' => array(
		'',
        "normal",
        "bold",
        "100",
        "200",
        "300",
        "400",
        "500",
        "600",
        "700",
        "800",
        "900"
    ),
    'icon' => '27',
    // ref
    'ref_desc' => "<p>The font-weight property sets the thickness of text characters.</p>",
    'ref_values' => array(
        "normal" => "Defines normal characters. This is default",
        "bold" => "Defines thick characters",
        "100" => "a numeric definition of thickness",
        "200" => "a numeric definition of thickness",
        "300" => "a numeric definition of thickness",
        "400" => "a numeric definition of thickness (equivalent to the 'normal' keyword)",
        "500" => "a numeric definition of thickness",
        "600" => "a numeric definition of thickness",
        "700" => "a numeric definition of thickness (equivalent to the 'bold' keyword)",
        "800" => "a numeric definition of thickness",
        "900" => "a numeric definition of thickness"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_font_weight.asp'
);
$propertyOptions['font']['font_style'] = array(
    'short_label' => 'Style',
    'label' => 'Font Style',
    'field-class' => 'icon-size-0a',
    'type' => 'combobox',
    'select_options' => array(
		'',
        "normal",
        "italic",
        "oblique"),
    'icon' => '28',
    // ref
    'ref_desc' => "<p>The font-style property specifies the font style for text. Generally used for setting italic text.</p>",
    'ref_values' => array(
        "normal" => "The browser displays a normal font style. This is default",
        "italic" => "The browser displays an italic font style",
        "oblique" => "The browser displays an oblique font style"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_font_font-style.asp'
);
$propertyOptions['font']['text_decoration'] = array(
    'short_label' => 'Decoration',
    'label' => 'Text Decoration',
    'field-class' => 'icon-size-0a',
    'input-class' => 'size-5',
    'type' => 'combobox',
    'select_options' => array(
		"",
        "underline",
        "overline",
        "line-through",
        "none"),
    'icon' => '31',
    // ref
    'ref_desc' => "<p>The text-decoration property specifies the line decoration below, above, or through text.</p>",
    'ref_values' => array(
        "underline" => "Defines a line below the text",
        "overline" => "Defines a line above the text",
        "line-through" => "Defines a line through the text",
        "none" => "	Defines normal text. This is default"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_text_text-decoration.asp'
);
$propertyOptions['font']['font_variant'] = array(
    'short_label' => 'Variant',
    'label' => 'Font Variant',
    'field-class' => 'icon-size-2',
    'input-class' => 'size-4',
    'type' => 'combobox',
    'select_options' => array(
		'',
        "normal",
        "small-caps"),
    'icon' => '29',
    // ref
    'ref_desc' => "<p>The font-variant property specifies whether or not text should
be displayed in a small-caps font. And that is the only option!</p>",
    'ref_values' => array(
        "normal" => "The browser displays a normal font. This is default",
        "small-caps" => "The browser displays a small-caps font"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_font_font-variant.asp'
);
// text
$propertyOptions['text']['text_align'] = array(
    'short_label' => 'Text Align',
    'label' => 'Text Align',
    'input-class' => 'size-1',
    'type' => 'combobox',
    'select_options' => array(
		'',
        "left",
        "right",
        "center",
        "justify"),
    'icon' => '38, 14',
    // ref
    'ref_desc' => "<p>The text-align property specifies the horizontal alignment of text in an element.</p>",
    'ref_values' => array(
        "left" => "Aligns the text to the left. This is default",
        "right" => "Aligns the text to the right",
        "center" => "Centers the text",
        "justify" => "Stretches the lines so that each line has equal width (like in newspapers and magazines)"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_text_text-align.asp'
);
$propertyOptions['text']['text_indent'] = array(
    'short_label' => 'Indent',
    'label' => 'Text Indent',
    'field-class' => 'icon-size-2',
    'input-class' => 'size-0b',
    'default_unit' => 'px',
    'icon' => '23, 4',
    // ref
    'ref_desc' => "<p>The text-indent property specifies the indentation of the first line in a text-block. You might choose to set this property on dense articles of text to improve readability where paragraphs have no bottom margin.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '80' would set the text indent to 80 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '15%' in the Text Indent field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_text_text-indent.asp'
);
$propertyOptions['text']['text_transform'] = array(
    'short_label' => 'Transform',
    'label' => 'Text Transform',
    'input-class' => 'size-4',
    'type' => 'combobox',
    'select_options' => array(
		'',
        "capitalize",
        "uppercase",
        "lowercase",
        "none"),
    'field-class' => 'last',
    'icon' => '32',
    // ref
    'ref_desc' => "<p>The text-transform property controls the capitalization of text.</p>",
    'ref_values' => array(
        "capitalise" => "Transforms the first character of each word to uppercase",
        "uppercase" => "Transforms all characters to uppercase",
        "lowercase" => "Transforms all characters to lowercase",
        "none" => "No capitalization. The text renders as it is. This is default"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_text_text-transform.asp'
);
$propertyOptions['text']['word_spacing'] = array(
    'short_label' => 'Word Spacing',
    'field-class' => 'icon-size-4',
    'input-class' => 'size-0b',
    'label' => 'Word Spacing',
    'default_unit' => 'px',
    'icon' => '22, 4',
    // ref
    'ref_desc' => "<p>The word-spacing property increases or decreases the white space between words.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '10' would set the font size to 10 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '1em' in the Word Spacing field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_text_word-spacing.asp'
);
$propertyOptions['text']['letter_spacing'] = array(
    'short_label' => 'Letter Spacing',
    'label' => 'Letter Spacing',
    'input-class' => 'size-0b',
    'default_unit' => 'px',
    'icon' => '24',
    // ref
    'ref_desc' => "<p>The letter-spacing property increases or decreases the space between characters in text.<p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '2' would set the letter spacing to 2 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified)."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_text_text-indent.asp'
);
$propertyOptions['text']['word_wrap'] = array(
    'short_label' => 'Word Wrap',
    'label' => 'Word Wrap',
    'input-class' => 'size-4a',
    'type' => 'combobox',
    'select_options' => array(
		'',
        "normal",
        "break-word"),
    'icon' => '41, 14',
    // ref
);
$propertyOptions['text']['white_space'] = array(
    'short_label' => 'White Space',
    'label' => 'White Space',
    'input-class' => 'size-3a',
    'type' => 'combobox',
    'select_options' => array(
		'',
        "normal",
        "nowrap",
        "pre",
        "pre-line",
        "pre-wrap"),
    'icon' => '43, 14',
    // ref
);
$propertyOptions['text']['direction'] = array(
    'short_label' => 'Direction',
    'label' => 'Text Direction',
    'input-class' => '',
    'type' => 'combobox',
    'select_options' => array(
		'',
        "ltr",
        "rtl"),
    'icon' => '42, 14',
    // ref
);


// list (new group)
$propertyOptions['list']['list_style_type'] = array(
    'short_label' => 'Type',
    'label' => 'List Style Type',
    'input-class' => 'size-big',
    'type' => 'combobox',
    'select_options' => array(
		'',
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
    ),
    'field-class' => 'last',
    'icon' => '23',
    // ref
    'ref_desc' => "<p>The list-style-type specifies the type of list-item marker in a list. Circle, disc and square can be applied to unordered lists (&#60;ul&#62;), commonly referred to as bulleted lists. All the rest can be applied to ordered lists (&#60;ol&#62;), such as those with numbers at the start.</p>",
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
    'w3s' => 'http://www.w3schools.com/cssref/pr_list-style-type.asp'
);
$propertyOptions['list']['list_style_image'] = array(
    'short_label' => 'Image',
    'label' => 'List Style Image',
    'type' => 'combobox',
    'field-class' => 'last span-3',
    'input-class' => 'bg-image-select size-very-big strictly-dropdown',
    'select_options' => array(
        '',
        'none'
    ),
    'icon' => '28, 14',
    // ref
);
$propertyOptions['list']['list_style_position'] = array(
    'short_label' => 'Position',
    'label' => 'List Style Position',
    'field-class' => 'icon-size-2',
    'input-class' => 'size-2',
    'type' => 'combobox',
    'select_options' => array(
		'',
        "inside",
        "outside"
    ),
    'icon' => '27, 14',
    // ref
);
// shadow text/box (new group)
$propertyOptions['shadow']['text_shadow_color'] = array(
    'short_label' => 'Color',
    'label' => 'Text Shadow Color',
    'input-class' => 'color',
    'hide imp' => 1,
    'icon' => '41',
    // ref
    'ref_desc' => "<p>The color of the text shadow.</p>",
    'ref_values' => array(
        "(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the 'Text Shadow Color' text field to reveal the color picker."),
    'w3s' => ''
);
$propertyOptions['shadow']['text_shadow_x'] = array(
    'short_label' => 'X-Offset',
    'label' => 'Text Shadow x-offset',
    'input-class' => 'size-0b',
    'default_unit' => 'px',
    'hide imp' => 1,
    'icon' => '39',
    // ref
    'ref_desc' => "<p>The position of the horizontal shadow. Negative values are allowed</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '1' would create a 1 pixel shadow to the right of the text (Microthemer automatically adds the 'px' unit if a unit isn't specified)."),
    'w3s' => ''
);
$propertyOptions['shadow']['text_shadow_y'] = array(
    'short_label' => 'Y-Offset',
    'label' => 'Text Shadow y-offset',
    'input-class' => 'size-0b',
    'default_unit' => 'px',
    'hide imp' => 1,
    'icon' => '40',
    // ref
    'ref_desc' => "<p>The position of the vertical shadow. Negative values are allowed</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '-1' would create a 1 pixel shadow above the element (Microthemer automatically adds the 'px' unit if a unit isn't specified)."),
    'w3s' => ''
);
$propertyOptions['shadow']['text_shadow_blur'] = array(
    'short_label' => 'Blur',
    'label' => 'Text Shadow Blur',
    'input-class' => 'size-0b',
    'default_unit' => 'px',
    'field-class' => 'last',
    'icon' => '42',
    // ref
    'ref_desc' => "<p>The blur distance. If you define a black text shadow and a blur of 3px, their will be a 3 pixel blur area where the shadow fades evenly from black to transparent.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '4' would create a 4 pixel text blur."),
    'w3s' => ''
);

$propertyOptions['shadow']['box_shadow_color'] = array(
    'short_label' => 'Color',
    'label' => 'Box Shadow Color',
    'sub_label' => 'Box Shadow:',
    'field-class' => 'icon-size-3',
    'input-class' => 'color',
    'hide imp' => 1,
    'icon' => '36',
    // ref
    'ref_desc' => "<p>The color of the shadow. Choosing a darker version of the parent element's background color produces the most natural effect.</p>",
    'ref_values' => array(
        "(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Color text field to reveal the color picker."),
    'w3s' => ''
);
$propertyOptions['shadow']['box_shadow_x'] = array(
    'short_label' => 'X-Offset',
    'label' => 'Box Shadow x-offset',
    'default_unit' => 'px',
    'input-class' => 'size-0b',
    'hide imp' => 1,
    'icon' => '34',
    // ref
    'ref_desc' => "<p>The position of the horizontal shadow. Negative values are allowed</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '15' would create a 15 pixel shadow to the right of the element (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '2%'."),
    'w3s' => ''
);
$propertyOptions['shadow']['box_shadow_y'] = array(
    'short_label' => 'Y-Offset',
    'label' => 'Box Shadow y-offset',
    'field-class' => 'icon-size-3',
    'default_unit' => 'px',
    'input-class' => 'size-0b',
    'hide imp' => 1,
    'icon' => '35',
    // ref
    'ref_desc' => "<p>The position of the vertical shadow. Negative values are allowed.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '-10' would create a 10 pixel shadow above the element (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '-2%'."),
    'w3s' => ''
);
$propertyOptions['shadow']['box_shadow_blur'] = array(
    'short_label' => 'Blur',
    'label' => 'Box Shadow Blur',
    'default_unit' => 'px',
    'input-class' => 'size-0b',
    'field-class' => 'last',
    'icon' => '37',
    // ref
    'ref_desc' => "<p>The blur distance. If you defined a black shadow and a blur of 10px, their would be a 10 pixel blur area where the shadow faded evenly from black to transparent at every extremity.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '10' would create a 10 pixel blur at the edge of the element's shadow (Microthemer automatically adds the 'px' unit if a unit isn't specified. Other commonly used units include 'em' and '%'. So you could enter '5%'.)"),
    'w3s' => ''
);
// background
$propertyOptions['background']['background_color'] = array(
    'short_label' => 'Color',
    'label' => 'Background Color',
    'input-class' => 'color',
    'icon' => '25',
    // ref
    'ref_desc' => "<p>The background-color property sets the background color of an element.</p>",
    'ref_values' => array(
        "(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Color text field to reveal the color picker."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_background-color.asp'
);
$propertyOptions['background']['background_image'] = array(
    'short_label' => 'Image',
    'label' => 'Background Image',
    'type' => 'combobox',
    'field-class' => 'last span-3',
    'input-class' => 'bg-image-select size-very-big strictly-dropdown',
    'select_options' => array(
		'',
        'none'
    ),
    'icon' => '39, 14',
    // ref
    'ref_desc' => "<p>The background-image property sets the background image for an element.</p>",
    'ref_values' => array(
        "(image)" => "Microthemer lists all the images contained within micro themes in a dropdown menu. You can also click the 'view images' link at the top right of the images menu to browse the images visually. Tip: the image slidehow will start from the image selected in the menu and will iterate through in the same order."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_background-image.asp'
);
$propertyOptions['background']['background_position'] = array(
    'short_label' => 'Position',
    'label' => 'Background Position',
    'type' => 'combobox',
    'input-class' => 'bg-position-select size-6',
    'select_options' => array(
		'',
        'left top',
        'left center',
        'left bottom',
        'right top',
        'right center',
        'right bottom',
        'center top',
        'center center',
        'center bottom'
    ),
    'icon' => '3, 14',
    // ref
    'ref_desc' => "<p>The background-position property sets the starting position of a background image.</p>
<p>You can enter two values separated by a space. The first value will determine the horizontal (x-axis) position of the background image. The second value wil determine the vertical (y-axis) position of the background image. Microthemer will default to 'px' if no unit is specified, you can also use '%' or 'em' though.</p>",
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
    'w3s' => 'http://www.w3schools.com/cssref/pr_background-position.asp'
);
$propertyOptions['background']['background_repeat'] = array(
    'short_label' => 'Repeat',
    'label' => 'Background Repeat',
    'input-class' => 'size-3a',
    'type' => 'combobox',
    'select_options' => array(
		'',
        'repeat',
        'repeat-x',
        'repeat-y',
        'no-repeat'
    ), 'icon' => '1, 14',
    // ref
    'ref_desc' => "<p>The background-repeat property sets if/how a background image will be repeated.</p>",
    'ref_values' => array(
        "repeat" => "The background image will be repeated both vertically and horizontally. This is default",
        "repeat-x" => "The background image will be repeated only horizontally",
        "repeat-y" => "The background image will be repeated only vertically",
        "no-repeat" => "The background-image will not be repeated"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_background-repeat.asp'
);
$propertyOptions['background']['background_attachment'] = array(
    'short_label' => 'Attachment',
    'label' => 'Background Attachment',
    'type' => 'combobox',
    'select_options' => array(
		'',
        'scroll',
        'fixed'
    ), 'icon' => '2, 14',
    // ref
    'ref_desc' => "<p>The background-attachment property sets whether a background image is
fixed or scrolls with the rest of the page.</p>",
    'ref_values' => array(
        "scroll" => "The background image scrolls with the rest of the page. This is default",
        "fixed" => "The background image is fixed"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_background-attachment.asp'
);
$propertyOptions['background']['background_size'] = array(
    'short_label' => 'Size',
    'label' => 'Background Size',
    'type' => 'combobox',
    'input-class' => 'size-3a',
    'select_options' => array(
		'',
        'auto',
        'cover',
        'contain'
    ),
    'field-class' => 'last',
    'icon' => '25, 14',
    // ref
);
$propertyOptions['background']['background_clip'] = array(
    'short_label' => 'Clip',
    'label' => 'Background Clip',
    'type' => 'combobox',
    'input-class' => 'size-5',
    'select_options' => array(
		'',
        'border-box',
        'padding-box',
        'content-box'
    ),
    'icon' => '26, 14',
    // ref
);
/* - it is hard to understand the difference between clip and origin, include if requested.
$propertyOptions['background']['background_origin'] = array(
    'short_label' => 'Origin',
    'label' => 'Background Origin',
    'type' => 'combobox',
    'select_options' => array(
		'',
        'border-box',
        'padding-box',
        'content-box'
    ));
*/
// dimensions
$propertyOptions['dimensions']['width'] = array(
    'short_label' => 'Width',
    'label' => 'Width',
    'input-class' => 'size-1',
    'default_unit' => 'px',
    'icon' => '9',
    // ref
    'ref_desc' => "<p>The width property sets the width of an element. If 'box-sizing' is set to 'content-box' (default) the total width of an element is
	width + padding + borders (and in terms of the space it takes up on the page + margin too). However, if the width hasn't been given a numeric or percentage value (or has been explicitly set to 'auto') it will have a value of 'auto'. Applying padding, margin and border values when width is 'auto' causes the browser to decrease the value it calculates for width. Otherwise the element would be too big for it's parent element - which is what happens if you enter a value of '100%' for width and then add margins, padding or borders.</p>
	<p>If 'box-sizing' is set to 'border-box' padding and border values are not added to the defined width. The defined width specifies the total width. So if you set width to '100' and border-left to '20' the total width would be '100' as opposed to '120' (the padding forces the width down to '80').</p>).",
    'ref_values' => array(
        "(numeric)" => "e.g. '400' would set the width to 400 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '50%' in the Width field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_dim_width.asp'
);
$propertyOptions['dimensions']['min_width'] = array(
    'short_label' => 'Min',
    'label' => 'Min Width',
    'input-class' => 'size-1',
    'default_unit' => 'px',
    'icon' => '36, 14',
    // ref
    'ref_desc' => "<p>The min-width property sets the minimum width of an element. Note: The min-width property does not include padding, borders, or margins.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '400' would set the minimun width to 400 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '50%' in the Min Width field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_dim_min-width.asp'
);
$propertyOptions['dimensions']['max_width'] = array(
    'short_label' => 'Max',
    'label' => 'Max Width',
    'input-class' => 'size-1',
    'default_unit' => 'px',
    'icon' => '34, 14',
    // ref
    'ref_desc' => "<p>The max-width property sets the maximum width of an element. Note: The max-width property does not include padding, borders, or margins.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '400' would set the maximun width to 400 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '50%' in the Max Width field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_dim_max-width.asp'
);
$propertyOptions['dimensions']['height'] = array(
    'short_label' => 'Height',
    'label' => 'Height',
    'field-class' => 'icon-size-0b',
    'sub_label' => 'Height:',
    'input-class' => 'size-1',
    'default_unit' => 'px',
    'icon' => '10',
    // ref
    'ref_desc' => "<p>The height property sets the height of an element. <b>Note:</b> The total height of an element is
	height + padding + borders + margins.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '200' would set the height to 400 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '10em' in the Height field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_dim_height.asp'
);
$propertyOptions['dimensions']['min_height'] = array(
    'short_label' => 'Min',
    'label' => 'Min Height',
    'field-class' => 'icon-size-2',
    'input-class' => 'size-1',
    'default_unit' => 'px',
    'icon' => '37, 14',
    // ref
    'ref_desc' => "<p>The min-height property sets the minimum height of an element. Note: The min-height property does not include padding, borders, or margins</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '400' would set the minimun height to 400 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified)."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_dim_min-width.asp'
);
$propertyOptions['dimensions']['max_height'] = array(
    'short_label' => 'Max',
    'label' => 'Max Height',
    'field-class' => 'icon-size-2',
    'input-class' => 'size-1',
    'default_unit' => 'px',
    'icon' => '35, 14',
    // ref
    'ref_desc' => "<p>The max-height property sets the maximum height of an element. Note: The max-height property does not include padding, borders, or margins.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '400' would set the maximun height to 400 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified)."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_dim_max-width.asp'
);
$propertyOptions['dimensions']['box_sizing'] = array(
    'short_label' => 'Box Sizing',
    'label' => 'Box Sizing',
    'input-class' => 'size-5',
    'type' => 'combobox',
    'select_options' => array(
		'',
        'content-box',
        'border-box'
    ),
    'field-class' => 'last',
    'icon' => '32, 14',
    // ref
);

// padding & margin (new group)
$propertyOptions['padding_margin']['padding_top'] = array(
    'short_label' => 'Top',
    'label' => 'Padding Top',
    'input-class' => 'size-0',
    'default_unit' => 'px',
    'rel' => 'paddding',
    'icon' => '3',
    // ref
    'ref_desc' => "<p>The padding-top property sets the top padding (space) of an element. The space is created <i>inside</i> the element's border.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '15' would set the top padding for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Top Padding field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_padding-top.asp'
);
$propertyOptions['padding_margin']['padding_right'] = array(
    'short_label' => 'Right',
    'label' => 'Padding Right',
    'input-class' => 'size-0',
    'default_unit' => 'px',
    'rel' => 'paddding',
    'icon' => '2',
    // ref
    'ref_desc' => "<p>The padding-right property sets the right padding (space) of an element. The space is created <i>inside</i> the element's border.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '15' would set the right padding for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Right Padding field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_padding-right.asp'
);
$propertyOptions['padding_margin']['padding_bottom'] = array(
    'short_label' => 'Bottom',
    'label' => 'Padding Bottom',
    'input-class' => 'size-0',
    'default_unit' => 'px',
    'rel' => 'paddding',
    'icon' => '4',
    // ref
    'ref_desc' => "<p>The padding-bottom property sets the bottom padding (space) of an element. The space is created <i>inside</i> the element's border.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '15' would set the bottom padding for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Bottom Padding field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_padding-bottom.asp'
);
$propertyOptions['padding_margin']['padding_left'] = array(
    'short_label' => 'Left',
    'label' => 'Padding Left',
    'input-class' => 'size-0',
    'default_unit' => 'px',
    'rel' => 'paddding',
    'field-class' => 'last',
    'icon' => '1',
    // ref
    'ref_desc' => "<p>The padding-left property sets the left padding (space) of an element. The space is created <i>inside</i> the element's border.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '15' would set the left padding for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Left Padding field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_padding-left.asp'
);
$propertyOptions['padding_margin']['margin_top'] = array(
    'short_label' => 'Top',
    'label' => 'Margin Top',
    'sub_label' => 'Margin:',
    'input-class' => 'size-0',
    'default_unit' => 'px',
    'rel' => 'margin',
    'icon' => '7',
    // ref
    'ref_desc' => "<p>The margin-top property sets the top margin (space) of an element.
The space is created <i>outside</i> the element's border.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '15' would set the top margin for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Top Margin field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_margin-top.asp'
);
$propertyOptions['padding_margin']['margin_right'] = array(
    'short_label' => 'Right',
    'label' => 'Margin Right',
    'input-class' => 'size-0',
    'default_unit' => 'px',
    'rel' => 'margin',
    'icon' => '6',
    // ref
    'ref_desc' => "<p>The margin-right property sets the right margin (space) of an element. The space is created <i>outside</i> the element's border.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '15' would set the right margin for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Right Margin field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_margin-right.asp'
);
$propertyOptions['padding_margin']['margin_bottom'] = array(
    'short_label' => 'Bottom',
    'label' => 'Margin Bottom',
    'input-class' => 'size-0',
    'default_unit' => 'px',
    'rel' => 'margin',
    'icon' => '8',
    // ref
    'ref_desc' => "<p>The margin-bottom property sets the bottom margin (space) of an element. The space is created <i>outside</i> the element's border.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '15' would set the bottom margin for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Bottom Margin field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_margin-bottom.asp'
);
$propertyOptions['padding_margin']['margin_left'] = array(
    'short_label' => 'Left',
    'label' => 'Margin Left',
    'input-class' => 'size-0',
    'default_unit' => 'px',
    'rel' => 'margin',
    'field-class' => 'last',
    'icon' => '5',
    // ref
    'ref_desc' => "<p>The margin-left property sets the left margin (space) of an element. The space is created <i>outside</i> the element's border.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '15' would set the left margin for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Left Margin field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_margin-left.asp'
);
// border
$propertyOptions['border']['border_top_color'] = array(
    'short_label' => 'Top',
    'label' => 'Border Top Color',
    'default_unit' => 'px',
    'rel' => 'border_color',
    'input-class' => 'color',
    'icon' => '10, 14',
    // ref
    'ref_desc' => "<p>The border-top-color property sets the top border color of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.</p>",
    'ref_values' => array(
        "(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Top Border Color text field to reveal the color picker."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_border-top_color.asp'
);
$propertyOptions['border']['border_right_color'] = array(
    'short_label' => 'Right',
    'label' => 'Border Right Color',
    'default_unit' => 'px',
    'rel' => 'border_color',
    'input-class' => 'color',
    'icon' => '13, 14',
    // ref
    'ref_desc' => "<p>The border-right-color property sets the right border color of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.</p>",
    'ref_values' => array(
        "(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Right Border Color text field to reveal the color picker."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_border-right_color.asp'
);
$propertyOptions['border']['border_bottom_color'] = array(
    'short_label' => 'Bottom',
    'label' => 'Border Bottom Color',
    'default_unit' => 'px',
    'rel' => 'border_color',
    'input-class' => 'color',
    'icon' => '11, 14',
    // ref
    'ref_desc' => "<p>The border-bottom-color property sets the bottom border color of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.</p>",
    'ref_values' => array(
        "(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Bottom Border Color text field to reveal the color picker."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_border-bottom_color.asp'
);
$propertyOptions['border']['border_left_color'] = array(
    'short_label' => 'Left',
    'label' => 'Border Left Color',
    'default_unit' => 'px',
    'rel' => 'border_color',
    'input-class' => 'color',
    'field-class' => 'last',
    'icon' => '12, 14',
    // ref
    'ref_desc' => "<p>The border-left-color property sets the left border color of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.</p>",
    'ref_values' => array(
        "(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the Left Border Color text field to reveal the color picker."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_border-left_color.asp'
);
$propertyOptions['border']['border_top_width'] = array(
    'short_label' => 'Top',
    'label' => 'Border Top Width',
    'sub_label' => 'Border Width:',
    'field-class' => 'icon-size-2',
    'default_unit' => 'px',
    'rel' => 'border_width',
    'icon' => '6, 14',
    // ref
    'ref_desc' => "<p>The border-top-width property sets the top border width of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '15' would set the top border width for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Top Border Width field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_border-top_width.asp'
);
$propertyOptions['border']['border_right_width'] = array(
    'short_label' => 'Right',
    'label' => 'Border Right Width',
    'default_unit' => 'px',
    'rel' => 'border_width',
    'icon' => '9, 14',
    // ref
    'ref_desc' => "<p>The border-right-width property sets the right border width of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '15' would set the right border for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Right Border Width field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_border-right_width.asp'
);
$propertyOptions['border']['border_bottom_width'] = array(
    'short_label' => 'Bottom',
    'label' => 'Border Bottom Width',
    'default_unit' => 'px',
    'rel' => 'border_width',
    'icon' => '7, 14',
    // ref
    'ref_desc' => "<p>The border-bottom-width property sets the bottom border width of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '15' would set the bottom border for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Bottom Border Width field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_border-bottom_width.asp'
);
$propertyOptions['border']['border_left_width'] = array(
    'short_label' => 'Left',
    'label' => 'Border Left Width',
    'default_unit' => 'px',
    'rel' => 'border_width',
    'field-class' => 'last',
    'icon' => '8, 14',
    'linebreak' => 1,
    // ref
    'ref_desc' => "<p>The border-left-width property sets the left border width of an element. <b>Note</b>: the Border Style property must be set for any of the other border properties to work.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '15' would set the left border for an element to 15 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%' in the Left Border Width field."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_border-left_width.asp'
);
$border_style_options = array(
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
);
$propertyOptions['border']['border_top_style'] = array(
    'short_label' => 'Top',
    'label' => 'Border Top Style',
    'type' => 'combobox',
    'rel' => 'border_style',
    'select_options' => $border_style_options,
    'icon' => '14, 14',
    // ref
    'ref_desc' => "<p>The border-top-style property sets the style of an element's top border.</p>",
    'ref_values' => array(
        "hidden" => "The same as 'none', except in border conflict resolution for table elements",
        "dotted" => "Specifies a dotted border",
        "dashed" => "Specifies a dashed border",
        "solid" => "Specifies a solid border",
        "double" => "Specifies a double border",
        "groove" => "Specifies a 3D grooved border. The effect depends on the border-color value",
        "ridge" => "Specifies a 3D ridged border. The effect depends on the border-color value!",
        "inset" => "Specifies a 3D inset border. The effect depends on the border-color value",
        "outset" => "Specifies a 3D outset border. The effect depends on the border-color value",
        "none" => "Specifies no border"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_border-top_style.asp'
);
$propertyOptions['border']['border_right_style'] = array(
    'short_label' => 'Right',
    'label' => 'Border Right Style',
    'type' => 'combobox',
    'rel' => 'border_style',
    'select_options' => $border_style_options,
    'icon' => '17, 14',
    // ref
    'ref_desc' => "<p>The border-right-style property sets the style of an element's right border.</p>",
    'ref_values' => array(
        "hidden" => "The same as 'none', except in border conflict resolution for table elements",
        "dotted" => "Specifies a dotted border",
        "dashed" => "Specifies a dashed border",
        "solid" => "Specifies a solid border",
        "double" => "Specifies a double border",
        "groove" => "Specifies a 3D grooved border. The effect depends on the border-color value",
        "ridge" => "Specifies a 3D ridged border. The effect depends on the border-color value!",
        "inset" => "Specifies a 3D inset border. The effect depends on the border-color value",
        "outset" => "Specifies a 3D outset border. The effect depends on the border-color value",
        "none" => "Specifies no border"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_border-right_style.asp'
);
$propertyOptions['border']['border_bottom_style'] = array(
    'short_label' => 'Bottom',
    'label' => 'Border Bottom Style',
    'type' => 'combobox',
    'rel' => 'border_style',
    'select_options' => $border_style_options,
    'icon' => '15, 14',
    // ref
    'ref_desc' => "<p>The border-bottom-style property sets the style of an element's bottom border.</p>",
    'ref_values' => array(
        "hidden" => "The same as 'none', except in border conflict resolution for table elements",
        "dotted" => "Specifies a dotted border",
        "dashed" => "Specifies a dashed border",
        "solid" => "Specifies a solid border",
        "double" => "Specifies a double border",
        "groove" => "Specifies a 3D grooved border. The effect depends on the border-color value",
        "ridge" => "Specifies a 3D ridged border. The effect depends on the border-color value!",
        "inset" => "Specifies a 3D inset border. The effect depends on the border-color value",
        "outset" => "Specifies a 3D outset border. The effect depends on the border-color value",
        "none" => "Specifies no border"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_border-bottom_style.asp'
);
$propertyOptions['border']['border_left_style'] = array(
    'short_label' => 'Left',
    'label' => 'Border Left Style',
    'type' => 'combobox',
    'field-class' => 'last',
    'rel' => 'border_style',
    'select_options' => $border_style_options,
    'icon' => '16, 14',
    // ref
    'ref_desc' => "<p>The border-left-style property sets the style of an element's left border.</p>",
    'ref_values' => array(
        "hidden" => "The same as 'none', except in border conflict resolution for table elements",
        "dotted" => "Specifies a dotted border",
        "dashed" => "Specifies a dashed border",
        "solid" => "Specifies a solid border",
        "double" => "Specifies a double border",
        "groove" => "Specifies a 3D grooved border. The effect depends on the border-color value",
        "ridge" => "Specifies a 3D ridged border. The effect depends on the border-color value!",
        "inset" => "Specifies a 3D inset border. The effect depends on the border-color value",
        "outset" => "Specifies a 3D outset border. The effect depends on the border-color value",
        "none" => "Specifies no border"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_border-left_style.asp'

);
// border radius
$propertyOptions['border']['border_top_left_radius'] = array(
    'short_label' => 'Top Left',
    'label' => 'Top Left Border Radius',
    'sub_label' => 'Border Radius:',
    'field-class' => 'icon-size-2',
    'default_unit' => 'px',
    'icon' => '17',
    'rel' => 'border_radius',
    'hide imp' => 1,
    // ref
    'ref_desc' => "<p>The top left radius property defines the shape of the border of the top-left corner. A higher value creates a more rounded curve.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '5' would set the top left border radius for an element to 5 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%'."),
    'w3s' => 'http://www.w3schools.com/cssref/css3_pr_border-top-left-radius.asp'
);
$propertyOptions['border']['border_top_right_radius'] = array(
    'short_label' => 'Top Right',
    'label' => 'Top Right Border Radius',
    'default_unit' => 'px',
    'icon' => '18',
    'rel' => 'border_radius',
    'hide imp' => 1,
    //ref
    'ref_desc' => "<p>The top right radius property defines the shape of the border of the top-right corner. A higher value creates a more rounded curve.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '5' would set the top right border radius for an element to 5 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%'."),
    'w3s' => 'http://www.w3schools.com/cssref/css3_pr_border-top-right-radius.asp'
);
$propertyOptions['border']['border_bottom_right_radius'] = array(
    'short_label' => 'Bottom Right',
    'label' => 'Bottom Right Border Radius',
    'default_unit' => 'px',
    'icon' => '20',
    'rel' => 'border_radius',
    'hide imp' => 1,
    // ref
    'ref_desc' => "<p>The bottom left radius property defines the shape of the border of the bottom-left corner. A higher value creates a more rounded curve.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '5' would set the bottom left border radius for an element to 5 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%'."),
    'w3s' => 'http://www.w3schools.com/cssref/css3_pr_border-bottom-left-radius.asp'
);
$propertyOptions['border']['border_bottom_left_radius'] = array(
    'short_label' => 'Bottom Left',
    'label' => 'Bottom Left Border Radius',
    'default_unit' => 'px',
    'icon' => '19',
    'rel' => 'border_radius',
    'field-class' => 'last',
    // ref
    'ref_desc' => "<p>The bottom right radius property defines the shape of the border of the bottom-right corner. A higher value creates a more rounded curve.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '5' would set the bottom right border radius for an element to 5 pixels (Microthemer automatically adds the 'px' unit if a unit isn't specified). Other commonly used units include 'em' and '%'. So you could enter '5%'."),
    'w3s' => 'http://www.w3schools.com/cssref/css3_pr_border-bottom-right-radius.asp'
);
// behaviour
$propertyOptions['behaviour']['display'] = array(
    'short_label' => 'Display',
    'label' => 'Display',
    'input-class' => 'size-4',
    'type' => 'combobox',
    'select_options' => array(
		'',
        'block',
        'inline',
        'inline-block',
        'none'
    ),
    'icon' => '23, 14',
    // ref
    'ref_desc' => "<p>The display property specifies the type of box an element should generate.</p>",
    'ref_values' => array(
        "block" => "The element will generate a block box (a line break before and after the element)",
        "inline" => "The element will generate an inline box (no line break before or after the element)",
        "inline-block" => "The element will generate a block box, laid out as an inline box",
        "none" => "The element doesn't appear on the page at all"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_class_display.asp'
);
$propertyOptions['behaviour']['overflow'] = array(
    'short_label' => 'Overflow',
    'label' => 'Overflow',
    'field-class' => 'icon-size-2',
    'input-class' => 'size-2',
    'type' => 'combobox',
    'select_options' => array(
		'',
        'visible',
        'scroll',
        'auto',
        'hidden'
    ),
    'icon' => '20, 14',
    // ref
    'ref_desc' => "<p>The overflow property specifies what happens if content overflows an element's box.</p>",
    'ref_values' => array(
        "visible" => "The overflow is not clipped. It renders outside the element's box. This is default",
        "scroll" => "The overflow is clipped, but a scroll-bar is added to see the rest of the content",
        "auto" => "If overflow is clipped, a scroll-bar should be added to see the rest of the content",
        "hidden" => "The overflow is clipped, and the rest of the content will be invisible"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_pos_overflow.asp'
);
$propertyOptions['behaviour']['overflow_x'] = array(
    'short_label' => 'Overflow X',
    'label' => 'Overflow X',
    'field-class' => 'icon-size-2',
    'input-class' => 'size-2',
    'type' => 'combobox',
    'select_options' => array(
		'',
        'visible',
        'scroll',
        'auto',
        'hidden'
    ),
    'icon' => '29, 14',
    // ref
);
$propertyOptions['behaviour']['overflow_y'] = array(
    'short_label' => 'Overflow Y',
    'label' => 'Overflow Y',
    'field-class' => 'icon-size-4',
    'input-class' => 'size-2',
    'type' => 'combobox',
    'select_options' => array(
		'',
        'visible',
        'scroll',
        'auto',
        'hidden'
    ),
    'icon' => '30, 14',
    // ref
);
$propertyOptions['behaviour']['visibility'] = array(
    'short_label' => 'Visibility',
    'label' => 'Visibility',
    'type' => 'combobox',
    'input-class' => 'size-3',
    'select_options' => array(
		'',
        'visible',
        'hidden',
        'collapse'
    ),
    'icon' => '24, 14',
    // ref
    'ref_desc' => "<p>The visibility property specifies whether or not an element is visible. But unlike setting 'display' to 'none', if you set 'visibility' to 'hidden' the hidden element will still take up the same space on the page - it just won't be visible.</p>",
    'ref_values' => array(
        "visible" => "The element is visible. This is default",
        "hidden" => "The element is invisible (but still takes up space)",
        "collapse" => "Only for table elements. collapse removes a row or column, but it does not affect the table layout. The space taken up by the row or column will be available for other content. If collapse is used on other elements, it renders as 'hidden'"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_class_visibility.asp'
);
$propertyOptions['behaviour']['cursor'] = array(
    'short_label' => 'Cursor',
    'label' => 'Cursor',
    'field-class' => 'icon-size-0a',
    'input-class' => 'size-3',
    'type' => 'combobox',
    'select_options' => array(
		'',
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
    ),
    'icon' => '22, 14',
    // ref
    'ref_desc' => "<p>The cursor property specifies the type of cursor to be displayed when pointing on an element.</p>",
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
    'w3s' => 'http://www.w3schools.com/cssref/pr_class_visibility.asp'
);
$propertyOptions['behaviour']['opacity'] = array(
    'short_label' => 'Opacity',
    'label' => 'Opacity',
    'field-class' => 'icon-size-2',
    'input-class' => 'size-0c',
    'icon' => '4, 14',
    // ref
    'ref_desc' => "<p>The opacity property sets the opacity level for an element. You can enter any numeric value between 0 and 1 (e.g. 0.25 or 0.9)</p>",
    'ref_values' => array(
        "(decimal 0 - 1)" => "e.g. '0.5' would set the opacity to 50% (half transparent)."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_border-top_width.asp'
);
// position
$propertyOptions['position']['position'] = array(
    'short_label' => 'Position',
    'label' => 'Position',
    'input-class' => 'size-3',
    'type' => 'combobox',
    'select_options' => array(
		'',
        'absolute',
        'relative',
        'fixed',
        'static'
    ),
    'icon' => '40, 14',
    // ref
    'ref_desc' => "<p>The position property is used to position an element.</p>",
    'ref_values' => array(
        "absolute" => "Generates an absolutely positioned element, positioned relative to the first parent element that has a position other than static. The element's position is specified with the 'left', 'top', 'right', and 'bottom' properties",
        "relative" => "	Generates a relatively positioned element, positioned relative to its normal position. The element's position is specified with the 'left', 'top', 'right', and 'bottom' properties",
        "fixed" => "Generates an absolutely positioned element, positioned relative to the browser window. The element's position is specified with the 'left', 'top', 'right', and 'bottom' properties",
        "static" => "Default. No position, the element occurs in the normal flow (ignores any top, bottom, left, right, or z-index declarations)"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_class_position.asp'
);
$propertyOptions['position']['top'] = array(
    'short_label' => 'Top',
    'label' => 'Top (Position)',
    'default_unit' => 'px',
    'icon' => '11',
    // ref
    'ref_desc' => "<p>For absolutely positioned elements, the top property sets the top edge of an element to a unit above/below the top edge of its containing element. For relatively positioned elements, the top property sets the top edge of an element to a unit above/below its normal position. Negative values are allowed.</p>
	<p><b>Note</b>: the way an element moves on screen when you apply a positive 'top' value may seem counterintuitive. It moves down on the screen when given a positive value because the browser increases the distance between the top of the element and some reference point. If in doubt, just look at the direction of the property icon. The icon depicts the direction the element will move on the page as you increase the value for 'top'.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '75' would move an element 75 pixels below the top edge of its parent element (if the element is absolutely positioned), or 75px below it's normal position (if the element is relatively positioned)"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_pos_top.asp'
);
$propertyOptions['position']['bottom'] = array(
    'short_label' => 'Bottom',
    'label' => 'Bottom (Position)',
    'default_unit' => 'px',
    'icon' => '12',
    // ref
    'ref_desc' => "<p>For absolutely positioned elements, the bottom property sets the bottom edge of an element to a unit above/below the bottom edge of its containing element. For relatively positioned elements, the bottom property sets the bottom edge of an element to a unit above/below its normal position. Negative values are allowed.</p>
	<p><b>Note</b>: the way an element moves on screen when you apply a positive 'bottom' value may seem counterintuitive. It moves up on the screen when given a positive value because the browser increases the distance between the bottom of the element and some reference point. If in doubt, just look at the direction of the property icon. The icon depicts the direction the element will move on the page as you increase the value for 'bottom'.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '75' would move an element 75 pixels below the bottom edge of its parent element (if the element is absolutely positioned), or 75px below it's normal position (if the element is relatively positioned)"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_pos_bottom.asp'

);
$propertyOptions['position']['left'] = array(
    'short_label' => 'Left',
    'label' => 'Left (Position)',
    'default_unit' => 'px',
    'icon' => '13',
    // ref
    'ref_desc' => "<p>For absolutely positioned elements, the left property sets the left edge of an element to a unit to the
	left or right of the left edge of its containing element. For relatively positioned elements, the left property sets the left edge of an element to a unit to the left or right of its normal position. Negative values are allowed.</p>
	<p><b>Note</b>: the way an element moves on screen when you apply a positive 'left' value may seem counterintuitive. It moves right on the screen when given a positive value because the browser increases the distance between the left of the element and some reference point. If in doubt, just look at the direction of the icon. The icon depicts the direction the element will move on the page as you increase the value for 'left'.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '20' would move an element 20 pixels to the right of the left edge of its parent element (if the element is absolutely positioned), or 20px to the right of it's normal position (if the element is relatively positioned)"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_pos_left.asp'

);
$propertyOptions['position']['right'] = array(
    'short_label' => 'Right',
    'label' => 'Right (Position)',
    'default_unit' => 'px',
    'icon' => '14',
    'field-class' => 'last',
    // ref
    'ref_desc' => "<p>For absolutely positioned elements, the right property sets the right edge of an element to a unit to the left or right of the right edge of its containing element. For relatively positioned elements, the right property sets the right edge of an element to a unit to the left or right of its normal position. Negative values are allowed.</p>
	<p><b>Note</b>: the way an element moves on screen when you apply a positive 'right' value may seem counterintuitive. It moves left on the screen when given a positive value because the browser increases the distance between the right of the element and some reference point. If in doubt, just look at the direction of the icon. The icon depicts the direction the element will move on the page as you increase the value for 'right'.</p>",
    'ref_values' => array(
        "(numeric)" => "e.g. '20' would move an element 20 pixels to the left of the right edge of its parent element (if the element is absolutely positioned), or 20px to the left of it's normal position (if the element is relatively positioned)"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_pos_right.asp'
);
$propertyOptions['position']['z_index'] = array(
    'short_label' => 'Z-index',
    'label' => 'Z-index',
    'icon' => '21, 14',
    // ref
    'ref_desc' => "<p>The z-index property specifies the stack order of an element. An element with greater stack order is always in front of an element with a lower stack order. Note: z-index only works on positioned elements (position:absolute, position:relative, or position:fixed).</p>",
    'ref_values' => array(
        "(numeric)" => "If you had 2 absolutely positioned elements that overlapped and you gave element A a z-index value of 5 and element B a z-index value of 10, element B would show in front of element A."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_pos_z-index.asp'
);
$propertyOptions['position']['float'] = array(
    'short_label' => 'Float',
    'label' => 'Float',
    'field-class' => 'icon-size-2',
    'input-class' => 'size-0',
    'type' => 'combobox',
    'select_options' => array(
		'',
        'left',
        'right',
        'none'
    ), 'icon' => '18, 14',
    // ref
    'ref_desc' => "<p>The float property specifies whether or not an element should float. An element can either float left or right.</p>",
    'ref_values' => array(
        "left" => "The element floats to the left",
        "right" => "The element floats to the right",
        "none" => "The element is not floated. This is the default."),
    'w3s' => 'http://www.w3schools.com/cssref/pr_class_float.asp'
);
$propertyOptions['position']['clear'] = array(
    'short_label' => 'Clear',
    'label' => 'Clear',
    'field-class' => 'icon-size-2',
    'input-class' => 'size-0',
    'type' => 'combobox',
    'select_options' => array(
		'',
        'left',
        'right',
        'both',
        'none'
    ), 'icon' => '19, 14',
    // ref
    'ref_desc' => "<p>The clear property specifies which sides of an element where other floating elements are not allowed.</p>",
    'ref_values' => array(
        "left" => "No floating elements allowed on the left side",
        "right" => "No floating elements allowed on the right side",
        "both" => "No floating elements allowed on the left or the right side",
        "none" => "Default. Allows floating elements on both sides"),
    'w3s' => 'http://www.w3schools.com/cssref/pr_class_clear.asp'
);
// gradient
$propertyOptions['gradient']['gradient_angle'] = array(
    'short_label' => 'Angle',
    'label' => 'Gradient Angle',
    'field-class' => 'last',
    'input-class' => 'size-big',
    'type' => 'combobox',
    "select_options" => array(
        "top to bottom",
        "bottom to top",
        "left to right",
        "right to left",
        "top left to bottom right",
        "bottom right to top left",
        "top right to bottom left",
        "bottom left to top right"
    ),
    'hide imp' => 1,
    'icon' => '28, 1',
    // ref
    'ref_desc' => "<p>The angle of the gradient. You have 8 options which cover all possible horizontal, vertical and perfectly diagonal variations. Note: old versions of Safari and Chrome (< Chrome 10 and < Safari 5.1) don't properly support diagonal gradients.<p>",
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
    'w3s' => ''
);
$propertyOptions['gradient']['gradient_a'] = array(
    'short_label' => 'A',
    'label' => 'Gradient A',
    'field-class' => 'always-label',
    'input-class' => 'color',
    'hide imp' => 1,
    'icon' => '25',
    // ref
    'ref_desc' => "<p>One of the color stops in the linear gradient. The color you specify here will gradually blend into the color you specify for Gradient B if you specify one. If you don't specify a color for Gradient B, Gradient A will blend into Gradient C.</p>",
    'ref_values' => array(
        "(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the 'Gradient A' text field to reveal the color picker."),
    'w3s' => ''
);
$propertyOptions['gradient']['gradient_b'] = array(
    'short_label' => 'B',
    'label' => 'Gradient B',
    'field-class' => 'always-label',
    'input-class' => 'color',
    'hide imp' => 1,
    'icon' => '25',
    // ref
    'ref_desc' => "<p>One of the color stops in the linear gradient. The color you specify here will gradually blend into Gradient A and C. <b>Gradient B is optional</b>. If you just want to blend 2 colors, you only need to specify a value for Grandient A and C. If you do use it, there is an additional option for Gradient B: 'B Position (optional)'. Click the label for this option for details.</p>",
    'ref_values' => array(
        "(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the 'Gradient B' text field to reveal the color picker."),
    'w3s' => ''
);
$propertyOptions['gradient']['gradient_b_pos'] = array(
    'short_label' => 'B Position',
    'label' => 'Gradient B Position',
    'field-class' => 'icon-size-0b',
    'default_unit' => 'px',
    'hide imp' => 1,
    'icon' => '40, 14',
    // ref
    'ref_desc' => "<p>The position of Gradient B in relation to Gradient A. Gradient B is the middle color of the gradient, but it doesn't have to appear exactly in between Gradient A and C. If you were to specify a 'B Position' of 10% (for instance) Gradient B would begin almost immediately after Gradient A. If you were to specify a 'B Position' of 90%, Gradient B wouldn't begin until just before Gradient C. If you leave this setting blank, or specify a value of 50% Gradient B, will be placed exactly between Gradient A and C.</p>",
    'ref_values' => array(
        "(numeric)" => "For consistency, the default unit is pixels. So if you enter 25 it will default to 25px. But it's more common to specify gradient positions in percentages. So you might put 15% (specifying the unit so it doesn't default to pixels)"),
    'w3s' => ''
);
$propertyOptions['gradient']['gradient_c'] = array(
    'short_label' => 'C',
    'label' => 'Gradient C',
    'field-class' => 'always-label',
    'input-class' => 'color',
    'icon' => '25',
    // ref
    'ref_desc' => "<p>One of the color stops in the linear gradient. The color you specify here will gradually blend into the color you specify for Gradient B if you specify one. If you don't specify a color for Gradient B, Gradient C will blend into Gradient A.</p>",
    'ref_values' => array(
        "(hex code)" => "Microthemer provides a color picker for specifying color without having to remember hex codes. Just click your mouse in the 'Gradient C' text field to reveal the color picker."),
    'w3s' => ''
);

$extraOptionsReference['CSS3_PIE']['CSS3_PIE'] = array(
    'short_label' => 'CSS3 PIE',
    'label' => 'CSS3 PIE (Progressive Internet Explorer)',
    'field-class' => '',
    'input-class' => '',
    'icon' => '26, 5',
    // ref
    'ref_desc' => "<p>Microthemer comes pre-integrated with CSS3 PIE which can be enabled by on a global or per-selector basis. CSS3 PIE makes Internet Explorer 6-9 render CSS3 properties like gradients, border-radius and box-shadow correctly. We recommend that you visit
            the <a href='http://css3pie.com/' target='_blank'>CSS3 PIE site</a> to learn more about it.</p>
            <p>One of the main drawbacks with PIE is that it is very frequently necessary to give elements a \"position\" value of \"relative\" when also assigning CSS3 properties to them. To make this less tedious, Microthemer automatically applies \"position:relative\" when you set CSS3 properties (if you have enabled CSS3 PIE).</p>
            <p>You can still use PIE and turn off the automatic \"position:relative\" by explictly setting the \"position\" value to something else (e.g. \"static\", \"absolute\" or \"fixed\"). However, sometimes it's better to just not use CSS3 PIE and allow corners to be square or backgrounds to be solid colors in old Internet Explorer versions that don't support CSS3 properties.</p>
            <p><b>Some Known PIE Shortcomings</b></p>

            <ul>
             <li>PIE does not currently work when applied to the \"body\" Selector</li>
             <li>Element types that cannot accept children (e.g. \"input\" and \"img\") will fail or throw errors if you apply styles that use relative length units such as em or ex. Stick to using px units for these elements (Microthemer automatically applies \"px\" to numerical values if no unit is set - apart from line-height as it is a valid (and useful) not to include a unit).</li>
             <li>There is another work around that avoids the \"position:relative\" fix mentioned above. You can make the ancestor element \"position:relative\" and give it a z-index. An ancester element is an element that contains another element. With WordPress, the \"post\" element will be the \"ancester\" of any content inside the post such as text, meta information, and images.</li>
           </ul>
           <br />
           <p><b>Donate to PIE:</b> PIE is Free for everyone. Please consider <a href='http://css3pie.com/' target='_blank'>donating to the PIE project</a> if it has helped you.</p>",
    'ref_values' => '',
    'w3s' => ''
);


// log moved groups/properties
$legacy_groups['font'] = array(
    'forecolor' => array(
        'color' => 1 // 1 means same as key
    ),
    'text' => array(
        'line_height' => 1,
        'text_decoration' => 1
    )
);
$legacy_groups['list'] = array(
    'text' => array(
        'list_style' => 'list_style_type' // define string when different
    )
);
$legacy_groups['shadow'] = array(
    'text' => array(
        'text_shadow_color' => 1,
        'text_shadow_x' => 1,
        'text_shadow_y' => 1,
        'text_shadow_blur' => 1
    ),
    'CSS3' => array(
        'box_shadow_color' => 1,
        'box_shadow_x' => 1,
        'box_shadow_y' => 1,
        'box_shadow_blur' => 1
    )
);
$legacy_groups['background'] = array(
    'background' => array(
        'background_position_x' => 1, // this may need to be an exception
        'background_position_y' => 1
    )
);
$legacy_groups['padding_margin'] = array(
    'padding' =>array(
        'padding_top' => 1,
        'padding_right' => 1,
        'padding_bottom' => 1,
        'padding_left' => 1
     ),
    'margin' => array(
        'margin_top' => 1,
        'margin_right' => 1,
        'margin_bottom' => 1,
        'margin_left' => 1
    )
);
$legacy_groups['border'] = array(
    'border' => array(
        'border_style' => array( // the only array - when the legacy value maps onto multiple props
            'border_top_style',
            'border_right_style',
            'border_bottom_style',
            'border_left_style'
        )
    ),
    'CSS3' => array(
        'radius_top_left' => 'border_top_left_radius',
        'radius_top_right' => 'border_top_right_radius',
        'radius_bottom_right' => 'border_bottom_right_radius',
        'radius_bottom_left' => 'border_bottom_left_radius'
    )
    /* was only moved temp - version not released
    'border' => array(
        'radius_top_left',
        'radius_top_right',
        'radius_bottom_right',
        'radius_bottom_left'
    )*/
);
$legacy_groups['position'] = array(
    'behaviour' => array(
        'float' => 1,
        'clear' => 1
    )
);
$legacy_groups['gradient'] = array(
    'CSS3' => array(
        'gradient_a' => 1,
        'gradient_b' => 1,
        'gradient_b_pos' => 1,
        'gradient_c' => 1,
        'gradient_angle' => 1
    )
);




