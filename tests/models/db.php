<?php
    class DBTest extends UnitTest {
        public function setUp() {
            db(
                'CREATE TABLE IF NOT EXISTS
                    test_models (
                        id INT(4) NOT NULL AUTO_INCREMENT,
                        a VARCHAR(10) NOT NULL DEFAULT "",
                        b INT(4) NOT NULL DEFAULT 0,
                        c INT(4) NOT NULL DEFAULT 0,
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
        public function testDb() {
            $res = db( 'SELECT 1 AS one' );

            $this->assertTrue( is_resource( $res ), 'db must return a database resource when a successful query is ran' );

            $this->assertThrows( function() {
                db( 'abracadabra' );
            }, 'DBException', 'db must throw a DBException when a SQL syntax error is spotted' );
        }
        public function testArray() {
            $rows = dbArray( 'SELECT 1 AS one' );

            $this->assertEquals( 1, count( $rows ), 'dbArray must execute a query and return the rows' );
            $this->assertTrue( isset( $rows[ 0 ] ), 'dbArray must return a list of rows' );
            $this->assertTrue( isset( $rows[ 0 ][ 'one' ] ), 'dbArray must return a list of rows, each row of which is a dictionary with the fields as keys' );
            $this->assertSame( 1, $rows[ 0 ][ 'one' ], 'dbArray must return a list of rows, each row of which is a dictionary with the record values as dictionary values' );
        }
        public function testInsert() {
            dbInsert( 'test_models', [
                'a' => 'test',
                'c' => 42,
                'b' => 17
            ] );
            $rows = dbSelect( 'test_models', [ 'id', 'a', 'b', 'c' ] );
            $this->assertEquals( 1, count( $rows ), 'dbInsert must insert exactly one row' );
            $row = $rows[ 0 ];
            $this->assertSame( 'test', $row[ 'a' ], 'dbInsert must insert the data specified' );
            $this->assertSame( 17, $row[ 'b' ], 'dbInsert must insert the data specified' );
            $this->assertSame( 42, $row[ 'c' ], 'dbInsert must insert the data specified' );
            $this->assertSame( 1, $row[ 'id' ], 'dbInsert must allow the DBMS to specify the auto-increment value freely' );;
        }
        public function testInsertNoFields() {
            $this->assertDoesNotThrow( function() {
                dbInsert( 'test_models', [] );
            }, 'DBException', "A DBException must not be caught if the fields parameter is empty" );
        }
        public function testDelete() {
            dbInsert( 'test_models', [] );

            dbDelete( 'test_models', [ 'id' => 0 ] );
            $rows = dbSelect( 'test_models', [ 'id' => 1 ] );
            $this->assertEquals( 1, count( $rows ), 'dbDelete must not delete rows that do not match the where clause' );

            dbDelete( 'test_models', [ 'id' => 1 ] );
            $rows = dbSelect( 'test_models', [ 'id' => 1 ] );
            $this->assertEquals( 0, count( $rows ), 'dbDelete must delete the row specified' );
        }
        public function testSelect() {
            $rows = dbSelect( 'test_models' );
            $this->assertEquals( 0, count( $rows ), 'dbSelect must not return rows that do not exist' );

            dbInsert( 'test_models', [
                'a' => 'test',
                'c' => 42
            ] );

            $rows = dbSelect( 'test_models', [ 'a', 'c' ] );
            $this->assertEquals( 1, count( $rows ), 'dbSelect must find the row inserted' );
            $this->assertTrue( isset( $rows[ 0 ][ 'a' ] ), 'dbSelect must return all columns that have been requested' );
            $this->assertSame( 'test', $rows[ 0 ][ 'a' ], 'dbSelect must return the data that exists in the database' );
            $this->assertFalse( isset( $rows[ 0 ][ 'b' ] ), 'dbSelect must not return columns that have not been requested' );
            $this->assertTrue( isset( $rows[ 0 ][ 'c' ] ), 'dbSelect must return all columns that have been requested' );
            $this->assertSame( 42, $rows[ 0 ][ 'c' ], 'dbSelect must return the data that exists in the database' );

            $row = dbSelectOne( 'test_models', [ 'a', 'c' ] );
            $this->assertTrue( isset( $row[ 'a' ] ), 'dbSelectOne must return all columns that have been requested' );
            $this->assertSame( 'test', $row[ 'a' ], 'dbSelectOne must return the data that exists in the database' );
            $this->assertFalse( isset( $row[ 'b' ] ), 'dbSelectOne must not return columns that have not been requested' );
            $this->assertTrue( isset( $row[ 'c' ] ), 'dbSelectOne must return all columns that have been requested' );
            $this->assertSame( 42, $row[ 'c' ], 'dbSelectOne must return the data that exists in the database' );

            dbInsert( 'test_models', [] );
            $this->assertThrows( function() {
                dbSelectOne( 'test_models' );
            }, 'DBException', 'dbSelectOne must throw an exception if more than one rows are found' );

            dbDelete( 'test_models' );

            $this->assertThrows( function() {
                dbSelectOne( 'test_models' );
            }, 'DBException', 'dbSelectOne must throw an exception if no rows are found' );
        }
        public function testUpdate() {
            dbInsert( 'test_models', [
                'a' => 'test',
                'c' => 42
            ] );
            dbUpdate( 'test_models', [
                'a' => 'toast',
                'c' => 17
            ] );
            $rows = dbSelect( 'test_models' );
            $this->assertEquals( 1, count( $rows ), 'dbUpdate must not affect the number of rows in the database' );
            $this->assertSame( 'toast', $rows[ 0 ][ 'a' ], 'dbUpdate must change the data in the database' );
            $this->assertSame( 17, $rows[ 0 ][ 'c' ], 'dbUpdate must change the data in the database' );
        }
        public function testListTables() {
            $tables = dbListTables();

            $this->assertTrue( array_search( 'test_models', $tables ), 'dbListTables must include all tables in the returned list' );
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
            db( 'DROP TABLE test_models' );
        }
    }
    return new DBTest();
?>
