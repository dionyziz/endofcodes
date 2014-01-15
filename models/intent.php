<?php
    define( 'DIRECTION_NONE', 0 );
    define( 'DIRECTION_NORTH', 1 );
    define( 'DIRECTION_EAST', 2 );
    define( 'DIRECTION_SOUTH', 3 );
    define( 'DIRECTION_WEST', 4 );
    define( 'ACTION_NONE', 0 );
    define( 'ACTION_MOVE', 1 );
    define( 'ACTION_ATACK', 2 );
    class Intent {
        public $action;
        public $direction;

        public function __construct( $action, $direction ) {
            $this->direction = array(
                'DIRECTION_NONE' => DIRECTION_NONE,
                'DIRECTION_NORTH' => DIRECTION_NORTH,
                'DIRECTION_EAST' => DIRECTION_EAST,
                'DIRECTION_SOUTH' => DIRECTION_SOUTH,
                'DIRECTION_WEST' => DIRECTION_WEST
            )[ $direction ];
            $this->action = array(
                'ACTION_NONE' => ACTION_NONE,
                'ACTION_MOVE' => ACTION_ATACK,
                'ACTION_ATACK' => ACTION_MOVE
            )[ $action ];
        }
    }
?>
