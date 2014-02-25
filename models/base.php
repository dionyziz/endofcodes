<?php
    abstract class ActiveRecordBase {
        public $id = 0;
        public $exists;

        public function delete() {
            $id = $this->id;
            dbDelete(
                static::$tableName,
                compact( "id" )
            );
        }

        protected function findAttributes() {
            $attributes = [];
            foreach ( static::$attributes as $attribute ) {
                if ( isset( $this->$attribute ) ) {
                    $attributes[ $attribute ] = $this->$attribute;
                }
            }

            return $attributes;
        }

        protected function create() {
            $this->onBeforeCreate();
            $attributes = $this->findAttributes();

            try {
                $id = dbInsert(
                    static::$tableName,
                    $attributes
                );
                if ( $this->id === 0 ) {
                    $this->id = $id;
                }
            }
            catch ( DBException $e ) {
                $this->onCreateError( $e );
            }
            $this->exists = true;
            $this->onCreate();
        }

        protected function update() {
            $this->onBeforeUpdate();
            $attributes = $this->findAttributes();
            $id = $this->id;

            try {
                dbUpdate(
                    static::$tableName,
                    $attributes,
                    compact( 'id' )
                );
            }
            catch ( DBException $e ) {
                $this->onUpdateError( $e );
            }

            $this->onUpdate();
        }

        protected static function arrayToCollection( $array ) {
            $collection = [];
            foreach ( $array as $result ) {
                $collection[] = new static( $result[ 'id' ] );
            }
            return $collection;
        }

        public static function findAll() {
            $array = dbSelect( static::$tableName );
            return self::arrayToCollection( $array );
        }

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

        protected function onBeforeCreate() {} // override me
        protected function onCreate() {} // override me
        protected function onCreateError( $e ) {} // override me
        protected function onBeforeUpdate() {} // override me
        protected function onUpdate() {} // override me
        protected function onUpdateError( $e ) {} // override me
        protected function onBeforeSave() {} // override me
        protected function onSave() {} // override me
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
