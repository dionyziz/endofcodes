<?php
    require_once 'models/grader/grader.php';

    class GameController extends ControllerBase {
        public function create() {
            $game = new Game();
            $game->save();
            $users = User::findAll();
            $grader = new Grader( $users, $game );
            $grader->initiate();
            $grader->createGame();
        }
        public function createView() {
            require 'views/game/create.php';
        }
        public function update( $grader ) {
            $grader->nextRound();
            $grader->game->nextRound();
        }
    }
?>
