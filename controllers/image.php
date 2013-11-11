<?php
    class ImageController {
        public static function create() {
            include 'models/image.php';
            include 'models/imgextentions.php';
            $imagename = basename( $_FILES[ 'image' ][ 'name' ] );
            $username = $_SESSION[ 'user' ][ 'username' ];
            $ext = substr( $imagename, strrpos( $imagename, "." ) );
            for ( $i = 0; $i < count( $extentions ); ++$i ) {
                if ( $ext === $extentions[ $i ] ) {
                    break;
                }
            }
            $ext = str_replace( ".", "", $ext );
            if ( $i == count( $extentions ) ) {
                header( 'Location: index.php?resource=user&method=view&notvalid=yes&username=' . $username );
                die();
            }
            $target_path = 'Avatars/';
            for ( $i = 0; $i < count( $extentions ); ++$i ) {
                if ( file_exists( $target_path . $username . $extentions[ $i ] ) ) {
                    unlink( $target_path . $username . $extentions[ $i ] );
                    Image::deleteImage( $username . $extentions[ $i ] );
                }
            }
            $imagename = $username . "." . $ext;
            Image::createImage( $_SESSION[ 'user' ][ 'userid' ], $imagename );
            $target_path = $target_path . $imagename;
            move_uploaded_file( $_FILES[ 'image' ][ 'tmp_name' ], $target_path );
            header( 'Location: index.php?resource=user&method=view&username=' . $username );
        }
    }
?>
