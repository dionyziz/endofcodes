<?php
    // moves creature
    function creatureDirection( $creature ) {
        $moved = false;
        switch ( $creature->intent->direction ) {
            case DIRECTION_NORTH:
                if ( $creature->locationy + 1 >= $creature->game->height ) {
                    throw new CreatureOutOfBoundsException();
                }
                $creature->locationy += 1;
                break;
            case DIRECTION_EAST:
                if ( $creature->locationx + 1 >= $creature->game->width ) {
                    throw new CreatureOutOfBoundsException();
                }
                $creature->locationx += 1;
                break;
            case DIRECTION_SOUTH:
                if ( $creature->locationy - 1 < 0 ) {
                    throw new CreatureOutOfBoundsException();
                }
                $creature->locationy -= 1;
                break;
            case DIRECTION_WEST:
                if ( $creature->locationx - 1 < 0 ) {
                    throw new CreatureOutOfBoundsException();
                }
                $creature->locationx -= 1;
                break;
        }
    }

    function creatureMove( $creature ) {
        try {
            creatureDirection( $creature );
        }
        catch ( CreatureOutOfBoundsException $e ) {
            $creature->intent = new Intent();
        }
    }

    function findCreatureByCoordinates( $round, $x, $y ) {
        foreach ( $round->creatures as $possibleCreature ) {
            if ( $possibleCreature->alive ) {
                if ( $possibleCreature->locationx === $x && $possibleCreature->locationy === $y ) {
                    return $possibleCreature;
                }
            }
        }
        throw new ModelNotFoundException();
    }
    function creatureAttack( $creature ) {
        $victim = clone $creature;
        try {
            creatureDirection( $victim );
        }
        catch ( CreatureOutOfBoundsException $e ) {
            $creature->intent = new Intent();
            return;
        }
        try {
            $victim = findCreatureByCoordinates( $creature->round, $victim->locationx, $victim->locationy );
        }
        catch ( ModelNotFoundException $e ) {
            $creature->intent = new Intent();
            return;
        }
        if ( $victim->user->id === $creature->user->id ) {
            $creature->intent = new Intent();
            return;
        }
        --$victim->hp;
    }
?>
