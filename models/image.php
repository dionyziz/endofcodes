<?php
    require_once 'models/user.php';
    require_once 'models/extentions.php';

    class Image extends ActiveRecordBase {
        public $tmp_name;
        public $name;
        public $target_path;
        public $ext;
        public $user;
        protected $userid;
        protected static $attributes = [ 'name', 'userid' ];
        protected static $tableName = 'images';

        public static function findByUser( $user ) {
            return new Image( $user->imageid );
        }

        public function __construct( $id = false ) {
            if ( $id ) {
                global $config;

                $this->exists = true;
                $image_info = dbSelectOne( 'images', [ '*' ], compact( "id" ) );
                $this->id = $id;
                $this->name = $image_info[ 'name' ];
                $this->ext = Extention::get( $this->name );
                $this->target_path = $config[ 'paths' ][ 'avatarPath' ] . $id . '.' . $this->ext;
            }
        }

        protected function onBeforeSave() {
            $this->ext = Extention::get( $this->name );
            $this->userid = $this->user->id;
            $this->name = basename( $this->name );
            if ( !Extention::valid( $this->ext ) ) {
                throw new ModelValidationException( 'imageInvalid' );
            }
        }

        protected function onCreate() {
            global $config;

            $targetPath = $config[ 'paths' ][ 'avatarPath' ];
            $ext = $this->ext;
            $name = $this->id . "." . $ext;
            $this->target_path = $targetPath . $name;
            $this->upload();
        }

        public function upload() {
            $tmpName = $this->tmp_name;
            $targetPath = $this->target_path;
            return move_uploaded_file( $tmpName, $targetPath );
        }
    }
?>
