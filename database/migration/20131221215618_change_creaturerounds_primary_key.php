<?php
    require 'migrate.php';

    Migration::dropIndex( 'roundcreatures', 'uc_roundcreatures' );
    Migration::addPrimaryKey( 'roundcreatures', 'pk_roundcreature', [ 'gameid', 'roundid', 'creatureid' ] );
?>
