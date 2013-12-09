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
                $this->name = $image_info[ 'name' ];
                $this->ext = Extention::get( $this->name );
                $config = getConfig();
                $this->target_path = $config[ 'paths' ][ 'avatar_path' ] . $id . '.' . $this->ext;
            }
        }

        protected function validate() {
            $this->ext = Extention::get( $this->name );
            if ( !Extention::valid( $this->ext ) ) {
                throw new ModelValidationException( 'notvalid' );
            }
        }

        protected function create() {
            global $config;

            $tmp_name = basename( $this->tmp_name );
            $name = basename( $this->name );
            $ext = $this->ext;
            $userid = $this->user->id;
            $target_path = $config[ 'paths' ][ 'avatar_path' ];
            $id = db_insert( 
                'images', 
                compact( "userid", "name" )
            );
            $name = "$id" . "." . $ext;
            $this->target_path = $target_path . $name;
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
            parent::save();
        }
    }
?>
