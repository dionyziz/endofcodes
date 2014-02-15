<?php
    class TestrunController extends ControllerBase {
        protected $environment = 'test';

        public function create( $name, $all = false ) {
            include_once 'models/test/base.php';
            include_once 'models/test/withfixtures.php';

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
                $unittest = include_once $path;
                $unittest->run();

                $unittests[] = $unittest;
            }
            include_once 'views/testrun/results.php';
        }

        public function createView() {
            include_once 'models/test/base.php';

            $tests = UnitTest::findAll();
            include_once 'views/testrun/create.php';
        }
    }
?>
