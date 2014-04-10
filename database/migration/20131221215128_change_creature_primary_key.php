<?php
    Migration::dropPrimaryKey( 'creatures' ); 
    Migration::addPrimaryKey( 'creatures', 'pk_creatures', [ 'gameid', 'userid', 'id' ] ); 
?>
