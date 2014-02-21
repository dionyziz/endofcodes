<?php
    require_once 'models/grader/grader.php';

    class GameController extends ControllerBase {
        public function create() {
            $game = new Game();
            $game->save();
            $users = User::findAll();
            $grader = new Grader( $users, $game );
            $grader->initiateBots();
            $grader->initiate();
            $grader->createGame();

            require 'views/game/view.php';
        }
        public function createView() {
            require 'views/game/create.php';
        }
        public function update( $gameid ) {
            $game = new Game( $gameid );

            $grader = new Grader( $game );
            $grader->nextRound();
            $grader->game->nextRound();
        }
        public function updateView( $gameid ) {
            require 'views/game/update.php';
        }
    }
?>
