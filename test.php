<?php
    include 'models/dependencies.php';
    include 'models/creature.php';
    for ( $i = 1; $i <= 10; ++$i ) {
        $creature = new Creature( $i, $i, $i );
        $creature->save();
    }
?>
