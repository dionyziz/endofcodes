<?php
    ?><h1>Game #<?php
    echo $gameid;
    ?></h1>
    <h2>Round #<?php
    echo $round->id;
    ?></h2>

    <div class='map' style='position: relative'>
        <?php
        $colors = [ 'red', 'blue', 'green', 'yellow', 'purple', 'cyan', 'black', 'pink' ];
        foreach ( $creatures as $creature ) {
            if ( $creature->alive ) {
                ?><div style="left: <?php
                echo $creature->locationx * 25;
                ?>px; top: <?php
                echo $creature->locationy * 25;
                ?>px; position: absolute; background-color: <?php
                if ( !isset( $playerColor[ $creature->user->id ] ) ) {
                    $playerColor[ $creature->user->id ] = array_shift( $colors );
                }
                echo $playerColor[ $creature->user->id ];
                ?>; width: 25px; height: 25px;"></div><?php
            }
        }
        ?>
    </div><?php
?>
