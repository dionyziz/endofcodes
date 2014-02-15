<?php
    class TestrunController extends ControllerBase {
        protected $environment = 'test';

        public function create( $name, $all = false ) {
            require_once 'models/test/base.php';
            require_once 'models/test/withfixtures.php';

            if ( $all ) {
                $tests = UnitTest::findAll();
            }
            else {
                $tests = [ $name ];
            }

            $unittests = [];
            foreach ( $tests as $test ) {
                $path = 'tests/' . $test . '.php';
                if ( !file_exists( $path ) ) {
                    throw new HTTPNotFoundException();
                }
                $unittest = require_once $path;
                $unittest->run();

                $unittests[] = $unittest;
            }
            require_once 'views/testrun/results.php';
        }

        public function createView() {
            require_once 'models/test/base.php';

            $tests = UnitTest::findAll();
            require_once 'views/testrun/create.php';
        }
    }
?>
