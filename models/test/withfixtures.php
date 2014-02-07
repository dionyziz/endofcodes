<?php
    class UnitTestWithFixtures extends UnitTest {
        protected function buildUser( $username ) {
            $user = new User();
            $user->username = $username;
            $user->password = 'secret1234';
            $user->email = "$username@gmail.com";
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
    }
?>
