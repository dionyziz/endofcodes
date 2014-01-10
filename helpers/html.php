<?php
    include_once 'models/formtoken.php';
    class Form {
        private $resource;
        private $method;
        public $id;
        protected $token;

        public function __construct( $resource = '', $method = '' ) {
            $this->resource = $resource;
            $this->method = $method;
        }

        public static function createError( $error_msg ) {
            ?><p class="error"><?php
                    echo $error_msg;
            ?></p><?php
        }

        public static function createInput( $type = 'text', $name = '', $id = '', $value = '' ) {
            ?><p><input type="<?php
                echo $type;
            ?>" <?php
                if ( !empty( $name ) ) {
                    ?>name="<?php
                        echo $name;
                    ?>" <?php
                }
                if ( !empty( $id ) ) {
                    ?>id="<?php
                        echo $id;
                    ?>" <?php
                }
                if ( !empty( $value ) ) {
                    ?>value="<?php
                        echo $value;
                    ?>" <?php
                }
            ?> /></p><?php
        }

        public static function createSelect( $name = '', $id = '', $option_array ) {
            ?><p><select <?php
                if ( isset( $name ) ) {
                    ?>name="<?php
                        echo $name;
                    ?>" <?php
                }
                if ( isset( $id ) ) {
                    ?>id="<?php
                        echo $id;
                    ?>" <?php
                }
            ?>><?php
            foreach ( $option_array as $option ) {
                ?><option <?php
                    if ( isset( $option[ 'value' ] ) ) {
                        ?>value="<?php
                            echo $option[ 'value' ];
                        ?>"<?php
                    }
                ?>><?php
                    echo $option[ 'content' ];
                ?></option><?php
            }
            ?></select></p><?php
        }
       
        public static function createLabel( $for, $text ) {
            ?><label for="<?php
                echo $for;
            ?>"><?php
                echo $text;
            ?></label><?php
        }

        public function output( $callable ) {
            $this->token = $_SESSION[ 'form' ][ 'token' ] = FormToken::create(); 
            ?><form <?php
                if ( isset( $this->id ) ) {
                    ?>id="<?php
                        echo $this->id;
                    ?>" <?php
                }
                ?>action="index.php?resource=<?php
                    echo $this->resource;
                ?>&amp;method=<?php
                    echo $this->method;
                ?>" method="POST"><?php
                $callable();
                Form::createInput( 'hidden', 'token', '', $this->token );
            ?></form><?php
        }
    }

    function includeStyle( $path ) {
        ?><link
            rel="stylesheet"
            type="text/css"
            href="<?php
                echo "static/style/" . $path . ".css";
            ?>" /><?php
    }
?>
