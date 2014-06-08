<?php
    abstract class ControllerBase {
        protected $acceptTypes = [];
        public $environment = 'development';
        public $trusted = false;
        public $outputFormat = 'html';
        public $pageGenerationBegin; // time marking the beginning of page generation, in epoch seconds

        public static function findController( $resource ) {
            $resource = basename( $resource );
            $filename = 'controllers/' . $resource . '.php';
            if ( !file_exists( $filename ) ) {
                throw new HTTPNotFoundException( 'The resource "' . $filename . '" specified was invalid' );
            }
            require_once $filename;
            $controllername = ucfirst( $resource ) . 'Controller';
            $controller = new $controllername();

            return $controller;
        }
        protected function protectFromForgery( $token = '', $httpRequestMethod = '' ) {
            if ( $httpRequestMethod === 'POST'
            && ( !isset( $_SESSION[ 'form' ] )
              || !isset( $_SESSION[ 'form' ][ 'token' ] )
              || $token !== $_SESSION[ 'form' ][ 'token' ]
              || $token == '' )
            && !$this->trusted ) {
                throw new HTTPUnauthorizedException( 'Your CSRF token was invalid.' );
            }
        }
        protected function getControllerMethod( $requestedMethod, $httpRequestMethod ) {
            $method = $requestedMethod;

            try {
                if ( Form::getRESTMethodIdempotence( $method ) === 1 && $httpRequestMethod != 'POST' ) {
                    $method .= 'View';
                }
            }
            catch ( HTMLFormInvalidException $e ) {
                $method = 'view';
            }

            return $method;
        }
        protected function getControllerVars( $get, $post, $files, $httpRequestMethod ) {
            switch ( $httpRequestMethod ) {
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
                    return;
                }
                $_SESSION[ 'user' ] = $user;
            }
        }
        protected function callWithNamedArgs( $methodReflection, $callable, $vars ) {
            $parameters = $methodReflection->getParameters();
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

            if ( getEnv( 'ENVIRONMENT' ) !== false ) {
                $env = getEnv( 'ENVIRONMENT' );
            }
            else {
                $env = $this->environment;
            }
            $config = getConfig( $env );
        }
        protected function readHTTPAccept() {
            if ( !isset( $_SERVER[ 'HTTP_ACCEPT' ] ) ) {
                return;
            }
            $accept = strtolower( str_replace( ' ', '', $_SERVER[ 'HTTP_ACCEPT' ] ) );
            $accept = explode( ',', $accept );
            $acceptTypes = [];
            foreach ( $accept as $a ) {
                if ( strpos( $a, ';q=' ) ) {
                    list( $a, $q ) = explode( ';q=', $a );
                    if ( $q === 0 ) {
                        continue;
                    }
                }
                $acceptTypes[ $a ] = true;
            }
            $this->acceptTypes = $acceptTypes;
            if ( isset( $this->acceptTypes[ 'application/json' ] ) ) {
                $this->outputFormat = 'json';
            }
        }
        protected function init() {
            $this->initDebug();
            $this->loadConfig();
            $this->readHTTPAccept();
            dbInit();
        }
        public function initDebug() {
            global $debugger;

            if ( isset( $_SESSION[ 'debug' ] ) ) {
                $debugger = new Debugger();
            }
            else {
                $debugger = new DummyDebugger();
            }
        }
        public function dispatch( $get, $post, $files, $httpRequestMethod ) {
            $this->pageGenerationBegin = microtime( true );

            $this->init();
            $this->sessionCheck();

            if ( !isset( $get[ 'method' ] ) ) {
                $get[ 'method' ] = '';
            }
            $method = $this->getControllerMethod( $get[ 'method' ], $httpRequestMethod );
            $vars = $this->getControllerVars( $get, $post, $files, $httpRequestMethod );
            if ( !isset( $vars[ 'token' ] ) ) {
                $token = '';
            }
            else {
                $token = $vars[ 'token' ];
            }
            $this->protectFromForgery( $token, $httpRequestMethod );
            $thisReflection = new ReflectionObject( $this );
            $methodReflection = $thisReflection->getMethod( $method );

            return $this->callWithNamedArgs( $methodReflection, [ $this, $method ], $vars );
        }
        public function getPageGenerationTime() {
            return microtime( true ) - $this->pageGenerationBegin;
        }
    }
?>
