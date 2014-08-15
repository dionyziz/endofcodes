<?php
    function flash( $message, $type = 'error' ) {
        switch ( $type ) {
            case 'error':
            case 'warning':
            case 'success':
                $_SESSION[ 'alert' ] = [ $message, $type ];
                break;
        }
    }
?>
