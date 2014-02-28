<?php
    require_once 'migrate.php';

    Migration::dropIndex( 'roundcreatures', 'uc_roundcreatures' );
    Migration::addPrimaryKey( 'roundcreatures', 'pk_roundcreatures', [ 'gameid', 'roundid', 'creatureid' ] );
?>
