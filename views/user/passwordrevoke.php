<?php
    include 'views/header.php';
?>

<div><?php 
    $form = new Form( 'forgotpasswordrequest', 'create' );  
    $form->output( function( $self ) use ( $input_empty, $username_not_exists, $email_not_exists ) {
        if ( isset( $input_empty ) ) {
            $self->createError( "Please enter your username or email" );
        }
        if ( isset( $email_not_exists ) ) {
            $self->createError( "This email doesn't exist" );
        }
        if ( isset( $username_not_exists ) ) {
            $self->createError( "This username doesn't exist" );
        }
        $self->createLabel( 'username', 'Please enter your username or email' );
        $self->createInput( 'text', 'input', 'username' );
        $self->createInput( 'submit', '', '', 'Reset your password' );
    } );
?>
</div>

<p><a href="user/create">Don't have an account?</a></p>

<?php
    include 'views/footer.php';
?>
