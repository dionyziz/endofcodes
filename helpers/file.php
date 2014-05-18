<?php
    class FileNotWritableException extends Exception {
        public $filename;
        public $content;
        public function __construct( $filename, $content ) {
            $this->filename = $filename;
            $this->content = $content;
            parent::__construct( 'Failed to write to ' . $filename );
        }
    }
?>
