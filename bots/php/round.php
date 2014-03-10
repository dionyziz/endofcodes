<?php
    $round = $_POST[ 'round' ];
    $map = json_decode( $_POST[ 'map' ] );
    $gameid = $_POST[ 'gameid' ];
    $myid = $_POST[ 'myid' ];
    $W = $_POST[ 'W' ];
    $H = $_POST[ 'H' ];
    $intent = [];
    foreach ( $map as $creature ) {
        if ( $creature->userid === $myid && $creature->hp > 0 ) {
            $offsets = [
                1 => [ 0, 1 ],
                2 => [ 1, 0 ],
                3 => [ 0, -1 ],
                4 => [ -1, 0 ]
            ];
            $directions = [
                1 => 'NORTH',
                2 => 'EAST',
                3 => 'SOUTH',
                4 => 'WEST'
            ];
            $directionAttack = [];
            $directionMove = [];
            foreach ( $offsets as $key => $offset ) {
                $creatureFound = false;
                $possiblex = $creature->x + $offset[ 0 ];
                $possibley = $creature->y + $offset[ 1 ];
                $positionValid = $possiblex < $W && $possibley < $H && $possiblex >= 0 && $possibley >= 0;
                if ( $positionValid ) {
                    $x = $possiblex;
                    $y = $possibley;
                    foreach ( $map as $otherCreature ) {
                        if ( $otherCreature->x === $x && $otherCreature->y === $y ) {
                            if ( $otherCreature->hp > 0 ) {
                                $creatureFound = true;
                                if ( $otherCreature->userid !== $myid ) {
                                    $directionAttack[] = $key;
                                }
                            }
                            break;
                        }
                    }
                    if ( !$creatureFound ) {
                        $directionMove[] = $key;
                    }
                }
            }
            if ( count( $directionAttack ) ) {
                $direction = $directions[ $directionAttack[ rand( 0, count( $directionAttack ) - 1 ) ] ];
                $intent[] = [
                    'creatureid' => $creature->creatureid,
                    'action' => 'ATTACK',
                    'direction' => $direction
                ];
            }
            else {
                $move = rand( 0, 1 );
                if ( $move != 0 && count( $directionMove ) ) {
                    $direction = $directions[ $directionMove[ rand( 0, count( $directionMove ) - 1 ) ] ];
                    $intent[] = [
                        'creatureid' => $creature->creatureid,
                        'action' => 'MOVE',
                        'direction' => $direction
                    ];
                }
            }
        }
    }
    echo json_encode( [ 'intent' => $intent ] );
?>
