<?php
    require 'views/header.php';
?>
<p class="alert alert-warning">We were not able to save the configuration, probably because of insufficient permissions.</p>
<p>Please copy/paste the following contents in endofcodes/config/config-local.php</p>
<textarea class="form-control" rows="8"><?php
    echo htmlspecialchars( $content );
?></textarea>
<?php
    require 'views/footer/view.php';
?>
