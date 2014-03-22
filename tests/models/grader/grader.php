<?php
    require_once 'models/grader/grader.php';
    require_once 'tests/models/grader/bot.php';
    require_once 'models/error.php';

    class GraderBotMock implements GraderBotInterface {
        public $boturlValid;
        public $gameResponseValid;
        public $roundResponseValid;
        public $user;
        public $roundReturnValue = [];

        public function __construct( User $user ) {
            $this->user = $user;
        }
        protected function throwError( $description ) {
            $error = new Error();
            $error->description = $description;
            $error->user = $this->user;
            throw new GraderBotException( $error );
        }
        public function sendInitiateRequest() {
            if ( !$this->boturlValid ) {
                $this->throwError( 'initiate_error' );
            }
        }
        public function sendGameRequest( Game $game ) {
            if ( !$this->gameResponseValid ) {
                $this->throwError( 'game_error' );
            }
        }
        public function sendRoundRequest( Round $round ) {
            if ( !$this->roundResponseValid ) {
                $this->throwError( 'round_error' );
            }
            return $this->roundReturnValue;
        }
    }

    class GraderTest extends UnitTestWithFixtures {
        protected $users;
        protected $game;

        public function setUp() {
            $user1 = $this->buildUser( 'dionyziz' );
            $user2 = $this->buildUser( 'pkakelas' );

            $this->users = [
                $user1->id => $user1,
                $user2->id => $user2
            ];

            $this->game = new Game();
            $this->game->save();
        }
        public function testLoadGraderFromExistingGame() {
            $round = new Round();
            $round->id = 0;
            $round->game = $this->game;
            $user = $this->users[ 1 ];
            $this->game->users = [ $user->id => $user ];
            $round->creatures = [ 1 => $this->buildCreature( 1, 0, 0, $user ) ];
            $round->save();
            $this->game->rounds[ 0 ] = $round;

            $grader = new Grader( $this->game );

            $this->assertTrue( isset( $grader->registeredUsers ), 'Constructor of grader must set the users' );
            $this->assertEquals( 1, count( $grader->registeredUsers ), 'Constructor of grader must find the correct number of users' );
            $this->assertEquals( $user->id, $grader->registeredUsers[ 1 ]->id, 'Constructor of grader must find the correct users' );
        }
        public function testIncludeUsersWithInvalidBots() {
            $grader = new Grader( $this->game, $this->users );
            $grader->graderBotClass = 'GraderBotMock';
            $grader->initiateBots();
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = false;
            }
            $grader->initiate();

            $this->assertEquals( 2, count( $grader->bots ), 'The number of bots must be the same as the number of users' );
            $this->assertEquals( 0, count( $grader->registeredBots ), 'The number of registered bots must be the same as the number of valid bots' );
            $this->assertEquals( count( $grader->registeredBots ), count( $grader->registeredUsers ), 'The number of registered users must be the same with the number of registered bots' );
        }
        public function testIncludeUsersWithValidBots() {
            $grader = new Grader( $this->game, $this->users );
            $grader->graderBotClass = 'GraderBotMock';
            $grader->initiateBots();
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = true;
            }
            $grader->initiate();

            $this->assertEquals( 2, count( $grader->registeredBots ), 'The number of registered bots must be the same as the number of valid bots' );
            $this->assertEquals( count( $grader->registeredBots ), count( $grader->registeredUsers ), 'The number of registered users must be the same with the number of registered bots' );
        }
        public function testRoundResolution() {
            $game = new Game();
            $game->users = $this->users;
            $game->initiateAttributes();
            $game->id = 1;
            $game->exists = true;
            $creature1 = $this->buildCreature( 1, 1, 1, $this->users[ 1 ], $game );
            $creature2 = $this->buildCreature( 2, 2, 2, $this->users[ 2 ], $game );
            $round = new Round();
            $round->creatures = [ 1 => $creature1, 2 => $creature2 ];
            $round->game = $game;
            $game->rounds[ 0 ] = $round;
            $game->users = $this->users;
            $grader = new Grader( $game );
            $grader->registeredUsers = $this->users;
            $bot1 = new GraderBot( $this->users[ 1 ] );
            $bot1->game = $game;
            $bot1->curlConnectionObject = new CurlConnectionMock();
            $bot1->curlConnectionObject->makeRespondWith( json_encode( [
                'intent' => [
                    [
                        'creatureid' => 1,
                        'action' => 'MOVE',
                        'direction' => 'NORTH'
                    ]
                ]
            ] ) );
            $bot2 = new GraderBot( $this->users[ 2 ] );
            $bot2->game = $game;
            $bot2->curlConnectionObject = new CurlConnectionMock();
            $bot2->curlConnectionObject->makeRespondWith( json_encode( [
                'intent' => [
                    [
                        'creatureid' => 2,
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

            $newCreature1 = $game->rounds[ 1 ]->creatures[ 1 ];
            $newCreature2 = $game->rounds[ 1 ]->creatures[ 2 ];

            $this->assertEquals( $creature1->id, $newCreature1->id, 'The creature must not change id' );
            $this->assertEquals( $creature2->id, $newCreature2->id, 'The creature must not change id' );

            $this->assertEquals( 1, $newCreature1->locationx, 'A creature must move in x axis if it is specified' );
            $this->assertEquals( 2, $newCreature1->locationy, 'A creature must move in y axis if it is specified' );

            $this->assertEquals( 2, $newCreature2->locationx, 'A creature must move in x axis if it is specified' );
            $this->assertEquals( 3, $newCreature2->locationy, 'A creature must move in y axis if it is specified' );
        }
        protected function buildGameWithUserAndCreatures() {
            $game = new Game();
            $game->save();
            $game->height = 100;
            $game->width = 100;
            $game->users = [ 1 => $this->users[ 1 ], 2 => $this->users[ 2 ] ];
            $game->rounds[ 0 ] = new Round();
            $game->rounds[ 0 ]->id = 0;
            $game->rounds[ 0 ]->creatures = [
                1 => $this->buildCreature( 1, 1, 1, $this->users[ 1 ], $game ),
                2 => $this->buildCreature( 2, 3, 3, $this->users[ 2 ], $game )
            ];
            $game->rounds[ 0 ]->game = $game;

            return $game;
        }
        public function testFindBotsFromGame() {
            $game = $this->buildGameWithUserAndCreatures();
            $grader = new Grader( $game );

            $this->assertTrue( isset( $grader->registeredUsers ), "Grader must get its users from the game" );
            $this->assertTrue( isset( $grader->registeredBots ), "Grader must get its bots from the game" );

            $this->assertEquals( $game->users[ 1 ]->id, $grader->registeredUsers[ 1 ]->id, 'Grader must get valid users from the game' );
            $this->assertEquals( $game->users[ 1 ]->id, $grader->registeredBots[ 0 ]->user->id, 'Grader must get valid bots from the game' );
        }
        public function testGameEnd() {
            $game = $this->buildGameWithUserAndCreatures();
            unset( $game->rounds[ 0 ]->creatures[ 2 ] );
            $grader = new Grader( $game );

            $caught = false;
            $grader->nextRound();

            $this->assertTrue( $game->ended, 'The game must end if there is only one creature' );
        }
        public function testGameEndWithoutPlayers() {
            $game = $this->buildGameWithUserAndCreatures();
            unset( $game->rounds[ 0 ]->creatures[ 1 ] );
            unset( $game->rounds[ 0 ]->creatures[ 2 ] );
            $grader = new Grader( $game );

            $caught = false;
            $grader->nextRound();

            $this->assertTrue( $game->ended, 'The game must end if there are no creatures' );
        }
        public function testBotsIntentsClearedBeforeRound() {
            $game = $this->buildGameWithUserAndCreatures();
            $game->rounds[ 0 ]->creatures[ 1 ]->intent = new Intent( ACTION_MOVE, DIRECTION_NORTH );
            $grader = new Grader( $game );
            try {
                $grader->nextRound();
            }
            catch ( WinnerException $e ) {
            }
            foreach ( $game->rounds[ 0 ]->creatures as $creature ) {
                $this->assertEquals( ACTION_NONE, $creature->intent->action, 'Action must be set to ACTION_NONE before the next round starts' );
                $this->assertEquals( DIRECTION_NONE, $creature->intent->direction, 'Direction must be set to direction_NONE before the next round starts' );
            }
        }
        public function testErrorsSavedAfterResolution() {
            $game = $this->buildGameWithUserAndCreatures();
            $grader = new Grader( $game );
            foreach ( $grader->registeredBots as $key => $bot ) {
                unset( $grader->registeredBots[ $key ] );
            }
            foreach ( $game->users as $user ) {
                $bot = new GraderBotMock( $user );
                $bot->game = $game;
                $bot->roundResponseValid = true;
                if ( $user->id == 1 ) {
                    $game->rounds[ 0 ]->creatures[ 1 ]->locationx = 1;
                    $game->rounds[ 0 ]->creatures[ 1 ]->locationy = 1;
                    $creature = new Creature();
                    $creature->id = 1;
                    $creature->intent = new Intent( ACTION_ATTACK, DIRECTION_NORTH );
                    $bot->roundReturnValue = [ $creature ];
                }
                $grader->registeredBots[] = $bot;
            }
            $grader->nextRound();

            $errors = Error::findErrorsByGameAndUser( $game->id, 1 );

            $this->assertEquals( 1, count( $errors ), 'There must be only one error' );
            $this->assertSame( $game->id, $errors[ 0 ]->game->id, 'gameid must be saved correctly on error' );
            $this->assertSame( 1, $errors[ 0 ]->user->id, 'userid must be saved correctly on error' );
            $this->assertSame(
                "Tried to attack non existent creature with creature 1.",
                $errors[ 0 ]->description,
                'Description must be valid'
            );
            $this->assertSame(
                "not attack non existent creature",
                $errors[ 0 ]->expected,
                'Expected must be valid'
            );
            $this->assertSame(
                "attacked non existent creature",
                $errors[ 0 ]->actual,
                'Actual must be valid'
            );

            $errors = Error::findErrorsByGameAndUser( $game->id, 2 );
            $this->assertEquals( 0, count( $errors ), 'There must be no errors' );
        }
        public function testGameSetOnBots() {
            $game = $this->buildGame();
            $game->initiateAttributes();
            $game->genesis();

            $grader = new Grader( $game );
            foreach ( $grader->registeredBots as $bot ) {
                $this->assertSame( $game->id, $bot->game->id, 'The bot must have the valid game' );
            }
        }
    }
    return new GraderTest();
?>
