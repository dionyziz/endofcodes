<?php
    interface CurlConnectionInterface {
        public function __construct();
        public function setOpt( $option, $value );
        public function exec();
        public function __destruct();
    }

    class CurlConnection implements CurlConnectionInterface {
        protected $ch;
        public $response;
        public $responseCode;

        public function __construct() {
            $this->ch = curl_init();
        }
        public function __destruct() {
            curl_close( $this->ch );
        }
        public function setOpt( $option, $value ) {
            curl_setopt( $this->ch, $option, $value );
        }
        public function exec() {
            $response = curl_exec( $this->ch );
            if ( $response === false ) {
                throw new NetworkException( curl_errno( $this->ch ) );
            }
            $this->response = $response;
            $this->responseCode = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE );
        }
    }

    interface URLRetrieverInterface {
        public function readURL( $url );
    }

    class URLRetriever implements URLRetrieverInterface {
        public function readURL( $url ) {
            $contents = @file_get_contents( $url );
            if ( $contents === false ) {
                throw new NetworkException( "Could not retrieve URL '$url'" );
            }
            return $contents;
        }
    }

    class NetworkException extends Exception {
        public $error;

        public function __construct( $error = '' ) {
            parent::__construct( $error );
            $this->error = $error;
        }
    }
?>
