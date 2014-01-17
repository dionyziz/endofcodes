<?php
    // moves creature
    function creatureMove( $creature ) {
        switch ( $creature->intent->direction ) {
            case DIRECTION_NORTH:
                $creature->locationy += 1;
                break;
            case DIRECTION_EAST:
                $creature->locationx += 1;
                break;
            case DIRECTION_SOUTH:
                $creature->locationy -= 1;
                break;
            case DIRECTION_WEST:
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
