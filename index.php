<?php
    require_once 'header.php';

    function launchController( $resource, $get, $post = '', $files = '', $httpRequestMethod = 'GET' ) {
        try {
            $controller = controllerBase::findController( $resource );
            $controller->dispatch( $get, $post, $files, $httpRequestMethod );
        }
        catch ( ErrorRedirectException $e ) {
            launchController( $e->controller, $e->arguments );
        }
        catch ( HTTPRedirectException $e ) {
            global $config;

            $url = $config[ 'base' ] . $e->url;
            header( 'Location: ' . $url );
        }
    }

    $resource = 'dashboard';
    if ( isset( $_GET[ 'resource' ] ) ) {
        $resource = $_GET[ 'resource' ];
    }

    launchController( $resource, $_GET, $_POST, $_FILES, $_SERVER[ 'REQUEST_METHOD' ] );
?>
