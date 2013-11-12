<?php
    class Mime {
        public function get( $name ) {
            return substr( $name, strrpos( $name, "." ) + 1 );
        }

        public function getValid() {
            return array( 'jpg', 'png', 'jpeg' );
        }

        public function valid( $ext ) {
            $valid = Extention::getValid();
            foreach ( $valid as $current ) {
                if ( $current === $ext ) {
                    return $current;
                }
            }
            return false;
        }
    }
?>
