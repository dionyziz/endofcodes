<?php
    require_once 'models/migration.php';

    class MigrationsTest extends UnitTestWithFixtures {
        protected function createTable() {
            Migration::createTable(
                'testTable',
                [
                    'id' => 'int(11) NOT NULL AUTO_INCREMENT'
                ],
                [
                    [ 'type' => 'primary', 'field' => 'id' ]
                ]
            );
        }
        public function setUp() {
            $this->createTable();
            Migration::$path = 'tests/migrations/';
            Migration::$log = 'tests/migrations/.testLog';
            mkdir( Migration::$path, 0777, true );
            for ( $i = 1; $i <= 5; ++$i ) {
                file_put_contents( Migration::$path . $i . '.php', '' );
            }
        }
        public function tearDown() {
            try {
                Migration::dropTable( 'testTable' );
            }
            catch ( MigrationException $e ) {
            }
            $this->rrmdir( Migration::$path );
        }
        public function testLoadLog() {
            file_put_contents( Migration::$log, '{"env1":"migr1"}' );

            $logs = Migration::loadLog();
            $this->assertTrue( is_array( $logs ), 'loadLog must return an array' );
            $this->assertTrue( isset( $logs[ 'env1' ] ), 'An existing environment must exist as a key in the loadLog array.' );
            $this->assertEquals( 'migr1', $logs[ 'env1' ], 'The last migration must be the one specified in log file.' );

            // $this->safeUnlink( Migration::$log );
            // $this->assertDoesNotThrow(
            //     function() {
            //         $logs = Migration::loadLog();
            //         $this->assertEquals( [], $logs, 'loadLog() must return an empty array when log not found.' );
            //     },
            //     'FileNotFoundException',
            //     'loadLog() must not throw an exception when log not found.'
            // );
        }
        public function testUpdateLog() {
            Migration::updateLog( 'migration1', 'env1' );
            Migration::updateLog( 'migration2', 'env2' );
            Migration::updateLog( 'migration3', 'env2' );
            $logs = Migration::loadLog();
            $this->assertTrue( file_exists( Migration::$path ), 'The log file must exist' );
            $this->assertEquals( 2, count( $logs ), 'updateLog() should create exactly one record for each environment' );
            $this->assertTrue( isset( $logs[ 'env1' ] ), 'The envs of last migrations must exist in the array' );
            $this->assertTrue( isset( $logs[ 'env2' ] ), 'The envs of last migrations must exist in the array' );
            $this->assertEquals( 'migration1', $logs[ 'env1' ], 'updateLog must keep the last migration in each environment' );
            $this->assertEquals( 'migration3', $logs[ 'env2' ], 'updateLog must keep the last migration in each environment' );
        }
        public function testFindAll() {
            file_put_contents( Migration::$path . 'notphp.txt', '' );
            $all = Migration::findAll();
            $this->assertTrue( is_array( $all ), 'findAll() must return an array' );
            $this->assertTrue( !in_array( 'notphp.txt', $all ), 'findAll() must return only php files' );
            $this->assertEquals( 5, count( $all ), 'findAll() must hold every php file in the directory' );
        }
        public function testFindUnexecuted() {
            Migration::$environments = [ 'env1', 'env2' ];
            Migration::updateLog( '3.php', 'env1' );
            Migration::updateLog( '2.php', 'env2' );
            $unex1 = Migration::findUnexecuted( 'env1' );
            $all = Migration::findUnexecuted( 'env0' );
            $this->assertTrue( is_array( $unex1 ), 'findUnexecuted() must return an array' );
            $this->assertTrue( !in_array( '1.php', $unex1 ), 'findUnexecuted() must not return executed files' );
            $this->assertTrue( !in_array( '2.php', $unex1 ), 'findUnexecuted() must not return executed files' );
            $this->assertTrue( !in_array( '3.php', $unex1 ), 'findUnexecuted() must not return executed files' );
            $this->assertTrue( in_array( '4.php', $unex1 ), 'findUnexecuted() must return unexecuted files' );
            $this->assertTrue( in_array( '5.php', $unex1 ), 'findUnexecuted() must return unexecuted files' );
            $this->assertEquals( 5, count( $all ), 'findUnexecuted() must return all files when not any log exists' );
        }
/*        public function testMigrate() {
            $this->assertDoesNotThrow(
                function() {
                    Migration::addField( 'testTable', 'test', 'int(11) NOT NULL' );
                },
                'MigrationException',
                'migrate() must not throw a migration error when the script completes successfully'
            );
            $this->assertThrows(
                function() {
                    Migration::addField( 'id', 'testTable', 'int(11) NOT NULL' );
                },
                'MigrationException',
                'migrate() must throw a migration error when the script does not complete successfully' );
        }
        public function testCreateTable() {
            $tables = dbListTables();
            $this->assertDoesNotThrow( function () {
                $this->createTable();
            }, 'MigrationException' );
            $this->assertTrue( in_array( 'testTable', $tables ), 'testTable should exist in the list of the tables' );
        }
        public function testAddField() {
            Migration::addField(
                'testTable',
                'testfield',
                'int(11) NOT NULL'
            );
            $fields = dbListFields( 'testTable' );
            $this->assertDoesNotThrow( function () {
                Migration::addField( 'testTable', 'test', 'int(11) NOT NULL' );
            }, 'MigrationException', 'addField must add a field when called' );
            $this->assertTrue( in_array( 'testfield', $fields ), 'testField should exist in the list of the fields' );
            $this->assertThrows( function() {
                Migration::addField( 'test', 'testfield1', 'int(11) NOT NULL' );
            }, 'MigrationException', 'addField must return an error when the table does not exist' );
        }
        public function testAlterField() {
            Migration::addField(
                'testTable',
                'testold',
                'int(11) NOT NULL'
            );
            $this->assertDoesNotThrow( function () {
                Migration::alterField( 'testTable', 'testold', 'testnew', 'int(11) NOT NULL' );
            }, 'MigrationException', 'alterField must alter a field when called' );
            $fields = dbListFields( 'testTable' );
            $this->assertThrows( function () {
                Migration::alterField( 'test', 'testold', 'testnew', 'int(11) NOT NULL AUTO_INCREMENT' );
            }, 'MigrationException', 'alterField must return an error when the table not exists' );
            $this->assertFalse( in_array( 'testold', $fields ), 'the old field should not exist in the list of the fields' );
            $this->assertTrue( in_array( 'testnew', $fields ), 'the renamed field should exist in the list of the fields' );
        }
        public function testDropField() {
            Migration::addField(
                'testTable',
                'testfield',
                'int(11) NOT NULL'
            );
            $fields = dbListFields( 'testTable' );
            $this->assertDoesNotThrow( function() {
                Migration::dropField( 'testTable', 'testfield' );
            }, 'MigrationException', 'dropField must drop a field when called' );
            $this->assertThrows( function() {
                Migration::dropField( 'test', 'test' );
            }, 'MigrationException', 'dropField must return an error when table not exists' );
            $this->assertFalse( in_array( 'test', $fields ), 'the thrown field should not exist in the list of the fields' );
        }*/
    }
    return new MigrationsTest();
?>
