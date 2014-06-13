<?php
    class HTTPErrorController extends ControllerBase {

        public function createView( $error, $reason, $header ) {
            header( $header );
            require "views/http/$error.php";
        }

    }
?>
