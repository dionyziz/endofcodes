<?php
    class UserControllerTest extends FunctionalTest {
        public function testCreate() {
            $response = $this->request( 'user', 'create', 'POST', [
                'username'         => 'dionyziz',
                'password'         => 'secret1234567',
                'password_repeat'  => 'secret1234567',
                'email'            => 'dionyziz@gmail.com',
                'countryShortname' => 'GR',
                'day'              => 30,
                'month'            => 11,
                'year'             => 1987
            ] );
            $response->assertHas( 'a[href="user/view?username=dionyziz"]', 'User must be logged in after registration' );
        }
    }

    return new UserControllerTest();
?>
