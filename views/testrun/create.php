<?php
    include 'views/header.php';
    $form = new Form( 'testrun', 'create' );
    $form->output( function( $self ) {
        $self->createLabel( 'name', 'Which test do you want to run?' );
        $self->createInput( 'text', 'name', 'name' ); 
        $self->createInput( 'submit', '', '', 'Test' ); 
    } );
    include 'views/footer.php';
?>
