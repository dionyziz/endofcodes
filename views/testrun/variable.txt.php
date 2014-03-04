<?php
    switch ( gettype( $arg ) ) {
        case 'boolean':
            if ( $arg ) {
                ?>true<?php
            }
            else {
                ?>false<?php
            }
            break;
        case 'integer':
        case 'double':
            echo "$arg";
            break;
        case 'string':
            ?>"<?php
            echo $arg;
            ?>"<?php
            break;
        case 'array':
            ?>[ array of <?php
            echo pluralize( count( $arg ), 'element' );
            ?> ]<?php
            break;
        case 'object':
            ?>[ object of class <?php
            echo get_class( $arg );
            ?> ]<?php
            break;
        case 'resource':
            ?>[ resource ]<?php
            break;
        case 'NULL':
            ?>[ NULL ]<?php
            break;
        default:
            ?>[ unknown ]<?php
            break;
    }
?>
