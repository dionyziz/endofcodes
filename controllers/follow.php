<?php
    require_once 'models/follow.php';
    require_once 'helpers/validation.php';

    class FollowController extends AuthenticatedController {
        public function create( $followedid ) {
            $this->requireLogin();

            if ( !isWholeNumber( $followedid ) ) {
                throw new HTTPBadRequestException( 'followedid is not a number' );
            }

            $follower = $_SESSION[ 'user' ];
            try {
                $followed = new User( $followedid );
            }
            catch ( ModelNotfoundException $e ) {
                throw new HTTPNotFoundException( 'The userid specified (followedid = ' . $followedid . ') does not correspond to a valid user' );
            }
            $follow = new Follow();
            $follow->follower = $follower;
            $follow->followed = $followed;
            $follow->save();
            go( 'user', 'view', [ 'username' => $followed->username ] );
        }

        public function delete( $followedid ) {
            $this->requireLogin();

            if ( !isWholeNumber( $followedid ) ) {
                throw new HTTPBadRequestException( 'followedid is not a number' );
            }

            $followerid = $_SESSION[ 'user' ]->id;
            try {
                $follow = new Follow( $followerid, $followedid );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException( 'There is no such follow relationship' );
            }
            $followed = $follow->followed;
            $follow->delete();
            go( 'user', 'view', [ 'username' => $followed->username ] );
        }
    }
?>
