<?php
    class TestrunController extends ControllerBase {
        public $environment = 'test';

        public function create( $name, $all = false ) {
            if ( $all ) {
                set_time_limit( 360 );
            }

            require_once 'models/test/base.php';
            require_once 'models/test/functional.php';
            require_once 'models/test/withfixtures.php';

            if ( $all ) {
                $tests = UnitTest::findAll();
            }
            else {
                $tests = [ $name ];
            }

            $unittests = [];
            $failed = false;
            foreach ( $tests as $test ) {
                $path = 'tests/' . $test . '.php';
                if ( !file_exists( $path ) ) {
                    throw new HTTPNotFoundException( 'No such test "' . $path . '"' );
                }
                $unittest = require_once $path;
                $unittest->run();
                foreach ( $unittest->tests as $test ) {
                    if ( !$test->success ) {
                        $failed = true;
                    }
                }

                $unittests[] = $unittest;
            }
            require_once 'views/testrun/results.php';

            if ( $failed ) {
                return 1;
            }
            return 0;
        }

        public function createView() {
            require_once 'models/test/base.php';

            $tests = UnitTest::findAll();
            require_once 'views/testrun/create.php';
        }
    }
?>
