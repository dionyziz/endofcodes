<?php
    class FollowController extends ControllerBase {
        public function create( $followerid, $followedid ) {
            include_once 'models/follow.php';
            $followerid = intval( $followerid );
            $followedid = intval( $followedid );
            $follow = new Follow(); 
            $follow->followerid = $followerid;
            $follow->followedid = $followedid;
            $follow->save();
        }

        public function delete( $followerid, $followedid ) {
            $follow = new Follow( $followerid, $followedid );
            $follow->delete();
        }
    }
?>
