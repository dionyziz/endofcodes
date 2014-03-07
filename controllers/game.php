<?php
    require_once 'models/grader/grader.php';

    class GameController extends ControllerBase {
        public function create() {
            $game = new Game();
            $game->save();
            $users = User::findAll();
            $grader = new Grader( $game, $users );
            $grader->initiateBots();
            $grader->initiate();
            $grader->createGame();

            go( 'game', 'update', [ 'gameid' => $game->id ] );
        }
        public function createView() {
            require 'views/game/create.php';
        }
        public function update( $gameid ) {
            $game = new Game( $gameid );

            $grader = new Grader( $game );
            try {
                $grader->nextRound();
            }
            catch ( WinnerException $e ) {
                die( 'We have a winner: ' . $e->winnerid );
            }

            go( 'game', 'update', compact( 'gameid' ) );
        }
        public function view( $gameid ) {
            try {
                $game = new Game( $gameid );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException();
            }
            $round = $game->getCurrentRound();
            $creatures = $round->creatures;
            require 'views/game/view.php';
        }
        public function updateView( $gameid ) {
            require 'views/game/update.php';
        }
    }
?>
