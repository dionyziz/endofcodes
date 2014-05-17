<?php
    if ( !isset( $_SESSION[ 'user' ] ) || !$_SESSION[ 'user' ]->isDeveloper() ) {
        // show this information to developers only
        return;
    }
?>
<div class="dev"><?php
    if ( empty( $_SESSION[ 'profiling' ] ) ) {
        // profiling is disabled; display link to enable it
        ?><a href='' title='Show profiling information' class='enable-profiling'>π</a><?php
        $form = new Form( 'profiling', 'update' );
        $form->id = 'profiling-form';
        $form->output( function( $self ) {
            $self->createInput( 'hidden', 'enable', '', 'true' );
        } );
    }
    else {
        // profiling is enabled; allow developer to view it
        ?>dt = <a href=''><?php
            echo round( 1000 * $this->getPageGenerationTime(), 2 );
        ?>ms</a><?php
    }
?></div>
