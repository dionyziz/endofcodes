<?php
    class ImageController extends ControllerBase {
        public function create( $image ) {
            include_once 'models/image.php';
            include_once 'models/extentions.php';
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $user = $_SESSION[ 'user' ];
            $user->image = new Image();
            $user->image->tmp_name = $image[ 'tmp_name' ];
            $user->image->name = $image[ 'name' ];
            $user->image->userid = $user->id;
            try {
                $user->image->save();
                $user->save();
            }
            catch ( ModelValidationException $e ) {
                go( 'user', 'update', [ $e->error => true ] );
            }
            go( 'user', 'view', [ 'username' => $user->username ] );
        }
    }
?>
