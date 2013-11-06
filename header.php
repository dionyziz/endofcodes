<?php
    include 'models/database.php';
    class RedirectException extends Exception {
        private $url;

        public function getURL() {
            return $this->url;
        }
    }
?>
