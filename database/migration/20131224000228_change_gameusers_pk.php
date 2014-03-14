<?php
    Migration::dropIndex( 'gameusers', 'uc_gameusers' );
    Migration::addPrimaryKey( 'gameusers', 'pk_gameusers', [ 'gameid', 'userid' ] );
?>
