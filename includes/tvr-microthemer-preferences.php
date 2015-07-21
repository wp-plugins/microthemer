<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}

// is edge mode active?
if ($this->edge_mode['available'] and !empty($this->preferences['edge_mode'])){
    $this->edge_mode['active'] = true;
}

?>

<!-- Edit Preferences -->
<form id="tvr-preferences" name='preferences_form' method="post" autocomplete="off"
      action="admin.php?page=<?php echo $page_context;?>" >
    <?php wp_nonce_field('tvr_preferences_submit'); ?>
	<?php echo $this->start_dialog('display-preferences', wp_kses(__('Edit your Microthemer preferences', 'tvr-microthemer'), array()), 'small-dialog',
		array(
			wp_kses(__('General', 'tvr-microthemer'), array()),
			wp_kses(__('CSS Units', 'tvr-microthemer'), array())
		)
	); ?>

<div class="content-main dialog-tab-fields">

    <?php
    $tab_count = -1; // start at -1 so ++ can be used for all tabs
    ?>

	<!-- Tab 1 (General Preferences) -->
	<div class="dialog-tab-field dialog-tab-field-<?php echo ++$tab_count; ?> hidden show">
		<ul class="form-field-list">
			<?php
			if (!$this->edge_mode['available']){
			   ?>
				<li class="edge-mode-unavailable"><?php printf(wp_kses(__('Nothing to try in Edge mode.', 'tvr-microthemer'), array())); ?>
					<a target="_blank" href="<?php echo $this->edge_mode['edge_forum_url']; ?>"><?php printf(wp_kses(__('Learn about the last pilot', 'tvr-microthemer'), array())); ?></a>.</li>
			   <?php
			}
			// yes no options
			$yes_no = array(
				'edge_mode' => array(
					'label' => wp_kses(__('Enable edge mode. ', 'tvr-microthemer'), array()) . ' <a title="' .
						__('Read a full explanation of the changes and give us constructive feedback on this forum thread.', 'tvr-microthemer') .
						'" href="'.$this->edge_mode['edge_forum_url'].'" target="_blank">' . wp_kses(__('Read about/comment here', 'tvr-microthemer'), array()) . '</a>',
					'explain' => $this->edge_mode['cta'],
				),
				'first_and_last' => array(
					'label' => wp_kses(__('Add "first" and "last" classes to menu items', 'tvr-microthemer'), array()),
					'explain' => wp_kses(__('Microthemer can insert "first" and "last" classes on WordPress menus so that you can style the first or last menu items a bit differently from the rest. Note: this only works with "Custom Menus" created on the  Appearance > Menus page.', 'tvr-microthemer'), array())
				),

				'gzip' => array(
					'label' => wp_kses(__('Gzip the Microthemer UI page for faster loading', 'tvr-microthemer'), array()),
					'explain' =>wp_kses(__('The Microthemer interface generates quite a bit of HTML. Having this gzip option enabled will speed up the initial page loading.', 'tvr-microthemer'), array())
				),
				'ie_notice' => array(
					'label' => wp_kses(__('Display "Chrome is faster" notice', 'tvr-microthemer'), array()),
					'explain' => wp_kses(__('Microthemer advises using Chrome if you are using an alternative because the interface runs noticeably faster in Chrome. But you can turn this off if you want.' , 'tvr-microthemer'), array())
				),
				'safe_mode_notice' => array(
					'label' => 'Display PHP safe mode warnings',
					'explain' => wp_kses(__('If your server has the PHP settings "safe mode" on, you may encounter permission errors when Microthemer tries to create directories. Microthemer will alert you to safe-mode being on. But if that\'s old news to you, you may want to disable the warnings about it.', 'tvr-microthemer'), array())
				),
				'css_important' => array(
					'label' => wp_kses(__('Always add !important to CSS styles', 'tvr-microthemer'), array()),
					'label_no' => '(configure manually)',
					'explain' => wp_kses(__('Always add the "!important" CSS declaration to CSS styles.This largely solves the issue of having to understand how CSS specificity works. But if you prefer, you can disable this functionality and still apply "!important" on a per style basis by clicking the faint "i\'s" that will appear to the right of every style input.', 'tvr-microthemer'), array())
				),
				'pie_by_default' => array(
					'label' => wp_kses(__('Always use CSS3 PIE pollyfill where relevant', 'tvr-microthemer'), array()),
					'label_no' => wp_kses(__('(configure manually)', 'tvr-microthemer'), array()),
					'explain' => wp_kses(__('Always include to the CSS3 PIE htc file if gradients, rounded corners, or box-shadow have been applied to make them work in Internet Explorer 6-8. Note: a "position" value of "relative" will automatically be applied to the selector unless you explicitly set the position property to "static" or some other value. Set this option to "No" if you would like to enable this pollyfill on a per selector basis.', 'tvr-microthemer'), array())
				),
				'admin_bar_shortcut' => array(
					'label' => wp_kses(__('Add a Microthemer shortcut to the WP admin bar', 'tvr-microthemer'), array()),
					'explain' => wp_kses(__('Include a link to the Microthemer interface from the WordPress admin toolbar at the top of every page.', 'tvr-microthemer'), array())
				),
				'top_level_shortcut' => array(
					'label' => wp_kses(__('Include admin bar shortcut as a top level link', 'tvr-microthemer'), array()),
					'explain' => wp_kses(__('If you are enabling the Microthemer shortcut in the admin bar, you can either have it as a top level menu link or as a sub-menu item of the main menu.', 'tvr-microthemer'), array())
				),
                /*'admin_bar_main_ui' => array(
                    'label' => wp_kses(__('Display WP admin bar on Microthemer page', 'tvr-microthemer'), array()),
                    'explain' => wp_kses(__('Display the WordPress admin bar at the top of the main Microthemer interface page'), array())
                ),*/
                'admin_bar_preview' => array(
                    'label' => wp_kses(__('Display WP admin bar on site preview', 'tvr-microthemer'), array()),
                    'explain' => wp_kses(__('Display the WordPress admin bar at the top of every page in the site preview', 'tvr-microthemer'), array())
                ),
				/*'boxsizing_by_default' => array(
					'label' => __('Always use the box-sizing pollyfill where relevant', 'tvr-microthemer'),
					'label_no' => __('(configure manually)', 'tvr-microthemer'),
					'explain' => __('Always include to the box-sizing htc file if the box-sizing property has been applied to make it work in Internet Explorer 6 and 7. Set this option to "No" if you would like to enable this pollyfill on a per selector basis. Note: this pollyfill doesn\'t work on text inputs.', 'tvr-microthemer')
				)*/
			);
			// text options
			$text_input = array(
				'preview_url' => array(
					'label' => wp_kses(__('Frontend preview URL Microthemer should load', 'tvr-microthemer'), array()),
					'explain' => wp_kses(__('Manually specify a link to the page you would like Microthemer to load for editing when it first starts. By default Microthemer will load your home page or the last page you visited. This option is useful if you want to style a page that can\'t be navigated to from the home page or other pages.', 'tvr-microthemer'), array())
				),
				'gfont_subset' => array(
					'label' => wp_kses(__('Google Font subset URL parameter', 'tvr-microthemer'), array()),
					'explain' => wp_kses(__('You can instruct Google Fonts to include a font subset by entering an URL parameter here. For example "&subset=latin,latin-ext" (without the quotes). Note: Microthemer only generates a Google Font URL if it detects that you have applied Google Fonts in your design.', 'tvr-microthemer'), array())),
				'tooltip_delay' => array(
					'label' => wp_kses(__('Tooltip delay time (in milliseconds)', 'tvr-microthemer'), array()),
					'explain' => wp_kses(__('Control how long it takes for a Microthemer tooltip to display. Set to "0" for instant, "native" to use the browser default tooltip on hover, or some value like "2000" for a 2 second delay (so it never shows when you don\'t need it to). The default is 500 milliseconds.', 'tvr-microthemer'), array())
				)

			);

			// display form fields
			$this->output_radio_input_lis($yes_no);
			$this->output_text_combo_lis($text_input);

			?>
		</ul>
	</div>

	<!-- Tab 2 (CSS Units)  -->
	<div class="dialog-tab-field dialog-tab-field-<?php echo ++$tab_count; ?> hidden">

		<ul class="form-field-list css_units">

            <li><span class="reveal-hidden-form-opts link reveal-unit-sets" rel="css-unit-set-opts">
                    <?php printf(wp_kses(__('Load a full set of suggested CSS units', 'tvr-microthemer'), array())); ?></span></li>
            <?php
            $css_unit_sets = array(
                'load_css_unit_sets' => array(
                    'input_id' => 'css_unit_set',
                    'combobox' => 'css_unit_sets',
                    'label' => __('Select a set of default units', 'tvr-microthemer'),
                    'explain' => __('Pixels are easier to work with. Consequently, pixels are the default CSS unit Microthemer applies. But many consider it best practice to use em (or rem) units in most instances. You can quickly load a full set of suggested CSS units here.', 'tvr-microthemer')
                )
            );

            $this->output_text_combo_lis($css_unit_sets, 'hidden css-unit-set-opts');

            // output CSS unit options
            foreach($this->preferences['my_props'] as $prop_group => $array){

                // loop through default unit props
                if (!empty($this->preferences['my_props'][$prop_group]['pg_props'])){
                    $first = true;
                    foreach ($this->preferences['my_props'][$prop_group]['pg_props'] as $prop => $arr){
                        unset($units);
                        // user doesn't need to set all padding (for instance) individually
                        $box_model_rel = false;
                        $first_in_group = false;
                        $label = $arr['label'];
                        if (!empty($this->propertyoptions[$prop_group][$prop]['rel'])){
                            $box_model_rel = $this->propertyoptions[$prop_group][$prop]['rel'];
                        }
                        if (!empty($this->propertyoptions[$prop_group][$prop]['sub_label'])){
                            $first_in_group = $this->propertyoptions[$prop_group][$prop]['sub_label'];
                        }
                        // only output unit eligable props
                        if ( empty($arr['default_unit']) or ($box_model_rel and !$first_in_group)  ){
                            continue;
                        }
                        // use group sub label if first box model e.g. padding, margin, border width, border radius
                        if ($box_model_rel and $first_in_group){
                            $label = $first_in_group . ' (all)';
                        }
                        // we don't need position repeated all the time
                        $label = str_replace(' (Position)', '', $label);

                        // output pg group heading if new group
                        if ($first){
                            echo
                            '<li class="section_title">' .
                            $this->preferences['my_props'][$prop_group]['pg_label']
                            . '</li>';
                            $first = false;
                        }
                        // output form fields
                        $units['cssu_'.$prop] = array(
                            'input_class' => 'custom_css_unit',
                            'arrow_class' => 'custom_css_unit',
                            'combobox' => 'css_units',
                            'input_name' => 'tvr_preferences[new_css_units]['.$prop_group.']['.$prop.']',
                            'input_value' => $this->preferences['my_props'][$prop_group]['pg_props'][$prop]['default_unit'],
                            'label' => $label,
                            'explain' => 'Set the default CSS unit for ' . $arr['label']
                        );
                        $this->output_text_combo_lis($units);
                    }
                }
            }
			?>
		</ul>

	</div>

    <?php echo $this->dialog_button(__('Save Preferences', 'tvr-microthemer'), 'input', 'save-preferences'); ?>

    <div class="explain">
		<div class="heading link explain-link"><?php _e("About this feature", "tvr-microthemer"); ?></div>

        <div class="full-about">
			<p><?php _e("Set your global Microthemer preferences on this page. Hover your mouse over the option labels for further information on each option.", "tvr-microthemer"); ?></p>
            <?php
            if (!$this->preferences['buyer_validated']){
                ?>
				<p><?php printf(
					wp_kses(__('Please unlock the full program by entering your PayPal email address on <span %s>this page</span>.',
						'tvr-microthemer'), array( 'span' => array() )),
					'class="link show-dialog" rel="unlock-microthemer"'
				); ?></p>
            <?php
            }
            ?>
			<p><?php printf(
				wp_kses(__('You may also wan to customise the default media queries Microthemer suggests on <span %s>this page</span>.',
					'tvr-microthemer'), array( 'span' => array() )),
				'class="link show-dialog" rel="edit-media-queries"'
			); ?></p>
        </div>
    </div>
</div>

<?php echo $this->end_dialog(wp_kses(__('Save Preferences', 'tvr-microthemer'), array()), 'input', 'save-preferences'); ?>
</form>