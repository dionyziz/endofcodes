<?php
    require 'views/header.php';
?>
<div class='gamemeta'>
    <h2 data-rounds="<?php
        echo count( $game->rounds );
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

<div class='game' style="width:<?php
    echo $game->width * 20;
?>px; height:<?php
    echo $game->height * 20;
?>px;">
    <div class='gameboard'>
        <?php
            foreach ( $round->creatures as $creature ) {
                if ( $creature->alive ) {
                    $creatureInfo = [
                        'creatureid' => $creature->id,
                        'username' => $creature->user->username,
                        'x' => $creature->locationx,
                        'y' => $creature->locationy,
                        'hp' => $creature->hp,
                        'maxHp' => $game->maxHp
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
        <div class='infobubble'>
            <span class='hp'>
                <span class='numeric'></span>
                <span class='damage' style=''></span>
            </span>
            <div class='stats'>
                <h3 class="creatureid"></h3>
                <div>Player: <strong class="player"></strong></div>
                <div>Location: <strong class="location"></strong></div>
            </div>
        </div>
    </div>
</div>

<div class='time'>
    <span class='round'>Round <?php
        echo $round->id;
    ?></span>
    <span class="previous"<?php
        if ( !isset( $game->rounds[ $round->id - 1 ] ) ) {
            ?> style="display: none"<?php
        }
    ?>>
        <a href="game/view?gameid=<?php
            echo htmlspecialchars( $gameid );
        ?>&amp;roundid=<?php
            echo htmlspecialchars( $round->id - 1 );
        ?>">Previous</a>
    </span>
    <span class="next"<?php
        if ( !isset( $game->rounds[ $round->id + 1 ] ) ) {
            ?> style="display: none"<?php
        }
    ?>>
        <a href="game/view?gameid=<?php
            echo htmlspecialchars( $gameid );
        ?>&amp;roundid=<?php
            echo htmlspecialchars( $round->id + 1 );
        ?>">Next</a>
    </span>
</div>
<?php
    require 'views/footer.php';
?>
