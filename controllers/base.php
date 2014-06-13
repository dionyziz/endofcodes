<?php
    abstract class ControllerBase {
        protected $acceptTypes = [];
        protected $environment = 'development';
        protected $trusted = false;
        protected $outputFormat = 'html';
        protected $pageGenerationBegin; // time marking the beginning of page generation, in epoch seconds
        protected $method = 'view'; // Override to specify a default controller method.

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
        private function sessionCheck() {
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
        private function getControllerVars( $get, $post, $files, $httpRequestMethod ) {
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
        private function protectFromForgery( $vars, $httpRequestMethod ) {
            if ( $this->trusted ) {
                return;
            }
            if ( $httpRequestMethod === 'POST' ) {
                if ( !isset( $vars[ 'token' ] )
                    || !isset( $_SESSION[ 'form' ] )
                    || !isset( $_SESSION[ 'form' ][ 'token' ] )
                    || $vars[ 'token' ] !== $_SESSION[ 'form' ][ 'token' ] ) {
                    throw new HTTPUnauthorizedException( 'Your CSRF token was invalid.' );
                }
            }
        }
        private function getControllerMethod( $vars, $httpRequestMethod ) {
            if ( isset( $vars[ 'method' ] ) ) {
                $this->method = $vars[ 'method' ];
            }
            try {
                $idempotence = Form::getRESTMethodIdempotence( $this->method ) === 1 ;
            }
            catch ( HTMLFormInvalidException $e ) {
                throw new HTTPNotFoundException( $this->method . ' is not a valid REST method.' );
            }
            if ( $idempotence && $httpRequestMethod != 'POST' ) {
                    $this->method .= 'View';
            }
            return $this->method;
        }
        private function callWithNamedArgs( $method, $vars ) {
            $controllerReflection = new ReflectionObject( $this );
            try {
                $methodReflection = $controllerReflection->getMethod( $method );
            }
            catch ( ReflectionException $e ) {
                throw new HTTPNotFoundException( $method . ' is not a method of ' . $controllerReflection->getShortName() . '.' );
            }

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
            return call_user_func_array( [ $this, $method ], $arguments );
        }
        protected function getEnvironment() {
            if ( getEnv( 'ENVIRONMENT' ) !== false ) {
                $this->environment = getEnv( 'ENVIRONMENT' );
            }
        }
        protected function getConfig() {
            global $config;

            $config = loadConfig( $this->environment );
        }
        private function readHTTPAccept() {
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
        protected function dbInit() {
            try {
                dbInit();
            }
            catch ( DBException $e ) {
                $arguments = get_object_vars( $e );
                $arguments['method'] = 'create';
                $controller = controllerBase::findController( 'dbconfig' );
                $controller->dispatch( $arguments, '', '', 'GET' );
                exit(0);
                //go( 'dbconfig', 'create', $arguments );
            }
        }
        private function initDebug() {
            global $debugger;

            if ( isset( $_SESSION[ 'debug' ] ) ) {
                $debugger = new Debugger();
            }
            else {
                $debugger = new DummyDebugger();
            }
        }
        protected function init() {
            $this->getEnvironment();
            $this->getConfig();
            $this->initDebug();
            $this->readHTTPAccept();
            $this->dbInit();
        }
        public function dispatch( $get, $post, $files, $httpRequestMethod ) {
            $this->pageGenerationBegin = microtime( true );

            $this->init();
            $this->sessionCheck();

            $vars = $this->getControllerVars( $get, $post, $files, $httpRequestMethod );
            $this->protectFromForgery( $vars, $httpRequestMethod );

            $method = $this->getControllerMethod( $vars, $httpRequestMethod );
            return $this->callWithNamedArgs( $method, $vars );
        }
        public function getPageGenerationTime() {
            return microtime( true ) - $this->pageGenerationBegin;
        }
    }
?>
