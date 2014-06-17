<?php
    abstract class ControllerBase {
        protected $acceptTypes = [];
        public $environment = 'development';
        public $trusted = false;
        public $outputFormat = 'html';
        public $pageGenerationBegin; // Time marking the beginning of page generation, in epoch seconds.
        public $method = 'view'; // Override to specify a default controller method.
        private $vars;

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
            $this->init();
            $this->sessionCheck();
            $this->getAcceptTypes();

            $this->getControllerVars( $get, $post, $files, $httpRequestMethod );

            $this->getControllerMethod();
            return $this->callWithNamedArgs();
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
        private function getControllerVars( $get, $post, $files, $httpRequestMethod ) {
            $this->vars = [];
            switch ( $httpRequestMethod ) {
                case 'POST':
                    $this->vars = array_merge( $post, $files );
                    break;
                case 'GET':
                    $this->vars = $get;
                    break;
            }
            $this->protectFromForgery( $this->vars, $httpRequestMethod );
        }
        private function protectFromForgery( $vars, $httpRequestMethod ) {
            if ( $httpRequestMethod === 'POST' && !$this->trusted ) {
                if ( !isset( $vars[ 'token' ] )
                    || !isset( $_SESSION[ 'form' ] )
                    || !isset( $_SESSION[ 'form' ][ 'token' ] )
                    || $vars[ 'token' ] !== $_SESSION[ 'form' ][ 'token' ] ) {
                    throw new HTTPUnauthorizedException( 'Your CSRF token was invalid.' );
                }
            }
        }
        private function getControllerMethod() {
            if ( isset( $this->vars[ 'method' ] ) ) {
                $this->method = $this->vars[ 'method' ];
            }
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
                    }
                }
            }
            return call_user_func_array( [ $this, $this->method ], $arguments );
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
        private function getAcceptTypes() {
            $this->acceptTypes = readHTTPAccept();
            if ( isset( $this->acceptTypes[ 'application/json' ] ) ) {
                $this->outputFormat = 'json';
            }
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
        protected function init() {
            $this->initDebug();
            $this->getEnvironment();
            $this->getConfig();
            $this->dbInit();
        }
        public function getPageGenerationTime() {
            return microtime( true ) - $this->pageGenerationBegin;
        }
    }

    class ErrorRedirectException extends Exception {
        private $controller;
        private $arguments;
        public function __construct( $controller, $arguments ) {
            $this->controller = $controller;
            $this->arguments = $arguments;
        }
        public function callErrorController() {
            $controller = controllerBase::findController( $this->controller );
            $controller->dispatch( $this->arguments, '', '', 'GET' );
        }
    }
?>
