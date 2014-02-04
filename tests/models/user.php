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
        public function testDuplicateUsername() {
            $cought = false;
            $user1 = $this->buildUser( 'pkakelas' );
            try {
                $user2 = $this->buildUser( 'pkakelas' );
            }
            catch ( ModelValidationException $e ) {
                $cought = true;
                $this->assertEquals(
                    'username_used',
                    $e->error,
                    "If the username is used we must get an 'username_used' error"
                );
            }
            $this->assertTrue( $cought, 'A ModelValidationException must be cought if we try to make a duplicate username' );
        }
        public function testDuplicateEmail() {
            $cought = false;
            $user1 = new User();
            $user2 = new User();
            $user1->username = 'pkakelas';
            $user2->username = 'dionyziz';
            $user1->password = $user2->password = 'secret1234';
            $user1->email = $user2->email = 'duplicate@gmail.com';
            $user1->save();
            try {
                $user2->save();
            }
            catch ( ModelValidationException $e ) {
                $cought = true;
                $this->assertEquals(
                    'email_used',
                    $e->error,
                    "If the email is used we must get an 'email_used' error"
                );
            }
            $this->assertTrue( $cought, 'A ModelValidationException must be cought if we try to make a duplicate email' );
        }
        public function testFindNonExistentUser() {
            $cought = false;
            try {
                $user = new User( 1 );
            }
            catch ( ModelNotFoundException $e ) {
                $cought = true;
            }
            $this->assertTrue( $cought, 'When we try to find a non existent user we must get a ModelNotFoundException' );
        }
        public function testFindById() {
            $user = $this->buildUser( 'dionyziz' );
            $dbUser = new User( 1 );
            $this->assertEquals( $user->username, $dbUser->username, "User's username must be correctly stored in the database" );
            $this->assertEquals( $user->email, $dbUser->email, "User's email must be correctly stored in the database" );
        }
        public function testFindByUsername() {
            $user = $this->buildUser( 'pkakelas' );
            $dbUser = User::findByUsername( 'pkakelas' );
            $this->assertEquals( $user->id, intval( $dbUser->id ), "User's id must be stored correctly in the database" );
        }
    }

    return new UserTest();
?>
