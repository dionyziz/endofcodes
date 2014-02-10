<?php
    include 'views/header.php';
    $form = new Form( 'game', 'create' );
    $form->output( function( $self ) {
        $self->createSubmit( 'Start game' );
    } );
    include 'views/footer.php';
?>
