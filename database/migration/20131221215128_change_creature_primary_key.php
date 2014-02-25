<?php
    require 'migrate.php';

    Migration::dropPrimaryKey( 'creatures' ); 
    Migration::addPrimaryKey( 'pk_creatures', [ 'gameid', 'userid', 'id' ] ); 
?>
