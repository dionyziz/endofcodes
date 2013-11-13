<?php
    class Image {
        public static function create( $userid, $imagename ) {
            db(
                'INSERT INTO
                    images
                SET
                    userid = :userid,
                    imagename = :imagename;',
                compact( "userid", "imagename" )
            );
            return mysql_insert_id();
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
        }

        public static function upload( $tmp_name, $target_path ) {
            return move_uploaded_file( $tmp_name, $target_path );
        }

        public static function update( $username, $avatarid ) {
            db(
                'UPDATE
                    users
                SET
                    avatarid = :avatarid
                WHERE
                    username = :username;',
                compact( "username", "avatarid" )
            );
        }
    }
?>
