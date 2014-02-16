<?php
    ob_start();
    switch ( gettype( $arg ) ) {
        case 'boolean':
            $type = 'boolean';
            if ( $arg ) {
                ?>true<?php
            }
            else {
                ?>false<?php
            }
            break;
        case 'integer':
            $type = 'integer';
            echo htmlspecialchars( $arg );
            break;
        case 'double':
            $type = 'double';
            echo htmlspecialchars( $arg );
            break;
        case 'string':
            $type = 'string';
            ?>"<?php
            echo htmlspecialchars( $arg );
            ?>"<?php
            break;
        case 'array':
            $type = 'array';
            ?>[ array of <span class='integer'><?php
            echo count( $arg );
            ?></span> element<?php
            if ( count( $arg ) != 1 ) {
                ?>s<?php
            }
            ?> ]<?php
            break;
        case 'object':
            $type = 'object';
            ?>[ object of class <span class='class'><?php
            echo htmlspecialchars( get_class( $arg ) );
            ?></span> ]<?php
            break;
        case 'resource':
            $type = 'resource';
            ?>[ resource: <?php
            echo htmlspecialchars( get_resource_type( $arg ) );
            ?> ]<?php
            break;
        case 'NULL':
            $type = 'null';
            ?>[ NULL ]<?php
            break;
        default:
            $type = 'unknown';
            ?>[ unknown ]<?php
            break;
    }
    $contents = ob_get_clean();
?>
<span class="<?php
    echo $type;
?>"><?php
    echo $contents;
?></span><?php
