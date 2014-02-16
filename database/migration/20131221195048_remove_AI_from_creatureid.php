<?php
    include 'migrate.php';

    Migration::renameField( 'creatures', 'id', 'id', 'int(11) NOT NULL' );
?>
