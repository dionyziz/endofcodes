<?php
    class ImageController extends AuthenticatedController {
        public function create( $image ) {
            $this->requireLogin();

            require_once 'models/image.php';
            require_once 'models/extentions.php';
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
                if ( $this->outputFormat == 'json' ) {
                    throw new HTTPBadRequestException( 'Invalid image' );
                }
                go( 'user', 'view', [ 'username' => $user->username, $e->error => true ] );
            }
            if ( $this->outputFormat == 'json' ) {
                echo json_encode( $user->image->target_path );
            }
            else {
                go( 'user', 'view', [ 'username' => $user->username ] );
            }
        }
    }
?>
