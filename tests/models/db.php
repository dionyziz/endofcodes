<?php
    class DBTest extends UnitTest {
        public function setUp() {
            db(
                'CREATE TABLE IF NOT EXISTS
                    models (
                        id INT(4) NOT NULL AUTO_INCREMENT,
                        PRIMARY KEY (id)
                    )'
            );
        }
        public function testDbInsertNoFields() {
            $caught = false;
            try {
                dbInsert( 'models', [] );
            }
            catch ( DBException $e ) {
                $caught = true;
            }

            $this->assertFalse( $caught, "A DBException must not be caught if the fields parameter is empty" );
        }
        public function testDbSelectNoFields() {
            $caught = false;
            try {
                dbSelect( 'models' );
            }
            catch ( DBException $e ) {
                $caught = true;
            }

            $this->assertFalse( $caught, "A DBException must not be caught if the only parameter we got is table" );
        }
        public function tearDown() {
            db( 'DROP TABLE models' );
        }
    }
    return new DBTest();
?>
