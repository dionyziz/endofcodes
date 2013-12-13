<?php
    include_once 'models/user.php';
    include_once 'models/extentions.php';

    class Image extends ActiveRecordBase {
        public $tmp_name;
        public $name;
        public $id;
        public $target_path;
        public $ext;
        public $userid;
        protected $tableName = 'images';

        public static function findByUser( $user ) {
            return new Image( $user->avatarid );
        }

        public function __construct( $id = false ) {
            if ( $id ) {
                global $config;

                $this->exists = true;
                $image_info = dbSelectOne( 'images', array( '*' ), compact( "id" ) );
                $this->id = $id;
                $this->name = $image_info[ 'name' ];
                $this->ext = Extention::get( $this->name );
                $this->target_path = $config[ 'paths' ][ 'avatar_path' ] . $id . '.' . $this->ext;
            }
        }

        protected function validate() {
            $this->ext = Extention::get( $this->name );
            if ( !Extention::valid( $this->ext ) ) {
                throw new ModelValidationException( 'image_invalid' );
            }
        }

        protected function create() {
            global $config;

            $tmp_name = $this->tmp_name;
            $name = basename( $this->name );
            $ext = $this->ext;
            $userid = $this->userid;
            $target_path = $config[ 'paths' ][ 'avatar_path' ];
            $id = dbInsert( 
                'images', 
                compact( "userid", "name" )
            );
            $name = $id . "." . $ext;
            $this->target_path = $target_path . $name;
            $this->id = $id;
            $this->upload();
            $this->exists = true;
        }

        public function upload() {
            $tmp_name = $this->tmp_name;
            $target_path = $this->target_path;
            return move_uploaded_file( $tmp_name, $target_path );
        }
    }
?>
