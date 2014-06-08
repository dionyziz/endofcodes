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

            switch ( $this->outputFormat ) {
                case 'text':
                    echo $game->id;
                    break;
                case 'html':
                    if ( $game->ended ) {
                        go();
                    }
                    go( 'game', 'update', [ 'gameid' => $game->id ] );
                    break;
            }
        }
        public function createView() {
            require 'views/game/create.php';
        }
        public function update( $gameid, $finishit = false ) {
            try {
                $game = new Game( $gameid );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException( 'There is no game with the specified gameid (gameid = ' . $gameid . ')' );
            }

            $grader = new Grader( $game );
            do {
                if ( $game->ended ) {
                    if ( $this->outputFormat == 'html' ) {
                        go();
                    }
                    break;
                }

                $grader->nextRound();
            } while ( $finishit );

            if ( $this->outputFormat == 'html' ) {
                go( 'game', 'update', compact( 'gameid' ) );
            }
        }
        public function view( $gameid, $roundid = false, $all = false ) {
            try {
                if ( $roundid !== false ) {
                    $game = new Game( $gameid, $roundid );
                }
                else {
                    $game = new Game( $gameid );
                }
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException( 'There is no game with the specified gameid (gameid = ' . $gameid . ')' );
            }
            if ( $roundid !== false ) {
                if ( !isset( $game->rounds[ $roundid ] ) ) {
                    throw new HTTPNotFoundException( 'The game specified (gameid = ' . $gameid . ') does not contain the specified round (roundid = ' . $roundid . ')' );
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
                throw new HTTPNotFoundException( 'There is no game with the specified gameid (gameid = ' . $gameid . ')' );
            }
            require 'views/game/update.php';
        }
    }
?>
