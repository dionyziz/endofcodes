<?php
    class ImageController {
        public static function create( $image ) {
            include 'models/image.php';
            include 'models/extentions.php';
            $config = getConfig();
            $avatarname = basename( $image[ 'name' ] );
            $tmp_name = $image[ 'tmp_name' ];
            $id = $_SESSION[ 'user' ][ 'userid' ];
            if ( !isset( $_SESSION[ 'user' ][ 'username' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $username = $_SESSION[ 'user' ][ 'username' ];
            $image = new Image( $username, $id, $tmp_name, $avatarname );
            try {
                $image->create();
            }
            catch ( ModelValidationException $e ) {
                go( 'user', 'view', array( 'username' => $username, $e->error => true ) );
            }
            go( 'user', 'view', compact( 'username' ) );
        }
    }
?>
