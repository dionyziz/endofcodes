<?php
    include 'views/header.php';
?>

<div><?php 
    $form = new Form( 'forgotpasswordrequest', 'create' );  
    $form->id = 'passRevoke';
    $form->output( function( $self ) use ( $username_empty, $username_not_valid, $username_not_exists ) {
        if ( isset( $username_empty ) ) {
            $self->createError( "Please enter your username" );
        }
        if ( isset( $username_not_exists ) ) {
            $self->createError( "This username doesn't exist" );
        }
        $self->createLabel( 'username', 'Please enter your username' );
        $self->createInput( 'text', 'username', 'username' );
        $self->createInput( 'submit', '', '', 'Reset your password' );
    } );
?>
</div>

<p><a href="index.php?resource=user&amp;method=create">Don't have an account?</a></p>

<?php
    include 'views/footer.php';
?>
