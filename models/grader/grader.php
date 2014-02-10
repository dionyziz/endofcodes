<?php
    include_once 'models/curl.php';
    include_once 'models/grader/serializer.php';
    include_once 'models/grader/bot.php';

    class Grader {
        public $registeredBots;
        protected $game;
        protected $users;
        protected $bots = [];

        public function __construct( $users, $game ) {
            $this->users = $users;
            foreach ( $users as $user ) {
                $this->bots[] = new GraderBot( $user );
            }
            $this->game = $game;
        }
        public function initiate() {
            $this->registeredBots = [];
            foreach ( $this->bots as $bot ) {
                try {
                    $bot->sendInitiateRequest();
                    $this->registeredBots[] = $bot;
                }
                catch ( GraderBotException $e ) {
                }
            }
        }
        public function createGame() {
            $this->game->users = $this->users;
            $this->game->save();

            foreach ( $this->registeredBots as $bot ) {
                $bot->sendGameRequest( $this->game );
            }
        }
        public function nextRound() {
            $round = $this->game->getCurrentRound();

            foreach ( $this->registeredBots as $bot ) {
                $bot->sendRoundRequest( $round );
            }
        }
    }
?>
