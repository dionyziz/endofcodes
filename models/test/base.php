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
                $this->unittest->baseSetUp();
                $this->unittest->$methodName();
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
            $this->unittest->baseTearDown();
        }
    }

    abstract class UnitTest {
        public $successTestsCount = 0;
        public $tests = [];
        public $testName;
        protected $currentTest = null;

        public function __construct() {
            $this->testName = get_class( $this );
        }
        public static function findAll( $subdir = '' ) {
            require_once 'models/extentions.php';
            $dir = 'tests/' . $subdir;
            $list = [];
            if ( is_dir( $dir ) ) {
                $objects = scandir( $dir );
                foreach ( $objects as $object ) {
                    if ( $object !== '.' && $object !== '..' ) {
                        if ( $subdir == '' ) {
                            $fullpath = $object;
                        }
                        else {
                            $fullpath = $subdir . '/' . $object;
                        }
                        if ( filetype( $dir . '/' . $object ) === "dir" ) {
                            $sublist = UnitTest::findAll( $fullpath );
                            $list = array_merge( $sublist, $list );
                        }
                        else {
                            if ( Extention::get( $fullpath ) === 'php' ) {
                                $list[] = Extention::remove( $fullpath );
                            }
                        }
                    }
                }
            }
            return $list;
        }
        public function assertTrue( $condition, $description = '' ) {
            ++$this->currentTest->assertCount;

            if ( !$condition ) {
                throw new UnitTestFailedException( $description );
            }

            ++$this->currentTest->passCount;
        }
        protected function makeEqualDescription( $expected, $actual, $description = '' ) {
            if ( $description != '' ) {
                $description .= '. ';
            }
            if ( is_array( $expected ) ) {
                $expected = "Array " . implode( ",", $expected );
            }
            if ( is_array( $actual ) ) {
                $actual = "Array " . implode( ",", $actual );
            }
            $description .= "Expected '$expected', found '$actual'.";
            return $description;
        }
        public function assertEquals( $expected, $actual, $description = '' ) {
            $this->assertTrue( $expected === $actual, $this->makeEqualDescription( $expected, $actual, $description ) );
        }
        public function assertSame( $expected, $actual, $description = '' ) {
            $this->assertTrue( $expected == $actual, $this->makeEqualDescription( $expected, $actual, $description ) );
        }
        public function assertFalse( $condition, $description = '' ) {
            $this->assertTrue( !$condition, $description );
        }
        protected function callAndGetException( $function ) {
            $caught = false;

            try {
                $function();
            }
            catch ( Exception $e ) {
                $caught = true;
                $name = get_class( $e );
                $exception = $e;
            }

            return compact( 'caught', 'name', 'exception' );
        }
        public function assertThrows( $function, $exception, $description = '', $callback = false ) {
            $exceptionData = $this->callAndGetException( $function );

            if ( $description != '' ) {
                $description .= '. ';
            }
            $description .= "Expected exception $exception to be thrown, but it was not thrown.";

            if ( $exceptionData[ 'caught' ] ) {
                if ( strtolower( $exceptionData[ 'name' ] ) != strtolower( $exception ) ) {
                    $description .= " A " . $exceptionData[ 'name' ] . " exception was thrown instead.";
                    $exceptionData[ 'caught' ] = false;
                }
            }
            $this->assertTrue( $exceptionData[ 'caught' ], $description );

            if ( $callback !== false ) {
                $callback( $exceptionData[ 'exception' ] );
            }
        }
        public function assertDoesNotThrow( $function, $exception, $description = '' ) {
            $exceptionData = $this->callAndGetException( $function );

            if ( $description != '' ) {
                $description .= '. ';
            }
            $description .= "Expected exception $exception to be avoided, but it was thrown.";

            if ( $exceptionData[ 'caught' ] ) {
                if ( strtolower( $exceptionData[ 'name' ] ) != strtolower( $exception ) ) {
                    // re-throw unexpected exception
                    throw $exceptionData[ 'exception' ];
                }
            }
            $this->assertFalse( $exceptionData[ 'caught' ], $description );
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
                db( 'TRUNCATE TABLE ' . $table );
            }
        }
        public function baseSetUp() {
            $this->truncateDb();
            $this->setUp();
        }
        public function baseTearDown() {
            $this->truncateDb();
            $this->tearDown();
        }
        public function setUp() {} // override me
        public function tearDown() {} // override me
    }

    class UnitTestFailedException extends Exception {
        public function __construct( $description ) {
            parent::__construct( $description );
        }
    }
?>
