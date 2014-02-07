<?php
    include 'views/header.php';
?> 
<p>Please enter a new password</p>
<div>
<?php 
    $form = new Form( 'forgotpasswordrequest', 'update' );  
    $form->id = 'passwordUpdate';
    $form->output( function( $self ) use ( $password_empty, $password_invalid, $password_not_matched ) {
        if ( isset( $password_empty ) ) {
            $self->createError( "Please enter a new password" );
        }
        if ( isset( $password_invalid ) ) {
            $self->createError( "Your new password must be more than 6 characters" );
        }
        if ( isset( $password_not_matched ) ) {
            $self->createError( "Your two passwords do not match" );
        }
        $self->createLabel( 'password', 'password' );
        $self->createInput( 'password', 'password', 'password' );
        $self->createLabel( 'password_repeat', 'Repeat' );
        $self->createInput( 'password', 'password_repeat', 'password_repeat' );
        $self->createInput( 'submit', '', '', 'Change password' );
    } );
?>
</div><?
    include 'views/footer.php';
?>



   
