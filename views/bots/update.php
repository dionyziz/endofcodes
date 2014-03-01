<?php
    require 'views/header.php';?>
    
    <p>To begin playing, you must set up your bot.<a href=''> Start by reading the tutorial.</a></p>
    <p>API key: <?php echo $apikey ?></p><?php

    $form = new Form( 'bot', 'update' );
    $form->output( function( $self ) use( $boturl_empty, $boturl_invalid ) {
        $self->createLabel( 'boturl', 'Bot URL' );
        if ( isset( $boturl_empty ) ) {
            $self->createError( 'Please enter your bot URL' );
        }
        if ( isset( $boturl_invalid ) ) {
            $self->createError( 'Please enter a valid HTTP URL' );
        }
        $self->createInput( 'text', 'boturl', 'boturl' );
        $self->createInput( 'submit', '', '', 'Save bot settings' );
    } );

    require 'views/footer.php';
?>
    
