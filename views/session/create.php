<?php
    include 'views/header.php';
?>

<div id="login"><?php 
    $form = new Form( 'session', 'create' );  
    $form->id = 'login-form';
    $form->output( function() use ( $username_empty, $password_empty,
            $username_wrong, $password_wrong ) {
        if ( isset( $username_empty ) ) {
            Form::produceError( "Please type a username." );
        }
        if ( isset( $password_empty ) ) {
            Form::produceError( "Please type a password." );
        }
        Form::createLabel( 'username', 'Username' );
        if ( isset( $username_wrong ) ) {
            Form::produceError( "Username doesn't exist." );
        }
        Form::createInput( 'text', 'username', 'username' );
        Form::createLabel( 'password', 'Password' );
        if ( isset( $password_wrong ) ) {
            Form::produceError( "Password is incorrect." );
        }
        Form::createInput( 'password', 'password', 'password' );
        Form::createInput( 'submit', '', '', 'Login' );
    } );
?></div>

<p><a href="index.php?resource=user&amp;method=create">Don't have an account?</a></p>

<?php
    include 'views/footer.php';
?>
