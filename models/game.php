<?php
    class Game extends ActiveRecordBase {
        public $id;
        public $created;
        public $width;
        public $height;
        public $rounds;

        public function __construct( $id = false ) {
            if ( $id ) {
                $game_info = dbSelectOne( 'games', array( 'created', 'width', 'height' ), compact( 'id' ) ); 
                $this->id = $id;
                $this->created = $game_info[ 'created' ];
                $this->width = $game_info[ 'width' ];
                $this->height = $game_info[ 'height' ];
            }
        }

        protected function validate() {
            if ( !is_int( $this->width ) ) {
                throw new ModelValidationException( 'width_not_valid' );
            }
            if ( !is_int( $this->height ) ) {
                throw new ModelValidationException( 'height_not_valid' );
            }
        }

        protected function create() {
            $width = $this->width;
            $height = $this->height;
            $created = $this->created; 
            $this->id = dbInsert(
                'games',
                compact( 'width', 'height', 'created' )
            );
        }
    }
?>
