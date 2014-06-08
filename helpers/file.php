<?php
    function recursiveCopy( $src, $dst ) {
        $src = escapeshellarg( $src );
        $dst = escapeshellarg( $dst );
        exec( "cp -R $src $dst" );
    }
?>
