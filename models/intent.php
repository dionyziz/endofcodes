<?php
    class Intent {
        public $action;
        public $direction;

        public function __construct( $action, $direction ) {
            $this->action = $action;
            $this->direction = $direction;
        }
    }
?>
