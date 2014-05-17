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
            if ( $game->ended ) {
                go();
            }

            go( 'game', 'update', [ 'gameid' => $game->id ] );
        }
        public function createView() {
            require 'views/game/create.php';
        }
        public function update( $gameid, $finishit = false ) {
            try {
                $game = new Game( $gameid );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException( 'There is no game with the specified gameid' );
            }

            if ( $game->ended ) {
                go();
            }

            $grader = new Grader( $game );
            do {
                if ( $game->ended ) {
                    go();
                }

                $grader->nextRound();
            } while ( $finishit );

            go( 'game', 'update', compact( 'gameid' ) );
        }
        public function view( $gameid, $roundid = false, $all = false ) {
            try {
                $game = new Game( $gameid );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException( 'There is no game with the specified gameid' );
            }
            if ( $roundid !== false ) {
                if ( !isset( $game->rounds[ $roundid ] ) ) {
                    throw new HTTPNotFoundException( 'The game specified does not contain the specified round' );
                }
                $round = $game->rounds[ $roundid ];
            }
            else {
                $round = $game->getCurrentRound();
            }
            if ( isset( $_SESSION[ 'user' ] ) ) {
                $currentUser = $_SESSION[ 'user' ];
            }
            switch ( $this->outputFormat ) {
                case 'json':
                case 'text':
                    require_once 'models/grader/serializer.php';
                    if ( $all ) {
                        $mapJson = GraderSerializer::serializeRoundList( $game->rounds );
                    }
                    else {
                        $mapJson = GraderSerializer::serializeCreatureList( $round->creatures );
                    }
                    require 'views/game/view.json.php';
                    break;
                default:
                    require 'views/game/view.php';
            }
        }
        public function updateView( $gameid ) {
            try {
                $game = new Game( $gameid );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException( 'There is no game with the specified gameid' );
            }
            require 'views/game/update.php';
        }
    }
?>
