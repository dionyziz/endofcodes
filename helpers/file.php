<?php
    function safeWrite( $filename, $content ) {
        $success = @file_put_contents( $filename, $content );
        if ( !$success ) {
            throw new FileNotWritableException( $filename, $content );
        }
    }
    function recursiveCopy( $source, $destination ) {
        $source = escapeshellarg( $source );
        $destination = escapeshellarg( $destination );
        exec( "cp -R $source $destination" );
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
