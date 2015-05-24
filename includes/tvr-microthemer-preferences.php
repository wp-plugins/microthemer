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
<?php echo $this->start_dialog('display-preferences', wp_kses(__('Edit your Microthemer preferences', 'tvr-microthemer'), array()), 'small-dialog'); ?>

<div class="content-main">
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
                'label' => wp_kses(__('Display Internet Explorer notice', 'tvr-microthemer'), array()),
                'explain' => wp_kses(__('Microthemer can be a bit clunky when run in Internet Explorer. And it just plain doesn\'t work in old versions of IE like IE8. But if you insist on using IE you might want to disable the notice telling you to use Firefox or Chrome instead.', 'tvr-microthemer'), array())
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
            )
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
        foreach ($yes_no as $key => $array) {
            if (empty($array['label_no'])){
                $array['label_no'] = '';
            }
            if ($key == 'edge_mode' and !$this->edge_mode['available']){
                continue;
            }
            ?>
            <li class="fake-radio-parent">
                <label title="<?php echo htmlentities($array['explain']); ?>"><?php echo $array['label']; ?>:
                    </label>


                            <span class="yes-wrap p-option-wrap">
                            <input type='radio' autocomplete="off" class='radio'
                                   name='tvr_preferences[<?php echo $key; ?>]' value='1'
                                <?php
                                if ($this->preferences[$key] == 1) {
                                    echo 'checked="checked"';
                                    $on = 'on';
                                } else {
                                    $on = '';
                                }
                                ?>
                                />
                                <span class="fake-radio <?php echo $on; ?>"></span>
								<span class="ef-label"><?php printf(wp_kses(__('Yes', 'tvr-microthemer'), array())); ?></span>
                            </span>
                            <span class="no-wrap p-wrap-wrap">
                                <input type='radio' autocomplete="off" class='radio' name='tvr_preferences[<?php echo $key; ?>]' value='0'
                                    <?php
                                    if ($this->preferences[$key] == 0) {
                                        echo 'checked="checked"';
                                        $on = 'on';
                                    } else {
                                        $on = '';
                                    }
                                    ?>
                                    />
                                    <span class="fake-radio <?php echo $on; ?>"></span>
									<span class="ef-label"><?php printf(wp_kses(__('No %s', 'tvr-microthemer'), array()), $array['label_no']); ?></span>
                            </span>
            </li>
        <?php
        }
        foreach ($text_input as $key => $array) {
            $input_attr = '';
            if (!empty($this->preferences[$key])){
                $input_attr = $this->preferences[$key];
            }
            ?>
            <li>
                <label title="<?php echo htmlentities($array['explain']); ?>" class="text-label">
                    <?php echo $array['label']; ?>:</label>
                <input type='text' autocomplete="off" name='tvr_preferences[<?php echo $key; ?>]'
                       value='<?php echo esc_attr($input_attr); ?>' />

            </li>
        <?php
        }
        ?>
    </ul>
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
</form>
<?php echo $this->end_dialog(wp_kses(__('Save Preferences', 'tvr-microthemer'), array()), 'input', 'save-preferences'); ?>
   
