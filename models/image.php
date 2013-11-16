<?php
    class Image {
        public static function create( $username, $tmp_name, $imagename, $userid ) {
            global $config;
            $ext = Extention::get( $imagename ); 
            if ( !Extention::valid( $ext ) ) {
                throw new ModelValidationException( 'notvalid' );
            }
            $target_path = $config[ 'paths' ][ 'avatar_path' ];
            $id = db_insert( 
                'images', 
                compact( "userid", "imagename" ) 
            );
            $imagename = "$id" . "." . $ext;
            $target_path = $target_path . $imagename;
            Image::upload( $tmp_name, $target_path );
            Image::update( $username, $id );
        }

        public static function getCurrentImage( $username ) {
            $res = db(
                'SELECT
                    users.avatarid AS avatarid,
                    images.imagename AS imagename
                FROM
                    users CROSS JOIN images ON
                    users.avatarid = images.imageid
                WHERE
                    username = :username
                LIMIT 1;', 
                compact( "username" )
            );
            if ( mysql_num_rows( $res ) == 1 ) {
                $row = mysql_fetch_array( $res );
                $ext = Extention::get( $row[ 'imagename' ] );
                $id = $row[ 'avatarid' ];
                return "$id" . "." . $ext;
            }
            return false;
        }

        public static function upload( $tmp_name, $target_path ) {
            return move_uploaded_file( $tmp_name, $target_path );
        }

        public static function update( $username, $avatarid ) {
            db_update( 
                'users', 
                compact( "avatarid" ), 
                compact( "username" )
            );
        }
    }
?>
