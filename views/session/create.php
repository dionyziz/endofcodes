<?php
    include 'views/header.php';
?>

<div id="login"><?php
    $form = new Form( 'session', 'create' );
    $form->id = 'login-form';
    $form->output( function( $self ) use ( $username_empty, $password_empty,
            $username_wrong, $password_wrong ) {
        if ( isset( $username_empty ) ) {
            $self->createError( "Please type a username." );
        }
        if ( isset( $password_empty ) ) {
            $self->createError( "Please type a password." );
        }
        $self->createLabel( 'username', 'Username' );
        if ( isset( $username_wrong ) ) {
            $self->createError( "Username doesn't exist." );
        }
        $self->createInput( 'text', 'username', 'username' );
        $self->createLabel( 'password', 'Password' );
        if ( isset( $password_wrong ) ) {
            $self->createError( "Password is incorrect." );
        }
        $self->createInput( 'password', 'password', 'password' );
        $self->createLabel( 'persistent', 'Remember me' );
        $self->createInput( 'checkbox', 'persistent', 'persistent', '', true );
        ?><p><a href="">Forgot password?</a></p><?php
        $self->createInput( 'submit', '', '', 'Login' );
    } );
?>
</div>

<p><a href="index.php?resource=user&amp;method=create">Don't have an account?</a></p>

<?php
    include 'views/footer.php';
?>
