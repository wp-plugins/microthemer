<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}

// the default readme.txt
$_POST['tvr_theme_readme'] = '=== Theme Instructions ===
MICRO THEME INSTALLATION INSTRUCTIONS:
http://www.themeover.com/install-a-micro-theme-with-microthemer-or-microloader/
FIND MORE STEP BY STEP TUTORIALS AT: 
http://www.themeover.com/support/microthemer-tutorials/
GET SUPPORT AT: 
http://www.themeover.com/forum/


== Contents ==
1. 


== Changelog ==
This is version 1.0. There have been no changes.';
?>