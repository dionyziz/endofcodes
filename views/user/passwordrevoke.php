<?php
    include 'views/header.php';
?>

<div><?php 
    $form = new Form( 'forgotpasswordrequest', 'create' );  
    $form->output( function( $self ) use ( $inputEmpty, $usernameNotExists, $emailNotExists ) {
        if ( $inputEmpty ) {
            $self->createError( "Please enter your username or email" );
        }
        if ( $emailNotExists ) {
            $self->createError( "This email doesn't exist" );
        }
        if ( $usernameNotExists ) {
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
