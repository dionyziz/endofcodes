<?php
    require 'views/header.php';
?>
<p class="bg-warning">We were not able to save the configuration, probably because of insufficient permissions.</p>
<p>Please copy/paste the following contents in endofcodes/config/config-local.php</p>
<textarea class="form-control" rows="6"><?php
	echo $content;
?></textarea>
<?php
    require 'views/footer.php';
?>
