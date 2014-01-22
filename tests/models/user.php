<?php
    include_once 'models/user.php';
    
    class UserTest extends UnitTest {
        public function run() {
            $this->testCreate();
            $this->testDelete();
            $this->testPasswordChange();
            $this->testEmailChange();
            $this->testSetCountry();
            $this->testSetAge();
        }
        public function testCreate() {
            $user = new User();
            $user->username = 'pkakelas';
            $user->password = 'secret1234';
            $user->email = 'pkakelas@gmail.com';
            $user->save();
            $this->assertEquals( 'pkakelas', $user->username, 'Username must be the one associated during creation' );
            $this->assertEquals( 'pkakelas@gmail.com', $user->email, 'Email must be the one associated during creation' );
        }
        public function testDelete() {
        }
        public function testPasswordChange() {
        }
        public function testEmailChange() {
        }
        public function testSetCountry() {
        }
        public function testSetAge() {
        }
    }

    return new UserTest();
?>
