<?php
    require_once 'models/follow.php';
    require_once 'helpers/validation.php';

    class FollowController extends ControllerBase {
        public function create( $followedid ) {
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }

            if ( !isWholeNumber( $followedid ) ) {
                throw new HTTPBadRequestException();
            }

            $follower = $_SESSION[ 'user' ];
            try {
                $followed = new User( $followedid );
            }
            catch ( ModelNotfoundException $e ) {
                throw new HTTPNotFoundException();
            }
            $follow = new Follow();
            $follow->follower = $follower;
            $follow->followed = $followed;
            $follow->save();
            go( 'user', 'view', [ 'username' => $followed->username ] );
        }

        public function delete( $followedid ) {
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }

            if ( !isWholeNumber( $followedid ) ) {
                throw new HTTPBadRequestException();
            }

            $followerid = $_SESSION[ 'user' ]->id;
            try {
                $follow = new Follow( $followerid, $followedid );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException();
            }
            $followed = $follow->followed;
            $follow->delete();
            go( 'user', 'view', [ 'username' => $followed->username ] );
        }
    }
?>
