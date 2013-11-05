<?php
    include 'models/database.php';
    include 'views/layout.php';
    class RedirectException extends Exception {
        private $url;

        public function getURL() {
            return $this->url;
        }
    }
?>
