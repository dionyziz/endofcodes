<?php
    class Follow extends ActiveRecordBase {
        public $follower;
        public $followed;
        protected static $attributes = [ 'followerid', 'followedid' ];
        protected $followerid;
        protected $followedid;
        protected static $tableName = 'follows';

        public function __construct( $followerid = false, $followedid = false ) {
            if ( $followerid !== false && $followedid !== false ) {
                try {
                    $res = dbSelectOne(
                        'follows',
                        [ 'followerid', 'followedid' ],
                        compact( 'followerid', 'followedid' )
                    );
                }
                catch ( DBExceptionWrongCount $e ) {
                    throw new ModelNotFoundException();
                }
                $this->follower = new User( $followerid );
                $this->followed = new User( $followedid );
            }
        }

        protected function onBeforeSave() {
            if ( !is_int( $this->follower->id ) ) {
                throw new ModelValidationException( 'followerid_not_valid' );
            }
            if ( !is_int( $this->followed->id ) ) {
                throw new ModelValidationException( 'followedid_not_valid' );
            }
        }

        protected function onBeforeCreate() {
            $this->followerid = $this->follower->id;
            $this->followedid = $this->followed->id;
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
