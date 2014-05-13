<?php
    require_once 'models/follow.php';
    class FollowController extends ControllerBase {
        public function create( $followedid ) {
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }

            $followedid = intval( $followedid );
            $follower = $_SESSION[ 'user' ];
            $followed = new User( $followedid );
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

            $followerid = $_SESSION[ 'user' ]->id;
            $followedid = intval( $followedid );
            $follow = new Follow( $followerid, $followedid );
            $follower = $follow->follower;
            $followed = $follow->followed;
            $follow->delete();
            go( 'user', 'view', [ 'username' => $followed->username ] );
        }
    }
?>
