<?php
    abstract class ControllerBase {
        protected $acceptTypes = [];
        public $environment = 'development';
        public $trusted = false;
        public $outputFormat = 'html';
        public $pageGenerationBegin; // Time marking the beginning of page generation, in epoch seconds.
        protected $method = 'view'; // Override to specify a default controller method.
        private $vars;
        private $httpRequestMethod;

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
        public function dispatch( $get, $post, $files, $httpRequestMethod ) {
            $this->initDebug();

            $this->init();
            $this->sessionCheck();
            $this->getAcceptTypes();

            $this->getControllerVars( $get, $post, $files, $httpRequestMethod );

            $this->getControllerMethod();
            return $this->callWithNamedArgs();
        }
        protected function init() {
            $this->getEnvironment();
            $this->getConfig();
            $this->dbInit();
        }
        private function sessionCheck() {
            global $config;

            if ( !isset( $_SESSION[ 'user' ] ) && isset( $_COOKIE[ $config[ 'persistent_cookie' ][ 'name' ] ] ) ) {
                require_once 'models/user.php';
                try {
                    $_SESSION[ 'user' ] = User::findBySessionId( $_COOKIE[ $cookiename ] );
                }
                catch ( ModelNotFoundException $e ) {
                }
            }
        }
        private function getAcceptTypes() {
            $this->acceptTypes = readHTTPAccept();
            if ( isset( $this->acceptTypes[ 'application/json' ] ) ) {
                $this->outputFormat = 'json';
            }
        }
        private function getControllerVars( $get, $post, $files, $httpRequestMethod ) {
            if ( isset( $get[ 'method' ] ) ) {
                $this->method = $get[ 'method' ];
            }
            $this->httpRequestMethod = $httpRequestMethod;
            $this->vars = [];
            switch ( $this->httpRequestMethod ) {
                case 'POST':
                    $this->vars = array_merge( $post, $files );
                    break;
                case 'GET':
                    $this->vars = $get;
                    $this->resource = $this->vars[ 'resource' ];
                    unset( $this->vars[ 'resource' ] );
                    unset( $this->vars[ 'method' ] );
                    break;
            }
            $this->protectFromForgery();
        }
        private function protectFromForgery() {
            if ( $this->httpRequestMethod === 'POST' && !$this->trusted ) {
                if ( !isset( $this->vars[ 'token' ] )
                    || !isset( $_SESSION[ 'form' ] )
                    || !isset( $_SESSION[ 'form' ][ 'token' ] )
                    || $this->vars[ 'token' ] !== $_SESSION[ 'form' ][ 'token' ] ) {
                    throw new HTTPUnauthorizedException( 'Your CSRF token was invalid.' );
                }
            }
        }
        private function getControllerMethod() {
            try {
                if ( Form::getRESTMethodIdempotence( $this->method ) && $this->httpRequestMethod != 'POST' ) {
                    $this->method .= 'View';
                }
            }
            catch ( HTMLFormInvalidException $e ) {
                throw new HTTPNotFoundException( $this->method . ' is not a valid REST method.' );
            }
        }
        private function callWithNamedArgs() {
            $controllerReflection = new ReflectionObject( $this );
            try {
                $methodReflection = $controllerReflection->getMethod( $this->method );
            }
            catch ( ReflectionException $e ) {
                throw new HTTPNotFoundException( $this->method . ' is not a method of ' . $controllerReflection->getShortName() . '.' );
            }

            $arguments = $this->getNamedArguments( $methodReflection );
            return call_user_func_array( [ $this, $this->method ], $arguments );
        }
        private function getNamedArguments( $methodReflection ) {
            $arguments = [];
            foreach ( $methodReflection->getParameters() as $parameter ) {
                if ( isset( $this->vars[ $parameter->name ] ) ) {
                    $arguments[] = $this->vars[ $parameter->name ];
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
            return $arguments;
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
        protected function dbInit() {
            try {
                dbInit();
            }
            catch ( DBException $e ) {
                throw new ErrorRedirectException( 'dbconfig', get_object_vars( $e ) );
            }
        }
        private function initDebug() {
            global $debugger;

            $this->pageGenerationBegin = microtime( true );

            if ( isset( $_SESSION[ 'debug' ] ) ) {
                $debugger = new Debugger();
            }
            else {
                $debugger = new DummyDebugger();
            }
        }
        public function getPageGenerationTime() {
            return microtime( true ) - $this->pageGenerationBegin;
        }
    }
?>
