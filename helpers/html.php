<?php
    require_once 'models/formtoken.php';
    class Form {
        protected $resource;
        protected $method;
        public $id;
        public $attributes;
        public $formMethod;
        protected $hasFile = false;
        protected $token;

        public static function isValidType( $type ) {
            $valid_types = [
                'button',
                'checkbox',
                'color',
                'date',
                'datetime',
                'datetime-local',
                'email',
                'file',
                'hidden',
                'image',
                'month',
                'number',
                'password',
                'radio',
                'reset',
                'search',
                'submit',
                'tel',
                'text',
                'time',
                'url',
                'week'
            ];
            $valid_types = array_flip( $valid_types );
            return isset( $valid_types[ $type ] );
        }

        public function __construct( $resource = '', $method = '' ) {
            $this->resource = $resource;
            $this->method = $method;
        }

        public function createError( $error_msg ) {
            ?><p class="alert alert-danger"><?php
                echo htmlspecialchars( $error_msg );
            ?></p><?php
        }

        public function createInput( $type = 'text', $name = '', $id = '', $value = '', $attributes = '' ) {
            if ( !Form::isValidType( $type ) ) {
                $type = 'text';
            }
            if ( $type === 'file' ) {
                $this->hasFile = true;
            }
            ?><p><input type="<?php
                echo htmlspecialchars( $type );
            ?>" <?php
                if ( !empty( $name ) ) {
                    ?>name="<?php
                        echo htmlspecialchars( $name );
                    ?>" <?php
                }
                if ( !empty( $id ) ) {
                    ?>id="<?php
                        echo htmlspecialchars( $id );
                    ?>" <?php
                }
                if ( !empty( $value ) ) {
                    ?>value="<?php
                        echo htmlspecialchars( $value );
                    ?>" <?php
                }
                if ( !empty( $attributes ) ) {
                    foreach ( $attributes as $key => $value ) {
                        echo $key; 
                        ?>="<?php
                            echo htmlspecialchars( $value );
                        ?>" <?php
                    }
                }
            ?> /></p><?php
        }

        public function createSubmit( $value, $attributes = '' ) {
            $this->createInput( 'submit', '', '', $value, $attributes );
        }

        public function createSelect( $option_array, $name = '', $selected = '', $id = '', $attributes = '' ) {
            ?><p><select <?php
                if ( isset( $name ) ) {
                    ?>name="<?php
                        echo htmlspecialchars( $name );
                    ?>" <?php
                }
                if ( isset( $id ) ) {
                    ?>id="<?php
                        echo htmlspecialchars( $id );
                    ?>" <?php
                }
                if ( !empty( $attributes ) ) {
                    foreach ( $attributes as $key => $value ) {
                        echo $key; 
                        ?>="<?php
                            echo htmlspecialchars( $value );
                        ?>" <?php
                    }
                }
            ?>><?php
            foreach ( $option_array as $option ) {
                ?><option <?php
                    if ( isset( $option[ 'value' ] ) ) {
                        ?>value="<?php
                            echo htmlspecialchars( $option[ 'value' ] );
                        ?>"<?php
                    }
                    if ( $selected == $option[ 'content' ] ) {
                        ?> selected="selected"<?php
                    }
                ?>><?php
                    echo htmlspecialchars( $option[ 'content' ] );
                ?></option><?php
            }
            ?></select></p><?php
        }

        public static function getRESTMethodIdempotence( $method ) {
            $methods = [
                'create' => 1,
                'listing' => 0,
                'delete' => 1,
                'update' => 1,
                'view' => 0
            ];
            if ( isset( $methods[ $method ] ) ) {
                return $methods[ $method ];
            }
            throw new HTMLFormInvalidException( $method );
        }

        public function createLabel( $for, $text ) {
            ?><label for="<?php
                echo htmlspecialchars( $for );
            ?>"><?php
                echo htmlspecialchars( $text );
            ?></label><?php
        }

        public function output( $callable = false ) {
            if ( $callable != false ) {
                ob_start();
                $callable( $this );
                $out = ob_get_clean();
            }
            else {
                $out = "";
            }
            if ( !isset( $_SESSION[ 'form' ][ 'token' ] ) ) {
                $this->token = $_SESSION[ 'form' ][ 'token' ] = FormToken::create();
            }
            else {
                $this->token = $_SESSION[ 'form' ][ 'token' ];
            }
            if ( Form::getRESTMethodIdempotence( $this->method ) === 1 ) {
                $this->formMethod = 'post';
            }
            else {
                $this->formMethod = 'get';
            }
            ?><form <?php
                if ( isset( $this->id ) ) {
                    ?>id="<?php
                        echo htmlspecialchars( $this->id );
                    ?>" <?php
                }
                if ( isset( $this->attributes ) ) {
                    foreach( $this->attributes as $key => $value ) {
                        echo $key; 
                        ?>="<?php
                            echo htmlspecialchars( $value );
                        ?>" <?php
                    }
                }
                ?>action="<?php
                    echo htmlspecialchars( $this->resource );
                ?>/<?php
                    echo htmlspecialchars( $this->method );
                ?>" method="<?php
                    echo htmlspecialchars( $this->formMethod );
                ?>" <?php
                    if ( $this->hasFile ) {
                        ?>enctype="multipart/form-data"<?php
                    }
                ?>><?php
                echo $out;
                $this->createInput( 'hidden', 'token', '', $this->token );
            ?></form><?php
        }
    }

    class HTMLException extends Exception {
        public function __construct( $error ) {
            parent::__construct( $error );
        }
    }

    class HTMLFormInvalidException extends HTMLException {
        public function __construct( $method ) {
            parent::__construct( "Not a valid REST method: " . $method .
                " (must be one of 'create', 'view', 'listing', 'update', 'delete')" );
        }
    }

    function includeStyle( $path ) {
        ?><link
            rel="stylesheet"
            type="text/css"
            href="<?php
                echo htmlspecialchars( "static/style/" . $path . ".css" );
            ?>" /><?php
    }
?>
