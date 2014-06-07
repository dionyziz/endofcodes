<?php
    function recurse_copy( $src, $dst ) {
        $dir = opendir( $src );
        @mkdir( $dst );
        while( false !== ( $file = readdir( $dir ) ) ) {
            if ( $file != '.' && $file != '..' ) {
                if ( is_dir( $src . '/' . $file ) ) {
                    recurse_copy( $src . '/' . $file, $dst . '/' . $file );
                }
                else {
                    copy( $src . '/' . $file, $dst . '/' . $file );
                }
            }
        }
        closedir( $dir );
    }
    function recurse_delete( $src ) {
        $dir = opendir( $src );
        while( false !== ( $file = readdir( $dir ) ) ) {
            if ( $file != '.' && $file != '..' ) {
                if ( is_dir( $src . '/' . $file ) ) {
                    recurse_delete( $src . '/' . $file );
                    rmdir( $src . '/' . $file );
                }
                else {
                    unlink( $src . '/' . $file );
                }
            }
        }
        closedir( $dir );
        rmdir( $src );
    }
    function recurse_chmod( $src, $mode ) {
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $src ), RecursiveIteratorIterator::SELF_FIRST );

        foreach ( $iterator as $item ) {
            chmod( $item, $mode );
        }
    }
?>
