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

<?php
    $colors = [ 'red', 'blue', 'green', 'yellow', 'purple', 'cyan', 'black', 'pink' ];
    $playerColor = [];
    foreach ( $game->users as $user ) {
        $playerColor[ $user->id ] = array_shift( $colors );
    }
    foreach ( $round->creatures as $creature ) {
        if ( $creature->alive ) {
            if ( !isset( $hasCreatures[ $creature->user->id ] ) ) {
                $hasCreatures[ $creature->user->id ] = true;
            }
        }
    }
?>

<aside>
    <ol><?php
        foreach ( $game->users as $user ) {
            ?><li<?php
                if ( isset( $currentUser ) && $user->id == $currentUser->id ) {
                    ?> class='you'<?php
                }
            ?>><span class='<?php
                echo $playerColor[ $user->id ];
            ?> bubble'></span> <?php
                if ( !isset( $hasCreatures[ $user->id ] ) ) {
                    ?><del><?php
                        echo $user->username;
                    ?></del><?php
                }
                else {
                    echo $user->username;
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
                    ?><div class="<?php
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
