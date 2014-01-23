<?php
    include_once 'models/user.php';
    include_once 'models/country.php';
    
    class UserTest extends UnitTest {
        public function run() {
            $this->testCreate();
            $this->testPasswordChange();
            $this->testEmailChange();
            $this->testSetCountry();
            $this->testSetAge();
            $this->testDelete();
        }
        public function testCreate() {
            $user = new User();
            $user->username = 'pkakelas';
            $user->password = 'secret1234';
            $user->email = 'pkakelas@gmail.com';
            $user->save();
            $passwordSuccess = intval( $user->authenticatesWithPassword( 'secret1234' ) );
            $this->assertEquals( 1, $passwordSuccess, 'Password must be the one associated during creation' );
            $this->assertEquals( 'pkakelas', $user->username, 'Username must be the one associated during creation' );
            $this->assertEquals( 'pkakelas@gmail.com', $user->email, 'Email must be the one associated during creation' );
        }
        public function testDelete() {
            $user = User::findByUsername( 'pkakelas' );
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
            $user = User::findByUsername( 'pkakelas' );
            $password = $user->password;
            $user->password = 'newsecret1234';
            $user->save();
            $success = intval( $user->authenticatesWithPassword( 'newsecret1234' ) );
            $this->assertEquals( 1, $success, 'Password must be the one associated during update' );
        }
        public function testEmailChange() {
            $user = User::findByUsername( 'pkakelas' );
            $user->email = 'pkakelas2@gmail.com';
            $user->save();
            $this->assertEquals( 'pkakelas2@gmail.com', $user->email, 'Email must be the one associated during update' );
        }
        public function testSetCountry() {
            $user = User::findByUsername( 'pkakelas' );
            $user->country = new Country( 1 );
            $this->assertEquals( 1, $user->country->id, 'Country must be the one associated during update' );
        }
    }

    return new UserTest();
?>
