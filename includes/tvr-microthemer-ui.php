<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}
// get the file structure for use in scripts
foreach ($this->file_structure as $dir => $array) {
    if (!empty($dir)) {
        $directories[] = $this->readable_name($dir);
    }
}
// get media query sets
$i = 0;
foreach ($this->mq_sets as $set => $junk){
    if (++$i == 1){
        $set.= ' (default)';
    }
    $mq_sets[] = $set;
}
?>
<script>
    TvrCombo.directories = <?php echo json_encode($directories); ?>;
    if (!TvrCombo.directories){
        TvrCombo.directories = [];
    }
    TvrCombo.cur_folders = [];
    TvrCombo.mq_sets = <?php echo json_encode(str_replace('_', ' ', $mq_sets)); ?>;
</script>


<?php // output the user interface

//css helper for option icons
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

?>

<div id="tvr" class='wrap tvr-wrap'>
	<div id='tvr-ui'>

        <span id="ui-nonce"><?php echo wp_create_nonce('tvr_microthemer_ui_load_styles'); ?></span>
        <span id="fonts-api" rel="<?php echo $this->thispluginurl.'includes/fonts-api.php'; ?>"></span>
        <span id="ui-url" rel="<?php echo 'admin.php?page=' . $this->microthemeruipage; ?>"></span>
        <span id="ajaxUrl" rel="<?php echo trailingslashit($this->site_url).'wp-admin/admin.php?page='.$this->microthemeruipage.'&_wpnonce='.wp_create_nonce('mcth_simple_ajax') ?>"></span>
        <span id="resetUrl" rel="<?php echo '&_wpnonce='.wp_create_nonce('tvr_microthemer_ui_reset');?>&action=tvr_ui_reset"></span>
        <span id="clearUrl" rel="<?php echo '&_wpnonce='.wp_create_nonce('tvr_microthemer_clear_styles');?>&action=tvr_clear_styles"></span>


        <span id='site-url' rel="<?php echo $this->site_url; ?>"></span>
        <span id="active-styles-url" rel="<?php echo $this->micro_root_url . 'active-styles.css' ?>"></span>
        <span id="revisions-url" rel="<?php echo 'admin.php?page=' . $this->microthemeruipage .
            '&_wpnonce='.wp_create_nonce('tvr_get_revisions'). '&action=get_revisions'; ?>"></span>

        <span id='auto-visual-pref' rel='<?php echo $this->preferences['load_visual']; ?>'></span>
        <span id='all_devices_default_width' rel='<?php echo $this->preferences['all_devices_default_width']; ?>'></span>


        <span id='plugin-url' rel='<?php echo $this->thispluginurl; ?>'></span>
        <span id='plugin-trial' rel='<?php echo $this->preferences['buyer_validated']; ?>'></span>
        <form method="post" name="tvr_microthemer_ui_serialised" id="tvr_microthemer_ui_serialised" autocomplete="off">
            <?php wp_nonce_field('tvr_microthemer_ui_serialised');?>
            <input type="hidden" name="action" value="tvr_microthemer_ui_serialised" />
            <textarea id="tvr-serialised-data" name="tvr_serialized_data">hello</textarea>
        </form>
        <?php

        // classes that affect display of things in the ui
        $main_class = '';
        if ($this->preferences['css_important'] != 1){
            $main_class.= 'manual-css-important';
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
                <div id="tvr-nav" class="tvr-nav">
                    <div id="toggle-highlighting" title="Toggle highlighting"></div>
                    <div id="quick-nav" class="quick-nav">
                        <span id="vb-focus-prev" class="scroll-buttons tvr-icon" title="Go To Previous Selector"></span>
                        <span id="vb-focus-next" class="scroll-buttons tvr-icon" title="Go To Next Selector"></span>
                    </div>
                    <div id="tvr-main-menu" class="tvr-main-menu-wrap">
                        <div id="main-menu-popdown" class="main-menu-popdown">
                            <div id="add-new-section">
                                <div class="inner-wrap">
                                    <div class='new-section'>
                                        <input type='text' title="New Folder..." class='new-section-input' name='new_section[name]' value='' />
                                        <span class='new-section-add tvr-button' title="Create a new folder">Add</span>
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
                                <span class="total-folders">Folders&nbsp;&nbsp;</span>

                                <span class="tvr-icon selector-icon"></span>
                                <span id="total-sel-count"><?php echo $this->total_selectors; ?></span>
                                <span>Selectors</span>

                            </div>
                        </div>
                    </div>

                    <span id="current-selector"></span>

                </div>

                <div class="right-stuff-wrap">




                    <div id="help-on-right" class="program-docs v-left-button show-dialog" rel="program-docs"
                         title="Learn how to use Microthemer"></div>
                    <div id="edit-css-code" class="edit-css-code v-left-button" title="Code editor view"></div>


                    <div id="status-board" class="tvr-popdown-wrap">

                        <!--<span class="tvr-icon info-icon"></span>-->
                        <div id="status-short"></div>

                        <div id="full-logs" class="tvr-popdown scrollable-area">
                            <div class="heading">Microthemer Notifications</div>
                            <?php

                            echo $this->display_log();
                            ?>
                            <div id="script-feedback"></div>
                        </div>
                    </div>

                    <span id="adv-wizard-toggle" class="adv-wizard-toggle">
                        <span class="adv-main-label link">Advanced options:</span>
                        <span class="always-adv-wrap">
                            <input type="checkbox" id="always-adv-checkbox" name="always_advanced" value="1"
                                <?php
                                $on = '';
                                if (!empty($this->preferences['show_adv_wizard'])){
                                   echo 'checked="checked"';
                                    $on = 'on';
                                }
                                ?>
                                />
                            <span class="fake-checkbox <?php echo $on; ?>"></span>
                            <span class="ef-label">always show</span>
                        </span>
                    </span>

                    <?php
                    if (!$this->preferences['buyer_validated']){
                        ?>
                        <a class="cta-button buy-cta tvr-button red-button" href="http://themeover.com" target="_blank"
                              title="Purchase a license to use the full program">
                            <span class="tvr-icon"></span>
                            <span class="cta-label">Buy</span>
                        </a>
                        <span class="cta-button unlock-cta tvr-button show-dialog"
                              title="Enter your email address to unlock the full program" rel="unlock-microthemer">
                            <span class="tvr-icon show-dialog" rel="unlock-microthemer"></span>
                            <span class="cta-label show-dialog" rel="unlock-microthemer">Unlock</span>
                        </span>
                    <?php
                    }
                    ?>
                </div>
                <div id="starter-message">Double-click anything on the page to begin restyling it.</div>

                <ul id='tvr-options'>
                    <?php
                    foreach ( $this->initial_options_html as $key => $html) {
                        echo $html;
                    }
                    ?>
                    <div class="frame-shadow"></div>
                </ul>


                <div id='hand-css-area'>
                    <div id="css-tab-areas" class="query-tabs css-code-tabs">
                        <?php
                        // save the configuration of the css tab
                        if ( empty($this->options['non_section']['css_focus'])) {
                            $css_focus = 'all-browsers';
                        } else {
                            $css_focus = $this->options['non_section']['css_focus'];
                        }
                        $tab_headings = array(
                            'all-browsers' => 'All Browsers',
                            'all' => 'All versions of IE',
                            'nine' => 'IE9 and below',
                            'eight' => 'IE8 and below',
                            'seven' => 'IE7 and below');
                        foreach ($tab_headings as $key => $value) {
                            if ($key == $css_focus){
                                $active_c = 'active';
                            } else {
                                $active_c = '';
                            }
                            echo '<span class="css-tab mq-tab css-tab-'.$key.' show '.$active_c.'" rel="'.$key.'">'.$tab_headings[$key].'</span>';
                        }
                        ?>
                        <input class="css-focus" type="hidden"
                               name="tvr_mcth[non_section][css_focus]"
                               value="<?php echo $css_focus; ?>" />
                    </div>
                    <div id="code-editors-wrap">
                        <?php
                        foreach ($tab_headings as $key => $value) {
                            if ($key == 'all-browsers'){
                                $css_code = htmlentities(stripslashes($this->options['non_section']['hand_coded_css']));
                                $name = 'tvr_mcth[non_section][hand_coded_css]';
                            } else {
                                $css_code = htmlentities(stripslashes($this->options['non_section']['ie_css'][$key]));
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

                <div id="selector-wizard">
                    <div class="quick-create">

                        <label>Selector Name:</label>
                        <input id='wizard-name' type='text' title="Something memorable" class='wizard-name wizard-input' name='wizard_name' value='' />


                        <label>Where:</label>
                        <span class="input-wrap">
                            <input type="text" class="combobox wizard-folder wizard-input text-label" title="Enter new or select a folder..."
                                   id="wizard_folder" name="wizard_folder" rel="cur_folders" value="" />
                            <span class="combo-arrow"></span>
                        </span>

                        <span class='wizard-add tvr-button' title="Create a new folder">Create Selector</span>
                        <span class="cancel-wizard cancel link" title="Cancel Operation - Close The Selector Wizard<">Close Wizard</span>


                    </div>
                    <div class="slider-with-wizard"></div>
                </div>

                <div id="v-mq-controls" class="hidden">
                    <span id="iframe-pixel-width"></span>
                    <span id="iframe-max-width"></span>
                    <div id="v-mq-slider" class="tvr-slider"></div>
                    <span id="iframe-min-width"></span>

                    <div id="v-mq-buttons">
                    <span id="mq-button-all-devices" class="v-mq-button active" rel="0"
                          title="All Devices">All Devices</span>
                        <?php
                        $min_store = '';
                        $max_store = '';
                        $first = true;
                        foreach ($this->preferences['m_queries'] as $key => $m_query) {
                            if (empty($m_query['min'])){
                                $m_query['min'] = '';
                            }
                            if (empty($m_query['max'])){
                                $m_query['max'] = '';
                            }
                            ?>
                            <span id="mq-button-<?php echo $key; ?>" class="v-mq-button" rel="<?php echo $key; ?>"
                                  title="<?php echo $m_query['query']; ?>"><?php echo $m_query['label']; ?></span>
                            <?php
                            if (!$first) {
                                $min_store.= '|';
                                $max_store.= '|';
                            }
                            $first = false;
                            $min_store.= $key.','.$m_query['min'];
                            $max_store.= $key.','.$m_query['max'];
                        }
                        ?>
                    </div>


                    <span id="tvr-min-store" rel="<?php echo $min_store; ?>"></span>
                    <span id="tvr-max-store" rel="<?php echo $max_store; ?>"></span>
                </div>
                <div class="frame-shadow"></div>
            </div>

            <div id="v-frontend-wrap">
                <div id="v-frontend" title="Double-click an element to create editing options for it">
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

                </div>
                <div id="advanced-wizard">
                    <div id="adv-tabs" class="query-tabs adv-tabs">
                        <?php
                        // save the configuration of the css tab
                        if (empty($this->preferences['adv_wizard_tab'])){
                            $adv_wizard_focus = 'refine-targeting';
                        } else {
                            $adv_wizard_focus = $this->preferences['adv_wizard_tab'];
                        }
                        $tab_headings = array(
                            'refine-targeting' => 'Targeting',
                            'html-inspector' => 'Inspector',);
                        foreach ($tab_headings as $key => $value) {
                            if ($key == $adv_wizard_focus){
                                $active_c = 'active';
                            } else {
                                $active_c = '';
                            }
                            echo '<span class="adv-tab mq-tab adv-tab-'.$key.' show '.$active_c.'" rel="'.$key.'">'.$tab_headings[$key].'</span>';
                        }
                        // this is redundant (preferences store focus) but kept for consistency with other tab remembering
                        ?>
                        <input class="adv-wizard-focus" type="hidden"
                               name="tvr_mcth[non_section][adv_wizard_focus]"
                               value="<?php echo $adv_wizard_focus; ?>" />
                    </div>

                    <div class="wizard-panes">
                        <div class="adv-area-refine-targeting adv-area hidden <?php
                        if ($adv_wizard_focus == 'refine-targeting') {
                            echo 'show';
                        }?>">
                            <div class="scrollable-refined">
                                <div class="refine-inner-wrap">
                                    <div class="code-suggestion-wrap">
                                        <div id="code-suggestion-slider" class="tvr-slider"></div>
                                    </div>
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
                                        <span class="tvr-prev-sibling refine-button" title="Move to Previous Sibling Element"></span>
                                        <div class="updown-wrap">
                                            <span class="tvr-parent refine-button" title="Move to Parent Element"></span>
                                            <span class="tvr-child refine-button" title="Move to Child Element"></span>
                                        </div>
                                        <span class="tvr-next-sibling refine-button" title="Move to Next Sibling Element"></span>
                                    </div>
                                    <div class="refine-target-html">
                                        <span class="display-html"></span>
                                    </div>
                                </div>

                                <div id="html-computed-css">
                                    <div class="scrollable-area">
                                        
                                        <?php
                                        $i = 1;
                                        foreach ($this->property_option_groups as $k => $property_group) {
                                            ?>
                                            <ul id="comp-<?php echo $property_group; ?>" class="accordion-menu <?php if ($i&1) { echo 'odd'; } ?>">
                                                <li class="css-group-heading accordion-heading">
                                                    <span class="menu-arrow accordion-menu-arrow tvr-icon" title="Open/close group"></span>
                                                    <span class="text-for-group"><?php echo str_replace('_', ' & ', $property_group); ?></span>
                                                </li>
                                                <?php
                                                /*foreach ($prop_array as $property_id => $array) {
                                                    ?>
                                                    <li class="property-item accordion-item"><a href="css-reference.php?snippet=<?php echo $property_id; ?>&prop_group=<?php echo $property_group; ?>">
                                                        <?php echo $array['title']; ?></a></li><?php
                                                }*/
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

                </div>
            </div>

            <div id="v-left-controls-wrap">

                <div id="m-logo" class="v-left-button m-logo" title="Visit themeover.com" rel="http://themeover.com/"></div>

                <?php
                $show = '';
                $showing = '';
                if (!empty($this->preferences['left_menu_down'])){
                    $show = 'show';
                    $showing = 'showing';
                }
                ?>
                <div id="v-left-controls" class="hidden <?php echo $show; ?>">
                    <?php echo $this->display_left_menu_icons(); ?>
                </div>

                <div id="toggle-left-menu" class="<?php echo $showing; ?>"></div>

            </div>


        </div>


        <?php
		// store the active media queries so they can be shared with design packs
		foreach ($this->preferences['m_queries'] as $key => $m_query) {
			echo '
			<input type="hidden" name="tvr_mcth[non_section][active_queries]['.$key.'][label]" value="'.esc_attr($m_query['label']).'" />
			<input type="hidden" name="tvr_mcth[non_section][active_queries]['.$key.'][query]" value="'.esc_attr($m_query['query']).'" />';	
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
                    $title = 'Microthemer Has Been Successfully Unlocked';
                } else {
                    $title = 'Enter Your PayPal Email Address To Unlock Microthemer';
                }
                echo $this->start_dialog('unlock-microthemer', $title, 'small-dialog'); ?>
                <div class="content-main">
                    <?php
                    if ($this->preferences['buyer_validated']){
                        $class = '';
                        if (!empty($this->preferences['license_type'])){
                            echo '<p>License Type: <b>'.$this->preferences['license_type'].'</b></p>';
                        }
                        ?>
                        <p><span class="link reveal-unlock">Validate software using a different email address</span>
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
                                <label title="Enter your PayPal or Email Address - or the email address listed on 'My Downloads'">Please enter your PayPal email address:</label>
                                <input type='text' autocomplete="off" name='tvr_preferences[buyer_email]'
                                       value='<?php echo $attempted_email; ?>' />

                            </li>
                        </ul>

                        <?php echo $this->dialog_button('Validate', 'input', 'ui-validate'); ?>



                        <div class="explain">
                            <div class="heading link explain-link">About this feature</div>

                            <div class="full-about">
                                <p>To disable Free Trial Mode and unlock the full program, please enter your PayPal email address. If you purchased Microthemer from CodeCanyon, please send us a "Validate my email" message via the contact form on the right hand side of <a target='_blank' href='http://codecanyon.net/user/themeover '>this page</a> (you will need to log in to CodeCanyon first).
                                    Receiving this email allows us to verify your purchase.</p>
                                <p><b>Note:</b> Themeover will record your domain name when you submit your email address for license verification purposes.</p>
                                <p><b>Note:</b> if you have any problems with the validator
                                    <a href="https://themeover.com/support/pre-sales-enquiries/" target="_blank">send Themeover a quick email</a>
                                    and we'll get you unlocked ASAP.</p>
                            </div>
                        </div>


                    </div>

                </div>
                <?php
                if (!$this->preferences['buyer_validated']){
                    echo $this->end_dialog('Validate', 'input', 'ui-validate');
                } else {
                    echo $this->end_dialog('Close', 'span', 'close-dialog');
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
                <?php echo $this->start_dialog('edit-media-queries', 'Edit Media Queries (For Designing Responsively)', 'small-dialog'); ?>

                <div class="content-main">

                    <ul class="form-field-list">
                        <?php

                        // yes no options
                        $yes_no = array(
                            'initial_scale' => array(
                                'label' => 'Set device viewport zoom level to "1"',
                                'explain' => 'Set this to yes if you\'re using media queries to make your site look good on mobile devices. Otherwise mobile phones etc will continue to scale your site down automatically as if you hadn\'t specified any media queries.'
                            )

                        );
                        // text options
                        $text_input = array(
                            'all_devices_default_width' => array(
                                'label' => 'Default screen width for "All Devices" tab',
                                'explain' => 'Leave this blank to let the frontend preview fill the full width of your screen when you\'re on the "All Devices" tab. However, if you\'re following best practice and designing "mobile first" you can set this to "480px" (for example) and then use min-width media queries to apply styles that will only have an effect on larger screens.'
                            ),
                        );

                        // mq set combo
                        $media_query_sets = array(
                            'load_mq_set' => array(
                                'combobox' => 'mq_sets',
                                'label' => 'Select a media query set',
                                'explain' => 'Microthemer lets you choose from a list of media query "sets". If you are trying to make a non-responsive site look good on mobiles, you may want to use the default "Desktop-first device MQs" set. If you designing mobile first, you may want to try an alternative set.'
                            )
                        );

                        // overwrite options
                        $overwrite = array(
                            'overwrite_existing_mqs' => array(
                                'default' => 'yes',
                                'label' => 'Overwrite your existing media queries?',
                                'explain' => 'You can overwrite your current media queries by choosing "Yes". However, if you would like to merge the selected media query set with your existing media queries please choose "No".'
                            )

                        );

                        $this->output_radio_input_lis($yes_no);

                        $this->output_text_combo_lis($text_input);
                        ?>
                        <li><span class="reveal-hidden-form-opts link reveal-mq-sets" rel="mq-set-opts">Load an alternative media query set</span></li>
                        <?php

                        $this->output_text_combo_lis($media_query_sets, 'hidden mq-set-opts');

                        $this->output_radio_input_lis($overwrite, 'hidden mq-set-opts');


                        ?>
                    </ul>




                    <div class="heading">Media Queries</div>

                    <div id="m-queries">
                        <ul id="mq-list">
                            <?php
                            $i = 0;

                            if (is_array($this->preferences['m_queries'])){
                                foreach ($this->preferences['m_queries'] as $key => $m_query) {
                                    ?>
                                    <li class="mq-row mq-row-<?php echo $i; ?>">
                                        <span class="del-m-query tvr-icon delete-icon"></span>
                                        <div class="mq-edit-wrap mq-label-wrap"><label>Label:</label>
                                            <input class="m-label" type="text" name="tvr_preferences[m_queries][<?php echo $key; ?>][label]"
                                                   value="<?php echo esc_attr($m_query['label']); ?>" /></div>
                                        <div class="mq-edit-wrap mq-query-wrap"><label>Media Query:</label>
                                            <input class="m-code" type="text" name="tvr_preferences[m_queries][<?php echo $key; ?>][query]"
                                                   value="<?php echo esc_attr($m_query['query']); ?>" /></div>
                                    </li>
                                    <?php
                                    ++$i;
                                }
                            }

                            ?>
                        </ul>

                        <span id="add-m-query" class="tvr-button add-m-query" rel="<?php echo $i; ?>">+ New</span>

                        <input type="hidden" name="tvr_preferences[user_set_mq]" value="1" />
                        <span id="unq-base" rel="<?php echo $this->unq_base; ?>"></span>

                    </div>

                    <div class="explain">
                        <div class="heading link explain-link">About this feature</div>

                        <div class="full-about">
                            <p>If you're not using media queries in Microthemer to make your site look good on mobile devices you don't need to set the viewport zoom level to 1. You will be passing judgement over to the devices (e.g. an iPhone) to display your
                                site by automatically scaling it down. But if you are using media queries you NEED to set this setting to "Yes" in order for things to work as expected on mobile devices (otherwise mobile devices will just show a proportionally reduced version of the full-size site).</p>
                            <p>You may want to read <a target="_blank" href="http://www.paulund.co.uk/understanding-the-viewport-meta-tag">
                                    this tutorial which gives a bit of background on the viewport meta tag.</a></p>
                            <p>Feel free to rename the media queries and change the media query code.
                                You can also reorder the media queries by dragging and dropping them. This will determine the order in which the
                                media queries are written to the stylesheet and the order that they are displayed in the Microthemer interface.</p>
                            <p><b>Tip:</b> to reset the default media queries simply delete all media query boxes and then save your settings</p>
                        </div>
                    </div>

                </div>

                <?php echo $this->end_dialog('Update Media Queries', 'input'); ?>
            </form>
            <!-- must be outside the form -->
            <ul id="m-query-hidden">
                <li class="mq-row m-query-tpl">
                    <span class="del-m-query tvr-icon delete-icon"></span>
                    <div class="mq-edit-wrap mq-label-wrap"><label>Label:</label>
                        <input class="m-label" type="text" name="tvr_preferences[m_queries][key][label]" value="" /></div>
                    <div class="mq-edit-wrap mq-query-wrap"><label>Media Query:</label>
                        <input class="m-code" type="text" name="tvr_preferences[m_queries][key][query]" value="" /></div>
                </li>
            </ul>


            <!-- Import dialog -->
            <form method="post" id="microthemer_ui_settings_import" action="admin.php?page=<?php echo $this->microthemeruipage; ?>" autocomplete="off">
                <?php wp_nonce_field('tvr_import_from_pack'); ?>
                <?php echo $this->start_dialog('import-from-pack', 'Import settings from a design pack', 'small-dialog'); ?>

                <div class="content-main">
                    <p>Select a design pack to import</p>
                    <p class="combobox-wrap input-wrap">
                        <input type="text" class="combobox" id="import_from_pack_name" name="import_from_pack_name" rel="directories"
                               value="" />
                        <span class="combo-arrow"></span>
                    </p>
                    <p class="enter-name-explain">Chose to overwrite or merge the imported settings with your current settings</p>

                    <ul id="overwrite-merge" class="checkboxes fake-radio-parent">
                        <li> <input name="tvr_import_method" type="radio" value="Overwrite" id='ui-import-overwrite'
                                    class="radio ui-import-method" />
                            <span class="fake-radio"></span>
                            <span class="ef-label">Overwrite</span>
                        </li>
                        <li><input name="tvr_import_method" type="radio" value="Merge" id='ui-import-merge'
                                   class="radio ui-import-method" />
                            <span class="fake-radio"></span>
                            <span class="ef-label">Merge</span>
                        </li>
                    </ul>
                    <p class="button-wrap"><?php echo $this->dialog_button('Import', 'span', 'ui-import'); ?></p>
                    <div class="explain">
                        <div class="heading link explain-link">About this feature</div>
                        <div class="full-about">
                            <p>Microthemer can be used to restyle any WordPress theme or plugin without the need
                                for pre-configuration. That's thanks to the handy "Double-click to edit" feature.
                                But just because you <i>can</i> do everything yourself doesn't mean <i>have</i> to.
                                That's where importable design packs come in. A design pack contains folders, selectors,
                                hand-coded CSS, and background images that someone else has created while working with
                                Microthemer. Of course it may not be someone else, you can create design packs too using the
                                "<span class="link show-dialog" rel="export-to-pack">Export</span>" feature!
                            </p>
                            <p>Note: you can install other people's design packs via the
                                "<span class="link show-dialog" rel="manage-design-packs">Manage Design Packs</span>" window.</p>
                            <p><b>You may want to make use of this feature for the following reasons:</b></p>
                            <ul>
                                <li>You've download and installed a design pack that you found on <a target="_blank" href="http://themeover.com/">themeover.com</a> for restyling
                                    a theme, contact form, or any other WordPress content you can think of.
                                    Importing it will load the folders and hand-coded CSS contained within the design pack
                                    into the Microthemer UI.</li>
                                <li>You previously exported your own work as a design pack and now you would like to reload
                                    it back into the Microthemer UI.</li>
                            </ul>
                        </div>
                    </div>
                    <br /><br /><br /><br />
                </div>
                <?php echo $this->end_dialog('Import', 'span', 'ui-import'); ?>
            </form>



            <!-- Export dialog -->
            <form method="post" id="microthemer_ui_settings_export" action="admin.php?page=<?php echo $this->microthemeruipage; ?>" autocomplete="off">
            <?php echo $this->start_dialog('export-to-pack', 'Export your work as a design pack',
                'small-dialog'); ?>

            <div class="content-main export-form">
                <input type='hidden' id='only_export_selected' name='only_export_selected' value='1' />
                <input type='hidden' id='export_to_pack' name='export_to_pack' value='0' />
                <input type='hidden' id='new_pack' name='new_pack' value='0' />

                <p class="enter-name-explain">Enter a new name or export to an existing design pack. Uncheck any folders or custom CSS you don't want included in the export.</p>
                <p class="combobox-wrap input-wrap">
                    <input type="text" class="combobox" id="export_pack_name" name="export_pack_name" rel="directories"
                           value="<?php //echo $this->readable_name($this->preferences['theme_in_focus']); ?>" autocomplete="off" />
                    <span class="combo-arrow"></span>

                </p>


                <div class="heading">Folders</div>
                <ul id="toggle-checked-folders" class="checkboxes">
                    <li><input type="checkbox" name="toggle_checked_folders" checked="checked"/>
                        <span class="fake-checkbox toggle-checked-folders on"></span>
                        <span class="ef-label"><b>Check All</b></span>
                    </li>
                </ul>
                <ul id="available-folders" class="checkboxes"></ul>

                <div class="heading">Custom CSS</div>
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
                        <span class="ef-label">Hand coded CSS (all browsers)</span>
                    </li>
                    <?php
                    $tab_headings = array(
                        'all' => 'IE-specific CSS code',
                        'nine' => 'IE9 and below CSS code',
                        'eight' => 'IE8 and below CSS code',
                        'seven' => 'IE7 and below CSS code');
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
                    <div class="heading link explain-link">About this feature</div>

                    <div class="full-about">
                        <p>Microthemer gives you the flexibility to export your current work to a design pack
                            for later use (you can <span class="link show-dialog" rel="import-from-pack">import</span> it back). Microthemer will create a directory on your server in
                            <code>/wp-content/micro-themes/</code> which will be used to store your settings and
                            background images. Your folders, selectors, and hand-coded css settings are saved to a
                            configuration file in this directory called config.json.</p>
                        <p><b>You may want to make use of this feature for the following reasons:</b></p>
                        <ul>
                            <li>To make extra sure that your work is backed up
                                (even though there is an automatic revision restore feature). After exporting your
                                work to a design pack you can also download it as a zip package for extra reassurance.
                                You can do this from the "<span class="link show-dialog" rel="manage-design-packs">Manage Design Packs</span>" window.</li>
                            <li>To save your current work but then start a fresh
                                (using the "reset" option in the left-hand menu)</li>
                            <li>To save one aspect of your design for reuse in other projects (e.g. styling for a menu).
                                You can do this by organising the styles you plan to reuse into a folder and
                                then export only that folder to a design pack by unchecking the other
                                folders before clicking the "Export" button.</li>
                            <li>To submit a design pack for sale or free download on
                                <a target="_blank" href="http://themeover.com/">themeover.com</a></li>
                        </ul>
                    </div>

                </div>

            </div>
            <?php echo $this->end_dialog('Export', 'span', 'export-dialog-button'); ?>
            </form>


            <!-- View CSS -->
            <?php echo $this->start_dialog('display-css-code', 'View the CSS code Microthemer generates', 'small-dialog'); ?>

            <div class="content-main">
                <span id="view-css-trigger" rel="display-css-code"></span>
                <pre id="generated-css"></pre>
                <div class="explain">
                    <div class="heading link explain-link">About this feature</div>

                    <div class="full-about">
                        <p>What you see above is the CSS code Microthemer is currently generating.
                            This can sometimes be useful for debugging issues if you know CSS. Or if you want to reuse
                            the code Microthemer generates elsewhere.
                        </p>
                        <p><b>Did you know</b> - it's possible to disable or completely uninstall Microthemer and
                            still use the customisations. You just need to paste a small piece of code in your theme's
                            functions.php file. See this
                            <a target="_blank"
                               href="http://themeover.com/forum/topic/microthemer-customizations-when-deactived/">forum post</a>
                            for further information.</p>
                        <p><b>Also note</b>, Microthemer adds the "!important" declaration to all CSS styles by default.
                            If you're up to speed on <a target="_blank"
                                                        href="http://themeover.com/beginners-guide-to-understanding-css-specificity/">CSS specificity</a> you may want to disable this behaviour on
                            the <span class="link show-dialog" rel="display-preferences">preferences page</span>. If so, you will
                            still be able to apply "!important" declarations on a per style basis by clicking the faint "i"s
                            that will appear to the right of all
                            style option fields.</p>
                    </div>

                </div>
            </div>
            <?php echo $this->end_dialog('Close', 'span', 'close-dialog'); ?>

            <!-- Restore Settings -->
            <?php echo $this->start_dialog('display-revisions', 'Restore settings from a previous save point', 'small-dialog'); ?>

            <div class="content-main">
                <div id='revisions'>
                    <div id='revision-area'></div>
                </div>
                <span id="view-revisions-trigger" rel="display-revisions"></span>
                <div class="explain">
                    <div class="heading link explain-link">About this feature</div>
                    <div class="full-about">
                        <p>Click the "restore" link in the right hand column of the table to restore your workspace settings
                            to a previous save point.</p>
                    </div>
                </div>
            </div>
            <?php echo $this->end_dialog('Close', 'span', 'close-dialog'); ?>

            <!-- Spread the word -->
            <?php echo $this->start_dialog('display-share', 'Show off your new discovery', 'small-dialog'); ?>
            <div class="content-main">
                <div class="explain">
                    <div class="heading link explain-link">About this feature</div>
                    <div class="full-about">
                        <p>cash back feature - coupon code to give new customer, affiliate commission for existing</p>
                        <p>For now, just a simply share widget.</p>
                    </div>
                </div>
            </div>
            <?php echo $this->end_dialog('Close', 'span', 'close-dialog'); ?>

        </div>

        <!-- Manage Design Packs -->
        <?php echo $this->start_dialog('manage-design-packs', 'Install & Manage Design Packs'); ?>
        <iframe id="manage_iframe" class="microthemer-iframe" frameborder="0" name="manage_iframe"
                rel="<?php echo 'admin.php?page='.$this->microthemespage; ?>"
                src="<?php echo $this->thispluginurl; ?>includes/place-holder2.html"
                data-frame-loaded="0"></iframe>
        <?php echo $this->end_dialog('Close', 'span', 'close-dialog'); ?>

        <!-- Program Docs -->
        <?php echo $this->start_dialog('program-docs', 'Help Centre'); ?>
        <iframe id="docs_iframe" class="microthemer-iframe" frameborder="0" name="docs_iframe"
                rel="http://themeover.com/support/"
                src="<?php echo $this->thispluginurl; ?>includes/place-holder2.html"
                data-frame-loaded="0"></iframe>
        <?php echo $this->end_dialog('Close', 'span', 'close-dialog'); ?>
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
            $short = 'Error saving settings';
            $long = '
            <p>Please <span id="email-error" class="link">click this link to email an error report to Themeover</span>.
            The error report sends us information about your current Microthemer settings, server and browser information,
            and your WP admin email address. We use this information purely for replicating your issue and then
            contacting you with a solution.</p>
            <p><b>Note:</b> reloading the page is normally a quick fix for now.
            However, unsaved changes will need to be redone.</p>';
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
            echo $this->media_query_tabs('selector_section', 'selector_css', 'property_group', '', true);
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