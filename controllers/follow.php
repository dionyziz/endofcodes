<?php
    include_once 'models/follow.php';
    class FollowController extends ControllerBase {
        public function create( $followerid, $followedid ) {
            $followerid = intval( $followerid );
            $followedid = intval( $followedid );
            $follower = new User( $followerid );
            $followed = new User( $followedid );
            $follow = new Follow(); 
            $follow->follower = $follower;
            $follow->followed = $followed;
            $follow->save();
            go( 'user', 'view', array( 'username' => $followed->username ) ); 
        }

        public function delete( $followerid, $followedid ) {
            $followerid = intval( $followerid );
            $followedid = intval( $followedid );
            if ( $followerid !== $_SESSION[ 'user' ]->id ) {
                throw new HTTPUnauthorizedException();
            }
            $follow = new Follow( $followerid, $followedid );
            $follower = $follow->follower;
            $followed = $follow->followed;
            $follow->delete();
            go( 'user', 'view', array( 'username' => $followed->username ) ); 
        }
    }
?>
