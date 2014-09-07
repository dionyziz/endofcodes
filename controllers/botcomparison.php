<?php
    class BotComparisonController extends AuthenticatedController {
        public $environment = 'test';

        public function create( $url1 = '', $url2 = '' ) {
            require_once 'models/country.php';
            require_once 'models/game.php';

            if ( empty( $url1 ) || empty( $url2 ) ) {
                go( 'botcomparison', 'create', [ 'emptyUrl' => true ] );
            }

            $urls = [ $url1, $url2 ];

            for ( $i = 1; $i < 3; ++$i ) {
                try {
                    $user = User::findByUsername( "bot$i" );
                }
                catch ( ModelNotFoundException $e ) {
                    $user = new User();
                    $user->username = "bot$i";
                    $user->password = 'salalala';
                    $user->email = "bot$i@example.org";
                    $user->country = new Country(1);
                    $user->dateOfBirth = [ 'day' => 1, 'month' => 1, 'year' => 1970 ];
                    $user->save();
                }
                try {
                    $user->setBoturl( $urls[ $i - 1 ] );
                }
                catch ( ModelValidationException $e ) {
                    go( 'botcomparison', 'create', [ 'bot_fail' => true, 'whichBot' => $i, 'errorid' => $e->error ] );
                }
            }
            shell_exec( 'ENVIRONMENT=test gamescript.sh' );
            try {
                $game = Game::getLastGame();
                $ratings = $game->getGlobalRatings();
            }
            catch ( ModelNotFoundException $e ) {
            }
            $creatures = $user->lastGameCreaturesCount();
                $winner = $ratings[1][0]->username;
                flash( $winner . ' won with ' . $creatures . ' creatures.' );
                go( 'botcomparison', 'create' );
        }
        public function createView( $emptyUrl, $bot_fail, $errorid = false, $whichBot = '' ) {
            require_once 'models/error.php';

            if ( $errorid !== false ) {
                $error = new Error( $errorid );
            }

            require_once 'models/grader/bot.php';
            require_once 'views/bot/comparison.php';
        }
    }
?>
