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
