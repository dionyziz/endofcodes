<?php
    class RedirectException extends Exception {
        private $url;

        public function getURL() {
            return $this->url;
        }

        public function __construct( $url ) {
            $this->url = $url;
        }
    }
?>
