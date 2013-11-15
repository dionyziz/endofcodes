<?php
    class ImageController {
        public static function create( $image ) {
            include 'models/image.php';
            include 'models/extentions.php';
            $config = getConfig();
            $avatarname = basename( $image[ 'name' ] );
            $tmp_name = $image[ 'tmp_name' ];
            $id = $_SESSION[ 'user' ][ 'userid' ];
            if ( isset( $_SESSION[ 'user' ][ 'username' ] ) ) {
                $username = $_SESSION[ 'user' ][ 'username' ];
            }
            else {
                throw new HTTPUnauthorizedException();
            }
            Image::create( $username, $tmp_name, $avatarname, $id );
            throw new RedirectException( 'index.php?resource=user&method=view&username=' . $username );
        }
    }
?>
