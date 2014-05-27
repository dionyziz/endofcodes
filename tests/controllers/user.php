<?php
    class UserControllerTest extends FunctionalTest {
        public function testCreate() {
            $response = $this->request( 'user', 'create', [
                'username'         => 'dionyziz',
                'password'         => 'secret',
                'password_repeat'  => 'secret',
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
