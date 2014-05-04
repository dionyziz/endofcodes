<?php
    class ImageController extends ControllerBase {
        public function create( $image ) {
            require_once 'models/image.php';
            require_once 'models/extentions.php';
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $user = $_SESSION[ 'user' ];
            $userImage = new Image();
            $userImage->tmp_name = $image[ 'tmp_name' ];
            $userImage->name = $image[ 'name' ];
            $userImage->user = $user;
            $user->image = $userImage;
            try {
                $userImage->save();
                $user->save();
            }
            catch ( ModelValidationException $e ) {
                if ( isset( $this->acceptTypes[ 'application/json' ] ) ) {
                    throw new HTTPBadRequestException();
                }
                go( 'user', 'view', [ 'username' => $user->username, $e->error => true ] );
            }
            if ( !isset( $this->acceptTypes[ 'application/json' ] ) ) {
                go( 'user', 'view', [ 'username' => $user->username ] );
            }
            echo json_encode( [] );
        }
    }
?>
