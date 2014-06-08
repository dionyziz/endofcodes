<?php
    require 'views/header.php';
?>
<div class='gamemeta'>
    <h2 data-rounds="<?php
        echo $game->roundCount;
    ?>" data-maxHp="<?php
        echo $game->maxHp;
    ?>" data-id="<?php
        echo $game->id;
    ?>">Game <?php
        echo $game->id;
    ?></h2>
    <strong><?php
        echo $game->created;
    ?></strong>
</div>

<?php
    $colors = [ 'red', 'blue', 'green', 'yellow', 'purple', 'cyan', 'black', 'pink' ];
    $playerColor = [];
    foreach ( $game->users as $user ) {
        $playerColor[ $user->id ] = array_shift( $colors );
    }
    $hasCreatures = [];
    foreach ( $round->creatures as $creature ) {
        if ( $creature->alive ) {
            if ( !isset( $hasCreatures[ $creature->user->id ] ) ) {
                $hasCreatures[ $creature->user->id ] = true;
            }
        }
    }
?>

<aside>
    <ol class="playerList"><?php
        foreach ( $game->users as $user ) {
            ?><li<?php
                if ( isset( $currentUser ) && $user->id == $currentUser->id ) {
                    ?> class='you'<?php
                }
            ?> data-id="<?php
                echo $user->id;
            ?>"><span class='<?php
                echo $playerColor[ $user->id ];
            ?> bubble' data-color="<?php
                echo $playerColor[ $user->id ];
            ?>"></span><?php
                if ( !isset( $hasCreatures[ $user->id ] ) ) {
                    ?><del><?php
                        echo htmlspecialchars( $user->username );
                    ?></del><?php
                }
                else {
                    echo htmlspecialchars( $user->username );
                }
            ?></li><?php
        }
    ?></ol>
</aside>

<div class='game' data-width="<?php
    echo $game->width;
?>" data-height="<?php
    echo $game->height;
?>">
    <div class='infobubble'>
        <span class='hp'>
            <span class='numeric'></span>
            <span class='damage'></span>
        </span>
        <div class='stats'>
            <h3 class="creatureid"></h3>
            <div>Player: <strong class="player"></strong></div>
            <div>Location: <strong class="location"></strong></div>
        </div>
    </div>
    <div class='gameboard'>
        <?php
            foreach ( $round->creatures as $creature ) {
                if ( $creature->alive ) {
                    $creatureInfo = [
                        'creatureid' => $creature->id,
                        'username' => $creature->user->username,
                        'userid' => $creature->user->id,
                        'x' => $creature->locationx,
                        'y' => $creature->locationy,
                        'hp' => $creature->hp
                    ];
                    ?><div class="<?php
                        echo $playerColor[ $creature->user->id ];
                    ?> creature" <?php
                        foreach ( $creatureInfo as $key => $value ) {
                            ?>data-<?php
                                echo $key;
                            ?>='<?php
                                echo $value;
                            ?>' <?php
                        }
                    ?>style="left: <?php
                        echo $creature->locationx * 20;
                    ?>px; top: <?php
                        echo $creature->locationy * 20;
                    ?>px;"></div><?php
                }
            }
        ?>
    </div>
</div>

<div class='time'>
    <span class='roundid' data-id="<?php
        echo $round->id;
    ?>">Round <?php
        echo $round->id;
    ?></span>
    <span class="previous game-tool"<?php
        if ( $round->id - 1 < 0 ) {
            ?> style="display: none"<?php
        }
    ?>>
        <a href="game/view?gameid=<?php
            echo htmlspecialchars( $gameid );
        ?>&amp;roundid=<?php
            echo htmlspecialchars( $round->id - 1 );
        ?>"><span class="glyphicon glyphicon-chevron-left"></span></a>
    </span>
    <span class="play game-tool"><a href="#"><span class="glyphicon glyphicon-play"></span></a></span>
    <span class="pause game-tool"><a href="#"><span class="glyphicon glyphicon-pause"></span></a></span>
    <span class="next game-tool"<?php
        if ( $round->id + 1 >= $game->roundCount ) {
            ?> style="display: none"<?php
        }
    ?>>
        <a href="game/view?gameid=<?php
            echo htmlspecialchars( $gameid );
        ?>&amp;roundid=<?php
            echo htmlspecialchars( $round->id + 1 );
        ?>"><span class="glyphicon glyphicon-chevron-right"></span></a>
    </span>
    <div class="slider"></div>
</div>
<?php
    require 'views/footer/view.php';
?>
