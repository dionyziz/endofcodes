<?php
    include_once 'models/grader/bot.php';
    include_once 'models/grader/grader.php';
    include_once 'models/grader/serializer.php';
    include_once 'models/game.php';
    include_once 'models/round.php';
    include_once 'models/curl.php';

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
        public function testInitiateRequest() {
            $user = $this->buildUser( 'vitsalis' );
            $bot = new GraderBot( $user );
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
        public function testGameRequest() {
            $game = $this->buildGame();
            $game->genesis();
            $user = $game->users[ 0 ];
            $bot = new GraderBot( $user );
            $botbase = $user->boturl;

            $this->assertTrue( method_exists( $bot, 'sendGameRequest' ), 'GraderBot object must export a "sendGameRequest" function' );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;
            $answer = $bot->sendGameRequest( $game );
            $data = GraderSerializer::gameRequestParams( $game );

            $this->assertEquals( 'POST', $curlConnectionMock->requestMethod, 'GameRequest must be a POST request' );

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

            $this->assertTrue( $curlConnectionMock->executed, 'GameRequest must execute curl request' );
        }
        public function testRoundRequest() {
            $game = $this->buildGame();
            $game->genesis();
            $round = $game->rounds[ 0 ];
            $user = $game->users[ 0 ];
            $bot = new GraderBot( $user );
            $botbase = $user->boturl;

            $this->assertTrue( method_exists( $bot, 'sendRoundRequest' ), 'GraderBot object must export a "sendRoundRequest" function' );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;
            $answer = $bot->sendRoundRequest( $round );
            $data = GraderSerializer::roundRequestParams( $round );

            $this->assertEquals( 'POST', $curlConnectionMock->requestMethod, 'RoundRequest must be a POST request' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'round' ] ), 'round must exist in curl connection' );
            $this->assertEquals( $data[ 'round' ], $curlConnectionMock->data[ 'round' ], 'round must be send properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'map' ] ), 'map must exist in curl connection' );
            $this->assertEquals( $data[ 'map' ], $curlConnectionMock->data[ 'map' ], 'map must be send properly to curl' );
        }
        public function testVerifyUsername() {
            $user = $this->buildUser( 'vitsalis' );
            $bot = new GraderBot( $user );

            $curlConnectionMock = new CurlConnectionMock();
            $curlConnectionMock->makeRespondWith( json_encode( [
                'botname' => 'suprabot',
                'version' => '0.1.0',
                'username' => $user->username
            ] ) );
            $bot->curlConnectionObject = $curlConnectionMock;

            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $caught = true;
            }

            $this->assertFalse( $caught, 'A GraderBotException should not be thrown if the username is correct' );

            $bot = new GraderBot( $user );

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
                $caught = true;
            }

            $this->assertTrue( $caught, 'A GraderBotException should be thrown if username is incorrect' );

            $this->assertEquals( 1, count( $bot->errors ), 'Bot that replies with incorrect username should have an error reported' );
            $this->assertEquals( 'username_mismatch', $bot->errors[ 0 ], 'Bot that replies with incorrect username should have a "username_mismatch" error reported' );
        }
        protected function initiateAndGetErrors( $mock_error ) {
            $user = $this->buildUser( 'vitsalis' );
            $user->boturl = 'http://endofcodes.com/does_not_matter';
            $bot = new GraderBot( $user );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;

            $curlConnectionMock->makeRespondWithError( $mock_error );

            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $caught = true;
            }

            return [
                'caught' => $caught,
                'errors' => $bot->errors
            ];
        }
        public function testResolvedHostname() {
            $result = $this->initiateAndGetErrors( CURLE_COULDNT_RESOLVE_HOST );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught when curl responds with an error' );
            $this->assertEquals( 'could_not_resolve', $result[ 'errors' ][ 0 ], 'Bot with url that could not be resolved must have a "could_not_resolve" error' );
        }
        public function testNetworkUnreachable() {
            $result = $this->initiateAndGetErrors( CURLE_COULDNT_CONNECT );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught when curl responds with an error' );
            $this->assertEquals( 'could_not_connect', $result[ 'errors' ][ 0 ], 'Bot with url that could not be resolved must have a "could_not_connect" error' );
        }
        public function testRespondCode() {
            $user = $this->buildUser( 'vitsalis' );
            $bot = new GraderBot( $user );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;

            $curlConnectionMock->makeRespondWithErrorCode( 404 );

            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $caught = true;
            }

            $this->assertTrue( $caught, 'A GraderBotExcpetion must be caught when HTTP response code is not OK(200)' );
            $this->assertEquals( 'http_code_not_ok', $bot->errors[ 0 ], 'Bot whose HTTP response code is not OK(200) must have a "http_code_not_ok" error' );
        }
        public function testRespondInvalidJson() {
            $result = $this->initiateWithJsonAndGetErrors( '{ invalid_json }' );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotExcpetion must be caught when response has invalid json' );
            $this->assertEquals( 'invalid_json', $result[ 'errors' ][ 0 ], 'Bot who has invalid json as a response must have a "invalid_json" error' );
        }
        protected function initiateWithJsonAndGetErrors( $json ) {
            $user = $this->buildUser( 'vitsalis' );
            $bot = new GraderBot( $user );

            $curlConnectionMock = new CurlConnectionMock();
            $curlConnectionMock->makeRespondWith( $json );

            $bot->curlConnectionObject = $curlConnectionMock;

            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $caught = true;
            }

            return [
                'caught' => $caught,
                'errors' => $bot->errors
            ];
        }
        public function testRespondWithoutBotname() {
            $result = $this->initiateWithJsonAndGetErrors( json_encode( [
                'version' => '0.1.0',
                'username' => 'vitsalis'
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotExcpetion must be caught when response does not have a botname' );
            $this->assertEquals( 'botname_not_set', $result[ 'errors' ][ 0 ], 'Bot whose botname is not set must have a "botname_not_set" error' );
        }
        public function testRespondWithoutVersion() {

            $result = $this->initiateWithJsonAndGetErrors( json_encode( [
                'botname' => 'suprabot',
                'username' => 'vitsalis'
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotExcpetion must be caught when response does not have a version' );
            $this->assertEquals( 'version_not_set', $result[ 'errors' ][ 0 ], 'Bot whose version is not set must have a "version_not_set" error' );
        }
        public function testRespondWithoutUsername() {
            $result = $this->initiateWithJsonAndGetErrors( json_encode( [
                'botname' => 'suprabot',
                'version' => '0.1.0'
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotExcpetion must be caught when response does not have a username' );
            $this->assertEquals( 'username_not_set', $result[ 'errors' ][ 0 ], 'Bot whose username is not set must have a "username_not_set" error' );
        }
    }

    return new GraderBotTest();
?>
