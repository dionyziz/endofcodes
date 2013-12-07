<?php
    include 'models/user.php';
    class Image extends ActiveRecordBase {
        public $user;
        public $tmp_name;
        public $imagename;
        public $avatarid;
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
            $imagename = $this->imagename;
            $ext = Extention::get( $imagename ); 
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
            $avatarid = db_insert( 
                'images', 
                compact( "userid", "imagename" )
            );
            $imagename = "$avatarid" . "." . $ext;
            $this->target_path = $target_path . $imagename;
            $this->avatarid = $avatarid;
            $this->upload();
            $this->update();
        }

        public function upload() {
            $tmp_name = $this->tmp_name;
            $target_path = $this->target_path;
            return move_uploaded_file( $tmp_name, $target_path );
        }

        public function update() {
            $username = $this->user->username;
            $avatarid = $this->avatarid;
            db_update( 
                'users', 
                compact( "avatarid" ), 
                compact( "username" )
            );
        }
    }
?>
