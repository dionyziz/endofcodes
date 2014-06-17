<?php
    class HTTPErrorController extends ControllerBase {
      public $method = 'view';

        public function view( $error, $reason, $header ) {
            header( $header );
            require "views/http/$error.php";
        }

    }
?>
