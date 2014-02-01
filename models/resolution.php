<?php
    // moves creature
    function creatureDirection( $creature ) {
        $delta = array(
            DIRECTION_NORTH => array( 0, 1 ),
            DIRECTION_EAST => array( 1, 0 ),
            DIRECTION_SOUTH => array( 0, -1 ),
            DIRECTION_WEST => array( -1, 0 )
        )[ $creature->intent->direction ];
        $x = $creature->locationx + $delta[ 0 ];
        $y = $creature->locationy + $delta[ 1 ];
        if ( $x < 0 || $x >= $creature->game->width || $y < 0 || $y >= $creature->game->height ) {
            throw new CreatureOutOfBoundsException();
        }
        $creature->locationx = $x;
        $creature->locationy = $y;
    }

    function creatureMove( $creature ) {
        try {
            creatureDirection( $creature );
        }
        catch ( CreatureOutOfBoundsException $e ) {
            $roundNumber = $creature->round->id;
            $creature->game->botError( 
                $creature->round,
                $creature->user, 
                "Tried to move creature $creature->id in a location outside of bounds on round $roundNumber."
            );
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
            $creature->game->botError( 
                $creature->round,
                $creature->user, 
                "Tried to attack creature $victim->id with creature $creature->id while they both belong to the same user."
            );
            $creature->intent = new Intent();
            return;
        }
        --$victim->hp;
    }
?>
