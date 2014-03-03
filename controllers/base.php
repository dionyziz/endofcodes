<?php
    abstract class ControllerBase {
        protected $environment = 'development';
        public $trusted = false;
        public $outputFormat = 'html';

        public static function findController( $resource ) {
            $resource = basename( $resource );
            $filename = 'controllers/' . $resource . '.php';
            if ( !file_exists( $filename ) ) {
                $resource = 'dashboard';
                $filename = 'controllers/' . $resource . '.php';
            }
            require_once $filename;
            $controllername = ucfirst( $resource ) . 'Controller';
            $controller = new $controllername();

            return $controller;
        }
        protected function protectFromForgery( $token = '', $http_request_method = '' ) {
            if ( $http_request_method === 'POST'
            && ( !isset( $_SESSION[ 'form' ] )
              || !isset( $_SESSION[ 'form' ][ 'token' ] )
              || $token !== $_SESSION[ 'form' ][ 'token' ]
              || $token == '' )
            && !$this->trusted ) {
                throw new HTTPUnauthorizedException();
            }
        }
        protected function getControllerMethod( $requested_method, $http_request_method ) {
            $method = $requested_method;

            try {
                if ( Form::getRESTMethodIdempotence( $method ) === 1 && $http_request_method != 'POST' ) {
                    $method .= 'View';
                }
            }
            catch ( HTMLFormInvalidException $e ) {
                $method = 'view';
            }

            return $method;
        }
        protected function getControllerVars( $get, $post, $files, $http_request_method ) {
            switch ( $http_request_method ) {
                case 'POST':
                    $vars = array_merge( $post, $files );
                    break;
                case 'GET':
                    $vars = $get;
                    break;
                default:
                    $vars = [];
                    break;
            }

            return $vars;
        }
        protected function sessionCheck() {
            global $config;

            if ( isset( $_SESSION[ 'user' ] ) ) {
                return;
            }
            $cookiename = $config[ 'persistent_cookie' ][ 'name' ];
            if ( isset( $_COOKIE[ $cookiename ] ) ) {
                require_once 'models/user.php';
                try {
                    $user = User::findBySessionId( $_COOKIE[ $cookiename ] );
                }
                catch ( ModelNotFoundException $e ) {
                    throw new HTTPUnauthorizedException();
                }
                $_SESSION[ 'user' ] = $user;
            }
        }
        protected function callWithNamedArgs( $method_reflection, $callable, $vars ) {
            $parameters = $method_reflection->getParameters();
            $arguments = [];

            foreach ( $parameters as $parameter ) {
                if ( isset( $vars[ $parameter->name ] ) ) {
                    $arguments[] = $vars[ $parameter->name ];
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
            return call_user_func_array( $callable, $arguments );
        }
        protected function loadConfig() {
            global $config;

            $config = getConfig()[ $this->environment ];
        }
        protected function init() {
            $this->loadConfig();
            dbInit();
        }
        public function dispatch( $get, $post, $files, $http_request_method ) {
            $this->init();
            $this->sessionCheck();

            if ( !isset( $get[ 'method' ] ) ) {
                $get[ 'method' ] = '';
            }
            $method = $this->getControllerMethod( $get[ 'method' ], $http_request_method );
            $vars = $this->getControllerVars( $get, $post, $files, $http_request_method );
            if ( !isset( $vars[ 'token' ] ) ) {
                $token = '';
            }
            else {
                $token = $vars[ 'token' ];
            }
            $this->protectFromForgery( $token, $http_request_method );
            $this_reflection = new ReflectionObject( $this );
            $method_reflection = $this_reflection->getMethod( $method );

            return $this->callWithNamedArgs( $method_reflection, [ $this, $method ], $vars );
        }
    }
?>
