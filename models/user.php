<?php
    require_once 'models/encrypt.php';
    require_once 'models/country.php';
    require_once 'models/image.php';

    class User extends ActiveRecordBase {
        protected static $attributes = [ 'username', 'password', 'dob', 'salt', 'boturl', 'countryid', 'imageid', 'email', 'sessionid', 'forgotpasswordtoken', 'forgotpasswordrequestcreated' ];
        public $username;
        public $password;
        public $email;
        public $country;
        public $forgotpasswordrequestcreated;
        public $forgotpasswordtoken;
        public $image;
        public $sessionid;
        public $salt;
        public $dateOfBirth;
        public $boturl;
        public $winCount;
        protected $dob;
        protected static $tableName = 'users';

        public static function findByUsername( $username ) {
            try {
                $user = dbSelectOne( 'users', [ 'id' ], compact( "username" ) );
            }
            catch ( DBExceptionWrongCount $e ) {
                throw new ModelNotFoundException();
            }
            return new User( $user[ 'id' ] );
        }

        public static function findBySessionId( $sessionid ) {
            if ( empty( $sessionid ) ) {
                throw new ModelNotFoundException();
            }
            try {
                $row = dbSelectOne(
                    'users',
                    [ 'id' ],
                    compact( "sessionid" )
                );
            }
            catch ( DBExceptionWrongCount $e ) {
                throw new ModelNotFoundException();
            }
            return new User( $row[ 'id' ] );
        }

        public static function passwordValidate( $password ) {
            global $config;

            if ( empty( $password ) ) {
                throw new ModelValidationException( 'passwordEmpty' );
            }
            if ( strlen( $password ) < $config[ 'passMinLen' ] ) {
                throw new ModelValidationException( 'passwordInvalid' );
            }
        }

        public function __construct( $id = false ) {
            if ( $id ) {
                // existing active record object
                try {
                    $userInfo = dbSelectOne( 'users', [ 'boturl', 'dob', 'username', 'email', 'countryid', 'imageid', 'forgotpasswordrequestcreated', 'forgotpasswordtoken' ], compact( "id" ) );
                }
                catch ( DBExceptionWrongCount $e ) {
                    throw new ModelNotFoundException();
                }
                $this->winCount = 0;
                $this->boturl = $userInfo[ 'boturl' ];
                $this->username = $userInfo[ 'username' ];
                $this->email = $userInfo[ 'email' ];
                $this->country = new Country( $userInfo[ 'countryid' ] );
                $this->image = new Image( $userInfo[ 'imageid' ] );
                $this->id = $id;
                $this->dob = $userInfo[ 'dob' ];
                $this->forgotpasswordtoken = $userInfo[ 'forgotpasswordtoken' ];
                $this->forgotpasswordrequestcreated = $userInfo[ 'forgotpasswordrequestcreated' ];
                $this->exists = true;
            }
        }
        
        public function getWinCount() {
            $games = Game::findAll();
            foreach ( $games as $game ) {
                $ratings = $game->getGlobalRatings();
                if ( isset( $ratings[ 1 ] ) ) {
                    foreach ( $ratings[ 1 ] as $winner ) {
                        if ( $winner->id == $this->id ) {
                            $this->winCount += 1;
                            break;
                        }
                    }
                }
            }
            return $this->winCount;
        }
        public static function findByEmail( $email ) {
            try {
                $user = dbSelectOne( 'users', [ 'id' ], compact( "email" ) );
            }
            catch ( DBExceptionWrongCount $e ) {
                throw new ModelNotFoundException();
            }
            return new User( $user[ 'id' ] );
        }

        protected function onBeforeSave() {
            global $config;

            if ( empty( $this->username ) ) {
                throw new ModelValidationException( 'usernameEmpty' );
            }
            if ( preg_match( '#[^a-zA-Z0-9._]#', $this->username ) ) {
                throw new ModelValidationException( 'usernameInvalid' );
            }
            if ( empty( $this->password ) && !$this->exists ) {
                throw new ModelValidationException( 'passwordEmpty' );
            }
            if ( !$this->exists && empty( $this->email ) ) {
                throw new ModelValidationException( 'emailEmpty' );
            }
            if ( isset( $this->password ) && strlen( $this->password ) < $config[ 'passMinLen' ] ) {
                if ( $this->exists ) {
                    throw new ModelValidationException( 'passwordNewSmall' );
                }
                throw new ModelValidationException( 'passwordSmall' );
            }
            if ( !filter_var( $this->email, FILTER_VALIDATE_EMAIL ) ) {
                throw new ModelValidationException( 'emailInvalid' );
            }

            if ( isset( $this->password ) ) {
                $array = encrypt( $this->password );
                $this->password = $array[ 'hash' ];
                $this->salt = base64_encode( $array[ 'salt' ] );
            }

            if ( isset( $this->country ) ) {
                $this->countryid = $this->country->id;
            }
            else {
                $this->countryid = 0;
            }
            if ( isset( $this->image ) ) {
                $this->imageid = $this->image->id;
            }
            else {
                $this->imageid = 0;
            }
            if ( !isset( $this->boturl ) ) {
                $this->boturl = '';
            }
        }

        protected function onBeforeCreate() {
            $day = intval( $this->dateOfBirth[ 'day' ] );
            $month = intval( $this->dateOfBirth[ 'month' ] );
            $year = intval( $this->dateOfBirth[ 'year' ] );
            if ( !checkdate( $day, $month, $year ) ) {
                $day = $month = $year = 0;
            }
            $dob = $this->dob = $year . '-' . $month . '-' . $day;
            $this->imageid = 0;
            $this->generateSessionId();
        }

        protected function onSave() {
            unset( $this->password );
            unset( $this->salt );
        }

        protected function onCreateError( $eDb ) {
            try {
                User::findByUsername( $this->username );
                throw new ModelValidationException( 'usernameUsed' );
            }
            catch ( ModelNotFoundException $e ) {
                try { 
                    User::findByEmail( $this->email ); 
                    throw new ModelValidationException( 'emailUsed' );
                } 
                catch ( ModelNotFoundException $e ) {
                    throw $eDb;
                }
            }
        }

        protected function onUpdateError( $e ) {
            throw new ModelValidationException( 'emailUsed' );
        }

        protected function generateSessionId() {
            $value = openssl_random_pseudo_bytes( 32 );
            $sessionid = base64_encode( $value );
            $this->sessionid = $sessionid;
        }

        public function authenticatesWithPassword( $password ) {
            $username = $this->username;
            $row = dbSelectOne(
                'users',
                [ 'id', 'password', 'salt' ],
                compact( "username" )
            );
            if ( !empty( $row ) ) {
                if ( $row[ 'password' ] == hashing( $password, base64_decode( $row[ 'salt' ] ) ) ) {
                    return true;
                }
            }
            return false;
        }

        public function renewSessionId() {
            $this->generateSessionId();
            $this->save();
        }

        public function createForgotPasswordLink() {
            global $config;

            $bytes = openssl_random_pseudo_bytes( 32 );
            $value = bin2hex( $bytes );
            $this->forgotpasswordtoken = $value;
            $this->forgotpasswordrequestcreated = date( "Y-m-d h:i:s" );
            $this->save();
            $email = $this->email;
            $username = urlencode( $this->username );
            $link = $config[ 'base' ] . "/forgotpasswordrequest/update?username=$username&passwordToken=$value";
            $this->mailFromExternalView( $email, "views/user/forgot/mail.php", 'Password Reset', compact( "username", "link" ) );
        }
        
        public function mailFromExternalView( $email, $extView, $subject = '', $vars = [] ) {
            global $config;

            if ( !file_exists( $extView ) ) {
                throw new ModelNotFoundException();
            }
            extract( $vars );
            ob_start();
            include $extView;
            $data = ob_get_clean();
            $headers = "From:" . $config[ 'email' ];
            mail( $email, $subject, $data, $headers );
        }

        public function revokePasswordCheck( $passwordToken ) {
            global $config;

            if ( $passwordToken != $this->forgotpasswordtoken ) {
                throw new ForgotPasswordModelInvalidTokenException();
            }
            if ( empty( $passwordToken ) ) {
                throw new ForgotPasswordModelInvalidTokenException();
            }
            $datetime = strtotime( $this->forgotpasswordrequestcreated );
            $now = time();
            $period = $now - $datetime;
            if ( $period > $config[ 'forgotPasswordExpTime' ] ) {
                throw new ModelValidationException( 'linkExpired' );
            } 
        }
    }
?>
