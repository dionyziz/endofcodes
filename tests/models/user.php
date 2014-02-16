<?php
    include_once 'models/user.php';
    include_once 'models/country.php';

    class UserTest extends UnitTestWithFixtures {
        public function testCreate() {
            $user = new User();
            $user->username = 'pkakelas';
            $user->password = 'secret1234';
            $user->email = 'pkakelas@gmail.com';
            $user->save();
            $passwordSuccess = $user->authenticatesWithPassword( 'secret1234' );
            $this->assertTrue( $passwordSuccess, 'Password must be the one associated during creation' );
            $this->assertEquals( 'pkakelas', $user->username, 'Username must be the one associated during creation' );
            $this->assertEquals( 'pkakelas@gmail.com', $user->email, 'Email must be the one associated during creation' );
        }
        public function testAuthenticatesWithPassword() {
            $user = $this->buildUser( 'pkakelas' );
            $this->assertFalse( $user->authenticatesWithPassword( 'wrongsecret' ), 'User must not be authenticated with a wrong password' );
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
            $user->password = 'newsecret1234';
            $user->save();
            $success = $user->authenticatesWithPassword( 'newsecret1234' );
            $this->assertTrue( $success, 'Password must be the one associated during update' );
        }
        public function testEmailChange() {
            $user = $this->buildUser( 'pkakelas' );
            $user->email = 'pkakelas2@gmail.com';
            $user->save();
            $this->assertEquals( 'pkakelas2@gmail.com', $user->email, 'Email must be the one associated during update' );
        }
        public function testSetCountry() {
            $country = $this->buildCountry( 'Greece', 'GR' );

            $user = $this->buildUser( 'pkakelas' );
            $user->country = $country;
            $this->assertEquals( 1, $user->country->id, 'Country must be the one associated during update' );
        }
        public function testDuplicateUsername() {
            $caught = false;
            $user1 = $this->buildUser( 'pkakelas' );
            try {
                $user2 = $this->buildUser( 'pkakelas' );
            }
            catch ( ModelValidationException $e ) {
                $caught = true;
                $this->assertEquals(
                    'username_used',
                    $e->error,
                    "If the username is used we must get a 'username_used' error"
                );
            }
            $this->assertTrue( $caught, 'A ModelValidationException must be caught if we try to make a duplicate username' );
        }
        public function testDuplicateEmail() {
            $caught = false;
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
                $caught = true;
                $this->assertEquals(
                    'email_used',
                    $e->error,
                    "If the email is used we must get a 'email_used' error"
                );
            }
            $this->assertTrue( $caught, 'A ModelValidationException must be caught if we try to make a duplicate email' );
        }
        public function testFindNonExistentUser() {
            $caught = false;
            try {
                $user = new User( 1 );
            }
            catch ( ModelNotFoundException $e ) {
                $caught = true;
            }
            $this->assertTrue( $caught, 'When we try to find a non existent user we must get a ModelNotFoundException' );
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
            $this->assertEquals( $user->id, intval( $dbUser->id ), "findByUsername must find the correct user" );
        }
        public function testJsonSerialize() {
            $user = $this->buildUser( 'pkakelas' );

            $this->assertTrue( method_exists( $user, 'toJson' ), 'User object must export a "toJson" function' );

            $json = $user->toJson();
            $data = json_decode( $json );

            $this->assertTrue( isset( $data->username ), 'username must exist in exported JSON' ); 
            $this->assertEquals( $user->username, $data->username, 'username must be encoded properly to JSON' );

            $this->assertTrue( isset( $data->userid ), 'userid must exist in exported JSON' ); 
            $this->assertEquals( $user->id, $data->userid, 'userid must be encoded properly to JSON' );
        }
        public function testAuthenticationAfterRenewSessionId() {
            $user = $this->buildUser( 'pkakelas' );

            $user->renewSessionId();
            $passwordSuccess = $user->authenticatesWithPassword( 'secret1234' );
            $this->assertTrue( $passwordSuccess, 'Password must not be changed after "renewSessionId" is run' );
        }
        public function testCreateForgotPasswordLink() {
            $user = $this->buildUser( 'pkakelas' );
            
            $link = $user->createForgotPasswordLink();
            $this->assertTrue( isset( $user->forgotPasswordToken ), 'CreateForgotPasswordLink must save the token to $user->forgotPasswordToken' );
            $this->assertTrue( isset( $user->forgotPasswordRequestCreated ), 'CreateForgotPasswordLink must save the time it created the link to $user->forgotPasswordRequestCreated' );
        }
        public function testRevokePasswordCheck() {
            $user = $this->buildUser( 'pkakelas' );
            
            $user->createForgotPasswordLink();
            try {
                $user->revokePasswordCheck( $user->forgotPasswordToken );
                $trueSuccess = 1;
            } 
            catch ( ForgotPasswordModelInvalidTokenException $e ) {
               $trueSuccess = 0; 
            }
            try {
                $user->revokePasswordCheck( 'dsafasfjsakf21ekjwlrfhkl321jhl' );
                $falseSuccess = 0;
            } 
            catch ( ForgotPasswordModelInvalidTokenException $e ) {
                $falseSuccess = 1; 
            }
            try {
                $user->revokePasswordCheck( '' );
                $emptySuccess = 0;
            } 
            catch ( ForgotPasswordModelInvalidTokenException $e ) {
               $emptySuccess = 1; 
            }
            $oldToken = $user->forgotPasswordToken;
            $user->createForgotPasswordLink();
            try {
                $user->revokePasswordCheck( $oldToken );
                $oldTokenSuccess = 0;
            } 
            catch ( ForgotPasswordModelInvalidTokenException $e ) {
               $oldTokenSuccess = 1; 
            }
            $user->forgotPasswordRequestCreated = time() - 60 * 24 * 2; 
            try {
                $user->revokePasswordCheck( $user->forgotPasswordToken );
                $expiredSuccess = 0;
            }
            catch ( ModelValidationException $e ) {
                $expiredSuccess = 1;
            }
            $this->assertTrue( $trueSuccess, 'revokePasswordCheck should validate correct tokens' );
            $this->assertTrue( $falseSuccess, 'revokePasswordCheck should not validate correct tokens' );
            $this->assertTrue( $emptySuccess, 'revokePasswordCheck should not validate empty tokens' );
            $this->assertTrue( $oldTokenSuccess, 'revokePasswordCheck should not validate with old tokens' );
            $this->assertTrue( $expiredSuccess, 'revokePasswordCheck should not validate when request is expired' );
        }
        public function testPasswordValidate() {
            $user = $this->buildUser( 'pkakelas' );
            try {
                $user->passwordValidate( 'Bob and Alice' );
                $trueSuccess = 1;
            } 
            catch ( ModelValidationException $e ) {
                $trueSuccess = 0;
            } 
            try {
                $user->passwordValidate( '' );
                $emptySuccess = 0;
            }
            catch ( ModelValidationException $e ) {
                $emptySuccess = 1;
            } 
            try {
                $user->passwordValidate( 'Bob' ); 
                $smallSuccess = 0;
            }
            catch ( ModelValidationException $e ) {
                $smallSuccess = 1;
            } 
            $this->assertTrue( $trueSuccess, 'passwordValidate should validate the default password that we have set' );
            $this->assertTrue( $emptySuccess, 'passwordValidate should not validate empty passwords' );
            $this->assertTrue( $smallSuccess, 'passwordValidate should not validate too small passwords' );
        }
    }

    return new UserTest();
?>
