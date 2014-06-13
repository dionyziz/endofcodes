<?php
    function safeWrite( $filename, $content ) {
        $success = @file_put_contents( $filename, $content );
        if ( !$success ) {
            throw new FileNotWritableException( $filename, $content );
        }
    }

    function safeRead( $filename ) {
        if ( !file_exists( $filename ) ) {
            throw new FileNotFoundException( $filename );
        }
        $content = @file_get_contents( $filename );
        if ( $content === FALSE ) {
            throw new FileNotReadableException( $filename );
        }
        return $content;
    }

    function recursiveCopy( $source, $destination ) {
        $source = escapeshellarg( $source );
        $destination = escapeshellarg( $destination );
        exec( "cp -R $source $destination" );
    }

    class FileException extends Exception {
        public $filename;
        public function __construct( $filename, $message ) {
            $this->filename = $filename;
            parent::__construct( $message );
        }
    }

    class FileNotWritableException extends FileException {
        public $content;
        public function __construct( $filename, $content = '' ) {
            $this->content = $content;
            parent::__construct( $filename, 'Failed to write to ' . $filename );
        }
    }

    class FileNotReadableException extends FileException {
        public function __construct( $filename ) {
            parent::__construct( $filename, 'Failed to read from ' . $filename );
        }
    }

    class FileNotFoundException extends FileException {
        public function __construct( $filename ) {
            parent::__construct( $filename, 'File ' . $filename . 'does not exist' );
        }
    }
?>
