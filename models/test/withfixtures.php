<?php
    class UnitTestWithFixtures extends UnitTest {
        protected function buildUser( $username ) {
            global $config;

            $user = new User();
            $user->username = $username;
            $user->password = 'secret1234';
            $user->email = "$username@gmail.com";
            $user->boturl = $config[ 'base' ] . 'bot_prototype.php';
            $user->save();

            return $user;
        }
        protected function buildCountry( $name, $shortname ) {
            $country = new Country();
            $country->name = $name;
            $country->shortname = $shortname;
            $country->save();

            return $country;
        }
        protected function buildRound() {
            $round = new Round();
            $round->id = 1;
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature1->locationx = 1;
            $creature1->locationy = 2;
            $creature2->locationx = 3;
            $creature2->locationy = 4;
            $creature1->hp = 10;
            $creature2->hp = 11;
            $creature1->user = $this->buildUser( 'vitsalis' );
            $creature2->user = $this->buildUser( 'pkakelas' );
            $creature1->id = 1;
            $creature2->id = 2;
            $round->creatures = [ $creature1, $creature2 ];
            return $round;
        }
        protected function buildGame() {
            $game = new Game();
            for ( $i = 1; $i <= 4; ++$i ) {
                $game->users[] = $this->buildUser( $i );
            }
            $game->save();
            return $game;
        }
    }
?>
