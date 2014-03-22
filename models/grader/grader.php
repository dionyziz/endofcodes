<?php
    require_once 'models/curl.php';
    require_once 'models/grader/serializer.php';
    require_once 'models/grader/bot.php';
    require_once 'models/error.php';

    class Grader {
        public $registeredBots;
        public $registeredUsers;
        public $bots = [];
        public $graderBotClass = 'GraderBot';
        protected $game;
        protected $users;
        protected $botsInitiated = false;

        public function __construct( Game $game, $users = false ) {
            assert( $game->exists/*, 'Game must exist when grader is constructed'*/ );
            
            if ( count( $game->rounds ) ) {
                // already existing game
                assert( $users === false/*, 'game already has users since genesis has run'*/ );
                assert( isset( $game->users )/*, 'game must have users'*/ );
                $this->registeredUsers = $game->users;
                foreach ( $game->users as $user ) {
                    $bot = new GraderBot( $user );
                    $bot->game = $game;
                    $this->registeredBots[] = $bot;
                }
            }
            else {
                // new game
                assert( is_array( $users )/*, 'Users must be an array'*/ );
                $this->users = $users;
                foreach ( $this->users as $user ) {
                    assert( $user instanceof User/*, '$grader->users is not a collection of users'*/ );
                }
            }
            $this->game = $game;
        }
        public function initiateBots() {
            foreach ( $this->users as $user ) {
                $bot = new $this->graderBotClass( $user );
                $bot->game = $this->game;
                $this->bots[] = $bot;
            }

            $this->botsInitiated = true;
        }
        public function initiate() {
            assert( $this->botsInitiated/*, 'Bots should be initiated before grader initiates'*/ );

            $this->registeredBots = [];
            $this->registeredUsers = [];

            foreach ( $this->bots as $bot ) {
                try {
                    $bot->sendInitiateRequest();
                    $this->registeredBots[] = $bot;
                    $bot->game = $this->game;
                    $this->registeredUsers[] = $bot->user;
                }
                catch ( GraderBotException $e ) {
                }
            }
        }
        public function createGame() {
            $this->game->users = $this->registeredUsers;
            $this->game->initiateAttributes();
            $this->game->save();
            $this->game->genesis();

            foreach ( $this->registeredBots as $bot ) {
                try {
                    $bot->sendGameRequest( $this->game );
                }
                catch ( GraderBotException $e ) {
                }
            }
        }
        public function nextRound() {
            assert( $this->game instanceof Game/*, '$this->game must be an instance of game when we create a new round'*/ );
            $this->game->beforeNextRound();
            $round = $this->game->getCurrentRound();

            foreach ( $this->registeredBots as $bot ) {
                try {
                    $creatureCollection = $bot->sendRoundRequest( $round );
                    foreach ( $creatureCollection as $creatureIntent ) {
                        $round->creatures[ $creatureIntent->id ]->intent = $creatureIntent->intent;
                    }
                }
                catch ( GraderBotException $e ) {
                }
            }

            $this->game->nextRound();

            foreach ( $this->game->getCurrentRound()->errors as $userid => $errors ) {
                foreach ( $errors as $errorInfo ) {
                    $error = new Error();
                    $error->game = $this->game;
                    $error->user = new User( $userid );
                    $error->description = $errorInfo[ 'description' ];
                    $error->actual = $errorInfo[ 'actual' ];
                    $error->expected = $errorInfo[ 'expected' ];
                    $error->save();
                }
            }
        }
    }
?>
