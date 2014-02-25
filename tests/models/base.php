<?php
    class Model extends ActiveRecordBase {
        public static $attributes = [];
        public static $tableName = 'models';
    }

    class ActiveRecordBaseTest extends UnitTest {
        public function setUp() {
            db(
                'CREATE TABLE IF NOT EXISTS
                    models (
                        id INT(4) NOT NULL AUTO_INCREMENT,
                        PRIMARY KEY (id)
                    )'
            );
        }
        public function testCreate() {
            $model = new Model();
            $model->save();

            $this->assertEquals( 1, $model->id, 'The first model must have an id of 1' );
        }
        public function testUpdate() {
            $model = new Model();
            $model->save();

            $model->id = 10;
            $model->save();

            $this->assertEquals( 10, $model->id, 'Id must be updated' );
        }
        public function tearDown() {
            db( 'DROP TABLE models' );
        }
    }

    return new ActiveRecordBaseTest();
?>
