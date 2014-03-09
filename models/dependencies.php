<?php
    require_once 'models/db.php';
    if ( file_exists( 'config/config-local.php' ) ) {
        require_once 'config/config-local.php';
    }
    else {
        require_once 'config/config.php';
    }
    require_once 'models/database.php';
    require_once 'models/base.php';
    require_once 'models/user.php';
    require_once 'controllers/base.php';
    require_once 'controllers/authentication.php';
    require_once 'helpers/html.php';
    require_once 'helpers/pluralize.php';
?>
