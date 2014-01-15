<?php
    include 'models/dependencies.php';
    include 'models/creature.php';
    include 'models/round.php';
    include 'models/intent.php';
    include 'models/user.php';
    include 'models/game.php';
    $user = new User( 1 );
    $game = new Game();
    $game->created = date('Y-m-d H:i:s');
    $game->width = $game->height = 10;
    $game->save();
    $game->rounds = array();
    for ( $i = 0; $i <= 2; ++$i ) {
        $game->rounds[ $i ] = new Round();
        $game->rounds[ $i ]->game = $game;
        $game->rounds[ $i ]->id = $i; 
        for ( $j = 0, $k = 0; $j <= 2; ++$j, $k += 2 ) {
            if ( $i === 0 ) {
                $game->rounds[ $i ]->creatures[ $j ] = new Creature(); 
                $game->rounds[ $i ]->creatures[ $j ]->id = $j;
                $game->rounds[ $i ]->creatures[ $j ]->game = $game;
                $game->rounds[ $i ]->creatures[ $j ]->user = $user;
                $game->rounds[ $i ]->creatures[ $j ]->round = $game->rounds[ $i ]; 
                $game->rounds[ $i ]->creatures[ $j ]->x = 1;
                $game->rounds[ $i ]->creatures[ $j ]->y = 0;
                $game->rounds[ $i ]->creatures[ $j ]->hp = 10;
                $game->rounds[ $i ]->creatures[ $j ]->intent = new Intent( 'ACTION_MOVE', 'DIRECTION_NONE' );
                $game->rounds[ $i ]->creatures[ $j ]->save();
            }
            else {
                $game->rounds[ $i ]->creatures[ $j ] = $game->rounds[ $i - 1 ]->creatures[ $j ];
                switch ( $game->rounds[ $i ]->creatures[ $j ]->intent->direction ) {
                    case 1:
                        $game->rounds[ $i ]->creatures[ $j ]->y += 1;
                        break;
                    case 2:
                        $game->rounds[ $i ]->creatures[ $j ]->x += 1;
                        break;
                    case 3:
                        $game->rounds[ $i ]->creatures[ $j ]->y -= 1;
                        break;
                    case 4:
                        $game->rounds[ $i ]->creatures[ $j ]->x -= 1;
                        break;
                }
            }
        }
        $game->rounds[ $i ]->save();
    }
?>
