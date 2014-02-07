<?php
    include_once 'models/dependencies.php';
    include_once 'header.php';

    if ( isset( $_GET[ 'resource' ] ) ) {
        $resource = $_GET[ 'resource' ];
    }
    else {
        $resource = '';
    }
    $resource = basename( $resource );
    $filename = 'controllers/' . $resource . '.php';
    if ( !file_exists( $filename ) ) {
        $resource = 'dashboard';
        $filename = 'controllers/' . $resource . '.php';
    }
    include_once $filename;
    $controllername = ucfirst( $resource ) . 'Controller';
    $controller = new $controllername();
    try {
        $controller->dispatch( $_GET, $_POST, $_FILES, $_SERVER[ 'REQUEST_METHOD' ] );
    }
    catch ( NotImplemented $e ) {
        die( 'An attempt was made to call a not implemented function: ' . $e->getFunctionName() );
    }
    catch ( RedirectException $e ) {
        global $config;

        $url = $e->getURL();

        header( 'Location: ' . $config[ 'base' ] . $url );
    }
    catch ( HTTPErrorException $e ) {
        header( $e->header );
    }
?>
