<?php
    define( 'DIRECTION_NONE', 0 );
    define( 'DIRECTION_NORTH', 1 );
    define( 'DIRECTION_EAST', 2 );
    define( 'DIRECTION_SOUTH', 3 );
    define( 'DIRECTION_WEST', 4 );
    define( 'ACTION_ATACK', 0 );
    define( 'ACTION_MOVE', 1 );
    class Intent {
        public $action;
        public $direction;

        public function __construct( $action, $direction ) {
            switch( $direction ) {
                case 'DIRECTION_NONE':
                    $this->direction = DIRECTION_NONE;
                    break;
                case 'DIRECTION_NORTH':
                    $this->direction = DIRECTION_NORTH;
                    break;
                case 'DIRECTION_EAST':
                    $this->direction = DIRECTION_EAST;
                    break;
                case 'DIRECTION_SOUTH':
                    $this->direction = DIRECTION_SOUTH;
                    break;
                case 'DIRECTION_WEST':
                    $this->direction = DIRECTION_SOUTH;
                    break;
                default:
                    $this->direction = NULL;
                    break;
            }

            switch( $action ) {
                case 'ACTION_ATACK':
                    $this->action = ACTION_ATACK;
                    break;
                case 'ACTION_MOVE':
                    $this->action = ACTION_MOVE;
                    break;
                default:
                    $this->action = NULL;
                    break;
            }
        }
    }
?>
