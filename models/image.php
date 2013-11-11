<?php
    class Image {
        public static function create( $userid, $imagename ) {
            db(
                'INSERT INTO
                    images
                SET
                    userid = :userid,
                    imagename = :imagename;',
                array( "userid" => $userid, "imagename" => $imagename )
            );
        }

        public static function delete( $imagename ) {
            db(
                'DELETE FROM
                    images
                WHERE
                    imagename = :imagename;',
                array( "imagename" => $imagename )
            );
        }
    }
?>
