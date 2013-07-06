<?php 
$docs_title = "Search Forum";
$main_class = 'search-forum';
$h1 = "Search Microthemer Forum";
include 'docs-header.inc.php'; 

?>
<script>
  (function() {
    var cx = '000071969491634751241:cmyujawpxji';
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        '//www.google.com/cse/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  })();
</script>
<div id="g-search">
    	<gcse:search linkTarget="_self"></gcse:search>
    </div>




<?php include 'docs-footer.inc.php'; ?>

</body>
</html>
