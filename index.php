<?php
    require_once 'header.php';

    if ( isset( $_GET[ 'resource' ] ) ) {
        $resource = $_GET[ 'resource' ];
    }
    else {
        $resource = 'dashboard';
    }
    try {
        $controller = controllerBase::findController( $resource );
        $controller->dispatch( $_GET, $_POST, $_FILES, $_SERVER[ 'REQUEST_METHOD' ] );
    }
    catch ( NotImplemented $e ) {
        die( 'An attempt was made to call a not implemented function: ' . $e->getFunctionName() );
    }
    catch ( RedirectException $e ) {
        $controller = controllerBase::findController( $resource );
        $controller->dispatch( $_GET, $_POST, $_FILES, $_SERVER[ 'REQUEST_METHOD' ] );
        // global $config;

        // $url = $e->getURL();

        // header( 'Location: ' . $config[ 'base' ] . $url );
    }
    catch ( HTTPErrorException $e ) {
        // $e->outputErrorPage();
        $arguments = get_object_vars( $e );
        $arguments['method'] = 'create';
        $controller = controllerBase::findController( 'httperror' );
        $controller->dispatch( $arguments, '', '', 'GET' );
    }
?>
