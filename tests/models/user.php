<?php
    include_once 'models/user.php';
    include_once 'models/country.php';
    
    class UserTest extends UnitTestWithUser {
        public function testCreate() {
            $user = new User();
            $user->username = 'pkakelas';
            $user->password = 'secret1234';
            $user->email = 'pkakelas@gmail.com';
            $user->save();
            $passwordSuccess = $user->authenticatesWithPassword( 'secret1234' );
            $this->assertEquals( true, $passwordSuccess, 'Password must be the one associated during creation' );
            $this->assertEquals( 'pkakelas', $user->username, 'Username must be the one associated during creation' );
            $this->assertEquals( 'pkakelas@gmail.com', $user->email, 'Email must be the one associated during creation' );
        }
        public function testDelete() {
            $user = $this->buildUser( 'pkakelas' );
            $user->delete();
            try {
                $user = User::findByUsername( 'pkakelas' );
                $success = 0;
            }
            catch ( ModelNotFoundException $e ) {
                $success = 1;
            }
            $this->assertEquals( 1, $success, 'The user must be deleted when the $user->delete() function runs' );
        }
        public function testPasswordChange() {
            $user = $this->buildUser( 'pkakelas' );
            $password = $user->password;
            $user->password = 'newsecret1234';
            $user->save();
            $success = $user->authenticatesWithPassword( 'newsecret1234' );
            $this->assertEquals( true, $success, 'Password must be the one associated during update' );
        }
        public function testEmailChange() {
            $user = $this->buildUser( 'pkakelas' );
            $user->email = 'pkakelas2@gmail.com';
            $user->save();
            $this->assertEquals( 'pkakelas2@gmail.com', $user->email, 'Email must be the one associated during update' );
        }
        public function testSetCountry() {
            $user = $this->buildUser( 'pkakelas' );
            $user->country = new Country( 1 );
            $this->assertEquals( 1, $user->country->id, 'Country must be the one associated during update' );
        }
    }

    return new UserTest();
?>
