<?php
    class GraderBot {
        public $curlConnectionObject;
        protected $url;
        public $user;
        public $errors = array();

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
            $output = $ch->exec();

            if ( isset( $ch->response ) ) {
                if ( $this->user->username !== $ch->response->username ) {
                    $this->errors[] = 'username_mismatch';
                    throw new GraderBotException();
                }
            }

            return $output;
        }
        public function sendInitiateRequest() {
            $this->httpRequest( 'bot', 'create' ); 
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
