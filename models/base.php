<?php
    abstract class ActiveRecordBase {
        public $id;
        protected $exists;

        public function delete() {
            $id = $this->id;
            dbDelete(
                $this->tableName,
                compact( "id" )
            );
        }

        protected function create() {
            $this->onBeforeCreate();
            $attributes = array();
            foreach ( $this->attributes as $attribute ) {
                $attributes[ $attribute ] = $this->$attribute;
            }
            try {
                $this->id = dbInsert(
                    $this->tableName,
                    $attributes
                );
            }
            catch ( DBException $e ) {
                $this->onCatch();
            }
            $this->exists = true;
            $this->onCreateError();
        }

        protected function onBeforeCreate() {} // override me
        protected function onCreate() {} // override me
        protected function onCreateError() {} // override me

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
            parent::__construct( 'Model not found' );
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
