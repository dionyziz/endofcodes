<?php
    define( 'NONE', 0 );
    define( 'NORTH', 1 );
    define( 'EAST', 2 );
    define( 'SOUTH', 3 );
    define( 'WEST', 4 );
    define( 'MOVE', 1 );
    define( 'ATACK', 2 );

    function convertDirection( $direction ) {
        $directionMap = array(
            'NONE' => NONE,
            'NORTH' => NORTH,
            'EAST' => EAST,
            'SOUTH' => SOUTH,
            'WEST' => WEST
        );
        if ( isset( $directionMap[ $direction ] ) ) {
            return $directionMap[ $direction ];
        }
        $directionMap = array_flip( $directionMap );
        if ( isset( $directionMap[ $direction ] ) ) {
            return $directionMap[ $direction ];
        }
        return false;
    }

    function convertAction( $action ) {
        $actionMap = array(
            'NONE' => NONE,
            'MOVE' => MOVE,
            'ATACK' => ATACK
        );
        if ( isset( $actionMap[ $action ] ) ) {
            return $actionMap[ $action ];
        }
        $actionMap = array_flip( $actionMap );
        if ( isset( $actionMap[ $action ] ) ) {
            return $actionMap[ $action ];
        }
        return false;
    }

    class Intent {
        public $action;
        public $direction;
        public $creature;

        public function __construct( $action, $direction ) {
            $this->direction = $direction;
            $this->action = $action;
        }
    }
?>
