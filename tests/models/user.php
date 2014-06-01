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
            $this->assertThrows(
                function() {
                    $user = User::findByUsername( 'pkakelas' );
                },
                'ModelNotFoundException',
                'The user should not exist after the execution of $user->delete()'
            );
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
            $user1 = $this->buildUser( 'pkakelas' );
            $this->assertThrows(
                function() {
                    $user2 = $this->buildUser( 'pkakelas' );
                },
                'ModelValidationException',
                'A ModelValidationException must be thrown when we try to register an existing username',
                function( ModelValidationException $e ) {
                    $this->assertEquals(
                        'username_used',
                        $e->error,
                        "We must get a 'username_used' error when we try to register an existing username"
                    );
                }
            );
        }
        public function testDuplicateEmail() {
            $user1 = $this->buildUser( 'pkakelas', 'duplicate@gmail.com' );
            $this->assertThrows(
                function() {
                    $user2 = $this->buildUser( 'dionyziz', 'duplicate@gmail.com' );
                },
                'ModelValidationException',
                'A ModelValidationException must be thrown when we try to register an existing email',
                function( ModelValidationException $e ) {
                    $this->assertEquals(
                        'email_used',
                        $e->error,
                        "We must get a 'email_used' error when we try to register an existing email"
                    );
                }
            );
        }
        public function testFindNonExistentUser() {
            $this->assertThrows(
                function() {
                    $user = new User( 1 );
                },
                'ModelNotFoundException',
                'A ModelNotFoundException must be thrown when we try find a non existent user'
            );
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
            $this->assertEquals( $user->id, intval( $dbUser->id ), "findByUsername() must find the correct user" );
        }
        public function testAuthenticationAfterRenewSessionId() {
            $user = $this->buildUser( 'pkakelas' );

            $user->renewSessionId();
            $passwordSuccess = $user->authenticatesWithPassword( 'secret1234' );
            $this->assertTrue( $passwordSuccess, 'Password should not be changed after "renewSessionId" is run' );
        }
        public function testCreateForgotPasswordLink() {
            $user = $this->buildUser( 'pkakelas' );

            $link = $user->createForgotPasswordLink();
            $this->assertTrue( isset( $user->forgotpasswordtoken ), 'CreateForgotPasswordLink must save the token to $user->forgotpasswordtoken' );
            $this->assertTrue( !empty( $user->forgotpasswordtoken ), '$user->forgotpasswordtoken must not be empty' );
            $this->assertTrue( isset( $user->forgotpasswordrequestcreated ), 'CreateForgotPasswordLink must save the time it created the link to $user->forgotpasswordrequestcreated' );
        }
        public function testRevokePasswordCheck() {
            $user = $this->buildUser( 'pkakelas' );

            $user->createForgotPasswordLink();
            $this->assertDoesNotThrow(
                function() use ( $user ) {
                    $user->revokePasswordCheck( $user->forgotpasswordtoken );
                },
                'ForgotPasswordModelInvalidTokenException',
                'revokePasswordCheck() should validate correct tokens'
            );
            $this->assertThrows(
                function() use ( $user ) {
                    $user->revokePasswordCheck( 'dsafasfjsakf21ekjwlrfhkl321jhl' );
                },
                'ForgotPasswordModelInvalidTokenException',
                'revokePasswordCheck() should not validate invalid tokens'
            );
            $this->assertThrows(
                function() use ( $user ) {
                    $user->revokePasswordCheck( '' );
                },
                'ForgotPasswordModelInvalidTokenException',
                'revokePasswordCheck() should not validate empty tokens'
            );

            $oldToken = $user->forgotpasswordtoken;
            $user->createforgotpasswordlink();
            $this->assertThrows(
                function() use ( $user, $oldToken ) {
                    $user->revokePasswordCheck( $oldToken );
                },
                'ForgotPasswordModelInvalidTokenException',
                'revokePasswordCheck() should not validate with old tokens'
            );

            $user->forgotpasswordrequestcreated = date( "Y-m-d h:i:s", time() - 60 * 60 * 24 * 2 );
            $this->assertThrows(
                function() use ( $user ) {
                    $user->revokePasswordCheck( $user->forgotpasswordtoken );
                },
                'ModelValidationException',
                'revokePasswordCheck() should not validate when request is expired'
            );
        }
        public function testPasswordValidate() {
            $user = $this->buildUser( 'pkakelas' );
            $this->assertDoesNotThrow(
                function() use ( $user ) {
                    $user->passwordValidate( 'Bob and Alice' );
                },
                'ModelValidationException',
                'passwordValidate() should validate the default password that we have set'
            );
            $this->assertThrows(
                function() use ( $user ) {
                    $user->passwordValidate( '' );
                },
                'ModelValidationException',
                'passwordValidate() should not validate empty passwords'
            );
            $this->assertThrows(
                function() use ( $user ) {
                    $user->passwordValidate( 'Bob' );
                },
                'ModelValidationException',
                'passwordValidate() should not validate too small passwords'
            );
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
            $this->assertSame( 1, $dbUser->image->id, 'The $imageid must be correctly stored in the database' );
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

            $this->assertTrue( isset( $dbUser->winCount ), '$winCount must be set for each user' );
            $this->assertSame( 1, $winCount, '$winCount must represent the number of wins a user has' );
        }
        public function testSetBoturl() {
            $user = $this->buildUser( 'vitsalis' );
            $currentBoturl = $user->boturl;

            $this->assertThrows(
                function() use ( $user ) {
                    $user->setBoturl( 'invalid_boturl' );
                },
                'ModelValidationException',
                'A ModelValidationException must be thrown when the boturl is invalid',
                function( ModelValidationException $e ) {
                    $this->assertTrue( !empty( $e->error ), 'An error must be returned if the boturl is invalid' );
                }
            );
            $this->assertEquals( $currentBoturl, $user->boturl, "The user's boturl must not change" );
        }
        public function testRoles() {
            $user = $this->buildUser( 'regular' );

            $this->assertFalse( $user->isDeveloper(), 'Regular users should not be developers' );
            $this->assertSame( ROLE_USER, $user->role, 'Regular users should have a role of 0 = ROLE_USER' );

            $admin = $this->buildUser( 'admin' );
            $admin->role = ROLE_DEVELOPER;
            $admin->save();

            $this->assertTrue( $admin->isDeveloper(), 'Admin users should be developers' );
            $this->assertSame( ROLE_DEVELOPER, $admin->role, 'Admin users should have a role of 10 = ROLE_DEVELOPER' );

            $negative = $this->buildUser( 'negative' );
            $negative->role = -50;
            $negative->save();

            $this->assertSame( ROLE_USER, $negative->role, 'Negative roles are not allowed and must be set to ROLE_USER' );

            $adminLookup = User::findByUsername( 'admin' );
            $this->assertSame( ROLE_DEVELOPER, $adminLookup->role, 'ROLE_DEVELOPER status must be permanent' );

            $userLookup = User::findByUsername( 'regular' );
            $this->assertSame( ROLE_USER, $userLookup->role, 'ROLE_USER status must be permanent' );
        }
    }

    return new UserTest();
?>
