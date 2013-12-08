<?php
    function includeStyle( $path ) {
        ?><link
            rel="stylesheet"
            type="text/css"
            href="<?php
                echo "static/style/" . $path . ".css";
            ?>" /><?php
    }
?>
