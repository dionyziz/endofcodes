<?php
    require_once 'helpers/file.php';
    class TestFile extends UnitTestWithFixtures {
        public function tearDown() {
            $this->rrmdir( 'tests/mock' );
        }
        public function testRecursiveCopy() {
            $base = 'tests/mock/';
            $path = 'depth1/depth2/depth3';
            mkdir( $base . $path, 0777, true );
            file_put_contents( $base . $path . '/magic.php', '' );

            recursiveCopy( $base, $base . 'deeper' );
            $this->assertTrue( file_exists( $base . 'deeper/' . $path . '/magic.php' ), 'The folder must be copied' );
        }
    }
    return new TestFile();
?>
