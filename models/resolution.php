<?php
    // moves creature
    function creatureDirection( $creature ) {
        $moved = false;
        switch ( $creature->intent->direction ) {
            case DIRECTION_NORTH:
                if ( $creature->locationy + 1 < $creature->game->height ) {
                    $creature->locationy += 1;
                    $moved = true;
                }
                break;
            case DIRECTION_EAST:
                if ( $creature->locationx + 1 < $creature->game->width ) {
                    $creature->locationx += 1;
                    $moved = true;
                }
                break;
            case DIRECTION_SOUTH:
                if ( $creature->locationy - 1 >= 0 ) {
                    $creature->locationy -= 1;
                    $moved = true;
                }
                break;
            case DIRECTION_WEST:
                if ( $creature->locationx - 1 >= 0 ) {
                    $creature->locationx -= 1;
                    $moved = true;
                }
                break;
        }
        if ( !$moved ) {
            $creature->intent = new Intent( ACTION_NONE, DIRECTION_NONE );
        }
        return $creature;
    }

    function creatureMove( $creature ) {
        return creatureDirection( $creature );
    }

    function findCreatureByCoordinates( $round, $x, $y ) {
        foreach ( $round->creatures as $possibleCreature ) {
            if ( $possibleCreature->alive ) {
                if ( $possibleCreature->locationx === $x && $possibleCreature->locationy === $y ) {
                    return $possibleCreature;
                }
            }
        }
        return false;
    }
    // atacker atacks victim, returns true if victim is still alive
    // after the atack, false otherwise
    function creatureAttack( $creature ) {
        $victim = creatureDirection( $creature );
        if ( $victim->intent->action === ACTION_NONE ) {
            $creature->intent = new Intent( ACTION_NONE, DIRECTION_NONE );
            return $creature;
        }
        $victim = findCreatureByCoordinates( $creature->round, $victim->locationx, $victim->locationy );
        if ( $victim === false ) {
            $creature->intent = new Intent( ACTION_NONE, DIRECTION_NONE );
            return $creature;
        }
        if ( $victim->user === $creature->user ) {
            $creature->intent = new Intent( ACTION_NONE, DIRECTION_NONE );
            return $creature;
        }
        --$victim->hp;
        $victim->round->creatures[ $victim->id ] = $victim;
        return $creature;
    }
?>
