<?php
    class Follow extends ActiveRecordBase {
        public $followerid;
        public $followedid;

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
                $this->followerid = $followerid;
                $this->followedid = $followedid;
            }
        }

        public function validate() {
            if ( !is_int( $this->followerid ) ) {
                throw new ModelValidationException( 'followerid_not_valid' );
            }
            if ( !is_int( $this->followedid ) ) {
                throw new ModelValidationException( 'followedid_not_valid' );
            }
        }

        public function create() {
            $followerid = $this->followerid;
            $followedid = $this->followedid;
            dbInsert(
                'follows',
                compact( 'followerid', 'followedid' )
            );
        }

        public function delete() {
            $followerid = $this->followerid;
            $followedid = $this->followedid;
            dbDelete(
                'follows',
                compact( 'followerid', 'followedid' )
            );
        }
    }
?>
