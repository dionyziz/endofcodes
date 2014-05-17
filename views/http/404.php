<?php
    require 'views/header.php';
?>
<h1>404 Not Found</h1>
<?php
    echo htmlspecialchars( $reason );
    require 'views/footer.php';
?>
