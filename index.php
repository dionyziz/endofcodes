<?php
    require_once 'header.php';

    $resource = 'dashboard';
    if ( isset( $_GET[ 'resource' ] ) ) {
        $resource = $_GET[ 'resource' ];
    }
    try {
        $controller = controllerBase::findController( $resource );
        $controller->dispatch( $_GET, $_POST, $_FILES, $_SERVER[ 'REQUEST_METHOD' ] );
    }
    catch ( NotImplemented $e ) {
        die( 'An attempt was made to call a not implemented function: ' . $e->getFunctionName() );
    }
    catch ( ErrorRedirectException $e ) {
        $e->callErrorController(); //should catch exception inside the ControllerBase object.
    }
?>
