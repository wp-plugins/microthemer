<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Please do not call this page directly.'); 
}

// calc pagination stuff
$total_packs = count($this->file_structure);
$results_per_page = 20;
$total_pages = ceil($total_packs/$results_per_page);
if (isset($_GET['packs_page'])) {
    $page = htmlentities(intval($_GET['packs_page']));
} else {
    $page = 1;
}
// determine start and end
$start = (($page-1)*$results_per_page)+1;
$end = ($start+$results_per_page)-1;
if ($end > $total_packs) {
    $end = $total_packs;
}

?>
<div id="tvr" class='wrap tvr-wrap tvr-manage'>
	<div id='tvr-manage'>

        <?php
        // ajax loaders, meta spans, logs tmpl
        $this->hidden_ajax_loaders();
        $this->manage_packs_meta();
        $this->manage_packs_header($this->microthemespage);
        ?>

        <pre>
            <?php // print_r($this->file_structure); ?>
        </pre>

        <div id="full-logs">
            <?php echo $this->display_log(); ?>
        </div>

        <?php
        $this->pack_pagination($page, $total_pages, $total_packs, $start, $end);
        ?>

        <ul id="all-packs">
            <?php
            // output packs
            $i = 0;
            foreach ($this->file_structure as $pack => $array){
                ++$i;
                if ($i < $start) {
                    continue;
                } else if ($i > $end) {
                    break;
                }
                ?>
                <li>
                    <div class="pack">
                        <?php
                        if (!empty($array['screenshot']) and file_exists($this->micro_root_dir . $pack.'/' . $array['screenshot'])) {
                            $screenshot = $this->micro_root_url . $pack.'/' . $array['screenshot'];
                        } else {
                            $screenshot = $this->thispluginurl . 'images/screenshot-placeholder-small.gif';
                        }
                        $url = 'admin.php?page=' . $this->managesinglepage . '&design_pack=' . $pack;
                        $name = $this->readable_name($pack);
                        ?>
                        <a href="<?php echo $url ; ?>">
                            <img src="<?php echo $screenshot; ?>" width="145" height="83"
                                 title="Edit <?php echo $name; ?>" />
                        </a>
                        <div class="pack-quick-icons">
                            <span class="tvr-icon delete-icon delete-pack-multi" title="Delete <?php echo $name; ?>"
                                  rel="delete" data-dir-name="<?php echo $pack; ?>"></span>

                            <span class="tvr-icon download-icon download-pack-multi" title="Download <?php echo $name; ?>"
                                  rel="download" data-dir-name="<?php echo $pack; ?>"></span>
                            <a href="<?php echo $url ; ?>" title="Edit <?php echo $name; ?>"><span class="tvr-icon edit-icon"></span></a>
                        </div>

                        <?php
                        // get meta info
                        $meta_info = $this->read_meta_file($this->micro_root_dir . $pack . '/meta.txt');
                        $h2 = '
                        <a href="'.$url.'" class="title-text" title="Edit '.$name.'">
                            ' . $this->readable_name($pack);
                            // version
                            if (!empty($meta_info['Version'])) {
                                //$h2.=' <span class="version">'.$meta_info['Version'] . '</span>';
                            }
                            $h2.= '
                        </a>';
                        // author
                        if (!empty($meta_info['Author'])) {
                            $h2.= '<p class="author-name"> by '.strip_tags($meta_info['Author']) . '</p>';
                        }
                        echo $h2;
                        ?>
                    </div>
                </li>
                <?php
            }
            ?>

        </ul>

        <?php
        $this->pack_pagination($page, $total_pages, $total_packs, $start, $end);
        ?>

    </div>
</div><!-- end wrap -->