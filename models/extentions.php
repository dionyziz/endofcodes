<?php
    class Extention {
        public function get( $name ) {
            if ( strrpos( $name, "." ) === false ) {
                return "";
            }
            return substr( $name, strrpos( $name, "." ) + 1 );
        }

        public function getValid() {
            return array( 'jpg', 'png', 'jpeg' );
        }

        public function valid( $ext ) {
            $valid = Extention::getValid();
            foreach ( $valid as $current ) {
                if ( $current === $ext ) {
                    return true;
                }
            }
            return false;
        }
    }
?>
