<?php
    require_once 'models/lib/simple_html_dom.php';

    abstract class FunctionalTest extends UnitTest {
        public function request( $resource, $method, $verb = 'GET', $vars = [] ) {
            $request = new FunctionalTestRequest( $this, $resource, $method, [], $verb, $vars );
            return $request->execute();
        }
    }

    class FunctionalTestRequest {
        protected $unitTest;
        public $resource;
        public $method;
        public $verb;
        public $vars;
        public $session;

        public function __construct( UnitTest $unitTest, $resource, $method, $session, $verb, $vars ) {
            $this->unitTest = $unitTest;
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

                $response = new FunctionalTestResponse( $this->unitTest, $content );
            }
            catch ( RedirectException $e ) {
                // maintain $_SESSION across redirect
                $redirect = new FunctionalTestRequest( $this->unitTest, $e->resource, $e->method, $_SESSION, 'GET', $e->args );

                $response = $redirect->execute();
            }
            catch ( HTTPErrorException $e ) {
                $response = new FunctionalTestResponse( $this->unitTest, '' );
                $response->status = $e->error;
            }
            // clean up
            $_SESSION = $oldSession;

            return $response;
        }
    }

    class FunctionalTestResponse {
        protected $unitTest;
        public $status = 200;
        public $content;
        public $dom;

        public function __construct( UnitTest $unitTest, $content ) {
            $this->content = $content;
            $this->unitTest = $unitTest;
            $this->dom = str_get_html( $content );
        }
        public function assertHas( $selector, $description = '' ) {
            $this->unitTest->assertTrue( $this->dom->find( $selector ), $description );
        }
        public function assertContains( $text, $description = '' ) {
            $this->unitTest->assertTrue( strpos( $this->dom->save(), $text ) !== false, $description );
        }
        public function assertStatusIs( $status, $description = '' ) {
            $this->unitTest->assertEquals( $status, $this->status, $description . ". Expected $status, found " . $this->status . "." );
        }
    }
?>