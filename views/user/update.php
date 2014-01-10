<?php
    include 'views/header.php';

    $form = new Form( 'user', 'update' );
    $form->output( function() use( $email_invalid, $email_used, $password_wrong,
            $password_new_not_matched, $password_new_small, $countries, $user ) {
        ?><p>Change email</p><?php
        Form::createLabel( 'email', 'Email' );
        if ( isset( $email_invalid ) ) {
            Form::produceError( 'Email is not valid' );
        }
        if ( isset( $email_used ) ) {
            Form::produceError( 'Email is already in use.' );
        }
        Form::createInput( 'text', 'email', 'email', htmlspecialchars( $user->email ) );
        ?><p>Change password</p><?php
        Form::createLabel( 'password', 'Old password' );
        if ( isset( $password_wrong ) ) {
            Form::produceError( 'Old password is incorrect' );
        }
        Form::createInput( 'password', 'password', 'password' );
        Form::createLabel( 'password_new', 'New password' );
        if ( isset( $password_new_not_matched ) ) {
            Form::produceError( 'Passwords do not match' );
        }
        else if ( isset( $password_new_small ) ) {
            Form::produceError( 'Your password should be at least 7 characters long' );
        }
        Form::createInput( 'password', 'password_new', 'password_new' );
        Form::createLabel( 'password_repeat', 'Repeat' );
        Form::createInput( 'password', 'password_repeat', 'password_repeat' );
        ?><p>Change country</p><?php
        $countries_select_array = array();
        $countries_select_array[] = array( 'content' => 'Select Country' );
        foreach ( $countries as $key => $country ) {
            $countries_select_array[] = array( 'value' => $key + 1, 'content' => $country[ 'name' ] );
        }
        Form::createSelect( 'countryid', '', $countries_select_array );
        Form::createInput( 'submit', '', '', 'Save settings' );
    } );

    $form = new Form( 'image', 'create' );
    $form->token = $token;
    $form->output( function() use( $image_invalid ) {
        Form::createLabel( 'image', 'Upload an avatar' );
        if ( isset( $image_invalid ) ) {
            Form::produceError( "This isn't an image" );
        }
        Form::createInput( 'file', 'image', 'image' );
        Form::createInput( 'submit', '', '', 'Upload' );
    } );

    $form = new Form( 'user', 'delete' );
    $form->token = $token;
    $form->output( function() {
        Form::createInput( 'submit', '', '', 'Delete your account' );
    } );

    include 'views/footer.php';
?>
