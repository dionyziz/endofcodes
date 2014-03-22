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
        public function testInsertMulti() {
            dbInsertMulti( 'test_models', [
                [
                    'a' => 'test1', 'b' => 1, 'c' => 2
                ],
                [
                    'a' => 'test2', 'b' => 3, 'c' => 4
                ]
            ] );
            $rows = dbSelect( 'test_models', [ 'id', 'a', 'b', 'c' ] );
            $this->assertEquals( 2, count( $rows ), 'dbInsertMulti must insert exactly two rows' );
            $row1 = $rows[ 0 ];
            $this->assertEquals( 'test1', $row1[ 'a' ], 'dbInsertMulti must insert the data specified' );
            $this->assertSame( 1, $row1[ 'b' ], 'dbInsertMulti must insert the data specified' );
            $this->assertSame( 2, $row1[ 'c' ], 'dbInsertMulti must insert the data specified' );
            $row2 = $rows[ 1 ];
            $this->assertEquals( 'test2', $row2[ 'a' ], 'dbInsertMulti must insert the data specified' );
            $this->assertSame( 3, $row2[ 'b' ], 'dbInsertMulti must insert the data specified' );
            $this->assertSame( 4, $row2[ 'c' ], 'dbInsertMulti must insert the data specified' );
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
            }, 'DBExceptionWrongCount', 'dbSelectOne must throw an exception if more than one rows are found', function( $e ) {
                $this->assertEquals( 1, $e->expected, 'Expected rows in DBExceptionWrongCount for selectOne must always be 1' );
                $this->assertTrue( $e->actual > 1, 'Actual rows in DBExceptionWrongCount for selectOne must be greater than 1 if more rows are present' );
            } );

            dbDelete( 'test_models' );

            $this->assertThrows( function() {
                dbSelectOne( 'test_models' );
            }, 'DBExceptionWrongCount', 'dbSelectOne must throw an exception if no rows are found', function( $e ) {
                $this->assertEquals( 1, $e->expected, 'Expected rows in DBExceptionWrongCount for selectOne must always be 1' );
                $this->assertEquals( 0, $e->actual, 'Actual rows in DBExceptionWrongCount for selectOne must be 0 when no rows are present' );
            } );
        }
        public function testSelectMulti() {
            dbInsertMulti( 'test_models', [
                [
                    'a' => 'test1', 'b' => 1, 'c' => 2
                ],
                [
                    'a' => 'test2', 'b' => 3, 'c' => 4
                ]
            ] );
            $rows = dbSelectMulti( 'test_models', [ 'a', 'b', 'c' ], [
                [
                    'a' => 'test1', 'b' => 1, 'c' => 2
                ],
                [
                    'a' => 'test2', 'b' => 3, 'c' => 4
                ]
            ] );

            $this->assertEquals( 2, count( $rows ), 'dbSelect must find the rows inserted' );
            $row1 = $rows[ 0 ];
            $this->assertEquals( 'test1', $row1[ 'a' ], 'dbSelectMulti must select the data specified' );
            $this->assertSame( 1, $row1[ 'b' ], 'dbSelectMulti must select the data specified' );
            $this->assertSame( 2, $row1[ 'c' ], 'dbSelectMulti must select the data specified' );
            $row2 = $rows[ 1 ];
            $this->assertEquals( 'test2', $row2[ 'a' ], 'dbSelectMulti must select the data specified' );
            $this->assertSame( 3, $row2[ 'b' ], 'dbSelectMulti must select the data specified' );
            $this->assertSame( 4, $row2[ 'c' ], 'dbSelectMulti must select the data specified' );
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
        public function testListFields() {
            $fields = dbListFields( 'test_models' );

            $this->assertEquals( [ 'id', 'a', 'b', 'c' ], $fields, 'dbListFields must include all fields in the returned list' );
        }
        public function testDbSelectNoFields() {
            $caught = false;
            try {
                dbSelect( 'test_models' );
            }
            catch ( DBException $e ) {
                $caught = true;
            }

            $this->assertFalse( $caught, "A DBException must not be caught if the only parameter we got is table" );
        }
        public function testDbSelectWithLimitAndOrderBy() {
            dbInsertMulti( 'test_models', [
                [
                    'a' => 'test',
                    'c' => 42
                ],
                [
                    'a' => 'toast',
                    'c' => 17
                ]
            ] );
            $rows = dbSelect( 'test_models', [ 'a', 'c' ], [], 'c ASC', 1 );

            $this->assertEquals( 1, count( $rows ), 'When limit = 1 we must get only one row' );
            $this->assertSame( 17, $rows[ 0 ][ 'c' ], 'dbSelect must return the correct row' );
            $this->assertSame( 'toast', $rows[ 0 ][ 'a' ], 'dbSelect must return the correct row' );
        }
        public function tearDown() {
            db( 'DROP TABLE test_models' );
        }
    }
    return new DBTest();
?>
