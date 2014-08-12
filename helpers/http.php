<?php

    function readHTTPAccept() {
        if ( !isset( $_SERVER[ 'HTTP_ACCEPT' ] ) ) {
            return;
        }
        $accept = strtolower( str_replace( ' ', '', $_SERVER[ 'HTTP_ACCEPT' ] ) );
        $accept = explode( ',', $accept );
        $acceptTypes = [];
        foreach ( $accept as $a ) {
            if ( strpos( $a, ';q=' ) ) {
                list( $a, $q ) = explode( ';q=', $a );
                if ( $q === 0 ) {
                    continue;
                }
            }
            $acceptTypes[ $a ] = true;
        }
        return $acceptTypes;
    }

    /*
    Redirects to a given URL or resource:

    go( "http://www.google.com" ); // full URL
    go( "user", "view", [ "example" => "argument" ] ); // resource & method
    go(); // home page
    */
    function go( $resourceOrURL = false, $method = false, $args = [] ) {
        throw new HTTPRedirectException( $resourceOrURL, $method, $args );
    }

    // This is a simple HTTP Redirect. The web page is reloaded.
    class HTTPRedirectException extends Exception {
        public $url;
        public $resource;
        public $method;
        public $args;

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

    // When this exception is thrown, the controller is changed to a new one, without reloading the page.
    class ErrorRedirectException extends Exception {
        public $controller;
        public $arguments;
        public function __construct( $controller, $arguments ) {
            $this->controller = $controller;
            $this->arguments = $arguments;
            parent::__construct();
        }
    }

    class HTTPErrorException extends ErrorRedirectException {
        public $header;
        public $error;
        public $reason;

        public function __construct( $error, $description = '', $reason = '' ) {
            $this->error = $error;
            $this->reason = $reason;
            $this->header = "HTTP/1.1 $error";
            if ( !empty( $description ) ) {
                $this->header .= ' ' . $description;
            }
            $arguments = get_object_vars( $this );
            parent::__construct( 'httperror', $arguments );
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
