<?php
    require_once 'models/lib/simple_html_dom.php';

    abstract class FunctionalTest extends UnitTest {
        public function request( $resource, $method, $verb = 'GET', $vars = [] ) {
            $returned = false;
            do {
                ob_start();
                try {
                    $controller = controllerBase::findController( $resource );
                    $controller->trusted = true;
                    $get = [ 'method' => $method ];
                    $post = [];
                    if ( $verb == 'GET' ) {
                        $get = array_merge( $get, $vars );
                    }
                    else {
                        $post = array_merge( $post, $vars );
                    }
                    $controller->dispatch( $get, $post, [], $verb );
                    $returned = true;
                }
                catch ( RedirectException $e ) {
                    die( 'redirect to ' . $e->resource . ', ' . $e->method );
                    $resource = $e->resource;
                    $method = $e->method;
                }
                $content = ob_get_clean();
            } while ( !$returned );

            return new FunctionalTestResponse( $this, $content );
        }
    }

    class FunctionalTestResponse {
        protected $unittest;
        public $content;
        public $dom;

        public function __construct( $unittest, $content ) {
            $this->content = $content;
            $this->unittest = $unittest;
            $this->dom = str_get_html( $content );
        }
        public function assertHas( $selector, $description = '' ) {
            $this->unittest->assertTrue( ( bool )$this->dom->find( $selector ), $description );
        }
    }
?>
