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
            $attributes = [];
            foreach ( $this->attributes as $attribute ) {
                $attributes[ $attribute ] = $this->$attribute;
            }
            try {
                $id = dbInsert(
                    $this->tableName,
                    $attributes
                );
                if ( !isset( $this->id ) ) {
                    $this->id = $id;
                }
            }
            catch ( DBException $e ) {
                $this->onCreateError( $e );
            }
            $this->exists = true;
            $this->onCreate();
        }

        protected function onBeforeCreate() {} // override me
        protected function onCreate() {} // override me
        protected function onCreateError( $e ) {} // override me
        protected function onBeforeSave() {} // override me
        protected function onSave() {} // override me

        public function save() {
            $this->onBeforeSave();
            if ( $this->exists ) {
                $this->update();
            }
            else {
                $this->create();
            }
            $this->onSave();
        }
    }
    
    class ModelException extends Exception {
        public function __construct( $description, $error = "" ) {
            if ( !empty( $error ) ) {
                $args = $description . ':' . $error; 
                parent::__construct( $args );
            }
            else {
                parent::__construct( $description );
            }
        }
    }

    class ModelNotFoundException extends ModelException {
        public function __construct() {
            parent::__construct( 'Model not found' );
        }
    }
    
    class ModelValidationException extends ModelException {
        public $error;

        public function __construct( $error = "" ) {
            parent::__construct( "Model validation error", $error );
            $this->error = $error;
        }
    }

    class ForgotPasswordModelInvalidTokenException extends ModelException {
        public function __construct() {
            parent::__construct( 'Invalid Token' );
        }
    }
?>
