<?php
    require 'views/header.php';

    $form = new Form( 'session', 'create' );
    $form->attributes = [
        'class' => 'form-signin'
    ];
    $form->output( function( $self ) use ( $username_empty, $password_empty,
            $username_wrong, $password_wrong ) {
        if ( isset( $username_empty ) ) {
            $self->createError( "Please type a username." );
        }
        if ( isset( $password_empty ) ) {
            $self->createError( "Please type a password." );
        }
        if ( isset( $username_wrong ) ) {
            $self->createError( "Username doesn't exist." );
        }
        $self->createLabel( 'username', 'Username' );
        $self->createInput( 'text', 'username', 'username', '', [ 
            'class' => 'form-control',
            'placeholder' => 'Username' 
        ] );
        $self->createLabel( 'password', 'Password' );
        if ( isset( $password_wrong ) ) {
            $self->createError( "Password is incorrect." );
        }
        $self->createInput( 'password', 'password', 'password', '', [
            'class' => 'form-control',
            'placeholder' => 'Password' 
        ] );
        ?><label><?php 
        //$self->createInput( 'checkbox', 'persistent', 'persistent', '', [
        //    'checked' => true,
        //] ); ?>
            <input type="checkbox"> Remember me 
        </label>   
        <p><a href="forgotpasswordrequest/create">Forgot password?</a></p><?php
        $self->createSubmit( 'Log in', [
            'class' => 'btn btn-lg btn-primary btn-block'
        ] );
    } );

    require 'views/footer.php';
?>
