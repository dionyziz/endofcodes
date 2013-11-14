<?php
    class ImageController {
        public static function create( $image ) {
            include 'models/image.php';
            include 'models/extentions.php';
            $config = getConfig();
            $avatarname = basename( $image[ 'name' ] );
            $tmp_name = $image[ 'tmp_name' ];
            if ( isset( $_SESSION[ 'user' ][ 'username' ] ) ) {
                $username = $_SESSION[ 'user' ][ 'username' ];
            }
            else {
                throw new HTTPUnauthorizedException();
            }
            $ext = Extention::get( $avatarname ); 
            if ( !Extention::valid( $ext ) ) {
                throw new RedirectException( 'index.php?resource=user&method=view&notvalid=yes&username=' . $username );
            }
            $target_path = $config[ 'paths' ][ 'avatar_path' ];
            $id = Image::create( $_SESSION[ 'user' ][ 'userid' ], $avatarname );
            $avatarname = "$id" . "." . $ext;
            $target_path = $target_path . $avatarname;
            Image::upload( $tmp_name, $target_path );
            Image::update( $username, $id );
            throw new RedirectException( 'index.php?resource=user&method=view&username=' . $username );
        }
    }
?>
