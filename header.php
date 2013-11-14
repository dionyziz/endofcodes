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

    class HTTPErrorException extends Exception {
        public $header;

        public function __construct( $error, $description= "" ) {
            if ( !empty( $description ) ) {
                parent::__construct( "HTTP/1.1 $error $description" );
            }
            else {
                $this->header = "HTTP/1.1 $error"
                parent::__construct( "HTTP/1.1 $error" );
            }
        }
    }

    class HTTPNotFoundException extends HTTPErrorException {
        public function __construct() {
            parent::__construct( '404', 'Not Found' );
        }
    }

    class HTTPUnauthorizedException extends HTTPErrorException {
        public function __construct() {
            parent::__construct( '401' );
        }
    }
?>
