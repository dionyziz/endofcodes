<?php
    class DBTest extends UnitTest {
        public function setUp() {
            db(
                'CREATE TABLE IF NOT EXISTS
                    test_models (
                        id INT(4) NOT NULL AUTO_INCREMENT,
                        PRIMARY KEY (id)
                    )'
            );
            db(
                'CREATE TABLE IF NOT EXISTS
                    test_multi (
                        id INT(4) NOT NULL AUTO_INCREMENT,
                        c varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                        b INT(4) NOT NULL DEFAULT 0,
                        a INT(4) NOT NULL DEFAULT 0,
                        PRIMARY KEY (id)
                    )'
            );
        }
        public function testDbInsertNoFields() {
            $caught = false;
            try {
                dbInsert( 'test_models', [] );
            }
            catch ( DBException $e ) {
                $caught = true;
            }

            $this->assertFalse( $caught, "A DBException must not be caught if the fields parameter is empty" );
        }
        public function testInsert() {
            $caught = false;
            try {
                dbInsert( 'test_multi', [ 'a' => 1, 'b' => 2, 'c' => 'adfa' ] );
            }
            catch ( DBException $e ) {
                $caught = true;
            }

            $res = dbSelect( 'test_multi', [ 'a', 'b', 'c' ], [ 'id' => 1 ] );

            $this->assertFalse( $caught, 'dbInsert must not throw an exception if valid data is given' );
            $this->assertEquals( 1, count( $res ), 'dbInsert must insert exactly one row' );
            $this->assertSame( 1, $res[ 0 ][ 'a' ], 'dbInsert must insert the data given on the table' );
            $this->assertSame( 2, $res[ 0 ][ 'b' ], 'dbInsert must insert the data given on the table' );
            $this->assertSame( 'adfa', $res[ 0 ][ 'c' ], 'dbInsert must insert the data given on the table' );
        }
        public function tearDown() {
            try {
                db( 'DROP TABLE test_models' );
                db( 'DROP TABLE test_multi' );
            }
            catch ( Exception $e ) {
            }
        }
    }
    return new DBTest();
?>
