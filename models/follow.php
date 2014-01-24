<?php
    class Follow extends ActiveRecordBase {
        public $follower;
        public $followed;

        public function __construct( $followerid = false, $followedid = false ) {
            if ( $followerid !== false && $followedid !== false ) {
                try {
                    $res = dbSelectOne(
                        'follows',
                        array( 'followerid', 'followedid' ),
                        compact( 'followerid', 'followedid' )
                    );
                }
                catch ( DBException $e ) {
                    throw new ModelNotFoundException();
                }
                $this->follower = new User( $followerid );
                $this->followed = new User( $followedid );
            }
        }

        public function validate() {
            if ( !is_int( $this->follower->id ) ) {
                throw new ModelValidationException( 'followerid_not_valid' );
            }
            if ( !is_int( $this->followed->id ) ) {
                throw new ModelValidationException( 'followedid_not_valid' );
            }
        }

        public function create() {
            $followerid = $this->follower->id;
            $followedid = $this->followed->id;
            dbInsert(
                'follows',
                compact( 'followerid', 'followedid' )
            );
        }

        public function delete() {
            $followerid = $this->follower->id;
            $followedid = $this->followed->id;
            dbDelete(
                'follows',
                compact( 'followerid', 'followedid' )
            );
        }
    }
?>
