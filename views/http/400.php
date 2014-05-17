<?php
    require 'views/header.php';
?>
<h1>400 Bad Request</h1>
<?php
    echo htmlspecialchars( $reason );
    require 'views/footer/view.php';
?>
