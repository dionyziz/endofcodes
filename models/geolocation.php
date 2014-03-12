<?php
    class Location {
        protected function info() {
            return var_export(
                unserialize(
                    file_get_contents( 
                        'http://www.geoplugin.net/php.gp?ip=' . $_SERVER[ 'REMOTE_ADDR' ] 
                    ) 
                ) 
            ); 
        }

        public static function getCountryCode() {
            $info = $this->info();
            return $info[ 'geoplugin_countryCode' ];
        }
    }
?>

