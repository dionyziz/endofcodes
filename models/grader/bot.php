<?php
    require_once 'models/curl.php';
    require_once 'models/error.php';

    interface GraderBotInterface {
        public function __construct( User $user );
        public function sendInitiateRequest();
        public function sendGameRequest( Game $game );
        public function sendRoundRequest( Round $round );
    }

    class GraderBot implements GraderBotInterface {
        public $curlConnectionObject;
        protected $url;
        public $user;
        public $errors = array();
        public $version;
        public $name;
        public $game;

        public function __construct( User $user ) {
            $this->curlConnectionObject = new CurlConnection();
            $this->user = $user;
            $this->url = $user->boturl;
        }
        protected function reportError( $description, $expected = '', $actual = '' ) {
            $this->errors[] = [
                'description' => $description,
                'expected' => $expected,
                'actual' => $actual
            ];
            $error = new Error();
            $error->description = $description;
            $error->expected = $expected;
            $error->actual = $actual;
            $error->user = $this->user;
            if ( isset( $this->game ) ) {
                $error->game = $this->game;
            }
            $error->save();
            throw new GraderBotException( $error );
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
                $errorMap = [
                    CURLE_COULDNT_RESOLVE_HOST => 'initiateCouldNotResolve',
                    CURLE_COULDNT_CONNECT => 'initiateCouldNotConnect',
                    CURLE_URL_MALFORMAT => 'initiateMalformedUrl'
                ];
                if ( isset( $errorMap[ $e->error ] ) ) {
                    $this->reportError( $errorMap[ $e->error ] );
                }
                throw $e;
            }

            if ( $ch->responseCode !== 200 ) {
                $this->reportError( 'initiateHttpCodeNotOk', '200', $ch->responseCode );
            }

            $decodedResponse = json_decode( $ch->response );
            if ( $decodedResponse === null ) {
                $this->reportError( 'initiateInvalidJson' );
            }
            $requiredAttributes = [ 'botname', 'version', 'username' ];
            foreach ( $requiredAttributes as $attribute ) {
                if ( !isset( $decodedResponse->$attribute ) ) {
                    $this->reportError( 'initiate' . $attribute . 'NotSet' );
                }
            }
            if ( count( ( array )$decodedResponse ) > count( $requiredAttributes ) ) {
                $this->reportError( 'initiateAdditionalData' );
            }
            if ( $this->user->username !== $decodedResponse->username ) {
                $this->reportError( 'initiateUsernameMismatch' );
            }
            $this->version = $decodedResponse->version;
            $this->botname = $decodedResponse->botname;
        }
        public function sendGameRequest( Game $game ) {
            try {
                $ch = $this->httpRequest( 'game', 'create', GraderSerializer::gameRequestParams( $game ) );
            }
            catch ( CurlException $e ) {
                $this->reportError( $e->error );
            }
            $decodedResponse = json_decode( $ch->response );
            if ( $decodedResponse === null ) {
                $this->reportError( 'gameInvalidJson' );
            }
            if ( count( ( array )$decodedResponse ) ) {
                $this->reportError( 'gameAdditionalData' );
            }
        }
        public function sendRoundRequest( Round $round ) {
            $gameid = $round->game->id;
            try {
                $ch = $this->httpRequest( "round", 'create', GraderSerializer::roundRequestParams( $round, $this->user, $this->game ) );
            }
            catch ( CurlException $e ) {
                $this->reportError( $e->error );
            }
            $decodedResponse = json_decode( $ch->response );
            if ( $decodedResponse === null ) {
                $this->reportError( 'roundInvalidJson', '', $ch->response );
            }
            if ( !isset( $decodedResponse->intent ) ) {
                $this->reportError( 'roundIntentNotSet' );
            }
            if ( count( ( array )$decodedResponse ) > 1 ) {
                $this->reportError( 'roundAdditionalData' );
            }
            $requiredAttributes = [ 'creatureid', 'direction', 'action' ];
            foreach ( $decodedResponse->intent as $creatureIntent ) {
                foreach ( $requiredAttributes as $attribute ) {
                    if ( !is_object( $creatureIntent ) ) {
                        $this->reportError( 'roundResponseNotObject' );
                    }
                    if ( !isset( $creatureIntent->$attribute ) ) {
                        $this->reportError( 'round' . $attribute . 'NotSet' );
                    }
                }
            }
            $collection = [];
            $round = $this->game->getCurrentRound();
            foreach ( $decodedResponse->intent as $creatureIntentData ) {
                if ( count( ( array )$creatureIntentData ) > count( $requiredAttributes ) ) {
                    $this->reportError( 'roundIntentAdditionalData' );
                }
                if ( !isset( $round->creatures[ $creatureIntentData->creatureid ] ) ) {
                    $this->reportError( 'roundInvalidCreatureid' );
                }
                if ( $round->creatures[ $creatureIntentData->creatureid ]->user->id !== $this->user->id ) {
                    $this->reportError( 'roundIntentNotOwnCreature' );
                }
                $creature = new Creature();
                $creature->id = $creatureIntentData->creatureid;
                try {
                    $action = actionStringToConst( $creatureIntentData->action );
                }
                catch ( ModelNotFoundException $e ) {
                    $this->reportError( 'roundActionInvalid' );
                }
                try {
                    $direction = directionStringToConst( $creatureIntentData->direction );
                }
                catch ( ModelNotFoundException $e ) {
                    $this->reportError( 'roundDirectionInvalid' );
                }
                $creature->intent = new Intent( $action, $direction );
                $collection[] = $creature;
            }
            return $collection;
        }
    }

    class GraderBotException extends Exception {
        public $error;

        public function __construct( Error $error ) {
            $this->error = $error;
            parent::__construct( "Grader bot error: $error->description. Expected: $error->expected. Actual: $error->actual." );
        }
    }
?>
