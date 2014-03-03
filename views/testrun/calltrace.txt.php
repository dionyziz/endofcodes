<?php
    global $config;

    $numColumns = 3;
    $columnWidth = floor( ( $config[ 'cli_max_width' ] - 6 ) / $numColumns );

    echo "Calltrace:\n";
    echo "|";
    echo str_repeat( "=", $config[ 'cli_max_width' ] );
    echo "|\n";
    echo "| ";
    echo str_pad( "Filename", $columnWidth );
    echo "| ";
    echo str_pad( "Line", $columnWidth );
    echo "| ";
    echo str_pad( "Function", $columnWidth );
    echo "|\n";
    echo "|";
    echo str_repeat( "=", $config[ 'cli_max_width' ] );
    echo "|\n";
    foreach ( $test->calltrace as $call ) {
        echo "| ";
        if ( isset( $call[ 'file' ] ) ) {
            $file = $call[ 'file' ];
            if ( substr( $file, 0, strlen( $config[ 'root' ] ) ) == $config[ 'root' ] ) {
                $file = substr( $file, strlen( $config[ 'root' ] ) );
            }
            echo str_pad( substr( $file, -$columnWidth, $columnWidth ), $columnWidth );
        }
        else {
            echo str_repeat( " ", $columnWidth );
        }
        echo "| ";
        if ( isset( $call[ 'line' ] ) ) {
            echo str_pad( $call[ 'line' ], $columnWidth );
        }
        else {
            echo str_repeat( " ", $columnWidth );
        }
        echo "| ";
        ob_start();
        echo $call[ 'function' ];
        ?>(<?php
        $outputargs = [];
        foreach ( $call[ 'args' ] as $arg ) {
            ob_start();
            require 'views/testrun/variable.txt.php';
            $outputargs[] = ob_get_clean();
        }
        if ( count( $outputargs ) ) {
            echo " " . implode( ', ', $outputargs ) . " ";
        }
        ?>)<?php
        $function = ob_get_clean();
        echo str_pad( substr( $function, 0, $columnWidth ), $columnWidth );
        echo "|\n";
    }
    echo "|";
    echo str_repeat( "=", $config[ 'cli_max_width' ] );
    echo "|\n";
?>
