<?php
    require 'views/header.php';
?>
<h1>401 Unauthorized</h1>
<?php
    echo htmlspecialchars( $reason );
    require 'views/footer/view.php';
?>
