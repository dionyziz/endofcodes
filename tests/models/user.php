<?php
    require_once 'models/user.php';
    require_once 'models/country.php';
    require_once 'models/grader/bot.php';

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
                $success = false;
            }
            catch ( ModelNotFoundException $e ) {
                $success = true;
            }
            $this->assertTrue( $success, 'The user must be deleted when the $user->delete() function runs' );
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
            $this->assertEquals( $user->boturl, $dbUser->boturl, "User's boturl must be correctly stored in the database" );
        }
        public function testFindByUsername() {
            $user = $this->buildUser( 'pkakelas' );
            $dbUser = User::findByUsername( 'pkakelas' );
            $this->assertEquals( $user->id, intval( $dbUser->id ), "findByUsername must find the correct user" );
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
            $this->assertTrue( isset( $user->forgotpasswordtoken ), 'CreateForgotPasswordLink must save the token to $user->forgotpasswordtoken' );
            $this->assertTrue( !empty( $user->forgotpasswordtoken ), '$user->forgotpasswordtokeni must not be empty' );
            $this->assertTrue( isset( $user->forgotpasswordrequestcreated ), 'CreateForgotPasswordLink must save the time it created the link to $user->forgotpasswordrequestcreated' );
        }
        public function testRevokePasswordCheck() {
            $user = $this->buildUser( 'pkakelas' );
            
            $user->createForgotPasswordLink();
            try {
                $user->revokePasswordCheck( $user->forgotpasswordtoken );
                $trueSuccess = true;
            } 
            catch ( ForgotPasswordModelInvalidTokenException $e ) {
                $trueSuccess = false; 
            }
            try {
                $user->revokePasswordCheck( 'dsafasfjsakf21ekjwlrfhkl321jhl' );
                $falseSuccess = false;
            } 
            catch ( ForgotPasswordModelInvalidTokenException $e ) {
                $falseSuccess = true; 
            }
            try {
                $user->revokePasswordCheck( '' );
                $emptySuccess = false;
            } 
            catch ( ForgotPasswordModelInvalidTokenException $e ) {
                $emptySuccess = true; 
            }
            $oldToken = $user->forgotpasswordtoken;
            $user->createforgotpasswordlink();
            try {
                $user->revokePasswordCheck( $oldToken );
                $oldTokenSuccess = false;
            } 
            catch ( ForgotPasswordModelInvalidTokenException $e ) {
                $oldTokenSuccess = true; 
            }
            $user->forgotpasswordrequestcreated = time() - 60 * 60 * 24 * 2;
            try {
                $user->revokePasswordCheck( $user->forgotpasswordtoken );
                $expiredSuccess = false;
            }
            catch ( ModelValidationException $e ) {
                $expiredSuccess = true;
            }
            $this->assertTrue( $trueSuccess, 'revokePasswordCheck should validate correct tokens' );
            $this->assertTrue( $falseSuccess, 'revokePasswordCheck should not validate invalid tokens' );
            $this->assertTrue( $emptySuccess, 'revokePasswordCheck should not validate empty tokens' );
            $this->assertTrue( $oldTokenSuccess, 'revokePasswordCheck should not validate with old tokens' );
            $this->assertTrue( $expiredSuccess, 'revokePasswordCheck should not validate when request is expired' );
        }
        public function testPasswordValidate() {
            $user = $this->buildUser( 'pkakelas' );
            try {
                $user->passwordValidate( 'Bob and Alice' );
                $trueSuccess = true;
            } 
            catch ( ModelValidationException $e ) {
                $trueSuccess = false;
            } 
            try {
                $user->passwordValidate( '' );
                $emptySuccess = false;
            }
            catch ( ModelValidationException $e ) {
                $emptySuccess = true;
            } 
            try {
                $user->passwordValidate( 'Bob' ); 
                $smallSuccess = false;
            }
            catch ( ModelValidationException $e ) {
                $smallSuccess = true;
            } 
            $this->assertTrue( $trueSuccess, 'passwordValidate should validate the default password that we have set' );
            $this->assertTrue( $emptySuccess, 'passwordValidate should not validate empty passwords' );
            $this->assertTrue( $smallSuccess, 'passwordValidate should not validate too small passwords' );
        }
        public function testSaveImage() {
            $user = $this->buildUser( 'vitsalis' );
            $image = new Image();
            $image->id = 1;
            $image->name = 'lala.png';
            $image->user = $user;
            $image->save();
            $user->image = $image;
            $user->save();
            $dbUser = new User( $user->id );
            $this->assertSame( 1, $dbUser->image->id, 'The imageid must be correctly stored in the database' );
        }
        public function testWinCount() {
            $game = new Game();
            $user = $this->buildUser( 'vitsalis' );
            $game->users = [ $user->id => $user ];
            $game->initiateAttributes();
            $game->save();
            $game->genesis();
            $dbUser = new User( $user->id );
            $winCount = $dbUser->getWinCount();

            $this->assertTrue( isset( $dbUser->winCount ), 'winCount must be set for each user' );
            $this->assertSame( 1, $winCount, 'winCount must represent the number of wins a user has' );
        }
        public function testSetBoturl() {
            $user = $this->buildUser( 'vitsalis' );
            $currentBoturl = $user->boturl;
            $caught = false;
            $error = '';

            try {
                $user->setBoturl( 'invalid_boturl' );
            }
            catch ( ModelValidationException $e ) {
                $caught = true;
                $error = $e->error;
            }

            $this->assertTrue( $caught, 'A ModelValidationException must be thrown if the boturl is invalid' );
            $this->assertTrue( $error != '', 'An error must be given' );
            $this->assertEquals( $currentBoturl, $user->boturl, "The user's boturl must not change" );
        }
        public function testRoles() {
            $user = $this->buildUser( 'regular' );
            
            $this->assertFalse( $user->isDeveloper(), 'Regular users should not be developers' );
            $this->assertEquals( ROLE_USER, $user->role, 'Regular users should have a role of 0 = ROLE_USER' );

            $admin = $this->buildUser( 'admin' );
            $admin->role = ROLE_DEVELOPER;
            $admin->save();

            $this->assertTrue( $admin->isDeveloper(), 'Admin users should be developers' );
            $this->assertEquals( ROLE_DEVELOPER, $admin->role, 'Admin users should have a role of 10 = ROLE_DEVELOPER' );

            $negative = $this->buildUser( 'negative' );
            $negative->role = -50;
            $negative->save();

            $this->assertEquals( ROLE_USER, $negative->role, 'Negative roles are not allowed and must be set to ROLE_USER' );
        }
    }

    return new UserTest();
?>
