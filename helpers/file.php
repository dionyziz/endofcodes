<?php
    function safeWrite( $filename, $content ) {
        if ( !file_exists( $filename ) ) {
            @touch( $filename );
        }
        if ( is_writable( $filename ) ) {
            $success = file_put_contents( $filename, $content );
        }
        if ( empty( $success ) ) {
            throw new FileNotWritableException( $filename, $content );
        }
    }
    class FileNotWritableException extends Exception {
        public $filename;
        public $content;
        public function __construct( $filename, $content = '' ) {
            $this->filename = $filename;
            $this->content = $content;
            parent::__construct( 'Failed to write to ' . $filename );
        }
    }
?>
