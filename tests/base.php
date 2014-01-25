<?php
    class UnitTestMethod {
        public $unittest;
        public $methodName;
        public $success;
        public $calltrace;
        public $error;
        public $assertCount = 0;
        public $passCount = 0;

        public function __construct( $unittest, $methodName ) {
            $this->unittest = $unittest;
            $this->methodName = $methodName;
        }
        public function run() {
            $methodName = $this->methodName;
            try {
                $this->unittest->setUp();
                $this->unittest->$methodName();
                $this->unittest->tearDown();
                $this->success = true;
            }
            catch ( UnitTestFailedException $e ) {
                // anticipated failure
                $this->success = false;
                $this->calltrace = $e->getTrace();
                $this->error = $e->getMessage();
            }
            catch ( Exception $e ) {
                // unanticipated failure
                $this->success = false;
                $this->calltrace = $e->getTrace();
                $this->error = 'Unanticipated failure: ' . $e->getMessage();
            }
        }
    }

    abstract class UnitTest {
        public $successTestsCount = 0;
        public $tests = array();
        protected $currentTest = null;

        public function assertTrue( $condition, $description = '' ) {
            ++$this->currentTest->assertCount;

            if ( !$condition ) {
                throw new UnitTestFailedException( $description );
            }

            ++$this->currentTest->passCount;
        }
        public function assertEquals( $expected, $actual, $description = '' ) {
            if ( $description != '' ) {
                $description .= '. ';
            }
            $description .= "Expected '$expected', found '$actual'.";
            $this->assertTrue( $expected === $actual, $description );
        }
        public function run() {
            $this_reflection = new ReflectionObject( $this );

            foreach ( $this_reflection->getMethods() as $method ) {
                if ( substr( $method->name, 0, strlen( 'test' ) ) == 'test' ) {
                    $methodname = $method->name;
                    $this->runTest( $methodname );
                }
            }
        }
        protected function runTest( $name ) {
            $test = new UnitTestMethod( $this, $name );
            $this->currentTest = $test;
            $test->run();
            if ( $test->success ) {
                ++$this->successTestsCount;
            }
            $this->tests[] = $test;
        }
        protected function truncateDb() {
            $tables = dbListTables();
            foreach ( $tables as $table ) {
                if ( $table === 'countries' ) {
                    continue;
                }
                db( 'TRUNCATE TABLE ' . $table );
            }
        }
        public function setUp() {
            $this->truncateDb();
        }
        public function tearDown() {
            $this->truncateDb();
        }
    }

    class UnitTestFailedException extends Exception {
        public function __construct( $description ) {
            parent::__construct( $description );
        }
    }
?>
