<?php
    class UnitTestWithUser extends UnitTest {
        protected function buildUser( $username ) {
            $user = new User();
            $user->username = $username;
            $user->password = 'secret1234';
            $user->email = "$username@gmail.com";
            $user->save();
            return $user;
        }
    }
?>
