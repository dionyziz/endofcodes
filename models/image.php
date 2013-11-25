<?php
    class Image extends ActiveRecordBase {
        public $username;
        public $id;
        public $tmp_name;
        public $imagename;
        public $avatarid;
        public $target_path;
        protected $tableName = 'images';
        protected $exists;

        public function __construct( $id = false ) {
            if ( $id ) {
                $this->exists = false;
            }
            else {
                $user_info = db_select( 'users', array( 'username' ), compact( "id" ) );
                $this->username = $user_info[ 0 ][ 'username' ];
                $this->id = $id;
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
            $username = $this->username;
            $tmp_name = $this->tmp_name;
            $imagename = $this->imagename;
            $id = $this->id;
            $target_path = $config[ 'paths' ][ 'avatar_path' ];
            $avatarid = db_insert( 
                'images', 
                compact( "id", "imagename" ) 
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
            $username = $this->username;
            $avatarid = $this->avatarid;
            db_update( 
                'users', 
                compact( "avatarid" ), 
                compact( "username" )
            );
        }

        public function getCurrentImage() {
            $id = $this->id;
            $res = db(
                'SELECT
                    users.avatarid AS avatarid,
                    images.imagename AS imagename
                FROM
                    users CROSS JOIN images ON
                    users.avatarid = images.imageid
                WHERE
                    id = :id
                LIMIT 1;', 
                compact( "id" )
            );
            if ( mysql_num_rows( $res ) == 1 ) {
                $row = mysql_fetch_array( $res );
                $ext = Extention::get( $row[ 'imagename' ] );
                $avatarid = $row[ 'avatarid' ];
                return "$avatarid" . "." . $ext;
            }
            return false;
        }
    }
?>
