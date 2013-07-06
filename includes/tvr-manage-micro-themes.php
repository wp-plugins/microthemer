<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}
/*** 
pre content php 
***/

$append_time = '?'.time(); // to prevent file caching

// get meta info from meta.txt/readme.txt if a theme has been selected
if ($this->preferences['theme_in_focus'] != '') { 
	$meta_info = $this->read_meta_file($this->micro_root_dir . $this->preferences['theme_in_focus'] . '/meta.txt');
	// Correct if $meta_info['Author'] == Anonymous
	if ($meta_info['Author'] == 'Anonymous') {
		$meta_info['Author'] = '';
	}
	// get readme data
	$readme_info = $this->read_readme_file($this->micro_root_dir . $this->preferences['theme_in_focus'] . '/readme.txt');
}

// get micro-themes dir file structure - unset $this->file_structure so it can be populated with fresh scan
unset($this->file_structure);
$file_structure = $this->dir_loop($this->micro_root_dir);

// check config.json for external images (microlthemer only)
if (TVR_MICRO_VARIANT == 'themer') {
	$json_file = $this->micro_root_dir . $this->preferences['theme_in_focus'] . '/config.json';
	if ($this->check_image_paths($json_file, $this->preferences['theme_in_focus'], 'notify')) {
		$external_image_notice = true;
	}
}

	
?>
<div class='wrap'>
	<div id='tvr-manage'>
    	<?php
		// show help message
		$this->need_help_notice();
		// show validation reminder
		$this->validate_reminder();
		// ie notice
		$this->ie_notice();
		?>
        
		<?php 
		// display message
		if ($this->globalmessage != '') {
			echo '<div id="microthemer-notice">' . $this->globalmessage . '</div>'; 
		}
		?>
       <div id='top-manage' class='context-<?php echo TVR_MICRO_VARIANT;?>'>  
            <h3>Manage Settings Packs</h3>
            
            <?php
			// slightly different layout for themer and loader
			if (TVR_MICRO_VARIANT == 'themer') {
				?>
				<!--<a href='#' class='reveal-add-micro prominent-action'>Create New Theme</a>-->
            	<a href='#' class='reveal-upload-micro prominent-action'>+ Install New Settings Pack</a>
                <?php
			}
			else {
				?>
                <div id='install-wrap'>
                <form name='upload_micro_form' id="upload-micro-form" method="post" enctype="multipart/form-data" 
                action="<?php echo $this->page_prefix; ?>.php?page=<?php echo $this->microthemespage;?>" >
                <?php wp_nonce_field('tvr_upload_micro_submit'); ?>
                <input type="file" name="upload_micro" />
                <input class="button-primary" type="submit" name= "tvr_upload_micro_submit" value="Install Theme" />
                </form>
                </div>
                <?php
			}
            
            ?>
            <form name='edit_micro_form' id="edit-micro-form" method="post" autocomplete="off"
            action="<?php echo $this->page_prefix; ?>.php?page=<?php echo $this->microthemespage;?>" >
            <?php wp_nonce_field('tvr_edit_micro_submit'); ?>
            <select name='preferences[theme_in_focus]' class='heading-input' autocomplete="off">
            	<?php
				// 'edit' with microloader 'manage' with microthemer 
				if (TVR_MICRO_VARIANT == 'themer') { 
					$word = 'Edit';
				}
				else {
					$word = 'Manage';
				}
				?>
                <option value=''>Select Theme to <?php echo $word;?></option>
                <?php
				if (is_array($file_structure)) {
					foreach ($file_structure as $dir => $array) {
						// determine checked
						if ( $this->preferences['theme_in_focus'] == $dir) {
							$selected = 'selected="selected"';
						}
						else {
							$selected = '';
						}
						// under some conditions a blank value can crop up. Not sure what the cause is but this fixes problem.
						if ($dir != '') {
							echo '<option value="'.$dir.'" '.$selected.'>'.$this->readable_name($dir).'</option>';
						}
					}
				}
                ?>
            </select>
            <input class="button-primary heading-input go-button" type="submit" name="tvr_edit_micro_submit" value="Go" />
            </form>
            
            <div id='add-micro' class='hidden-manage'>
			<form name='add_micro_form' id="add-micro-form" method="post" autocomplete="off" 
            action="<?php echo $this->page_prefix; ?>.php?page=<?php echo $this->microthemespage;?>" >
            <?php wp_nonce_field('tvr_create_micro_submit'); ?>
			<p><label>Micro Theme Name: </label>
            <input type='text' name='micro_name' value='' autocomplete="off" maxlength='19' />
            <input class="button-primary" type="submit" name="tvr_create_micro_submit" value="Create New" />
            <br />
            <span class='tipbit'>allowed characters: a-z, A-Z, 0-9, -, _</span>
            </p>
            </form>
            </div>

            <div id='upload-micro' class='hidden-manage'>
            <form name='upload_micro_form' id="upload-micro-form" method="post" enctype="multipart/form-data" 
            action="<?php echo $this->page_prefix; ?>.php?page=<?php echo $this->microthemespage;?>" >
            <?php wp_nonce_field('tvr_upload_micro_submit'); ?>
			<p><label>Select Zip File: </label>
            <input type="file" name="upload_micro" />
            <input class="button-primary" type="submit" name= "tvr_upload_micro_submit" value="Upload"/>
            <br />
            <span class='tipbit'>your server upload limit is: <?php echo ini_get('upload_max_filesize');?>B</span>
            </p>
            </form>
            </div>
       </div>

       	<?php 
		// if a micro theme has been selected for editing
		if ($this->preferences['theme_in_focus'] != '') { 
		?>
        <div class='manage-input-wrap'>       
			<?php
            // display information about the currently selected micro theme
            ?>       
            <h2><?php 
			$h2 = $this->readable_name($this->preferences['theme_in_focus']); // $meta_info['Name']; is wrong if the theme was auto-renamed to name-1
			// version
			if ($meta_info['Version'] != '') {
				$h2.=' '.$meta_info['Version'];
			}
			// author
			if ($meta_info['Author'] != '') {
				$h2.=' by '.$meta_info['Author'];
			}
			echo $h2;
			?>
            </h2>
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
            <img src='<?php echo $screenshot . $append_time; ?>' id='micro-screenshot' width='220' />
            
            <div id='main-theme-actions' class='main-theme-actions'>
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
            ?>
            <!-- Having this option probably just creates confusion
            <a href='admin.php?page=<?php echo $this->microthemespage;?>&action=<?php echo $act_param; ?>&_wpnonce=<?php echo wp_create_nonce($nonce); ?>' 
            class='ac-deac-micro'><?php echo  $act_text; ?></a>
             | -->
            <a class='delete-micro' href='admin.php?page=<?php echo $this->microthemespage;?>&action=tvr_delete_micro_theme&_wpnonce=<?php echo wp_create_nonce('tvr_delete_micro_theme'); ?>'>
                Delete</a> 
            <?php 
			// edit theme styles link for microthemer only
			if ($this->preferences['active_theme'] == $this->preferences['theme_in_focus'] and TVR_MICRO_VARIANT == 'themer') {
				echo "<!--| <a href='admin.php?page=".$this->microthemeruipage."' class='edit-theme-styles'>Edit Theme Styles</a>-->"; 
			}
			?>
			 
            </div>
            
            <?php 
			// description
			echo '<p><i>'.$meta_info['Description'].'</i></p>';
			// parent 
			if ($meta_info['Template'] != '') {
				// echo '<p><b>'.$this->readable_name($this->preferences['theme_in_focus']).'</b> is a Micro Theme for <b>'.$meta_info['Template'].'</b>.</p>';
			}
			// Tags 
			if (count($meta_info['Tags']) > 0) {
				echo '<p class="display-micro-tags"><b>Tags:</b> '.implode(', ', $meta_info['Tags']).'</p>';
			}
			?>  
        </div>
        
        <?php
		// only show meta data editting options and files table if microthemer
        if (TVR_MICRO_VARIANT == 'themer') {
        ?>
        <div class='manage-input-wrap edit-meta-wrap'>  
        	<a href='#' class='reveal-edit-meta prominent-action'>Edit Meta Info</a>
            <div id='edit-meta' class='hidden-manage'>
			<form name='edit_meta_form' id="edit-meta-form" method="post" class='float-form' autocomplete="off"
            action="<?php echo $this->page_prefix; ?>.php?page=<?php echo $this->microthemespage;?>" >
            <?php wp_nonce_field('tvr_edit_meta_submit'); ?>
			<p><label>Theme Name: </label>
            <input type='text' autocomplete="off" id='micro-name' name='theme_meta[Name]' value='<?php echo $meta_info['Name'];?>'  maxlength='19' />
            <input type='hidden' id='prev-micro-name' name='prev_micro_name' value='<?php echo $meta_info['Name'];?>' />
            </p>
            <p><label>&nbsp;</label><span class='tipbit'>allowed characters: a-z, A-Z, 0-9, -, _</span></p>
            <p><label>Theme URI: </label>
            <input type='text' autocomplete="off" name='theme_meta[URI]' value='<?php echo $meta_info['URI'];?>' />
            </p>
            <p><label>Description: </label>
            <textarea name='theme_meta[Description]' autocomplete="off"><?php echo $meta_info['Description'];?></textarea>
            </p>
            <p><label>Author: </label>
            <input type='text' autocomplete="off" name='theme_meta[Author]' value='<?php echo strip_tags($meta_info['Author']);?>' />
            </p>
            <p><label>Author URI: </label>
            <input type='text' autocomplete="off" name='theme_meta[AuthorURI]' value='<?php echo $meta_info['AuthorURI'];?>' />
            </p>
            <p><label>Template: </label>
            <input type='text' name='theme_meta[Template]' value='<?php echo $meta_info['Template'];?>' />
            </p>
            <p><label>Version: </label>
            <input type='text' autocomplete="off" name='theme_meta[Version]' value='<?php echo $meta_info['Version'];?>' />
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
            <input class="button-primary" type="submit" name="tvr_edit_meta_submit" value="Update" />
            </p>
            </form>
            </div>
        </div>
        
        <div class='manage-input-wrap edit-readme-wrap'>  
        	<a href='#' class='reveal-edit-readme prominent-action'>Edit readme.txt</a>
            <div id='edit-readme' class='hidden-manage'>
			<form name='edit_readme_form' id="edit-readme-form" method="post" class='float-form' autocomplete="off"
            action="<?php echo $this->page_prefix; ?>.php?page=<?php echo $this->microthemespage;?>" >
            <?php wp_nonce_field('tvr_edit_readme_submit'); ?>
			
            <p><label>Edit readme.txt: </label>
            <textarea name='tvr_theme_readme' autocomplete="off"><?php echo htmlentities($readme_info);?></textarea>
            </p>
            <p><label>&nbsp;</label>
            <input class="button-primary" type="submit" name="tvr_edit_readme_submit" value="Update" />
            </p>
            </form>
            </div>
        </div>
        
        <div class='manage-input-wrap'>
        	<table id='micro-files-table' class="widefat" cellspacing="0">
			<thead>
			<tr>
				<th scope="col" colspan="3" class="manage-column image-upload">
                <h3>Theme Files</h3>
                <form name='upload_file_form' id="upload-file-form" method="post" enctype="multipart/form-data" 
                action="<?php echo $this->page_prefix; ?>.php?page=<?php echo $this->microthemespage;?>" autocomplete="off">
                <?php wp_nonce_field('tvr_upload_file_submit'); ?>
                <span id='resize-wrap'>
                <input type="checkbox" id="resize-image" class="resize-image" name="tvr_upload_file_resize" value="1" autocomplete="off" /> Resize 
                <span id='resize-dimensions'>
                <b title="Max Width">W:</b><input type="text" title="Max Width" id="resize-width" name="tvr_upload_file_width" value="" /> 
                <b title="Max Height">H:</b><input type="text" title="Max Height" id="resize-height" name="tvr_upload_file_height" value="" />
                </span>
                </span>
                <input type="file" name="upload_file" />
                <input class="prominent-action" type="submit" name= "tvr_upload_file_submit" value="Upload File" />
                </form>
                </th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th scope="col" colspan="3" class="manage-column micro-export">
                <?php
                if ($external_image_notice) {
					echo "<span id='external-notice'>warn</span>";
				}
				?>
                <a id='export-zip-button' class='export-zip-button prominent-action' href='admin.php?page=<?php echo $this->microthemespage;?>&action=tvr_export_zip_package&_wpnonce=<?php echo wp_create_nonce('tvr_export_zip_package'); ?>'>
                Export As Zipped Settings Pack</a>
                </th>
			</tr>
			</tfoot>            
			<tbody>
            <?php
			// loop through files
			if (is_array($file_structure[$this->preferences['theme_in_focus']])) {
				sort($file_structure[$this->preferences['theme_in_focus']]);
				foreach ($file_structure[$this->preferences['theme_in_focus']] as $dir => $file) {
					if ($this->is_image($file)) {
						$rel = 'rel="bg-image"';
						$target = '';
						
					}
					else {
						$rel = '';
						$target = 'target="_blank"';		
					}
					$ext = $this->get_extension($file);
				?>
                <tr>
            	<td class="file-name">
    			
                <?php echo $file; ?>
                </th>
                <td><a class='non-image-file' href='<?php echo $this->micro_root_url . $this->preferences['theme_in_focus'].'/'.$file . $append_time;?>'
                 <?php echo $rel.' '.$target; ?>>View</a></td>
                <td><a class='delete-file' href='admin.php?page=<?php echo $this->microthemespage;?>&action=tvr_delete_micro_file&file=<?php echo $file; ?>&_wpnonce=<?php echo wp_create_nonce('tvr_delete_micro_file'); ?>'>
                Delete</a></td>
            </tr>
                <?php	
				}
			}
			
			?>
			</tbody>
			</table>
        </div>
        
        <div class='manage-input-wrap ext-pics'>
        <p>File types you are allowed to upload:</p>
        <?php
		$acceptable = $this->get_acceptable();
		foreach ($acceptable as $ext) {
			echo '<img src="'.$this->thispluginurl.'images/ext-'.$ext.'.gif" width="50" height="50" />';
		}
		?>
        </div>
        
        <?php 
		} // ends 'if themer'
		?>  
        
		<?php 
		} // ends if there is a theme in focus
		?>          
   </div><!-- end tvr-manage -->
   
   <div id='manage-menu-wrap'>
   <div id='fixed-menu'>
    	<div id='fixed-menu-inner'>   
            
           <?php 
		   // get microthemer message for microloader users
		   if (TVR_MICRO_VARIANT != 'themer') {
			   ?>
               <div>
                   <h3>Microthemer</h3>
                   <div class='menu-option-wrap'>
                      <a href='http://themeover.com/microthemer/' target='_blank' class="button-primary">Get it!</a>
                   </div>
               </div>
               <?php 
		   }
		   ?>
           
           <div>
               <h3>Buy Themes</h3>
               <div class='menu-option-wrap'>
                  <a href='http://themeover.com/buy-micro-themes/' target='_blank' class="button-primary">Buy Micro Themes</a>
               </div>
           </div>
            
           <div>
               <h3>Sell Themes</h3>
               <div class='menu-option-wrap'>
                  <a href='http://themeover.com/sell-micro-themes/' target='_blank' class="button-primary">Sell Micro Themes</a>
               </div>
           </div>
            
           <div>
                <h3>Affiliate Income</h3>
                <div class='menu-option-wrap'>
                   <a href='http://themeover.com/themeover-affliates/' target='_blank' class="button-primary">Earn Revenue</a>
                </div>
            </div>
           
           <?php
		   // only microthemer needs plugin menu 
		   if (TVR_MICRO_VARIANT == 'themer') {
			   $this->plugin_menu(); 
		   }
		   ?>
              
    </div><!-- fixed-menu -->
   </div><!-- manage-menu-wrap -->
</div><!-- end wrap -->