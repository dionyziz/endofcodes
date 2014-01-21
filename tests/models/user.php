<?php
    include_once 'models/user.php';
    
    class UserTest extends UnitTest {
        public function run() {
            $this->testCreate();
        }
        public function testCreate() {
            $user = new User();
            $user->username = 'vitalis';
            $user->save();

            $this->assertTrue( $user->exists, 'User must exist after creation' );
            $this->assertEquals( 'vitalis', $user->username, 'Username must be the one associated during creation' );
        }
    }

    return new UserTest();
?>
