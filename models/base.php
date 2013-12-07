<?php
    abstract class ActiveRecordBase {
        public function delete() {
            $id = $this->id;
            db_delete(
                $this->tableName,
                compact( "id" )
            );
        }

        public function save() {
            $this->validate();
            if ( $this->exists ) {
                $this->update();
            }
            else {
                $this->create();
            }
        }
    }

    class ModelNotFoundException extends Exception {
        public function __construct() {
            parent::__construct( 'NotFound' );
        }
    }

    class ModelValidationException extends Exception {
        public $error;
        public function __construct( $error = "" ) {
            parent::__construct( "Model validation error: " . $error );
            $this->error = $error;
        }
    }
?>
