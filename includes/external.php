<?php 
$docs_title = "External Web Design Learning Resources";
$main_class = 'external';
$h1 = "External Web Design Learning Resources";
include 'docs-header.inc.php'; 

// output list
function tvr_list_items($list) {
	?>
    <ol>
    <?php
	foreach ($list as $key => $array) {
		?>
		<li><a target="_blank" href="<?php echo $array['url']; ?>"><?php echo $array['title']; ?></a></li>
		<?php
	}
	?>
    </ol>
    <?php
}

// resources
$res[1]['title'] = 'W3 Schools HTML Tutorials';
$res[1]['url'] = 'http://www.w3schools.com/html/';
$res[2]['title'] = 'W3 Schools CSS Tutorials';
$res[2]['url'] = 'http://www.w3schools.com/css/default.asp';
$res[3]['title'] = 'WordPress Guide to Cross-browser CSS Bugs';
$res[3]['url'] = 'http://codex.wordpress.org/CSS_Fixing_Browser_Bugs';

// tools
$tools[1]['title'] = 'Firebug Browser Plugin for Firefox';
$tools[1]['url'] = 'https://getfirebug.com/';
$tools[2]['title'] = 'Firebug Lite Browser Plugin for Chrome';
$tools[2]['url'] = 'http://getfirebug.com/releases/lite/chrome/';
$tools[3]['title'] = 'Colorzilla Browser Plugin for Firefox';
$tools[3]['url'] = 'http://www.colorzilla.com/';

// books
$books[1]['title'] = 'Sitepoint - Build Your Own Website The Right Way Using HTML & CSS, 3rd Edition';
$books[1]['url'] = 'http://www.sitepoint.com/books/html3/';
$books[2]['title'] = 'Sitepoint - The CSS3 Anthology, 4th Edition';
$books[2]['url'] = 'http://www.sitepoint.com/books/cssant4/';
$books[3]['title'] = 'Sitepoint - The Principles of Beautiful Web Design Second Edition';
$books[3]['url'] = 'http://www.sitepoint.com/books/design2/';

?>
<div class='snippet content-box'>
    <h3>Online Resources</h3>
    <div class="inner-box">
		<?php
        tvr_list_items($res);
        ?>
    </div>
</div>

<div class='snippet content-box'>
    <h3>Useful Tools</h3>
    <div class="inner-box">
		<?php
        tvr_list_items($tools);
        ?>
    </div>
</div>

<div class='snippet content-box'>
    <h3>Books for Aspiring Web Designers</h3>
    <div class="inner-box">
		<?php
        tvr_list_items($books);
        ?>
    </div>
</div>


<?php include 'docs-footer.inc.php'; ?>

</body>
</html>
