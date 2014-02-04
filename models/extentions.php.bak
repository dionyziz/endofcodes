<?php
    class Extention {
        public static function get( $name ) {
            if ( strrpos( $name, "." ) === false ) {
                return "";
            }
            return substr( $name, strrpos( $name, "." ) + 1 );
        }

        public static function remove( $name ) {
            return substr( $name, 0, strrpos( $name, "." ) );
        }

        public static function getValid() {
            global $config;
            return $config[ 'files' ][ 'avatar_extentions' ];
        }

        public static function valid( $ext ) {
            $valid = Extention::getValid();
            $valid = array_flip( $valid );
            return isset( $valid[ $ext ] );
        }
    }
?>
