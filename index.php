<?php
    include 'header.php';
    include 'models/dependencies.php';
    $methods = array(
        'create' => 1,
        'listing' => 0,
        'delete' => 1,
        'update' => 1,
        'view' => 0
    );
    if ( isset( $_GET[ 'resource' ] ) ) {
        $resource = $_GET[ 'resource' ];
    }
    else {
        $resource = '';
    }
    if ( isset( $_GET[ 'method' ] ) ) {
        $method = $_GET[ 'method' ];
    }
    else {
        $method = '';
    }
    if ( !isset( $methods[ $method ] ) ) {
        $method = 'view';
    }
    switch ( $_SERVER[ 'REQUEST_METHOD' ] ) {
        case 'POST':
            $http_vars = $_POST; 
            break;
        case 'GET':
            $http_vars = $_GET;
            break;
        default:
            $http_vars = array(); 
            break;
    }
    if ( $methods[ $method ] == 1 && $_SERVER[ 'REQUEST_METHOD' ] != 'POST' ) {
        $method .= 'View';
    }
    $resource = basename( $resource );
    $filename = 'controllers/' . $resource . '.php';
    if ( !file_exists( $filename ) ) {
        $resource = 'dashboard';
        $method = 'view';
        $filename = 'controllers/' . $resource . '.php';
    }
    include $filename;
    $controllername = ucfirst( $resource ) . 'Controller';
    $reflection = new ReflectionMethod( $controllername, $method );
    $parameters = $reflection->getParameters();
    $arguments = array();
    foreach ( $parameters as $parameter ) {
        if ( isset( $http_vars[ $parameter->name ] ) ) {
            $arguments[] = $http_vars[ $parameter->name ];
        }
        else {
            try {
                $arguments[] = $parameter->getDefaultValue();
            }
            catch ( ReflectionException $e ) {
                $arguments[] = null;
            }
        }
    }
    try {
        call_user_func_array( array( $controllername, $method ), $arguments );
    }
    catch ( NotImplemented $e ) {
        die( 'An attempt was made to call a not implemented function: ' . $e->getFunctionName() );
    }
    catch ( RedirectException $e ) {
        $url = $e->getURL();
        header( 'Location: ' . $url );
    }
    catch ( Exception $e ) {
        die( $controllername . '::' . $method . ' call rejected: ' . $e->getMessage() );
    }
    catch ( HTTPErrorException $e ) {
        header( $e->header );
    }
?>
