<?php
    require 'views/header.php';
?>
<div class='gamemeta'>
    <h2>Game <?php
        echo $game->id;
    ?></h2>
    <strong><?php
        echo $game->created;
    ?></strong>
</div>

<div class='game' style="width:<?php
    echo $game->width * 20;
?>px; height:<?php
    echo $game->height * 20;
?>px;">
    <div class='gameboard'>
        <?php
            $colors = [ 'red', 'blue', 'green', 'yellow', 'purple', 'cyan', 'black', 'pink' ];
            foreach ( $round->creatures as $creature ) {
                if ( $creature->alive ) {
                    ?><div class="<?php
                        if ( !isset( $playerColor[ $creature->user->id ] ) ) {
                            $playerColor[ $creature->user->id ] = array_shift( $colors );
                        }
                        echo $playerColor[ $creature->user->id ];
                    ?> creature" style="left: <?php
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
    <span class='round'>Round <?php
        echo $round->id;
    ?></span>
    <span class="next"><?php
        if ( isset( $game->rounds[ $round->id + 1 ] ) ) {
            ?><p><a href="game/view?gameid=<?php
                echo htmlspecialchars( $gameid );
            ?>&amp;roundid=<?php
                echo htmlspecialchars( $round->id + 1 );
            ?>">Next round</a></p><?php
        }
    ?></span>
    <span class="previous"><?php
        if ( $round->id > 0 ) {
            ?><p><a href="game/view?gameid=<?php
                echo htmlspecialchars( $gameid );
            ?>&amp;roundid=<?php
                echo htmlspecialchars( $round->id - 1 );
            ?>">Previous round</a></p><?php
        }
    ?></span>
</div>
<?php
    require 'views/footer.php';
?>
