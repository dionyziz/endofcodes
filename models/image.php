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
    }
?>
