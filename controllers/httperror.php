<?php
    class HTTPErrorController extends ControllerBase {
      protected $method = 'create';

        public function createView( $error, $reason, $header ) {
            header( $header );
            require "views/http/$error.php";
        }

    }
?>
