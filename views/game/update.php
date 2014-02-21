<?php
    require 'views/header.php';

    $form = new Form( 'game', 'update' );
    $form->output( function( $self ) use( $gameid ) {
        $self->createInput( 'hidden', 'gameid', '', $gameid );
        $self->createSubmit( 'Next round' );
    } );

    require 'views/footer.php';
?>
