<?php
    require_once 'models/game.php';
    require_once 'models/user.php';
    require_once 'models/round.php';
    require_once 'models/creature.php';
    require_once 'models/country.php';

    class UnitTestWithFixtures extends UnitTest {
        protected function buildUser( $username, $email = '', $boturl = '' ) {
            global $config;

            $user = new User();
            $user->username = $username;
            $user->password = 'secret1234';
            $user->email = "$username@gmail.com";
            $user->name = "Frank";
            $user->surname = 'Sinatra';
            $user->website = "https://example.com";
            $user->github = "https://github.com/$username";
            $user->boturl = $config[ 'base' ] . 'bots/php';

            if ( $email == '' ) {
                $email = "$username@gmail.com";
            }
            $user->email = $email;

            if ( $boturl == '' ) {
                $boturl = $config[ 'base' ] . 'bots/php';
            }
            $user->boturl = $boturl;

            $user->save();

            return $user;
        }
        protected function buildBot( $username ) {
            $user = $this->buildUser( $username );
            $game = $this->buildGame();

            $bot = new GraderBot( $user );
            $bot->game = $game;

            return $bot;
        }
        protected function buildCountry( $name, $shortname ) {
            $country = new Country();
            $country->name = $name;
            $country->shortname = $shortname;
            $country->save();

            return $country;
        }
        protected function buildCreature( $id, $x, $y, $user, $game = false ) {
            $creature = new Creature();
            $creature->locationx = $x;
            $creature->locationy = $y;
            $creature->hp = 10;
            $creature->user = $user;
            $creature->id = $id;
            if ( $game === false ) {
                $creature->game = new Game();
                $creature->game->id = 1;
            }
            else {
                $creature->game = $game;
            }
            $creature->intent = new Intent( ACTION_NONE, DIRECTION_NONE );
            $creature->save();

            return $creature;
        }
        protected function buildRound() {
            $round = new Round();
            $round->id = 1;
            $creature1 = $this->buildCreature( 1, 1, 2, $this->buildUser( 'vitsalis' ) );
            $creature2 = $this->buildCreature( 2, 3, 4, $this->buildUser( 'pkakelas' ) );
            $round->creatures = [
                $creature1->id => $creature1,
                $creature2->id => $creature2
            ];
            return $round;
        }
        protected function buildGameWithRoundAndCreatures() {
            $user1 = $this->buildUser( 'vitsalis' );
            $user2 = $this->buildUser( 'vitsalissister' );
            $user3 = $this->buildUser( 'vitsalissisterssecondcousin' );

            $creature1 = $this->buildCreature( 1, 1, 1, $user1 );
            $creature2 = $this->buildCreature( 2, 2, 2, $user2 );
            $creature3 = $this->buildCreature( 3, 3, 3, $user3 );

            $round1 = new Round();
            $round1->id = 0;
            $round1->creatures = [ 1 => $creature1, 2 => $creature2, 3 => $creature3 ];

            $creature2Clone = clone $creature2;
            $creature3Clone = clone $creature3;
            $creature3Clone->alive = false;
            $round2 = new Round();
            $round2->id = 1;
            $round2->creatures = [ 1 => $creature1, 2 => $creature2Clone, 3 => $creature3Clone ];

            $game = new Game();
            $game->users = [ 1 => $user1, 2 => $user2, 3 => $user3 ];
            $game->rounds = [ 0 => $round1, 1 => $round2 ];
            $game->save();

            $round1->game = $round2->game = $game;
            $round1->save();
            $round2->save();

            return $game;
        }
        protected function buildGame() {
            $game = new Game();
            for ( $i = 1; $i <= 4; ++$i ) {
                $user = $this->buildUser( 'a' . $i );
                $game->users[ $user->id ] = $user; 
            }
            $game->save();
            return $game;
        }
        protected function buildError( $description, $actual, $expected, $user, $game = false ) {
            $error = new Error();
            $error->description = $description;
            $error->actual = $actual;
            $error->expected = $expected;
            $error->user = $user;
            if ( $game !== false ) {
                $error->game = $game;
            }
            $error->save();
            return $error;
        }
        protected function rrmdir( $dir ) {
            if ( is_dir( $dir ) ) {
                $objects = scandir( $dir );
                foreach ( $objects as $object ) {
                    if ( $object != "." && $object != ".." ) {
                        if ( filetype( $dir . "/" . $object ) == "dir" ) {
                            $this->rrmdir( $dir . "/" . $object );
                        }
                        else {
                            unlink( $dir . "/" . $object );
                        }
                    }
                }
                rmdir( $dir );
            }
        }
    }
?>
