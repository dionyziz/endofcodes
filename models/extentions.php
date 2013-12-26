<?php
    class Extention {
        public function get( $name ) {
            if ( strrpos( $name, "." ) === false ) {
                return "";
            }
            return substr( $name, strrpos( $name, "." ) + 1 );
        }

        public function getValid() {
            global $config;
            return $config[ 'files' ][ 'avatar_extentions' ];
        }

        public function valid( $ext ) {
            $valid = Extention::getValid();
            $valid = array_flip( $valid );
            return isset( $valid[ $ext ] );
        }
    }
?>
