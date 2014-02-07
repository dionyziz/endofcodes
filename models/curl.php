<?php
    interface CurlConnectionInterface {
        public function __construct();
        public function setOpt( $option, $value );
        public function exec();
        public function __destruct();
    }

    class CurlConnection implements CurlConnectionInterface {
        protected $ch;
        public $output;

        public function __construct() {
            $this->ch = curl_init();
        }
        public function __destruct() {
            curl_close( $this->ch );
        }
        public function setOpt( $option, $value ) {
            curl_setopt( $this->ch, $option, $value );
        }
        public function exec() {
            $this->output = curl_exec( $this->ch );
        }
    }
?>
