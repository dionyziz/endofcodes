<?php
    include 'models/dependencies.php';
    include 'models/creature.php';
    include 'models/round.php';
    include 'models/intent.php';
    $rounds = array();
    for ( $i = 0; $i <= 2; ++$i ) {
        $rounds[ $i ] = new Round();
        $rounds[ $i ]->gameid = 1;
        $rounds[ $i ]->id = $i; 
        for ( $j = 0, $k = 0; $j <= 2; ++$j, $k += 2 ) {
            if ( $i === 0 ) {
                $rounds[ $i ]->creatures[ $j ] = new Creature( $k ); 
                $rounds[ $i ]->creatures[ $j ]->gameid = $rounds[ $i ]->gameid;
                $rounds[ $i ]->creatures[ $j ]->userid = $k;
                $rounds[ $i ]->creatures[ $j ]->round = $rounds[ $i ]; 
                $rounds[ $i ]->creatures[ $j ]->x = 1;
                $rounds[ $i ]->creatures[ $j ]->y = 0;
                $rounds[ $i ]->creatures[ $j ]->hp = 10;
                $rounds[ $i ]->creatures[ $j ]->intent = new Intent( 'ACTION_MOVE', 'DIRECTION_NONE' );
                $rounds[ $i ]->creatures[ $j ]->save();
            }
            else {
                $rounds[ $i ]->creatures[ $j ] = $rounds[ $i - 1 ]->creatures[ $j ];
                switch ( $rounds[ $i ]->creatures[ $j ]->intent->direction ) {
                    case 1:
                        $rounds[ $i ]->creatures[ $j ]->y += 1;
                        break;
                    case 2:
                        $rounds[ $i ]->creatures[ $j ]->x += 1;
                        break;
                    case 3:
                        $rounds[ $i ]->creatures[ $j ]->y -= 1;
                        break;
                    case 4:
                        $rounds[ $i ]->creatures[ $j ]->x -= 1;
                        break;
                }
            }
        }
        $rounds[ $i ]->save();
    }
?>
