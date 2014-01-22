<?php
    abstract class UnitTest {
        public $assertCount = 0;
        public $passCount = 0;

        public function assertTrue( $condition, $description = '' ) {
            ++$this->assertCount;

            if ( !$condition ) {
                throw new UnitTestFailedException( $description );
            }

            ++$this->passCount;
        }
        public function assertEquals( $expected, $actual, $description = '' ) {
            if ( $description != '' ) {
                $description .= '. ';
            }
            $description .= "Expected '$expected', found '$actual'.";
            try {
                $this->assertTrue( $expected === $actual, $description );
            }
            catch ( UnitTestFailedException $e ) {
                echo $e->description . "\n";
            }
        }
        public function setUp() {
            $tables = dbListTables();
            foreach ( $tables as $table ) {
                if ( $table === 'countries' ) {
                    continue;
                }
                db( 'TRUNCATE TABLE ' . $table );
            }
        }
    }

    class UnitTestFailedException extends Exception {
        public $description;

        public function __construct( $decription ) {
            $this->description = $description;
        }
    }
?>
