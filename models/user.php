<?php
    require_once 'models/encrypt.php';
    require_once 'models/country.php';
    require_once 'models/image.php';

    define( 'ROLE_USER', 0 ); // default role
    define( 'ROLE_DEVELOPER', 10 );

    class User extends ActiveRecordBase {
        protected static $attributes = [ 'username', 'password', 'name', 'surname', 'dob', 'salt', 'boturl', 'countryid', 'imageid', 'email', 'website', 'github', 'sessionid', 'forgotpasswordtoken', 'forgotpasswordrequestcreated', 'role' ];
        public $username;
        public $password;
        public $name;
        public $surname;
        public $email;
        public $country;
        public $forgotpasswordrequestcreated;
        public $forgotpasswordtoken;
        public $image;
        public $sessionid;
        public $salt;
        public $dateOfBirth;
        public $website;
        public $github;
        public $boturl;
        public $winCount;
        public $role;
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
                throw new ModelValidationException( 'password_empty' );
            }
            if ( strlen( $password ) < $config[ 'pass_min_len' ] ) {
                throw new ModelValidationException( 'password_invalid' );
            }
        }

        public function __construct( $id = false ) {
            if ( $id ) {
                // existing active record object
                try {
                    $user_info = dbSelectOne( 'users', [ '*' ], compact( "id" ) );
                }
                catch ( DBExceptionWrongCount $e ) {
                    throw new ModelNotFoundException();
                }
                $this->winCount = 0;
                $attributesToAssign = [ 'boturl', 'username', 'email', 'name', 'surname', 'dob', 'website', 'github', 'forgotpasswordtoken', 'forgotpasswordrequestcreated', 'role' ];
                foreach ( $attributesToAssign as $key ) {
                    $this->$key = $user_info[ $key ];
                }

                $this->country = new Country( $user_info[ 'countryid' ] );
                $this->image = new Image( $user_info[ 'imageid' ] );
                $this->id = $id;
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
                throw new ModelValidationException( 'username_empty' );
            }
            if ( preg_match( '#[^a-zA-Z0-9._]#', $this->username ) ) {
                throw new ModelValidationException( 'username_invalid' );
            }
            if ( preg_match( '#[^a-zA-Z]#', $this->name ) ) {
                throw new ModelValidationException( 'name_invalid' );
            }
            if ( preg_match( '#[^a-zA-Z]#', $this->surname ) ) {
                throw new ModelValidationException( 'surname_invalid' );
            }
            if ( empty( $this->password ) && !$this->exists ) {
                throw new ModelValidationException( 'password_empty' );
            }
            if ( !$this->exists && empty( $this->email ) ) {
                throw new ModelValidationException( 'email_empty' );
            }
            if ( isset( $this->password ) && strlen( $this->password ) < $config[ 'pass_min_len' ] ) {
                if ( $this->exists ) {
                    throw new ModelValidationException( 'password_new_small' );
                }
                throw new ModelValidationException( 'password_small' );
            }
            if ( !empty( $this->website ) ) { 
                if ( !filter_var( $this->website, FILTER_VALIDATE_URL ) ) {
                    throw new ModelValidationException( 'website_invalid' );
                }
                if ( !checkdnsrr( $this->website, 'A' ) ) {
                    throw new ModelValidationException( 'website_not_exists' );
                }
            }
            if ( !filter_var( $this->email, FILTER_VALIDATE_EMAIL ) ) {
                throw new ModelValidationException( 'email_invalid' );
            }
            //if ( !empty( $this->github ) ) {  Please ignore it for now
            //    $handle = curl_init( 'https://github.com/' . $this->github );
            //    if ( curl_getinfo( $handle, CURLINFO_HTTP_CODE ) == 404 ) {
            //        throw new ModelValidationException( 'github_invalid' );
            //    }
            //}
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
            if ( $this->role < 0 || !isset( $this->role ) ) {
                $this->role = 0;
            }
        }

        protected function onBeforeCreate() {
            $this->prepareDob();
            $this->imageid = 0;
            $this->generateSessionId();
        }

        protected function onBeforeUpdate() {
            $this->prepareDob();
        }

        protected function onSave() {
            unset( $this->password );
            unset( $this->salt );
        }

        protected function onCreateError( $eDb ) {
            try {
                User::findByUsername( $this->username );
                throw new ModelValidationException( 'username_used' );
            }
            catch ( ModelNotFoundException $e ) {
                try { 
                    User::findByEmail( $this->email ); 
                    throw new ModelValidationException( 'email_used' );
                } 
                catch ( ModelNotFoundException $e ) {
                    throw $eDb;
                }
            }
        }

        protected function onUpdateError( $e ) {
            throw new ModelValidationException( 'email_used' );
        }

        protected function generateSessionId() {
            $value = openssl_random_pseudo_bytes( 32 );
            $sessionid = base64_encode( $value );
            $this->sessionid = $sessionid;
        }

        protected function prepareDob() {
            $day = intval( $this->dateOfBirth[ 'day' ] );
            $month = intval( $this->dateOfBirth[ 'month' ] );
            $year = intval( $this->dateOfBirth[ 'year' ] );
            if ( !checkdate( $day, $month, $year ) ) {
                $day = $month = $year = 0;
            }
            $this->dob = $year . '-' . $month . '-' . $day;
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
            $link = $config[ 'base' ] . "/forgotpasswordrequest/update?username=$username&password_token=$value";
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
            if ( $period > $config[ 'forgot_password_exp_time' ] ) {
                throw new ModelValidationException( 'link_expired' );
            } 
        }

        public function setBoturl( $boturl ) {
            $oldBoturl = $this->boturl;
            $this->boturl = $boturl;

            $bot = new GraderBot( $this );
            try {
                $bot->sendInitiateRequest();
                $this->save();
            }
            catch ( GraderBotException $e ) {
                $this->boturl = $oldBoturl;
                throw new ModelValidationException( $e->error->id );
            }
        }

        public function isDeveloper() {
            return $this->role >= ROLE_DEVELOPER;
        }
    }
?>
