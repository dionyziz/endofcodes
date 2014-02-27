<?php
    function listing( $target_path ) {
        $dir = opendir( $target_path ); 
        $names = array();
        while ( $read = readdir( $dir ) ) {
            if ( $read != '.' && $read != '..' && $read != 'migration.php' ) {
                $names[] = $read;
            }
        } 
        closedir( $dir );
        return $names;
    }
?>
