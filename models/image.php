<?php
    include_once 'models/user.php';

    class Image extends ActiveRecordBase {
        public $user;
        public $tmp_name;
        public $imagename;
        public $id;
        public $target_path;
        public $ext;
        protected $tableName = 'images';
        protected $exists;

        public static function find_by_user( $user ) {
            return new Image( $user->avatarid );
        }

        public function __construct( $id = false ) {
            if ( !$id ) {
                $this->exists = false;
            }
            else {
                $image_info = db_select_one( 'images', array( '*' ), compact( "id" ) );
                $this->id = $id;
                $this->imagename = $image_info[ 'imagename' ];
                $this->user = new User( $image_info[ 'userid' ] );
            }
        }

        protected function validate() {
            $ext = $this->ext; 
            if ( !Extention::valid( $ext ) ) {
                throw new ModelValidationException( 'notvalid' );
            }
        }

        protected function create() {
            global $config;

            $tmp_name = $this->tmp_name;
            $imagename = $this->imagename;
            $ext = $this->ext;
            $userid = $this->user->id;
            $target_path = $config[ 'paths' ][ 'avatar_path' ];
            $id = db_insert( 
                'images', 
                compact( "userid", "imagename" )
            );
            $imagename = "$id" . "." . $ext;
            $this->target_path = $target_path . $imagename;
            $this->id = $id;
            $this->upload();
            $this->update();
        }

        public function upload() {
            $tmp_name = $this->tmp_name;
            $target_path = $this->target_path;
            return move_uploaded_file( $tmp_name, $target_path );
        }

        public function update() {
            $this->user->avatarid = $this->id;
            $this->user->save();
        }
    }
?>
