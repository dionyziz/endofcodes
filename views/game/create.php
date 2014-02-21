<?php
    require 'views/header.php';

    $form = new Form( 'game', 'create' );
    $form->output( function( $self ) {
        $self->createSubmit( 'Start game' );
    } );

    require 'views/footer.php';
?>
