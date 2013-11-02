<?php
    include 'models/database.php';
    if ( $_GET[ 'wrong_contr' ] ) {
        ?><p>Wrong controller</p><?php
    }
    $resource = $_GET[ 'resource' ];
    $method = $_GET[ 'method' ];
    if ( !isset( $resource ) && !isset( $method ) ) {
        $resource = 'Dashboard';
        $method = 'listing';
    }
    $resource = basename( $resource );
    $controller = $resource . 'Controller';
    if ( file_exists( 'controllers/' . strtolower( $resource ) . '.php' )  ) {
        include 'controllers/' . strtolower( $resource ) . '.php';
    }
    else {
        header( 'Location: index.php?wrong_contr=yes' );
        die();
    }
    $controller::$method();
?>
