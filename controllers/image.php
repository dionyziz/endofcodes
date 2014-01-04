<?php
    class ImageController extends ControllerBase {
        public static function create( $image, $token = '' ) {
            include_once 'models/image.php';
            include_once 'models/extentions.php';
            include_once 'models/formtoken.php';
            if ( !isset( $_SESSION[ 'user' ][ 'username' ] ) || !FormToken::validate( $token, $_SESSION[ 'form' ][ 'token' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $user = User::findByUsername( $_SESSION[ 'user' ][ 'username' ] );
            $user->image = new Image();
            $user->image->tmp_name = $image[ 'tmp_name' ];
            $user->image->name = $image[ 'name' ];
            $user->image->userid = $user->id;
            try {
                $user->image->save();
                $user->save();
            }
            catch ( ModelValidationException $e ) {
                go( 'user', 'update', array( $e->error => true ) );
            }
            go( 'user', 'view', array( 'username' => $user->username ) );
        }
    }
?>
