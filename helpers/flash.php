<?php
    function flash( $message, $type = 'success' ) {
        switch ( $type ) {
            case 'error':
            case 'warning':
            case 'success':
                $_SESSION[ 'alert' ] = [
                    'message' => $message,
                    'type' => $type
                ];
                break;
        }
    }
?>
