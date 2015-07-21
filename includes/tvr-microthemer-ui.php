<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}

// is edge mode active?
if ($this->edge_mode['available'] and !empty($this->preferences['edge_mode'])){
    $this->edge_mode['active'] = true;
}

// output dynamic JS here as interim solution
echo '<script type="text/javascript">';
include $this->thisplugindir . '/includes/js-dynamic.php';
echo '</script>';

//css helper for option icons (dev tool)
$prev_key = '';
$refresh_css = false;
if ($refresh_css){
    foreach ($this->propertyoptions as $key => $arr){
        foreach ($arr as $prop => $value) {
            // pos
            $icon_pos = explode(',', $value['icon']);
            $x = $icon_pos[0];
            if ($icon_pos[1]) {
                $y = $icon_pos[1];
            } else {
                $y = 11;
            }
            // new group
            if ($prev_key != $key) {
                $group = "// $key
";
            } else {
                $group = "";
            }
            echo "{$group}.option-icon-{$prop} { @include state-sprite({$x}, {$y}); }
";
            $prev_key = $key;
        }
    }
}

// are we hiding the admin bar?
$this->preferences['admin_bar_preview'] ? $ui_class = 'show-admin-bar' : $ui_class = 'do-not-show-admin-bar';
?>

<div id="tvr" class='wrap tvr-wrap <?php echo $ui_class; ?>'>
	<div id='tvr-ui'>

        <span id="ui-nonce"><?php echo wp_create_nonce('tvr_microthemer_ui_load_styles'); ?></span>
        <span id="fonts-api" rel="<?php echo $this->thispluginurl.'includes/fonts-api.php'; ?>"></span>
        <span id="ui-url" rel="<?php echo 'admin.php?page=' . $this->microthemeruipage; ?>"></span>
        <span id="admin-url" rel="<?php echo $this->wp_admin_url; ?>"></span>

        <span id="ajaxUrl" rel="<?php echo $this->site_url .'/wp-admin/admin.php?page='.$this->microthemeruipage.'&_wpnonce='.wp_create_nonce('mcth_simple_ajax') ?>"></span>
        <span id="resetUrl" rel="<?php echo '&_wpnonce='.wp_create_nonce('tvr_microthemer_ui_reset');?>&action=tvr_ui_reset"></span>
        <span id="clearUrl" rel="<?php echo '&_wpnonce='.wp_create_nonce('tvr_microthemer_clear_styles');?>&action=tvr_clear_styles"></span>


        <span id='site-url' rel="<?php echo $this->site_url; ?>"></span>
        <span id="active-styles-url" rel="<?php echo $this->micro_root_url . 'active-styles.css' ?>"></span>
        <span id="revisions-url" rel="<?php echo 'admin.php?page=' . $this->microthemeruipage .
            '&_wpnonce='.wp_create_nonce('tvr_get_revisions'). '&action=get_revisions'; ?>"></span>

        <span id='all_devices_default_width' rel='<?php echo $this->preferences['all_devices_default_width']; ?>'></span>

        <span id='last-pg-focus' rel='<?php echo $this->preferences['pg_focus'] ?>'></span>
        <span id='plugin-url' rel='<?php echo $this->thispluginurl; ?>'></span>
        <span id='tooltip_delay' rel='<?php echo $this->preferences['tooltip_delay']; ?>'></span>
        <?php
        // edge mode settings
        if ($this->edge_mode['active']){
            ?>
            <span id='edge-mode' rel='1'></span>
            <?php
            if (is_array($this->edge_mode['config'])){
                foreach ($this->edge_mode['config'] as $key => $value){
                    echo '<span id="'.$key.'" rel="'.$value.'"></span>';
                }
            }
        }
        ?>
        <span id='plugin-trial' rel='<?php echo $this->preferences['buyer_validated']; ?>'></span>
        <form method="post" name="tvr_microthemer_ui_serialised" id="tvr_microthemer_ui_serialised" autocomplete="off">
            <?php wp_nonce_field('tvr_microthemer_ui_serialised');?>
            <input type="hidden" name="action" value="tvr_microthemer_ui_serialised" />
            <textarea id="tvr-serialised-data" name="tvr_serialized_data">hello</textarea>
        </form>
        <?php

        // classes that affect display of things in the ui
        $main_class = '';

        // set interface classes
        $this->preferences['buyer_validated'] ? $main_class.= ' plugin-unlocked' : false;
        $this->preferences['show_interface'] ? $main_class.= ' show-interface' : false;
        ($this->preferences['css_important'] != 1) ? $main_class.= ' manual-css-important' : false;
        $this->preferences['show_code_editor'] ? $main_class.= ' show-code_editor' : false;
        $this->preferences['show_rulers'] ? $main_class.= ' show-rulers' : false;

        // edge mode interface classes
        if ($this->edge_mode['active']){
            if (is_array($this->edge_mode['config'])){
                foreach ($this->edge_mode['config'] as $key => $value){
                    $main_class.= ' '.$key.'-'.$value;
                }
            }
        }

		// log ie notice
		$this->ie_notice();

        /*** Build Visual View ***/
        if (empty($this->preferences['last_viewed_selector'])){
            $last_viewed_selector = '';
        } else {
            $last_viewed_selector = $this->preferences['last_viewed_selector'];
        }
        ?>
        <form method="post" name="tvr_microthemer_ui_save" id="tvr_microthemer_ui_save" autocomplete="off">
        <?php wp_nonce_field('tvr_microthemer_ui_save');?>
        <input type="hidden" name="action" value="tvr_microthemer_ui_save" />
        <input id="last-edited-selector" type="hidden" name="tvr_mcth[non_section][meta][last_edited_selector]"
               value="<?php
               if (!empty($this->options['non_section']['meta']['last_edited_selector'])){
                   echo $this->options['non_section']['meta']['last_edited_selector'];
               }
               ?>" />
        <input id="last-viewed-selector" type="hidden" name="tvr_mcth[non_section][meta][last_viewed_selector]"
               value="<?php echo $last_viewed_selector; ?>" />
        <div id="visual-view" class="<?php echo $main_class; ?>">

            <div id="v-top-controls">
                <div id='hand-css-area'>
                    <div id="custom-code-toolbar">
                        <div class="heading">Enter your own custom CSS code</div>
                    </div>

                    <div id="code-editors-wrap">
                        <div id="css-tab-areas" class="query-tabs css-code-tabs">
                        <span class="edit-code-tabs show-dialog"
                              title="<?php echo __('Edit custom code tabs', 'tvr-microthemer'); ?>" rel="edit-code-tabs">
                        </span>

                            <?php
                            // save the configuration of the css tab
                            if ( empty($this->options['non_section']['css_focus'])) {
                                $css_focus = 'all-browsers';
                            } else {
                                $css_focus = $this->options['non_section']['css_focus'];
                            }
                            $tab_headings = array(
                                'all-browsers' => wp_kses(__('All Browsers', 'tvr-microthemer'), array()),
                                'all' => wp_kses(__('All versions of IE', 'tvr-microthemer'), array()),
                                'nine' => wp_kses(__('IE9 and below', 'tvr-microthemer'), array()),
                                'eight' => wp_kses(__('IE8 and below', 'tvr-microthemer'), array()),
                                'seven' => wp_kses(__('IE7 and below', 'tvr-microthemer'), array())
                            );
                            foreach ($tab_headings as $key => $value) {
                                if ($key == $css_focus){
                                    $active_c = 'active';
                                } else {
                                    $active_c = '';
                                }
                                echo '<span class="css-tab mt-tab css-tab-'.$key.' show '.$active_c.'" rel="'.$key.'">'.$tab_headings[$key].'</span>';
                            }
                            ?>
                            <input class="css-focus" type="hidden"
                                   name="tvr_mcth[non_section][css_focus]"
                                   value="<?php echo $css_focus; ?>" />
                        </div>
                        <?php
                        foreach ($tab_headings as $key => $value) {
                            if ($key == 'all-browsers'){
                                $css_code = htmlentities(stripslashes($this->options['non_section']['hand_coded_css']));
                                $name = 'tvr_mcth[non_section][hand_coded_css]';
                            } else {
                                $css_code = '';
                                if (!empty($this->options['non_section']['ie_css'][$key])){
                                    $css_code = htmlentities(stripslashes($this->options['non_section']['ie_css'][$key]));
                                }
                                $name = 'tvr_mcth[non_section][ie_css]['.$key.']';
                            }

                            if ($key == $css_focus){
                                $show_c = 'show';
                            } else {
                                $show_c = '';
                            }
                            ?>
                            <div class="css-code-wrap css-code-wrap-<?php echo $key; ?> hidden <?php echo $show_c; ?>">
                                <textarea id='css-<?php echo $key; ?>' class="hand-css-textarea"
                                          name="<?php echo $name; ?>"
                                          autocomplete="off"><?php
                                    //=esc
                                    if (!empty($css_code)) {
                                        echo $css_code;
                                    } ?></textarea>
                                    <pre id="custom-css-<?php echo $key; ?>" rel="<?php echo $key; ?>"
                                         class="custom-css-pre"><?php echo $css_code; ?></pre>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <!-- code editor with syntax highlighting - TODO load this on code editor switch -->
                    <script src="<?php echo $this->thispluginurl; ?>js/ace/ace.js" type="text/javascript" charset="utf-8"></script>
                    <script>
                        jQuery(document).ready(function($){
                            //var TvrEditors = {};
                            <?php
                            foreach ($tab_headings as $key => $value) {
                                ?>
                            TvrEditors['<?php echo $key; ?>'] = ace.edit("custom-css-<?php echo $key; ?>");
                            TvrEditors['<?php echo $key; ?>'].setTheme("ace/theme/chrome");
                            TvrEditors['<?php echo $key; ?>'].getSession().setMode("ace/mode/css");
                            TvrEditors['<?php echo $key; ?>'].setShowPrintMargin(false);
                            TvrEditors['<?php echo $key; ?>'].getSession().on('change', function(e) {
                                $('#custom-css-<?php echo $key; ?>').addClass('changed');
                            });
                            <?php
                        }
                        ?>
                        });
                    </script>
                        <span id="editors-list" rel="<?php
                        $first = true;
                        foreach ($tab_headings as $key => $value) {
                            if ($first){
                                echo $key;
                                $first = false;
                            } else {
                                echo '|'.$key;
                            }
                        }
                        ?>"></span>
                </div>

                <div id="responsive-bar">
                    <?php echo $this->global_media_query_tabs(); ?>
                </div>

                <div id="tvr-nav" class="tvr-nav">
                    <div id="quick-nav" class="quick-nav">
						<span id="vb-focus-prev" class="scroll-buttons tvr-icon" title="<?php _e("Go To Previous Selector", "tvr-microthemer"); ?>"></span>
						<span id="vb-focus-next" class="scroll-buttons tvr-icon" title="<?php _e("Go To Next Selector", "tvr-microthemer"); ?>"></span>
                        <?php
                        // for quick debugging
                        echo $this->show_me;
                        ?>
                    </div>
                    <div id="tvr-main-menu" class="tvr-main-menu-wrap">
						<span class="main-menu-tip-trigger" title="<?php _e("Manage folders & selectors", "tvr-microthemer"); ?>"></span>
                        <div id="main-menu-popdown" class="main-menu-popdown">
                            <div id="add-new-section">
                                <div class="inner-wrap">
                                    <div class='new-section'>
										<input type='text' data-ph-title="<?php _e('Enter a new folder name', 'tvr-microthemer'); ?>" class='new-section-input' name='new_section[name]' value='' />
										<span class='new-section-add tvr-button' title="<?php _e("Create a new folder", "tvr-microthemer"); ?>"><?php printf(wp_kses(__('Add Folder', 'tvr-microthemer'), array())); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="scrollable-area">
                                <ul id='tvr-menu'>
                                    <?php
                                    foreach ( $this->options as $section_name => $array) {
                                        // if non_section continue
                                        if ($section_name == 'non_section') {
                                            continue;
                                        }
                                        // section menu item (trigger function for menu selectors too)
                                        $this->menu_section_html($section_name, $array);
                                        ++$this->total_sections;
                                    }
                                    ?>
                                </ul>
                            </div>
                            <!-- keep track of total sections & selectors -->
                            <div id="ui-totals-count">

                                <span id="section-count-state" class='section-count-state' rel='<?php echo $this->total_selectors; ?>'></span>
                                <span class="tvr-icon folder-icon"></span>
                                <span id="total-sec-count"><?php echo $this->total_sections; ?></span>
								<span class="total-folders"><?php printf(wp_kses(__('Folders', 'tvr-microthemer'), array())); ?>&nbsp;&nbsp;</span>

                                <span class="tvr-icon selector-icon"></span>
                                <span id="total-sel-count"><?php echo $this->total_selectors; ?></span>
								<span><?php printf(wp_kses(__('Selectors', 'tvr-microthemer'), array())); ?></span>

                            </div>
                        </div>
                    </div>
                    <span id="current-selector"></span>
                    <div id="starter-message"><?php printf(wp_kses(__("Double-click anything on the page to begin restyling it.", "tvr-microthemer"), array())); ?></div>
                </div>

                <?php
                if (!$this->preferences['buyer_validated']){
                    ?>
                    <div class="cta-wrap">
                        <a class="cta-button buy-cta tvr-button red-button" href="http://themeover.com" target="_blank"
                           title="<?php _e('Purchase a license to use the full program', 'tvr-microthemer'); ?>">
                            <span class="tvr-icon"></span>
                            <span class="cta-label"><?php printf(wp_kses(__('Buy', 'tvr-microthemer'), array())); ?></span>
                        </a>
                            <span class="cta-button unlock-cta tvr-button show-dialog"
                                  title="<?php _e("If you have purchased Microthemer you can enter your email address to unlock the full program. If you have not yet purchased Microthemer, you cannot unlock the full version.", "tvr-microthemer"); ?>" rel="unlock-microthemer">
                                <span class="tvr-icon show-dialog" rel="unlock-microthemer"></span>
                                <span class="cta-label show-dialog" rel="unlock-microthemer"><?php printf(wp_kses(__("Unlock", "tvr-microthemer"), array())); ?></span>
                            </span>
                    </div>
                <?php
                }
                ?>

                <div class="right-stuff-wrap">

                    <div id="status-board" class="tvr-popdown-wrap">

                        <!--<span class="tvr-icon info-icon"></span>-->
                        <div id="status-short"></div>

                        <div id="full-logs" class="tvr-popdown scrollable-area">
							<div class="heading"><?php printf(wp_kses(__('Microthemer Notifications', 'tvr-microthemer'), array())); ?></div>
                            <?php

                            echo $this->display_log();
                            ?>
                            <div id="script-feedback"></div>
                        </div>
                    </div>

                </div>


                <ul id='tvr-options'>
                    <?php
                    foreach ( $this->initial_options_html as $key => $html) {
                        echo $html;
                    }
                    ?>

                </ul>


                <div class="frame-shadow"></div>
            </div>

            <div id="v-frontend-wrap">
                <div id="rHighlight-wrap" class="ruler-stuff">
                    <div id="min-neg" class="ruler-stuff"></div>
                    <div id="max-neg" class="ruler-stuff"></div>
                </div>
                <div id="rHighlight" class="ruler-stuff"></div>

                <div id="v-frontend">
                    <?php
                    // resolve iframe url
                    $site_url = $this->site_url;
                    $strpos = strpos($this->preferences['preview_url'], $site_url);
                    if ( !empty($this->preferences['preview_url']) and ($strpos === 0)) {
                        $iframe_url = esc_attr($this->preferences['preview_url']);
                    } else {
                        $iframe_url = $this->site_url;
                    }
                    // maybe use src="includes/holding.html" with an animated GIF saying "Loading Website Frontend"
                    ?>
                    <iframe id="viframe" frameborder="0" name="viframe"
                            rel="<?php echo $iframe_url; ?>" src="<?php echo $this->thispluginurl; ?>includes/place-holder2.html"></iframe>
                    <div id="iframe-dragger"></div>

                </div>

                <div id="v-mq-controls" class="ruler-stuff">
                    <span id="iframe-pixel-width"></span>
                    <span id="iframe-max-width"></span>
                    <div id="v-mq-slider" class="tvr-slider"></div>
                    <span id="iframe-min-width"></span>


                </div>

                <?php
                // do we show the mob devices preview?
                !$this->preferences['show_rulers'] ? $device_preview_class = 'hidden' : $device_preview_class = '';
                ?>
                <div id="common-devices-preview" class="tvr-popright-wrap <?php echo $device_preview_class; ?>">
                    <div class="tvr-popright">
                        <div id="current-screen-width"></div>
                        <div class="scrollable-area">
                            <ul class="mob-preview-list">
                                <?php
                                foreach ($this->mob_preview as $i => $array){
                                    echo '
                                    <li id="mt-screen-preview-'.$i.'"
                                    class="mt-screen-preview" rel="'.$i.'">
                                    <span class="mt-screen-preview mob-wxh">'.$array[1].' x '.$array[2].'</span>
                                    <span class="mt-screen-preview mob-model">'.$array[0].'</span>
                                    </li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="height-screen" class="hidden"></div>
            </div>

            <div id="advanced-wizard">

                <div class="heading dialog-header">
                    <span class="dialog-icon"></span>
                    <span class="text">Create a Selector <!--<a href="#" target="_blank">(learn how)</a>--></span>
                        <span class="cancel-wizard cancel tvr-icon close-icon"
                              title="<?php _e("Close the selector wizard", "tvr-microthemer"); ?>"></span>
                </div>

                <div id="selector-wizard">
                    <div class="quick-create">

                        <div class="quick-wrap wiz-name">
                            <label title="<?php _e("Give your selector a memorable name that describes the element or set of elements on the page", "tvr-microthemer"); ?>"><?php printf(wp_kses(__('Name', 'tvr-microthemer'), array())); ?>:</label>
                                <span class="input-wrap wizard-name-wrap" >
                                    <input id='wizard-name' type='text' class='wizard-name wizard-input' name='wizard_name' value='' />
                                </span>
                        </div>

                        <div class="quick-wrap wiz-folder">
                            <label data-ph-title="<?php _e("Organise your selector into a folder", "tvr-microthemer"); ?>"><?php printf(wp_kses(__('Folder', 'tvr-microthemer'), array())); ?>:</label>
                                <span class="input-wrap wizard-folder-wrap" >
                                   <input type="text" class="combobox wizard-folder wizard-input"
                                          id="wizard_folder" name="wizard_folder" rel="cur_folders" value=""
                                          data-ph-title="<?php _e("Enter new or select a folder...", "tvr-microthemer"); ?>" />
                                    <span class="combo-arrow"></span>
                                </span>
                        </div>

                    </div>
                </div>

                <div id="adv-tabs" class="query-tabs adv-tabs">
                    <?php
                    // save the configuration of the css tab
                    if (empty($this->preferences['adv_wizard_tab'])){
                        $adv_wizard_focus = 'refine-targeting';
                    } else {
                        $adv_wizard_focus = $this->preferences['adv_wizard_tab'];
                    }
                    $tab_headings = array(
                        'refine-targeting' => wp_kses(__('Targeting', 'tvr-microthemer'), array()),
                        'html-inspector' => wp_kses(__('Inspector', 'tvr-microthemer'), array())
                    );
                    foreach ($tab_headings as $key => $value) {
                        if ($key == $adv_wizard_focus){
                            $active_c = 'active';
                        } else {
                            $active_c = '';
                        }
                        echo '<span class="adv-tab mt-tab adv-tab-'.$key.' show '.$active_c.'" rel="'.$key.'">'.$tab_headings[$key].'</span>';
                    }
                    // this is redundant (preferences store focus) but kept for consistency with other tab remembering
                    ?>
                    <input class="adv-wizard-focus" type="hidden"
                           name="tvr_mcth[non_section][adv_wizard_focus]"
                           value="<?php echo $adv_wizard_focus; ?>" />
                </div>

                <div class="wizard-panes">
                    <div class="adv-area-refine-targeting adv-area hidden scrollable-area <?php
                    if ($adv_wizard_focus == 'refine-targeting') {
                        echo 'show';
                    }
                    ?>">
                        <div class="scrollable-refined">
                            <div class="refine-inner-wrap">

                                <ul id="code-suggestions"></ul>
                            </div>
                        </div>
                    </div>

                    <div class="adv-area-html-inspector adv-area hidden <?php
                    if ($adv_wizard_focus == 'html-inspector') {
                        echo 'show';
                    }?>">

                        <div id="refine-target">
                            <div class="refine-target-controls">
                                <span class="tvr-prev-sibling refine-button" title="<?php _e("Move to Previous Sibling Element", "tvr-microthemer"); ?>"></span>
                                <div class="updown-wrap">
                                    <span class="tvr-parent refine-button" title="<?php _e("Move to Parent Element", "tvr-microthemer"); ?>"></span>
                                    <span class="tvr-child refine-button" title="<?php _e("Move to Child Element", "tvr-microthemer"); ?>"></span>
                                </div>
                                <span class="tvr-next-sibling refine-button" title="<?php _e("Move to Next Sibling Element", "tvr-microthemer"); ?>"></span>
                            </div>
                            <div class="refine-target-html">
                                <span class="display-html"></span>
                            </div>
                        </div>

                        <div id="html-computed-css">
                            <div class="scrollable-area">

                                <?php
                                $i = 1;
                                foreach ($this->property_option_groups as $property_group => $pg_label) {
                                    ?>
                                    <ul id="comp-<?php echo $property_group; ?>" class="accordion-menu <?php if ($i&1) { echo 'odd'; } ?>">
                                        <li class="css-group-heading accordion-heading">
                                            <span class="menu-arrow accordion-menu-arrow tvr-icon" title="<?php _e("Open/close group", "tvr-microthemer"); ?>"></span>
                                            <span class="text-for-group"><?php echo $pg_label; ?></span>
                                        </li>
                                        <?php
                                        ++$i;
                                        ?>
                                    </ul>
                                <?php
                                }
                                ?>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="create-sel-wrap">
                    <span class='wizard-add tvr-button' title="<?php _e("Create a new selector", "tvr-microthemer"); ?>"><?php printf(wp_kses(__('Create Selector', 'tvr-microthemer'), array())); ?></span>
                </div>

            </div>

            <div id="v-left-controls-wrap">

                <div id="v-left-controls">
                    <?php echo $this->display_left_menu_icons(); ?>
                </div>


            </div>

        </div>

        <?php
        $interface_class = $this->preferences['show_interface'] ? 'on' : '';
        ?>
        <div id="m-logo" class="v-left-button m-logo <?php echo $interface_class; ?>"
             title="<?php _e("Expand/collapse the interface", "tvr-microthemer"); ?>" rel="http://themeover.com/"></div>


        <?php
		// store the active media queries so they can be shared with design packs
        if (is_array($this->preferences['m_queries'])){
            foreach ($this->preferences['m_queries'] as $key => $m_query) {
                echo '
			<input type="hidden" name="tvr_mcth[non_section][active_queries]['.$key.'][label]" value="'.esc_attr($m_query['label']).'" />
			<input type="hidden" name="tvr_mcth[non_section][active_queries]['.$key.'][query]" value="'.esc_attr($m_query['query']).'" />';
            }
        }
		?>

        </form>


    </div><!-- end tvr-ui -->

    <?php
    if (!$this->optimisation_test){
        ?>
        <div id="dialogs">

            <!-- Unlock Microthemer -->
            <form name='tvr_validate_form' method="post"
                  autocomplete="off" action="admin.php?page=<?php echo $this->microthemeruipage;?>" >
                <?php
                if ($this->preferences['buyer_validated']){
                    $title = wp_kses(__('Microthemer Has Been Successfully Unlocked', 'tvr-microthemer'), array());
                } else {
                    $title = wp_kses(__('Enter your PayPal email to unlock Microthemer', 'tvr-microthemer'), array());
                }
                echo $this->start_dialog('unlock-microthemer', $title, 'small-dialog'); ?>
                <div class="content-main">
                    <?php
                    if ($this->preferences['buyer_validated']){
                        $class = '';
                        if (!empty($this->preferences['license_type'])){
                            echo '<p>' . wp_kses(__('License Type: ', 'tvr-microthemer'), array()) . '<b>'.$this->preferences['license_type'].'</b></p>';
                        }
                        ?>
						<p><span class="link reveal-unlock"><?php printf(wp_kses(__('Validate software using a different email address', 'tvr-microthemer'), array())); ?></span>
                        </p>
                    <?php
                    } else {
                        $class = 'show';
                    }
                    ?>
                    <div id='tvr_validate_form' class='hidden <?php echo $class; ?>'>
                        <?php wp_nonce_field('tvr_validate_form'); ?>
                        <?php
                        if (!$this->preferences['buyer_validated']){
                            $attempted_email = esc_attr($this->preferences['buyer_email']);
                        } else {
                            $attempted_email = '';
                        }
                        ?>
                        <ul class="form-field-list">
                            <li>
								<label class="text-label" title="<?php _e("Enter your PayPal or Email Address - or the email address listed on 'My Downloads'", "tvr-microthemer"); ?>"><?php printf(wp_kses(__('Enter PayPal email or see email in "My Downloads"', 'tvr-microthemer'), array())); ?></label>
                                <input type='text' autocomplete="off" name='tvr_preferences[buyer_email]'
                                       value='<?php echo $attempted_email; ?>' />

                            </li>
                        </ul>

                        <?php echo $this->dialog_button('Validate', 'input', 'ui-validate'); ?>



                        <div class="explain">
							<div class="heading link explain-link"><?php printf(wp_kses(__('About this feature', 'tvr-microthemer'), array())); ?></div>

                            <div class="full-about">
							<p><?php wp_kses( printf( __('To disable Free Trial Mode and unlock the full program, please enter your PayPal email address. If you purchased Microthemer from CodeCanyon, please send us a "Validate my email" message via the contact form on the right hand side of <a %s>this page</a> (you will need to log in to CodeCanyon first). Receiving this email allows us to verify your purchase.', 'tvr-microthemer'), 'target="_blank" href="http://codecanyon.net/user/themeover"'), array( 'a' => array('href' => array(), 'target' => array()) ));
								?></p>
								<p><?php printf(wp_kses(__('<b>Note:</b> Themeover will record your domain name when you submit your email address for license verification purposes.', 'tvr-microthemer'), array( 'b' => array() )) ); ?></p>
								<p><?php wp_kses( printf(__('<b>Note:</b> if you have any problems with the validator <a %s>send Themeover a quick email</a> and we"ll get you unlocked ASAP.', 'tvr-microthemer'), 'href="https://themeover.com/support/pre-sales-enquiries/" target="_blank"'), array( 'a' => array( 'href' => array(), 'target' => array() ), 'b' => array() )); ?></p>
                            </div>
                        </div>


                    </div>

                </div>
                <?php
                if (!$this->preferences['buyer_validated']){
                    echo $this->end_dialog(wp_kses(__('Validate', 'tvr-microthemer'), array()), 'input', 'ui-validate');
                } else {
                    echo $this->end_dialog(wp_kses(_x('Close', 'verb', 'tvr-microthemer'), array()), 'span', 'close-dialog');
                }
                ?>
            </form>

            <?php
            // this is a separate include because it needs to have separate page for changing gzip
            $page_context = $this->microthemeruipage;
            include $this->thisplugindir . 'includes/tvr-microthemer-preferences.php';
            ?>

            <!-- Edit Media Queries -->
            <form id="edit-media-queries-form" name='tvr_media_queries_form' method="post" autocomplete="off"
                  action="admin.php?page=<?php echo $this->microthemeruipage;?>" >
                <?php wp_nonce_field('tvr_media_queries_form'); ?>
                <?php echo $this->start_dialog('edit-media-queries', wp_kses(__('Edit Media Queries (For Designing Responsively)', 'tvr-microthemer'), array()), 'small-dialog'); ?>

                <div class="content-main">

                    <ul class="form-field-list">
                        <?php

                        // yes no options
                        $yes_no = array(
                            'initial_scale' => array(
                                'label' => __('Set device viewport zoom level to "1"', 'tvr-microthemer'),
                                'explain' => __('Set this to yes if you\'re using media queries to make your site look good on mobile devices. Otherwise mobile phones etc will continue to scale your site down automatically as if you hadn\'t specified any media queries. If you set leave this set to "No" it will not override any viewport settings in your theme, Microthemer just won\'t add a viewport tag at all.', 'tvr-microthemer')
                            )

                        );
                        // text options
                        $text_input = array(
                            'all_devices_default_width' => array(
                                'label' => __('Default screen width for "All Devices" tab', 'tvr-microthemer'),
                                'explain' => __('Leave this blank to let the frontend preview fill the full width of your screen when you\'re on the "All Devices" tab. However, if you\'re designing "mobile first" you can set this to "480px" (for example) and then use min-width media queries to apply styles that will only have an effect on larger screens.', 'tvr-microthemer')
                            ),
                        );

                        // mq set combo
                        $media_query_sets = array(
                            'load_mq_set' => array(
                                'combobox' => 'mq_sets',
                                'label' => __('Select a media query set', 'tvr-microthemer'),
                                'explain' => __('Microthemer lets you choose from a list of media query "sets". If you are trying to make a non-responsive site look good on mobiles, you may want to use the default "Desktop-first device MQs" set. If you designing mobile first, you may want to try an alternative set.', 'tvr-microthemer')
                            )
                        );

                        // overwrite options
                        $overwrite = array(
                            'overwrite_existing_mqs' => array(
                                'default' => __('yes', 'tvr-microthemer'),
                                'label' => __('Overwrite your existing media queries?', 'tvr-microthemer'),
                                'explain' => __('You can overwrite your current media queries by choosing "Yes". However, if you would like to merge the selected media query set with your existing media queries please choose "No".', 'tvr-microthemer')
                            )
                        );

                        $this->output_radio_input_lis($yes_no);

                        $this->output_text_combo_lis($text_input);
                        ?>
						<li><span class="reveal-hidden-form-opts link reveal-mq-sets" rel="mq-set-opts"><?php printf(wp_kses(__('Load an alternative media query set', 'tvr-microthemer'), array())); ?></span></li>
                        <?php

                        $this->output_text_combo_lis($media_query_sets, 'hidden mq-set-opts');

                        $this->output_radio_input_lis($overwrite, 'hidden mq-set-opts');


                        ?>
                    </ul>




					<div class="heading"><?php printf(wp_kses(__('Media Queries', 'tvr-microthemer'), array())); ?></div>

                    <div id="m-queries">
                        <ul id="mq-list">
                            <?php
                            $i = 0;
                            if (is_array($this->preferences['m_queries'])){
                                foreach ($this->preferences['m_queries'] as $key => $m_query) {
                                    $this->edit_mq_row($m_query, $key, $i, false);
                                    ++$i;
                                }
                            }
                            ?>
                        </ul>

						<span id="add-m-query" class="tvr-button add-m-query" rel="<?php echo $i; ?>"><?php printf(wp_kses(__('+ New', 'tvr-microthemer'), array())); ?></span>

                        <input type="hidden" name="tvr_preferences[user_set_mq]" value="1" />
                        <span id="unq-base" rel="<?php echo $this->unq_base; ?>"></span>

                    </div>

                    <div class="explain">
						<div class="heading link explain-link"><?php printf(wp_kses(__('About this feature', 'tvr-microthemer'), array())); ?></div>

                        <div class="full-about">

							<p><?php printf(wp_kses(__('If you\'re not using media queries in Microthemer to make your site look good on mobile devices you don\'t need to set the viewport zoom level to 1. You will be passing judgement over to the devices (e.g. an iPhone) to display your site by automatically scaling it down. But if you are using media queries you NEED to set this setting to "Yes" in order for things to work as expected on mobile devices (otherwise mobile devices will just show a proportionally reduced version of the full-size site).', 'tvr-microthemer'), array())); ?></p>
							<p><?php wp_kses(printf(__('You may want to read <a %s>this tutorial which gives a bit of background on the viewport meta tag</a>.', 'tvr-microthemer'), 'target="_blank" href="http://www.paulund.co.uk/understanding-the-viewport-meta-tag"'), array( 'a' => array( 'href' => array(), 'target' => array() ) )); ?></p>
							<p><?php printf(wp_kses(__('Feel free to rename the media queries and change the media query code. You can also reorder the media queries by dragging and dropping them. This will determine the order in which the media queries are written to the stylesheet and the order that they are displayed in the Microthemer interface.', 'tvr-microthemer'), array())); ?></p>
							<p><?php printf(wp_kses(__('<b>Tip:</b> to reset the default media queries simply delete all media query boxes and then save your settings', 'tvr-microthemer'), array()) ); ?></p>
                        </div>
                    </div>

                </div>

                <?php echo $this->end_dialog(wp_kses(__('Update Media Queries', 'tvr-microthemer'), array()), 'input'); ?>
            </form>

            <!-- must be outside the form -->
            <ul id="m-query-hidden">
                <?php echo $this->edit_mq_row(); ?>
            </ul>


            <!-- Edit Custom Code Tabs -->
            <form id="edit-code-tabs" name='tvr_code_tabs_form' method="post" autocomplete="off"
                  action="admin.php?page=<?php echo $this->microthemeruipage;?>" >
                <?php wp_nonce_field('tvr_code_tabs_form'); ?>
                <?php echo $this->start_dialog('edit-code-tabs', wp_kses(__('Manage Custom Code Editors', 'tvr-microthemer'), array()), 'small-dialog'); ?>

                <div class="content-main">
                    <ul id="code-list">
                        <?php
                        $i = 0;
                        if (is_array($this->preferences['code_tabs'])){
                            foreach ($this->preferences['code_tabs'] as $key => $value) {
                                if (is_array($value)){
                                    foreach ($value as $key => $v2) {
                                        echo '<li>This is just a teaser: <b>'.$v2.'</b></li>';
                                    }
                                } else {
                                    echo '<li>This is just a teaser: <b>'.$value.'</b></li>';
                                }
                                ++$i;
                            }
                        }
                        ?>
                    </ul>
                    <p>You will be able to create new code editors and specify the language (CSS, SCSS, LESS, JavaScript)
                    as well as browser targeting.</p>
                </div>
                <?php echo $this->end_dialog(wp_kses(__('Update Custom Code Tabs', 'tvr-microthemer'), array()), 'input'); ?>
            </form>

            <!-- manage custom code row template
            ...
            -->



            <!-- Import dialog -->
            <form method="post" id="microthemer_ui_settings_import" autocomplete="off">
                <?php wp_nonce_field('tvr_import_from_pack'); ?>
                <?php echo $this->start_dialog('import-from-pack', wp_kses(__('Import settings from a design pack', 'tvr-microthemer'), array()), 'small-dialog'); ?>

                <div class="content-main">
					<p><?php printf(wp_kses(__('Select a design pack to import', 'tvr-microthemer'), array())); ?></p>
                    <p class="combobox-wrap input-wrap">
                        <input type="text" class="combobox" id="import_from_pack_name" name="import_from_pack_name" rel="directories"
                               value="" />
                        <span class="combo-arrow"></span>
                    </p>
					<p class="enter-name-explain"><?php printf(wp_kses(__('Choose to overwrite or merge the imported settings with your current settings', 'tvr-microthemer'), array())); ?></p>

                    <ul id="overwrite-merge" class="checkboxes fake-radio-parent">
						<li><input name="tvr_import_method" type="radio" value="<?php _e('Overwrite', 'tvr-microthemer'); ?>" id='ui-import-overwrite'
                                  class="radio ui-import-method" />
                            <span class="fake-radio"></span>
							<span class="ef-label"><?php printf(wp_kses(__('Overwrite', 'tvr-microthemer'), array())); ?></span>
                        </li>
						<li><input name="tvr_import_method" type="radio" value="<?php _e('Merge', 'tvr-microthemer'); ?>" id='ui-import-merge'
                                  class="radio ui-import-method" />
                            <span class="fake-radio"></span>
							<span class="ef-label"><?php printf(wp_kses(__('Merge', 'tvr-microthemer'), array())); ?></span>
                        </li>
                    </ul>
                    <p class="button-wrap"><?php echo $this->dialog_button(__('Import', 'tvr-microthemer'), 'span', 'ui-import'); ?></p>
                    <div class="explain">
						<div class="heading link explain-link"><?php printf(wp_kses(__('About this feature', 'tvr-microthemer'), array())); ?></div>
                        <div class="full-about">
							<p><?php printf(
								wp_kses(__('Microthemer can be used to restyle any WordPress theme or plugin without the need for pre-configuration. That\'s thanks to the handy "Double-click to edit" feature. But just because you <i>can</i> do everything yourself doesn\'t mean <i>have</i> to. That\'s where importable design packs come in. A design pack contains folders, selectors, hand-coded CSS, and background images that someone else has created while working with Microthemer. Of course it may not be someone else, you can create design packs too using the "<span %s>Export</span>" feature!', 'tvr-microthemer'),
								array( 'i' => array(), 'span' => array() )
								),
								'class="link show-dialog" rel="export-to-pack"'
							); ?> </p>
							<p><?php printf(
								wp_kses(__('Note: you can install other people\'s design packs via the "%s" window.', 'tvr-microthemer'),
									array()),
								'<span class="link show-dialog" rel="manage-design-packs">' . wp_kses(__('Manage Design Packs', 'tvr-microthemer'), array()) . '</span>'
							); ?></p>
							<p><b><?php printf(wp_kses(__('You may want to make use of this feature for the following reasons:', 'tvr-microthemer'), array())); ?></b></p>
                            <ul>
								<li><?php printf(wp_kses(__('You\'ve download and installed a design pack that you found on %s for restyling a theme, contact form, or any other WordPress content you can think of. Importing it will load the folders and hand-coded CSS contained within the design pack into the Microthemer UI.', 'tvr-microthemer'), array()),
									'<a target="_blank" href="http://themeover.com/">themeover.com</a>'
								); ?></li>
								<li><?php printf(wp_kses(__('You previously exported your own work as a design pack and now you would like to reload it back into the Microthemer UI.', 'tvr-microthemer'), array())); ?></li>
                            </ul>
                        </div>
                    </div>
                    <br /><br /><br /><br />
                </div>
                <?php echo $this->end_dialog(wp_kses(_x('Import', 'verb', 'tvr-microthemer'), array()), 'span', 'ui-import'); ?>
            </form>



            <!-- Export dialog -->
            <form method="post" id="microthemer_ui_settings_export" action="#" autocomplete="off">
            <?php echo $this->start_dialog('export-to-pack', wp_kses(__('Export your work as a design pack', 'tvr-microthemer'), array()),
                'small-dialog'); ?>

            <div class="content-main export-form">
                <input type='hidden' id='only_export_selected' name='only_export_selected' value='1' />
                <input type='hidden' id='export_to_pack' name='export_to_pack' value='0' />
                <input type='hidden' id='new_pack' name='new_pack' value='0' />

				<p class="enter-name-explain"><?php printf(wp_kses(__('Enter a new name or export to an existing design pack. Uncheck any folders or custom CSS you don\'t want included in the export.', 'tvr-microthemer'), array())); ?></p>
                <p class="combobox-wrap input-wrap">
                    <input type="text" class="combobox" id="export_pack_name" name="export_pack_name" rel="directories"
                           value="<?php //echo $this->readable_name($this->preferences['theme_in_focus']); ?>" autocomplete="off" />
                    <span class="combo-arrow"></span>

                </p>


				<div class="heading"><?php printf(wp_kses(__('Folders', 'tvr-microthemer'), array())); ?></div>
                <ul id="toggle-checked-folders" class="checkboxes">
                    <li><input type="checkbox" name="toggle_checked_folders" checked="checked"/>
                        <span class="fake-checkbox toggle-checked-folders on"></span>
						<span class="ef-label"><b><?php printf(wp_kses(__('Check All', 'tvr-microthemer'), array())); ?></b></span>
                    </li>
                </ul>
                <ul id="available-folders" class="checkboxes"></ul>

				<div class="heading"><?php printf(wp_kses(__('Custom CSS', 'tvr-microthemer'), array())); ?></div>
                <ul id="custom-css" class="checkboxes">
                    <?php
                    /*if (!empty($this->options['non_section']['hand_coded_css'])) {
                        $checked = 'checked="checked"';
                        $on = 'on';
                    } else {
                        $checked = '';
                        $on = '';
                    }*/
                    ?>
                    <li>
                        <input type="checkbox" name="export_sections[hand_coded_css]" />
                        <span class="fake-checkbox custom-css-all-browsers"></span>
                        <span class="code-icon tvr-icon"></span>
						<span class="ef-label"><?php printf(wp_kses(__('Hand coded CSS (all browsers)', 'tvr-microthemer'), array())); ?></span>
                    </li>
                    <?php
                    $tab_headings = array(
                        'all' => wp_kses(__('IE-specific CSS code', 'tvr-microthemer'), array()),
                        'nine' => wp_kses(__('IE9 and below CSS code', 'tvr-microthemer'), array()),
                        'eight' => wp_kses(__('IE8 and below CSS code', 'tvr-microthemer'), array()),
						'seven' => wp_kses(__('IE7 and below CSS code', 'tvr-microthemer'), array())
					);
                    foreach ($this->preferences['ie_css'] as $key => $value) {
                        /*if (!empty($this->options['non_section']['ie_css'][$key])) {
                            $checked = 'checked="checked"';
                            $on = 'on';
                        } else {
                            $checked = '';
                            $on = '';
                        }*/
                        ?>
                        <li>
                            <input type="checkbox" name="export_sections[ie_css][<?php echo $key; ?>]"  />
                            <span class="fake-checkbox custom-css-<?php echo $key; ?>"></span>
                            <span class="code-icon tvr-icon"></span>
                            <span class="ef-label"><?php echo $tab_headings[$key]; ?></span>
                        </li>
                    <?php
                    }
                    ?>
                </ul>

                <p class="button-wrap"><?php echo $this->dialog_button('Export', 'span', 'export-dialog-button'); ?></p>

                <div class="explain">
					<div class="heading link explain-link"><?php printf(wp_kses(__('About this feature', 'tvr-microthemer'), array())); ?></div>

                    <div class="full-about">
						<p><?php printf(
							wp_kses(__('Microthemer gives you the flexibility to export your current work to a design pack for later use (you can <span %1$s>import</span> it back). Microthemer will create a directory on your server in %2$s which will be used to store your settings and background images. Your folders, selectors, and hand-coded css settings are saved to a configuration file in this directory called config.json.', 'tvr-microthemer'),
								array( 'span' => array() )),
							'class="link show-dialog" rel="import-from-pack"',
							'<code>/wp-content/micro-themes/</code>'
						); ?></p>
						<p><b><?php printf(wp_kses(__('You may want to make use of this feature for the following reasons:', 'tvr-microthemer'), array())); ?></b></p>
                        <ul>
							<li><?php printf(
								wp_kses(__('To make extra sure that your work is backed up (even though there is an automatic revision restore feature). After exporting your work to a design pack you can also download it as a zip package for extra reassurance. You can do this from the "%s" window.', 'tvr-microthemer'), array()),
								'<span class="link show-dialog" rel="manage-design-packs">' . wp_kses(__('Manage Design Packs', 'tvr-microthemer'), array()) . '</span>'
							); ?></li>
							<li><?php printf(wp_kses(__('To save your current work but then start a fresh (using the "reset" option in the left-hand menu)', 'tvr-microthemer'), array())); ?></li>
							<li><?php printf(wp_kses(__('To save one aspect of your design for reuse in other projects (e.g. styling for a menu). You can do this by organising the styles you plan to reuse into a folder and then export only that folder to a design pack by unchecking the other folders before clicking the "Export" button.', 'tvr-microthemer'), array())); ?></li>
							<li><?php printf(
								wp_kses(__('To submit a design pack for sale or free download on %s', 'tvr-microthemer'), array()),
								'<a target="_blank" href="http://themeover.com/">themeover.com</a>'
							); ?></li>
                        </ul>
                    </div>

                </div>

            </div>
            <?php echo $this->end_dialog(wp_kses(_x('Export', 'verb', 'tvr-microthemer'), array()), 'span', 'export-dialog-button'); ?>
            </form>


            <!-- View CSS -->
            <?php echo $this->start_dialog('display-css-code', wp_kses(__('View the CSS code Microthemer generates', 'tvr-microthemer'), array()), 'small-dialog'); ?>

            <div class="content-main">
                <span id="view-css-trigger" rel="display-css-code"></span>
                <pre id="generated-css"></pre>
                <div class="explain">
					<div class="heading link explain-link"><?php printf(wp_kses(__('About this feature', 'tvr-microthemer'), array())); ?></div>

                    <div class="full-about">
						<p><?php printf(wp_kses(__('What you see above is the CSS code Microthemer is currently generating. This can sometimes be useful for debugging issues if you know CSS. Or if you want to reuse the code Microthemer generates elsewhere.', 'tvr-microthemer'), array())); ?></p>
						<p><?php wp_kses(
							printf(__('<b>Did you know</b> - it\'s possible to disable or completely uninstall Microthemer and still use the customisations. You just need to paste a small piece of code in your theme\'s functions.php file. See this <a %s>forum post</a> for further information.', 'tvr-microthemer'),
								'target="_blank" href="http://themeover.com/forum/topic/microthemer-customizations-when-deactived/"' ),
							array( 'a' => array( 'href' => array(), 'target' => array() ), 'b' => array() )
						); ?></p>
						<p><?php printf(wp_kses(__('<b>Also note</b>, Microthemer adds the "!important" declaration to all CSS styles by default. If you\'re up to speed on %1$s you may want to disable this behaviour on the <span %2$s>preferences page</span>. If so, you will still be able to apply "!important" declarations on a per style basis by clicking the faint "i"s that will appear to the right of all style option fields.', 'tvr-microthemer'),
							array( 'b' => array(), 'span' => array() )),
							'<a target="_blank" href="http://themeover.com/beginners-guide-to-understanding-css-specificity/">' .
								wp_kses(__('CSS specificity', 'tvr-microthemer'), array()) . '</a>',
							'class="link show-dialog" rel="display-preferences"'
						); ?></p>
                    </div>

                </div>
            </div>
            <?php echo $this->end_dialog(wp_kses(_x('Close', 'verb', 'tvr-microthemer'), array()), 'span', 'close-dialog'); ?>

            <!-- Restore Settings -->
            <?php echo $this->start_dialog('display-revisions', wp_kses(__('Restore settings from a previous save point', 'tvr-microthemer'), array()), 'small-dialog'); ?>

            <div class="content-main">
                <div id='revisions'>
                    <div id='revision-area'></div>
                </div>
                <span id="view-revisions-trigger" rel="display-revisions"></span>
                <div class="explain">
				<div class="heading link explain-link"><?php printf(wp_kses(__('About this feature', 'tvr-microthemer'), array())); ?></div>
                    <div class="full-about">
					<p><?php printf(wp_kses(__('Click the "restore" link in the right hand column of the table to restore your workspace settings to a previous save point.', 'tvr-microthemer'), array())); ?></p>
                    </div>
                </div>
            </div>
            <?php echo $this->end_dialog(wp_kses(_x('Close', 'verb', 'tvr-microthemer'), array()), 'span', 'close-dialog'); ?>

            <!-- Spread the word -->
            <?php echo $this->start_dialog('display-share', wp_kses(__('Show off your new discovery', 'tvr-microthemer'), array()), 'small-dialog'); ?>
            <div class="content-main">
                <div class="explain">
					<div class="heading link explain-link"><?php printf(wp_kses(__('About this feature', 'tvr-microthemer'), array())); ?></div>
                    <div class="full-about">
						<p><?php printf(wp_kses(__('cash back feature - coupon code to give new customer, affiliate commission for existing', 'tvr-microthemer'), array())); ?></p>
						<p><?php printf(wp_kses(__('For now, just a simply share widget.', 'tvr-microthemer'), array())); ?></p>
                    </div>
                </div>
            </div>
            <?php echo $this->end_dialog(wp_kses(_x('Close', 'verb', 'tvr-microthemer'), array()), 'span', 'close-dialog'); ?>

        </div>

        <!-- Manage Design Packs -->
        <?php echo $this->start_dialog('manage-design-packs', wp_kses(__('Install & Manage Design Packs', 'tvr-microthemer'), array())); ?>
        <iframe id="manage_iframe" class="microthemer-iframe" frameborder="0" name="manage_iframe"
                rel="<?php echo 'admin.php?page='.$this->microthemespage; ?>"
                src="<?php echo $this->thispluginurl; ?>includes/place-holder2.html"
                data-frame-loaded="0"></iframe>
        <?php echo $this->end_dialog(wp_kses(_x('Close', 'verb', 'tvr-microthemer'), array()), 'span', 'close-dialog'); ?>

        <!-- Program Docs -->
        <?php echo $this->start_dialog('program-docs', wp_kses(__('Help Centre', 'tvr-microthemer'), array())); ?>
        <iframe id="docs_iframe" class="microthemer-iframe" frameborder="0" name="docs_iframe"
                rel="http://themeover.com/support/"
                src="<?php echo $this->thispluginurl; ?>includes/place-holder2.html"
                data-frame-loaded="0"></iframe>
        <?php echo $this->end_dialog(wp_kses(_x('Close', 'verb', 'tvr-microthemer'), array()), 'span', 'close-dialog'); ?>

        <!-- Integration -->
        <?php echo $this->start_dialog('integration', wp_kses(__('Integration with 3rd party software', 'tvr-microthemer'), array()), 'small-dialog'); ?>
        <div class="content-main">
			<div class="heading"><?php printf(wp_kses(__('WPTouch Mobile Plugin', 'tvr-microthemer'), array())); ?></div>
			<p><?php wp_kses(
				printf(__('Microthemer can be used to style the mobile-only theme that WPTouch presents to mobile devices. In order to load the mobile theme in Microthemer\'s preview window, simply enable WPTouch mode using the toggle in the left toolbar. This toggle will only appear if Microthemer detects that you have installed and activated WPTouch. There is a <a %1$s>free</a> and <a %2$s>premium version</a> of WPTouch.', 'tvr-microthemer'),
					'target="_blank" href="<?php echo $this->wp_admin_url; ?>plugin-install.php?tab=search&type=term&s=wptouch+mobile+plugin"',
					'target="_blank" href="http://www.wptouch.com/"' ),
				array( 'a' => array( 'href' => array(), 'target' => array() ) )
			); ?></p>
            <div class="explain">
			<div class="heading link explain-link"><?php printf(wp_kses(__('About this feature', 'tvr-microthemer'), array())); ?></div>
                <div class="full-about">
					<p><?php printf(wp_kses(__('When possible, we\'ll add little features to make it easier to use Microthemer with complementary products.', 'tvr-microthemer'), array())); ?></p>
                </div>
            </div>
        </div>
        <?php echo $this->end_dialog(wp_kses(_x('Close', 'verb', 'tvr-microthemer'), array()), 'span', 'close-dialog'); ?>
        <?php
    }
    ?>



    <!-- error report form -->
    <form id="error-report-form" name="error_report" method="post">
        <textarea name="tvr_php_error"></textarea>
        <textarea name="tvr_serialised_data"></textarea>
        <textarea name="tvr_browser_info"></textarea>
    </form>

    <!-- html templates -->
    <form action='#' name='dummy' id="html-templates">
        <?php
        if (!$this->optimisation_test){
            ?>
            <img id="loading-gif-template" class="ajax-loader small" src="<?php echo $this->thispluginurl; ?>/images/ajax-loader-green.gif" />
            <img id="loading-gif-template-wbg" class="ajax-loader small" src="<?php echo $this->thispluginurl; ?>/images/ajax-loader-wbg.gif" />
            <img id="loading-gif-template-mgbg" class="ajax-loader small" src="<?php echo $this->thispluginurl; ?>/images/ajax-loader-mgbg.gif" />
            <?php
            // template for displaying save error and error report option
            $short = __('Error saving settings', 'tvr-microthemer');
			$long = '<p>' .
				printf( wp_kses(__('Please <span %s>click this link to email an error report to Themeover</span>. The error report sends us information about your current Microthemer settings, server and browser information, and your WP admin email address. We use this information purely for replicating your issue and then contacting you with a solution.', 'tvr-microthemer'),
					array( 'span' => array() )),
				'id="email-error" class="link"'
			) . '</p>
			<p>' . printf( wp_kses(__('<b>Note:</b> reloading the page is normally a quick fix for now. However, unsaved changes will need to be redone.', 'tvr-microthemer'), array( 'span' => array() ))
			). '</p>';
            echo $this->display_log_item('error', array('short'=> $short, 'long'=> $long), 0, 'id="log-item-template"');
            // define template for menu section
            $this->menu_section_html('selector_section', 'section_label');
            // define template for menu selector
            $this->menu_selector_html('selector_section', 'selector_css', array('selector_code', 'selector_label'), 1);
            // define template for section
            echo $this->section_html('selector_section', array());
            // define template for selector
            echo $this->single_selector_html('selector_section', 'selector_css', '', true);
            // define mq template
            //echo $this->media_query_tabs('selector_section', 'selector_css', 'property_group', '', true);
            // define property group templates
            foreach ($this->propertyoptions as $property_group_name => $property_group_array) {
                echo $this->single_option_fields('selector_section', 'selector_css', $property_group_array,
                    $property_group_name, '', true);
            }
        }
        ?>

    </form>
    <!-- end html templates -->


</div><!-- end #tvr -->
