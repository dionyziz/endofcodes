<?php
    require_once 'migrate.php';

    Migration::addPrimaryKey( 'roundcreatures', 'pk_roundcreatures', [ 'gameid', 'roundid', 'creatureid' ] );
?>
