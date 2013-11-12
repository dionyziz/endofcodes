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
        }

        public static function delete( $imagename ) {
            db(
                'DELETE FROM
                    images
                WHERE
                    imagename = :imagename;',
                compact( "imagename" )
            );
        }

        public static function deleteCurrent( $target_path, $username ) {
            $extentions = Extention::getValid();
            foreach ( $extentions as $key => $value ) {
                $extentions[ $key ] = '.' . $value;
            }

            foreach ( $extentions as $extention ) {
                if ( file_exists( $target_path . $username . $extention ) ) {
                    unlink( $target_path . $username . $extention );
                    Image::delete( $username . $extention );
                }
            }
        }

        public static function upload( $tmp_name, $target_path ) {
            move_uploaded_file( $tmp_name, $target_path );
        }
    }
?>
