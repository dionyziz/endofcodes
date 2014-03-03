<?php
    require_once 'header.php';

    if ( isset( $_GET[ 'resource' ] ) ) {
        $resource = $_GET[ 'resource' ];
    }
    else {
        $resource = '';
    }
    $controller = controllerBase::findController( $resource );
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
