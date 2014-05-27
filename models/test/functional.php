<?php
    require_once 'models/lib/simple_html_dom.php';

    abstract class FunctionalTest extends UnitTest {
        public function request( $resource, $method, $verb = 'GET', $vars = [] ) {
            $request = new FunctionalTestRequest( $this, $resource, $method, [], $verb, $vars );
            return $request->execute();
        }
    }

    class FunctionalTestRequest {
        protected $unittest;
        public $resource;
        public $method;
        public $verb;
        public $vars;
        public $session;

        public function __construct( UnitTest $unittest, $resource, $method, $session, $verb, $vars ) {
            $this->unittest = $unittest;
            $this->resource = $resource;
            $this->method = $method;
            $this->verb = $verb;
            $this->vars = $vars;
            $this->session = $session;
        }
        public function execute() {
            $oldSession = $_SESSION;
            try {
                ob_start();
                $controller = controllerBase::findController( $this->resource );
                $controller->trusted = true;
                $get = [ 'method' => $this->method ];
                $post = [];
                if ( $this->verb == 'GET' ) {
                    $get = array_merge( $get, $this->vars );
                }
                else {
                    $post = array_merge( $post, $this->vars );
                }
                $controller->environment = 'test';
                $controller->dispatch( $get, $post, [], $this->verb );
                $content = ob_get_clean();

                $response = new FunctionalTestResponse( $this->unittest, $content );
            }
            catch ( RedirectException $e ) {
                // maintain $_SESSION across redirect
                $redirect = new FunctionalTestRequest( $this->unittest, $e->resource, $e->method, $_SESSION, 'GET', $e->args );

                $response = $redirect->execute();
            }
            // clean up
            $_SESSION = $oldSession;

            return $response;
        }
    }

    class FunctionalTestResponse {
        protected $unittest;
        public $content;
        public $dom;

        public function __construct( UnitTest $unittest, $content ) {
            $this->content = $content;
            $this->unittest = $unittest;
            $this->dom = str_get_html( $content );
        }
        public function assertHas( $selector, $description = '' ) {
            $this->unittest->assertTrue( ( bool )$this->dom->find( $selector ), $description );
        }
    }
?>
