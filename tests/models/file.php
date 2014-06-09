<<<<<<< HEAD:tests/helpers/file.php
<?php
    class FileHelperTest extends UnitTestWithFixtures {
        protected $mockPath = 'tests/mock/';
        protected $copyPath = 'tests/mock2/';

        public function setUp() {
            $this->content = 'I shall be written safely';
            $this->directory = 'tests/helpers/file/';
            $this->filename = $this->directory . 'message.txt';

            mkdir( $this->directory );
        }
        private function readOnlyDirectory() {
            chmod( $this->directory, 0444 );
            $this->assertThrows(
                function() {
                    safeWrite( $this->filename, $this->content );
                },
                'FileNotWritableException',
                'safeWrite() must throw an Exception when attempting to write to a read-only directory'
            );
        }
        private function emptyWritableDirectory() {
            // chmod( $this->directory, 0666 );
            $this->assertDoesNotThrow(
                function() {
                    safeWrite( $this->filename, $this->content );
                },
                'FileNotWritableException',
                'safeWrite() must not throw an Exception when attempting to write to an empty, writable directory'
            );
            $read = file_get_contents( $this->filename );
            $this->assertEquals( $this->content, $read, 'Content read must be the same passed to safeWrite()' );
        }
        private function readOnlyFile() {
            chmod( $this->filename, 0444 );
            $this->assertThrows(
                function() {
                    safeWrite( $this->filename, $this->content );
                },
                'FileNotWritableException',
                'safeWrite() must throw an Exception when attempting to write a read-only file',
                function( FileNotWritableException $e ) {
                    $this->assertEquals( $this->filename, $e->filename );
                    $this->assertEquals( $this->content, $e->content );
                }
            );
        }
        public function testSafeWrite() {
            //$this->readOnlyDirectory(); // This test is not functional yet.
            $this->emptyWritableDirectory();
            $this->readOnlyFile();
        }
        public function testRecursiveCopy() {
            $path = 'depth1/depth2/depth3';
            mkdir( $this->mockPath . $path, 0777, true );
            mkdir( $this->copyPath, 0777, true );
            touch( $this->mockPath . $path . '/magic.php' );

            recursiveCopy( $this->mockPath, $this->copyPath . 'deeper' );
            $this->assertTrue( file_exists( $this->copyPath . 'deeper/' . $path . '/magic.php' ), 'The folder must be copied' );
        }
        private function safeUnlink( $filename ) {
            if ( file_exists( $filename ) ) {

                chmod( $filename, 0666 );

                if ( is_dir( $filename ) ) {
                    rmdir( $filename );
                }
                else {
                    unlink( $filename );
                }
            }
        }
        public function tearDown() {
            $this->safeUnlink( $this->filename );
            $this->safeUnlink( $this->directory );

            $this->rrmdir( $this->copyPath );
            $this->rrmdir( $this->mockPath );
        }
    }

    return new FileHelperTest();
?>
=======
<?php
    require_once 'models/file.php';
    class TestFile extends UnitTestWithFixtures {
        protected $mockPath = 'tests/mock/';
        protected $copyPath = 'tests/mock2/';

        public function testRecursiveCopy() {
            $path = 'depth1/depth2/depth3';
            mkdir( $this->mockPath . $path, 0777, true );
            mkdir( $this->copyPath, 0777, true );
            touch( $this->mockPath . $path . '/magic.php' );

            recursiveCopy( $this->mockPath, $this->copyPath . 'deeper' );
            $this->assertTrue( file_exists( $this->copyPath . 'deeper/' . $path . '/magic.php' ), 'The folder must be copied' );
        }
        public function tearDown() {
            $this->rrmdir( $this->copyPath );
            $this->rrmdir( $this->mockPath );
        }
    }
    return new TestFile();
?>
>>>>>>> a5f454dcc89a4341ae203ffbfd8148972b9da692:tests/models/file.php
