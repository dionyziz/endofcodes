<?php
    /*
    Redirects to a given URL or resource:

    go( "http://www.google.com" ); // full URL
    go( "user", "view", [ "example" => "argument" ] ); // resource & method
    go(); // home page
    */
    function go( $resourceOrURL = false, $method = false, $args = [] ) {
        throw new RedirectException( $resourceOrURL, $method, $args );
    }

    class RedirectException extends Exception {
        private $url;
        public $resource;
        public $method;
        public $args;

        public function getURL() {
            return $this->url;
        }

        public function __construct( $resourceOrURL = false, $method = false, $args = [] ) {
            if ( $resourceOrURL === false ) {
                $this->__construct( 'dashboard', 'view' );
            }
            else if ( $method === false ) {
                $this->url = $resourceOrURL;
            }
            else {
                $this->resource = $resourceOrURL;
                foreach ( $args as $key => $arg ) {
                    if ( $arg === true ) {
                        $arg = 'yes';
                    }
                    else if ( $arg === false ) {
                        $arg = 'no';
                    }
                    $args[ $key ] = "$key=" . urlencode( $arg );
                }
                $this->method = $method;
                $this->args = $args;
                $this->url = $this->resource . '/' . $this->method . '?' . implode( '&', $this->args );
            }
        }
    }

    class HTTPErrorException extends Exception {
        public $header;
        public $error;
        public $reason;

        public function __construct( $error, $description = "", $reason = '' ) {
            $this->error = $error;
            $this->reason = $reason;
            if ( !empty( $description ) ) {
                $this->header = "HTTP/1.1 $error $description";
            }
            else {
                $this->header = "HTTP/1.1 $error";
            }
            parent::__construct( $this->header );
        }
        public function outputErrorPage() {
            $error = $this->error;
            $reason = $this->reason;
            require_once "views/http/$error.php";
        }
    }

    class HTTPNotFoundException extends HTTPErrorException {
        public function __construct( $reason = '' ) {
            parent::__construct( 404, 'Not Found', $reason );
        }
    }

    class HTTPUnauthorizedException extends HTTPErrorException {
        public function __construct( $reason = '' ) {
            parent::__construct( 401, 'Unauthorized', $reason );
        }
    }

    class HTTPBadRequestException extends HTTPErrorException {
        public function __construct( $reason = '' ) {
            parent::__construct( 400, 'Bad Request', $reason );
        }
    }
?>
