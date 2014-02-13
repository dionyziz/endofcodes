<?php
    include_once 'models/curl.php';
    include_once 'models/grader/serializer.php';
    include_once 'models/grader/bot.php';
    include_once 'models/grader/error.php';

    class Grader {
        public $registeredBots;
        public $registeredUsers;
        protected $game;
        protected $users;
        public $bots = [];

        public function __construct( $users, $game, $graderBotObject = 'GraderBot' ) {
            $this->users = $users;
            foreach ( $users as $user ) {
                $this->bots[] = new $graderBotObject( $user );
            }
            $this->game = $game;
        }
        public function initiate() {
            $this->registeredBots = [];
            $this->registeredUsers = [];

            foreach ( $this->bots as $bot ) {
                try {
                    $bot->sendInitiateRequest();
                    $this->registeredBots[] = $bot;
                    $this->registeredUsers[] = $bot->user;
                }
                catch ( GraderBotException $e ) {
                    $error = new Error( $this->game->id, $bot->user->id, $e->error );
                    $error->save();
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
                    $error = new Error( $this->game->id, $bot->user->id, $e->error );
                    $error->save();
                }
            }
        }
        public function nextRound() {
            $round = $this->game->getCurrentRound();

            foreach ( $this->registeredBots as $bot ) {
                try {
                    $bot->sendRoundRequest( $round );
                }
                catch ( GraderBotException $e ) {
                    $error = new Error( $this->game->id, $bot->user->id, $e->error );
                    $error->save();
                }
            }
        }
    }
?>
