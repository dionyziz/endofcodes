<?php
    $round = $_POST[ 'round' ];
    $map = json_decode( $_POST[ 'map' ] );
    $gameid = $_POST[ 'gameid' ];
    $myid = $_POST[ 'myid' ];
    $W = $_POST[ 'W' ];
    $H = $_POST[ 'H' ];
    $intent = [];
    foreach ( $map as $creature ) {
        if ( $creature->userid = $myid ) {
            $offsets = [
                1 => [ 0, 1 ],
                2 => [ 1, 0 ],
                3 => [ 0, -1 ],
                4 => [ -1, 0 ]
            ];
            $delta = [
                1 => 'NORTH',
                2 => 'EAST',
                3 => 'SOUTH',
                4 => 'WEST'
            ];
            $directionAttack = [];
            $directionMove = [];
            foreach ( $offsets as $key => $offset ) {
                $creatureFound = false;
                $positionValid = $creature->x + $offset[ 0 ] < $W && $creature->y + $offset[ 1 ] < $H;
                if ( $positionValid ) {
                    $x = $creature->x + $offset[ 0 ];
                    $y = $creature->y + $offset[ 1 ];
                    foreach ( $map as $creature ) {
                        if ( $creature->x === $x && $creature->y === $y ) {
                            $creatureFound = true;
                            if ( $creature->userid = $myid ) {
                                break;
                            }
                            $directionAttack[] = $key;
                            break;
                        }
                    }
                    if ( !$creatureFound ) {
                        $directionMove[] = $key;
                    }
                }
            }
            if ( count( $directionAttack ) ) {
                $direction = $delta[ $directionAttack[ rand( 1, count( $directionAttack ) ) ] ];
                $intent[] = [
                    'creatureid' => $creature->creatureid,
                    'action' => 'ATTACK',
                    'direction' => $direction
                ];
            }
            else {
                $move = rand( 0, 1 );
                if ( $move === 0 || !count( $directionMove ) ) {
                    $intent[] = [
                        'creatureid' => $creature->creatureid,
                        'action' => 'NONE',
                        'direction' => 'NONE'
                    ];
                }
                else {
                    $direction = $delta[ $directionMove[ rand( 1, count( $directionMove ) ) ] ];
                    $intent[] = [
                        'creatureid' => $creature->creatureid,
                        'action' => 'ATTACK',
                        'direction' => $direction
                    ];
                }
            }
        }
    }
    echo json_encode( [ 'intent' => $intent ] );
?>
