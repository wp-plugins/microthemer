<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}
?>

<!-- Edit Preferences -->
            <form id="tvr-preferences" name='preferences_form' method="post" autocomplete="off"
                  action="admin.php?page=<?php echo $page_context;?>" >
                <?php wp_nonce_field('tvr_preferences_submit'); ?>
<?php echo $this->start_dialog('display-preferences', 'Edit your Microthemer preferences', 'small-dialog'); ?>

<div class="content-main">
    <ul class="form-field-list">
        <?php
        // yes no options
        $yes_no = array(
            'first_and_last' => array(
                'label' => 'Add "first" and "last" classes to menu items',
                'explain' => 'Microthemer can insert "first" and "last" classes on WordPress menus so that you can style the first or last menu items a bit differently from the rest. Note: this only works with "Custom Menus" created on the  Appearance > Menus page.'
            ),

            'gzip' => array(
                'label' => 'Gzip the Microthemer UI page for faster loading',
                'explain' => 'The Microthemer interface generates quite a bit of HTML. Having this gzip option enabled will speed up the initial page loading.'
            ),
            'ie_notice' => array(
                'label' => 'Display Internet Explorer notice',
                'explain' => 'Microthemer can be a bit clunky when run in Internet Explorer. And it just plain doesn\'t work in old versions of IE like IE8. But if you insist on using IE you might want to disable the notice telling you to use Firefox or Chrome instead.'
            ),
            'safe_mode_notice' => array(
                'label' => 'Display PHP safe mode warnings',
                'explain' => 'If your server has the PHP settings "safe mode" on, you may encounter permission errors when Microthemer tries to create directories. Microthemer will alert you to safe-mode being on. But if that\'s old news to you, you may want to disable the warnings about it.'
            ),
            'css_important' => array(
                'label' => 'Always add !important to CSS styles',
                'label_no' => '(configure manually)',
                'explain' => 'Always add the "!important" CSS declaration to CSS styles.This largely solves the issue of having to understand how CSS specificity works. But if you prefer, you can disable this functionality and still apply "!important" on a per style basis by clicking the faint "i\'s" that will appear to the right of every style input.'
            ),
            'pie_by_default' => array(
                'label' => 'Always use CSS3 PIE pollyfill where relevant',
                'label_no' => '(configure manually)',
                'explain' => 'Always include to the CSS3 PIE htc file if gradients, rounded corners, or box-shadow have been applied to make them work in Internet Explorer 6-8. Note: a "position" value of "relative" will automatically be applied to the selector unless you explicitly set the position property to "static" or some other value. Set this option to "No" if you would like to enable this pollyfill on a per selector basis.'
            )
            /*'boxsizing_by_default' => array(
                'label' => 'Always use the box-sizing pollyfill where relevant',
                'label_no' => '(configure manually)',
                'explain' => 'Always include to the box-sizing htc file if the box-sizing property has been applied to make it work in Internet Explorer 6 and 7.
Set this option to "No" if you would like to enable this pollyfill on a per selector basis.
Note: this pollyfill doesn\'t work on text inputs.'
            )*/
        );
        // text options
        $text_input = array(
            'preview_url' => array(
                'label' => 'Frontend preview URL Microthemer should load',
                'explain' => 'Manually specify a link to the page you would like Microthemer to load for editing when it first starts. By default Microthemer will load your home page or the last page you visited. This option is useful if you want to style a page that can\'t be navigated to from the home page or other pages.'
            ),
            'gfont_subset' => array(
                'label' => 'Google Font subset URL parameter',
                'explain' => 'You can instruct Google Fonts to include a font subset by entering an URL parameter here. For example "&subset=latin,latin-ext" (without the quotes). Note: Microthemer only generates a Google Font URL if it detects that you have applied Google Fonts in your design.'
            )
        );
        foreach ($yes_no as $key => $array) {
            if (empty($array['label_no'])){
                $array['label_no'] = '';
            }
            ?>
            <li class="fake-radio-parent">
                <label title="<?php echo htmlentities($array['explain']); ?>"><?php echo $array['label']; ?>:</label>

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
                                <span class="ef-label">Yes</span>
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
                                    <span class="ef-label">No <?php echo $array['label_no']; ?></span>
                            </span>
            </li>
        <?php
        }
        foreach ($text_input as $key => $array) {
            ?>
            <li>
                <label title="<?php echo htmlentities($array['explain']); ?>" class="text-label">
                    <?php echo $array['label']; ?>:</label>
                <input type='text' autocomplete="off" name='tvr_preferences[<?php echo $key; ?>]'
                       value='<?php echo esc_attr($this->preferences[$key]); ?>' />

            </li>
        <?php
        }
        ?>
    </ul>
    <?php echo $this->dialog_button('Save Preferences', 'input', 'save-preferences'); ?>

    <div class="explain">
        <div class="heading link explain-link">About this feature</div>

        <div class="full-about">
            <p>Set your global Microthemer preferences on this page. Hover your mouse over the option labels for further information on each option.</p>
            <?php
            if (!$this->preferences['buyer_validated']){
                ?>
                <p>Please unlock the full program by entering your PayPal email address on
                    <span class="link show-dialog" rel="unlock-microthemer">this page</span>.</p>
            <?php
            }
            ?>
            <p>You may also wan to customise the default media queries Microthemer suggests on
                <span class="link show-dialog" rel="edit-media-queries">this page</span>.</p>
        </div>
    </div>
</div>
</form>
<?php echo $this->end_dialog('Save Preferences', 'input', 'save-preferences'); ?>
   
