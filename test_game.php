<?php
    include 'models/dependencies.php';
    include 'models/creature.php';
    include 'models/round.php';
    include 'models/intent.php';
    include 'models/user.php';
    include 'models/game.php';
    include 'models/resolution.php';
    //$game = new Game( 7 );
    //echo $game->rounds[ 2 ]->creatures[ 1 ]->id;
    //echo $game->rounds[ 2 ]->creatures[ 1 ]->user->id;
    $user = new User( 1 );
    $game = new Game();
    $game->width = $game->height = 10;
    $game->save();
    for ( $i = 0; $i <= 2; ++$i ) {
        $game->nextRound();
        for ( $j = 0, $k = 0; $j <= 2; ++$j, $k += 2 ) {
            if ( $i === 0 ) {
                $game->rounds[ $i ]->nextCreature( $user );
                $game->rounds[ $i ]->creatures[ $j ]->round = $game->rounds[ $i ]; 
                $game->rounds[ $i ]->creatures[ $j ]->locationx = 1;
                $game->rounds[ $i ]->creatures[ $j ]->locationy = 0;
                $game->rounds[ $i ]->creatures[ $j ]->hp = 10;
                $game->rounds[ $i ]->creatures[ $j ]->intent = new Intent( ACTION_MOVE, DIRECTION_NONE );
                $game->rounds[ $i ]->creatures[ $j ]->save();
            }
            else {
                $game->rounds[ $i ]->creatures[ $j ] = creatureMove( $game->rounds[ $i - 1 ]->creatures[ $j ] );
            }
        }
        $game->rounds[ $i ]->save();
    }
?>
