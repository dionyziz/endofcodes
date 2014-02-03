<?php
    class TestTest extends UnitTest {
        public function testFindAllFindsFilesInFolders() {
            mkdir( 'tests/mock/depth1/depth2/depth3/', 0777, true );
            file_put_contents( 'tests/mock/depth1/depth2/depth3/magic.php', '' );

            $tests = UnitTest::findAll();

            $this->assertTrue( array_search( 'tests/mock/depth1/depth2/depth3/magic.php', $tests ) !== false, 'findAll() must find tests in subfolders' );
        }
        protected function rrmdir( $dir ) {
            if ( is_dir( $dir ) ) {
                $objects = scandir( $dir );
                foreach ( $objects as $object ) {
                    if ( $object != "." && $object != ".." ) {
                        if ( filetype( $dir . "/" . $object ) == "dir" ) {
                            $this->rrmdir( $dir . "/" . $object );
                        }
                        else {
                            unlink( $dir . "/" . $object );
                        }
                    }
                }
                reset( $objects );
                rmdir( $dir );
            }
        }
        public function tearDown() {
            $this->rrmdir( 'tests/mock' );
        }
    }
    return new TestTest();
?>
