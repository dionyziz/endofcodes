<?php
    class GraderBot {
        public $curlConnectionObject;
        protected $url;
        public $user;
        public $errors = array();
        public $version;
        public $name;

        public function __construct( $user ) {
            $this->curlConnectionObject = new CurlConnection();
            $this->user = $user;
            $this->url = $user->boturl;
        }
        protected function httpRequest( $endpoint = '', $method = 'view', $data = array() ) {
            switch ( $method ) {
                case 'create':
                case 'delete':
                case 'update':
                    $method = 'POST';
                    break;
                case 'view':
                case 'listing':
                    $method = 'GET';
                    break;
            }
            $ch = $this->curlConnectionObject;

            $url = $this->url . '/' . $endpoint;
            if ( $method == 'GET' ) {
                $parts = [];
                foreach ( $data as $key => $value ) {
                    $value = urlencode( $value );
                    $parts[] = "$key=$value";
                }
                $queryString = implode( '&', $parts );
                $url .= '?' . $queryString;
            }

            $ch->setOpt( CURLOPT_URL, $url );
            $ch->setOpt( CURLOPT_RETURNTRANSFER, 1 );

            if ( $method == 'POST' ) {
                $ch->setOpt( CURLOPT_POST, 1 );
                $ch->setOpt( CURLOPT_POSTFIELDS, $data );
            }
            $ch->exec();

            return $ch;
        }
        public function sendInitiateRequest() {
            try {
                $ch = $this->httpRequest( 'bot', 'create' );
            }
            catch ( CurlException $e ) {
                $this->errors[] = [
                    CURLE_COULDNT_RESOLVE_HOST => 'could_not_resolve',
                    CURLE_COULDNT_CONNECT => 'could_not_connect'
                ][ $e->error ];
                throw new GraderBotException();
            }

            if ( $ch->responseCode !== 200 ) {
                $this->errors[] = 'http_code_not_ok';
                throw new GraderBotException();
            }

            $decodedResponse = json_decode( $ch->response );
            if ( $decodedResponse === null ) {
                $this->errors[] = 'invalid_json';
                throw new GraderBotException();
            }
            if ( !isset( $decodedResponse->botname ) ) {
                $this->errors[] = 'botname_not_set';
                throw new GraderBotException();
            }
            if ( !isset( $decodedResponse->version ) ) {
                $this->errors[] = 'version_not_set';
                throw new GraderBotException();
            }
            if ( !isset( $decodedResponse->username ) ) {
                $this->errors[] = 'username_not_set';
                throw new GraderBotException();
            }
            if ( $this->user->username !== $decodedResponse->username ) {
                $this->errors[] = 'username_mismatch';
                throw new GraderBotException();
            }
            $this->version = $decodedResponse->version;
            $this->botname = $decodedResponse->botname;
        }
        public function sendGameRequest( $game ) {
            $this->httpRequest( 'game', 'create', GraderSerializer::gameRequestParams( $game ) );
        }
        public function buildGameRequestParams() {
        }
        public function sendRoundRequest( $round ) {
            $this->httpRequest( 'round', 'create', GraderSerializer::roundRequestParams( $round ) );
        }
    }

    class GraderBotException extends Exception {}
?>
