<?php
    // moves creature
    function creatureMove( $creature ) {
        switch ( $creature->intent->direction ) {
            case NORTH:
                $creature->locationy += 1;
                break;
            case EAST:
                $creature->locationx += 1;
                break;
            case SOUTH:
                $creature->locationy -= 1;
                break;
            case WEST:
                $creature->locationx -= 1;
                break;
        }
        return $creature;
    }

    // atacker atacks victim, returns true if victim is still alive
    // after the atack, false otherwise
    function creatureAttack( $attacker, $victim ) {
        $victim->hp -= 1;
        if ( $victim->hp === 0 ) {
            $victim->alive = false;
        }
        return $victim->alive;
    }
?>
