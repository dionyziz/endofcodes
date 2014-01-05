<?php
    abstract class ControllerBase {
        
        public function dispatch( $method, $vars ) {
            $this_reflection = new ReflectionObject( $this );
            $method_reflection = $this_reflection->getMethod( $method );
            $parameters = $method_reflection->getParameters();
            $arguments = array();

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
            call_user_func_array( array( $this, $method ), $arguments );
        }
    }
?>
