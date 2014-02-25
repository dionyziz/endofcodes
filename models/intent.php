<?php
    define( 'DIRECTION_NONE', 0 );
    define( 'DIRECTION_NORTH', 1 );
    define( 'DIRECTION_EAST', 2 );
    define( 'DIRECTION_SOUTH', 3 );
    define( 'DIRECTION_WEST', 4 );
    define( 'ACTION_NONE', 0 );
    define( 'ACTION_MOVE', 1 );
    define( 'ACTION_ATTACK', 2 );

    function directionStringToConst( $direction ) {
        $directionMap = [
            'NONE' => DIRECTION_NONE,
            'NORTH' => DIRECTION_NORTH,
            'EAST' => DIRECTION_EAST,
            'SOUTH' => DIRECTION_SOUTH,
            'WEST' => DIRECTION_WEST
        ];
        if ( isset( $directionMap[ $direction ] ) ) {
            return $directionMap[ $direction ];
        }
        throw new ModelNotFoundException();
    }

    function directionConstToString( $direction ) {
        $directionMap = [
            DIRECTION_NONE => 'NONE',
            DIRECTION_NORTH => 'NORTH',
            DIRECTION_EAST => 'EAST',
            DIRECTION_SOUTH => 'SOUTH',
            DIRECTION_WEST => 'WEST'
        ];
        if ( isset( $directionMap[ $direction ] ) ) {
            return $directionMap[ $direction ];
        }
        throw new ModelNotFoundException();
    }

    function actionStringToConst( $action ) {
        $actionMap = [
            'NONE' => ACTION_NONE,
            'MOVE' => ACTION_MOVE,
            'ATTACK' => ACTION_ATTACK
        ];
        if ( isset( $actionMap[ $action ] ) ) {
            return $actionMap[ $action ];
        }
        throw new ModelNotFoundException();
    }

    function actionConstToString( $action ) {
        $actionMap = [
            ACTION_NONE => 'NONE',
            ACTION_MOVE => 'MOVE',
            ACTION_ATTACK => 'ATTACK'
        ];
        if ( isset( $actionMap[ $action ] ) ) {
            return $actionMap[ $action ];
        }
        throw new ModelNotFoundException();
    }

    class Intent {
        public $action;
        public $direction;
        public $creature;

        public function __construct( $action = ACTION_NONE, $direction = DIRECTION_NONE ) {
            $this->direction = $direction;
            $this->action = $action;
        }
    }
?>
