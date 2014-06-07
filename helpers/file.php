<?php
    function recursiveCopy( $src, $dst ) {
        $dir = opendir( $src );
        @mkdir( $dst );
        while( false !== ( $file = readdir( $dir ) ) ) {
            if ( $file != '.' && $file != '..' ) {
                if ( is_dir( $src . '/' . $file ) ) {
                    recursiveCopy( $src . '/' . $file, $dst . '/' . $file );
                }
                else {
                    copy( $src . '/' . $file, $dst . '/' . $file );
                }
            }
        }
        closedir( $dir );
    }
?>
