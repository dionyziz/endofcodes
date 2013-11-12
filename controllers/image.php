<?php
    class ImageController {
        public static function create() {
            include 'models/image.php';
            include 'models/extentions.php';
            $imagename = basename( $_FILES[ 'image' ][ 'name' ] );
            if ( isset( $_SESSION[ 'user' ][ 'username' ] ) ) {
                $username = $_SESSION[ 'user' ][ 'username' ];
            }
            else {
                throw new Exception( 'username isn\'t set' );
            }
            $ext = Extention::get( $imagename ); 
            $valid = Extention::valid( $ext );
            if ( !$valid ) {
                header( 'Location: index.php?resource=user&method=view&notvalid=yes&username=' . $username );
                die();
            }
            $target_path = 'Avatars/';
            Image::deleteCurrent( $target_path, $username );
            $imagename = $username . "." . $ext;
            Image::create( $_SESSION[ 'user' ][ 'userid' ], $imagename );
            $target_path = $target_path . $imagename;
            Image::upload( $_FILES[ 'image' ][ 'tmp_name' ], $target_path );
            header( 'Location: index.php?resource=user&method=view&username=' . $username );
        }
    }
?>
