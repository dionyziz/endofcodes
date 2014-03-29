<?php
    require 'views/header.php';
?>

<div class="text-center"><?php
    if( isset( $validBot ) ) {
        ?><p>Your bot is set up to play the next game</p><?php
    }
    ?><p><a href='bot/update'>Configure Bot.</a></p><?php
    $form = new Form( 'user', 'update' );
    $form->output( function( $self ) use( $emailInvalid, $emailUsed, $passwordWrong,
                                          $passwordNewNotMatched, $passwordNewSmall, $countries, $user ) {
        ?><p>Change email</p><?php
        $self->createLabel( 'email', 'Email' );
        if ( isset( $emailInvalid ) ) {
            $self->createError( 'Email is not valid' );
        }
        if ( isset( $emailUsed ) ) {
            $self->createError( 'Email is already in use.' );
        }
        $self->createInput( 'text', 'email', 'email', htmlspecialchars( $user->email ) );
        ?><p>Change password</p><?php
        $self->createLabel( 'password', 'Old password' );
        if ( isset( $passwordWrong ) ) {
            $self->createError( 'Old password is incorrect' );
        }
        $self->createInput( 'password', 'password', 'password' );
        $self->createLabel( 'passwordNew', 'New password' );
        if ( isset( $passwordNewNotMatched ) ) {
            $self->createError( 'Passwords do not match' );
        }
        else if ( isset( $passwordNewSmall ) ) {
            $self->createError( 'Your password should be at least 7 characters long' );
        }
        $self->createInput( 'password', 'passwordNew', 'passwordNew' );
        $self->createLabel( 'passwordRepeat', 'Repeat' );
        $self->createInput( 'password', 'passwordRepeat', 'passwordRepeat' );
        ?><p>Change country</p><?php
        $countriesSelectArray = [ [ 'content' => 'Select Country' ] ];
        foreach ( $countries as $key => $country ) {
            $countriesSelectArray[] = [ 'value' => $key + 1, 'content' => $country->name ];
        }
        $self->createSelect( $countriesSelectArray, 'countryid' );
        $self->createSubmit( 'Save settings' );
    } );

    $form = new Form( 'image', 'create' );
    $form->output( function( $self ) use( $imageInvalid ) {
        $self->createLabel( 'image', 'Upload an avatar' );
        if ( isset( $imageInvalid ) ) {
            $self->createError( "This isn't an image" );
        }
        $self->createInput( 'file', 'image', 'avatar-form' );
        $self->createSubmit( 'Upload' );
    } );

    $form = new Form( 'user', 'delete' );
    $form->output( function( $self ) {
        $self->createSubmit( 'Delete your account' );
    } );
?></div>

<?php
    require 'views/footer.php';
?>
