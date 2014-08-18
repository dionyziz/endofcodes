<?php
    class BotComparisonController extends AuthenticatedController {
        public $environment = 'test';

        public function create( $url1 = '', $url2 = '' ) {
            require_once 'models/country.php';
            require_once 'models/game.php';

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
                $user->setBoturl( $urls[ $i - 1 ] );
            }
            echo 'ready to run';
            echo shell_exec( 'ENVIRONMENT=test gamescript.sh' );
            try {
                $game = Game::getLastGame();
                $ratings = $game->getGlobalRatings();
            }
            catch ( ModelNotFoundException $e ) {
            }
            $creatures = $user->lastGameCreaturesCount();
                $winner = $ratings[1][0]->username;
                echo $winner . ' won with ' . $creatures . ' creatures.';
        }
    }
?>

