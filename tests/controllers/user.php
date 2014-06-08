<?php
    class UserControllerTest extends FunctionalTest {
        private $userDetails;
        private $response;

        public function setUp() {
            $this->userDetails = [
                'username'         => 'dionyziz',
                'password'         => 'secret1234567',
                'password_repeat'  => 'secret1234567',
                'email'            => 'dionyziz@gmail.com',
                'countryShortname' => 'GR',
                'day'              => 30,
                'month'            => 11,
                'year'             => 1987
            ];
            $response->assertHas( '.navbar-nav a[href="user/view?username=dionyziz"]', 'User must be logged in after registration' );
        }

        /* create */
        private function makeCreateRequest() {
            $this->response = $this->request( 'user', 'create', 'POST', $this->userDetails );
        }
        private function assertValidates( $fields, $error, $description ) {
            $this->userDetails = array_merge( $this->userDetails, $fields );
            $this->makeCreateRequest();
            $this->response->assertContains( $error, $description . ". Expected error \"$error\" to be produced, but validation incorrectly passed" );
        }
        public function testCreate() {
            $this->makeCreateRequest();
            $this->response->assertHas( 'a[href="user/view?username=dionyziz"]', 'User must be logged in after registration' );
        }
        public function testCreateWithoutDob() {
            unset( $this->userDetails[ 'day' ] );
            unset( $this->userDetails[ 'month' ] );
            unset( $this->userDetails[ 'year' ] );
            $this->makeCreateRequest();
            $this->response->assertHas( 'a[href="user/view?username=dionyziz"]', 'Date of birth must be optional during registration' );
        }
        public function testPasswordRepeat() {
            $this->assertValidates(
                [ 'password_repeat' => 'secretWRONG' ], 
                'Passwords do not match',
                'User must not be created if passwords do not match'
            );
        }
        public function testPasswordShort() {
            $this->assertValidates(
                [ 'password' => '123456', 'password_repeat' => '123456' ],
                'Password should be at least 7 characters long',
                'Passwords with less than 7 characters must not be accepted'
            );
        }
        public function testEmail() {
            $this->assertValidates(
                [ 'email' => 'not an e-mail' ],
                'not a valid email',
                'Invalid e-mail addresses must be detected during registration'
            );
        }
        public function testEmptyUsername() {
            $this->assertValidates(
                [ 'username' => '' ],
                'type a username',
                'Empty usernames must not be accepted'
            );
        }
        public function testEmptyPassword() {
            $this->assertValidates(
                [ 'password' => '', 'password_repeat' => '' ],
                'type a password',
                'Empty passwords must not be accepted'
            );
        }

        /* view */
        public function testNonExistingUser() {
            $this->response = $this->request( 'user', 'view', 'GET', [ 'username' => 'waldo' ] );
            $this->response->assertStatusIs( 404, 'Non-existing user must yield 404' );
        }

        /* delete */
        public function testDeleteLoggedout() {
            $this->response = $this->request( 'user', 'delete', 'POST' );
            $this->response->assertStatusIs( 401, 'Logged out user must not be able to delete any accounts' );
        }
    }

    return new UserControllerTest();
?>
