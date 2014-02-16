<?php
    require_once 'models/grader/grader.php';
    require_once 'tests/models/grader/bot.php';

    class GraderBotMock implements GraderBotInterface {
        public $boturlValid;
        public $gameResponseValid;
        public $roundResponseValid;
        public $user;

        public function __construct( $user ) {
            $this->user = $user;
        }
        public function sendInitiateRequest() {
            if ( !$this->boturlValid ) {
                throw new GraderBotException( 'initiate_error' );
            }
        }
        public function sendGameRequest( $game ) {
            if ( !$this->gameResponseValid ) {
                throw new GraderBotException( 'game_error' );
            }
        }
        public function sendRoundRequest( $round ) {
            if ( !$this->roundResponseValid ) {
                throw new GraderBotException( 'round_error' );
            }
            return array();
        }
    }

    class GraderTest extends UnitTestWithFixtures {
        public function testIncludeUsersWithInvalidBots() {
            $game = new Game();
            $game->save();
            $users = [ $this->buildUser( 'dionyziz' ), $this->buildUser( 'pkakelas' ) ];
            $users[ 0 ]->boturl = 'koko';
            $users[ 1 ]->boturl = 'lala';
            $grader = new Grader( $users, $game, 'GraderBotMock' );
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = false;
            }
            $grader->initiate();

            $this->assertEquals( 2, count( $grader->bots ), 'The number of bots must be the same as the number of users' );
            $this->assertEquals( 0, count( $grader->registeredBots ), 'The number of registered bots must be the same as the number of valid bots' );
            $this->assertEquals( count( $grader->registeredBots ), count( $grader->registeredUsers ), 'The number of registered users must be the same with the number of registered bots' );

            $error1 = Error::findErrorsByGameAndUser( $game->id, $users[ 0 ]->id );
            $error2 = Error::findErrorsByGameAndUser( $game->id, $users[ 1 ]->id );

            $this->assertTrue( !empty( $error1 ), 'A user with invalid boturl must have errors' );
            $this->assertTrue( !empty( $error2 ), 'A user with invalid boturl must have errors' );

            $this->assertEquals( 1, count( $error1 ), 'A user must get one error if they have an invalid boturl' );
            $this->assertEquals( 1, count( $error2 ), 'A user must get one error if they have an invalid boturl' );

            $this->assertEquals( 'initiate_error', $error1[ 0 ]->error, 'A user must get a "initiate_error" error if he has an invalid boturl' );
            $this->assertEquals( 'initiate_error', $error2[ 0 ]->error, 'A user must get a "initiate_error" error if he has an invalid boturl' );
        }
        public function testIncludeUsersWithValidBots() {
            $game = new Game();
            $game->save();
            $users = [ $this->buildUser( 'dionyziz' ), $this->buildUser( 'pkakelas' ) ];
            $users[ 0 ]->boturl = 'localhost/endofcodes/bots/php';
            $users[ 1 ]->boturl = 'localhost/endofcodes/bots/php';
            $grader = new Grader( $users, $game, 'GraderBotMock' );
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = true;
            }
            $grader->initiate();

            $this->assertEquals( 2, count( $grader->registeredBots ), 'The number of registered bots must be the same as the number of valid bots' );
            $this->assertEquals( count( $grader->registeredBots ), count( $grader->registeredUsers ), 'The number of registered users must be the same with the number of registered bots' );

            $error1 = Error::findErrorsByGameAndUser( $game->id, $users[ 0 ]->id );
            $error2 = Error::findErrorsByGameAndUser( $game->id, $users[ 1 ]->id );

            $this->assertTrue( empty( $error1 ), 'A user with valid boturl must not have errors' );
            $this->assertTrue( empty( $error2 ), 'A user with valid boturl must not have errors' );
        }
        public function testCreateGameInvalidResponse() {
            $game = new Game();
            $game->save();
            $users = [ $this->buildUser( 'dionyziz' ), $this->buildUser( 'pkakelas' ) ];
            $grader = new Grader( $users, $game, 'GraderBotMock' );
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = true;
                $bot->gameResponseValid = false;
            }
            $grader->initiate();
            $grader->createGame();

            $error1 = Error::findErrorsByGameAndUser( $game->id, $users[ 0 ]->id );
            $error2 = Error::findErrorsByGameAndUser( $game->id, $users[ 1 ]->id );

            $this->assertTrue( !empty( $error1 ), 'A user with invalid game response must have errors' );
            $this->assertTrue( !empty( $error2 ), 'A user with invalid game response must have errors' );

            $this->assertEquals( 1, count( $error1 ), 'A user must get one error if they have an invalid game response' );
            $this->assertEquals( 1, count( $error2 ), 'A user must get one error if they have an invalid game response' );

            $this->assertEquals( 'game_error', $error1[ 0 ]->error, 'A user must get a "game_error" error if he has an invalid game response' );
            $this->assertEquals( 'game_error', $error2[ 0 ]->error, 'A user must get a "game_error" error if he has an invalid game response' );
        }
        public function testCreateGameValidResponse() {
            $game = new Game(); $game->save();
            $users = [ $this->buildUser( 'dionyziz' ), $this->buildUser( 'pkakelas' ) ];
            $grader = new Grader( $users, $game, 'GraderBotMock' );
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = true;
                $bot->gameResponseValid = true;
            }
            $grader->initiate();
            $grader->createGame();

            $error1 = Error::findErrorsByGameAndUser( $game->id, $users[ 0 ]->id );
            $error2 = Error::findErrorsByGameAndUser( $game->id, $users[ 1 ]->id );

            $this->assertTrue( empty( $error1 ), 'A user with valid game response must not have errors' );
            $this->assertTrue( empty( $error2 ), 'A user with valid game response must not have errors' );
        }
        public function testNextRoundInvalidResponse() {
            $game = new Game();
            $game->save();
            $users = [ $this->buildUser( 'dionyziz' ), $this->buildUser( 'pkakelas' ) ];
            $grader = new Grader( $users, $game, 'GraderBotMock' );
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = true;
                $bot->gameResponseValid = true;
                $bot->roundResponseValid = false;
            }
            $grader->initiate();
            $grader->createGame();
            $grader->nextRound();

            $error1 = Error::findErrorsByGameAndUser( $game->id, $users[ 0 ]->id );
            $error2 = Error::findErrorsByGameAndUser( $game->id, $users[ 1 ]->id );

            $this->assertTrue( !empty( $error1 ), 'A user with invalid round response must have errors' );
            $this->assertTrue( !empty( $error2 ), 'A user with invalid round response must have errors' );

            $this->assertEquals( 1, count( $error1 ), 'A user must get one error if they have an invalid round response' );
            $this->assertEquals( 1, count( $error2 ), 'A user must get one error if they have an invalid round response' );

            $this->assertEquals( 'round_error', $error1[ 0 ]->error, 'A user must get a "round_error" error if he has an invalid round response' );
            $this->assertEquals( 'round_error', $error2[ 0 ]->error, 'A user must get a "round_error" error if he has an invalid round response' );
        }
        public function testNextRoundValidResponse() {
            $game = new Game();
            $game->save();
            $users = [ $this->buildUser( 'dionyziz' ), $this->buildUser( 'pkakelas' ) ];
            $grader = new Grader( $users, $game, 'GraderBotMock' );
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = true;
                $bot->gameResponseValid = true;
                $bot->roundResponseValid = true;
            }
            $grader->initiate();
            $grader->createGame();
            $grader->nextRound();

            $error1 = Error::findErrorsByGameAndUser( $game->id, $users[ 0 ]->id );
            $error2 = Error::findErrorsByGameAndUser( $game->id, $users[ 1 ]->id );

            $this->assertTrue( empty( $error1 ), 'A user with valid round response must not have errors' );
            $this->assertTrue( empty( $error2 ), 'A user with valid round response must not have errors' );
        }
        public function testRoundResolution() {
            $users = [ $this->buildUser( 'vitsalis' ), $this->buildUser( 'dionyziz' ) ];
            $game = new Game();
            $game->users = $users;
            $game->initiateAttributes();
            $game->id = 1;
            $creature1 = $this->buildCreature( 0, 1, 1, $users[ 0 ], $game );
            $creature2 = $this->buildCreature( 1, 2, 2, $users[ 1 ], $game );
            $round = new Round();
            $round->creatures = [ $creature1, $creature2 ];
            $round->game = $game;
            $game->rounds[ 0 ] = $round;
            $grader = new Grader( $users, $game );
            $grader->registeredUsers = $users;
            $bot1 = new GraderBot( $users[ 0 ] );
            $bot1->game = $game;
            $bot1->curlConnectionObject = new CurlConnectionMock();
            $bot1->curlConnectionObject->makeRespondWith( json_encode( [
                'intent' => [
                    [
                        'creatureid' => 0,
                        'action' => 'MOVE',
                        'direction' => 'NORTH'
                    ]
                ]
            ] ) );
            $bot2 = new GraderBot( $users[ 1 ] );
            $bot2->game = $game;
            $bot2->curlConnectionObject = new CurlConnectionMock();
            $bot2->curlConnectionObject->makeRespondWith( json_encode( [
                'intent' => [
                    [
                        'creatureid' => 1,
                        'action' => 'MOVE',
                        'direction' => 'NORTH'
                    ]
                ]
            ] ) );

            $grader->registeredBots = [ $bot1, $bot2 ];
            $grader->nextRound();

            $this->assertTrue( isset( $game->rounds[ 1 ] ), 'A new round must be created after nextRound is called' );
            $this->assertTrue( is_object( $game->rounds[ 1 ] ), 'The new round must be an object' );

            $this->assertTrue( isset( $game->rounds[ 1 ]->creatures ), 'the new round must have creatures' );
            $this->assertTrue( is_array( $game->rounds[ 1 ]->creatures ), 'the new round must have a creatures array' );

            $newCreature1 = $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = $game->rounds[ 1 ]->creatures[ 1 ];

            $this->assertEquals( $creature1->id, $newCreature1->id, 'The creature must not change id' );
            $this->assertEquals( $creature2->id, $newCreature2->id, 'The creature must not change id' );

            $this->assertEquals( 1, $newCreature1->locationx, 'A creature must move in x axis if it is specified' );
            $this->assertEquals( 2, $newCreature1->locationy, 'A creature must move in y axis if it is specified' );

            $this->assertEquals( 2, $newCreature2->locationx, 'A creature must move in x axis if it is specified' );
            $this->assertEquals( 3, $newCreature2->locationy, 'A creature must move in y axis if it is specified' );
        }
    }
    return new GraderTest();
?>
