<?php
    function pluralize( $count, $singular ) {
        ob_start();
        echo $count;
        ?> <?php
        if ( $count == 1 ) {
            echo $singular;
        }
        else {
            echo $singular;
            ?>s<?php
        }
        return ob_get_clean();
    }
?>
