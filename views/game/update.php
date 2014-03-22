<?php
    require 'views/header.php';

    if ( $game->ended ) {
        ?><h1>This game is already over.</h1><?php
        return;
    }

    $form = new Form( 'game', 'update' );
    $form->output( function( $self ) use( $game ) {
        $self->createInput( 'hidden', 'gameid', '', $game->id );
        $self->createSubmit( 'Next round' );
    } );

    require 'views/footer.php';
?>
