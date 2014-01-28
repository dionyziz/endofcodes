<?php
    class TestrunController extends ControllerBase {
        protected $environment = 'test';

        public function create( $name ) {
            include_once 'tests/base.php';
            include_once 'tests/testwithuser.php';

            $path = 'tests/' . $name . '.php';
            if ( !file_exists( $path ) ) {
                throw new HTTPNotFoundException();
            }
            $unittest = include_once $path;
            $unittest->run();

            include_once 'views/testrun/results.php';
        }

        public function createView() {
            include_once 'views/testrun/create.php';
        }
    }
?>
