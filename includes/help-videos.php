<?php 
$docs_title = "Microthemer Started Video Guides";
$main_class = 'help-videos';
$h1 = "Getting Started Video Guides";
include 'docs-header.inc.php'; 

?>
<p><b>IMPORTANT TIP</b>: Click the Full Screen icon at the bottom right of the video to see what's going on much more clearly.</p>

<?php
$youtube[1]['title'] = 'Introducing Microthemer V2';
$youtube[1]['id'] = 'kPaPM5RLshc';
$youtube[2]['title'] = 'Using Microthemer\'s Selector Wizard';
$youtube[2]['id'] = 'E_PRtRAT-Ik';
$youtube[3]['title'] = 'Using Microthemer\'s Editing Options';
$youtube[3]['id'] = 'inuRMGC1G4k';
$youtube[4]['title'] = 'Installing Microthemer Settings Packs';
$youtube[4]['id'] = 'ae7zY1E4OwU';
$youtube[5]['title'] = 'Managing Microthemer Settings Packs';
$youtube[5]['id'] = 'yCo-5B5p6oM';
$youtube[6]['title'] = 'Managing, Importing and Exporting Sections and Selectors';
$youtube[6]['id'] = 'oIPHFBWMHX4';
$youtube[7]['title'] = 'Restoring Your Settings From A Previous Save ';
$youtube[7]['id'] = 'y1L-tn26exU';


foreach ($youtube as $key => $array) {
	?>
    <div class='content-box'>
        <h3 class="box-title"><?php echo $key; ?>. <?php echo $array['title']; ?></h3>
        <div class="inner-box">
            <iframe width="853" height="480" style="max-width:100%" src="" rel="https://www.youtube.com/embed/<?php echo $array['id']; ?>?vq=hd720" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
    <?php
}
?>

<?php include 'docs-footer.inc.php'; ?>

</body>
</html>
