<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}
/*** 
pre content php 
***/

$append_time = '?'.time(); // to prevent file caching

// get the theme from the url or fall back to theme_in_focus
if (!empty($_GET['design_pack'])) {
    $this->preferences['theme_in_focus'] = $pref_array['theme_in_focus'] = htmlentities($_GET['design_pack']);
    $this->savePreferences($pref_array);
}

// if nothing installed yet, exit
if (empty($this->preferences['theme_in_focus'])) {
    ?>
    <p>Please install your first design pack on the
        <a href="admin.php?page=<?php echo $this->microthemespage; ?>">manage design packs page</a></p>
    <?php
    die();
}

// get meta info
$meta_info = $this->read_meta_file($this->micro_root_dir . $this->preferences['theme_in_focus'] . '/meta.txt');
// Correct if $meta_info['Author'] == Anonymous
if ($meta_info['Author'] == 'Anonymous') {
    $meta_info['Author'] = '';
}

// get readme data
$readme_info = $this->read_readme_file($this->micro_root_dir . $this->preferences['theme_in_focus'] . '/readme.txt');

// json file
$json_config_file = $this->micro_root_dir . $this->preferences['theme_in_focus'] . '/config.json'

?>
<div id="tvr" class='wrap tvr-wrap tvr-manage tvr-manage-single'>
    <?php echo $this->manage_packs_header($this->managesinglepage); ?>
	<div id='tvr-manage-single'>

        <?php
        // ajax loaders, meta spans, logs tmpl
        $this->hidden_ajax_loaders();
        $this->manage_packs_meta();
        ?>

        <p><a class="no-line" href="<?php echo 'admin.php?page='. $this->microthemespage;?>">&laquo; Back to all packs</a></p>

        <div id="full-logs">
            <?php echo $this->display_log(); ?>
        </div>

        <div id='pack-summary'>
            <?php
            // screenshot
            $screenshot = $this->thispluginurl . 'images/screenshot-placeholder.gif';
            foreach ( array('png', 'gif', 'jpg', 'jpeg') as $ext ) {
                if (file_exists($this->micro_root_dir . $this->preferences['theme_in_focus'].'/screenshot.'.$ext)) {
                    $screenshot = $this->micro_root_url . $this->preferences['theme_in_focus'].'/screenshot.'.$ext;
                    break;
                }
            }
            ?>
            <div id='micro-screenshot'>
                <?php $src = $screenshot . $append_time; ?>
                <img class="color-img" src='<?php echo $src; ?>'  />
            </div>


            <div class="summary-text">
                <?php
                $h2 = '<div class="title-text">' . $this->readable_name($this->preferences['theme_in_focus']);
                // version
                if (!empty($meta_info['Version'])) {
                    $h2.=' <span class="version">'.$meta_info['Version'] . '</span>';
                }
                $h2.= '</div>';
                // author
                if (!empty($meta_info['Author'])) {
                    $h2.= '<p class="author-name"> by '. strip_tags($meta_info['Author']) . '</p>';
                } else {
                    $h2.= '<br />';
                }
                echo $h2;
                if ($meta_info['Description']){
                    ?>
                    <div class="heading">Description</div>
                    <p><?php echo $meta_info['Description']; ?></p>
                    <?php
                } else {
                    ?>
                    <div class="heading">No Description Available</div>
                    <p>Optionally enter a description using the "Info" form below.</p>
                    <?php
                }

                if (count($meta_info['Tags']) > 0) {
                    ?>
                    <div class="heading">Tags</div>
                    <p class="display-micro-tags"><?php echo implode(', ', $meta_info['Tags']); ?></p>
                    <?php
                }
                ?>

                <ul id='main-theme-actions' class='main-theme-actions'>
                    <?php
                    // activate / deactivate
                    if ($this->preferences['active_theme'] == $this->preferences['theme_in_focus']) {
                        $act_text = 'Deactivate';
                        $act_param = 'tvr_deactivate_micro_theme';
                        $nonce = 'tvr_deactivate_micro_theme';
                    }
                    else {
                        $act_text = 'Activate';
                        $act_param = 'tvr_activate_micro_theme';
                        $nonce = 'tvr_activate_micro_theme';
                    }
                    // Having this option probably just creates confusion for Microthemer - just have for Microloader
                    if (TVR_MICRO_VARIANT == 'loader') {
                        ?>
                        <a href='admin.php?page=<?php echo $this->microthemespage;?>&action=<?php echo $act_param; ?>&_wpnonce=<?php echo wp_create_nonce($nonce); ?>'
                           class='tvr-button'><?php echo  $act_text; ?></a> |
                    <?php
                    } else {
                        $action_buttons = array(
                            'info',
                            'attachments',
                            'instructions',
                            'download',
                            'delete'
                        );
                        foreach ($action_buttons as $button){
                           if ($button == 'info'){
                               $class = 'on';
                           } else {
                               $class = '';
                           }
                           ?>
                           <li class="pack-action <?php echo $class; ?> pack-action-<?php echo $button; ?>"
                               rel="<?php echo $button; ?>" data-dir-name="<?php echo $this->preferences['theme_in_focus']; ?>">
                               <span class="pack-icon icon tvr-icon"></span>
                               <span class="action-text"><?php echo $button; ?></span>
                           </li>
                           <?php
                        }
                    }
                    ?>
                </ul>

            </div>

            <?php
            if ($external_image_notice) {
                echo "<span id='external-notice' rel='warn'></span>";
            }
			?>  
        </div>
        
        <?php
		// only show meta data editing options and files table if microthemer
        if (TVR_MICRO_VARIANT == 'themer') {
        ?>
        <div id="single-content-areas">
            <div id='edit-info' class='manage-content-wrap hidden show'>
                <div class="explain">
                    <div class="heading">About This Feature</div>
                    <p>If you plan to make your design pack available for sale or free download on themeover.com, please fill out
                        all of the form fields on the right. Hover your mouse over the field labels to view instructions
                        for each field. You will also need to upload a thumbnail image on the
                        <span class="pack-action link" rel="attachments">Attachments tab</span> and (optionally) some
                        instructions for the end user on the
                        <span class="pack-action link" rel="instructions">Instructions tab</span>
                    </p>
                    <p>The information you enter will update the meta.txt files in your design pack. Themeover will make use
                    of this information if you submit your design pack for inclusion in our marketplace.</p>
                    <p>It's not necessary to fill out these fields if don't wish to share your design pack.
                        But you may wish to upload a thumbnail image to replace the "No Screenshot" placeholder on the
                        <span class="pack-action link" rel="attachments">Attachments tab</span>.</p>
                </div>
                <div class="heading">Design Pack Meta Info</div>
                <form name='edit_meta_form' id="edit-meta-form" method="post" class='float-form' autocomplete="off"
                      action="admin.php?page=<?php echo $this->managesinglepage;?>" >
                    <?php wp_nonce_field('tvr_edit_meta_submit'); ?>
                    <p class="combobox-wrap input-wrap">
                        <label>Type of Design Pack: </label>
                        <input type="text" class="combobox" id="type_of_pack" name="theme_meta[PackType]" rel="packTypes"
                               value="<?php echo $meta_info['PackType'];?>" />
                        <span class="combo-arrow"></span>
                    </p>
                    <p><label>Design Pack Name: </label>
                        <input type='text' autocomplete="off" id='micro-name' name='theme_meta[Name]' value='<?php echo $meta_info['Name'];?>'  maxlength='40' />
                        <input type='hidden' id='prev-micro-name' name='prev_micro_name' value='<?php echo $meta_info['Name'];?>' />
                    </p>

                    <p><label>&nbsp;</label><span class='tipbit'>allowed characters: a-z, A-Z, 0-9, -, _</span></p>
                    <p><label>Version: </label>
                        <input type='text' autocomplete="off" name='theme_meta[Version]' value='<?php echo $meta_info['Version'];?>' />
                    </p>
                    <!--<p><label>Design Pack URI: </label>
                        <input type='text' autocomplete="off" name='theme_meta[URI]' value='<?php echo $meta_info['URI'];?>' />
                    </p>-->
                    <p><label>Description: </label>
                        <textarea name='theme_meta[Description]' autocomplete="off"><?php echo $meta_info['Description'];?></textarea>
                    </p>
                    <p><label>Author: </label>
                        <input type='text' autocomplete="off" name='theme_meta[Author]' value='<?php echo strip_tags($meta_info['Author']);?>' />
                    </p>
                    <p><label>Author URI: </label>
                        <input type='text' autocomplete="off" name='theme_meta[AuthorURI]' value='<?php echo $meta_info['AuthorURI'];?>' />
                    </p>
                    <p><label>Target Theme or Plugin: </label>
                        <input type='text' name='theme_meta[Template]' value='<?php echo $meta_info['Template'];?>' />
                    </p>

                    <p><label>Tags: </label>
                        <textarea name='theme_meta[Tags]'><?php
                            // this will only be set if the meta file exits (it won't exist if the user created the theme dir manually)
                            if (count($meta_info['Tags']) > 0) {
                                echo implode(', ', $meta_info['Tags']);
                            }
                            ?></textarea>
                    </p>
                    <p><label>&nbsp;</label>
                        <input class="tvr-button" type="submit" name="tvr_edit_meta_submit" value="Update Info" />
                    </p>
                </form>

            </div>

            <div id='edit-attachments' class='manage-content-wrap hidden'>
                <div class="explain">
                    <div class="heading">About This Feature</div>
                    <p>Upload a screenshot image file 896px wide and 513px tall called "screenshot.gif/jpg/png"
                        to give your design pack a nice thumbnail. It's important to upload a full-size
                        thumbnail in the dimensions 896 x 513 or larger. Microthemer will crop your image to 896 x 513
                        if you upload a larger image. It will also
                        automatically create a smaller version called screenshot-small.gif/jpg/png.</p>
                    <p><b>Important Note:</b> As of version 3 Microthemer now uses the WordPress media manager for
                        storing background images. It's  therefore not necessary or advised to upload background images here.
                        It's much better to upload them from the Microthemer UI page, when you select a background
                        image for inclusion in your design.</p>
                    <p>Any images your design pack links to will be included in your design pack if you choose to
                        download it as a zip file. When you install a design pack zip file, all the images will be copied to
                        the WordPress media library. Image file paths will be updated accordingly.</p>
                </div>
                <div class="heading">Design Pack Files</div>
                <table id='micro-files-table' cellspacing="0">
                    <thead>
                    <tr>
                        <th scope="col" colspan="3" class="manage-column image-upload">

                            <form name='upload_file_form' id="upload-file-form" method="post" enctype="multipart/form-data"
                                  action="<?php echo $this->page_prefix; ?>.php?page=<?php echo $this->managesinglepage;?>" autocomplete="off">
                                <?php wp_nonce_field('tvr_upload_file_submit'); ?>
                                <input type="file" name="upload_file" />
                                <input class="tvr-button" type="submit" name= "tvr_upload_file_submit" value="Upload File" />
                            </form>
                            <span class="design-packs-table-heading">Files</span>

                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    // loop through files
                    $combined_files = array();
                    if (is_array($this->file_structure[$this->preferences['theme_in_focus']])) {
                        sort($this->file_structure[$this->preferences['theme_in_focus']]);
                        $i = 0;
                        foreach ($this->file_structure[$this->preferences['theme_in_focus']] as $key => $file) {
                            ++$i;
                            $file_url = $this->micro_root_url . $this->preferences['theme_in_focus'] . '/' . $file;
                            $combined_files[$i]['location'] = 'pack';
                            $combined_files[$i]['file_url'] = $file_url;
                            $combined_files[$i]['display_url'] = $this->root_rel($this->micro_root_url . $this->preferences['theme_in_focus'], false, true) . '/' . '<span class="base-file">'.$file.'</span>';
                        }
                        // add any media library images to the array
                        if ($library_images = $this->get_linked_library_images($json_config_file )) {
                            foreach ($library_images as $key => $file_url) {
                                // check if file exists
                                //$file_path = str_replace($this->wp_content_url, $this->wp_content_dir, $file_url);
                                if (!file_exists(ABSPATH . $file_url)){
                                    continue;
                                }
                                echo ABSPATH . $file_url;
                                ++$i;
                                $file = basename($file_url);
                                $combined_files[$i]['location'] = 'library';
                                $combined_files[$i]['file_url'] = $file_url;
                                $combined_files[$i]['display_url'] = str_replace($file, '', $file_url)
                                    . '<span class="base-file">'.$file.'</span>';
                            }
                        }
                        $library = false;
                        foreach ($combined_files as $key => $array) {
                            if ($this->is_image($array['file_url'])) {
                                $ext = 'image';
                            }
                            else {
                                $ext = $this->get_extension($array['file_url']);
                            }
                            // heading for library images
                            if ($array['location'] == 'library' and !$library){
                                $library = true;
                                ?>
                                <tr>
                                    <td scope="col" colspan="3" class="library-heading heading">Media library images this design pack incorporates</td>
                               </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td class="file-name">
                                    <?php echo $array['display_url']; ?>
                                </td>
                                <td>
                                    <span class='show-dialog tvr-icon view-icon'
                                          data-ext="<?php echo $ext; ?>"
                                          rel="view-pack-file"
                                          data-base-name="<?php echo basename($array['file_url']); ?>"
                                          data-href="<?php echo $array['file_url'] . $append_time;?>"
                                       >

                                       </span>
                                </td>
                                <td>
                                    <span class='delete-file view-file tvr-icon delete-icon'
                                          data-href='admin.php?page=<?php echo
                                    $this->managesinglepage;?>&action=tvr_delete_micro_file&file=<?php
                                    echo $this->site_url . $array['file_url']; ?>&location=<?php
                                    echo $array['location']; ?>&_wpnonce=<?php echo wp_create_nonce('tvr_delete_micro_file'); ?>'>
                                    </span>
                                </td>
                            </tr>
                        <?php
                        }
                    }

                    ?>
                    </tbody>
                </table>

                <div class='file-types'>
                    <p>File types you are allowed to upload:</p>
                    <?php
                    $acceptable = $this->get_acceptable();
                    foreach ($acceptable as $ext) {
                        echo '<img src="'.$this->thispluginurl.'images/ext-'.$ext.'.gif" width="50" height="50" />';
                    }
                    ?>
                </div>
            </div>

            <div id='edit-instructions' class='manage-content-wrap hidden'>
                <div class="explain">
                    <div class="heading">About This Feature</div>
                    <p>If you're going to submit your design pack to Themeover, adding some instructions to help end users
                    customize your design pack is a really nice touch. This could include pointing out potential pitfalls
                    or recommending best practices. The instructions you add will feature on Themeover's listing page.</p>
                    <p>Some design packs may not benefit much from additional instructions of course.
                        In which case feel free to leave this blank.</p>
                </div>
                <div class="heading">Instructions For The End User</div>

                <form name='edit_readme_form' id="edit-readme-form" method="post" class='float-form' autocomplete="off"
                      action="admin.php?page=<?php echo $this->managesinglepage;?>" >
                    <?php wp_nonce_field('tvr_edit_readme_submit'); ?>
                    <textarea name='tvr_theme_readme' autocomplete="off"><?php echo htmlentities($readme_info);?></textarea>
                    <p class="button-wrap">
                        <input class="tvr-button" type="submit" name="tvr_edit_readme_submit" value="Update Instructions" />
                    </p>
                </form>
            </div>

        </div>
        <?php 
		} // ends 'if themer'
		?>

        <!-- View file dialog -->
        <?php echo $this->start_dialog('view-pack-file', 'Design Pack File', 'sidebar'); ?>
        <div class="explain">
            <div class="heading">About this file</div>
            <div class="explain-config">
                <p>This is the configuration file which contains all of the Microthemer settings for this design pack.
                   It is created automatically when you export your work. When you import a design pack into the Microthemer                    UI the style settings, media queries, and any custom CSS code are loaded from this file.</p>
                <p>You're not likely to want to manually edit this file. But occasionally programmers download it,
                    do some find and replace adjustments, and then re-upload it using the "Upload File" button
                    at the top of the design pack files table.</p>
            </div>
            <div class="explain-meta">
                <p>This is the information file which contains meta information about design pack.
                    Themeover uses the information in this file to list a user-submitted design pack on themeover.com.
                You can edit this file using the "Design Pack Meta Info" form.</p>
            </div>
            <div class="explain-instructions">
                <p>This is the readme file which contains instructions for the end user of a design pack - if the author
                    decided that instructions would be beneficial.
                    You can edit this file using the "Instructions" form.</p>
            </div>
        </div>
        <div class="content-main">
            <textarea id="pack-file-content"></textarea>
        </div>
        <?php echo $this->end_dialog('Close', 'span', 'close-dialog'); ?>

   </div><!-- end tvr-manage -->

</div><!-- end wrap -->