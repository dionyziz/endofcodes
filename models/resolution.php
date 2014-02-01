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
    function creatureAttack( $attackerCreature ) {
        $potentialVictim = clone $attackerCreature;
        try {
            creatureDirection( $potentialVictim );
        }
        catch ( CreatureOutOfBoundsException $e ) {
            $attackerCreature->game->botError( 
                $attackerCreature->round,
                $attackerCreature->user, 
                "Tried to attack a creature outside of bounds with creature $attackerCreature->id."
            );
            return;
        }
        $victim = $potentialVictim;
        try {
            $victim = findCreatureByCoordinates( $attackerCreature->round, $victim->locationx, $victim->locationy );
        }
        catch ( ModelNotFoundException $e ) {
            $attackerCreature->intent = new Intent();
            $attackerCreature->game->botError( 
                $attackerCreature->round,
                $attackerCreature->user, 
                "Tried to attack non existent creature with creature $attackerCreature->id."
            );
            return;
        }
        if ( $victim->user->id === $attackerCreature->user->id ) {
            $attackerCreature->game->botError( 
                $attackerCreature->round,
                $attackerCreature->user, 
                "Tried to attack creature $victim->id with creature $attackerCreature->id while they both belong to the same user."
            );
            return;
        }
        --$victim->hp;
    }
?>
