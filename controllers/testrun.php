<?php
    class TestrunController extends ControllerBase {
        public function create( $name ) {
            include_once 'tests/base.php';
            $path = 'tests/' . $name;
            $unittest = include_once $path;
            $unittest->run();
            echo $unittest->passCount . ' / ' . $unittest->assertCount . ' tests pass';
        }

        public function createView() {
            include_once 'views/testrun/create.php';
        }
    }
?>
