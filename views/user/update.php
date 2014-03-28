<?php
    require 'views/header.php';
?>

<div class="text-center"><?php
    if( isset( $valid_bot ) ) {
        ?><p>Your bot is set up to play the next game</p><?php
    }
    ?><p><a href='bot/update'>Configure Bot.</a></p><?php
    $form = new Form( 'user', 'update' );
    $form->output( function( $self ) use( $email_invalid, $email_used, $password_wrong,
                                          $password_new_not_matched, $password_new_small, $countries, $user ) {
        ?><p>Change email</p><?php
        $self->createLabel( 'email', 'Email' );
        if ( isset( $email_invalid ) ) {
            $self->createError( 'Email is not valid' );
        }
        if ( isset( $email_used ) ) {
            $self->createError( 'Email is already in use.' );
        }
        $self->createInput( 'text', 'email', 'email', htmlspecialchars( $user->email ) );
        ?><p>Change password</p><?php
        $self->createLabel( 'password', 'Old password' );
        if ( isset( $password_wrong ) ) {
            $self->createError( 'Old password is incorrect' );
        }
        $self->createInput( 'password', 'password', 'password' );
        $self->createLabel( 'password_new', 'New password' );
        if ( isset( $password_new_not_matched ) ) {
            $self->createError( 'Passwords do not match' );
        }
        else if ( isset( $password_new_small ) ) {
            $self->createError( 'Your password should be at least 7 characters long' );
        }
        $self->createInput( 'password', 'password_new', 'password_new' );
        $self->createLabel( 'password_repeat', 'Repeat' );
        $self->createInput( 'password', 'password_repeat', 'password_repeat' );
        ?><p>Change country</p><?php
        $countries_select_array = [ [ 'content' => 'Select Country' ] ];
        foreach ( $countries as $key => $country ) {
            $countries_select_array[] = [ 'value' => $key + 1, 'content' => $country->name ];
        }
        $self->createSelect( $countries_select_array, 'countryid' );
        $self->createSubmit( 'Save settings' );
    } );

    $form = new Form( 'image', 'create' );
    $form->output( function( $self ) use( $image_invalid ) {
        $self->createLabel( 'image', 'Upload an avatar' );
        if ( isset( $image_invalid ) ) {
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
