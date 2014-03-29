<?php
    require_once 'models/grader/bot.php';
    require_once 'models/grader/grader.php';
    require_once 'models/grader/serializer.php';
    require_once 'models/game.php';
    require_once 'models/round.php';
    require_once 'models/curl.php';

    class CurlConnectionMock implements CurlConnectionInterface {
        public $url;
        public $data;
        public $requestMethod;
        public $executed = false;
        public $response;
        protected $responseError;
        protected $hasResponseError;
        public $responseCode = 200;

        public function __construct() {
        }
        public function setOpt( $option, $value ) {
            switch ( $option ) {
                case CURLOPT_URL:
                    $this->url = $value;
                    break;
                case CURLOPT_POST:
                    $this->requestMethod = 'POST';
                    break;
                case CURLOPT_POSTFIELDS:
                    $this->data = $value;
                    break;
            }
        }
        public function exec() {
            if ( $this->hasResponseError ) {
                throw new CurlException( $this->responseError );
            }
            $this->executed = true;
        }
        public function __destruct() {}
        public function makeRespondWith( $json ) {
            $this->response = $json;
        }
        public function makeRespondWithError( $errorno ) {
            $this->hasResponseError = true;
            $this->responseError = $errorno;
        }
        public function makeRespondWithErrorCode( $code ) {
            $this->responseCode = $code;
        }
    }

    class GraderBotTest extends UnitTestWithFixtures {
        protected function assertErrorSavedInDb( $error ) {
            $dbError = new Error( $error->id );
            $this->assertSame( $dbError->actual, $error->actual, 'reportError must save the actual in the database' );
            $this->assertSame( $dbError->expected, $error->expected, 'reportError must save the expected in the database' );
            $this->assertSame( $dbError->description, $error->description, 'reportError must save the description in the database' );
            $this->assertSame( $dbError->user->id, $error->user->id, 'reportError must save the userid in the database' );
            if ( isset( $error->game ) ) {
                $this->assertSame( $dbError->game->id, $error->game->id, 'reportError must save the gameid in the database' );
            }
            else {
                $this->assertFalse( isset( $error->game->id ), 'game should not be set if the error does not have a game' );
            }
        }
        public function testInitiateRequest() {
            $user = $this->buildUser( 'vitsalis' );
            $bot = new GraderBot( $user );
            $bot->game = $this->buildGame();
            $botbase = $user->boturl;

            $this->assertTrue( method_exists( $bot, 'sendInitiateRequest' ), 'GraderBot object must export a "sendInitiateRequest" function' );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;
            $curlConnectionMock->makeRespondWith( json_encode( [
                'botname' => 'suprabot',
                'version' => '0.1.0',
                'username' => 'vitsalis'
            ] ) );
            $bot->sendInitiateRequest();

            $this->assertEquals( $botbase . '/bot', $curlConnectionMock->url, 'Initiation must send a request to the URL {{botbase}}/bot' );
            $this->assertEquals( 'POST', $curlConnectionMock->requestMethod, 'Initiation must do a POST request' );
            $this->assertTrue( $curlConnectionMock->executed, 'Initiation must execute curl request' );
        }
        public function testInitiateUsernameInvalid() {
            $bot = $this->buildBot( 'vitsalis' );

            $curlConnectionMock = new CurlConnectionMock();
            $curlConnectionMock->makeRespondWith( json_encode( [
                'botname' => 'suprabot',
                'version' => '0.1.0',
                'username' => 'god'
            ] ) );

            $bot->curlConnectionObject = $curlConnectionMock;

            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $this->assertErrorSavedInDb( $e->error );
                $caught = true;
            }

            $this->assertTrue( $caught, 'A GraderBotException should be thrown if username is incorrect' );

            $this->assertEquals( 1, count( $bot->errors ), 'Bot that replies with incorrect username should have an error reported' );
            $this->assertEquals( 'initiateUsernameMismatch', $bot->errors[ 0 ][ 'description' ], 'Bot that replies with incorrect username should have a "initiateUsernameMismatch" error reported' );
        }
        protected function initiateAndGetErrors( $mockError ) {
            $bot = $this->buildBot( 'vitsalis' );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;

            $curlConnectionMock->makeRespondWithError( $mockError );

            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $this->assertErrorSavedInDb( $e->error );
                $caught = true;
            }

            return [
                'caught' => $caught,
                'errors' => $bot->errors
            ];
        }
        public function testInitiateNotResolvedHostname() {
            $result = $this->initiateAndGetErrors( CURLE_COULDNT_RESOLVE_HOST );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught when curl responds with an error' );
            $this->assertEquals( 'initiateCouldNotResolve', $result[ 'errors' ][ 0 ][ 'description' ], 'Bot with url that could not be resolved must have a "initiateCouldNotResolve" error' );
        }
        public function testInitiateNetworkUnreachable() {
            $result = $this->initiateAndGetErrors( CURLE_COULDNT_CONNECT );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught when curl responds with an error' );
            $this->assertEquals( 'initiateCouldNotConnect', $result[ 'errors' ][ 0 ][ 'description' ], 'Bot with url that could not be reached must have a "initiateCouldNotConnect" error' );
        }
        public function testIniatiateMalformedUrl() {
            $result = $this->initiateAndGetErrors( CURLE_URL_MALFORMAT );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught when curl responds with an error' );
            $this->assertEquals( 'initiateMalformedUrl', $result[ 'errors' ][ 0 ][ 'description' ], 'Bot with malformed url must have a "initiateMalformedUrl" error' );
        }
        public function testInitiateRespondCodeInvalid() {
            $bot = $this->buildBot( 'vitsalis' );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;

            $curlConnectionMock->makeRespondWithErrorCode( 404 );

            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $this->assertErrorSavedInDb( $e->error );
                $caught = true;
            }

            $this->assertTrue( $caught, 'A GraderBotExcpetion must be caught when HTTP response code is not OK(200)' );
            $this->assertEquals( 'initiateHttpCodeNotOk', $bot->errors[ 0 ][ 'description' ], 'Bot whose HTTP response code is not OK(200) must have a "initiateHttpCodeNotOk" error' );
            $this->assertSame( 404, $bot->errors[ 0 ][ 'actual' ], 'Bot whose HTTP response code is not OK(200) must have its actual HTTP status code reported' );
            $this->assertSame( 200, $bot->errors[ 0 ][ 'expected' ], 'Bot whose HTTP response code is not OK(200) must have its expected HTTP status code reported' );
        }
        protected function initiateWithJsonAndGetErrors( $json ) {
            $bot = $this->buildBot( 'vitsalis' );

            $curlConnectionMock = new CurlConnectionMock();
            $curlConnectionMock->makeRespondWith( $json );

            $bot->curlConnectionObject = $curlConnectionMock;

            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $caught = true;
                $this->assertErrorSavedInDb( $e->error );
            }

            return [
                'caught' => $caught,
                'errors' => $bot->errors
            ];
        }
        public function testInitiateRespondValidJson() {
            $result = $this->initiateWithJsonAndGetErrors( json_encode( [
                'botname' => 'suprabot',
                'version' => '0.1.0',
                'username' => 'vitsalis'
            ] ) );

            $this->assertFalse( $result[ 'caught' ], 'A GraderBotException must not be caught if bot responds with valid json' );
            $this->assertTrue( empty( $result[ 'errors' ] ), 'There should be no errors if the json is valid' );
        }
        public function testInitiateRespondInvalidJson() {
            $result = $this->initiateWithJsonAndGetErrors( '{ invalidJson }' );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotExcpetion must be caught when response has invalid json' );
            $this->assertEquals( 'initiateInvalidJson', $result[ 'errors' ][ 0 ][ 'description' ], 'Bot who has invalid json as a response must have a "initiateInvalidJson" error' );
        }
        protected function assertInitiationThrows( $array, $error ) {
            $result = $this->initiateWithJsonAndGetErrors( json_encode( $array ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException is expected with error ' . $error . ' but was not caught' );
            $this->assertEquals( $error, $result[ 'errors' ][ 0 ][ 'description' ], 'Error must be ' . $error );
        }
        public function testInitiateRespondWithoutBotname() {
            $this->assertInitiationThrows(
                [
                    'version' => '0.1.0',
                    'username' => 'vitsalis'
                ], 
                'initiateBotnameNotSet'
            );
        }
        public function testInitiateRespondWithoutVersion() {
            $this->assertInitiationThrows(
                [
                    'botname' => 'suprabot',
                    'username' => 'vitsalis'
                ], 
                'initiateVersionNotSet'
            );
        }
        public function testIniatiateRespondWithoutUsername() {
            $this->assertInitiationThrows(
                [
                    'botname' => 'suprabot',
                    'version' => '0.1.0'
                ], 
                'initiateUsernameNotSet'
            );
        }
        public function testInitiateRespondAdditionalData() {
            $this->assertInitiationThrows(
                [
                    'botname' => 'suprabot',
                    'version' => '0.1.0',
                    'username' => 'vitsalis',
                    'additional' => 'shit'
                ], 
                'initiateAdditionalData'
            );
        }
        protected function gameRequestWithJsonAndGetErrors( $json ) {
            $game = $this->buildGame();
            $game->initiateAttributes();
            $user = $game->users[ 1 ];
            $bot = new GraderBot( $user );
            $bot->game = $game;
            $game->rounds[ 0 ] = new Round();

            $curlConnectionMock = new CurlConnectionMock();
            $curlConnectionMock->makeRespondWith( $json );
            $bot->curlConnectionObject = $curlConnectionMock;

            $caught = false;
            try {
                $bot->sendGameRequest( $game );
            }
            catch ( GraderBotException $e ) {
                $caught = true;
                $this->assertErrorSavedInDb( $e->error );
            }

            return [
                'caught' => $caught,
                'errors' => $bot->errors
            ];
        }
        public function testGameRequest() {
            $game = $this->buildGame();
            $game->initiateAttributes();
            $user = $game->users[ 1 ];
            $bot = new GraderBot( $user );
            $bot->game = $game;
            $game->rounds[ 0 ] = new Round();
            $botbase = $user->boturl;

            $this->assertTrue( method_exists( $bot, 'sendGameRequest' ), 'GraderBot object must export a "sendGameRequest" function' );

            $curlConnectionMock = new CurlConnectionMock();
            $curlConnectionMock->makeRespondWith( json_encode( [] ) );
            $bot->curlConnectionObject = $curlConnectionMock;
            $bot->sendGameRequest( $game );
            $data = GraderSerializer::gameRequestParams( $game );

            $this->assertEquals( $botbase . '/game', $curlConnectionMock->url, 'GameRequest must send a request to the URL {{botbase}}/game' );
            $this->assertEquals( 'POST', $curlConnectionMock->requestMethod, 'GameRequest must be a POST request' );
            $this->assertTrue( $curlConnectionMock->executed, 'GameRequest must execute curl process' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'gameid' ] ), 'gameid must exist in curl connection' );
            $this->assertEquals( $data[ 'gameid' ], $curlConnectionMock->data[ 'gameid' ], 'gameid must be sent properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'W' ] ), 'W must exist in curl connection' );
            $this->assertEquals( $data[ 'W' ], $curlConnectionMock->data[ 'W' ], 'W must be sent properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'H' ] ), 'H must exist in curl connection' );
            $this->assertEquals( $data[ 'H' ], $curlConnectionMock->data[ 'H' ], 'H must be sent properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'M' ] ), 'M must exist in curl connection' );
            $this->assertEquals( $data[ 'M' ], $curlConnectionMock->data[ 'M' ], 'M must be sent properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'MAX_HP' ] ), 'MAX_HP must exist in curl connection' );
            $this->assertEquals( $data[ 'MAX_HP' ], $curlConnectionMock->data[ 'MAX_HP' ], 'MAX_HP must be sent properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'players' ] ), 'players must exist in curl connection' );
            $this->assertEquals( $data[ 'players' ], $curlConnectionMock->data[ 'players' ], 'players must be sent properly to curl' );
        }
        public function testGameRespondValidJson() {
            $result = $this->gameRequestWithJsonAndGetErrors( json_encode( [] ) );

            $this->assertFalse( $result[ 'caught' ], 'A GraderBotException must not be caught if the response is valid' );
            $this->assertTrue( empty( $result[ 'errors' ] ), 'There should be no errors if the json is valid' );
        }
        public function testGameRespondInvalidJson() {
            $result = $this->gameRequestWithJsonAndGetErrors( 'notCorrectAnswer' );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if the response is invalid' );
            $this->assertEquals( 'gameInvalidJson', $result[ 'errors' ][ 0 ][ 'description' ], 'A "gameInvalidJson" error must be recorded when bot responds with invalid json' );
        }
        public function testGameRespondAdditionalData() {
            $result = $this->gameRequestWithJsonAndGetErrors( json_encode( [
                'additional' => 'shit'
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if the response has additional data' );
            $this->assertEquals( 'gameAdditionalData', $result[ 'errors' ][ 0 ][ 'description' ], 'A "gameAdditionalData" error must be recorded when bot responds with additional data' );
        }
        protected function roundRequestWithJsonAndGetErrors( $json ) {
            $game = $this->buildGame();
            $game->initiateAttributes();
            $game->genesis();
            $round = $game->rounds[ 0 ];
            $user = $game->users[ 1 ];
            $creature1 = $round->creatures[ 1 ];
            $creature1->user = $user;
            $creature2 = $round->creatures[ 2 ];
            $creature2->user = $game->users[ 2 ];
            $round->creatures = [
                $creature1->id => $creature1,
                $creature2->id => $creature2
            ];
            $bot = new GraderBot( $user );
            $bot->game = $game;

            $curlConnectionMock = new CurlConnectionMock();
            $curlConnectionMock->makeRespondWith( $json );

            $bot->curlConnectionObject = $curlConnectionMock;

            $return = [ 'caught' => false ];
            try {
                $response = $bot->sendRoundRequest( $round );
                $return[ 'response' ] = $response;
            }
            catch ( GraderBotException $e ) {
                $return[ 'caught' ] = true;
                $this->assertErrorSavedInDb( $e->error );
            }

            $return[ 'errors' ] = $bot->errors;

            return $return;
        }
        public function testRoundRequest() {
            $game = $this->buildGame();
            $game->initiateAttributes();
            $game->genesis();
            $round = $game->rounds[ 0 ];
            $user = $game->users[ 1 ];
            $bot = new GraderBot( $user );
            $botbase = $user->boturl;
            $bot->game = $game;

            $this->assertTrue( method_exists( $bot, 'sendRoundRequest' ), 'GraderBot object must export a "sendRoundRequest" function' );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;
            $curlConnectionMock->makeRespondWith( json_encode( [
                'intent' => [
                    [
                        'creatureid' => 1,
                        'action' => 'ATTACK',
                        'direction' => 'NORTH'
                    ]
                ]
            ] ) );
            $bot->sendRoundRequest( $round );
            $data = GraderSerializer::roundRequestParams( $round, $user, $game );

            $this->assertEquals( $botbase . "/round", $curlConnectionMock->url, 'RoundRequest must send a request to the URL {{botbase}}/game/{{gameid}}/round' ); 
            $this->assertEquals( 'POST', $curlConnectionMock->requestMethod, 'RoundRequest must be a POST request' );
            $this->assertTrue( $curlConnectionMock->executed, 'RoundRequest must execute curl process' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'round' ] ), 'round must exist in curl connection' );
            $this->assertEquals( $data[ 'round' ], $curlConnectionMock->data[ 'round' ], 'round must be send properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'map' ] ), 'map must exist in curl connection' );
            $this->assertEquals( $data[ 'map' ], $curlConnectionMock->data[ 'map' ], 'map must be send properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'myid' ] ), 'myid must exist in curl connection' );
            $this->assertEquals( $data[ 'myid' ], $curlConnectionMock->data[ 'myid' ], 'myid must be send properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'W' ] ), 'W must exist in curl connection' );
            $this->assertEquals( $data[ 'W' ], $curlConnectionMock->data[ 'W' ], 'W must be send properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'H' ] ), 'H must exist in curl connection' );
            $this->assertEquals( $data[ 'H' ], $curlConnectionMock->data[ 'H' ], 'H must be send properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'gameid' ] ), 'gameid must exist in curl connection' );
            $this->assertEquals( $data[ 'gameid' ], $curlConnectionMock->data[ 'gameid' ], 'gameid must be send properly to curl' );
        }
        public function testRoundRespondValidJson() {
            $result = $this->roundRequestWithJsonAndGetErrors( json_encode( [
                'intent' => [
                    [
                        'creatureid' => 1,
                        'action' => 'ATTACK',
                        'direction' => 'NORTH'
                    ]
                ]
            ] ) );

            $this->assertFalse( $result[ 'caught' ], 'A GraderBotException must not be caught if the response is valid' );
            $this->assertTrue( empty( $result[ 'errors' ] ), 'There should be no errors if the json is valid' );
        }
        public function testRoundRespondInvalidJson() {
            $result = $this->roundRequestWithJsonAndGetErrors( 'invalidJson' );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if the response is invalid json' );
            $this->assertEquals( 'roundInvalidJson', $result[ 'errors' ][ 0 ][ 'description' ], 'A "roundInvalidJson" error must be recorded if the bot responds with invalid json' );
            $this->assertEquals( 'invalidJson', $result[ 'errors' ][ 0 ][ 'actual' ], 'A "roundInvalidJson" error must be recorded if the bot responds with invalid json' );
        }
        public function testRoundRespondWithoutIntent() {
            $result = $this->roundRequestWithJsonAndGetErrors( json_encode( [] ) );
            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if the response does not have intent' );
            $this->assertEquals( 'roundIntentNotSet', $result[ 'errors' ][ 0 ][ 'description' ], 'A "roundIntentNotSet" error must be recorded when bot responds with intent not set' );
        }
        public function testRoundRespondWithoutCreatureid() {
            $result = $this->roundRequestWithJsonAndGetErrors( json_encode( [
                'intent' => [
                    [
                        'action' => 'ATTACK',
                        'direction' => 'NORTH'
                    ]
                ]
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if the response is invalid' );
            $this->assertEquals( 'roundCreatureidNotSet', $result[ 'errors' ][ 0 ][ 'description' ], 'A "roundCreatureidNotSet" error must be recorded when bot responds with creatureid not set' );
        }
        public function testRoundRespondWithoutAction() {
            $result = $this->roundRequestWithJsonAndGetErrors( json_encode( [
                'intent' => [
                    [
                        'creatureid' => 1,
                        'direction' => 'NORTH'
                    ]
                ]
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if the response is invalid' );
            $this->assertEquals( 'roundActionNotSet', $result[ 'errors' ][ 0 ][ 'description' ], 'A "roundActionNotSet" error must be recorded when bot responds with action not set' );
        }
        protected function assertRoundThrows( $array, $error ) {
            $result = $this->roundRequestWithJsonAndGetErrors( json_encode( $array ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException is expected with error ' . $error . ' but was not caught' );
            $this->assertEquals( $error, $result[ 'errors' ][ 0 ][ 'description' ], 'Error must be ' . $error );
        }
        public function testRoundInvalidCreatureId() {
            $this->assertRoundThrows(
                [
                    'intent' => [
                        [
                            'creatureid' => 9001,
                            'action' => 'MOVE',
                            'direction' => 'NORTH'
                        ]
                    ]
                ],
                'roundInvalidCreatureid'
            );
        }
        public function testRoundRespondWithoutDirection() {
            $this->assertRoundThrows(
                [
                    'intent' => [
                        [
                            'creatureid' => 1,
                            'action' => 'faksdfjaskfja'
                        ]
                    ]
                ],
                'roundDirectionNotSet'
            );
        }
        public function testRoundRespondAdditionalData() {
            $this->assertRoundThrows(
                [
                    'intent' => [],
                    'additional' => []
                ],
                'roundAdditionalData'
            );
        }
        public function testRoundRespondIntentAdditionalData() {
            $this->assertRoundThrows(
                [
                    'intent' => [
                        [
                            'creatureid' => 1,
                            'action' => 'ATTACK',
                            'direction' => 'NORTH'
                        ],
                        [
                            'creatureid' => 2,
                            'action' => 'faksdfjaskfja',
                            'direction' => 'NORTH',
                            'additional' => 'shit'
                        ]
                    ]
                ],
                'roundIntentAdditionalData'
            );
        }
        public function testRoundRespondInvalidAction() {
            $this->assertRoundThrows(
                [
                    'intent' => [
                        [
                            'creatureid' => 1,
                            'action' => 'faksdfjaskfja',
                            'direction' => 'NORTH'
                        ]
                    ]
                ],
                'roundActionInvalid'
            );
        }
        public function testRoundRespondInvalidDirection() {
            $this->assertRoundThrows(
                [
                    'intent' => [
                        [
                            'creatureid' => 1,
                            'action' => 'ATTACK',
                            'direction' => 'faksdfjasljf'
                        ]
                    ]
                ],
                'roundDirectionInvalid'
            );
        }
        public function testRoundRespondNotObject() {
            $this->assertRoundThrows(
                [
                    'intent' => [
                        3, 4, 5
                    ]
                ],
                'roundResponseNotObject'
            );
        }
        public function testRoundRespondMultipleIntents() {
            $this->assertRoundThrows(
                [
                    'intent' => [
                        [
                            'creatureid' => 1,
                            'action' => 'ATTACK',
                            'direction' => 'NORTH'
                        ],
                        [
                            'creatureid' => 1
                        ]
                    ]
                ],
                'roundDirectionNotSet'
            );
        }
        public function testRoundRespondIntents() {
            $result = $this->roundRequestWithJsonAndGetErrors( json_encode( [
                'intent' => [
                    [
                        'creatureid' => 1,
                        'action' => 'ATTACK',
                        'direction' => 'NORTH'
                    ]
                ]
            ] ) );

            $clone = new Creature();
            $clone->id = 1;
            $clone->intent = new Intent( ACTION_ATTACK, DIRECTION_NORTH );

            $this->assertTrue( isset( $result[ 'response' ] ), 'A bot must respond with a dictionary containing the response key' ); 
            $this->assertTrue( is_array( $result[ 'response' ] ), 'A bot must respond with a collection' );
            $this->assertEquals( 1, count( $result[ 'response' ] ), 'The collection must have as many objects as specified' );

            $this->assertEquals( $clone->id, $result[ 'response' ][ 0 ]->id, 'creatureid must be the same as specified' );
            $this->assertEquals( $clone->intent->action, $result[ 'response' ][ 0 ]->intent->action, 'action must be the same as specified' );
            $this->assertEquals( $clone->intent->direction, $result[ 'response' ][ 0 ]->intent->direction, 'direction must be the same as specified' );
        }
        public function testRoundMoveDifferentUsersCreature() {
            $result = $this->roundRequestWithJsonAndGetErrors( json_encode( [
                'intent' => [
                    [
                        'creatureid' => 2,
                        'action' => 'ATTACK',
                        'direction' => 'NORTH'
                    ]
                ]
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if someone tries to move an creature that does not belong to him' );
            $this->assertEquals( "roundIntentNotOwnCreature", $result[ 'errors' ][ 0 ][ 'description' ], 'A "roundIntentNotOwnCreature" error must be given when a user tries to move a creature that does not belong to him' );
        }
        public function testRoundRequestResponse() {
            $result = $this->roundRequestWithJsonAndGetErrors( json_encode( [
                'intent' => [
                    [
                        'creatureid' => 1,
                        'action' => 'ATTACK',
                        'direction' => 'NORTH'
                    ]
                ]
            ] ) );

            $this->assertFalse( $result[ 'caught' ], 'A GraderBotException must not be caught if someone send valid json' );

            $this->assertTrue( is_array( $result[ 'response' ] ), 'sendRoundRequest must return a collection' );
            foreach ( $result[ 'response' ] as $creature ) {
                $this->assertTrue( is_object( $creature ), 'sendReoundRequest response must be a collection of objects' );
            }

            $this->assertTrue( isset( $result[ 'response' ][ 0 ]->id ), 'The creature from the collection must have an id' );
            $this->assertEquals( 1, $result[ 'response' ][ 0 ]->id, 'The creatureid must be the same as specified' );

            $this->assertTrue( isset( $result[ 'response' ][ 0 ]->intent->action ), 'The creature from the collection must have an intent->action' );
            $this->assertEquals( ACTION_ATTACK, $result[ 'response' ][ 0 ]->intent->action, 'action must be the same as specified' );

            $this->assertTrue( isset( $result[ 'response' ][ 0 ]->intent->direction ), 'The creature from the collection must have an intent->direction' );
            $this->assertEquals( DIRECTION_NORTH, $result[ 'response' ][ 0 ]->intent->direction, 'direction must be the same as specified' );
        }
        public function testReportError() {
            $user = $this->buildUser( 'vitsalis' );
            $bot = new GraderBot( $user );
            $bot->curlConnectionObject = new CurlConnectionMock();
            $bot->curlConnectionObject->responseCode = 404;
            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $caught = true;
                $this->assertEquals( 'initiateHttpCodeNotOk', $e->error->description, 'The GraderBotException that reportError throws must have the correct error' );
                $this->assertSame( '200', $e->error->expected, 'The GraderBotException that reportError throws must have the correct expected' );
                $this->assertSame( '404', $e->error->actual, 'The GraderBotException that reportError throws must have the correct actual' );
                $this->assertErrorSavedInDb( $e->error );
            }
            $this->assertTrue( $caught, 'reportError must throw a GraderBotException' );
        }
    }

    return new GraderBotTest();
?>
