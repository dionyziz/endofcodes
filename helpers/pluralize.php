<?php
    function pluralize( $count, $singular ) {
        ob_start();
        echo $count;
        ?> <?php
        if ( $count == 1 ) {
            echo $singular;
        }
        else {
            if ( $singular[ strlen( $singular ) - 1 ] == 'y' ) {
                echo substr( $singular, 0, strlen( $singular ) - 1 ) . 'ies';
            }
            else {
                echo $singular . 's';
            }
        }
        return ob_get_clean();
    }
?>
