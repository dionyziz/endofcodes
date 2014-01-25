<?php
    class TestrunController extends ControllerBase {
        protected $environment = 'test';

        public function create( $name ) {
            include_once 'tests/base.php';
            $path = 'tests/' . $name . '.php';
            if ( !file_exists( $path ) ) {
                throw new HTTPNotFoundException();
            }
            $unittest = include_once $path;
            $unittest->setUp();
            $unittest->run();
            echo $unittest->passCount . ' / ' . $unittest->assertCount . ' tests pass';
        }

        public function createView() {
            include_once 'views/testrun/create.php';
        }
    }
?>
