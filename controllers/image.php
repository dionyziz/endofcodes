<?php
    class ImageController {
        public static function create( $image ) {
            include 'models/image.php';
            include 'models/extentions.php';
            if ( !isset( $_SESSION[ 'user' ][ 'username' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $config = getConfig();
            $imagename = basename( $image[ 'name' ] );
            $tmp_name = $image[ 'tmp_name' ];
            $id = $_SESSION[ 'user' ][ 'id' ];
            $username = $_SESSION[ 'user' ][ 'username' ];
            $image = new Image();
            $image->username = $username;
            $image->tmp_name = $tmp_name;
            $image->imagename = $imagename;
            $image->id = $id;
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
